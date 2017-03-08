<?php

namespace AppBundle\Action;

use AppBundle\Repository\ResultRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetStudyClassAllUsersResultsByDomainsForAnAssessmentAction
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
     * List all students' results for an assessment of a course in a studyclass.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Route("/studyclasses/{studyClassId}/courses/{courseCid}/period/{period}/assessments/{assessmentCid}/results", requirements={"studyClassId" = "\d+"})
     * @Method({"GET"})
     *
     * @param int    $studyClassId  the studyclass id
     * @param string $courseCid     the course cid
     * @param int    $period        the period number
     * @param string $assessmentCid the assessment cid
     *
     * @return JsonResponse
     */
    public function __invoke($studyClassId, $courseCid, $period, $assessmentCid)
    {
        return new JsonResponse($this->repository->findByAssessment($studyClassId, $courseCid, $period, $assessmentCid));
    }
}
