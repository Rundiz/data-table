<?php
/**
 * Test db configuration.
 */


$db['dsn'] = 'mysql:dbname=test_dummy_data;host=localhost;port=3306;charset=UTF8';
$db['username'] = 'user';
$db['password'] = 'pass';
$db['options'] = [
    \PDO::ATTR_EMULATE_PREPARES => true,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // throws PDOException.
];
$db['tablename'] = 'people_dummy_data';

return $db;