<?php
/**
 * @package Vinduesgrossisten
 * @author    artsem.belvg@gmail.com
 * @copyraight Copyright © 2015 - 2025
 */
declare(strict_types=1);

namespace SSO\FpBundle\Security;

use SSO\FpBundle\Provider\FactoryResourceOwner;
use SSO\FpBundle\Service\MergeUserData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use KnpU\OAuth2ClientBundle\Security\Exception\InvalidStateAuthenticationException;


class FactoryPortalAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var MergeUserData
     */
    private $mergeUserData;

    /**
     * @param ClientRegistry $clientRegistry
     * @param RouterInterface $router
     * @param MergeUserData $mergeUserData
     */
    public function __construct(ClientRegistry $clientRegistry,
                                RouterInterface $router,
                                MergeUserData $mergeUserData)
    {

        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->mergeUserData = $mergeUserData;
    }

    public function start(Request $request, ?AuthenticationException $authException = null) : Response
    {
        if($request->isMethod('POST') || $this->isApiCall($request)){
            return new JsonResponse(['url'=>'/connect/factoryportal/','isRedirect'=>true], Response::HTTP_UNAUTHORIZED);
        }
        return new RedirectResponse(
            '/connect/factoryportal/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'factoryportal_connect_check';
    }

    public function authenticate(Request $request) : Passport
    {
        $client = $this->clientRegistry->getClient('factory_oauth_client');
        $accessToken = $this->fetchAccessToken($client);
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /**
                 * @var FactoryResourceOwner $factoryUser
                 */
                $factoryUser = $client->fetchUserFromToken($accessToken);
                $user = $this->mergeUserData->merge($factoryUser, $accessToken);
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_homepage');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if($exception instanceof InvalidStateAuthenticationException){
//            another attempt to login
            $targetUrl = $this->router->generate('app_homepage');
            return new RedirectResponse($targetUrl);
        }
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    private function isApiCall(Request $request)
    {
        return $request->getContentTypeFormat() === 'jsonld';
    }

}