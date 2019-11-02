<div align="center">
    <a href="https://travis-ci.org/julienbornstein/oauth2-deezer" title="Build">
        <img src="https://img.shields.io/travis/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="Build">
    </a>
    <a href="https://scrutinizer-ci.com/g/julienbornstein/oauth2-deezer/" title="Coverage">
        <img src="https://img.shields.io/scrutinizer/coverage/g/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="Coverage">
    </a>
    <a href="https://scrutinizer-ci.com/g/julienbornstein/oauth2-deezer/" title="Code Quality">
        <img src="https://img.shields.io/scrutinizer/g/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="Code Quality">
    </a>
    <a href="https://php.net" title="PHP Version">
        <img src="https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=for-the-badge" alt="PHP Version">
    </a>
    <a href="https://packagist.org/packages/julienbornstein/oauth2-deezer" title="Downloads">
        <img src="https://img.shields.io/packagist/dt/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="Downloads">
    </a>
    <a href="https://packagist.org/packages/julienbornstein/oauth2-deezer" title="Latest Stable Version">
        <img src="https://img.shields.io/packagist/v/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/julienbornstein/oauth2-deezer" title="License">
        <img src="https://img.shields.io/packagist/l/julienbornstein/oauth2-deezer.svg?style=for-the-badge" alt="License">
    </a>
</div>

# Deezer Provider for OAuth 2.0 Client

This package provides Deezer OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

You can install this package using Composer:

```
composer require julienbornstein/oauth2-deezer
```

You will then need to:
* run ``composer install`` to get these dependencies added to your vendor directory
* add the autoloader to your application with this line: ``require('vendor/autoload.php');``

## Usage

Usage is the same as The League's OAuth client, using `\ParisBouge\OAuth2\Client\Provider\Deezer` as the provider.

### Authorization Code Flow

```php
$provider = new ParisBouge\OAuth2\Client\Provider\Deezer([
    'clientId'     => '{deezer-client-id}',
    'clientSecret' => '{deezer-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => [
            ParisBouge\OAuth2\Client\Provider\Deezer::SCOPE_BASIC_ACCESS,
            ParisBouge\OAuth2\Client\Provider\Deezer::SCOPE_EMAIL,
        ]
    ]);
    
    $_SESSION['oauth2state'] = $provider->getState();
    
    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the user's details
    /** @var \ParisBouge\OAuth2\Client\Provider\DeezerResourceOwner $user */
    $user = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello %s!', $user->getFirstname());
    
    echo '<pre>';
    var_dump($user);
    echo '</pre>';

} catch (Exception $e) {

    // Failed to get user details
    exit('Damned...');
}

echo '<pre>';
// Use this to interact with an API on the users behalf
var_dump($token->getToken());
# string(217) "CAADAppfn3msBAI7tZBLWg...

// The time (in epoch time) when an access token will expire
var_dump($token->getExpires());
# int(1436825866)
echo '</pre>';
```

### Authorization Scopes

The following scopes are available as described in the [official documentation](https://developers.deezer.com/api/permissions):

* SCOPE_BASIC_ACCESS
* SCOPE_EMAIL
* SCOPE_OFFLINE_ACCESS
* SCOPE_MANAGE_LIBRARY
* SCOPE_MANAGE_COMMUNITY
* SCOPE_DELETE_LIBRARY
* SCOPE_LISTENING_HISTORY

## Contributing

Please see [CONTRIBUTING](https://github.com/julienbornstein/oauth2-deezer/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Julien Bornstein](https://github.com/julienbornstein)

## License

The MIT License (MIT). Please see [License File](https://github.com/julienbornstein/oauth2-deezer/blob/master/LICENSE) for more information.
