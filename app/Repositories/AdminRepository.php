<?php

namespace App\Repositories;
use Hash;
use App\Models\User;
use App\Models\CartItem;
use App\Enums\UserRolesEnum;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Admin\AdminInterface;


class AdminRepository implements AdminInterface
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function register(array $data)
    {
        $admin = $this->model::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'original_password' => $data['password'],
            'role'      => UserRolesEnum::ADMIN,
            'phone'     => $data['phone']
        ]);
        return $admin;
    }

    public function login(array $data)
    {
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['user'] = $user;
            return $success;
        }else {
            return false;
        }
    }

    public function update_profile(array $data)
    {
        $user = $this->model::find(auth()->user()->id);
        $update = $user->update([
            'name'      => $data['name'] ?? $user->name,
            'email'     => $data['email'] ?? $user->email,
            'password'  => Hash::make($data['password']) ?? $user->password,
            'original_password' => $data['password'] ?? $user->password,
            'phone'     => $data['phone'] ?? $user->phone
        ]);

        return $user;
    }
}
