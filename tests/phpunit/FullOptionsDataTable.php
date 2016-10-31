<?php
/**
 * @package Data Table
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT MIT
 */


/**
 * Default values for list data table.<br>
 * This class copied from via-http/test-full-options/PeopleDummyDataTable.php file.
 *
 * @author Vee W.
 */
class FullOptionsDataTable extends \Rundiz\DataTable\Tests\DataTableExtend
{


    /**
     * @var string My table name.
     */
    public $myTable;


    /**
     * {@inheritDoc}
     */
    protected function columnCheckbox($row)
    {
        return '<label class="sr-only screen-reader-only" for="checkbox-id-' . $row->id . '">' . $this->getText('select_all') . '</label>'
            . '<input id="checkbox-id-' . $row->id . '" class="data-table-checkbox" type="checkbox" name="id[]" value="' . $row->id . '">';
    }// columnCheckbox


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
     * {@inheritDoc}
     */
    protected function columnGender($row)
    {
        if (strtolower($row->gender) == 'male') {
            return '♂ ' . $row->gender;
        } else {
            return '♀ ' . $row->gender;
        }
    }// columnGender


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
    protected function getBulkActions()
    {
        return [
            'view_selected' => 'View selected',
        ];
    }// getBulkActions


    /**
     * {@inheritDoc}
     */
    protected function getColumnClasses($row, $column_unique_name = '')
    {
        if ($column_unique_name === 'gender') {
            if (strtolower($row->gender) == 'male') {
                return ['gender-male'];
            } else {
                return ['gender-female'];
            }
        }

        return [];
    }// getColumnClasses


    /**
     * {@inheritDoc}
     */
    protected function getColumnFooterClasses($column_unique_name = '')
    {
        if ($column_unique_name === 'gender') {
            return ['column-gender-thead-tfoot'];
        }

        return [];
    }// getColumnFooterClasses


    /**
     * {@inheritDoc}
     */
    protected function getColumnHeaderClasses($column_unique_name = '')
    {
        if ($column_unique_name === 'gender') {
            return ['column-gender-thead-tfoot'];
        }

        return [];
    }// getColumnHeaderClasses


    /**
     * {@inheritDoc}
     */
    protected function getColumns()
    {
        return [
            'checkbox' => '<input type="checkbox">',
            'name' => 'Full name',
            'email' => 'Email',
            'gender' => 'Gender',
            'ip_address' => 'IP Address',
        ];
    }// getColumns


    /**
     * {@inheritDoc}
     */
    protected function getRowActions($row, $column_unique_name, $primary_column_name)
    {
        return [
            'viewdetails' => '<a href="view-details.php?id=' . $row->id . '" onclick="alert(\'Just for test\'); return false;">Details</a>',
            'edit' => '<a href="edit.php?id=' . $row->id . '" onclick="alert(\'Just for test\'); return false;">Edit</a>',
        ];
    }// getRowActions


    /**
     * {@inheritDoc}
     */
    protected function getSortableColumns()
    {
        return [
            'name' => ['first_name', false],
            'email' => ['email', false],
            'gender' => ['gender', false],
            'ip_address' => ['ip_address', true],
        ];
    }// getSortableColumns


    /**
     * {@inheritDoc}
     */
    protected function getTableAttributes()
    {
        return ['data-sortable' => 'true'];
    }// getTableAttributes


    /**
     * {@inheritDoc}
     */
    protected function getTableClasses()
    {
        $classes = parent::getTableClasses();

        return array_merge($classes, ['full-options-data-table']);
    }// getTableClasses


    /**
     * {@inheritDoc}
     */
    protected function getTbodyAttributes()
    {
        return ['data-tbodyName' => 'full-options-data-table-tbody'];
    }// getTbodyAttributes


    /**
     * {@inheritDoc}
     */
    protected function getTfootAttributes()
    {
        return ['data-tbodyName' => 'full-options-data-table-tfoot'];
    }// getTfootAttributes


    /**
     * {@inheritDoc}
     */
    protected function getTfootTrAttributes()
    {
        return ['data-tbodyName' => 'full-options-data-table-tfoot-tr'];
    }// getTfootTrAttributes


    /**
     * {@inheritDoc}
     */
    protected function getTheadAttributes()
    {
        return ['data-tbodyName' => 'full-options-data-table-thead'];
    }// getTheadAttributes


    /**
     * {@inheritDoc}
     */
    protected function getTheadTrAttributes()
    {
        return ['data-tbodyName' => 'full-options-data-table-thead-tr'];
    }// getTheadTrAttributes


