<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Controller\SSO;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Logout extends AbstractController
{
    /**
     * @var string
     */
    private $factoryPortalUrl;

    /**
     * @param string $factoryPortalUrl
     */
    public function __construct(string $factoryPortalUrl)
    {
        $this->factoryPortalUrl = $factoryPortalUrl;
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function index()
    {
        
    }

    /**
     * @Route("/fp_logout", name="factory_portal_logout", methods={"GET"})
     */
    public function logoutFromFactoryPortal()
    {
       $url = $this->generateUrl('app_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
       return new RedirectResponse($this->factoryPortalUrl.'?redirect='.$url);
    }
}