<?php

namespace Functional;


use DavidMorenoCortina\JWT\Exception\RSAException;
use DavidMorenoCortina\JWT\Model\RSARepository;
use PDO;
use PHPUnit\Framework\TestCase;

class RSARepositoryTest extends TestCase {
    /** @var PDO $conn */
    protected $conn;

    protected function setUp(): void {
        parent::setUp();

        $settings = require __DIR__ . '/../../phpunit-settings.php';

        $dsn = 'mysql:host=' . $settings['db']['host'] . ';port=' . $settings['db']['port'] . ';dbname=' . $settings['db']['dbName'];
        $this->conn = new PDO($dsn, $settings['db']['user'], $settings['db']['password'], [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    }

    public function testKeyExists() {
        $rsaRepository = new RSARepository($this->conn);

        try {
            $rsaModel = $rsaRepository->getKeyByCode('tests');
            $this->assertNotEmpty($rsaModel->getPrivateKey());
        } catch (RSAException $e) {
            $this->assertTrue(false);
        }
    }

    public function testKeyNotFound() {
        $rsaRepository = new RSARepository($this->conn);

        try {
            $rsaRepository->getKeyByCode('no-existe');
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(true);
        }
    }
}