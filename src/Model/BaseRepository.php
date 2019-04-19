<?php

namespace DavidMorenoCortina\JWT\Model;


use PDO;

abstract class BaseRepository {
    /** @var PDO $connection */
    protected $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }
}