<?php

namespace Tools\Test;

use Exception;

class Assert {

    public static function equals($a, $b) {
        if ($a != $b) throw new Exception("[ERROR] IS NOT EQUAL");
    }

    public static function strictEquals($a, $b) {
        if ($a !== $b) throw new Exception("[ERROR] IS NOT STRICT EQUAL");
    }

    public static function notEquals($a, $b) {
        if ($a == $b) throw new Exception("[ERROR] IS EQUAL");
    }

    public static function strictNotEquals($a, $b) {
        if ($a === $b) throw new Exception("[ERROR] IS STRICT EQUAL");
    }

    public static function true($a) {
        if (!$a) throw new Exception("[ERROR] IS NOT TRUE");
    }

    public static function false($a) {
        if ($a) throw new Exception("[ERROR] IS NOT FALSE");
    }

    public static function null($a) {
        if ($a !== null) throw new Exception("[ERROR] IS NOT NULL");
    }

    public static function notNull($a) {
        if ($a === null) throw new Exception("[ERROR] IS NULL");
    }

    public static function fail($a) {
        try {
            $a();
        } catch (Exception $e) {
            return;
        }
        throw new Exception("[ERROR] DID NOT FAIL");
    }
}
