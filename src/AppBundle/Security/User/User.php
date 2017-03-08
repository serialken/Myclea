<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;
    private $username;
    private $externalId;
    private $roles;

    public function __construct($id, $username, $externalId)
    {
        $this->id         = $id;
        $this->username   = $username;
        $this->externalId = $externalId;
        $this->roles = $this->getRoles();
//        var_dump($id);
//        var_dump($username);
//        var_dump($externalId);
//        die();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
