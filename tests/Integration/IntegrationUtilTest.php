<?php
declare(strict_types=1);

namespace App\Tests\Integration;

use App\Constants;
use App\Settings;
use App\Util\net\exelearning\Util\IntegrationUtil;
use Firebase\JWT\JWT;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class IntegrationUtilTest extends TestCase
{
    private const SECRET = 'test_secret';

    private IntegrationUtil $util;

    protected function setUp(): void
    {
        // Set environment secret required by the JWT logic
        $_ENV['APP_SECRET'] = self::SECRET;
        $this->util = new IntegrationUtil(new NullLogger());
    }

    private static function createJwt(array $payload): string
    {
        // Add issued-at and expiration to payload and encode
        return JWT::encode(
            $payload + ['iat' => time(), 'exp' => time() + 3600],
            self::SECRET,
            Settings::JWT_SECRET_HASH
        );
    }

    /* ------------------------------------------------------------------ */
    /* decodeJWT()                                                        */
    /* ------------------------------------------------------------------ */

    public function testDecodeJwtReturnsPayload(): void
    {
        $token = self::createJwt(['foo' => 'bar']);
        $decoded = $this->util->decodeJWT($token);

        self::assertIsArray($decoded);
        self::assertSame('bar', $decoded['foo']);
    }

    public function testDecodeJwtWithInvalidTokenReturnsNull(): void
    {
        self::assertNull($this->util->decodeJWT('not-a-token'));
    }

    /* ------------------------------------------------------------------ */
    /* getParamsMoodleIntegration()                                       */
    /* ------------------------------------------------------------------ */

    #[DataProvider('scormCases')]
    public function testScormParams(string $op, string $expectedLetter): void
    {
        $payload = [
            'pkgtype' => 'scorm',
            'returnurl' => 'http://example.com/mod/exescorm/view.php?id=99',
        ];

        $jwt = self::createJwt($payload);
        $params = $this->util->getParamsMoodleIntegration($jwt, $op);

        self::assertSame(Constants::EXPORT_TYPE_SCORM12, $params['exportType']);
        self::assertSame(
            "http://example.com/mod/exescorm/{$expectedLetter}et_ode.php",
            $params['platformIntegrationUrl']
        );
    }

    public static function scormCases(): iterable
    {
        yield ['set', 's'];
        yield ['get', 'g'];
    }

    public function testWebzipParamsReplacesLocalhost(): void
    {
        // Simulate access from LAN address instead of localhost
        $_SERVER['REMOTE_ADDR'] = '192.168.1.20';

        $payload = [
            'pkgtype' => 'webzip',
            'returnurl' => 'http://localhost/mod/exeweb/view.php?id=7',
        ];

        $jwt = self::createJwt($payload);
        $params = $this->util->getParamsMoodleIntegration($jwt, 'set');

        self::assertSame(Constants::EXPORT_TYPE_HTML5, $params['exportType']);
        self::assertSame(
            'http://192.168.1.20/mod/exeweb/set_ode.php',
            $params['platformIntegrationUrl']
        );
    }

    public function testUnknownPkgtypeAddsNoExtraKeys(): void
    {
        // When pkgtype is unknown, platformIntegrationUrl and exportType should not be set
        $payload = [
            'pkgtype' => 'zip',
            'returnurl' => 'http://host/mod/whatever/view.php',
        ];

        $jwt = self::createJwt($payload);
        $params = $this->util->getParamsMoodleIntegration($jwt, 'get');

        self::assertArrayNotHasKey('platformIntegrationUrl', $params);
        self::assertArrayNotHasKey('exportType', $params);
    }
}
