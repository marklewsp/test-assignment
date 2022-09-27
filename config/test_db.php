<?php
//$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname=yii2_test',
    'username' => 'yii2advanced',
    'password' => 'secret',
    'charset' => 'utf8',
    ];

return $db;
