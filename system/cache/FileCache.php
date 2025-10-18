<?php

namespace system\cache;

/**
 * File Cache - Implementación de cache usando archivos
 * Almacena datos serializados en el sistema de archivos
 */
class FileCache implements CacheInterface
{
    private $cacheDir;
    private $defaultTtl;

    public function __construct($cacheDir = null, $defaultTtl = 3600)
    {
        $this->cacheDir = $cacheDir ?: ROOT . 'cache' . DS;
        $this->defaultTtl = $defaultTtl;

        // Crear directorio si no existe
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        // Crear archivo .gitkeep para que git mantenga el directorio
        $gitkeep = $this->cacheDir . '.gitkeep';
        if (!file_exists($gitkeep)) {
            file_put_contents($gitkeep, '');
        }
    }

    public function get($key)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return false;
        }

        $data = unserialize(file_get_contents($file));

        // Verificar si expiró
        if ($data['expires'] < time()) {
            $this->delete($key); // Limpiar cache expirado
            return false;
        }

        return $data['value'];
    }

    public function set($key, $data, $ttl = null)
    {
        $ttl = $ttl ?: $this->defaultTtl;
        $file = $this->getFilePath($key);

        $cacheData = [
            'value' => $data,
            'expires' => time() + $ttl,
            'created' => time()
        ];

        return file_put_contents($file, serialize($cacheData)) !== false;
    }

    public function delete($key)
    {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    public function clear()
    {
        $files = glob($this->cacheDir . '*.cache');
        $success = true;

        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }

    public function has($key)
    {
        $file = $this->getFilePath($key);
        return file_exists($file) && $this->get($key) !== false;
    }

    /**
     * Obtener ruta completa del archivo cache
     */
    private function getFilePath($key)
    {
        return $this->cacheDir . md5($key) . '.cache';
    }

    /**
     * Limpiar cache expirado (mantenimiento)
     */
    public function cleanExpired()
    {
        $files = glob($this->cacheDir . '*.cache');
        $cleaned = 0;

        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            if ($data['expires'] < time()) {
                unlink($file);
                $cleaned++;
            }
        }

        return $cleaned;
    }

    /**
     * Obtener información del cache
     */
    public function getStats()
    {
        $files = glob($this->cacheDir . '*.cache');
        $stats = [
            'total_files' => count($files),
            'total_size' => 0,
            'valid_entries' => 0,
            'expired_entries' => 0
        ];

        foreach ($files as $file) {
            $stats['total_size'] += filesize($file);

            $data = unserialize(file_get_contents($file));
            if ($data['expires'] > time()) {
                $stats['valid_entries']++;
            } else {
                $stats['expired_entries']++;
            }
        }

        return $stats;
    }
}