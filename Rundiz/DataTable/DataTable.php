<?php
/**
 * @package Data Table
 * @version 0.1
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\DataTable;

/**
 * Data table class for generate table with header, footer, data list, bulk actions, pagination.<br>
 * This class is copied from the WordPress list table class.
 *
 * @author Vee W.
 */
class DataTable
{


    /**
     * @var integer Columns count.
     */
    protected $columnsCount = 0;


    /**
     * @var \Rundiz\DataTable\Database The database class connector.
     */
    protected $Database;


    /**
     * @var array Store data items fetched from database.
     */
    protected $dataItems = [];


    /**
     * @var integer Number of items to display per page.
     */
    protected $itemsPerPage = 20;


    /**
     * @var string The order query string name. Its value is column name in the db table. Override this if you have many data table in a page.
     */
    protected $orderQueryName = 'order';


    /**
     * @var \Rundiz\Pagination\Pagination The pagination class connector.
     */
    protected $Pagination;


    /**
     * @var string Pagination query string name. Example: pagination query string like this (?paged=3) the pagination query string name is "paged". You can change this value in case that you have many data table in a page.
     */
    protected $paginationQueryName = 'paged';


    /**
     * @var boolean Show footer column or not. (By default it is set to false means not show.)
     */
    protected $showFooterColumn = true;


    /**
     * @var string The sort query string name. Its value basically is ASC, or DESC. Override this if you have many data table in a page.
     */
    protected $sortQueryName = 'sort';


    /**
     * @var mixed Temporary default properties for restore their values when call reset() method.
     */
    protected $tempProperties;


    /**
     * @var integer Total items count from database.
     */
    protected $totalItems = 0;


    /**
     * DataTable class constructor.
     * 
     * @param array $config Available config key: [pdoconfig [dsn], [username], [password], [options]] (see more at http://php.net/manual/en/pdo.construct.php)
     */
    public function __construct(array $config = [])
    {
        $class_properties = get_class_vars(__CLASS__);
        $this->tempProperties = $class_properties;
        unset($class_properties);

        $this->Database = new Database((isset($config['pdoconfig']) ? $config['pdoconfig'] : []));
        $this->Pagination = new \Rundiz\Pagination\Pagination();
        $this->Pagination->page_number_type = 'page_num';
    }// __construct


    /**
     * DataTable class de-constructor.<br>
     * This method will be called reset() method to clear everything to its default values.
     */
    public function __destruct()
    {
        $this->reset();
    }// __destruct


    /**
     * Column checkbox.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @return string Return checkbox element with value. Use return, not echo.
     */
    protected function columnCheckbox($row)
    {
        
    }// columnCheckbox


    /**
     * Column default.<br>
     * Return content for columns by default (no special, the special columns need to create their own column with unique name).<br>
     * For special column method. Example: You have unique name as "time". The special column method name will be columnTime($row).
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @param string $column_unique_name The column unique name.
     * @return string Return column content. Use return, not echo.
     */
    protected function columnDefault($row, $column_unique_name)
    {
        
    }// columnDefault


    /**
     * Display the data table.
     */
    public function display()
    {
        // working about pagination before echo out.
        $this->preparePagination();

        echo "\n";
        $this->printBeforeTableControlsTop();
        $this->printTableControls('top');
        $this->printElementBeforeTable();
        echo '<table class="rundiz-data-table ' . implode(' ', $this->getTableClasses()) . '"' . $this->renderAttributes($this->getTableAttributes()) . '>'."\n";

        echo '    <thead' . $this->renderAttributes($this->getTheadAttributes()) . '>'."\n";
        echo '        <tr' . $this->renderAttributes($this->getTheadTrAttributes()) . '>'."\n";
        echo '            ';
        $this->printColumnHeaders();
        echo '        </tr>'."\n";
        echo '    </thead>'."\n";

        echo '    <tbody' . $this->renderAttributes($this->getTbodyAttributes()) . '>'."\n";
        $this->printRowsOrPlaceholder();
        echo '    </tbody>'."\n";

        if ($this->showFooterColumn === true) {
            echo '    <tfoot' . $this->renderAttributes($this->getTfootAttributes()) . '>'."\n";
            echo '        <tr' . $this->renderAttributes($this->getTfootTrAttributes()) . '>'."\n";
            echo '            ';
            $this->printColumnHeaders(true);
            echo '        </tr>'."\n";
            echo '    </tfoot>'."\n";
        }

        echo '</table>'."\n";
        $this->printElementAfterTable();
        $this->printTableControls('bottom');
    }// display


