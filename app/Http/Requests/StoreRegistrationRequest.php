<?php

namespace App\Http\Requests;

use App\Support\WhatsappNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'whatsapp_number' => WhatsappNumber::normalize((string) $this->input('whatsapp_number')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'home_address' => ['required', 'string'],
            'school_origin' => ['required', 'string', 'max:255'],
            'school_class' => ['required', 'string', 'max:50'],
            'gender' => ['required', 'string', 'in:Laki-laki,Perempuan'],
            'service_interests' => ['required', 'array', 'min:1'],
            'service_interests.*' => ['required', 'string', 'in:Worship Leader,Singer,Creative Ministry,Multimedia,Musik - Drum,Musik - Keyboard,Musik - Bass,Musik - Gitar,Usher'],
            'whatsapp_number' => [
                'required',
                'string',
                'regex:/^62\d{8,13}$/',
            ],
            'website' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp_number.regex' => 'Nomor HP harus diawali 62 dan berisi 10 sampai 15 digit angka.',
            'service_interests.required' => 'Pilih minimal satu bidang pelayanan.',
            'service_interests.array' => 'Pilih minimal satu bidang pelayanan.',
            'service_interests.min' => 'Pilih minimal satu bidang pelayanan.',
            'gender.in' => 'Pilih gender yang valid.',
        ];
    }
}
