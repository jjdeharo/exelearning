<?php

namespace App\Security;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class, method: 'onLoginSuccessEvent')]
class AuthenticatorListener
{
    /**
     * Handles a successful login event.
     *
     * @param LoginSuccessEvent $event the event instance containing the login success data
     */
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest();

        // Remove sensitive parameters from the query string
        $request->query->remove('ticket');
        $request->query->remove('access_token');

        // Update PHP superglobals with the modified request parameters
        $request->overrideGlobals();

        // Redirect to the same URI to clean the URL from sensitive tokens
        $event->setResponse(new RedirectResponse($request->getUri()));
    }
}
