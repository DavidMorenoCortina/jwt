<?php

namespace DavidMorenoCortina\JWT\Validator;


use DavidMorenoCortina\JWT\Exception\PasswordException;
use DavidMorenoCortina\JWT\Exception\UserException;
use DavidMorenoCortina\JWT\Model\UserModel;
use DavidMorenoCortina\JWT\Model\UserRepository;
use PDOException;

class UserValidator {
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $userId
     * @return bool
     * @throws UserException
     * @throws PDOException
     */
    public function validateActiveUser($userId) {
        $user = $this->userRepository->getUserById($userId);
        return $user->getIsActive();
    }

    /**
     * @param $username
     * @param $password
     * @return UserModel
     * @throws UserException
     * @throws PDOException
     * @throws PasswordException
     */
    public function validateLogin($username, $password) {
        $user = $this->userRepository->getUserByUsername($username);
        if(password_verify($password, $user->getPassword())){
            return $user;
        }else{
            throw new PasswordException();
        }
    }
}