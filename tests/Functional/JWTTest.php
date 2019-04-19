<?php

namespace Functional;


use DavidMorenoCortina\JWT\Exception\InvalidJWTException;
use DavidMorenoCortina\JWT\Exception\OpenSSLException;
use DavidMorenoCortina\JWT\Exception\PasswordException;
use DavidMorenoCortina\JWT\Exception\RSAException;
use DavidMorenoCortina\JWT\Exception\UserException;
use DavidMorenoCortina\JWT\JWT;
use DavidMorenoCortina\JWT\Model\RSARepository;
use DavidMorenoCortina\JWT\Model\UserRepository;
use DavidMorenoCortina\JWT\Validator\UserValidator;
use Exception;
use JsonException;
use PDO;
use PHPUnit\Framework\TestCase;

class JWTTest extends TestCase {
    /** @var PDO $conn */
    protected $conn;

    protected function setUp(): void {
        parent::setUp();

        $settings = require __DIR__ . '/../../phpunit-settings.php';

        $dsn = 'mysql:host=' . $settings['db']['host'] . ';port=' . $settings['db']['port'] . ';dbname=' . $settings['db']['dbName'];
        $this->conn = new PDO($dsn, $settings['db']['user'], $settings['db']['password'], [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    }

    public function testTokenGenerate() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenValidation() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
            try {
                sleep(1);
                $userId = $jwt->decode($token, $rsaName);
                $this->assertGreaterThan(0, $userId);
            } catch (InvalidJWTException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (RSAException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (UserException $e) {
                $this->assertTrue(false, $e->getMessage());
            }
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenGenerateWrongPassword() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = '';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(true);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenInvalidUser() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = '';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(true);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenInvalidRSAName() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = '';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(true);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenValidationInvalidRSAName() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
            try {
                $rsaName = '';
                sleep(1);
                $jwt->decode($token, $rsaName);
            } catch (InvalidJWTException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (RSAException $e) {
                $this->assertTrue(true, $e->getMessage());
            } catch (UserException $e) {
                $this->assertTrue(false, $e->getMessage());
            }
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    public function testTokenValidationInvalidToken() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
            try {
                sleep(1);
                $jwt->decode($token . 'a', $rsaName);
            } catch (InvalidJWTException $e) {
                $this->assertTrue(true, $e->getMessage());
            } catch (RSAException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (UserException $e) {
                $this->assertTrue(false, $e->getMessage());
            }
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }

    protected function tearDown(): void {
        parent::tearDown();
        $userRepository = new UserRepository($this->conn);
        //So this is not a test and must be run or break execution exception is not handled
        $userModel = $userRepository->getUserByUsername('demo');
        $userRepository->setIsActiveUser($userModel->getId(), true);
    }

    public function testTokenValidationInvalidUser() {

        $userRepository = new UserRepository($this->conn);
        $userValidator = new UserValidator($userRepository);
        $rsaRepository = new RSARepository($this->conn);

        $jwt = new JWT($userValidator, $rsaRepository);

        $rsaName = 'tests';
        $username = 'demo';
        $password = 'test';
        $exp = time() + 800;

        try {
            $token = $jwt->encode($rsaName, $username, $password, $exp);
            $this->assertNotEmpty($token);
            sleep(1);
            try{
                $userId = $jwt->decode($token, $rsaName);
                $this->assertGreaterThan(0, $userId);
                $userRepository->setIsActiveUser($userId, false);
            }catch (Exception $e){
                $this->assertTrue(false);
            }

            try {
                $jwt->decode($token, $rsaName);
            } catch (InvalidJWTException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (RSAException $e) {
                $this->assertTrue(false, $e->getMessage());
            } catch (UserException $e) {
                $this->assertTrue(true, $e->getMessage());
            }
        } catch (OpenSSLException $e) {
            $this->assertTrue(false);
        } catch (PasswordException $e) {
            $this->assertTrue(false);
        } catch (RSAException $e) {
            $this->assertTrue(false);
        } catch (UserException $e) {
            $this->assertTrue(false);
        } catch (JsonException $e) {
            $this->assertTrue(false);
        }
    }
}