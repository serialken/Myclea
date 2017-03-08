<?php

namespace AppBundle\Action;

use AppBundle\Security\User\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class GetJwtTokenAction
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var JWTManager
     */
    private $jwtManager;

    private $request;

    public function __construct(TokenStorageInterface $tokenStorage, JWTManager $jwtManager, Request $request)
    {
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager   = $jwtManager;
        $this->request = $request;
//        var_dump($request);
//        die();
    }

    /**
     * Get a JWT token for authenticated user.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     },
     *     filters={
     *         {"name"="ticket", "dataType"="string"}
     *     }
     * )
     *
     * @Route("/token")
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        $user = $this->tokenStorage->getToken()->getUser();
//        var_dump($user);
        if (!$user instanceof User) {
            throw new \RuntimeException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        $myToken = $this->jwtManager->create($user);
//        $session = new Session();
//        $this->request->setSession($session);
//        getSession()->set('JwtRegistered', $myToken);
//        var_dump($this->request->getSession());
//        die();
        return new JsonResponse([
            'token'  => $myToken,
            'userId' => $user->getId(),
        ]);
    }
}
