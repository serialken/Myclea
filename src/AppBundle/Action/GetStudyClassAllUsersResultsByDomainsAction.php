<?php

namespace AppBundle\Action;

use AppBundle\Repository\ResultRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetStudyClassAllUsersResultsByDomainsAction
{
    /**
     * @var ResultRepository
     */
    private $repository;

    public function __construct(ResultRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * List all students' results for all assessments of a course in a studyclass.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Route("/studyclasses/{studyClassId}/courses/{courseCid}/period/{period}/results", requirements={"studyClassId" = "\d+", "period" = "\d+"})
     * @Method({"GET"})
     *
     * @param int    $studyClassId the studyclass id
     * @param string $courseCid    the course cid
     * @param int    $period       the period number
     *
     * @return JsonResponse
     */
    public function __invoke($studyClassId, $courseCid, $period)
    {
        return new JsonResponse($this->repository->findByCourse($studyClassId, $courseCid, $period));
    }
}
