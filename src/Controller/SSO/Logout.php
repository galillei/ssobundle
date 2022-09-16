<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2022
 */
declare(strict_types=1);

namespace SSO\FpBundle\Controller\SSO;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Logout extends AbstractController implements ContainerAwareInterface
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
     * @required
     */
    public function setContainer(ContainerInterface $container = null): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
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