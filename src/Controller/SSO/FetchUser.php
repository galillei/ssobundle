<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Controller\SSO;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SSO\FpBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FetchUser extends AbstractController
{

    /**
     * @return void
     * @Route("/api/user", name="user_data")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PRICEROBOT_USER')")
     */
    public function getUserData() : Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        return $this->json(['firstname'=>$user->getFirstname(),
                            'lastname'=>$user->getLastname(),
                            'email'=> $user->getEmail()]);
    }
}