<?php

declare(strict_types=1);

namespace ParisBouge\OAuth2\Client\Test\Provider;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use League\OAuth2\Client\Token\AccessToken;
use ParisBouge\OAuth2\Client\Provider\Deezer;
use ParisBouge\OAuth2\Client\Provider\DeezerResourceOwner;
use ParisBouge\OAuth2\Client\Provider\Exception\DeezerIdentityProviderException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class FooDeezerProvider extends Deezer
{
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        return json_decode(file_get_contents(__DIR__ . '/../fixtures/user.json'), true);
    }
}

/**
 * @internal
 * @covers \ParisBouge\OAuth2\Client\Provider\Deezer
 */
class DeezerTest extends TestCase
{
    /**
     * @var Deezer
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new Deezer([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_client_secret',
            'redirectUri' => 'none',
            'responseType' => Deezer::RESPONSE_TYPE,
        ]);
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();

        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetBaseAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertSame('/oauth/auth.php', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $params = [];

        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);

        $this->assertSame('/oauth/access_token.php', $uri['path']);
    }

    public function testGetResourceOwnerDetailsUrl(): void
    {
        $accessToken = $this->createMock(AccessToken::class);

        $url = $this->provider->getResourceOwnerDetailsUrl($accessToken);
        $uri = parse_url($url);

        $this->assertSame('/user/me', $uri['path']);
    }

    public function testGetAccessToken(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $body = '{"access_token": "mock_access_token", "expires_in": 3600}';
        $stream = Utils::streamFor($body);

        $response->method('getBody')->willReturn($stream);
        $response->method('getHeader')->willReturn(['content-type' => 'json']);
        $response->method('getStatusCode')->willReturn(200);

        $client = $this->createMock(ClientInterface::class);
        $client->method('send')->willReturn($response);

        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertSame('mock_access_token', $token->getToken());
        $this->assertLessThanOrEqual(time() + 3600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testGetResourceOwner(): void
    {
        $provider = new FooDeezerProvider();

        $token = $this->createMock(AccessToken::class);
        /** @var DeezerResourceOwner $user */
        $resourceOwner = $provider->getResourceOwner($token);

        $this->assertNull($resourceOwner->getBirthday());
        $this->assertSame('FR', $resourceOwner->getCountry());
        $this->assertSame('foo@bar.com', $resourceOwner->getEmail());
        $this->assertSame('explicit_display', $resourceOwner->getExplicitContentLevel());
        $this->assertSame([
            'explicit_display',
            'explicit_no_recommendation',
            'explicit_hide',
        ], $resourceOwner->getExplicitContentLevelsAvailable());
        $this->assertSame('Julien', $resourceOwner->getFirstname());
        $this->assertNull($resourceOwner->getGender());
        $this->assertSame('347832', $resourceOwner->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $resourceOwner->getInscriptionDate());
        $inscriptionDate = new \DateTimeImmutable('2007-08-13 00:00:00');
        $this->assertSame($inscriptionDate->getTimestamp(), $resourceOwner->getInscriptionDate()->getTimestamp());
        $this->assertFalse($resourceOwner->isKid());
        $this->assertSame('fr', $resourceOwner->getLang());
        $this->assertSame('Foobar', $resourceOwner->getLastname());
        $this->assertSame('https://www.deezer.com/profile/347832', $resourceOwner->getLink());
        $this->assertSame('julien', $resourceOwner->getName());

        $this->assertSame('https://api.deezer.com/user/347832/image', $resourceOwner->getPicture());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/56x56-000000-80-0-0.jpg', $resourceOwner->getPictureSmall());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/250x250-000000-80-0-0.jpg', $resourceOwner->getPictureMedium());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/500x500-000000-80-0-0.jpg', $resourceOwner->getPictureBig());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/1000x1000-000000-80-0-0.jpg', $resourceOwner->getPictureXl());

        $this->assertSame(0, $resourceOwner->getStatus());
        $this->assertSame('https://api.deezer.com/user/347832/flow', $resourceOwner->getTracklist());
    }

    public function testCheckResponseFailureWithRegularError(): void
    {
        $this->expectException(DeezerIdentityProviderException::class);
        $this->expectExceptionMessage('No data returned.');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $data = [
            'error' => [
                'type' => 'DataException',
                'message' => 'No data returned.',
            ],
        ];

        $this->callMethod('checkResponse', [$response, $data]);
    }

    public function testCheckResponseFailureWithWrongCode(): void
    {
        $this->expectException(DeezerIdentityProviderException::class);
        $this->expectExceptionMessage('Wrong code.');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $data = [
            'wrong_code' => '',
        ];

        $this->callMethod('checkResponse', [$response, $data]);
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    protected function callMethod($name, array $args = [])
    {
        try {
            $reflection = new \ReflectionMethod(\get_class($this->provider), $name);
            $reflection->setAccessible(true);

            return $reflection->invokeArgs($this->provider, $args);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
}
