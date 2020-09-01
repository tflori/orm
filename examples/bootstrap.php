<?php

// find the next autoload..
$dir = __DIR__;
while ($dir !== '/') {
    if (file_exists($dir . '/vendor/autoload.php')) {
        $loader = require_once $dir . '/vendor/autoload.php';
        break;
    }
    $dir = dirname($dir);
}
if (!isset($loader)) {
    require_once __DIR__ . '/vendor/autoload.php'; // we fail here
}

// create an entitymanager
$em = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTION => new ORM\DbConfig('sqlite', '/tmp/example.sqlite')
]);

// reset the database
$em->getConnection()->query("DROP TABLE IF EXISTS user");

$em->getConnection()->query("CREATE TABLE user (
  id INTEGER NOT NULL PRIMARY KEY,
  username VARCHAR (20) NOT NULL,
  password VARCHAR (32) NOT NULL
)");

$em->getConnection()->query("CREATE UNIQUE INDEX user_username ON user (username)");

$em->getConnection()->query("INSERT INTO user (username, password) VALUES
  ('user_a', '" . md5('password_a') . "'),
  ('user_b', '" . md5('password_b') . "'),
  ('user_c', '" . md5('password_c') . "')
");
