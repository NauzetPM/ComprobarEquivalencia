<?php

namespace ComprobadorEquivalencias\Infrastructure;

class CacheManager {
    private $cacheDir;

    public function __construct($cacheDir) {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function guardarToken($key, $value) {
        $filename = $this->getCacheFileName($key);
        file_put_contents($filename, $value);
    }

    public function comprobarToken($key, $expiry = 180) {
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


    private function getCacheFileName($key) {
        return $this->cacheDir . '/' . md5($key);
    }
}
