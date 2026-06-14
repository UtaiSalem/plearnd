<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$fresh = in_array('--fresh', $argv, true);

if ($fresh) {
    reset_database();
    echo "Database recreated successfully.\n";
    exit(0);
}

ensure_database();
echo "Database is ready.\n";
