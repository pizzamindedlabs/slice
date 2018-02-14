<?php

namespace Slice\Security\Auth;


/**
 * Object Oriented representation of user array
 * @package Slice\Security\Auth
 * @author pizzaminded <miki@appvende.net>
 * @license MIT
 */
class User implements \ArrayAccess
{

    /**
     * @var array
     */
    protected $userStorage;


    /**
     * User constructor.
     * @param array $userData array with user data, stored in session
     */
    public function __construct($userData)
    {
        if($userData === null) {
            $userData = [];
        }
        $this->userStorage = $userData;
    }

    public function hasRole($roleName)
    {
        if(isset($this->userStorage['roles'])) {
            return false;
        }

        return \in_array($roleName, $this->userStorage['roles'], true);
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->userStorage[$offset]);
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->userStorage[$offset];
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->userStorage[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->userStorage[$offset]);
    }
}