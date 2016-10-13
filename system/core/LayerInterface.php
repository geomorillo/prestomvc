<?php
namespace system\core;
use Closure;

interface LayerInterface
{

    public function peel($object, Closure $next);
}
