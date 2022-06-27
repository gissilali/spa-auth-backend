<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->client = Client::where('name', config('app.name') . ' Password Grant Client')->first();
    }


    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        return $this->issueToken($request, 'password');
    }

    public function issueToken(Request $request, $grantType, $scope = "")
    {
        $params = [
            'grant_type' => $grantType,
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'scope' => $scope,
            'username' => $request->username ?: $request->email
        ];


        $request->request->add($params);

        $proxy = Request::create('api/oauth/token', 'POST');

        return Route::dispatch($proxy);
    }

}
