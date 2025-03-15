<?php

declare(strict_types=1);

namespace rkl110\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class KanidmProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $options['pkceMethod'] = self::PKCE_METHOD_S256;
        parent::__construct($options, $collaborators);
        if (!isset($options['baseUrl']) || !preg_match('/^https?:\/\//', $options['baseUrl'])) {
            throw new \InvalidArgumentException('baseUrl must be a valid URL');
        }
        $this->baseUrl = rtrim($options['baseUrl'], '/');
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->baseUrl . '/oauth2/authorise';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseUrl . '/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->baseUrl . '/oauth2/token/introspect';
    }

    protected function getDefaultScopes()
    {
        return [
            'openid',
            'email',
            'profile',
        ];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        // @codeCoverageIgnoreStart
        if (empty($data['error'])) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $code = 0;
        $error = $data['error'];

        if (is_array($error)) {
            $code = $error['code'];
            $error = $error['message'];
        }

        throw new IdentityProviderException($error, $code, $data);
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new KanidmResourceOwner($response);
    }
}
