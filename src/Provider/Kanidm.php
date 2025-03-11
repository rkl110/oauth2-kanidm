<?php

namespace rkl110\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Kanidm extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $issuer = '';
    protected $kanidmBaseUrl = '';

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->kanidmBaseUrl = $this->getProtocolAndDomain($this->issuer);
    }

    private function getProtocolAndDomain($url)
    {
        $parsedUrl = parse_url($url);
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    }

    public function getBaseAuthorizationUrl()
    {
        return $this->kanidmBaseUrl . '/ui/oauth2';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->kanidmBaseUrl . '/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->issuer . '/userinfo';
    }

    protected function getDefaultScopes()
    {
        return [
            'openid',
            'email',
            'profile'
        ];
    }
    protected function getScopeSeparator()
    {
        return ' ';
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
