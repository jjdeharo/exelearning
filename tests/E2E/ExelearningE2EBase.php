<?php
declare(strict_types=1);

namespace App\Tests\E2E;

use App\Kernel;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;
use Facebook\WebDriver\WebDriverBy;


/**
 * Base class for eXeLearning end-to-end tests.
 *
 * Provides default Panther client creation with consistent configuration,
 * including an external base URI for remote Selenium usage.
 */
abstract class ExelearningE2EBase extends PantherTestCase
{
    protected static string $defaultUserEmail;
    protected static string $defaultUserPass;
    protected static string $baseUrl;

    /**
     * Initializes static variables with environment defaults.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$defaultUserEmail = $_ENV['TEST_USER_EMAIL'];
        self::$defaultUserPass  = $_ENV['TEST_USER_PASSWORD'];
        self::$baseUrl          = 'http://exelearning:8080';
        //self::$baseUrl          = 'http://172.19.0.2:8080';
    }

    /**
     * Returns the application Kernel class name.
     *
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    /**
     * Creates the default options for Panther with remote Selenium.
     *
     * @return array<string, mixed>
     */
    protected static function getDefaultPantherOptions(): array
    {
        return [
            'browser'             => PantherTestCase::SELENIUM,
            'external_base_uri'   => self::$baseUrl, // Important for remote containers
            'port'                => 9080,           // Not used if external_base_uri is set, but safe to keep
            // Additional keys if you need them, e.g. 'webServerDir'
        ];
    }

    /**
     * Creates the default manager (Selenium) options with Chrome capabilities.
     *
     * @return array<string, mixed>
     */
    protected static function getDefaultPantherManagerOptions(): array
    {
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability('goog:chromeOptions', [
            'args' => [
                '--headless',
                '--no-sandbox',
                '--disable-gpu',
                '--disable-dev-shm-usage',
            ],
        ]);

        return [
            'host'         => 'http://chrome:9515', // Container name and port for Selenium
            //'host'         => 'http://172.19.0.1:9515', // Container name and port for Selenium
            'capabilities' => $capabilities,
        ];
    }

    /**
     * Creates a Panther Client with the default configuration.
     *
     * @return Client
     */
    protected static function createTestClient(): Client
    {
        return static::createPantherClient(
            self::getDefaultPantherOptions(),
            [], // kernel options if needed
            self::getDefaultPantherManagerOptions()
        );
    }

    /**
     * Logs in to the application using the given client or a new one.
     *
     * @param Client|null $client
     * @param string|null $email
     * @param string|null $password
     * @param string|null $loginUrl
     *
     * @return Client
     */
    protected function login(
        ?Client $client = null,
        ?string $email = null,
        ?string $password = null,
        ?string $loginUrl = null
    ): Client {
        if (null === $client) {
            $client = static::createTestClient();
        }
        $email    = $email    ?? self::$defaultUserEmail;
        $password = $password ?? self::$defaultUserPass;
        $loginUrl = $loginUrl ?? (self::$baseUrl . '/login');

        // 1) Load the login page
        $client->request('GET', $loginUrl);

        // 2) Check presence (important! use the client, never use $this)
        $client->waitFor('form[id="login-form"]'); // Wait for the form
        $this->assertGreaterThan(0, $client->getCrawler()->filter('form[id="login-form"]')->count());

        // 3) Submit credentials
        $client->submitForm('btn-submit', [
            'email'    => $email,
            'password' => $password,
        ]);

        // 4) Assert user is redirected
        $this->assertStringContainsString('/workarea', $client->getCurrentURL());

        return $client;
    }

    public function createNewDocument(Client $client): Client
    {
        // Create new document
        $client->waitForVisibility('#dropdownFile', 2);
        $client->getWebDriver()->findElement(WebDriverBy::id('dropdownFile'))->click();
        $client->getWebDriver()->findElement(WebDriverBy::id('navbar-button-new'))->click();
        
        // Answer 'create new file without save 

        try {
            $client->waitForVisibility('#modalSessionLogout > div > div > div.modal-footer > button.session-logout-without-save.btn.btn-primary', 2);
            $client->getWebDriver()->findElement(WebDriverBy::cssSelector('#modalSessionLogout > div > div > div.modal-footer > button.session-logout-without-save.btn.btn-primary'))->click();
        } catch (\Exception $e) {
            // continue if modal is not present            
        }

        sleep(1);

        return $client;
    }

    /**
     * Captures screenshots of all open browser windows.
     *
     * @param Client $client
     * @param string $clientName
     *
     * @return void
     */
    protected function captureAllWindowsScreenshots(Client $client, string $clientName = 'c1'): void
    {
        $screenshotDir = sys_get_temp_dir() . '/e2e_screenshots';
        if (!is_dir($screenshotDir)) {
            mkdir($screenshotDir, 0777, true);
        }

        $timestamp = date('Ymd-His');
        $testName  = str_replace(['\\', ':', ' '], '_', $this->name());
        $handles   = $client->getWindowHandles();

        foreach ($handles as $index => $handle) {
            $client->switchTo()->window($handle);
            $filename = sprintf(
                '%s/%s-%s-w%d-%s.png',
                $screenshotDir,
                $timestamp,
                $testName,
                $index + 1,
                $clientName
            );
            $client->takeScreenshot($filename);
            echo "\n[Screenshot saved]: $filename\n";
        }
    }

    /**
     * Captures and displays browser console logs (JavaScript errors, warnings, etc).
     *
     * @param Client $client
     *
     * @return void
     */
    protected function captureBrowserConsoleLogs(Client $client): void
    {
        $logs = $client->getWebDriver()->manage()->getLog('browser');

        foreach ($logs as $logEntry) {
            $level = strtoupper($logEntry['level']);
            $message = $logEntry['message'];
            echo "\n[Browser Console][$level]: $message\n";
        }
    }


    /**
     * Takes screenshots of the main client if available.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // If you want to capture screenshots from a single "default" client
        // If you keep $this->client, or you may remove it
    }
}
