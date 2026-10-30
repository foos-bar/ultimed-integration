<?php

namespace Ultimed\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
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
