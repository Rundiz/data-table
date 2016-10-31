<?php


require_once dirname(dirname(__DIR__)) . '/Rundiz/DataTable/DataTable.php';
require_once dirname(dirname(__DIR__)) . '/Rundiz/DataTable/Database.php';
if (!class_exists('\\Rundiz\\Pagination\\Pagination')) {
    require_once '_Pagination.php';
}