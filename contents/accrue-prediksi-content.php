<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT ap.*, sp.stockpile_name, 
            CASE WHEN ap.shipment_id IS NOT NULL THEN sh.`shipment_code` ELSE '-' END AS shipmentCode, cu.`customer_name`, DATE_FORMAT(ap.PEB_Date, '%d/%m/%Y') AS PEBDate,  
            (SELECT user_name FROM USER WHERE user_id = ap.entry_by) AS user1,
            CASE WHEN 
                (SELECT COUNT(*) FROM accrue_prediction_detail WHERE prediction_id = ap.prediction_id AND STATUS = 0) > 0 THEN 'On Process'
            ELSE 'Invoice' END AS status1,
            (SELECT SUM(qty) FROM accrue_prediction_detail apd 
                LEFT JOIN mst_costing_detail mcd ON mcd.mcd_id = apd.mcd_id WHERE ap.prediction_id = prediction_id AND mcd.qty_type = 2) AS qtyTongkang,
            (SELECT SUM(qty) FROM accrue_prediction_detail apd 
                LEFT JOIN mst_costing_detail mcd ON mcd.mcd_id = apd.mcd_id WHERE ap.prediction_id = prediction_id AND mcd.qty_type = 3) AS qtyTimbangan,
            (SELECT COUNT(*) FROM accrue_prediction_detail apd WHERE ap.prediction_id = prediction_id AND journal_status in (1,2)) AS jmlJurnal,
            (SELECT COUNT(*) FROM accrue_prediction_detail apd WHERE ap.prediction_id = prediction_id AND STATUS = 1) AS jmlInvoice,
            (SELECT COUNT(*) FROM accrue_prediction_detail apd WHERE ap.prediction_id = prediction_id AND journal_status = 0 AND STATUS = 0) AS jmlLainnya
        FROM accrue_prediction ap 
        LEFT JOIN stockpile sp ON sp.stockpile_id = ap.`stockpile_id`
        LEFT JOIN shipment sh ON sh.shipment_id = ap.shipment_id
        LEFT JOIN customer cu ON cu.customer_id = ap.customer_id 
        ORDER BY ap.prediction_id DESC";
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
                
                $('#dataContent').load('forms/accrue-prediksi-forms.php', {prediksiId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } 
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<a href="#addNew|accrue-prediksi-forms" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Prediction</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Generate Code</th>
            <th>Stockpile</th>
            <th>Shipment Code</th>
            <th>Buyer</th>
            <th>PEB Date</th>
            <th>Qty Vessel</th>
            <th>Qty Tongkang</th>
            <th>Qty Timbangan</th>
            <th>Total Invoice </th>
            <th>Total Journal/Cancel</th>
            <th>Total Others</th>
            <th>User</th>
            <th>Datetime</th>
            <th>Status</th>
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
                    <a href="#" id="edit|prediksi|<?php echo $rowData->prediction_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
            <td><?php echo $rowData->prediction_code; ?></td>
            <td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->shipmentCode; ?></td>
            <td><?php echo $rowData->customer_name; ?></td>
            <td><?php echo $rowData->PEBDate; ?></td>
            <td><?php echo number_format($rowData->qty_vessel, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->qtyTongkang, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->qtyTimbangan, 2, ".", ","); ?></td>
            <td><?php echo $rowData->jmlInvoice; ?></td>
            <td><?php echo $rowData->jmlJurnal; ?></td>
            <td><?php echo $rowData->jmlLainnya; ?></td>
            <td><?php echo $rowData->user1; ?></td>
            <td><?php echo $rowData->entry_date; ?></td>
            <?php 
                if($rowData->status1 == 'Invoice'){
                    echo "<td style='font_weight: bold; color: Green;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                } else {
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }
            ?>
            
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
