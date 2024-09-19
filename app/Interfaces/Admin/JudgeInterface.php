<?php

namespace App\Interfaces\Admin;

interface JudgeInterface
{
    public function index($request);

    public function show(string $id);

    public function store(array $data);

    public function update(array $data, $id);

    public function destroy(string $id);
}
