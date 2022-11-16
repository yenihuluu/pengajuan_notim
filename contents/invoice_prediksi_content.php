<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT apd.*, apd.prediction_detail_id AS detailId, gv.general_vendor_name, sh.shipment_code, apd.total_amount as prediksiAmount,
        CASE WHEN apd.status = 0 THEN 'Belum Lunas' 
            WHEN apd.status = 1 THEN 'Approve'
            WHEN apd.status = 3 THEN 'Reject'
        ELSE NULL END AS statusPrediksi, apd.status as statusPrediksiDT,
        dp.dpAmount, dp.methodId, dp.maxC, dp.minC, dp.invQty, dp.invPrice, dp.invAmount, dp.statusInv, dp.invoice_status
            FROM accrue_prediction_detail apd
            LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
            LEFT JOIN accrue_prediction ap ON ap.prediction_id = apd.prediction_id 
            LEFT JOIN shipment sh ON sh.shipment_id = ap.shipment_id 
            LEFT JOIN(
                        SELECT DISTINCT(inv.prediction_detail_id) AS detailId, 
                        CASE WHEN invd.invoice_method_detail = 2 THEN SUM(invd.amount) 
                            WHEN invd.invoice_method_detail = 1 THEN invd.amount ELSE 0 END AS dpAmount, 
                        CASE WHEN inv.invoice_status = 0 THEN 'Belum Lunas' 
                                WHEN inv.invoice_status = 1 THEN 'Lunas' else 'Reject' END AS statusInv, inv.invoice_status,
                       invd.invoice_method_detail as methodId, invd.`max_charge` as maxC, invd.`min_charge` as minC, invd.`qty` as invQty, 
                       invd.`price` as invPrice, invd.`amount` as invAmount
                        FROM invoice inv
                        LEFT JOIN invoice_detail invd ON inv.invoice_id = invd.invoice_id
                        WHERE inv.invoice_status = 0
                        GROUP BY inv.invoice_id
                    ) AS dp ON dp.detailId = apd.prediction_detail_id
            WHERE apd.status != 1 ORDER BY apd.prediction_detail_id DESC";
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
            if (menu[0] == 'approve') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/inv-prediksi-forms.php', {detailId: menu[2]}, iAmACallbackFunction2);

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
            <th>Status </th>
            <th>Generate Code</th>
            <th>Shipment Code</th>
            <th>Biaya</th>
            <th>General Vendor</th>
            <th>Max Charge</th>
            <th>Min Charge</th>
            <th>Qty</th>
            <th>Price/MT</th>
            <th>Total Prediksi</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                if($rowData->methodId == 2 ){
                    $invMin = $rowData->maxC;
                    $invMax = $rowData->minC;
                    $invQty = $rowData->invQty;
                    $invPrice = $rowData->invPrice;
                    $invAmount = $rowData->invAmount;
                    $statusText = $rowData->statusInv;
                    $statusId = $rowData->invoice_status;
                }else{
                    $invMin = $rowData->max_charge;
                    $invMax = $rowData->min_charge;
                    $invQty = $rowData->qty;
                    $invPrice = $rowData->priceMT;
                    $invAmount = $rowData->total_amount;
                    $statusText = $rowData->statusPrediksi;
                    $statusId = $rowData->statusPrediksiDT;
                }
                $dpAmount = $rowData->dpAmount;
                $totalPrediksi = $rowData->prediksiAmount;

                if($rowData->invoice_status == 3){
                    $dpAmount = 0;
                }
                   
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
                    <a href="#" id="approve|inv-prediksi-forms|<?php echo $rowData->detailId; ?>" role="button" title="Approve"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
            
            <?php 
                if($statusId == 0){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $statusText;
                    echo "</td>";
                }else if($statusId == 3){
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $statusText;
                    echo "</td>";
                }else if($statusId == 1){
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $statusText;
                    echo "</td>";
                }
            ?>
            <td><?php echo $rowData->generate_code_detai; ?></td>
            <td><?php echo $rowData->shipment_code; ?></td>
            <td><?php echo $rowData->cost_name; ?></td>
            <td><?php echo $rowData->general_vendor_name; ?></td> 
            <td><?php echo number_format($invMax, 2, ".", ","); ?></td>
            <td><?php echo number_format($invMin, 2, ".", ","); ?></td>
            <td><?php echo number_format($invQty, 2, ".", ","); ?></td>
            <td><?php echo number_format($invPrice, 2, ".", ","); ?></td>
            <td><?php echo number_format($totalPrediksi, 2, ".", ","); ?></td>
            <td><?php echo number_format($dpAmount, 2, ".", ","); ?></td>
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
