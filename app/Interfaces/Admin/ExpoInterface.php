<?php

namespace App\Interfaces\Admin;

interface ExpoInterface
{
    public function index();

    public function show(string $id);

    public function store(array $data);

    public function update(array $data, $id);

    public function destroy(string $id);
    public function getAllEvents();
}
