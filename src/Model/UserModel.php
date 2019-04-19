<?php

namespace DavidMorenoCortina\JWT\Model;


class UserModel {
    private $id;

    private $username;

    private $password;

    private $isActive;

    /**
     * @return int
     */
    public function getId() {
        return (int)$this->id;
    }

    /**
     * @param int $id
     * @return UserModel
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     * @return UserModel
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserModel
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive() {
        return (bool)(int)$this->isActive;
    }

    /**
     * @param bool $isActive
     * @return UserModel
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
        return $this;
    }
}