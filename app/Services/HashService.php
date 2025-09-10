<?php

namespace App\Services;

use Illuminate\Support\Str;

class HashService
{
    /**
     * Gera um hash seguro de 32 caracteres
     */
    public static function generateHash(int $length = 32): string
    {
        $randomString = Str::random(32);
        $hash = hash('sha256', $randomString);
        $cleanHash = preg_replace('/[^a-zA-Z0-9]/', '', $hash);
        return substr($cleanHash, 0, $length);
    }

    /**
     * Gera hash único com timestamp para evitar colisões
     */
    public static function generateUniqueHash(int $length = 32): string
    {
        $randomString = Str::random(32) . microtime(true) . rand(1000, 9999);
        $hash = hash('sha256', $randomString);
        $cleanHash = preg_replace('/[^a-zA-Z0-9]/', '', $hash);
        return substr($cleanHash, 0, $length);
    }
}