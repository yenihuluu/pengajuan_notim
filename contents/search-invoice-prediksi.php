<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT inv1.*,
            IF(inv1.fullPayment > 0, inv1.qtyFullpayment, inv1.qtyDp) AS qtyInv,
            IF(inv1.fullPayment > 0, inv1.priceFullpayment, inv1.priceDp) AS priceInv,
            IF(inv1.invoice_status = 0, inv1.total_amount, inv1.fullPayment) AS totalAmount,
            IF(inv1.invoice_status = 1, 0, inv1.dpAmount) AS tempDp

            FROM (
            SELECT inv.`invoice_id`, inv.`invoice_no`, apd.`generate_code_detai`, sp.stockpile_name, apd.total_amount,
                DATE_FORMAT(inv.input_date, '%d/%m/%Y') AS inputDate, DATE_FORMAT(inv.request_date, '%d/%m/%Y') AS requestDate, 
                DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') AS invoiceDate,
                gv.`general_vendor_name` AS vendorName, apd.cost_name, 
                CASE WHEN inv.invoice_status = 0 THEN 'ON PROCESS'
                    WHEN inv.invoice_status = 1 THEN 'LUNAS' ELSE 'RETURNED' END AS statusInv, inv.invoice_status,
                IFNULL((SELECT SUM(amount) FROM invoice_detail WHERE invoice_method_detail = 2 AND invoice_id = inv.`invoice_id`),0) AS dpAmount, 
                IFNULL((SELECT price FROM invoice_detail WHERE invoice_method_detail = 2 AND invoice_id = inv.invoice_id ORDER BY invoice_detail_id DESC LIMIT 1),0) AS priceDp,
                IFNULL((SELECT qty FROM invoice_detail WHERE invoice_method_detail = 2 AND invoice_id = inv.invoice_id ORDER BY invoice_detail_id DESC LIMIT 1),0) AS qtyDp,

                IFNULL((SELECT amount FROM invoice_detail WHERE invoice_method_detail = 1 AND invoice_id = inv.invoice_id),0) AS fullPayment,
                IFNULL((SELECT price FROM invoice_detail WHERE invoice_method_detail = 1 AND invoice_id = inv.invoice_id),0) AS priceFullpayment,
                IFNULL((SELECT qty FROM invoice_detail WHERE invoice_method_detail = 1 AND invoice_id = inv.invoice_id),0) AS qtyFullpayment
                FROM invoice inv
                INNER JOIN accrue_prediction_detail apd ON apd.prediction_detail_id = inv.prediction_detail_id
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id 
                LEFT JOIN stockpile sp ON sp.stockpile_id = inv.`stockpileId`
                WHERE apd.status != 0
        ) inv1";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

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
                    4: true
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
        
        $('#addNew').click(function(e){
            
            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
        
        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/search-invoice-prediksi.php', {invId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } 
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<!-- <a href="#addNew|inv-prediksi-forms" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Costing</a> -->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Status</th>
            <th>Generate Invoice Code</th>
            <th>Generate Detail Code</th>
            <th>Stosckpile Name</th> 
            <th>Input Date</th>
            <th>Request Date</th>
            <th>Invoice Date</th>
            <th>General Vendor</th>
            <th>Biaya</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Prediksi Amount</th>
            <th>Total Invoice</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
                    <a href="#" id="edit|search-inv-prediksi|<?php echo $rowData->invoice_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
            <?php 
                if($rowData->invoice_status == 0){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->statusInv;
                    echo "</td>";
                }else  if($rowData->invoice_status == 1){
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->statusInv;
                    echo "</td>";
                }else{
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->statusInv;
                    echo "</td>";
                }
            ?>
            <td><?php echo $rowData->invoice_no; ?></td>
            <td><?php echo $rowData->generate_code_detai; ?></td>
            <td><?php echo $rowData->stockpile_name; ?></td> 
            <td><?php echo $rowData->inputDate; ?></td>
            <td><?php echo $rowData->requestDate; ?></td>
            <td><?php echo $rowData->invoiceDate; ?></td> 
            <td><?php echo $rowData->vendorName; ?></td> 
            <td><?php echo $rowData->cost_name; ?></td> 
            <td><?php echo number_format($rowData->qtyInv, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->priceInv, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->totalAmount, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->tempDp, 2, ".", ","); ?></td>

        </tr>
        <?php
            }
        } else {
        ?>
        <tr>
            <td colspan="4">
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
