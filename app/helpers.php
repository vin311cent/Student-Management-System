<?php
declare(strict_types=1);

use App\Core\Request;
use App\Core\Session;

/** Escape output for HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Build an application URL (works in a sub-folder such as /sms/public). */
function url(string $path = '/'): string
{
    return Request::basePath() . '/' . ltrim($path, '/');
}

/** URL of a static asset in public/assets. */
function asset(string $path): string
{
    return Request::basePath() . '/assets/' . ltrim($path, '/');
}

/** Hidden CSRF input for every POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Session::csrfToken()) . '">';
}
