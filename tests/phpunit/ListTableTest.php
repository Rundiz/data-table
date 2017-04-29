<?php


namespace Rundiz\DataTable\Tests;

class ListTableTest extends \PHPUnit\Framework\TestCase
{


    protected $db_config;


    /**
     * @var \FullOptionsDataTable
     */
    protected $DataTable;


    protected function setUp()
    {
        $this->db_config = require dirname(__DIR__) . '/via-http/_config.php';
        $this->DataTable = new \FullOptionsDataTable(['pdoconfig' => $this->db_config]);
        $this->DataTable->myTable = $this->db_config['tablename'];
    }// setUp


    protected function tearDown()
    {
        $this->db_config = null;
        $this->DataTable = null;
    }// tearDown


    public function testListDataFullOptions()
    {
        $this->DataTable->prepareData();
        ob_start();
        $this->DataTable->display();
        $dataTableDisplay = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(stristr($dataTableDisplay, 'data-types') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'search-box') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'bulk-actions') !== false);
        $this->assertTrue(stristr($dataTableDisplay, '<select id="bulk-action-top"') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'total-items-found') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'pagination-links') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'pagination-page') !== false);
        $this->assertTrue(stristr($dataTableDisplay, '<table') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'class="rundiz-data-table') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'sortable asc') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'full-options-data-table-thead') !== false);
        $this->assertTrue(stristr($dataTableDisplay, '<tbody') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'full-options-data-table-tbody') !== false);
        $this->assertTrue(stristr($dataTableDisplay, 'column-primary') !== false);

        echo "\n\n";
        echo mb_strimwidth($dataTableDisplay, 0, 900, '...');
        echo "\n\n";
        echo 'To see more, please test via http or web browser.';
        echo "\n\n";

        unset($dataTableDisplay);
    }// testListDataFullOptions


}
