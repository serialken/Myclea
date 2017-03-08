<?php

namespace AppBundle\Security;

use AppBundle\Cas\ServerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;

class CasAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @var HttpUtils
     */
    private $httpUtils;

    public function __construct(ServerInterface $server, HttpUtils $httpUtils)
    {
        $this->server    = $server;
        $this->httpUtils = $httpUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
            return new RedirectResponse(\phpCAS::getServerLoginURL());
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        \phpCAS::client($this->server->getVersion(), $this->server->getHost(), $this->server->getPort(), $this->server->getContext());
        \phpCAS::setNoCasServerValidation();

        if (!\phpCAS::isAuthenticated()) {
            return;
        }

        return \phpCAS::getAttribute('codeUtil');
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationCredentialsNotFoundException('', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            ['message' => $exception->getMessageKey()],
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //$token has all the information about the user
        $session = new Session();
        $session->set('registeredId', $token->getUser()->getId());
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
