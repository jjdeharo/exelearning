<?php

// src/Security/OidcUserInfoTokenHandlerCustom.php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\FallbackUserLoader;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Custom OIDC UserInfo Token Handler (temporary until Symfony 7.3).
 *
 * @deprecated Remove this class when Symfony 7.3 is stable.
 */
class OidcUserInfoTokenHandlerCustom implements AccessTokenHandlerInterface
{
    private ?CacheInterface $discoveryCache = null;
    private ?string $oidcConfigurationCacheKey = null;

    public function __construct(
        private HttpClientInterface $client,
        private string $oidcIssuer,
        private LoggerInterface $logger,
        private string $claim = 'sub',
    ) {
    }

    /**
     * Enables OIDC discovery with caching.
     */
    public function enableDiscovery(CacheInterface $cache, string $oidcConfigurationCacheKey): void
    {
        $this->discoveryCache = $cache;
        $this->oidcConfigurationCacheKey = $oidcConfigurationCacheKey;
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $userinfoEndpoint = $this->oidcIssuer.'/connect/userinfo'; // Default fallback

        if (null !== $this->discoveryCache) {
            try {
                $oidcConfiguration = json_decode(
                    $this->discoveryCache->get($this->oidcConfigurationCacheKey, function (): string {
                        $response = $this->client->request('GET', $this->oidcIssuer.'/.well-known/openid-configuration');

                        return $response->getContent();
                    }),
                    true,
                    512,
                    \JSON_THROW_ON_ERROR
                );

                $userinfoEndpoint = $oidcConfiguration['userinfo_endpoint'] ?? $userinfoEndpoint;
            } catch (\Throwable $e) {
                $this->logger?->error('An error occurred while requesting OIDC configuration.', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw new BadCredentialsException('Invalid credentials.', $e->getCode(), $e);
            }
        }

        try {
            // Call OIDC userinfo endpoint
            $claims = $this->client->request('GET', $userinfoEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                ],
            ])->toArray();

            // Check if exists the specified claim, fallback to "sub" if not.
            if (empty($claims[$this->claim])) {
                $this->logger->warning(sprintf(
                    'Claim "%s" not found or empty in OIDC response. Falling back to "sub".',
                    $this->claim
                ));

                $identifier = $claims['sub'];
            } else {
                $identifier = $claims[$this->claim];
            }

            return new UserBadge(
                $identifier,
                new FallbackUserLoader(fn () => $this->createUser($claims)),
                $claims
            );
        } catch (\Exception $e) {
            $this->logger?->error('An error occurred on OIDC server.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new BadCredentialsException('Invalid credentials.', $e->getCode(), $e);
        }
    }

    /**
     * Custom user creation logic (implement if needed).
     */
    private function createUser(array $claims)
    {
        // Implement user creation or retrieval logic
    }
}
