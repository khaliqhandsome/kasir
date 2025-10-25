<?php

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function invoice_number(): string
{
    return 'INV-' . date('Ymd-His');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message === null) {
        if (empty($_SESSION['flash'][$key])) {
            return null;
        }
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    $_SESSION['flash'][$key] = $message;
    return null;
}

function input_value(array $data, string $key, $default = '')
{
    return htmlspecialchars($data[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}

function selected($left, $right): string
{
    return $left == $right ? 'selected' : '';
}

function active_menu(string $current, string $expected): string
{
    return $current === $expected ? 'active' : '';
}
