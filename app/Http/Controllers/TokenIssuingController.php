<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class TokenIssuingController extends AccessTokenController
{
    public function issueToken(ServerRequestInterface $request)
    {
        $requestData = $request->getParsedBody();
        try {
            $username = $requestData['username'] ?? $requestData['email'];
            $user = User::where('email', '=', $username)->first();

            $tokenResponse = parent::issueToken($request);

            $content = $tokenResponse->getContent();

            $data = json_decode($content, true);
            if(isset($data["error"])) {
                throw new Exception('random error');
            }



            return response()->json([
                'success' => true,
                'user' => $user,
                'tokens' => $data
            ]);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                "success" => false,
                "message" => "user not found"
            ], 500);
        }
        catch (OAuthServerException $e) {
            return response()->json([
                "success" => false,
                "message" => "invalid credentials (password incorrect)"
            ], 422);
        }
        catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "internal server error : - ".$e->getMessage()
            ], 500);
        }
    }

}
