<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\API\Authentication\LoginService;
use App\Services\API\Authentication\RefreshSessionService;

class LoginController extends Controller
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return app(LoginService::class, ['request' => $request])->login();
    }

    public function refreshSession(Request $request)
    {
        return app(RefreshSessionService::class, ['request' => $request])->refreshSession();
    }
}
