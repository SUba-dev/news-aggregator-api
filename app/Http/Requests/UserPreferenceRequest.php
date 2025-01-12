<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\NewsSource;
use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferred_sources' => 'nullable|array',
            // 'preferred_sources.*' => 'exists:news_sources,name',
            'preferred_categories' => 'nullable|array',
            // 'preferred_categories.*' => 'exists:categories,name',
            'preferred_authors' => 'nullable|array',
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([
            'preferred_sources' => $this->mapNamesToIds('preferred_sources', NewsSource::class),
            'preferred_categories' => $this->mapNamesToIds('preferred_categories', Category::class),
        ]);
    }

    private function mapNamesToIds(string $key, $model)
    {
        if ($this->has($key)) {
            return collect($this->input($key))
                ->map(fn($name) => $model::where('name', strtolower($name))->first()?->id)
                ->filter()
                ->unique()
                ->toArray();
        }

        return [];
    }

    /**
     * Custom validation error message
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Filter the input data
     */

    public function filters()
    {
        return [
            'preferred_news_sources' => 'trim|escape',
            'preferred_categories' => 'trim|escape',
            'preferred_authors' => 'trim|escape',
        ];
    }
}
