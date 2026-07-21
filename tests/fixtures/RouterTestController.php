<?php

namespace app\controllers;

class RouterTestController
{
    public $called = false;
    public $lastAction = '';

    public function index()
    {
        $this->called = true;
        $this->lastAction = 'index';
    }

    public function store()
    {
        $this->called = true;
        $this->lastAction = 'store';
    }

    public function update()
    {
        $this->called = true;
        $this->lastAction = 'update';
    }

    public function destroy()
    {
        $this->called = true;
        $this->lastAction = 'destroy';
    }
}