    /**
     * Get bulk actions.
     * 
     * @return array Return array of bulk action value and text to display in the select box. Example: ['delete' => 'Delete'].
     */
    protected function getBulkActions()
    {
        return [];
    }// getBulkActions


    /**
     * Get column classes.<br>
     * This class will be add in to <code>th</code> or <code>td</code> in the <code>tbody</code>.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @param string $column_unique_name Column unique name for check.
     * @return array Return classes in array value. [class_name1, class_name2].
     */
    protected function getColumnClasses($row, $column_unique_name = '')
    {
        return [];
    }// getColumnClasses


    /**
     * Get column footer classes.<br>
     * This class will be add in to <code>th</code> or <code>td</code> in the <code>tfoot</code>.
     * 
     * @param string $column_unique_name Column unique name for check.
     * @return array Return classes in array value. [class_name1, class_name2].
     */
    protected function getColumnFooterClasses($column_unique_name = '')
    {
        return [];
    }// getColumnFooterClasses


    /**
     * Get column header classes.<br>
     * This class will be add in to <code>th</code> or <code>td</code> in the <code>thead</code>.
     * 
     * @param string $column_unique_name Column unique name for check.
     * @return array Return classes in array value. [class_name1, class_name2].
     */
    protected function getColumnHeaderClasses($column_unique_name = '')
    {
        return [];
    }// getColumnHeaderClasses


    /**
     * Get all the table columns to display.
     * 
     * @return array Return array key that is unique name and text display in array value. [key (unique name, no need to refer to table field) => text display].
     */
    protected function getColumns()
    {
        die('The required getColumns() method is missing from sub class.');
    }// getColumns


    /**
     * Get current pagination page number.
     * 
     * @return integer Return pagination page number.
     */
    protected function getCurrentPage()
    {
        if (isset($_GET[$this->paginationQueryName])) {
            $output = abs(intval($_GET[$this->paginationQueryName]));
        } else {
            $output = 1;
        }

        return max(1, $output);
    }// getCurrentPage


    /**
     * Get primary column from unique name.<br>
     * If there is "checkbox" unique name in the getColumns() method, it will return the second unique name.<br>
     * If there is no "checkbox" unique name in the getColumns() method, it will return the first unique name.<br>
     * You can override this method by set the primary column from unique name from getColumns() method.
     * 
     * @return string Return unique name that was set in the getColumns() method.
     */
    protected function getPrimaryColumn()
    {
        $columns = $this->getColumns();

        if (is_array($columns) && array_key_exists('checkbox', $columns)) {
            // there is "checkbox" unique name in the getColumns() method.
            while (key($columns) !== 'checkbox') {
                next($columns);
            }

            // get next unique name next to the checkbox column.
            $next_checkbox_column_display = next($columns);
            $next_checkbox_column_uniquename = key($columns);

            if ($next_checkbox_column_display !== false) {
                // if there is unique name next to the checkbox column. return it as primary column.
                unset($columns, $next_checkbox_column_display);
                return $next_checkbox_column_uniquename;
            }
        }

        // clear unused variables.
        unset($next_checkbox_column_display, $next_checkbox_column_uniquename);

        // come to this means there is no "checkbox" unique name in the getColumns() method or no column next to checkbox column.
        // return the first column as primary column.
        if (is_array($columns)) {
            reset($columns);
            return key($columns);
        } else {
            throw new \Exception('Wrong value type for getColumns() method. The value type for getColumns() method should be array.');
        }
    }// getPrimaryColumn


    /**
     * Get row actions.<br>
     * Override this method to set actions for each data row.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @param string $column_unique_name Column unique name for check.
     * @param string $primary_column_name Column name that is primary.
     * @return array Return row actions as array. Example: [action key => html link, action key2 => html link2]
     */
    protected function getRowActions($row, $column_unique_name, $primary_column_name)
    {
        return [];
    }// getRowActions


