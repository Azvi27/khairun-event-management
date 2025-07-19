<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreMemoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'memory_date' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'image' => [
                'nullable',
                File::image()
                    ->max(5 * 1024) // 5MB
                    ->dimensions(
                        minWidth: 100,
                        minHeight: 100,
                        maxWidth: 4000,
                        maxHeight: 4000
                    )
            ],
            'tags' => [
                'nullable',
                'array',
                'max:10'
            ],
            'tags.*' => [
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/'
            ],
            'is_private' => [
                'boolean'
            ],
            'location' => [
                'nullable',
                'string',
                'max:255'
            ]
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kenangan wajib diisi.',
            'title.min' => 'Judul kenangan minimal 3 karakter.',
            'title.max' => 'Judul kenangan maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'memory_date.required' => 'Tanggal kenangan wajib diisi.',
            'memory_date.date' => 'Format tanggal tidak valid.',
            'memory_date.before_or_equal' => 'Tanggal kenangan tidak boleh di masa depan.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
            'image.dimensions' => 'Dimensi gambar tidak sesuai (min: 100x100px, max: 4000x4000px).',
            'tags.max' => 'Maksimal 10 tag.',
            'tags.*.max' => 'Setiap tag maksimal 50 karakter.',
            'tags.*.regex' => 'Tag hanya boleh mengandung huruf, angka, spasi, tanda hubung, dan underscore.',
            'location.max' => 'Lokasi maksimal 255 karakter.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'description' => 'deskripsi',
            'memory_date' => 'tanggal kenangan',
            'image' => 'gambar',
            'tags' => 'tag',
            'is_private' => 'privasi',
            'location' => 'lokasi'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string tags to array if needed
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', explode(',', $this->tags)))
            ]);
        }

        // Ensure is_private is boolean
        if ($this->has('is_private')) {
            $this->merge([
                'is_private' => filter_var($this->is_private, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    /**
     * Get the validated data with additional processing.
     */
    public function getProcessedData(): array
    {
        $validated = $this->validated();
        
        // Add user_id
        $validated['user_id'] = auth()->id();
        
        // Process tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_unique(array_filter($validated['tags']));
        }
        
        return $validated;
    }
}