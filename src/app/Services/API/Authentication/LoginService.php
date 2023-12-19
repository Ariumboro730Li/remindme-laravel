<?php

namespace App\Services\API\Authentication;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;

class LoginService {

    protected Request $request;
    protected Http $http;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->http = new Http();
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(){
        if($this->checkCredentials()){
            try {
                $client = $this->getActiveClient();
                $httpData = [
                    'grant_type' => 'password',
                    'client_id' => $client->id,
                    'client_secret' => $client->secret,
                    'username' => $this->request->email,
                    'password' => $this->request->password,
                    'scope' => '*',
                ];
                $response = Http::asForm()->post(config('app.host_docker').'/oauth/token', $httpData);
                $user = Auth::user();
                // dd($client, $user, $httpData, $response);
                return $this->returnValidAuth($user, $response);
            } catch (\Throwable $th) {
                throw new ErrorException($th->getMessage());
            }
        } else {
            return $this->returnInvalidCredens();
        }
    }

    /**
     * Get active client
     *
     * @return Client
     */
    public function getActiveClient(){
        $client = Client::where('password_client', 1)->first();
        if (!$client) {
            $allClient = Client::count();
            if (!$allClient) {
                throw new ErrorException("you might forgot to run passport:install");
            }
        }

        return $client;
    }

    /**
     * Check credentials
     *
     * @return bool
     */
    public function checkCredentials(){
        return Auth::attempt(['email' => $this->request->email, 'password' => $this->request->password]);
    }

    /**
     * return if authentication valid
     *
     * @param [type] $user
     * @param [type] $response
     * @return \Illuminate\Http\JsonResponse
     */
    private function returnValidAuth($user, $response){
        return response()->json([
            'ok' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
            ],
        ], 200);
    }

    /**
     * return if authentication invalid
     * @return \Illuminate\Http\JsonResponse
     */

    private function returnInvalidCredens(){
        return response()->json([
            'ok' => false,
            'err' => 'ERR_INVALID_CREDS',
            'msg' => 'incorrect username or password'
        ], 401);
    }
}


