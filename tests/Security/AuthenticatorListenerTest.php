<?php

namespace App\Tests\Security;

use App\Security\AuthenticatorListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticatorListenerTest extends TestCase
{
    private AuthenticatorListener $listener;
    
    protected function setUp(): void
    {
        $this->listener = new AuthenticatorListener();
    }
    
    public function testOnLoginSuccessEventWithTicket(): void
    {
        // Create a request with a ticket parameter
        $request = Request::create('https://example.com/?ticket=ST-1234-abcdefg&other=value');
        
        // Mock the authenticator
        $authenticator = $this->createMock(AuthenticatorInterface::class);
        
        // Mock the passport
        $passport = $this->createMock(Passport::class);
        
        // Mock the token
        $token = $this->createMock(TokenInterface::class);
        
        // Create the event
        $event = new LoginSuccessEvent($authenticator, $passport, $token, $request, null, 'main');
        
        // Call the listener
        $this->listener->onLoginSuccessEvent($event);
        
        // Get the response from the event
        $response = $event->getResponse();
        
        // Assert that a redirect response was created
        $this->assertInstanceOf(RedirectResponse::class, $response);
        
        // Assert that the URL doesn't contain the ticket parameter
        $this->assertEquals('https://example.com/?other=value', $response->getTargetUrl());
    }
    
    public function testOnLoginSuccessEventWithAccessToken(): void
    {
        // Create a request with an access_token parameter
        $request = Request::create('https://example.com/?access_token=eyJhbGciOiJIUzI1NiJ9&page=1');
        
        // Mock the authenticator
        $authenticator = $this->createMock(AuthenticatorInterface::class);
        
        // Mock the passport
        $passport = $this->createMock(Passport::class);
        
        // Mock the token
        $token = $this->createMock(TokenInterface::class);
        
        // Create the event
        $event = new LoginSuccessEvent($authenticator, $passport, $token, $request, null, 'main');
        
        // Call the listener
        $this->listener->onLoginSuccessEvent($event);
        
        // Get the response from the event
        $response = $event->getResponse();
        
        // Assert that a redirect response was created
        $this->assertInstanceOf(RedirectResponse::class, $response);
        
        // Assert that the URL doesn't contain the access_token parameter
        $this->assertEquals('https://example.com/?page=1', $response->getTargetUrl());
    }
    
    public function testOnLoginSuccessEventWithNoSensitiveParams(): void
    {
        // Create a request with no sensitive parameters
        $request = Request::create('https://example.com/?page=1&sort=desc');
        
        // Mock the authenticator
        $authenticator = $this->createMock(AuthenticatorInterface::class);
        
        // Mock the passport
        $passport = $this->createMock(Passport::class);
        
        // Mock the token
        $token = $this->createMock(TokenInterface::class);
        
        // Create the event
        $event = new LoginSuccessEvent($authenticator, $passport, $token, $request, null, 'main');
        
        // Call the listener
        $this->listener->onLoginSuccessEvent($event);
        
        // Get the response from the event
        $response = $event->getResponse();
        
        // Assert that a redirect response was created
        $this->assertInstanceOf(RedirectResponse::class, $response);
        
        // Assert that the URL remains unchanged
        $this->assertEquals('https://example.com/?page=1&sort=desc', $response->getTargetUrl());
    }
}