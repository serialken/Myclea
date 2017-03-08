<?php

namespace AppBundle\Action;

use AppBundle\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetStudyClassStudentsAction
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
     * List all students of a studyclass.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Route("/studyclasses/{id}/students", requirements={"id" = "\d+"})
     * @Method({"GET"})
     *
     * @param int $id the studyclass id
     *
     * @return JsonResponse
     */
    public function __invoke($id)
    {
        return new JsonResponse($this->repository->findStudentsByStudyClass($id));
    }
}
