<?php
declare(strict_types=1);

namespace App\Tests\E2E;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use Symfony\Component\Panther\PantherTestCase;

/**
 * End-to-end test for new node creation.
 */
class NewNodeTest extends ExelearningE2EBase
{
    /**
     * Tests the new node functionality.
     */
    public function testNewNode(): void
    {
        // Call the base class login method that gets the PantherClient object
        $client = $this->login();

        // Wait for the interface to fully load
        $client->waitForInvisibility('#load-screen-main');

        // Close any confirmation modals
        $this->closeConfirmationModals($client);

        // Click the button that opens the new node modal
        $client->getCrawler()->filter('.button_nav_action')->click();

        // Wait for the modal to appear
        $client->waitFor('#modalConfirm');


        // Wait for presence of #input-new-node
        $client->waitFor('#input-new-node'); 


        // Generate a random node name
        $newNodeName = 'new node ' . uniqid();

        // Type the new node name in the input using Panther's type method
        $client->getCrawler()->filter('#input-new-node')->sendKeys($newNodeName);

        // // Dump the full page HTML, for debugging
        // dump($client->getPageSource());

        // Wait for presence of #input-new-node
        $client->waitFor('button.confirm.btn.btn-primary'); 
        
        // Click on the "save" button
        $client->getCrawler()->filter('#modalConfirm > div > div > div.modal-footer > button.confirm.btn.btn-primary')->click();

        // Wait until the new node appears
        $client->waitFor('#nav_list > div > div > div.nav-element.toggle-off.selected > span.nav-element-text > span.node-text-span');

        // Assert that the span with class 'node-text-span' contains the $newNodeName
        $this->assertSelectorWillContain('#nav_list > div > div > div.nav-element.toggle-off.selected > span.nav-element-text > span.node-text-span', $newNodeName);
    }

    /**
     * Closes the confirmation modal if present.
     */
    private function closeConfirmationModals($client): void
    {
        try {
            $client->executeScript("
                let modal = document.querySelector('.modal-confirm .cancel.btn.btn-secondary');
                if (modal) modal.click();
            ");
            // Wait for the confirmation modal backdrop to disappear
            $client->waitForInvisibility('.modal-backdrop');
            sleep(1);
        } catch (\Exception $e) {
            // Ignore errors if the modal is not present
        }
    }
}
