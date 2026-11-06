<?php

namespace Ultimed\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
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

    #[DataProvider('authorizationHeaderNames')]
    public function test_explicit_authorization_headers_are_present_without_an_access_token(string $name): void
    {
        $request = new Requests\Custom('GET', '/v1/example', [$name => 'Bearer explicit-token']);

        $this->assertAuthorizationPresent($request, 'Bearer explicit-token');
        $this->assertAuthorizationMissing($request->withoutHeader('Authorization'));
    }

    public function test_unrelated_header_changes_preserve_authorization_presence(): void
    {
        $request = new Requests\Custom('POST', 'https://api.example.org/v1/example', [], '{"example":true}');
        $request->setAccessToken($this->accessToken());

        $changed = Utils::modifyRequest($request, ['set_headers' => ['Content-Length' => '16']]);

        $this->assertAuthorizationPresent($changed);
        $this->assertSame(['16'], $changed->getHeader('Content-Length'));
        $this->assertAuthorizationPresent($request);
        $this->assertFalse($request->hasHeader('Content-Length'));
    }

    #[DataProvider('authorizationHeaders')]
    public function test_removing_authorization_suppresses_the_token_without_mutating_the_original(
        string $name,
        array $headers,
    ): void {
        $request = new Requests\Custom('GET', '/v1/example', $headers);
        $request->setAccessToken($this->accessToken());

        $stripped = $request->withoutHeader($name);

        $this->assertNotSame($request, $stripped);
        $this->assertInstanceOf(Requests\Custom::class, $stripped);
        $this->assertAuthorizationMissing($stripped);
        $this->assertSame(['Bearer core-token'], $request->getHeaders()['Authorization']);
        $this->assertTrue($request->hasHeader('Authorization'));

        $redirected = $stripped->withUri(new \GuzzleHttp\Psr7\Uri('https://other.example.org/next'));
        $this->assertAuthorizationMissing($redirected);
    }

    public function test_removing_another_header_keeps_the_access_token(): void
    {
        $request = new Requests\Custom('GET', '/v1/example', ['X-Test' => 'value']);
        $request->setAccessToken($this->accessToken());

        $changed = $request->withoutHeader('X-Test');

        $this->assertFalse($changed->hasHeader('X-Test'));
        $this->assertSame(['Bearer core-token'], $changed->getHeader('Authorization'));
        $this->assertSame(['value'], $request->getHeader('X-Test'));
    }

    #[DataProvider('redirectTargets')]
    public function test_redirects_preserve_guzzle_authorization_stripping(string $target, bool $keepAuthorization): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([
            new Response(302, ['Location' => $target]),
            new Response(200),
        ]));
        $handler->push(Middleware::history($history));
        $client = $this->client($handler);

        $response = $client->send(new Requests\Custom('GET', '/v1/example'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $history);
        $this->assertAuthorizationPresent($history[0]['request']);

        if ($keepAuthorization) {
            $this->assertAuthorizationPresent($history[1]['request']);
        } else {
            $this->assertAuthorizationMissing($history[1]['request']);
        }
    }

    #[DataProvider('sameOriginPostRedirects')]
    public function test_same_origin_post_redirects_preserve_authorization_presence(int $status, string $redirectedMethod): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([
            new Response($status, ['Location' => '/next']),
            new Response(200),
        ]));
        $handler->push(Middleware::history($history));

        $response = $this->client($handler)->send(new Requests\Custom('POST', '/v1/example', [
            'Content-Type' => 'application/json',
        ], '{"example":true}'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $history);
        $this->assertAuthorizationPresent($history[0]['request']);
        $this->assertAuthorizationPresent($history[1]['request']);
        $this->assertSame($redirectedMethod, $history[1]['request']->getMethod());
    }

    public function test_stripped_tokens_do_not_reappear_when_a_redirect_returns_to_the_original_origin(): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([
            new Response(302, ['Location' => 'https://other.example.org/next']),
            new Response(302, ['Location' => 'https://api.example.org/final']),
            new Response(200),
        ]));
        $handler->push(Middleware::history($history));

        $this->client($handler)->send(new Requests\Custom('GET', '/v1/example'));

        $this->assertCount(3, $history);
        $this->assertAuthorizationPresent($history[0]['request']);
        $this->assertAuthorizationMissing($history[1]['request']);
        $this->assertAuthorizationMissing($history[2]['request']);
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
        $this->assertAuthorizationPresent($sent);
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

    public static function authorizationHeaderNames(): array
    {
        return [
            'canonical' => ['Authorization'],
            'lowercase' => ['authorization'],
            'mixed case' => ['aUtHoRiZaTiOn'],
        ];
    }

    public static function sameOriginPostRedirects(): array
    {
        return [
            'POST becomes GET' => [302, 'GET'],
            'POST stays POST' => [307, 'POST'],
        ];
    }

    public static function authorizationHeaders(): array
    {
        return [
            'virtual header' => ['Authorization', []],
            'lowercase removal' => ['authorization', []],
            'uppercase removal' => ['AUTHORIZATION', []],
            'explicit header' => ['Authorization', ['Authorization' => 'Bearer existing']],
            'mixed-case explicit header' => ['aUtHoRiZaTiOn', ['authorization' => 'Bearer existing']],
        ];
    }

    public static function redirectTargets(): array
    {
        return [
            'different host' => ['https://other.example.org/next', false],
            'different port' => ['https://api.example.org:8443/next', false],
            'different scheme' => ['http://api.example.org/next', false],
            'same origin' => ['https://api.example.org/next', true],
            'relative redirect' => ['/next', true],
        ];
    }

    private function assertAuthorizationPresent(RequestInterface $request, string $value = 'Bearer core-token'): void
    {
        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertTrue($request->hasHeader('authorization'));
        $this->assertTrue($request->hasHeader('AUTHORIZATION'));
        $this->assertSame([$value], $request->getHeader('Authorization'));
        $this->assertSame($value, $request->getHeaderLine('Authorization'));
        $this->assertSame([$value], array_change_key_case($request->getHeaders(), CASE_LOWER)['authorization']);
    }

    private function assertAuthorizationMissing(RequestInterface $request): void
    {
        $this->assertFalse($request->hasHeader('Authorization'));
        $this->assertSame([], $request->getHeader('Authorization'));
        $this->assertSame('', $request->getHeaderLine('Authorization'));
        foreach ($request->getHeaders() as $name => $values) {
            $this->assertNotSame('authorization', strtolower($name));
        }
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
