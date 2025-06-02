<?php
namespace App\Tests\Functional;

use Firebase\JWT\JWT;
use App\Settings;                       // Where JWT_SECRET_HASH is declared
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class OdeEntryPointTest extends WebTestCase
{
    public function testNewOdeWithValidToken(): void
    {
        $client = self::createClient();

        // Minimum payload required
        $payload = [
            'action'    => '/new_ode',
            'user_id'   => 2,
            'pkgtype'   => 'scorm',              // or 'webzip' to test the other branch
            'returnurl' => 'http://localhost/mod/exescorm/view.php?id=123',
            'iat'       => time(),
            'exp'       => time() + 3600,
        ];

        $jwt = JWT::encode(
            $payload,
            $_ENV['APP_SECRET'],                 // Same pass IntegrationUtil reads
            Settings::JWT_SECRET_HASH            // Usually 'HS256'
        );

        $client->request(
            'GET',
            '/new_ode',
            ['jwt_token' => $jwt]                // How your controller expects it
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $expected = '/workarea?newOde=new&jwt_token='.$jwt;
        self::assertResponseRedirects($expected);
    }
}
