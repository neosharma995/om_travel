<?php

namespace App\Http\Requests\Admin;

use App\Models\TourCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourCategoryRequest extends FormRequest
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
        /** @var TourCategory|null $category */
        $category = $this->route('category');
        $categoryId = $category?->id;
        $excludedParentIds = $category !== null
            ? array_merge([$category->id], $category->descendantIds())
            : [];

        return [
            'parent_id' => array_values(array_filter([
                'nullable',
                'integer',
                'exists:tour_categories,id',
                $excludedParentIds !== [] ? Rule::notIn($excludedParentIds) : null,
            ])),
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('tour_categories', 'slug')->ignore($categoryId)],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
