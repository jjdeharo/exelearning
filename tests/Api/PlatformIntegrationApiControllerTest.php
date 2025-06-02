<?php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Entity\net\exelearning\Entity\User;
use App\Settings;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\net\exelearning\Controller\Api\PlatformIntegrationApiController
 */
final class PlatformIntegrationApiControllerTest extends WebTestCase
{
    public function testNewOdeRedirectsToWorkarea(): void
    {
        $client = self::createClient();
        $jwt = 'dummy-jwt';

        $client->request('GET', '/new_ode', ['jwt_token' => $jwt]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/workarea?newOde=new&jwt_token=' . $jwt);
    }

    public function testEditOdeRedirectsWithOdeId(): void
    {
        $client = self::createClient();
        $jwt = 'tok';
        $odeId = 42;

        $client->request('GET', '/edit_ode', [
            'ode_id' => $odeId,
            'jwt_token' => $jwt,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/workarea?odeId=' . $odeId . '&jwt_token=' . $jwt);
    }

    public function testSecondTypeEditRedirectsWithItemUuid(): void
    {
        $client = self::createClient();
        $itemUuid = 'abc-123';

        $client->request('GET', '/edit_second_type_platform_ode', [
            'item_uuid' => $itemUuid,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/workarea?item_uuid=' . $itemUuid);
    }

    public function testSetPlatformNewOdeWithoutSessionIdReturnsError(): void
    {
        $client = self::createClient();

        $em = self::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->find(1); // Assumes user ID 1 exists

        // Authenticate as the test user (session cookie is set)
        $client->loginUser($user);

        // Create a JWT token with required and optional claims
        $jwt = JWT::encode(
            [
                'user_id' => $user->getId(),
                'exp' => time() + 3600,
                'returnurl' => 'http://localhost/dummy',
                'pkgtype' => 'scorm', // or 'webzip'
                'cmid' => 0,
            ],
            $_ENV['APP_SECRET'],
            Settings::JWT_SECRET_HASH
        );

        // Send POST request without `odeSessionId`, expecting error response
        $client->request(
            'POST',
            '/api/platform/integration/set_platform_new_ode',
            [
                'jwt_token' => $jwt,
            ]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $payload = json_decode($client->getResponse()->getContent(), true);
        self::assertSame(['responseMessage' => 'error: invalid data'], $payload);
    }
}
