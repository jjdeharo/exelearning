<?php
declare(strict_types=1);

namespace App\Tests\E2E\RealTime;

use Symfony\Component\Panther\PantherTestCase;

/**
 * Example test that uses two real-time clients.
 */
class SomeRealTimeFeatureTest extends ExelearningRealTimeE2EBase
{
    public function testTwoClientsSeeSameDocument(): void
    {

        // $this->markTestSkipped('disabled until we release the new test sysstem');


        // 1) Create both browsers and log them in
        $this->createRealTimeClients();

        // 2) Main client navigates somewhere
        $this->mainClient->request('GET', '/workarea');

        // 3) Possibly retrieve a share URL
        $shareUrl = $this->getMainShareUrl();
        $this->assertNotEmpty($shareUrl, 'Expected a share URL from main client.');

        // 4) Secondary client visits the same URL
        $this->secondaryClient->request('GET', $shareUrl);

        // 5) Wait for real-time elements in the secondary client
        //    NOTE: The "assertSelectorTextContains" runs in the main client,
        //    so if you want to check a secondary client’s DOM, call $this->secondaryClient->waitFor()...
        // $this->secondaryClient->waitFor('.some-realtime-indicator');

        // // 6) Switch to main client (the default context) and verify
        // $this->assertSelectorTextContains('.some-realtime-element', 'Synchronized');
    }
}
