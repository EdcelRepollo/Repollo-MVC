<?php // Start PHP file; Validator class begins here.

declare(strict_types=1); // Strict types para consistent ang validation inputs.

namespace Core\Validation; // Namespace for validation helpers.

final class Validator // Final validator; handles form validation rules.
{
    /**
     * Validation errors grouped by field name.
     *
     * @var array<string, list<string>>
     */
    private array $errors = []; // Current validation errors.

    /**
     * Check data against rules; returns true kung valid tanan fields.
     *
     * @param array<string, mixed> $data
     * @param array<string, list<string>> $rules
     */
    public function validate(array $data, array $rules): bool // Validate data using provided rules.
    {
        // Reset errors every validation run.
        $this->errors = []; // Clear previous errors.

        // Loop each field and apply its rules one by one.
        foreach ($rules as $field => $fieldRules) { // Go through each field and its rules.
            $value = $data[$field] ?? null; // Get submitted field value or null.

            foreach ($fieldRules as $rule) { // Check each rule for this field.
                // Split rule like min:3 into name=min and argument=3.
                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null); // Separate rule name and argument.

                // Skip non-required checks when empty; required handles empty value.
                if ($this->isEmpty($value) && $name !== 'required') { // If empty and not required rule...
                    continue; // Skip other rules for empty optional value.
                } // End skip check.

                // Match rule name and check if value passes.
                $valid = match ($name) { // Choose validation logic by rule name.
                    'required' => ! $this->isEmpty($value), // Required means value is not blank.
                    'min' => mb_strlen((string) $value) >= (int) $argument, // Minimum character length.
                    'max' => mb_strlen((string) $value) <= (int) $argument, // Maximum character length.
                    'date' => $this->isDate((string) $value), // Must be valid Y-m-d date.
                    'in' => in_array((string) $value, explode(',', (string) $argument), true), // Must be in allowed list.
                    'integer' => filter_var($value, FILTER_VALIDATE_INT) !== false, // Must be integer value.
                    default => true, // Unknown rules pass; simple fallback.
                }; // Validation result for this rule.

                if (! $valid) { // If rule failed...
                    // Save user-friendly error message for this field.
                    $this->errors[$field][] = $this->message($field, $name, $argument); // Add error message.
                } // End failed-rule check.
            } // End rules loop for field.
        } // End fields loop.

        // No errors means validation passed.
        return $this->errors === []; // True if no validation errors.
    }

    /**
     * Return validation errors; gamiton sa form display.
     *
     * @return array<string, list<string>>
     */
    public function errors(): array // Return stored validation errors.
    {
        return $this->errors; // Give errors to controller/view.
    }

    private function isEmpty(mixed $value): bool // Check if value is blank.
    {
        // Empty means null or blank string after trim.
        return $value === null || trim((string) $value) === ''; // True for null or empty text.
    }

    private function isDate(string $value): bool // Check if string is valid Y-m-d date.
    {
        // Accept date only when it matches Y-m-d format.
        $timestamp = strtotime($value); // Convert date text to timestamp.

        return $timestamp !== false && date('Y-m-d', $timestamp) === $value; // Ensure exact format matches.
    }

    private function message(string $field, string $rule, ?string $argument): string // Build readable error message.
    {
        // Custom messages for required fields.
        if ($rule === 'required') { // Required rule gets special messages.
            return match ($field) { // Choose required message by field.
                'title' => 'Title is required.', // Title missing message.
                'description' => 'Description is required.', // Description missing message.
                'due_date' => 'Due Date is Required.', // Due date missing message.
                default => ucfirst(str_replace('_', ' ', $field)) . ' is required.', // Generic required message.
            }; // End required message match.
        } // End required message block.

        // Convert field name to readable label.
        $label = $field === 'due_date' ? 'Due Date' : ucfirst(str_replace('_', ' ', $field)); // Human-readable field label.

        // Return message based on failed rule.
        return match ($rule) { // Choose message by failed rule.
            'min' => "{$label} must be at least {$argument} characters.", // Too short message.
            'max' => "{$label} must not be greater than {$argument} characters.", // Too long message.
            'date' => "{$label} must be a valid date.", // Invalid date message.
            'in' => "{$label} has an invalid value.", // Not in allowed list message.
            'integer' => "{$label} must be an integer.", // Invalid integer message.
            default => "{$label} is invalid.", // Generic invalid message.
        }; // End message match.
    }
} // End Validator class.