    /**
     * {@inheritDoc}
     */
    public function prepareData()
    {
        if (!class_exists('\\Rundiz\\Pagination\\Pagination') || !is_object($this->Pagination)) {
            throw new \Exception('The class \\Rundiz\\Pagination\\Pagination is not exists, please install rundiz/pagination class.');
        }

        $current_page = $this->getCurrentPage();
        $filter_gender = (isset($_GET['filter-gender']) ? trim($_GET['filter-gender']) : '');
        $search = (isset($_GET['search']) ? trim($_GET['search']) : '');

        $sql = 'SELECT COUNT(*) FROM `' . $this->myTable . '`';
        $sql .= ' WHERE 1';
        if ($filter_gender != null) {
            $sql .= ' AND `gender` = :gender';
        }
        if ($search != null) {
            $sql .= ' AND (';
            $sql .= '`first_name` LIKE :search';
            $sql .= ' OR `first_name` LIKE :search';
            $sql .= ' OR `email` LIKE :search';
            $sql .= ')';
        }
        $stmt = $this->Database->PDO->prepare($sql);
        if ($filter_gender != null) {
            $stmt->bindValue('gender', $filter_gender, \PDO::PARAM_STR);
        }
        if ($search != null) {
            $stmt->bindValue('search', '%' . $search . '%', \PDO::PARAM_STR);
        }
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
        if ($filter_gender != null) {
            $stmt->bindValue('gender', $filter_gender, \PDO::PARAM_STR);
        }
        if ($search != null) {
            $stmt->bindValue('search', '%' . $search . '%', \PDO::PARAM_STR);
        }
        $stmt->execute();
        $items = $stmt->fetchAll();
        unset($order, $sort, $sql, $stmt);

        $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . $_SERVER['PHP_SELF'] . '?';
        if (!empty($_GET)) {
            foreach ($_GET as $get_key => $get_val) {
                if ($get_key != $this->paginationQueryName) {
                    $current_url .= $get_key . '=' . urlencode($get_val) . '&amp;';
                }
            }// endforeach;
            unset($get_key, $get_val);
        }
        $current_url .= $this->paginationQueryName . '=%PAGENUMBER%';
        $this->Pagination->base_url = $current_url;
        unset($current_url);

        // set total items found and data items for listing to the properties.
        $this->totalItems = $total_items;
        $this->dataItems = $items;
        unset($current_page, $filter_gender, $items, $search, $total_items);
    }// prepareData


    /**
     * {@inheritDoc}
     */
    protected function printBeforeTableControlsTop()
    {
        $search_value = (isset($_GET['search']) ? trim($_GET['search']) : '');

        echo '<ul class="data-types">'."\n";
        echo '    <li>More</li>'."\n";
        echo '    <li>data types</li>'."\n";
        echo '    <li>can be</li>'."\n";
        echo '    <li>here</li>'."\n";
        echo '</ul>'."\n";

        echo '<div class="search-box">'."\n";
        echo '    <input id="search-box" type="search" name="search" value="' . htmlspecialchars($search_value) . '">'."\n";
        echo '    <button class="button search-button" type="button">Search</button>'."\n";
        echo '</div>'."\n";

        echo '<div class="clearfix"></div>'."\n";

        unset($search_value);
    }// printBeforeTableControlsTop


    /**
     * {@inheritDoc}
     */
    protected function printElementAfterTable()
    {
        echo '</div><!--.cloak-full-options-data-table-->'."\n";
    }// printElementAfterTable


    /**
     * {@inheritDoc}
     */
    protected function printElementBeforeTable()
    {
        echo '<div class="cloak-full-options-data-table">'."\n";
    }// printElementBeforeTable


    /**
     * {@inheritDoc}
     */
    protected function printExtraTableControls($which)
    {
        if ($which != 'top') {
            return null;
        }

        $current_filter_gender = (isset($_GET['filter-gender']) ? $_GET['filter-gender'] : '');

        echo '    <div class="align-left extra-table-controls control-actions">'."\n";
        echo '        <label class="sr-only screen-reader-only" for="filter-gender">Filter by gender</label>'."\n";
        echo '        <select id="filter-gender" name="filter-gender">'."\n";
        echo '            <option value="">All genders</option>'."\n";
        echo '            <option value="Male"' . (isset($current_filter_gender) && $current_filter_gender === 'Male' ? ' selected="selected"' : '') . '>Male</option>'."\n";
        echo '            <option value="Female"' . (isset($current_filter_gender) && $current_filter_gender === 'Female' ? ' selected="selected"' : '') . '>Female</option>'."\n";
        echo '        </select>'."\n";
        echo '        <button class="button filter-button" type="button">Filter</button>'."\n";
        echo '    </div>'."\n";

        unset($current_filter_gender);
    }// printExtraTableControls


    /**
     * {@inheritDoc}
     */
    protected function printSingleRow($row)
    {
        echo '        <tr';
        if (substr($row->ip_address, 0, 2) == '0.') {
            echo ' class="supicious-ip"';
        }
        echo '>'."\n";
        $this->printSingleRowColumns($row);
        echo '        </tr>'."\n";
    }// printSingleRow


}
