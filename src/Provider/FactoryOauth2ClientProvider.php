<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Provider;

use SSO\FpBundle\Exception\FactoryOauth2Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class FactoryOauth2ClientProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public $domain = 'https://factoryportal.skanva.dk';

    public $apiDomain = 'https://api.factoryportal.skanva.dk';


    public function getBaseAuthorizationUrl()
    {
        return $this->domain . '/authorize';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain .  '/api/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getUserDetails();
    }

    public function getUserDetails()
    {
        return $this->domain . '/api/v3/user';
    }

    public function fetchUserData( string $token )
    {
        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $this->getUserDetails(), $token);
        $response = $this->getResponse($request);
        $preparedData = $this->parseResponse($response);
        return new FactoryResourceOwner($preparedData);
    }

    protected function getDefaultScopes()
    {
        return  ['price-robot'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if($response->getStatusCode() >= 400) {
            return new FactoryOauth2Exception();
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new FactoryResourceOwner($response);
        $user->setDomain($this->domain);
        return $user;
    }
}