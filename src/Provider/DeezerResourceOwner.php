<?php

namespace ParisBouge\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DeezerResourceOwner implements ResourceOwnerInterface
{
    protected $data = [];

    public function __construct(array $response)
    {
        $this->data = $response;
    }

    public function getBirthday()
    {
        if (null === $this->data['birthday'] || '0000-00-00' === $this->data['birthday']) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->data['birthday'] . '00:00:00');
        if (false === $date) {
            return null;
        }

        return $date;
    }

    public function getCountry()
    {
        return isset($this->data['country']) ? $this->data['country'] : null;
    }

    public function getEmail()
    {
        return isset($this->data['email']) ? $this->data['email'] : null;
    }

    public function getExplicitContentLevel()
    {
        return isset($this->data['explicit_content_level'])? $this->data['explicit_content_level'] : null;
    }

    public function getExplicitContentLevelsAvailable()
    {
        return isset($this->data['explicit_content_levels_available']) ? $this->data['explicit_content_levels_available'] : [];
    }

    public function getFirstname()
    {
        return isset($this->data['firstname']) ? $this->data['firstname'] : null;
    }

    public function getGender()
    {
        if (!\in_array($this->data['gender'], ['F', 'M'], true)) {
            return null;
        }

        return $this->data['gender'];
    }

    public function getId()
    {
        return (string) $this->data['id'];
    }

    public function getInscriptionDate()
    {
        if (null === $this->data['inscription_date']) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->data['inscription_date'] . '00:00:00');
        if (false === $date) {
            return null;
        }

        return $date;
    }

    public function isKid()
    {
        return $this->data['is_kid'];
    }

    public function getLang()
    {
        return $this->data['lang'];
    }

    public function getLastname()
    {
        return isset($this->data['lastname']) ? $this->data['lastname'] : null;
    }

    public function getLink()
    {
        return isset($this->data['link']) ? $this->data['link'] : null;
    }

    public function getName()
    {
        return isset($this->data['name']) ? $this->data['name'] : null;
    }

    public function getPicture()
    {
        return isset($this->data['picture']) ? $this->data['picture'] : null;
    }

    public function getPictureSmall()
    {
        return isset($this->data['picture_small']) ? $this->data['picture_small'] : null;
    }

    public function getPictureMedium()
    {
        return isset($this->data['picture_medium']) ? $this->data['picture_medium'] : null;
    }

    public function getPictureBig()
    {
        return isset($this->data['picture_big']) ? $this->data['picture_big'] : null;
    }

    public function getPictureXl()
    {
        return isset($this->data['picture_xl']) ? $this->data['picture_xl'] : null;
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function getTracklist()
    {
        return $this->data['tracklist'];
    }

    /**
     * Return all of the owner details available as an array.
     */
    public function toArray()
    {
        return $this->data;
    }
}