    /**
     * Get sortable columns.<br>
     * Override this method to set sortable columns.
     * 
     * @return array Return array key that is unique name from getColumns() method and array of order by and pre-sorted as ascending (false) or descending (true) in the array value. Example: [key (unique name, no need to refer to table field) => [order_by (refer to table field), false]].
     */
    protected function getSortableColumns()
    {
        return [];
    }// getSortableColumns


    /**
     * Get <code>table</code> attributes (not including class).
     * 
     * @return array Return array of attributes in key => value pairs. example: ['data-sortable' => 'true', 'data-sortcolumn' => 'name'] will becomes (<code>data-sortable="true" data-sortcolumn="name"</code>).
     */
    protected function getTableAttributes()
    {
        return [];
    }// getTableAttributes


    /**
     * Get <code>table</code> classes.
     * 
     * @return array Return array of table classes. [class1, class2, ...]
     */
    protected function getTableClasses()
    {
        return ['data-table', 'list-table'];
    }// getTableClasses


    /**
     * Get table body (<code>tbody</code>) attributes (including class).
     * 
     * @return array Return array of <code>tbody</code> attributes. example: ['data-name' => 'true', 'class' => 'my-tbody'] will becomes (<code>data-name="true" class="my-tbody"</code>).
     */
    protected function getTbodyAttributes()
    {
        return [];
    }// getTbodyAttributes


    /**
     * Get <code>tbody</code> > <code>tr</code> > <code>td</code> attributes where it has no data (including class but do not including colspan).
     * 
     * @return array Return array of <code>tbody</code> > <code>tr</code> > <code>td</code> attributes. example: ['data-name' => 'true', 'class' => 'my-tbody'] will becomes (<code>data-name="true" class="my-tbody"</code>).
     */
    protected function getTbodyTrTdNodataAttributes()
    {
        return ['class' => 'no-item no-data'];
    }// getTbodyTrTdNodataAttributes


    /**
     * Get text message.<br>
     * You can override this method to make it translatable.
     * 
     * @param string $text String key of text message.
     * @return string Return translated from string key to readable message.
     */
    protected function getText($text = '')
    {
        switch ($text) {
            case 'apply':
                return 'Apply';
            case 'bulk_actions':
                return 'Bulk actions';
            case 'current_page':
                return 'Current page';
            case 'delete':
                return 'Delete';
            case 'no_items_found':
                return 'No items found.';
            case 'page_of_x':
                return ' of %d';
            case 'page_x_of_x':
                return '%d of %d';
            case 'select_all':
                return 'Select all';
            case 'select_bulk_action':
                return 'Select bulk action';
            case 'show_more_details':
                return 'Show more details';
            case 'x_items':
                return '%d items';
            default:
                return $text;
        }
    }// getText


    /**
     * Get table foot (<code>tfoot</code>) attributes (including class).
     * 
     * @return array Return array of <code>tfoot</code> attributes. example: ['data-name' => 'true', 'class' => 'my-tfoot'] will becomes (<code>data-name="true" class="my-tfoot"</code>).
     */
    protected function getTfootAttributes()
    {
        return [];
    }// getTfootAttributes


    /**
     * Get table row of <code>tfoot<code> attributes (including class).
     * 
     * @return array Return array of <code>tfoot</code> > <code>tr</code> attributes. example: ['data-name' => 'true', 'class' => 'my-tfoot'] will becomes (<code>data-name="true" class="my-tfoot"</code>).
     */
    protected function getTfootTrAttributes()
    {
        return [];
    }// getTfootTrAttributes


    /**
     * Get table head (<code>thead</code>) attributes (including class).
     * 
     * @return array Return array of <code>thead</code> attributes. example: ['data-name' => 'true', 'class' => 'my-thead'] will becomes (<code>data-name="true" class="my-thead"</code>).
     */
    protected function getTheadAttributes()
    {
        return [];
    }// getTheadAttributes


