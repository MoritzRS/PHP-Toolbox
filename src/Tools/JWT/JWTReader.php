<?php

namespace Tools\JWT;

class JWTReader {

    /**
     * Payload Signing Secret
     * @var string
     */
    private string $secret;

    /**
     * Encoded JWT
     */
    private string $jwt;


    public function __construct(string $jwt, string $secret) {
        $this->jwt = $jwt;
        $this->secret = $secret;
    }

    private function base64url_decode($data, $strict = false) {
        $b64 = strtr($data, '-_', '+/');
        return base64_decode($b64, $strict);
    }

    public function validate() {
        $parts = explode(".", $this->jwt);
        if (count($parts) != 3) return false;

        $head = json_decode($this->base64url_decode($parts[0]));
        if (!isset($head->typ) || $head->typ != "JWT") return false;
        if (!isset($head->alg) || $head->alg != "HS256") return false;

        $body = json_decode($this->base64url_decode($parts[1]));
        if (!$body) return false;

        $signature = $this->base64url_decode($parts[2]);
        $verified = hash_hmac("sha256", "{$parts[0]}.{$parts[1]}", $this->secret);

        if ($signature !== $verified) return false;

        return true;
    }

    public function payload() {
        if (!$this->validate()) return false;
        [$head, $body, $signature] = explode(".", $this->jwt);
        return json_decode($this->base64url_decode($body));
    }
}
