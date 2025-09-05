// Define constants for URL and jQuery elements
const break_url = $('#break_url').val();
const $page = $("#page");
const $entries = $('#entries');
const $search = $('#search');
const $dateRangeFrom = __date_range['from'];
const $dateRangeTo = __date_range['to'];

// Function to build the query string
function buildQueryString() {
    const queryParams = [];

    // Append parameters conditionally
    if ($entries.val()) {
        queryParams.push('entries=' + $entries.val());
    }
    if ($search.val()) {
        queryParams.push('search=' + $search.val());
    }
    if ($dateRangeFrom) {
        queryParams.push('from=' + $dateRangeFrom);
    }
    if ($dateRangeTo) {
        queryParams.push('to=' + $dateRangeTo);
    }

    // Always include 'page' parameter
    queryParams.push('page=' + $page.val());

    // Join the query parameters with '&'
    return queryParams.join('&');
}

// Function to update the user document
function updateRecords() {
    const queryString = buildQueryString();
    const current_url = `${break_url}?${queryString}`;
    updateTbody(current_url);
}

// Function to update the table body via AJAX
function updateTbody(current_url) {
    $.ajax({
        url: current_url,
        method: 'GET',
        success: function (data) {
            $('._ajaxData').empty().html(data.view);
        },
        error: function (error) {
            console.error(error);
        }
    });
}



// Function for pagination
function ModulePagination(page) {
    $page.val(page);
    updateRecords();
}
// Initial call to updateRecords
updateRecords();