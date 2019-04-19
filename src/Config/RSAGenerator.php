<?php

namespace DavidMorenoCortina\JWT\Config;


class RSAGenerator {
    /** @var string $publicKey */
    protected $publicKey;

    /** @var string $privateKey */
    protected $privateKey;

    public function __construct() {
        $privateKeyResource = openssl_pkey_new(array('private_key_bits' => 2048));
        $details = openssl_pkey_get_details($privateKeyResource);
        $this->publicKey = $details['key'];

        $this->privateKey = '';
        openssl_pkey_export($privateKeyResource, $this->privateKey);
    }

    public function getPublicKey() :string {
        return $this->publicKey;
    }

    public function getPrivateKey() :string {
        return $this->privateKey;
    }

    public function isValid() :bool {
        return !empty($this->privateKey) && !empty($this->publicKey);
    }
}