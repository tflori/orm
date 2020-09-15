<?php

use ORM\EntityManager;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/entities.php';

/** @var EntityManager $em */

$username = 'user_a'; // $_POST['username']
$password = 'password_a'; // $_POST['password']

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
        ->groupBy('id')
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

/*******************
 * Cache an entity *
 *******************/
$cachedUser = serialize($user);
var_dump($cachedUser);

/******************************
 * Get previously cached User *
 ******************************/
// lets say we cached user3 with password from user1 - so modify the $cachedUser
$cachedUser = str_replace([
    's:1:"1"',
    's:6:"user_a"'
], [
    's:1:"3"',
    's:6:"user_c"'
], $cachedUser);
/** @var User $user */
$user = $em->map(unserialize($cachedUser));
$user = $em->fetch(User::class, 3);
var_dump($user, $user->isDirty(), $user->isDirty('username'));

/*********************************
 * Get a previously fetched user *
 *********************************/
// sqlite returns strings and currently we do not convert to int
$user1 = $em->fetch(User::class, 1); // queries the database again
$user2 = $em->map(new User(['id' => 1]));
$user3 = $em->fetch(User::class, 1); // returns $user2
$user4 = $em->map(new User(['id' => '1']));
var_dump($user1->username, $user2->username, $user3 === $user2, $user1 === $user4);

/********************************
 * Validate data for a new user *
 ********************************/
$data = [
    'username' => 'This username is way to long for a username',
    'password' => null // null is not allowed
];
$result = User::validateArray($data);
echo $result['username']->getMessage() . "\n" . $result['password']->getMessage() . "\n";
