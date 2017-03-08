<?php

namespace AppBundle\Profile;

class Profile
{
    private $profile;
    private $profileAberrant;
    private $profileSuspect;
    private $notation;
    private $recommendedActivities;

    /**
     * @return int
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param int $profile
     *
     * @return Profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileAberrant()
    {
        return $this->profileAberrant;
    }

    /**
     * @param int $profileAberrant
     *
     * @return Profile
     */
    public function setProfileAberrant($profileAberrant)
    {
        $this->profileAberrant = $profileAberrant;

        return $this;
    }

    /**
     * @return int
     */
    public function getProfileSuspect()
    {
        return $this->profileSuspect;
    }

    /**
     * @param int $profileSuspect
     *
     * @return Profile
     */
    public function setProfileSuspect($profileSuspect)
    {
        $this->profileSuspect = $profileSuspect;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotation()
    {
        return $this->notation;
    }

    /**
     * @param string $notation
     *
     * @return Profile
     */
    public function setNotation($notation)
    {
        $this->notation = $notation;

        return $this;
    }

    /**
     * @return array
     */
    public function getRecommendedActivities()
    {
        return $this->recommendedActivities;
    }

    /**
     * @param array $recommendedActivities
     *
     * @return Profile
     */
    public function setRecommendedActivities(array $recommendedActivities)
    {
        $this->recommendedActivities = $recommendedActivities;

        return $this;
    }
}
