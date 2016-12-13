<?php

use ORM\EntityManager;

require __DIR__ . '/vendor/autoload.php';

$username = 'user_a'; // $_POST['username']
$password = 'password_a'; // $_POST['password']

/**************************
 * SETUP EXAMPLE DATABASE *
 **************************/
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
  ('user_b', '" . md5('password_b') . "'),
  ('user_c', '" . md5('password_c') . "')
");

/*********************
 * DEFINE THE ENTITY *
 *********************/

/**
 * Class User
 *
 * The following annotations are optional
 * @property int id
 * @property string username
 * @property string password
 */
class User extends ORM\Entity {}

/*******************************
 * Fetch entity with own query *
 *******************************/
$user = $em->fetch(User::class)
    ->setQuery("SELECT * FROM user WHERE username = ? AND password = ?", [$username, md5($password)])
    ->one();

var_dump($user);


/*******************************
 * Fetch with where conditions *
 *******************************/
$user = $em->fetch(User::class)
    ->where('username', 'LIKE', $username)
    ->andWhere('password', '=', md5($password))
    ->one();

var_dump($user);

/*******************************************
 * Fetch with parenthesis, group and order *
 *******************************************/
try {
    $fetcher = $em->fetch(User::class)
                  ->where(User::class . '::username LIKE ?', 'USER_A')
                  ->andWhere('password', '=', md5('password_a'))
                  ->orParenthesis()
                      ->where(User::class . '::username = ' . $em->getConnection()->quote('user_b'))
                      ->andWhere('t0.password = \'' . md5('password_b') . '\'')
                      ->close()
                  ->groupBy('rowid')
                  ->orderBy(
                      'CASE WHEN username = ? THEN 1 WHEN username = ? THEN 2 ELSE 3 END',
                      'ASC',
                      ['user_a', 'user_b']
                  );
    $users = $fetcher->all();

    var_dump($users);
} catch (\PDOException $exception) {
    file_put_contents('php://stderr', $exception->getMessage() . "\nSQL:" . $fetcher->getQuery());
}

/******************************
 * Get previously cached User *
 ******************************/
//$user = unserialize(file_get_contents('/tmp/user'));
//$user = $em->fetch(User::class, $user->id);
//var_dump($user, $user->isDirty(), $user->isDirty('password'));

//$query = new \ORM\QueryBuilder('ab');
//$query->where()
