<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true; // Cambia a `true` si el usuario está autorizado a hacer esta solicitud
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'email' => 'required|email|unique:users,email',
      'nickname' => [
        'nullable',
        'string',
        'min:3',
        'max:20',
        function ($attribute, $value, $fail) {
          if ($value && User::where('nickname', $value)->exists()) {
            $fail('The ' . $attribute . ' has already been taken.');
          }
        }
      ],
      'password' => [
        'required',
        'string',
        'min:8',
        'regex:/[a-z]/',
        'regex:/[A-Z]/',
        'regex:/[0-9]/',
        'regex:/[@$!%*#?&]/'
      ],
      'password_confirmation' => 'required|same:password'
    ];
  }
}
