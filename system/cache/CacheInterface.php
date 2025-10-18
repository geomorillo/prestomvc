<?php

namespace system\cache;

/**
 * Cache Interface - Define métodos estándar para sistemas de cache
 */
interface CacheInterface
{
    /**
     * Obtener un valor del cache
     * @param string $key
     * @return mixed|false False si no existe o expiró
     */
    public function get($key);

    /**
     * Guardar un valor en cache
     * @param string $key
     * @param mixed $data
     * @param int $ttl Time to live en segundos
     * @return bool
     */
    public function set($key, $data, $ttl = 0);

    /**
     * Eliminar un valor del cache
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * Limpiar todo el cache
     * @return bool
     */
    public function clear();

    /**
     * Verificar si una clave existe en cache
     * @param string $key
     * @return bool
     */
    public function has($key);
}