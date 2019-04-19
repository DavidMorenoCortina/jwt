<?php

namespace DavidMorenoCortina\JWT\Model;


use DavidMorenoCortina\JWT\Exception\RSAException;
use PDO;

class RSARepository extends BaseRepository {
    /**
     * @param $code
     * @return RSAModel
     * @throws RSAException
     */
    public function getKeyByCode($code) {
        $stmt = $this->connection->prepare('SELECT name, private_key as privateKey, public_key as publicKey FROM rsa_key WHERE name = ? LIMIT 1;');
        $stmt->execute([$code]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, RSAModel::class);
        /** @var RSAModel $key */
        $key = $stmt->fetch();

        if($key instanceof RSAModel){
            return $key;
        }else{
            throw new RSAException();
        }
    }
}