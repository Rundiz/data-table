# Data Table

Data table class for generate table with header, footer, data list, bulk actions, pagination. 
This repository use the idea of WordPress's list table class to generate data table easily and supported responsive table. 
Tested on PHP 8.4.

[![Latest Stable Version](https://poser.pugx.org/rundiz/data-table/v/stable)](https://packagist.org/packages/rundiz/data-table)
[![License](https://poser.pugx.org/rundiz/data-table/license)](https://packagist.org/packages/rundiz/data-table)
[![Total Downloads](https://poser.pugx.org/rundiz/data-table/downloads)](https://packagist.org/packages/rundiz/data-table)

## Example

### Install
I recommend you to install this library via Composer and use Composer autoload for easily include the files. If you are not using Composer, you have to manually include these files by yourself.<br>
Please make sure that the path to files are correct.

```php
require_once '/path/to/Rundiz/DataTable/DataTable.php';
require_once '/path/to/Rundiz/DataTable/Database.php';
if (!class_exists('\\Rundiz\\Pagination\\Pagination')) {
    require_once '/path/to/Pagination.php';
}
```

Import mysql_db_test_dummy_data.sql to MySQL or MariaDB (for test only).

### Configuration
You have to provide db configuration to the class to read, update, delete the data.

```php
$db['dsn'] = 'mysql:dbname=YOUR_DB_NAME;host=localhost;port=3306;charset=UTF8';
$db['username'] = 'admin';
$db['password'] = 'pass';
$db['options'] = [
    \PDO::ATTR_EMULATE_PREPARES => true,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // throws PDOException.
];
$db['tablename'] = 'people_dummy_data';
```

### Override
You have to override the Rundiz\DataTable\DataTable by extends to sub class. Open the parent class or the [API document] (http://apidocs.rundiz.com/data-table/) to see more about methods and properties that are able to override.<br>
Assume that the sub class file name is PeopleDummyDataTable.php.

```php
class PeopleDummyDataTable extends \Rundiz\DataTable\DataTable
{


    /**
     * @var string My table name.
     */
    public $myTable;


    /**
     * {@inheritDoc}
     */
    protected function columnDefault($row, $column_unique_name)
    {
        switch ($column_unique_name) {
            default:
                if (isset($row->{$column_unique_name})) {
                    return $row->{$column_unique_name};
                }
        }
    }// columnDefault


    /**
     * Special column method for "name".
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @return string Return column content. Use return, not echo.
     */
    protected function columnName($row)
    {
        return $row->first_name . ' ' . $row->last_name;
    }// columnName


    /**
     * {@inheritDoc}
     */
    protected function getColumns()
    {
        return [
            'name' => 'Full name',
            'email' => 'Email',
            'gender' => 'Gender',
            'ip_address' => 'IP Address',
        ];
    }// getColumns


    /**
     * {@inheritDoc}
     */
    public function prepareData()
    {
        if (!class_exists('\\Rundiz\\Pagination\\Pagination') || !is_object($this->Pagination)) {
            throw new \Exception('The class \\Rundiz\\Pagination\\Pagination is not exists, please install rundiz/pagination class.');
        }

        $current_page = $this->getCurrentPage();

        $sql = 'SELECT COUNT(*) FROM `' . $this->myTable . '`';
        $stmt = $this->Database->PDO->prepare($sql);
        $stmt->execute();
        $total_items = $stmt->fetchColumn();
        unset($stmt);

        // order (column) and sort (asc, desc)
        $order = 'id';
        if (isset($_GET[$this->orderQueryName])) {
            if (in_array($_GET[$this->orderQueryName], ['id', 'first_name', 'last_name', 'email', 'gender', 'ip_address'])) {
                $order = $_GET[$this->orderQueryName];
            }
        }
        $sort = 'ASC';
        if (isset($_GET[$this->sortQueryName])) {
            if (in_array(strtoupper($_GET[$this->sortQueryName]), ['ASC', 'DESC'])) {
                $sort = strtoupper($_GET[$this->sortQueryName]);
            }
        }

        $sql = str_replace('COUNT(*)', '*', $sql);
        $sql .= ' ORDER BY `' . $order . '` ' . $sort;
        $sql .= ' LIMIT ' . (($current_page - 1) * $this->itemsPerPage) . ', ' . $this->itemsPerPage;
        $stmt = $this->Database->PDO->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchAll();
        unset($order, $sort, $sql, $stmt);

        $this->Pagination->base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $this->paginationQueryName . '=%PAGENUMBER%';

        // set total items found and data items for listing to the properties.
        $this->totalItems = $total_items;
        $this->dataItems = $items;
        unset($current_page, $items, $total_items);
    }// prepareData


}
```

### Initialize the class
In the php file that will be display the data table, write this simple code.

```php
include 'PeopleDummyDataTable.php';

$PDDTable = new PeopleDummyDataTable(['pdoconfig' => $db]);
$PDDTable->myTable = $db['tablename'];
$PDDTable->prepareData();
$PDDTable->display();
```

The `$db` variable is in the configuration section. Set your database values there correctly and run.

### Styles and scripts
The css and javascript is already in the **tests/via-http** folder. You can copy those files to use easily or customize to the way you want.

For more example, please look in tests folder.