<?php
require __DIR__ . '/_includes.php';


$db = require __DIR__ . '/_config.php';

$Database = new \Rundiz\DataTable\Database($db);
$result = $Database->PDO->query('SELECT * FROM `' . $db['tablename'] . '`');

echo 'Hooray! Db config is correct.';

unset($db, $NestedSet, $result);