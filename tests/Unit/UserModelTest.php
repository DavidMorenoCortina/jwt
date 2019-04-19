<?php

namespace Unit;


use DavidMorenoCortina\JWT\Model\UserModel;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase {
    public function testUserModel() {
        $id = 1;
        $username = 'demo';
        $password = 'password';
        $isActive = true;

        $userModel = new UserModel();
        $userModel->setId($id)
            ->setUsername($username)
            ->setPassword($password);

        $userModel->setIsActive($isActive);

        $this->assertEquals($id, $userModel->getId());
        $this->assertEquals($username, $userModel->getUsername());
        $this->assertEquals($password, $userModel->getPassword());
        $this->assertEquals($isActive, $userModel->getIsActive());


        $userModel->setIsActive('1');
        $this->assertEquals(true, $userModel->getIsActive());

        $userModel->setIsActive('0');
        $this->assertEquals(false, $userModel->getIsActive());
    }
}