    /**
     * Get table row of <code>thead</code> attributes (including class).
     * 
     * @return array Return array of <code>thead</code> > <code>tr</code> attributes. example: ['data-name' => 'true', 'class' => 'my-thead'] will becomes (<code>data-name="true" class="my-thead"</code>).
     */
    protected function getTheadTrAttributes()
    {
        return [];
    }// getTheadTrAttributes


    /**
     * Prepare items for listing.
     */
    public function prepareData()
    {
        die('The required prepareData() method is missing from sub class.');
    }// prepareData


    /**
     * Prepare pagination. Any customizable of pagination can override by this method.<br>
     * This data table pagination use Rundiz/Pagination class ( https://github.com/Rundiz/pagination ).
     * 
     * @see rundiz/pagination See more document at https://github.com/Rundiz/pagination or http://rundiz.com/web-resources/pagination-v3
     * @throws \Exception Throw exception if pagination class is missing.
     */
    protected function preparePagination()
    {
        if (!class_exists('\\Rundiz\\Pagination\\Pagination') || !is_object($this->Pagination)) {
            throw new \Exception('The class \\Rundiz\\Pagination\\Pagination is not exists, please install rundiz/pagination class.');
        }

        // prepare pagination values (total items, items per page, current page).
        $this->Pagination->total_records = $this->totalItems;
        $this->Pagination->items_per_page = $this->itemsPerPage;
        $this->Pagination->page_number_value = $this->getCurrentPage();

        // customize how pagination display.
        $this->Pagination->first_page_always_show = true;
        $this->Pagination->first_page_text = '&laquo;';
        $this->Pagination->previous_page_always_show = true;
        $this->Pagination->previous_page_text = '&lsaquo;';
        $this->Pagination->number_display = false;
        $this->Pagination->next_page_always_show = true;
        $this->Pagination->next_page_text = '&rsaquo;';
        $this->Pagination->last_page_always_show = true;
        $this->Pagination->last_page_text = '&raquo;';
    }// preparePagination


    /**
     * Print out before table controls top.
     */
    protected function printBeforeTableControlsTop()
    {
        
    }// printBeforeTableControlsTop


    /**
     * Print out bulk actions (select box).
     * 
     * @param string $which Which position of table controls (top or bottom).
     */
    protected function printBulkActions($which)
    {
        $bulk_actions = $this->getBulkActions();

        if (empty($bulk_actions)) {
            return null;
        }

        echo '        <label class="sr-only screen-reader-only" for="bulk-action-' . $which . '">' . $this->getText('select_bulk_action') . '</label>'."\n";
        echo '        <select id="bulk-action-' . $which . '" name="action' . ($which == 'bottom' ? '2' : '') . '">'."\n";
        echo '            <option value="">' . $this->getText('bulk_actions') . '</option>'."\n";

        if (is_array($bulk_actions)) {
            foreach ($bulk_actions as $value => $title) {
                echo '            <option value="' . $value . '">' . $title . '</option>'."\n";
            }// endforeach;
            unset($title, $value);
        }
        unset($bulk_actions);

        echo '        </select>'."\n";
        echo '        <button id="submit-button-action' . ($which == 'bottom' ? '2' : '') . '" class="button action" type="submit">' . $this->getText('apply') . '</button>';
        echo "\n";
    }// printBulkActions


