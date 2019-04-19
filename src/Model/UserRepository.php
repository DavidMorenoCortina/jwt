<?php

namespace DavidMorenoCortina\JWT\Model;


use DavidMorenoCortina\JWT\Exception\UserException;
use PDO;
use PDOException;
use PDOStatement;

class UserRepository extends BaseRepository {
    /**
     * @param $userId
     * @return UserModel
     * @throws UserException
     * @throws PDOException
     */
    public function getUserById($userId) {
        $stmt = $this->connection->prepare('SELECT id, username, password, is_active as isActive FROM user WHERE id = ? LIMIT 1;');
        return $this->getUser($stmt, [$userId]);
    }

    /**
     * @param PDOStatement $stmt
     * @param array $data
     * @return UserModel
     * @throws UserException
     * @throws PDOException
     */
    protected function getUser(PDOStatement $stmt, array $data) {
        $stmt->execute($data);
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        /** @var UserModel $user */
        $user = $stmt->fetch();

        if($user instanceof UserModel){
            return $user;
        }else{
            throw new UserException();
        }
    }

    /**
     * @param $username
     * @return UserModel
     * @throws UserException
     * @throws PDOException
     */
    public function getUserByUsername($username) {
        $stmt = $this->connection->prepare('SELECT id, username, password, is_active as isActive FROM user WHERE username = ? LIMIT 1;');
        return $this->getUser($stmt, [$username]);
    }

    public function setIsActiveUser(int $userId, bool $status) {
        $stmt = $this->connection->prepare('UPDATE user SET is_active = ? WHERE id = ? LIMIT 1;');
        $stmt->execute([
            (int)$status,
            $userId
        ]);
        $stmt->closeCursor();
    }
}