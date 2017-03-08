<?php

namespace AppBundle\Repository;

class ResultRepository extends AbstractRepository
{
    public function findByCourse($studyClassId, $courseCid, $period)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select([
                'u.id',
                'u.firstName',
                'u.lastName',
                'st.domainName AS domain',
                'SUM(fsas.automatedScore) AS score',
                'SUM(st.totalScore) AS total',
                'ROUND((SUM(fsas.automatedScore) / SUM(st.totalScore)) * 100) AS percentage',
            ])
            ->from(sprintf('(%s)', $this->getStudentsQuery()->getSQL()), 'u')
            ->innerJoin('u', sprintf('(%s)', $this->getTasksLatestCourseQuery()->getSQL()), 'st', 'u.studyClassId = st.studyClassId')
            ->leftJoin(
                'st',
                sprintf('(%s)', $this->getLastSubmittedResultsQuery()->getSQL()),
                'fsas',
                $queryBuilder->expr()->andX(
                    'u.id = fsas.userId',
                    'st.taskCid = fsas.taskCid'
                )
            )
            ->groupBy('u.id', 'st.domainId')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
            ->addOrderBy('st.domainId')
            ->setParameter('studyClassId', $studyClassId, \PDO::PARAM_INT)
            ->setParameter('courseCid', $courseCid)
            ->setParameter('period', $period, \PDO::PARAM_INT);

        return $this->aggregateByDomain($queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function findByAssessment($studyClassId, $courseCid, $period, $assessmentCid)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select([
                'u.id',
                'u.firstName',
                'u.lastName',
                'st.domainName AS domain',
                'SUM(fsas.automatedScore) AS score',
                'SUM(st.totalScore) AS total',
                'ROUND((SUM(fsas.automatedScore) / SUM(st.totalScore)) * 100) AS percentage',
            ])
            ->from(sprintf('(%s)', $this->getStudentsQuery()->getSQL()), 'u')
            ->innerJoin('u', sprintf('(%s)', $this->getTasksLatestCourseQuery()->getSQL()), 'st', 'u.studyClassId = st.studyClassId')
            ->leftJoin(
                'st',
                sprintf('(%s)', $this->getLastSubmittedResultsForAnAssessmentQuery()->getSQL()),
                'fsas',
                $queryBuilder->expr()->andX(
                    'u.id = fsas.userId',
                    'st.taskCid = fsas.taskCid'
                )
            )
            ->groupBy('u.id', 'st.domainId')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
            ->addOrderBy('st.domainId')
            ->setParameter('studyClassId', $studyClassId, \PDO::PARAM_INT)
            ->setParameter('courseCid', $courseCid)
            ->setParameter('period', $period, \PDO::PARAM_INT)
            ->setParameter('assessmentCid', $assessmentCid);

        return $this->aggregateByDomain($queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function findResultsByUserAndSubDomain($userId, $studyClassId, $courseCid)
    {
    }

    public function findObjectiveResults($userId, $studyClassId, $courseCid, $period)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select([
                'st.domainName AS domain',
                'st.objectiveName AS objective',
                'SUM(fsas.automatedScore) AS score',
                'SUM(st.totalScore) AS total',
                'ROUND((SUM(fsas.automatedScore) / SUM(st.totalScore)) * 100) AS percentage',
            ])
            ->from(sprintf('(%s)', $this->getStudentsQuery()->andWhere('u.id = :userId')->getSQL()), 'u')
            ->innerJoin('u', sprintf('(%s)', $this->getTasksLatestCourseQuery()->getSQL()), 'st', 'u.studyClassId = st.studyClassId')
            ->leftJoin(
                'st',
                sprintf('(%s)', $this->getLastSubmittedResultsQuery()->andWhere('o.userId = :userId')->getSQL()),
                'fsas',
                $queryBuilder->expr()->andX(
                    'u.id = fsas.userId',
                    'st.taskCid = fsas.taskCid'
                )
            )
            ->groupBy('st.objectiveId')
            ->orderBy('st.objectiveId')
            ->setParameter('userId', $userId, \PDO::PARAM_INT)
            ->setParameter('studyClassId', $studyClassId, \PDO::PARAM_INT)
            ->setParameter('courseCid', $courseCid)
            ->setParameter('period', $period, \PDO::PARAM_INT);

        return $this->aggregateByObjective($queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getMDAndPSVars($userId, $studyClassId, $courseCid, $period)
    {
        $queryBuilder = $this->createQueryBuilder();

        $standardsQuery = $this->getTasksLatestCourseQuery()
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        'o.pedagogicalId = "adldom1obj2"',
                        't.num BETWEEN 6 AND 15'
                    ),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->andX(
                            'o.pedagogicalId = "adldom2obj1"',
                            't.num BETWEEN 11 AND 15'
                        ),
                        $queryBuilder->expr()->andX(
                            'o.pedagogicalId = "adldom2obj2"',
                            $queryBuilder->expr()->orX(
                                't.num BETWEEN 1 AND 5',
                                't.num BETWEEN 11 AND 15'
                            )
                        )
                    )
                )
            );

