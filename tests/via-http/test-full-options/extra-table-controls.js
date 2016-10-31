/**
 * Js for extra table controls
 * 
 * @author Vee W.
 */


/**
 * Filter button clicked
 * 
 * @returns {undefined}
 */
function rundizDataTableFilterButtonClick() {
    $ = jQuery.noConflict();

    $('.filter-button').on('click', function() {
        var e = $.Event('keyup');
        e.which = 13;
        $('#current-pagination-input').trigger(e);
    });
}// rundizDataTableFilterButtonClick


/**
 * Listening pagination input and redirect when press enter.<br>
 * This file & function is special from _rundiz-data-table.js that it was added filter input.
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
 * Generate pagination link from input.<br>
 * This file & function is special from _rundiz-data-table.js that it was added filter input.
 * 
 * @param {integer} paged_input The input pagination number.
 * @returns {String} Return generated URL with pagination query string.
 */
function rundizDataTablePaginationInputLink(paged_input) {
    if (typeof(location.search) === 'undefined' || location.search === '') {
        //return '';
    }
    $ = jQuery.noConflict();

    var full_url = location.protocol + '//' + location.host + location.pathname + '?';
    var queries = {};
    $.each(document.location.search.substr(1).split('&'),function(c,q) {
        var i = q.split('=');
        if (typeof(i) !== 'undefined' && typeof(i[0]) !== 'undefined' && typeof(i[1]) !== 'undefined') {
            queries[i[0].toString()] = i[1].toString();
            // @todo [rundiz-datatable][your todo] change the "paged" value if you set anything different in the extended 'DataTable' class and 'paginationQueryName' property.
            if (i[0] != 'paged' && i[0] != 'filter-gender') {
                full_url += i[0].toString() + '=' + i[1].toString();
                full_url += '&';
            }
        }
    });

    full_url += 'paged=' + paged_input + '&filter-gender=' + $('#filter-gender').val() + '&search=' + $('#search-box').val();

    return full_url;
}// rundizDataTablePaginationInputLink

/**
 * Search input enter.
 * 
 * @returns {undefined}
 */
function rundizDataTableSearchInputEnter() {
    $ = jQuery.noConflict();

    search_input = $('#search-box');

    search_input.keydown(function(e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            console.log('preventing press enter (key down).');
        }
    });

    search_input.keyup(function(e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            var e = $.Event('keyup');
            e.which = 13;
            $('#current-pagination-input').trigger(e);
        }
    });

    delete search_input;
}// rundizDataTableSearchInputEnter


/**
 * Search button clicked.
 * 
 * @returns {undefined}
 */
function rundizDataTableSearchButtonClick() {
    $ = jQuery.noConflict();

    $('.search-button').on('click', function() {
        var e = $.Event('keyup');
        e.which = 13;
        $('#current-pagination-input').trigger(e);
    });
}// rundizDataTableSearchButtonClick


// run jQuery on page loaded. -----------------------------------------------------------------------------
jQuery(function($) {
    rundizDataTablePaginationInput();
    rundizDataTableFilterButtonClick();
    rundizDataTableSearchInputEnter();
    rundizDataTableSearchButtonClick();
});