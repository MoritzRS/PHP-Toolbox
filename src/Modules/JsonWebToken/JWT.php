<?php

namespace Modules\JsonWebToken;

class JWT {
    private string $secret;
    private array $data = [];
    private bool $valid = true;

    /**
     * Creates a new JWT Object
     * @param string $secret Secret for signature
     * @param string $token Token (empty if newly created)
     */
    public function __construct(string $secret, string $token = "") {
        $this->secret = $secret;
        if (!$token) return;
        $this->valid = $this->validate($token);
        $this->set($this->extractPayload($token));
    }

    /**
     * Generates Token
     * @return string
     */
    public function getToken() {
        $header = $this->base64url_encode(json_encode(["typ" => "JWT", "alg" => "HS256"]));
        $body = $this->base64url_encode(json_encode((object)$this->data));
        $content = "{$header}.{$body}";
        $signature = $this->base64url_encode(hash_hmac("sha256", $content, $this->secret));
        return "{$content}.{$signature}";
    }

    /**
     * Validates token
     * @return bool
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * Sets data on the token
     * @param array $data Data as associative array
     */
    public function set(array $data) {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Gets key value from token payload
     * @param string $key Key to get from payload
     * @return mixed
     */
    public function get(string $key) {
        return $this->data[$key] ?? null;
    }

    /**
     * Extracts payload from token
     * @param string $token Token to extract from
     * @return array
     */
    private function extractPayload(string $token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) return [];
        $payload = json_decode($this->base64url_decode($parts[1]), true);
        return $payload ?? [];
    }

    /**
     * Validates token
     * @param string $token Token to validate
     * @return bool
     */
    private function validate(string $token) {
        $parts = explode(".", $token);
        if (count($parts) !== 3) return false;

        $head = json_decode($this->base64url_decode($parts[0]));
        if (!isset($head->typ) || $head->typ !== "JWT") return false;
        if (!isset($head->alg) || $head->alg !== "HS256") return false;

        $body = json_decode($this->base64url_decode($parts[1]));
        if (!$body) return false;

        $signature = $this->base64url_decode($parts[2]);
        $verified = hash_hmac("sha256", "{$parts[0]}.{$parts[1]}", $this->secret);

        if ($signature !== $verified) return false;

        return true;
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
     * Base64URL decodes a string
     * @param string $data String to decode
     * @return string
     */
    private function base64url_decode($data, $strict = false) {
        $b64 = strtr($data, '-_', '+/');
        return base64_decode($b64, $strict);
    }
}
