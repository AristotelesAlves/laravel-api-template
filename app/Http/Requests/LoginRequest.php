<?php

namespace App\Http\Requests;

use App\DTO\Auth\LoginInputDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function toDTO(): LoginInputDTO
    {
        return new LoginInputDTO(
            email: (string) $this->validated('email'),
            password: (string) $this->validated('password'),
            deviceName: (string) ($this->validated('device_name') ?? 'api-token'),
        );
    }
}
