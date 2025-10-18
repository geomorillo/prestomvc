<?php

namespace system\cache;

/**
 * Array Cache - Cache en memoria usando arrays (útil para testing)
 * Los datos se pierden al terminar el request
 */
class ArrayCache implements CacheInterface
{
    private $storage = [];

    public function get($key)
    {
        if (!isset($this->storage[$key])) {
            return false;
        }

        $data = $this->storage[$key];

        // Verificar expiración
        if ($data['expires'] < time()) {
            unset($this->storage[$key]);
            return false;
        }

        return $data['value'];
    }

    public function set($key, $data, $ttl = 0)
    {
        $this->storage[$key] = [
            'value' => $data,
            'expires' => time() + $ttl
        ];

        return true;
    }

    public function delete($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return true;
        }

        return false;
    }

    public function clear()
    {
        $this->storage = [];
        return true;
    }

    public function has($key)
    {
        return isset($this->storage[$key]) && $this->get($key) !== false;
    }

    /**
     * Obtener todas las claves en cache
     */
    public function getKeys()
    {
        return array_keys($this->storage);
    }

    /**
     * Obtener estadísticas del cache
     */
    public function getStats()
    {
        $valid = 0;
        $expired = 0;

        foreach ($this->storage as $data) {
            if ($data['expires'] > time()) {
                $valid++;
            } else {
                $expired++;
            }
        }

        return [
            'total_entries' => count($this->storage),
            'valid_entries' => $valid,
            'expired_entries' => $expired
        ];
    }
}