    /**
     * Display column headers for table. (Echo out.)
     * 
     * @param boolean $is_footer Set to true if this column headers is in footer of the table, set to false if it is header of the table.
     */
    protected function printColumnHeaders($is_footer = false)
    {
        $columns = $this->getColumns();
        $sortable = $this->getSortableColumns();
        $primary = $this->getPrimaryColumn();

        if (!is_array($columns)) {
            throw new \Exception('Wrong value type for getColumns() method. The value type for getColumns() method should be array.');
        }
        if (!is_array($sortable)) {
            throw new \Exception('Wrong value type for getSortableColumns() method. The value type for getSortableColumns() method should be array.');
        }

        if (array_key_exists('checkbox', $columns)) {
            static $checkbox_count = 1;
            $columns['checkbox'] = '<label class="sr-only screen-reader-only" for="checkbox-select-all-' . $checkbox_count . '">' . $this->getText('select_all') . '</label>'
                . '<input id="checkbox-select-all-' . $checkbox_count . '" class="checkbox" type="checkbox">';
            $checkbox_count++;
        }

        $this->columnsCount = 0;
        foreach ($columns as $column_unique_name => $column_display_name) {
            $class = ['column-' . $column_unique_name];

            if ($column_unique_name == $primary) {
                $class[] = 'column-primary';
            }

            if ($is_footer === false) {
                $class = array_merge($class, $this->getColumnHeaderClasses($column_unique_name));
            } else {
                $class = array_merge($class, $this->getColumnFooterClasses($column_unique_name));
            }

            if ($column_unique_name == 'checkbox') {
                $table_cell = 'td';
            } else {
                $table_cell = 'th';
            }

            // sortable column
            if (is_array($sortable)) {
                $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . $_SERVER['PHP_SELF'] . '?';
                if (!empty($_GET)) {
                    foreach ($_GET as $get_key => $get_val) {
                        if ($get_key != $this->paginationQueryName && $get_key != $this->orderQueryName && $get_key != $this->sortQueryName) {
                            $current_url .= $get_key . '=' . urlencode($get_val) . '&amp;';
                        }
                    }// endforeach;
                    unset($get_key, $get_val);
                }
                $current_order = (isset($_GET[$this->orderQueryName]) ? $_GET[$this->orderQueryName] : '');
                $current_sort = (isset($_GET[$this->sortQueryName]) ? $_GET[$this->sortQueryName] : '');

                if (array_key_exists($column_unique_name, $sortable)) {
                    list($order, $descending_first) = $sortable[$column_unique_name];

                    if ($current_order == $order) {
                        $sort = ($current_sort === 'asc' ? 'desc' : 'asc');
                        $class[] = 'sorted';
                        $class[] = $current_sort;
                    } else {
                        $sort = ($descending_first === true ? 'desc' : 'asc');
                        $class[] = 'sortable';
                        $class[] = $sort;
                    }
                    $current_url .= $this->orderQueryName . '=' . urlencode($order) . '&amp;' . $this->sortQueryName . '=' . $sort;

                    unset($descending_first, $order, $sort);
                    $column_display_name = '<a href="' . $current_url . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
                }

                unset($current_order, $current_sort, $current_url);
            }

            if ($is_footer === false) {
                $id = ' id="table-column-' . $column_unique_name . '"';
            } else {
                $id = ' id="table-column-' . $column_unique_name . '-footer"';
            }

            $class = ' class="' . implode(' ', $class) . '"';

            if ($table_cell == 'th') {
                $scope = ' scope="col"';
            } else {
                $scope = '';
            }

            $this->columnsCount++;

            echo '<' . $table_cell . $id . $class . $scope . '>' . $column_display_name . '</' . $table_cell . '>';
        }// endforeach;
        unset($class, $column_display_name, $column_unique_name, $id, $scope, $table_cell);

        unset($columns, $primary, $sortable);
        echo "\n";
    }// printColumnHeaders


    /**
     * Display element after end of the table element.
     */
    protected function printElementAfterTable()
    {
        
    }// printElementAfterTable


    /**
     * Display element before begins of the table element.
     */
    protected function printElementBeforeTable()
    {
        
    }// printElementBeforeTable


    /**
     * Print extra table controls next to bulk actions but before pagination.
     * 
     * @param string $which Which position of table controls (top or bottom).
     */
    protected function printExtraTableControls($which)
    {
        
    }// printExtraTableControls


