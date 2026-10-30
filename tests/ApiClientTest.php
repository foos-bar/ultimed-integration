<?php

namespace Ultimed\Tests;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ultimed\ApiClient;
use Ultimed\OAuth\AccessToken;
use Ultimed\Requests;
use Ultimed\Responses;

class ApiClientTest extends TestCase
{
    public function test_oauth_headers_use_psr7_value_arrays(): void
    {
        $request = new Requests\Custom('GET', '/v1/example', [
            'X-Empty' => '',
            'X-Multiple' => ['first', 'second'],
        ]);
        $request->setAccessToken($this->accessToken());

        $this->assertSame(['Bearer core-token'], $request->getHeaders()['Authorization']);
        $this->assertSame($request->getHeader('Authorization'), $request->getHeaders()['Authorization']);
        $this->assertSame([''], $request->getHeader('X-Empty'));
        $this->assertSame(['first', 'second'], $request->getHeader('X-Multiple'));

        foreach ($request->getHeaders() as $values) {
            $this->assertIsArray($values);
        }
    }

    public function test_requests_without_an_access_token_do_not_add_authorization(): void
    {
        $request = new Requests\Custom('GET', '/v1/example');

        $this->assertArrayNotHasKey('Authorization', $request->getHeaders());
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function test_expired_access_tokens_do_not_add_authorization(): void
    {
        $request = new Requests\Custom('GET', '/v1/example');
        $request->setAccessToken($this->accessToken('2000-01-01 00:00:00'));

        $this->assertArrayNotHasKey('Authorization', $request->getHeaders());
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    public function test_sending_preserves_headers_body_and_request_uri(): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(200)]));
        $handler->push(Middleware::history($history));
        $client = $this->client($handler);
        $request = new Requests\Custom('POST', '/v1/example?custom=target', [
            'X-Member-Token' => '',
            'X-Multiple' => ['first', 'second'],
        ], '{"example":true}');

        $client->send($request);

        $sent = $history[0]['request'];
        $this->assertSame(['Bearer core-token'], $sent->getHeaders()['Authorization']);
        $this->assertSame([''], $sent->getHeader('X-Member-Token'));
        $this->assertSame(['first', 'second'], $sent->getHeader('X-Multiple'));
        $this->assertSame('{"example":true}', (string) $sent->getBody());
        $this->assertSame('/v1/example?custom=target', $sent->getRequestTarget());
    }

    public function test_sending_keeps_typed_integration_responses(): void
    {
        $client = $this->client(HandlerStack::create(new MockHandler([
            new Response(200, [], '{"patient":{"id":123}}'),
        ])));

        $response = $client->send(new Requests\PatientShow(123));

        $this->assertInstanceOf(Responses\PatientShow::class, $response);
        $this->assertSame(['id' => 123], $response->getPatient());
    }

    public function test_relative_request_targets_resolve_against_the_base_uri(): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(200)]));
        $handler->push(Middleware::history($history));
        $client = $this->client($handler, 'https://api.example.org/prefix/');

        $client->send(new Requests\ApiRequest('GET', 'relative'));

        $this->assertSame('/prefix/relative', $history[0]['request']->getRequestTarget());
    }

    #[DataProvider('memberTokens')]
    public function test_oauth_headers_work_with_real_curl_transport(?string $memberToken): void
    {
        if (!extension_loaded('curl') || !function_exists('proc_open')) {
            $this->markTestSkipped('The cURL extension and proc_open are required for transport tests.');
        }

        $process = proc_open([PHP_BINARY, __DIR__.'/fixtures/capture-http-request.php'], [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);
        $this->assertIsResource($process);

        try {
            fclose($pipes[0]);
            stream_set_timeout($pipes[1], 10);
            $address = trim((string) fgets($pipes[1]));
            $this->assertNotSame('', $address);

            $client = $this->client(HandlerStack::create(new CurlHandler()), 'http://'.$address);
            $headers = ['Content-Type' => 'application/json'];
            if ($memberToken !== null) {
                $headers['X-Member-Token'] = $memberToken;
            }
            $payload = '{"example":true}';

            $response = $client->send(new Requests\Custom('POST', '/v1/example', $headers, $payload));
            $received = Message::parseRequest((string) $response->getBody());

            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame('POST', $received->getMethod());
            $this->assertSame('/v1/example', $received->getRequestTarget());
            $this->assertSame('Bearer core-token', $received->getHeaderLine('Authorization'));
            $this->assertSame($memberToken === null ? [] : [$memberToken], $received->getHeader('X-Member-Token'));
            $this->assertSame($payload, (string) $received->getBody());
        } finally {
            foreach ($pipes as $pipe) {
                if (is_resource($pipe)) {
                    fclose($pipe);
                }
            }
            if (proc_get_status($process)['running']) {
                proc_terminate($process);
            }
            proc_close($process);
        }
    }

    public static function memberTokens(): array
    {
        return [
            'missing header' => [null],
            'empty header' => [''],
            'non-empty header' => ['member-token'],
        ];
    }

    private function client(HandlerStack $handler, string $baseUri = 'https://api.example.org'): ApiClient
    {
        $client = new ApiClient([
            'base_uri' => $baseUri,
            'client_id' => 2,
            'client_secret' => 'test-secret',
            'handler' => $handler,
            'connect_timeout' => 2,
            'timeout' => 5,
            'proxy' => '',
        ]);
        $client->setAccessToken($this->accessToken());

        return $client;
    }

    private function accessToken(string $expiresAt = '2222-01-01 00:00:00'): AccessToken
    {
        return new AccessToken([
            'token_type' => 'Bearer',
            'access_token' => 'core-token',
            'expires_at' => $expiresAt,
        ]);
    }
}
