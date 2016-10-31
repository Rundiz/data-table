<?php


namespace Rundiz\DataTable\Tests;

class DBTest extends \PHPUnit_Framework_TestCase
{


    protected $db_config;


    protected function setUp()
    {
        $this->db_config = require dirname(__DIR__) . '/via-http/_config.php';
    }// setUp


    protected function tearDown()
    {
        $this->db_config = null;
    }// tearDown


    /**
     * Test db configuration
     */
    public function testDbConfig()
    {
        $DataTable = new \Rundiz\DataTable\Tests\DataTableExtend(['pdoconfig' => $this->db_config, 'tablename' => $this->db_config['tablename']]);

        // assert Database class from NestedSet class (class chaining).
        $this->assertTrue(is_object($DataTable->Database));
    }// testDbConfig


    /**
     * Test that table name configuration correctly.
     */
    public function testTableNameConfig()
    {
        $DataTable = new \Rundiz\DataTable\Tests\DataTableExtend(['pdoconfig' => $this->db_config, 'tablename' => $this->db_config['tablename']]);
        $result = $DataTable->Database->PDO->query('SELECT * FROM `' . $this->db_config['tablename'] . '`');

        // assert result not false
        $this->assertTrue($result !== false);
    }// testTableNameConfig


}
