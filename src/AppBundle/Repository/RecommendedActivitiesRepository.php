<?php

namespace AppBundle\Repository;

use AppBundle\Repository\Exception\NoResultException;

class RecommendedActivitiesRepository
{
    private $recommendedActivities;

    public function __construct(array $recommendedActivities)
    {
        $this->recommendedActivities = $recommendedActivities;
    }

    public function findByNotation($notation)
    {
        if (!isset($this->recommendedActivities[$notation])) {
            throw new NoResultException();
        }

        return $this->recommendedActivities[$notation];
    }
}
