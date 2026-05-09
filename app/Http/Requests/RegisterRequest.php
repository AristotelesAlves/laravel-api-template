<?php

namespace App\Http\Requests;

use App\DTO\Auth\RegisterInputDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
     * @return array{confirmPassword: string[], email: string[], name: string[], password: string[]}
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'includes_letter', 'includes_number', 'includes_special_character'],
            'confirmPassword' => ['required', 'string', 'same:password'],
        ];
    }

    /**
     * Summary of toDTO
     * @return RegisterInputDTO
     */
    public function toDTO(): RegisterInputDTO
    {
        return new RegisterInputDTO(
            name: (string) $this->validated('name'),
            email: (string) $this->validated('email'),
            password: (string) $this->validated('password'),
            confirmPassword: (string) $this->validated('confirmPassword'),
        );
    }

    /**
     * Summary of messages
     * @return array{confirmPassword.required: string, confirmPassword.same: string, email.email: string, email.required: string, email.unique: string, name.required: string, password.required: string}
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'confirmPassword.required' => 'O campo confirmação de senha é obrigatório.',
            'confirmPassword.same' => 'A confirmação de senha deve ser igual à senha.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha não pode exceder 255 caracteres.',
            'password.includes_letter' => 'A senha deve conter pelo menos uma letra.',
            'password.includes_number' => 'A senha deve conter pelo menos um número.',
            'password.includes_special_character' => 'A senha deve conter pelo menos um caractere especial.',
        ];
    }   
}
