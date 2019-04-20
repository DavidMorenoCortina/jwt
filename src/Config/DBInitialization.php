<?php

namespace DavidMorenoCortina\JWT\Config;


use DavidMorenoCortina\JWT\Exception\DBException;
use PDO;

class DBInitialization {
    public function createRSATable(PDO $conn) :void {
        $stmt = $conn->prepare('
            CREATE TABLE IF NOT EXISTS `rsa_key` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `private_key` TEXT NOT NULL,
                `public_key` TEXT NOT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE=\'utf8_general_ci\'
            ENGINE=InnoDB
            ;
        ');

        $stmt->execute();
        $stmt->closeCursor();
    }

    public function removeRSATable(PDO $conn) :void {
        $stmt = $conn->prepare('DROP TABLE `rsa_key`;');
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * @param PDO $conn
     * @param string $name
     * @return bool
     * @throws DBException
     */
    public function createRsaKey(PDO $conn, string $name) :bool {
        $stmt = $conn->prepare('SELECT id FROM rsa_key WHERE name = ?');
        if($stmt === false){ // Emulated prepared queries OFF
            throw new DBException();
        }
        $done = $stmt->execute([$name]);
        if($done === false){ // Emulated prepared queries ON
            throw new DBException();
        }

        $rsaKey = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $rsaGenerator = new RSAGenerator();

        if (empty($rsaKey)) {
            $stmt = $conn->prepare('INSERT INTO rsa_key (name, private_key, public_key) VALUES (?, ?, ?)');
            $stmt->execute([
                $name,
                $rsaGenerator->getPrivateKey(),
                $rsaGenerator->getPublicKey()
            ]);

            return true;
        }

        return false;
    }

    public function createUserTable(PDO $conn) {
        $stmt = $conn->prepare('
            CREATE TABLE IF NOT EXISTS `user` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(255) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `is_active` INT(1) NOT NULL,
                PRIMARY KEY (`id`)
            )
            COLLATE=\'utf8_general_ci\'
            ENGINE=InnoDB
            ;
        ');

        $stmt->execute();
        $stmt->closeCursor();
    }

    public function removeUserTable(PDO $conn) :void {
        $stmt = $conn->prepare('DROP TABLE `user`;');
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * @param PDO $conn
     * @param string $username
     * @param string $password
     * @param bool $isActive
     * @return bool
     * @throws DBException
     */
    public function createUser(PDO $conn, string $username, string $password, bool $isActive) :bool {
        $stmt = $conn->prepare('SELECT id FROM user WHERE username = ?');
        if($stmt === false){ // Emulated prepared queries OFF
            throw new DBException();
        }
        $done = $stmt->execute([$username]);
        if($done === false){ // Emulated prepared queries ON
            throw new DBException();
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (empty($user)) {
            $stmt = $conn->prepare('INSERT INTO user (username, password, is_active) VALUES (?, ?, ?)');
            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_BCRYPT),
                (int)$isActive
            ]);

            return true;
        }

        return false;
    }
}