    /**
     * Print pagination.<br>
     * To display different pagination style more than customize via pagination's properties, overwrite this method in the sub class.
     * 
     * @param string $which Which position of table controls (top or bottom).
     */
    protected function printPagination($which)
    {
        if (!is_object($this->Pagination)) {
            return null;
        }

        echo '    <div class="align-right table-controls-pagination">'."\n";
        echo '        <span class="align-left total-items-found">' . sprintf($this->getText('x_items'), $this->totalItems) . '</span>'."\n";

        $pagination_data = $this->Pagination->getPaginationData();
        $current_page = $this->getCurrentPage();

        if (is_array($pagination_data) && array_key_exists('generated_pages', $pagination_data)) {
            echo '        <div class="align-right pagination-links">'."\n";
            foreach ($pagination_data['generated_pages'] as $page_key => $page_item) {
                if (is_array($page_item)) {
                    if (array_key_exists('link', $page_item) && array_key_exists('page_value', $page_item) && array_key_exists('text', $page_item)) {
                        echo '            ';

                        if ($current_page == $page_item['page_value']) {
                            echo '<span class="pagination-page disabled ' . str_replace('_', '-', $page_key) . '" data-pageValue="' . $page_item['page_value'] . '" aria-hidden="true">' . $page_item['text'] . '</span>';
                        } else {
                            echo '<a class="pagination-page ' . str_replace('_', '-', $page_key) . '" href="' . $page_item['link'] . '" data-pageValue="' . $page_item['page_value'] . '">' . $page_item['text'] . '</a>';
                        }

                        if ($page_key === 'previous_page') {
                            // next to previous page button, display the current page of total page.
                            echo "\n";
                            echo '            ';
                            if ($which == 'top') {
                                echo '<span class="pagination-input">'."\n";
                                echo '                <label class="sr-only screen-reader-only" for="current-pagination-input">' . $this->getText('current_page') . '</label>'."\n";
                                echo '                <input id="current-pagination-input" class="current-page" type="number" name="' . $this->paginationQueryName . '" value="' . $current_page . '" step="1" min="1">'."\n";
                                echo '            </span>'."\n";
                                echo '            <span class="pagination-text">' . sprintf($this->getText('page_of_x'), (isset($pagination_data['total_pages']) ? $pagination_data['total_pages'] : 0)) . '</span>';
                            } else {
                                echo '<span class="pagination-text">' . sprintf($this->getText('page_x_of_x'), $current_page, (isset($pagination_data['total_pages']) ? $pagination_data['total_pages'] : 0)) . '</span>';
                            }
                        }

                        echo "\n";
                    }
                }
            }// endforeach;
            unset($page_item, $page_key);
            echo '        </div>'."\n";// .pagination-links
        }

        unset($current_page, $pagination_data);

        echo '        <div class="clearfix"></div>'."\n";
        echo '    </div>';// .table-controls-pagination
        echo "\n";
    }// printPagination


    /**
     * Loop through data items (rows) and call to print each row.
     */
    protected function printRows()
    {
        foreach ($this->dataItems as $row) {
            $this->printSingleRow($row);
        }
    }// printRows


    /**
     * Display table rows inside <code>tbody</code> or no items place holder if there is no item found.
     */
    protected function printRowsOrPlaceholder()
    {
        if (is_array($this->dataItems) && !empty($this->dataItems)) {
            // there are data.
            $this->printRows();
        } else {
            // there is no data.
            echo '        <tr class="no-item">'."\n";
            echo '            <td' . $this->renderAttributes($this->getTbodyTrTdNodataAttributes()) . ' colspan="' . $this->columnsCount . '">'."\n";
            echo '                ' . $this->getText('no_items_found') . "\n";
            echo '            </td>'."\n";
            echo '        </tr>'."\n";
        }
    }// printRowsOrPlaceholder


    /**
     * Print each row and call to print single row columns.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     */
    protected function printSingleRow($row)
    {
        echo '        <tr>'."\n";
        $this->printSingleRowColumns($row);
        echo '        </tr>'."\n";
    }// printSingleRow


