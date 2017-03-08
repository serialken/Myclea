<?php

namespace AppBundle\Action;

use AppBundle\Repository\Exception\NoResultException;
use AppBundle\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetUserAction
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get a single user.
     *
     * @ApiDoc(
     *     resource=true,
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when the user is not found"
     *     }
     * )
     *
     * @Route("/users/{id}", requirements={"id" = "\d+"})
     * @Method({"GET"})
     *
     * @param int $id the user id
     *
     * @throws NotFoundHttpException
     *
     * @return JsonResponse
     */
    public function __invoke($id)
    {
        try {
            return new JsonResponse($this->repository->find($id));
        } catch (NoResultException $e) {
            throw new NotFoundHttpException('User does not exist', $e);
        }
    }
}
