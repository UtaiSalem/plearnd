<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/database.php';

$config = [
    'app_name' => 'LabReserve',
    'demo_password' => 'demo123',
];

ensure_storage();
ensure_database();

$sessionPath = __DIR__ . '/../database/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

session_save_path($sessionPath);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

function app_config(string $key, mixed $default = null): mixed
{
    global $config;
    return $config[$key] ?? $default;
}

function csrf_token(): string
{
    return (string) ($_SESSION['csrf_token'] ?? '');
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        throw new RuntimeException('Invalid CSRF token.');
    }
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function current_user(): ?array
{
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        return null;
    }
    return find_user((int) $userId);
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect_to('/?page=login');
    }
    return $user;
}

function require_role(array $roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        redirect_to(home_path($user));
    }
    return $user;
}

function home_path(array $user): string
{
    return match ($user['role']) {
        'staff' => '/?page=staff',
        'admin' => '/?page=admin_rooms',
        default => '/?page=dashboard',
    };
}

function status_badge(string $status, string $staffStatus): array
{
    if ($status === 'rejected') {
        return ['label' => 'Rejected', 'class' => 'danger'];
    }
    if ($status === 'pending') {
        return ['label' => 'Pending', 'class' => 'warning'];
    }
    return match ($staffStatus) {
        'ready' => ['label' => 'Ready', 'class' => 'success'],
        'in_use' => ['label' => 'In Use', 'class' => 'primary'],
        'cleanup' => ['label' => 'Cleaning', 'class' => 'muted'],
        'issue' => ['label' => 'Issue', 'class' => 'danger'],
        default => ['label' => 'Scheduled', 'class' => 'info'],
    };
}

function room_badge(string $status): array
{
    return match ($status) {
        'available' => ['label' => 'Available', 'class' => 'success'],
        'limited' => ['label' => 'Limited', 'class' => 'warning'],
        default => ['label' => 'Maintenance', 'class' => 'danger'],
    };
}

function fmt_date(string $value): string
{
    return (new DateTimeImmutable($value))->format('d M Y');
}

function fmt_datetime(string $value): string
{
    return (new DateTimeImmutable($value))->format('d M Y H:i');
}

function fmt_time_range(string $start, string $end): string
{
    return (new DateTimeImmutable($start))->format('H:i') . ' - ' . (new DateTimeImmutable($end))->format('H:i');
}

function page_title(string $title): string
{
    return $title . ' | ' . app_config('app_name');
}
