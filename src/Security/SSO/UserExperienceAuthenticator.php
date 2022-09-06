<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Security\SSO;

use App\Provider\FactoryOauth2ClientProvider;
use App\Service\SSO\MergeUserData;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserExperienceAuthenticator extends  AbstractAuthenticator
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var FactoryOauth2ClientProvider
     */
    private $clientProvider;
    /**
     * @var FactoryOauth2ClientProvider
     */
    private $provider;
    /**
     * @var MergeUserData
     */
    private $mergeUserData;

    /**
     * @param Security $security
     * @param ClientRegistry $clientRegistry
     * @param FactoryOauth2ClientProvider $provider
     * @param MergeUserData $mergeUserData
     */
    public function __construct(Security                    $security,
                                ClientRegistry              $clientRegistry,
                                FactoryOauth2ClientProvider $provider,
                                MergeUserData $mergeUserData
                                )
    {
        $this->security = $security;
        $this->clientRegistry = $clientRegistry;
        $this->provider = $provider;
        $this->mergeUserData = $mergeUserData;
    }

    public function supports(Request $request): ?bool
    {
        $route = $request->attributes->get('_route');
        return in_array($route, ['factoryportal_connect_check', 'logout']) === false;
    }

    public function authenticate(Request $request)
    {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->security->getUser();
        if(($user instanceof UserInterface) === false){
             throw new AuthenticationException('User not set');
        }
        /**
         * @var OAuth2Client $client
         */
//        $requestFp = $this->clientProvider->getAuthenticatedRequest('GET',
//            $this->clientProvider->getUserDetails(), $user->getAccessToken());
        $client = $this->clientRegistry->getClient('factory_oauth_client');

        /**
         * @var FactoryOauth2ClientProvider $provider
         */
        $provider = $client->getOAuth2Provider();
        try{
             $userDetails = $provider->fetchUserData($user->getAccessToken());
             $user = $this->mergeUserData->merge($userDetails);
        }catch (\Exception $clientException){
            try{
                $accessToken = $client->refreshAccessToken($user->getRefreshToken());
                $user = $this->mergeUserData->merge($client->fetchUser(), $accessToken);
            }catch (\Exception $exception){
                $user->setToLogoutUser(true);
            }
        }
        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), function () use ($user) {
                return $user;
            })
        );

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}