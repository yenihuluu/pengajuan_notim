<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$period = $_POST['period'];
$stockpileTo = $_POST['stockpileTo'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT st.*, mh.`kode_mutasi`, c.`price`, c.`quantity`, ROUND(c.`price` * c.`quantity`,3) AS amount 
FROM stock_transit st
LEFT JOIN mutasi_header mh ON st.`mutasi_header_id` = mh.mutasi_header_id 
LEFT JOIN stockpile_contract sc ON st.`stockpile_contract_id` = sc.`stockpile_contract_id`
LEFT JOIN contract c ON sc.`contract_id` = c.`contract_id`
WHERE st.status = 0 AND mh.`stockpile_to` = {$stockpileTo} AND st.`loading_date` < '{$period}'";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
// </editor-fold>

?>
<script type="text/javascript">
    $(document).ready(function(){	//executed after the page has loaded

        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table      : 'table table-bordered',
            header     : 'bootstrap-header', // give the header a gradient background
            footerRow  : '',
            footerCells: '',
            icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone   : 'bootstrap-icon-unsorted',
            sortAsc    : 'icon-chevron-up',
            sortDesc   : 'icon-chevron-down',
            active     : '', // applied when column is sorted
            hover      : '', // use custom css here - bootstrap class may not override it
            filterRow  : '', // filter row class
            even       : '', // odd row zebra striping
            odd        : ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable2").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme : "bootstrap",

            widthFixed: true,

            headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets : [ 'zebra', 'filter', 'uitheme' ],

            headers: { 0: { sorter: false, filter: false } },

            widgetOptions : {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra : ["even", "odd"],

                filter_functions : {
                    1: true
                },
                // reset filters button
//                filter_reset : ".reset"

                // set the uitheme widget to use the bootstrap theme class names
                // this is no longer required, if theme is set
                // ,uitheme : "bootstrap"

            }
        })
        .tablesorterPager({

            // target the pager markup - see the HTML block below
            container: $(".pager"),

            // target the pager page select dropdown - choose a page
            cssGoto  : ".pagenum",

            // remove rows from the table to speed up the sort of large tables.
            // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
            removeRows: false,
            // output string - default is '{page}/{totalPages}';
            // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

        });



    });


</script>

<input type="hidden" name="period" id="period" value="<?php echo $period; ?>" />
<input type="hidden" name="stockpileTo" id="stockpileTo" value="<?php echo $stockpileTo; ?>" />
 <table class="table table-bordered table-striped" id="contentTable2" style="font-size: 9pt;">
    <thead>
        <tr>
            <th>Stock Transit</th>
			<th>Mutasi Code</th>
            <th>Loading Date</th>
            <th>Netto Weight Draft Survey</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
			//echo 'test';
            while ($row = $resultData->fetch_object()){

        ?>
        <tr>

            <td><?php echo $row->kode_stock_transit; ?></td>
            <td><?php echo $row->kode_mutasi; ?></td>
			<td><?php echo $row->loading_date; ?></td>
			<td style="text-align: center"><?php echo number_format($row->netto_weight, 2, ".", ",");?></td>
			<td style="text-align: center"><?php echo number_format($row->price, 2, ".", ",");?></td>
			<td style="text-align: center"><?php echo number_format($row->quantity, 2, ".", ",");?></td>

        </tr>
        <?php

            }
        } else {
        ?>
        <tr>
            <td colspan="8">
                No data to be shown.
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div class="pager">
    Page: <select class="pagenum input-mini"></select>
    <i class="first icon-step-backward" alt="First" title="First page"></i>
    <i class="prev icon-arrow-left" alt="Prev" title="Previous page"></i>
    <button type="button" class="btn first"><i class="icon-step-backward"></i></button>
    <button type="button" class="btn prev"><i class="icon-arrow-left"></i></button>
    <span class="pagedisplay"></span>
    <i class="next icon-arrow-right" alt="Next" title="Next page"></i>
    <i class="last icon-step-forward" alt="Last" title="Last page"></i>
    <button type="button" class="btn next"><i class="icon-arrow-right"></i></button>
    <button type="button" class="btn last"><i class="icon-step-forward"></i></button>
    <select class="pagesize input-mini">
            <option selected="selected" value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
    </select>
</div>
