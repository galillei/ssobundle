<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2025
 */
declare(strict_types=1);

namespace SSO\FpBundle\Controller;

use SSO\FpBundle\Provider\FactoryOauth2ClientProvider;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;


class FactoryPortalConnect extends AbstractController
{
     #[Route("/connect/factoryportal/", name:"factoryportal_connect_start")]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
                ->getClient('factory_oauth_client')
                ->redirect(['price-robot']);
    }

    #[Route("/connect/factoryportal/check", name: "factoryportal_connect_check")]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        $client = $clientRegistry->getClient('factory_oauth_client');
        try {
            // the exact class depends on which provider you're using
            /** @var FactoryOauth2ClientProvider $factoryUser */
            $factoryUser = $client->fetchUser();
            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            dd($e->getMessage()); die;
        }
    }
}