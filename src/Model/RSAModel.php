<?php

namespace DavidMorenoCortina\JWT\Model;


class RSAModel {
    private $name;

    private $privateKey;

    private $publicKey;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RSAModel
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrivateKey() {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return RSAModel
     */
    public function setPrivateKey($privateKey) {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey() {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return RSAModel
     */
    public function setPublicKey($publicKey) {
        $this->publicKey = $publicKey;
        return $this;
    }
}