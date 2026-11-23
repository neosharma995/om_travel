<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $tourId = $this->route('tour')?->id;

        return [
            'destination_id' => ['nullable', 'exists:destinations,id'],
            'tour_category_id' => ['nullable', 'exists:tour_categories,id'],
            'tour_type_id' => ['nullable', 'exists:tour_types,id'],
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('tours', 'slug')->ignore($tourId)],
            'location' => ['nullable', 'string', 'max:160'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:90'],
            'duration_label' => ['nullable', 'string', 'max:60'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'reviews_count' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'highlights' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
