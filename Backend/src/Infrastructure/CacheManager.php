<?php

declare(strict_types=1);

namespace ComprobadorEquivalencias\Infrastructure;

class CacheManager
{
    private string $cacheDir;

    /**
     *
     * @param  string $cacheDir
     */
    public function __construct(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function guardarToken(string $key, string $value): void
    {
        $filename = $this->cacheDir . '/' . $key;
        file_put_contents($filename, $value);
    }

    /**
     *
     * @param  string $key
     * @param  float $expiry
     * @return bool
     */
    public function esTokenValido(string $key, float $expiry = 10): bool
    {
        $filename = $this->cacheDir . '/' . $key;
        if (!file_exists($filename)) {
            return false;
        }
        $filemtime = filemtime($filename);
        if (time() - $filemtime <= ($expiry * 60)) {
            return true;
        }
        unlink($filename);
        return false;
    }
}
