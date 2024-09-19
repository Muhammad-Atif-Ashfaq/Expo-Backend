<?php

namespace App\Interfaces\Admin;

interface AdminInterface
{
    public function register(array $data);

    public function login(array $data);

    public function update_profile(array $data);
}
