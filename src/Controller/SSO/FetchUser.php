<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Controller\SSO;

use SSO\FpBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FetchUser extends AbstractController
{

    /**
     * @Route("/api/user", name="user_data")
     */
    public function getUserData() : Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        return $this->json(['firstname'=>$user->getFirstname(),
                            'lastname'=>$user->getLastname(),
                            'email'=> $user->getEmail(),
                            'extras'=> $user->getExtras()]);
    }
}