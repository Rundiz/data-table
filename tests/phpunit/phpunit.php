<?php


require __DIR__.'/Autoload.php';

$Autoload = new \Rundiz\DataTable\Tests\Autoload();
$Autoload->addNamespace('Rundiz\\DataTable\\Tests', __DIR__);
$Autoload->addNamespace('Rundiz\\DataTable', dirname(dirname(__DIR__)).'/Rundiz/DataTable');
$Autoload->register();

if (!class_exists('\\Rundiz\\Pagination\\Pagination')) {
    require_once dirname(__DIR__) . '/via-http/_Pagination.php';
}