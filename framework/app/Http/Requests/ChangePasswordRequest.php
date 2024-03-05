<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest {
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
			'old_password' => 'nullable|min:6',
			'new_password' => 'nullable|required_if:old_password,!=,null|min:6',
			'cpassword' => 'nullable|required_with:new_password|same:new_password|min:6',

		];
	}
}
