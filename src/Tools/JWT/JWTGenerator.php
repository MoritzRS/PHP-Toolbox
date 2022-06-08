<?php

namespace Tools\JWT;

class JWTGenerator {

    /**
     * Payload Signing Secret
     * @var string
     */
    private string $secret;


    public function __construct(string $secret) {
        $this->secret = $secret;
    }


    /**
     * Base64URL encodes a string
     * @param string $data String to encode
     * @return string
     */
    private function base64url_encode(string $data) {
        $b64 = base64_encode($data);
        if ($b64 === false) return false;
        $url = strtr($b64, '+/', '-_');
        return rtrim($url, '=');
    }

    /**
     * Generates Payload Header
     * @return string
     */
    private function header() {
        $payload = (object)[
            "typ" => "JWT",
            "alg" => "HS256"
        ];
        return $this->base64url_encode(json_encode($payload));
    }

    /**
     * Generates Payload Body
     * @param object $payload
     * @return string
     */
    private function body($payload) {
        return $this->base64url_encode(json_encode($payload));
    }

    /**
     * Generates Payload Signature
     * @param string $content
     * @return string
     */
    private function sign(string $content) {
        $signature = hash_hmac("sha256", $content, $this->secret);
        return $this->base64url_encode($signature);
    }

    /**
     * Generates a new JSON Web Token
     * @param object $payload Token Payload
     * @return string
     */
    public function generate($payload) {
        $header = $this->header();
        $body = $this->body($payload);
        $content = "{$header}.{$body}";
        $signature = $this->sign($content);
        return "{$content}.{$signature}";
    }
}
