<?php

namespace Unit;


use DavidMorenoCortina\JWT\Config\RSAGenerator;
use PHPUnit\Framework\TestCase;

class RSAGeneratorTest extends TestCase {
    public function testGeneration() {
        $rsaGenerator = new RSAGenerator();

        $this->assertTrue($rsaGenerator->isValid());

        $this->assertNotEmpty($rsaGenerator->getPublicKey());
        $this->assertNotEmpty($rsaGenerator->getPrivateKey());
    }
}