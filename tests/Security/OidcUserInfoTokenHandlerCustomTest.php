<?php

namespace App\Tests\Security;

use App\Security\OidcUserInfoTokenHandlerCustom;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class OidcUserInfoTokenHandlerCustomTest extends TestCase
{
    private OidcUserInfoTokenHandlerCustom $handler;
    private MockHttpClient $httpClient;
    private LoggerInterface $logger;
    
    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }
    
    public function testGetUserBadgeFromWithValidToken(): void
    {
        // Create a response with valid user info.
        $responseData = json_encode([
            'sub'   => 'user123',
            'email' => 'test@example.com',
            'name'  => 'Test User'
        ]);

        // Variable to capture request details.
        $lastRequest = null;
        $callback = function (string $method, string $url, array $options) use ($responseData, &$lastRequest) {
            $lastRequest = [
                'method'  => $method,
                'url'     => $url,
                'options' => $options,
            ];
            return new MockResponse($responseData);
        };
        $this->httpClient = new MockHttpClient($callback);
        
        // Instantiate the handler with the mock client.
        $this->handler = new OidcUserInfoTokenHandlerCustom(
            $this->httpClient,
            'https://oidc.example.com',
            $this->logger,
            'email'
        );
        
        // Call the method with a valid token.
        $userBadge = $this->handler->getUserBadgeFrom('valid-token');
        $this->assertEquals('test@example.com', $userBadge->getUserIdentifier());
        
        // Assert that the request contains the correct parameters.
        $this->assertNotNull($lastRequest);
        $this->assertEquals('GET', $lastRequest['method']);
        $this->assertEquals('https://oidc.example.com/connect/userinfo', $lastRequest['url']);

		$this->assertArrayHasKey('headers', $lastRequest['options']);
		$this->assertIsArray($lastRequest['options']['headers']);

		// Convert headers array to associative format
		$headers = [];
		foreach ($lastRequest['options']['headers'] as $header) {
		    if (is_string($header) && str_contains($header, ':')) {
		        [$key, $value] = explode(':', $header, 2);
		        $headers[strtolower(trim($key))] = trim($value);
		    }
		}

		// Ensure the authorization header exists
		$this->assertArrayHasKey('authorization', $headers);
		$this->assertEquals('Bearer valid-token', $headers['authorization']);


    }
    
    public function testGetUserBadgeFromWithMissingClaim(): void
    {
        // Create a response with a missing email claim.
        $mockResponse = new MockResponse(json_encode([
            'sub'  => 'user123',
            'name' => 'Test User'
            // 'email' claim is missing.
        ]));

        $this->httpClient = new MockHttpClient($mockResponse);

        $this->handler = new OidcUserInfoTokenHandlerCustom(
            $this->httpClient,
            'https://oidc.example.com',
            $this->logger,
            'email' // The claim we are looking for
        );

        // Expect that it will log a warning but not throw an exception.
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Claim "email" not found or empty in OIDC response'));

        // It should fall back to 'sub'.
        $userBadge = $this->handler->getUserBadgeFrom('valid-token');

        $this->assertInstanceOf(UserBadge::class, $userBadge);
        $this->assertEquals('user123', $userBadge->getUserIdentifier());
    }
    
    public function testGetUserBadgeFromWithHttpClientError(): void
    {
        // Create a response simulating a server error.
        $mockResponse = new MockResponse('Server Error', [
            'http_code' => 500,
        ]);
        
        $this->httpClient = new MockHttpClient($mockResponse);
        
        $this->handler = new OidcUserInfoTokenHandlerCustom(
            $this->httpClient,
            'https://oidc.example.com',
            $this->logger,
            'email'
        );
        
        // Expect a BadCredentialsException due to the HTTP error.
        $this->expectException(BadCredentialsException::class);
        $this->handler->getUserBadgeFrom('valid-token');
    }
    
    public function testGetUserBadgeFromWithDiscovery(): void
    {
        // Create a mock cache that returns a custom discovery configuration.
        $cache = $this->createMock(\Symfony\Contracts\Cache\CacheInterface::class);
        $cache->method('get')
            ->willReturn(json_encode([
                'userinfo_endpoint' => 'https://oidc.example.com/custom/userinfo'
            ]));
        
        $responseData = json_encode([
            'sub'   => 'user123',
            'email' => 'test@example.com',
            'name'  => 'Test User'
        ]);
        
        // Variable to capture request details.
        $lastRequest = null;
        $callback = function (string $method, string $url, array $options) use ($responseData, &$lastRequest) {
            $lastRequest = [
                'method'  => $method,
                'url'     => $url,
                'options' => $options,
            ];
            return new MockResponse($responseData);
        };
        $this->httpClient = new MockHttpClient($callback);
        
        $this->handler = new OidcUserInfoTokenHandlerCustom(
            $this->httpClient,
            'https://oidc.example.com',
            $this->logger,
            'email'
        );
        
        // Enable discovery with the custom cache.
        $this->handler->enableDiscovery($cache, 'oidc_config');
        
        $userBadge = $this->handler->getUserBadgeFrom('valid-token');
        $this->assertEquals('test@example.com', $userBadge->getUserIdentifier());
        
        // Assert that the request uses the custom discovery endpoint.
        $this->assertNotNull($lastRequest);
        $this->assertEquals('GET', $lastRequest['method']);
        $this->assertEquals('https://oidc.example.com/custom/userinfo', $lastRequest['url']);
    }
}
