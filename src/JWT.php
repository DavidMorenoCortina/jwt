<?php

namespace DavidMorenoCortina\JWT;


use DavidMorenoCortina\JWT\Exception\InvalidJWTException;
use DavidMorenoCortina\JWT\Exception\OpenSSLException;
use DavidMorenoCortina\JWT\Exception\PasswordException;
use DavidMorenoCortina\JWT\Exception\RSAException;
use DavidMorenoCortina\JWT\Exception\UserException;
use DavidMorenoCortina\JWT\Model\RSARepository;
use DavidMorenoCortina\JWT\Validator\UserValidator;
use JsonException;
use PDOException;

class JWT {
    const ALG_NAME = 'RS256';
    const ALG = 'SHA256';
    /**
     * @var UserValidator
     */
    private $userValidator;

    /**
     * @var RsaRepository
     */
    private $rsaRepository;

    public function __construct(UserValidator $userValidator, RsaRepository $rsaRepository) {
        $this->userValidator = $userValidator;
        $this->rsaRepository = $rsaRepository;
    }

    /**
     * @param string $jwt
     * @param string $keyCode
     * @return int
     * @throws UserException
     * @throws PDOException
     * @throws InvalidJWTException
     * @throws RSAException
     */
    public function decode(string $jwt, string $keyCode) :int {
        $tokens = explode('.', $jwt);
        if(count($tokens) !== 3){
            throw new InvalidJWTException('Invalid parts count');
        }
        list($headB64, $payloadB64, $signB64) = $tokens;
        $head = $this->jsonDecode($this->base64Decode($headB64));
        if(empty($head) || !array_key_exists('alg', $head) || $head['alg'] !== self::ALG_NAME){
            throw new InvalidJWTException('Invalid head');
        }

        $payload = $this->jsonDecode($this->base64Decode($payloadB64));
        if(empty($payload)){
            throw new InvalidJWTException('Invalid payload');
        }

        $sign = $this->base64Decode($signB64);
        if(empty($sign)){
            throw new InvalidJWTException('Invalid sign');
        }

        $key = $this->rsaRepository->getKeyByCode($keyCode);
        if(!$this->verifySign($headB64 . '.' . $payloadB64, $sign, $key->getPublicKey())){
            throw new InvalidJWTException('Invalid signature');
        }

        $now = time();
        if(!array_key_exists('iat', $payload) || $payload['iat'] > $now){
            throw new InvalidJWTException('Invalid iat');
        }

        if(!array_key_exists('exp', $payload) || $payload['exp'] <= $now){
            throw new InvalidJWTException('Invalid exp');
        }

        if(array_key_exists('userId', $payload) && $this->userValidator->validateActiveUser($payload['userId'])){
            return $payload['userId'];
        }else{
            throw new UserException('Invalid user');
        }
    }

    /**
     * @param string $keyCode
     * @param string $username
     * @param string $password
     * @param int $exp
     * @return string
     * @throws JsonException
     * @throws OpenSSLException
     * @throws PasswordException
     * @throws RSAException
     * @throws UserException
     */
    public function encode(string $keyCode, string $username, string $password, int $exp) :string {
        $header = [
            'alg' => self::ALG_NAME,
            'typ' => 'JWT'
        ];

        $userModel = $this->userValidator->validateLogin($username, $password);
        $payload = [
            'iat' => time(),
            'exp' => $exp,
            'userId' => $userModel->getId()
        ];

        $segments = [];
        $segments[] = $this->base64Encode($this->jsonEncode($header));
        $segments[] = $this->base64Encode($this->jsonEncode($payload));

        $signingInput = implode('.' , $segments);

        $key = $this->rsaRepository->getKeyByCode($keyCode);
        $privateKey = openssl_pkey_get_private($key->getPrivateKey());

        $signature = $this->sign($signingInput, $privateKey);

        $segments[] = $this->base64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * @param array $data
     * @return string
     * @throws JsonException
     */
    protected function jsonEncode(array $data) :string {
        $encoded = json_encode($data);

        if(empty($encoded)){
            throw new JsonException();
        }

        return $encoded;
    }

    /**
     * @param string $data
     * @return string
     */
    protected function base64Encode(string $data) :string {
        $encoded = base64_encode($data);

        // URL encode
        return str_replace('=', '', strtr($encoded, '+/', '-_'));
    }

    /**
     * @param string $signingInput
     * @param resource $privateKey
     * @return string
     * @throws OpenSSLException
     */
    private function sign(string $signingInput, $privateKey) :string {
        $signature = '';
        $done = openssl_sign($signingInput, $signature, $privateKey, self::ALG);

        if($done){
            return $signature;
        }else{
            throw new OpenSSLException();
        }
    }

    /**
     * @param string $encoded
     * @return string
     */
    private function base64Decode(string $encoded) :string {
        $remainder = strlen($encoded) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $encoded .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    /**
     * @param string $encoded
     * @return array
     */
    private function jsonDecode(string $encoded) :array {
        return json_decode($encoded, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * @param string $signingInput
     * @param bool $sign
     * @param string $publicKey
     * @return bool
     */
    private function verifySign(string $signingInput, string $sign, string $publicKey) :bool {
        return openssl_verify($signingInput, $sign, $publicKey, self::ALG) === 1;
    }
}