<?php

namespace Unit;


use DavidMorenoCortina\JWT\Config\DBInitialization;
use DavidMorenoCortina\JWT\Exception\DBException;
use PDO;
use PHPUnit\Framework\TestCase;

class DBInitializationTest extends TestCase {
    /** @var PDO $conn */
    protected $conn;

    protected function setUp(): void {
        parent::setUp();

        $settings = require __DIR__ . '/../../phpunit-settings.php';

        $dsn = 'mysql:host=' . $settings['db']['host'] . ';port=' . $settings['db']['port'] . ';dbname=' . $settings['db']['dbName'];
        $this->conn = new PDO($dsn, $settings['db']['user'], $settings['db']['password'], [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
    }

    public function testRsa() {
        $stmt = $this->conn->prepare('DROP TABLE rsa_key');
        $stmt->execute();
        $stmt->closeCursor();

        $dbInitialization = new DBInitialization();
        try {
            $dbInitialization->createRsaKey($this->conn, 'tests');
            $this->assertTrue(false);
        }catch (DBException $e){
            $this->assertTrue(true);
        }

        $dbInitialization->createRSATable($this->conn);

        try {
            $this->assertTrue($dbInitialization->createRsaKey($this->conn, 'tests'));

            $this->assertFalse($dbInitialization->createRsaKey($this->conn, 'tests'));
        }catch (DBException $e){
            $this->assertTrue(false);
        }
    }

    public function testUser() {
        $username = 'test-user';
        $password = 'test';
        $isActive = true;

        $dbInitialization = new DBInitialization();
        $dbInitialization->createUserTable($this->conn);
        try {
            $stmt = $this->conn->prepare('DELETE FROM user WHERE username = ?');
            $stmt->execute([$username]);

            $this->assertTrue($dbInitialization->createUser($this->conn, $username, $password, $isActive));
            $this->assertTrue(true);
        } catch (DBException $e) {
            $this->assertTrue(false);
        }

        try {
            $this->assertFalse($dbInitialization->createUser($this->conn, $username, $password, $isActive));
        } catch (DBException $e) {
            $this->assertTrue(false);
        }
    }
}