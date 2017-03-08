<?php

namespace AppBundle\Repository;

class AssessmentRepository extends AbstractRepository
{
    public function findNotCompleted($studyClassId, $courseCid, $period, $userId)
    {
        $queryBuilder = $this->createQueryBuilder();

        $assessments = $queryBuilder
            ->select('a.cid', 'a.title')
            ->from('dim_course', 'c')
            ->innerJoin('c', 'map_studyclass_course', 'msc', 'c.cid = msc.courseCid AND msc.studyClassId = :studyClassId AND msc.associated = 1 AND msc.deleted = "9999-12-12 12:12:12"')
            ->innerJoin('c', 'map_toc_contentitem', 'mtc1', 'c.id = mtc1.courseId AND mtc1.tocDepthLevel = 1 AND mtc1.contentItemType = "unit"')
            ->innerJoin('mtc1', 'dim_unit', 'p', 'mtc1.contentItemId = p.id AND p.num = :period AND p.deleted = "9999-12-12 12:12:12"')
            ->innerJoin('mtc1', 'map_tocitem_parenttocitem', 'mtp1', 'mtc1.tocItemId = mtp1.parentTocItemId')
            ->innerJoin('mtp1', 'map_toc_contentitem', 'mtc2', 'mtp1.tocItemId = mtc2.tocItemId')
            ->innerJoin('mtc2', 'map_tocitem_parenttocitem', 'mtp2', 'mtc2.tocItemId = mtp2.parentTocItemId')
            ->innerJoin('mtp2', 'map_toc_contentitem', 'mtc3', 'mtp2.tocItemId = mtc3.tocItemId AND mtc3.contentItemType = "assessment"')
            ->innerJoin('mtc3', 'map_assessment_course', 'mac', 'mtc3.courseId = mac.courseId AND mtc3.contentItemId = mac.assessmentId')
            ->innerJoin('mac', 'dim_assessment', 'a', 'mac.assessmentId = a.id')
            ->innerJoin('a', 'map_sequence_assessment', 'msa', 'a.id = msa.assessmentId')
            ->leftJoin(
                'a',
                sprintf('(%s)', $this->getResultsQueryBuilder()->getSQL()),
                'fsas',
                'msc.studyClassId = fsas.studyClassId AND c.cid = fsas.courseCid AND a.cid = fsas.assessmentCid'
            )
            ->where('c.cid = :courseCid')
            ->andWhere('c.isLatest = 1')
            ->andWhere('c.deleted = "9999-12-12 12:12:12"')
            ->andWhere('fsas.studyClassId IS NULL')
            ->groupBy('a.cid')
            ->orderBy('a.title')
            ->setParameter('studyClassId', $studyClassId, \PDO::PARAM_INT)
            ->setParameter('period', $period, \PDO::PARAM_INT)
            ->setParameter('courseCid', $courseCid)
            ->setParameter('userId', $userId, \PDO::PARAM_INT)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $assessments;
    }

    private function getResultsQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->select('f.studyClassId, c.cid AS courseCid, f.assessmentCid')
            ->from('fct_student_assessment_session', 'f')
            ->innerJoin('f', 'dim_course', 'c', 'f.courseId = c.id AND c.cid = :courseCid')
            ->where('f.studyClassId = :studyClassId')
            ->andWhere('f.userId = :userId')
            ->groupBy('f.studyClassId, c.cid, f.assessmentCid');
    }
}
