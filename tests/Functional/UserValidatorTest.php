<?php

namespace Functional;


use DavidMorenoCortina\JWT\Exception\PasswordException;
use DavidMorenoCortina\JWT\Exception\UserException;
use DavidMorenoCortina\JWT\Model\UserRepository;
use DavidMorenoCortina\JWT\Validator\UserValidator;
use PDO;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase {
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

        $userValidator = new UserValidator($userRepository);

        try {
            $userModel = $userRepository->getUserByUsername('demo');

            $this->assertTrue($userValidator->validateActiveUser($userModel->getId()));
        } catch (UserException $e) {
            $this->assertTrue(false);
        }
    }

    public function testUserNotFound() {
        $userRepository = new UserRepository($this->conn);

        $userValidator = new UserValidator($userRepository);
        try {
            $userValidator->validateActiveUser(0);
        } catch (UserException $e) {
            $this->assertTrue(true);
        }
    }

    public function testLoginWrongPassword() {
        $userRepository = new UserRepository($this->conn);

        $userValidator = new UserValidator($userRepository);

        try {
            $userValidator->validateLogin('demo', '');
        } catch (PasswordException $e) {
            $this->assertTrue(true);
        } catch (UserException $e) {
            $this->assertTrue(false);
        }
    }

    public function testLoginUserNotFount() {
        $userRepository = new UserRepository($this->conn);

        $userValidator = new UserValidator($userRepository);

        try {
            $userValidator->validateLogin('', 'test');
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(true);
        }
    }

    public function testLogin() {
        $userRepository = new UserRepository($this->conn);

        $userValidator = new UserValidator($userRepository);

        try {
            $userModel = $userValidator->validateLogin('demo', 'test');
            $this->assertNotEmpty($userModel->getId());
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        }
    }
}