<?php

namespace system\cache;

/**
 * Cache Manager - Factory para crear instancias de cache
 */
class CacheManager
{
    /**
     * Crear instancia de cache según tipo
     * @param string $type Tipo de cache (file, array)
     * @param array $config Configuración específica
     * @return CacheInterface
     */
    public static function create($type = 'file', $config = [])
    {
        switch (strtolower($type)) {
            case 'file':
                $cacheDir = $config['dir'] ?? null;
                $ttl = $config['ttl'] ?? 3600;
                return new FileCache($cacheDir, $ttl);

            case 'array':
                return new ArrayCache();

            default:
                throw new \Exception("Unsupported cache type: {$type}");
        }
    }

    /**
     * Crear cache basado en configuración del sistema
     * @return CacheInterface
     */
    public static function createFromConfig()
    {
        $type = defined('CACHE_TYPE') ? CACHE_TYPE : 'file';
        $config = [];

        if ($type === 'file') {
            $config['dir'] = defined('CACHE_DIR') ? CACHE_DIR : null;
            $config['ttl'] = defined('CACHE_TTL') ? CACHE_TTL : 3600;
        }

        return self::create($type, $config);
    }
}