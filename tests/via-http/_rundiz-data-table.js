/**
 * Rundiz data-table js. (jQuery required).
 * 
 * @author Vee W.
 */


/**
 * Listening pagination input and redirect when press enter.
 * 
 * @returns {undefined}
 */
function rundizDataTablePaginationInput() {
    $ = jQuery.noConflict();

    pagination_input = $('#current-pagination-input');

    pagination_input.keydown(function(e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            console.log('preventing press enter (key down).');
        }
    });

    pagination_input.keyup(function(e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            new_paged_url = rundizDataTablePaginationInputLink($(this).val());
            window.location.href = new_paged_url;
        }
    });
}// rundizDataTablePaginationInput


/**
 * Generate pagination link from input.
 * 
 * @param {integer} paged_input The input pagination number.
 * @returns {String} Return generated URL with pagination query string.
 */
function rundizDataTablePaginationInputLink(paged_input) {
    if (typeof(location.search) === 'undefined' || location.search === '') {
        //return '';
    }

    var full_url = location.protocol + '//' + location.host + location.pathname + '?';
    var queries = {};
    $.each(document.location.search.substr(1).split('&'),function(c,q) {
        var i = q.split('=');
        if (typeof(i) !== 'undefined' && typeof(i[0]) !== 'undefined' && typeof(i[1]) !== 'undefined') {
            queries[i[0].toString()] = i[1].toString();
            // @todo [rundiz-datatable][your todo] change the "paged" value if you set anything different in the extended 'DataTable' class and 'paginationQueryName' property.
            if (i[0] != 'paged') {
                full_url += i[0].toString() + '=' + i[1].toString();
                full_url += '&';
            }
        }
    });

    full_url += 'paged=' + paged_input;

    return full_url;
}// rundizDataTablePaginationInputLink


/**
 * Toggle all checkboxes
 * 
 * @returns {undefined}
 */
function rundizDataTableToggleCheckbox() {
    $ = jQuery.noConflict();


    checkbox_all1 = $('#checkbox-select-all-1');
    checkbox_all2 = $('#checkbox-select-all-2');

    if (typeof(checkbox_all1) !== 'undefined') {
        checkbox_all1.click(function() {
            console.log('toggling checkbox.');
            $('.data-table-checkbox').prop('checked', this.checked);
            if (typeof(checkbox_all2) !== 'undefined') {
                checkbox_all2.prop('checked', this.checked);
            }
        });
    }

    if (typeof(checkbox_all2) !== 'undefined') {
        checkbox_all2.click(function() {
            console.log('toggling checkbox.');
            $('.data-table-checkbox').prop('checked', this.checked);
            if (typeof(checkbox_all1) !== 'undefined') {
                checkbox_all1.prop('checked', this.checked);
            }
        });
    }

    delete checkbox_all1;
    delete checkbox_all2;
}// rundizDataTableToggleCheckbox


/**
 * Toggle table row (for small screen device such as smart phone).
 * 
 * @returns {Boolean}
 */
function rundizDataTableToggleRow() {
    $ = jQuery.noConflict();

    $('.toggle-row').click(function() {
        console.log('toggling table row.');
        $(this).parents('tr').toggleClass('is-expanded');
    });

    return false;
}// rundizDataTableToggleRow


// run jQuery on page loaded. -----------------------------------------------------------------------------
jQuery(function($) {
    rundizDataTableToggleRow();
    rundizDataTableToggleCheckbox();
    rundizDataTablePaginationInput();
});