    /**
     * Print row columns.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     */
    protected function printSingleRowColumns($row)
    {
        $columns = $this->getColumns();
        $primary = $this->getPrimaryColumn();

        foreach ($columns as $column_unique_name => $column_display_name)
        {
            $attributes = [];
            $class = [$column_unique_name, 'column-' . $column_unique_name];

            if ($column_unique_name == $primary) {
                $class[] = 'column-primary';
            }

            $class = array_merge($class, $this->getColumnClasses($row, $column_unique_name));

            if ($column_unique_name === 'checkbox') {
                $table_cell = 'th';
                $scope = 'row';
                $table_cell_content = $this->columnCheckbox($row);
            } elseif (method_exists($this, 'column' . ucfirst($column_unique_name))) {
                $table_cell = 'td';
                $table_cell_content = call_user_func([$this, 'column' . ucfirst($column_unique_name)], $row);
                $table_cell_content .= $this->renderRowActions($row, $column_unique_name, $primary);
            } else {
                $table_cell = 'td';
                $table_cell_content = $this->columnDefault($row, $column_unique_name);
                $table_cell_content .= $this->renderRowActions($row, $column_unique_name, $primary);
            }

            $attributes['class'] = implode(' ', $class);
            if (isset($scope)) {
                $attributes['scope'] = $scope;
                unset($scope);
            }
            if (strip_tags($column_display_name) != null) {
                $attributes['data-colname'] = strip_tags($column_display_name);
            }

            echo '            <' . $table_cell . $this->renderAttributes($attributes) . '>'."\n";
            echo '                ' . $table_cell_content . "\n";
            echo '            </' . $table_cell . '>'."\n";

        }// endforeach;
        unset($attributes, $class, $column_display_name, $column_unique_name, $scope, $table_cell, $table_cell_content);

        unset($columns, $primary);
    }// printSingleRowColumn


    /**
     * Print table controls such as bulk actions, pagination or also know as table nav in WordPress.
     * 
     * @param string $which Which position of table controls (top or bottom).
     */
    protected function printTableControls($which = 'top')
    {
        echo '<div class="table-controls ' . $which . '">'."\n";
        if (!empty($this->dataItems)) {
            echo '    <div class="align-left control-actions bulk-actions">'."\n";
            $this->printBulkActions($which);
            echo '    </div>'."\n";
        }
        $this->printExtraTableControls($which);
        $this->printPagination($which);
        echo '    <div class="clearfix"></div>'."\n";
        echo '</div>'."\n";
    }// printTableControls


    /**
     * Render attributes from get***Attributes() methods. (Not echo out just render as string.)
     * 
     * @param array $attributes The attributes as array key => value pairs. Example: ['class' => 'table-cell column-pk', 'data-colname' => 'full name'].
     * @return string Return rendered attributes. Example: <code> class="table-cell column-pk" data-colname="full name"</code>.
     */
    protected function renderAttributes(array $attributes = [])
    {
        $output = '';
        if (is_array($attributes) && !empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                if (!is_object($attribute) && !is_array($attribute) && !is_object($value) && !is_array($value)) {
                    $output .= ' ' . $attribute . '="' . $value . '"';
                }
            }// endforeach;
            unset($attribute, $value);
        }

        return $output;
    }// renderAttributes


    /**
     * Render row actions.
     * 
     * @param object $row Data from PDO in each row from fetchAll() method.
     * @param string $column_unique_name Column unique name for check.
     * @param string $primary_column_name Column name that is primary.
     */
    protected function renderRowActions($row, $column_unique_name, $primary_column_name)
    {
        if ($column_unique_name != $primary_column_name) {
            return null;
        }

        $output = "\n";

        $actions = $this->getRowActions($row, $column_unique_name, $primary_column_name);
        if (is_array($actions)) {
            $action_count = count($actions);
            if ($action_count > 0) {
                $output .= '                <div class="row-actions">'."\n";
                foreach ($actions as $action_key => $action_link) {
                    $output .= '                    <span class="action-' . $action_key . '">' . $action_link . '</span>'."\n";
                }// endforeach;
                unset($action_key, $action_link);
                $output .= '                </div>'."\n";
            }
            unset($action_count);
        }
        unset($actions);

        $output .= '                <button class="toggle-row" type="button"><span class="sr-only screen-reader-only">' . $this->getText('show_more_details') . '</span></button>'."\n";

        return $output;
    }// renderRowActions


    /**
     * Reset all properties including database configuration to start data table class from the beginnings.
     */
    public function reset()
    {
        $this->Pagination->clear();

        if (is_array($this->tempProperties)) {
            foreach ($this->tempProperties as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }// endforeach;
            unset($key, $value);
        }

        $this->tempProperties = null;
    }// reset


}
