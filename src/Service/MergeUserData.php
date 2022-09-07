<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Service\SSO;

use SSO\FpBundle\Entity\User;
use SSO\FpBundle\Provider\FactoryResourceOwner;
use SSO\FpBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Security\Core\User\UserInterface;

class MergeUserData
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager,
                                UserRepository         $userRepository)
    {

        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param AccessToken $accessToken
     * @return void
     */
    public function merge(ResourceOwnerInterface $factoryUser, AccessToken $accessToken = null) :UserInterface
    {
        $user = $this->userRepository->findUserByEmail($factoryUser->getEmail())
            ?? $this->createUser($factoryUser);
        $this->mergeUserData($user, $factoryUser);
        if($accessToken){
            $this->updateTokens($user, $accessToken);
        }
        $this->saveUser($user);
        return $user;
    }



    private function createUser(FactoryResourceOwner $factoryUser)
    {
        $user = new User();
        $user->setEmail($factoryUser->getEmail());
        return $user;
    }

    private function mergeUserData(User $user, FactoryResourceOwner $factoryUser)
    {
        $response = $factoryUser->toArray();
        $user->setLastname($response['lastname']);
        $user->setFirstname($response['firstname']);
        $user->setRoles($response['roles']);
        $user->setIsBlockedByFp($response['blockedUser']??false);
    }

    private function updateTokens(User $user, AccessToken $accessToken)
    {
        $user->setRefreshToken($accessToken->getRefreshToken());
        $user->setAccessToken($accessToken->getToken());
    }

    private function saveUser($user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}