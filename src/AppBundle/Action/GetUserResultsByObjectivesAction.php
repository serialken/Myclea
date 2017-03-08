<?php

namespace AppBundle\Action;

use AppBundle\Repository\AssessmentRepository;
use AppBundle\Repository\ResultRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserResultsByObjectivesAction
{
    private $resultRepository;
    private $assessmentRepository;

    public function __construct(ResultRepository $resultRepository, AssessmentRepository $assessmentRepository)
    {
        $this->resultRepository     = $resultRepository;
        $this->assessmentRepository = $assessmentRepository;
    }

    /**
     * List student's results by objectives of a course in a studyclass.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Route("/users/{userId}/studyclasses/{studyClassId}/courses/{courseCid}/period/{period}/results", requirements={"userId" = "\d+", "studyClassId" = "\d+", "period" = "\d+"})
     * @Method({"GET"})
     *
     * @param int    $userId       the user id
     * @param int    $studyClassId the studyclass id
     * @param string $courseCid    the course cid
     * @param int    $period       the period number
     *
     * @return JsonResponse
     */
    public function __invoke($userId, $studyClassId, $courseCid, $period)
    {
        $results = $this->resultRepository->findObjectiveResults($userId, $studyClassId, $courseCid, $period);

        $results['assessments_not_completed'] = $this->assessmentRepository->findNotCompleted($studyClassId, $courseCid, $period, $userId);

        return new JsonResponse($results);
    }
}
