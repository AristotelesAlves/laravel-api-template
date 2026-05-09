<?php

namespace App\Http\Requests;

use App\DTO\Auth\LoginInputDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Summary of failedValidation
     * @param Validator $validator
     * @throws HttpResponseException
     * @return never
     */
    protected function failedValidation(validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Erro de validação.',
            'errors' => $validator->errors(),
        ], 422));
    }
    /**
     * Summary of rules
     * @return array{device_name: string[], email: string[], password: string[]}
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    /**
     * Summary of toDTO
     * @return LoginInputDTO
     */
    public function toDTO(): LoginInputDTO
    {
        return new LoginInputDTO(
            email: (string) $this->validated('email'),
            password: (string) $this->validated('password'),
            deviceName: (string) ($this->validated('device_name') ?? 'api-token'),
        );
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.exists' => 'O e-mail informado não está cadastrado.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'A senha deve ser um texto válido.',
            'device_name.string' => 'O nome do dispositivo deve ser um texto válido.',
            'device_name.max' => 'O nome do dispositivo não pode exceder 100 caracteres.',
        ];
    }
}
