<?php

namespace App\Http\Resources;

use App\DTO\Auth\LoginOutputDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var LoginOutputDTO $login */
        $login = $this->resource;

        return [
            'token' => $login->token,
            'token_type' => $login->tokenType,
            'user' => (new UserResource($login->user))->resolve($request),
        ];
    }
}
