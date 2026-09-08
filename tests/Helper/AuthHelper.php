<?php

declare(strict_types=1);

namespace App\Tests\Helper;

/**
 * Requests an access token via password grant from the oidc provider (keycloak, see docker-compose.yml /
 * .github/workflows/ci.yml, no auth mocking): the client secret and the user password are read from the realm
 * import.
 */
final class AuthHelper
{
    private const string CLIENT_ID = 'petstore';

    private const string USERNAME = 'petstore';

    // keycloak needs a while to boot and import the realm
    private const int OIDC_PROVIDER_TIMEOUT_IN_SECONDS = 120;

    private static ?string $authorization = null;

    public static function getAuthorizationHeader(): string
    {
        if (null === self::$authorization) {
            self::$authorization = 'Bearer '.self::requestAccessToken();
        }

        return self::$authorization;
    }

    private static function requestAccessToken(): string
    {
        $issuer = getenv('OIDC_ISSUER');

        if (false === $issuer) {
            throw new \RuntimeException('Missing env variable "OIDC_ISSUER"');
        }

        $realm = self::readRealmImport();

        $response = self::httpRequest(self::resolveTokenEndpoint($issuer), http_build_query([
            'grant_type' => 'password',
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::resolveClientSecret($realm),
            'username' => self::USERNAME,
            'password' => self::resolvePassword($realm),
        ]));

        if (null === $response || 200 !== $response['status']) {
            throw new \RuntimeException(\sprintf(
                'Cannot request access token: status %s, body %s',
                null !== $response ? (string) $response['status'] : 'unknown',
                null !== $response ? $response['body'] : 'unknown'
            ));
        }

        /** @var array{access_token: string} $token */
        $token = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

        return $token['access_token'];
    }

    private static function resolveTokenEndpoint(string $issuer): string
    {
        $url = $issuer.'/.well-known/openid-configuration';

        for ($i = 0; $i < self::OIDC_PROVIDER_TIMEOUT_IN_SECONDS; ++$i) {
            $response = self::httpRequest($url);

            if (null !== $response && 200 === $response['status']) {
                /** @var array{token_endpoint: string} $configuration */
                $configuration = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);

                return $configuration['token_endpoint'];
            }

            echo 'wait for oidc provider to be up and running...'.PHP_EOL;

            sleep(1);
        }

        throw new \RuntimeException(\sprintf('Timeout in waiting for oidc provider "%s"', $issuer));
    }

    private static function readRealmImport(): array
    {
        $path = __DIR__.'/../../docker/development/keycloak/import/petstore-realm.json';

        $content = file_get_contents($path);

        if (false === $content) {
            throw new \RuntimeException(\sprintf('Cannot read realm import "%s"', $path));
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    private static function resolveClientSecret(array $realm): string
    {
        foreach ($realm['clients'] as $client) {
            if (self::CLIENT_ID === $client['clientId'] && isset($client['secret'])) {
                return $client['secret'];
            }
        }

        throw new \RuntimeException(\sprintf('Missing secret for client "%s" in realm import', self::CLIENT_ID));
    }

    private static function resolvePassword(array $realm): string
    {
        foreach ($realm['users'] as $user) {
            if (self::USERNAME !== $user['username']) {
                continue;
            }

            foreach ($user['credentials'] as $credential) {
                if ('password' === $credential['type']) {
                    return $credential['value'];
                }
            }
        }

        throw new \RuntimeException(\sprintf('Missing password for user "%s" in realm import', self::USERNAME));
    }

    /**
     * @return null|array{status: int, body: string}
     */
    private static function httpRequest(string $url, ?string $body = null): ?array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        if (null !== $body) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        if (false === $responseBody || !\is_string($responseBody)) {
            return null;
        }

        return ['status' => (int) $status, 'body' => $responseBody];
    }
}
