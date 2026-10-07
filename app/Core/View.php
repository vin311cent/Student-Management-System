<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/** Renders a view template inside a layout. Views contain presentation only. */
final class View
{
    private const VIEW_DIR = __DIR__ . '/../Views/';

    /**
     * @param array<string,mixed> $data  variables exposed to the view
     * @param string|null         $layout layout name in Views/layouts, or null for none
     */
    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $content = self::capture($view, $data);

        if ($layout === null) {
            echo $content;
            return;
        }
        echo self::capture('layouts/' . $layout, $data + ['content' => $content]);
    }

    private static function capture(string $view, array $data): string
    {
        $file = self::VIEW_DIR . $view . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("View not found: {$view}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
