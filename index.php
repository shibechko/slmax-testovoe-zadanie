<?php

require('config.php');
require('User.php');
require('UserRepository.php');

$db = new PDO("mysql:host={$host};dbname={$db_name}", $user, $pass);
if(!$db) {
    die("Connection error");
}

// $u = new User($db, 1);
// var_dump( $u->format(age: True) );
// var_dump( $u->format(gender:True) );
// var_dump( $u->format(age: True, gender:True) );

// $u2 = new User($db, [
//     'name' => "Name",
//     'surname' => 'Surname',
//     'birthday' => '1983-10-08',
//     'gender' => 1,
//     'city' => 'Minsk'
// ]);

// $u->remove();

// print User::getAge('1983-10-08');

// print User::getGender(0);

$filters = [];
$ur = new UserRepository($db, $filters);
var_dump($ur->getUsers());
var_dump($ur->delUsers());
