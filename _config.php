<?php


echo "Config loaded!";

if (file_exists(__DIR__ . '/load_env.php')) require_once __DIR__ . '/load_env.php';
define('API_KEY', $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?? '');
?>
