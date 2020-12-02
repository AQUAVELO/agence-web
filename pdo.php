<?php
try {
  $database = new PDO(
    'mysql:host=' . getenv("MYSQL_ADDON_DIRECT_HOST") . ';port' . getenv('MYSQL_ADDON_DIRECT_PORT') . ';dbname=' . getenv("MYSQL_ADDON_DB"),
    getenv("MYSQL_ADDON_USER"),
    getenv("MYSQL_ADDON_PASSWORD")
  );
} catch (PDOException $e) {
  echo $e->getMessage();
}
var_dump($database);
