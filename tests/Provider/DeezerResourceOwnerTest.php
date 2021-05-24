<?php

declare(strict_types=1);

namespace ParisBouge\OAuth2\Client\Test\Provider;

use ParisBouge\OAuth2\Client\Provider\DeezerResourceOwner;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \ParisBouge\OAuth2\Client\Provider\DeezerResourceOwner
 */
class DeezerResourceOwnerTest extends TestCase
{
    /**
     * @var DeezerResourceOwner
     */
    protected $resourceOwner;

    protected function setUp(): void
    {
        $user = json_decode(file_get_contents(__DIR__ . '/../fixtures/user.json'), true);
        $this->resourceOwner = new DeezerResourceOwner($user);
    }

    public function testGetter(): void
    {
        $this->assertNull($this->resourceOwner->getBirthday());
        $this->assertSame('FR', $this->resourceOwner->getCountry());
        $this->assertSame('foo@bar.com', $this->resourceOwner->getEmail());
        $this->assertSame('explicit_display', $this->resourceOwner->getExplicitContentLevel());
        $this->assertSame([
            'explicit_display',
            'explicit_no_recommendation',
            'explicit_hide',
        ], $this->resourceOwner->getExplicitContentLevelsAvailable());
        $this->assertSame('Julien', $this->resourceOwner->getFirstname());
        $this->assertNull($this->resourceOwner->getGender());
        $this->assertSame('347832', $this->resourceOwner->getId());
        $this->assertInstanceOf(\DateTimeInterface::class, $this->resourceOwner->getInscriptionDate());
        $inscriptionDate = new \DateTimeImmutable('2007-08-13 00:00:00');
        $this->assertSame($inscriptionDate->getTimestamp(), $this->resourceOwner->getInscriptionDate()->getTimestamp());
        $this->assertFalse($this->resourceOwner->isKid());
        $this->assertSame('fr', $this->resourceOwner->getLang());
        $this->assertSame('Foobar', $this->resourceOwner->getLastname());
        $this->assertSame('https://www.deezer.com/profile/347832', $this->resourceOwner->getLink());
        $this->assertSame('julien', $this->resourceOwner->getName());

        $this->assertSame('https://api.deezer.com/user/347832/image', $this->resourceOwner->getPicture());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/56x56-000000-80-0-0.jpg', $this->resourceOwner->getPictureSmall());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/250x250-000000-80-0-0.jpg', $this->resourceOwner->getPictureMedium());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/500x500-000000-80-0-0.jpg', $this->resourceOwner->getPictureBig());
        $this->assertSame('https://cdns-images.dzcdn.net/images/user/7e2903c9177831aefae4a034e67f9cf4/1000x1000-000000-80-0-0.jpg', $this->resourceOwner->getPictureXl());

        $this->assertSame(0, $this->resourceOwner->getStatus());
        $this->assertSame('https://api.deezer.com/user/347832/flow', $this->resourceOwner->getTracklist());
    }

    public function testToArray(): void
    {
        $array = json_decode(file_get_contents(__DIR__ . '/../fixtures/user.json'), true);

        $this->assertSame($array, $this->resourceOwner->toArray());
    }
}
