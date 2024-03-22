<?php

declare(strict_types=1);

namespace ComprobadorEquivalenciasTest\Infrastructure;

use PHPUnit\Framework\TestCase;
use ComprobadorEquivalencias\Infrastructure\CacheManager;

class CacheManagerTest extends TestCase
{
    private string $cacheDir;
    private CacheManager $cacheManager;

    /**
     * @test
     * @return void
     */
    protected function setUp(): void
    {
        $this->cacheDir = __DIR__ . '/test_cache';
        $this->cacheManager = new CacheManager($this->cacheDir);
    }

    /**
     * @test
     * @return void
     */
    protected function tearDown(): void
    {
        $this->deleteCacheDir();
    }

    /**
     * @test
     * @return void
     */
    public function testGuardarToken(): void
    {
        $key = 'keyGuardarToken';
        $value = 'keyGuardarToken';

        $this->cacheManager->guardarToken($key, $value);

        $filename = $this->cacheDir . '/' . $key;
        $this->assertFileExists($filename);

        $this->assertSame($value, file_get_contents($filename));
    }

    /**
     * @test
     * @return void
     */
    public function testEsTokenValido(): void
    {
        $key = 'keyTokenValido';
        $value = 'keyTokenValido';

        $this->cacheManager->guardarToken($key, $value);

        $this->assertTrue($this->cacheManager->esTokenValido($key, 0.0166));
        sleep(1);
        $this->assertFalse($this->cacheManager->esTokenValido($key, 0.0166));
    }

    /**
     * @test
     * @return void
     */
    private function deleteCacheDir(): void
    {
        if (is_dir($this->cacheDir)) {
            $files = glob($this->cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->cacheDir);
        }
    }
}
