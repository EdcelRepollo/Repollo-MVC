<?php // Start PHP file; view engine code begins here.

declare(strict_types=1); // Strict typing enabled.

namespace Core\View; // Namespace for view-related classes.

use RuntimeException; // Used when a view file cannot be found.

final readonly class Engine // Final readonly class; view path cannot be changed after construct.
{
    // Store views folder path; diri pangitaon ang PHP view files.
    public function __construct(private string $viewPath) // Constructor stores base views folder.
    {
    } // Constructor ends.

    /**
     * Render a view and optionally wrap it inside a layout.
     * Function flow: render page first, then inject it into layout if layout is set.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $view, array $data = [], string $layout = 'layouts/main'): string // Render public view output.
    {
        // Render page content first.
        $content = $this->renderFile($view, $data); // Render requested view into $content.

        // If layout is empty, return content only; otherwise place content inside layout.
        return $layout === '' // Check if caller wants no layout.
            ? $content // Return raw view content.
            : $this->renderFile($layout, array_merge($data, ['content' => $content])); // Render layout with content variable.
    }

    public function e(mixed $value): string // Escape value before printing in HTML.
    {
        // Escape output; protection ni against unwanted HTML/script.
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); // Convert special chars safely.
    }

    /**
     * Load one PHP view file and capture its output as string.
     * Function flow: build file path, extract data, include file, return captured HTML.
     *
     * @param array<string, mixed> $data
     */
    private function renderFile(string $view, array $data): string // Render one PHP view file.
    {
        // Convert view name like tasks/index to full PHP file path.
        $file = $this->viewPath . '/' . str_replace('.', '/', $view) . '.php'; // Build physical file path.

        if (! file_exists($file)) { // Check if view file exists.
            throw new RuntimeException("View {$view} was not found."); // Stop with clear error if missing.
        } // End missing-view check.

        // Make data array variables available inside the view, then capture output.
        extract($data, EXTR_SKIP); // Turn data keys into variables for the view.
        ob_start(); // Start output buffering so HTML becomes a string.
        include $file; // Run the PHP view file.

        return (string) ob_get_clean(); // Return captured view output and close buffer.
    }
} // End Engine class.
