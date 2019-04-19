<?php

namespace Functional;


use DavidMorenoCortina\JWT\Exception\UserException;
use DavidMorenoCortina\JWT\Model\UserRepository;
use DavidMorenoCortina\JWT\Validator\UserValidator;
use PDO;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase {
    /** @var PDO $conn */
    protected $conn;

    protected function setUp(): void {
        parent::setUp();

        $settings = require __DIR__ . '/../../phpunit-settings.php';

        $dsn = 'mysql:host=' . $settings['db']['host'] . ';port=' . $settings['db']['port'] . ';dbname=' . $settings['db']['dbName'];
        $this->conn = new PDO($dsn, $settings['db']['user'], $settings['db']['password'], [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    }

    public function testUserExists() {
        $userRepository = new UserRepository($this->conn);

        try {
            $userModel = $userRepository->getUserByUsername('demo');

            $userModel = $userRepository->getUserById($userModel->getId());
            $this->assertNotEmpty($userModel->getUsername());
        } catch (UserException $e) {
            $this->assertTrue(false);
        }
    }

    public function testUserNotFound() {
        $userRepository = new UserRepository($this->conn);

        try {
            $userRepository->getUserById(0);
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(true);
        }

        try {
            $userRepository->getUserByUsername('');
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(true);
        }
    }

    public function testUserInvalid() {
        $userRepository = new UserRepository($this->conn);
        try {
            $userModel = $userRepository->getUserByUsername('demo');
            $userValidator = new UserValidator($userRepository);

            $this->assertTrue($userValidator->validateActiveUser($userModel->getId()));

            $userRepository->setIsActiveUser($userModel->getId(), false);
            $this->assertFalse($userValidator->validateActiveUser($userModel->getId()));

            $userRepository->setIsActiveUser($userModel->getId(), true);
            $this->assertTrue($userValidator->validateActiveUser($userModel->getId()));
        } catch (UserException $e) {
            $this->assertTrue(false);
        }


    }
}