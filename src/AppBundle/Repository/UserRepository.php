<?php

namespace AppBundle\Repository;

use AppBundle\Repository\Exception\NoResultException;

class UserRepository extends AbstractRepository
{
    public function find($id)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select('id', 'firstName', 'lastName')
            ->from('dim_user')
            ->where('id = '.$queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            ->andWhere('deleted = "9999-12-12 12:12:12"');

        $results = $this->executeCacheQuery($queryBuilder);

        if (count($results) === 0) {
            throw new NoResultException();
        }

        return $results[0];
    }

    public function findStudentsByStudyClass($id)
    {
        $queryBuilder = $this->createQueryBuilder();

        return $queryBuilder
            ->select('u.id', 'u.firstName', 'u.lastName')
            ->from('dim_user', 'u')
            ->innerJoin('u', 'map_student_study_class', 'mssc', 'u.id = mssc.userId')
            ->where('mssc.studyClassId = '.$queryBuilder->createNamedParameter($id, \PDO::PARAM_INT))
            ->andWhere('mssc.relationEnd > NOW()')
            ->andWhere('u.deleted = "9999-12-12 12:12:12"')
            ->orderBy('u.lastName')
            ->addOrderBy('u.firstName')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOneByExternalId($externalId)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder
            ->select('id', 'userName', 'externalId')
            ->from('dim_user')
            ->where('externalId = '.$queryBuilder->createNamedParameter($externalId))
            ->andWhere('deleted = "9999-12-12 12:12:12"')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $results = $this->executeCacheQuery($queryBuilder);

        if (count($results) === 0) {
            throw new NoResultException();
        }

        return $results[0];
    }
}
