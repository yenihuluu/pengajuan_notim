<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 49) {
            $allowReturnInvoice = true;
        }
    }
}


$sql = "SELECT pfc.STATUS as status_1, COUNT(pfc.status) AS total_status, COUNT(pfc2.status) AS total_status_2
FROM pengajuan_freight_cost AS pfc
	LEFT JOIN (
	SELECT p_freight_cost_id, entry_date, `status`
	FROM pengajuan_freight_cost WHERE DATE_FORMAT(entry_date, '%Y-%m-%d') = CURDATE()) AS pfc2
	ON pfc.p_freight_cost_id = pfc2.p_freight_cost_id
GROUP BY pfc.status";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<script type="text/javascript">
    $(document).ready(function() { //executed after the page has loaded

        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table: 'table table-bordered',
            header: 'bootstrap-header', // give the header a gradient background
            footerRow: '',
            footerCells: '',
            icons: '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone: 'bootstrap-icon-unsorted',
            sortAsc: 'icon-chevron-up',
            sortDesc: 'icon-chevron-down',
            active: '', // applied when column is sorted
            hover: '', // use custom css here - bootstrap class may not override it
            filterRow: '', // filter row class
            even: '', // odd row zebra striping
            odd: '' // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
                // this will apply the bootstrap theme if "uitheme" widget is included
                // the widgetOptions.uitheme is no longer required to be set
                theme: "bootstrap",

                widthFixed: true,

                headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

                // widget code contained in the jquery.tablesorter.widgets.js file
                // use the zebra stripe widget if you plan on hiding any rows (filter widget)
                widgets: ['zebra', 'uitheme'],

                //headers: { 0: { sorter: false, filter: false } },

                widgetOptions: {
                    // using the default zebra striping class name, so it actually isn't included in the theme variable above
                    // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                    zebra: ["even", "odd"],

                    filter_functions: {
                        4: true
                    },
                    // reset filters button

                    // set the uitheme widget to use the bootstrap theme class names
                    // this is no longer required, if theme is set
                    // ,uitheme : "bootstrap"

                }
            })
            .tablesorterPager({

                // target the pager markup - see the HTML block below
                container: $(".pager"),

                // target the pager page select dropdown - choose a page
                cssGoto: ".pagenum",

                // remove rows from the table to speed up the sort of large tables.
                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                removeRows: false,
                // output string - default is '{page}/{totalPages}';
                // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

            });

        $('#contentTable a').click(function(e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'show') {
                // $("#dataSearch").fadeOut();
                // $("#dataContent").fadeOut();

                $.blockUI({
                    message: '<h4>Please wait...</h4>'
                });

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('contents/pengajuan-freight-cost.php', {
                    statusId: menu[2]
                }, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden'); //and hide the rotating gif
            }
        });

    });

    function iAmACallbackFunction2() {
        $("#contentTable").fadeIn("slow");
    }
</script>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 33%;">Status</th>
            <th style="width: 33%;">Total Freight Cost <small style="color:grey;"> All</small></th>
            <th style="width: 33%;">Total Freight Cost <small style="color:grey;"> / Day</small></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                ?>

                <tr>
                    <td>
						<?php if ($rowData->status_1 == 1) { ?>
                            <a href="#" id="show|pengajuan|<?php echo $rowData->status_1; ?>" class="badge badge-info">New</a>
                        <?php } elseif ($rowData->status_1 == 2) { ?>
                            <a href="#" id="show|pengajuan|<?php echo $rowData->status_1; ?>" class="badge badge-success">Approved</a>
                        <?php } elseif ($rowData->status_1 == 3) { ?>
                            <a href="#" id="show|pengajuan|<?php echo $rowData->status_1; ?>" class="badge badge-danger">Canceled</a>

                        <?php } ?>
                    </td>
					<td><?php echo $rowData->total_status; ?></td>
                    <td><?php echo $rowData->total_status_2; ?></td>
                </tr>
            <?php
                }
            } else {
                ?>
            <tr>
                <td colspan="2">
                    No data to be shown.
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>