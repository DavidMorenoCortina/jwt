<?php

namespace Unit;


use DavidMorenoCortina\JWT\Model\RSAModel;
use PHPUnit\Framework\TestCase;

class RSAModelTest extends TestCase {
    public function testUserModel() {
        $name = 'test';
        $publicKey = 'demo';
        $privateKey = 'password';

        $rsaModel = new RSAModel();
        $rsaModel->setName($name)
            ->setPublicKey($publicKey);
        $rsaModel->setPrivateKey($privateKey);

        $this->assertEquals($name, $rsaModel->getName());
        $this->assertEquals($publicKey, $rsaModel->getPublicKey());
        $this->assertEquals($privateKey, $rsaModel->getPrivateKey());
    }
}