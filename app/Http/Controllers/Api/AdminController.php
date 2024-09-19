<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AdminRepository;
use App\Helpers\{ExceptionHandlerHelper, NotificationHelper};
use App\Http\Requests\Api\Admin\{RegisterRequest, LoginRequest};

class AdminController extends Controller
{
    private $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function register(RegisterRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use ($request) {
            $user = $this->adminRepository->register($request->validated());
            return $this->sendResponse($user, 'User Register Successfully');
        });
    }

    public function login(LoginRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use ($request) {
            $user = $this->adminRepository->login($request->validated());
            if($user)
            {
                return $this->sendResponse($user, 'User login Successfully');
            }else
            {
                return $this->sendError('Invalid credentials', [], 401);
            }

        });
    }

    public function update_profile(RegisterRequest $request)
    {
        return ExceptionHandlerHelper::tryCatch(function () use ($request) {
            $user = $this->adminRepository->update_profile($request->validated());
            return $this->sendResponse($user, 'User Updated Successfully');
        });
    }


    public function logout()
{
    $user = Auth::guard('api')->user();
    if ($user) {
        $user->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    } else {
        return response()->json(['message' => 'User not authenticated'], 401);
    }
}


}
