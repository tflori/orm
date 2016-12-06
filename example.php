<?php

use ORM\EntityManager;

require __DIR__ . '/vendor/autoload.php';

$em = new EntityManager([
    EntityManager::OPT_DEFAULT_CONNECTION => new \ORM\DbConfig('sqlite', '/tmp/example.sqlite')
]);

$em->getConnection()->query("DROP TABLE IF EXISTS user");

$em->getConnection()->query("CREATE TABLE user (
  id INTEGER NOT NULL PRIMARY KEY,
  username VARCHAR (50) NOT NULL,
  password VARCHAR (32) NOT NULL
)");

$em->getConnection()->query("CREATE UNIQUE INDEX user_username ON user (username)");

$em->getConnection()->query("INSERT INTO user (username, password) VALUES
  ('user_a', '" . md5('password_a') . "'),
  ('user_b', '" . md5('password_b') . "')
");

class User extends ORM\Entity {}

$user = $em->fetch(User::class)
    ->setQuery("SELECT * FROM user WHERE username = ? AND password = ?", ['user_a', md5('password_a')])
    ->one();

var_dump($user);

//// simple example
//$em->fetch(User::class)
//    ->where('username', 'ILIKE', $_POST['username'])
//    ->and('password', '=', md5($_POST['password']))
//    ->one();
//
//// complex example (useless)
//$em->fetch(User::class)
//    ->where(User::class . '.username ILIKE ?', 'USER_A')
//    ->and('password', '=', md5('password_a'))
//    ->orParenthesis()
//        ->and(User::class . '.username = ' . $em->getConnection()->quote('user_b'))
//        ->and('user.password = \'' . md5('password_b') . '\'')
//        ->close()
//    ->groupBy('rowid')
//    ->orderBy(User::class . '.password')
//    ->orderBy('CASE WHEN username = ? THEN 1 WHEN username = ? THEN 2 ELSE 3 END', 'user_a', 'user_b')
//    ->limit(1)
//    ->one();
