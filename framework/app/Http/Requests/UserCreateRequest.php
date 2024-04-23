<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|max:150',
			'email' => 'required|unique:users,email,' . $this->id . ',id,deleted_at,NULL',
			'password' => 'required|confirmed',
			'role' => 'required',
		];
	}
	public function messages() {
		return [
			'role.required' => 'Role field is Required',
		];
	}
}
