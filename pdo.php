<?php
try {
  $database = new PDO(
    'postgresql:host=' . getenv("MYSQL_ADDON_HOST") . ';dbname=' . getenv("MYSQL_ADDON_DB"),
    getenv("MYSQL_ADDON_USER"),
    getenv("MYSQL_ADDON_PASSWORD")
  );
} catch (PDOException $e) {
  echo $e->getMessage();
}
var_dump($database);