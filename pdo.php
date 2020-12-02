<?php
try {
  $database = new PDO(
    'mysql:host=' . getenv("MYSQL_ADDON_HOST") . ';port=' . getenv('MYSQL_ADDON_PORT') . ';dbname=' . getenv("MYSQL_ADDON_DB"),
    getenv("MYSQL_ADDON_USER"),
    getenv("MYSQL_ADDON_PASSWORD"),
  );
} catch (PDOException $e) {
  echo $e->getMessage();
}
var_dump($database);

try {

  $database = new PDO("mysql:host=" . $settings['dbhost'] . ";port=" . $settings['dbport'] . ";dbname=" . $settings['dbname'], $settings['dbusername'], $settings['dbpassword']);
} catch (PDOException $e) {
  echo $e->getMessage();
}

