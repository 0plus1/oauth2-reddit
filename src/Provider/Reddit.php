<?php namespace Rudolf\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use Rudolf\OAuth2\Client\Entity\RedditUser;
use Rudolf\OAuth2\Client\Provider\Exception\RedditIdentityProviderException;

class Reddit extends AbstractProvider
{

    /**
     * Api domain
     *
     * @var string
     */
    public $apiDomain = 'https://ssl.reddit.com';

    /**
     * Api domain for token based requests
     * @var string
     */
    public $apiDomainWithToken = 'https://oauth.reddit.com';

    /**
     * @var array
     */
    public $scopes = [ 'identity' ];

    /**
     * User agent string required by Reddit
     * Format <platform>:<app ID>:<version string> (by /u/<reddit username>)
     *
     * @see https://github.com/reddit/reddit/wiki/API
     */
    public $userAgent = "";

    /**
     * @var string
     */
    public $duration = 'temporary';

    /**
     * {@inheritDoc}
     */
    public $authorizationHeader = "bearer";

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->apiDomain.'/api/v1/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->apiDomain.'/api/v1/access_token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiDomainWithToken.'/api/v1/me.json';
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return $this->scopes;
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400)
        {
            throw RedditIdentityProviderException::clientException($response, $data);
        }
        elseif (isset($data['error']))
        {
            throw RedditIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return TwitchUser
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new RedditUser((array)$response);
    }

    /**
     * Builds the authorization URL.
     *
     * @param  array $options
     * @return string Authorization URL
     */
    public function getAuthorizationUrl(array $options = [])
    {
        $options['duration'] = $this->duration;

        return parent::getAuthorizationUrl($options);
    }

    /**
     * Returns all headers used by this provider for a request.
     *
     * The request will be authenticated if an access token is provided.
     *
     * @param  mixed|null $token object or string
     * @return array
     */
    public function getHeaders($token = null)
    {
        if ($token) {
            return array_merge(
                $this->getDefaultHeaders(),
                $this->getAuthorizationHeaders($token)
            );
        }
        else
        {
            return array_merge(
                $this->getDefaultHeaders(),
                ['Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret)]
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultHeaders()
    {
        return [ 'User-Agent' => $this->userAgent ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return ['Authorization' => 'bearer '.$token];
    }
}
