<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidatePasswordRequest;
use App\Services\Passwords\StorePassword;
use App\Services\Passwords\ValidatePassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordsController extends Controller
{
    /**
     * Store password
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate(['user_id' => 'required|string|max:10']);

        return response()->json((new StorePassword($request->input('user_id')))->call());
    }

    /**
     * Validate password
     *
     * @param ValidatePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function validatePassword(ValidatePasswordRequest $request)
    {
        return response()->json((new ValidatePassword($request->input()))->call());
    }
}
