<?php
require dirname(__DIR__) . '/_includes.php';
require 'PeopleDummyDataTable.php';


$db = require dirname(__DIR__) . '/_config.php';


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>DataTable - test sortable</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../_css-reset.css">
        <link rel="stylesheet" href="../_basic-css.css">
        <link rel="stylesheet" href="../_rundiz-data-table.css">
    </head>
    <body>
        <?php
        $PDDTable = new PeopleDummyDataTable(['pdoconfig' => $db]);
        $PDDTable->myTable = $db['tablename'];
        $PDDTable->prepareData();
        $PDDTable->display();
        ?> 

        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <script src="../_rundiz-data-table.js"></script>
    </body>
</html>