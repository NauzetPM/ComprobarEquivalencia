<?php

namespace ComprobadorEquivalencias\Infrastructure;

class CacheManager
{
    private string $cacheDir;

    /**
     * __construct
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
     * guardarToken
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function guardarToken(string $key,string $value): void
    {
        $filename = $this->cacheDir . '/' . $key;
        file_put_contents($filename, $value);
    }

    /**
     * esTokenValido
     *
     * @param  string $key
     * @param  int $expiry
     * @return bool
     */
    public function esTokenValido(string $key, int $expiry = 180): bool
    {
        $filename = $this->cacheDir . '/' . $key;
        if (file_exists($filename)) {
            $filemtime = filemtime($filename);
            if (time() - $filemtime <= $expiry) {
                return true;
            } else {
                unlink($filename);
            }
        }
        return false;
    }

}
