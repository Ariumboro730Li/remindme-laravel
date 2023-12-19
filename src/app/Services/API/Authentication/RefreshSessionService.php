<?php

namespace App\Services\API\Authentication;

use Illuminate\Support\Facades\Http;
use ErrorException;

class RefreshSessionService extends LoginService {

    public function refreshSession(){
        try {
            $refreshToken = $this->request->refresh_token;
            $client = $this->getActiveClient();
            $response = Http::asForm()->post(config('app.host_docker').'/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'scope' => '*',
            ]);

            if ($response->failed()) {
                return $this->returnInvalidToken();
            } else {
                return $this->returnValidToken($response);
            }
        } catch (\Throwable $th) {
            throw new ErrorException($th->getMessage());
        }
    }

    private function returnInvalidToken(){
        return response()->json([
            'ok' => false,
            'err' => 'ERR_INVALID_REFRESH_TOKEN',
            'msg' => 'invalid refresh token'
        ], 401);
    }

    private function returnValidToken($response){
        return response()->json([
            'ok' => true,
            'data' => [
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
            ],
        ], 200);
    }

}
