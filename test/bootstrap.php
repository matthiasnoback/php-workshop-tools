<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

putenv('APP_ROOT=' . realpath(__DIR__ . '/../'));

putenv('DB_PATH=' . realpath(getenv('APP_ROOT') . '/var/db'));
