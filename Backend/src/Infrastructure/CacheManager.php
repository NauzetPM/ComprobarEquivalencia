<?php

namespace ComprobadorEquivalencias\Infrastructure;

class CacheManager
{
    private $cacheDir;

    /**
     * __construct
     *
     * @param  string $cacheDir
     * @return void
     */
    public function __construct($cacheDir)
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
    public function guardarToken($key, $value): void
    {
        $filename = $this->getCacheFileName($key);
        file_put_contents($filename, $value);
    }

    /**
     * esTokenValido
     *
     * @param  string $key
     * @param  int $expiry
     * @return bool
     */
    public function esTokenValido($key, $expiry = 180): bool
    {
        $filename = $this->getCacheFileName($key);
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


    /**
     * getCacheFileName
     *
     * @param  string $key
     * @return string
     */
    private function getCacheFileName($key): string
    {
        return $this->cacheDir . '/' . md5($key);
    }
}
