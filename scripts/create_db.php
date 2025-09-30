<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'lphs_sms';

$mysqli = @new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "MySQL connect failed: {$mysqli->connect_error}" . PHP_EOL);
    exit(2);
}

$sql = "CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (! $mysqli->query($sql)) {
    fwrite(STDERR, "Create DB failed: {$mysqli->error}" . PHP_EOL);
    $mysqli->close();
    exit(3);
}

echo "Database '{$db}' ready." . PHP_EOL;
$mysqli->close();


