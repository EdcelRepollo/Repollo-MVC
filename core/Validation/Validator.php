<?php // Start sa PHP file; diri magsugod ang Validator class.

declare(strict_types=1); // Gi-enable ang strict types para consistent ang validation inputs.

namespace Core\Validation; // Namespace for validation helpers, diri naka-organize ang validation code.

final class Validator // Final validator; siya ang mo-handle sa form validation rules.
{
    /**
     * Validation errors nga gi-group by field name.
     * Ang matag field pwede naay multiple error messages.
     *
     * @var array<string, list<string>>
     */
    private array $errors = []; // Current validation errors nga na-detect sa latest validation run.

    /**
     * I-check ang data against sa rules.
     * Mo-return ug true kung valid tanan fields, false kung naay errors.
     *
     * @param array<string, mixed> $data
     * @param array<string, list<string>> $rules
     */
    public function validate(array $data, array $rules): bool // Validate ang data gamit ang provided rules.
    {
        // I-reset ang errors every validation run para fresh ang result.
        $this->errors = []; // Clear previous errors gikan sa last validation.

        // I-loop ang each field then i-apply ang rules one by one.
        foreach ($rules as $field => $fieldRules) { // Iagi ang matag field ug ang iyang rules.
            $value = $data[$field] ?? null; // Kuhaon ang submitted field value, or null kung wala.

            foreach ($fieldRules as $rule) { // I-check ang matag rule para ani nga field.
                // I-split ang rule like min:3 into name=min and argument=3.
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null); // Bulagon ang rule name ug optional argument.

                // I-skip ang non-required checks kung empty; required rule maoy mo-handle sa blank value.
                if ($this->isEmpty($value) && $name !== 'required') { // Kung empty and dili required rule...
                    continue; // Skip other rules kay optional empty value ni.
                } // End sa skip check.

                // I-match ang rule name then i-check kung mo-pass ang value sa specific rule.
                $valid = match ($name) { // Pili-on ang validation logic based sa rule name.
                    'required' => ! $this->isEmpty($value), // Required means dapat dili blank ang value.
                    'min' => mb_strlen((string) $value) >= (int) $argument, // Minimum character length nga required.
                    'max' => mb_strlen((string) $value) <= (int) $argument, // Maximum character length nga allowed.
                    'date' => $this->isDate((string) $value), // Dapat valid Y-m-d date format.
                    'in' => in_array((string) $value, explode(',', (string) $argument), true), // Dapat naa sa allowed list.
                    'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false, // Dapat integer value.
                    default => true, // Unknown rules mo-pass as fallback para dili mo-fail unexpectedly.
                }; // Result sa validation para ani nga rule.

                if (! $valid) { // Kung failed ang rule...
                    // I-save ang user-friendly error message para ani nga field.
                    $this->errors[$field][] = $this->message($field, $name, $argument); // Idugang ang error message ani nga field.
                } // End sa failed-rule check.
            } // End sa rules loop para sa field.
        } // End sa fields loop.

        // Kung walay errors, meaning passed ang validation.
        return $this->errors === []; // True kung walay validation errors.
    }

    /**
     * I-return ang validation errors.
     * Gamiton ni sa controller or view para ma-display ang form errors.
     *
     * @return array<string, list<string>>
     */
    public function errors(): array // I-return ang stored validation errors.
    {
        return $this->errors; // Ihatag ang errors sa controller/view.
    }

    private function isEmpty(mixed $value): bool // Check kung blank ba ang value.
    {
        // Empty means null or blank string after trim.
        return $value === null || trim((string) $value) === ''; // True kung null or empty text.
    }

    private function isDate(string $value): bool // Check kung valid Y-m-d date ba ang string.
    {
        // I-accept ang date only kung mo-match sa exact Y-m-d format.
        $timestamp = strtotime($value); // I-convert ang date text into timestamp.

        return $timestamp !== false && date('Y-m-d', $timestamp) === $value; // Siguraduhon nga exact format ang match.
    }

    private function message(string $field, string $rule, ?string $argument): string // Mag-build ug readable error message.
    {
        // Custom messages for required fields para mas clear sa user.
        if ($rule === 'required') { // Required rule naay special messages.
            return match ($field) { // Pili-on ang required message based sa field.
                'title' => 'Title is required.', // Message kung missing ang title.
                'description' => 'Description is required.', // Message kung missing ang description.
                'due_date' => 'Due Date is Required.', // Message kung missing ang due date.
                default => ucfirst(str_replace('_', ' ', $field)) . ' is required.', // Generic required message.
            }; // End sa required message match.
        } // End sa required message block.

        // I-convert ang field name into readable label.
        $label = $field === 'due_date' ? 'Due Date' : ucfirst(str_replace('_', ' ', $field)); // Human-readable label para limpyo ang error text.

        // I-return ang message based sa failed rule.
        return match ($rule) { // Pili-on ang message based sa failed rule.
            'min' => "{$label} must be at least {$argument} characters.", // Message kung kulang ang characters.
            'max' => "{$label} must not be greater than {$argument} characters.", // Message kung sobra ang characters.
            'date' => "{$label} must be a valid date.", // Message kung invalid ang date.
            'in' => "{$label} has an invalid value.", // Message kung wala sa allowed list.
            'integer' => "{$label} must be an integer.", // Message kung dili integer.
            default => "{$label} is invalid.", // Generic invalid message.
        }; // End sa message match.
    }
} // End sa Validator class.