        return $queryBuilder
            ->select([
                'IF(st.domainId = "adldom1", "MD", "PS")',
                'SUM(fsas.automatedScore) AS score',
                'SUM(st.totalScore) AS total',
                'ROUND((SUM(fsas.automatedScore) / SUM(st.totalScore)) * 100) AS percentage',
            ])
            ->from(sprintf('(%s)', $this->getStudentsQuery()->andWhere('u.id = :userId')->getSQL()), 'u')
            ->innerJoin('u', sprintf('(%s)', $standardsQuery->getSQL()), 'st', 'u.studyClassId = st.studyClassId')
            ->leftJoin(
                'st',
                sprintf('(%s)', $this->getLastSubmittedResultsQuery()->getSQL()),
                'fsas',
                $queryBuilder->expr()->andX(
                    'u.id = fsas.userId',
                    'st.taskCid = fsas.taskCid'
                )
            )
            ->groupBy('st.domainId')
            ->orderBy('st.objectiveId')
            ->setParameter('userId', $userId, \PDO::PARAM_INT)
            ->setParameter('studyClassId', $studyClassId, \PDO::PARAM_INT)
            ->setParameter('courseCid', $courseCid)
            ->setParameter('period', $period, \PDO::PARAM_INT)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
    }

    private function getStudentsQuery()
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select([
                'mssc.studyClassId',
                'u.id',
                'u.firstName',
                'u.lastName',
            ])
            ->from('dim_user', 'u')
            ->innerJoin('u', 'map_student_study_class', 'mssc', $queryBuilder->expr()->andX(
                'u.id = mssc.userId',
                'mssc.studyClassId = :studyClassId',
                'mssc.relationEnd > NOW()'
            ));
    }

    private function getTasksLatestCourseQuery()
    {
        $queryBuilder = $this->createQueryBuilder();
        $exprBuilder  = $queryBuilder->expr();

        $queryBuilder
            ->select([
                't.cid AS taskCid',
                't.totalScore',
                'o.pedagogicalId AS objectiveId',
                'o.name AS objectiveName',
                'd.pedagogicalId AS domainId',
                'd.name AS domainName',
                'msc.studyClassId',
                'c.cid AS courseCid',
                'a.cid AS assessmentCid',
            ])
            ->from('dim_assessment_task', 't')
            ->innerJoin('t', 'map_assessment_task_standard', 'mats', 't.id = mats.taskId')
            ->innerJoin(
                'mats',
                'dim_standard',
                'o',
                $exprBuilder->andX(
                    'mats.standardId = o.id',
                    'o.depth = 2',
                    'o.pedagogicalId REGEXP "^adldom[0-9]+obj[0-9]+$"'
                )
            )
            ->innerJoin(
                'o',
                'dim_standard',
                'd',
                $exprBuilder->andX(
                    'o.parentInternalId = d.internalId',
                    'o.packageId = d.packageId',
                    'd.depth = 1'
                )
            )
            ->innerJoin(
                't',
                'dim_assessment_sequence',
                'seq',
                $exprBuilder->andX(
                    't.sequenceId = seq.id',
                    'seq.deleted = "9999-12-12 12:12:12"'
                )
            )
            ->innerJoin('seq', 'map_sequence_assessment', 'msa', 'seq.id = msa.sequenceId')
            ->innerJoin('msa', 'dim_assessment', 'a', 'msa.assessmentId = a.id')
            ->innerJoin('a', 'map_assessment_course', 'mac', 'a.id = mac.assessmentId')
            ->innerJoin('mac', 'map_toc_contentitem', 'mtc3', $exprBuilder->andX(
                'mac.courseId = mtc3.courseId',
                'mac.assessmentId = mtc3.contentItemId',
                'mtc3.tocDepthLevel = 3',
                'mtc3.contentItemType = "assessment"'
            ))
            ->innerJoin('mtc3', 'map_tocitem_parenttocitem', 'mtp3', 'mtc3.tocItemId = mtp3.tocItemId')
            ->innerJoin('mtp3', 'map_tocitem_parenttocitem', 'mtp2', 'mtp3.parentTocItemId = mtp2.tocItemId')
            ->innerJoin('mtp2', 'map_toc_contentitem', 'mtc1', $exprBuilder->andX(
                'mtp2.parentTocItemId = mtc1.tocItemId',
                'mtc1.contentItemType = "unit"'
            ))
            ->innerJoin('mtc1', 'dim_unit', 'p', 'mtc1.contentItemId = p.id AND p.num = :period AND p.deleted = "9999-12-12 12:12:12"')
            ->innerJoin(
                'mtc1',
                'dim_course',
                'c',
                $exprBuilder->andX(
                    'mtc1.courseId = c.id',
                    'c.cid = :courseCid',
                    'c.isLatest = 1',
                    'c.deleted = "9999-12-12 12:12:12"'
                )
            )
            ->innerJoin(
                'c',
                'map_studyclass_course',
                'msc',
                $exprBuilder->andX(
                    'c.cid = msc.courseCid',
                    'msc.studyClassId = :studyClassId',
                    'msc.associated = 1',
                    'msc.deleted = "9999-12-12 12:12:12"'
                )
            );

        return $queryBuilder;
    }

    private function getLastSubmittedResultsQuery()
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select([
                't.cid AS taskCid',
                'o.automatedScore',
                'o.userId',
            ])
            ->from('fct_student_assessment_session', 'o')
            ->innerJoin('o', 'dim_assessment_task', 't', 'o.taskId = t.id')
            ->innerJoin('o', 'dim_course', 'c', 'o.courseId = c.id AND c.cid = :courseCid')
            ->leftJoin(
                'o',
                'fct_student_assessment_session',
                'b',
                $queryBuilder->expr()->andX(
                    'o.studyClassId = b.studyClassId',
                    'o.userId = b.userId',
                    'o.courseId = b.courseId',
                    'o.assessmentId = b.assessmentId',
                    'o.assessmentCid = b.assessmentCid',
                    'o.sequenceId = b.sequenceId',
                    'o.taskId = b.taskId',
                    'b.status = 2 OR b.status IS NULL',
                    $queryBuilder->expr()->lt('o.submittedTime', 'b.submittedTime')
                )
            )
            ->where('o.studyClassId = :studyClassId')
            ->andWhere('o.status = 2')
            ->andWhere($queryBuilder->expr()->isNull('b.submittedTime'));
    }

    private function getLastSubmittedResultsForAnAssessmentQuery()
    {
        $queryBuilder = $this->getLastSubmittedResultsQuery();

        return $queryBuilder->andWhere('o.assessmentCid = :assessmentCid');
    }

    private function aggregateByDomain(array $results)
    {
        $aggregated = [];

        foreach ($results as $result) {
            if (!isset($aggregated[$result['id']])) {
                $aggregated[$result['id']] = [
                    'id'        => $result['id'],
                    'firstName' => $result['firstName'],
                    'lastName'  => $result['lastName'],
                    'results'   => [],
                ];
            }

            $domain = $this->getShortDomainName($result['domain']);

            $aggregated[$result['id']]['results'][$domain] = [
                'domain'     => $domain,
                'score'      => $result['score'] === null ? null : (int) $result['score'],
                'total'      => (int) $result['total'],
                'percentage' => $result['percentage'] === null ? null : (int) $result['percentage'],
            ];
        }

        return array_values($aggregated);
    }

    private function aggregateByObjective(array $results)
    {
        $aggregated = [];

        foreach ($results as $result) {
            $domain = $this->getShortDomainName($result['domain']);

            if (!isset($aggregated[$domain])) {
                $aggregated[$domain] = [
                    'domain'  => $domain,
                    'results' => [],
                ];
            }

            $aggregated[$domain]['results'][] = [
                'objective'  => $result['objective'],
                'score'      => $result['score'] === null ? null : (int) $result['score'],
                'total'      => (int) $result['total'],
                'percentage' => $result['percentage'] === null ? null : (int) $result['percentage'],
            ];
        }

        return $aggregated;
    }

    private function getShortDomainName($domain)
    {
        preg_match('#mots|phrases|histoires|documents#', $domain, $domain);

        return $domain[0];
    }
}
