<?php

namespace AppBundle\Repository;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function createQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    protected function executeCacheQuery(QueryBuilder $queryBuilder, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $stmt = $this->connection->executeCacheQuery(
            $queryBuilder->getSQL(),
            $queryBuilder->getParameters(),
            $queryBuilder->getParameterTypes(),
            new QueryCacheProfile()
        );

        $results = $stmt->fetchAll($fetchMode);

        $stmt->closeCursor();

        return $results;
    }
}
