<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public function __construct(private string $viewDirectory)
    {
    }

    public function render(string $template, array $data = []): void
    {
        $templatePath = $this->viewDirectory . DIRECTORY_SEPARATOR . $template . '.php';
        if (!is_file($templatePath)) {
            throw new \RuntimeException("View template not found: {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        require $this->viewDirectory . DIRECTORY_SEPARATOR . 'layout.php';
    }
}
