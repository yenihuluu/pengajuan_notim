<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Vehicle Data ';

$pgId = '';
$gvId = '';


if (isset($_POST['generalVendorId']) && $_POST['generalVendorId'] != '') {
    $gvId = $_POST['generalVendorId'];
}

if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
    $pgId = $_POST['pgId'];
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    $sql = "SELECT  v.vendor_name, s.stockpile_name,  tt.vehicle_no, vh.vehicle_name, 
            c.contract_no, tt.quantity, tt.slip, oks.slip_no, tt.driver,
            CASE WHEN tt.quantity < 15000 THEN 'Small Car' ELSE 'Big Car' END AS type_cars,
            DATE_FORMAT(tt.transaction_date, '%d/%m/%Y') AS transactionDate,
            pg.total_price, pg.total_pph, pg.total_dpp, pg.total_amount,
            oks.* FROM temp_oks_akt_others oks
            INNER JOIN transaction_timbangan tt ON tt.transaction_id = oks.transaction_id
            LEFT JOIN `transaction` t ON tt.transaction_id = t.t_timbangan
            LEFT JOIN stockpile_contract sc ON t.stockpile_contract_id = sc.stockpile_contract_id
            LEFT JOIN contract c ON sc.contract_id = c.contract_id
            LEFT JOIN vendor v ON tt.vendor_id = v.vendor_id
            LEFT JOIN stockpile s on tt.stockpile_id = s.stockpile_id
            LEFT JOIN unloading_cost uc on tt.unloading_cost_id = uc.unloading_cost_id
            LEFT JOIN vehicle vh on uc.vehicle_id = vh.vehicle_id
            LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = oks.pg_id
            WHERE oks.pg_id = {$pgId} ORDER BY oks.pg_id ASC";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
  //  echo $sql;
} ?>

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
        $("#contentTable").tablesorter({
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
                    4: false
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
        
        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
        });
    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
</script>

<?php
    $date_now = date('Y-m');
?>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        <th>Slip No.</th></th>
        <th>Stockpile</th></th>
        <th>Transaction Date</th>
        <th>Vehicle No</th>
        <th>Vehicle</th>
        <th>Cars Type</th>
        <th>Driver</th>
        <th>Vendor Name</th>
        <th>Qty</th>
        <th>Slip</th>
        <th>Contract No</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            $count_slip = 0;
            while ($rowData = $resultData->fetch_object()) {
				//echo $lock_date;
            $totalPrice = $rowData->total_price;
            $totalPph = $rowData->total_pph;
            $totalDpp = $rowData->total_dpp;
            $totalAmount = $rowData->total_amount;

        ?>
        <tr>
            <td><?php echo $rowData->slip_no; ?></td>
            <td><?php echo $rowData->stockpile_name; ?></td>
			<td><?php echo $rowData->transactionDate; ?></td>
            <td><?php echo $rowData->vehicle_no; ?></td>
            <td><?php echo $rowData->vehicle_name; ?></td>
			<td><?php echo $rowData->type_cars;?></td>
            <td><?php echo $rowData->driver; ?></td>
            <td><?php echo $rowData->vendor_name; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->quantity, 2, ".", ","); ?></div></td>
            <td><?php echo $rowData->slip; ?></td>
            <td><?php echo $rowData->contract_no; ?></td>
        </tr>
        
        <?php
            $count_slip++;
            }
        } else {
        ?>
        <tr>
            <td colspan="7">
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