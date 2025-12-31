<?php

namespace App\Http\Requests\Story;

use App\Http\Requests\Story\v1\IntroFormRequest;
use App\Http\Requests\Story\v1\SectionAFormRequest;
use App\Http\Requests\Story\v1\SectionBFormRequest;
use App\Http\Requests\Story\v1\SectionCFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoryFormRequest extends FormRequest
{
    /**
     * Valid step IDs.
     */
    private const VALID_STEPS = ['intro', 'section-a', 'section-b', 'section-c'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * Prepare the data for validation.
     *
     * Validates step.id and page before processing to ensure valid data.
     * Throws ValidationException with 400 status for invalid values.
     *
     * @throws ValidationException If step.id or page is invalid
     */
    protected function prepareForValidation(): void
    {
        $stepId = $this->input('step.id');
        $page = $this->input('page');

        // Validate step.id is present and valid
        if (empty($stepId)) {
            throw ValidationException::withMessages([
                'step.id' => 'The step.id field is required.',
            ])->status(400);
        }

        if (! in_array($stepId, self::VALID_STEPS, true)) {
            throw ValidationException::withMessages([
                'step.id' => 'The selected step.id is invalid. Valid values are: ' . implode(', ', self::VALID_STEPS),
            ])->status(400);
        }

        // Validate page is a positive integer
        if (! is_numeric($page) || (int) $page < 1) {
            throw ValidationException::withMessages([
                'page' => 'The page must be a positive integer.',
            ])->status(400);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return match ($this->input('step.id')) {
            'intro' => (new IntroFormRequest((int) $this->input('page')))->rules(),
            'section-a' => (new SectionAFormRequest((int) $this->input('page')))->rules(),
            'section-b' => (new SectionBFormRequest((int) $this->input('page')))->rules(),
            'section-c' => (new SectionCFormRequest((int) $this->input('page')))->rules(),
            default => [],
        };
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return match ($this->input('step.id')) {
            'intro' => (new IntroFormRequest((int) $this->input('page')))->messages(),
            'section-a' => (new SectionAFormRequest((int) $this->input('page')))->messages(),
            'section-b' => (new SectionBFormRequest((int) $this->input('page')))->messages(),
            'section-c' => (new SectionCFormRequest((int) $this->input('page')))->messages(),
            default => [],
        };
    }
}
