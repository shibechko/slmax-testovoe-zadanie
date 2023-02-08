<?php

require('config.php');
require('User.php');

$db = new PDO("mysql:host={$host};dbname={$db_name}", $user, $pass);
if(!$db) {
    die("Connection error");
}

// $u = new User($db, 6);

// $u2 = new User($db, [
//     'name' => "Name",
//     'surname' => 'Surname',
//     'birthday' => '1983-10-08',
//     'gender' => 1,
//     'city' => 'Minsk'
// ]);

// $u->remove();

// print User::getAge('1983-10-08');