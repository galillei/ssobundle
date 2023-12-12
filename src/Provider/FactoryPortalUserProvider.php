<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Provider;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use SSO\FpBundle\Entity\User;
use SSO\FpBundle\Repository\UserRepository;
use SSO\FpBundle\Service\MergeUserData;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FactoryPortalUserProvider implements UserProviderInterface
{
    private UserRepository $userRepository;
    private ClientRegistry $clientRegistry;
    private MergeUserData $mergeUserData;

    /**
     * @param UserRepository $userRepository
     * @param ClientRegistry $clientRegistry
     * @param MergeUserData $mergeUserData
     */
    public function __construct(UserRepository $userRepository,
                                ClientRegistry $clientRegistry,
                                MergeUserData $mergeUserData
    )
    {
        $this->userRepository = $userRepository;
        $this->clientRegistry = $clientRegistry;
        $this->mergeUserData = $mergeUserData;
    }

    /**
     * @param User $user
     * @return UserInterface 
     */
    public function refreshUser(UserInterface $user)
    {
        $expiresIn = $user->getExpiresIn(); 
        if($expiresIn && $expiresIn > (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))){
            return $user;
        }
        $client = $this->clientRegistry->getClient('factory_oauth_client');

        /**
         * @var FactoryOauth2ClientProvider $provider
         */
        $provider = $client->getOAuth2Provider();
        try{
            $userDetails = $provider->fetchUserData($user->getAccessToken());
            $refreshedUser = $this->mergeUserData->merge($userDetails);
        }catch (\Exception $clientException){
            try{
                $accessToken = $client->refreshAccessToken($user->getRefreshToken());
                $refreshedUser = $this->mergeUserData->merge($client->fetchUser(), $accessToken);
            }catch (\Exception $exception){
                $user->setToLogoutUser(true);
                throw new AuthenticationException('User not set');
            }
        }
        return $refreshedUser;
    }

    public function supportsClass(string $class)
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->userRepository->findUserByEmail($identifier);
    }
}