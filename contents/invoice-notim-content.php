<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowDelete = false;



$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 18) {
            $allowDelete = false;
        }
    }
}

$allowImport = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 17) {
            $allowImport = true;
        }
    }
}

$allowImport = true;

$sql = "SELECT CASE WHEN pp.payment_method = '1' THEN 'Claim Payment'
                    WHEN pp.payment_method = '2' THEN 'Down Payment'
                    WHEN pp.payment_method = '3' THEN 'Payment'  ELSE NULL END AS Payment, 
        CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE NULL END AS tipe, 
        CASE WHEN pp.vendor_id <> 0 THEN ven.vendor_name 
            WHEN pp.freight_id <> 0 THEN fr.freight_supplier 
            WHEN pp.vendor_handling_id <> 0 THEN vh.vendor_handling_name 
            WHEN pp.labor_id <> 0 THEN l.labor_name ELSE NULL END AS vendorName, 
        CASE WHEN pp.vendor_bank_id IS NOT NULL THEN vb.bank_name 
            WHEN pp.vendor_bank_id IS NOT NULL THEN fb.bank_name 
            WHEN pp.vendor_bank_id IS NOT NULL THEN lb.bank_name 
            WHEN pp.vendor_bank_id IS NOT NULL THEN vhb.bank_name ELSE NULL END AS bankName, 
        ino.inv_notim_no,
        CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
            WHEN pp.payment_for = 1 THEN 'PKS Curah' 
            WHEN pp.payment_for = 2 THEN 'Freight Cost' 
            WHEN pp.payment_for = 9 THEN 'Handling Cost' 
            WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For, 
        sp.stockpile_name AS stockpileName, us.user_name AS entryby,
        CASE WHEN  (pp.status = 1 AND ino.invoice_status = 1 AND  ino.status_payment = 0 )
                -- (pp.status = 1 AND ino.invoice_status = 1 AND  ino.status_payment = 0 AND pp.grand_total > 0) 
               -- OR (pp.status = 3 AND ino.invoice_status = 1 AND pp.grand_total > 0 AND (ino.status_payment = 0 || ino.status_payment = 2)) THEN 'APPROVED'
               OR (pp.status = 3 AND ino.invoice_status = 1 AND (ino.status_payment = 0 || ino.status_payment = 2)) THEN 'APPROVED'
            WHEN ino.status_payment = 1 AND pp.status = 3 AND pp.grand_total > 0 THEN 'PAID' 
            WHEN ino.status_payment = 1 AND pp.status = 3 AND pp.grand_total = 0 THEN 'Settlement' 
            WHEN pp.status = 2 THEN 'CANCELED' 
            WHEN ino.invoice_status = '4' THEN 'RETURN PAYMENT'
			 WHEN (ino.status_payment = '2' AND ino.invoice_status = 2) OR (ino.status_payment = 4 AND ino.invoice_status = 2) OR  (ino.status_payment = 0 AND ino.invoice_status = 2) THEN 'RETURN INVOICE'
            WHEN pp.status = 4 THEN 'REJECTED' ELSE 'PENGAJUAN' END AS statuspengajuan,
        pp.status as status1,
        
        CASE WHEN (ino.status_payment = '2' || ino.invoice_status = 4) THEN ino.return_remarks else pp.reject_remarks  end as remark1, 
        sp.stockpile_name,
        ino.inv_notim_id as invId,
		ino.status_payment,
        ino.invoice_status,
		ino.payment_id,
        CASE WHEN pp.urgent_payment_type = 1 then 'URGENT' else 'NORMAL' END AS urgentType,
        DATE_FORMAT(urgent_payment_date, '%d-%m-%Y') AS urgentDate,
        pp.* 
        FROM pengajuan_payment pp LEFT JOIN vendor ven ON ven.vendor_id = pp.vendor_id 
        LEFT JOIN vendor_bank vb ON vb.v_bank_id = pp.vendor_bank_id 
        LEFT JOIN freight fr ON fr.freight_id = pp.freight_id 
        LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id 
        LEFT JOIN labor l ON l.labor_id = pp.labor_id 
        LEFT JOIN freight_bank fb ON fb.f_bank_id = pp.vendor_bank_id 
        LEFT JOIN labor_bank lb ON lb.l_bank_id = pp.vendor_bank_id 
        LEFT JOIN vendor_handling_bank vhb ON vhb.vh_bank_id = pp.vendor_bank_id 
        LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id
        LEFT JOIN USER us ON us.user_id = pp.user 
        LEFT JOIN invoice_notim ino ON ino.idpp = pp.idpp
		where pp.email_date is not null AND pp.status != 4
        ORDER BY pp.idPP DESC";
//echo $sql;
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
                        
               // filter_functions : {
                 //   1: true,
                   // 6: true,
                    //7: true
                //},
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

    //    $('#addNew').click(function (e) {
    //         e.preventDefault();
    //         loadContent($('#addNew').attr('href'));
    //     });
		
        
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
                

                $('#dataContent').load('forms/invoice-notim-forms.php', {idPP: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                
                $('#dataContent').load('forms/invoice-notim-forms.php', {invId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'jurnal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/view-journal-invoiceNotim.php', {invId: menu[2], status: menu[3], }, iAmACallbackFunction2);	//and hide the rotating gif
                
            }
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>
<!-- <?php
if($allowImport) {
?>
<a href="#" id="importPayment"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Import Payment</a>
<?php
}
?> -->

<!--forms/add-request.php [UBAH]--> 
 <!-- <a href="#addNew|payment-forms" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" 
                                                             style="margin-bottom: 5px;"/> Add payment</a> -->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
			<th>Journal</th>
            <th>No Pengajuan</th>
			<th>Status Pengajuan</th>
            <th>Status</th>
            <th>Request Payment Date</th>
            <th>Payment Method</th>
            <th>Stockpile</th>
			<th>Oringinal Invoice No</th>
            <th>Invoice No</th>
            <th>Vendor</th>
            <th>Payment For</th>
            <th>Dpp</th>
            <th>PPn</th>
            <th>PPh</th>
			<th>Total Amount</th>
            <th>Entry Date</th>
            <th>Entry By</th>
			<th>Remarks</th>
            <th>Return/Reject Remarks</th>

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
                    <?php if(($rowData->status1 == 1 && $rowData->invoice_status == 1) || ($rowData->status1 == 3 && $rowData->invoice_status == 1)){ ?>
                        <a href="#" id="edit|previewa-forms|<?php echo $rowData->invId; ?>" role="button" title="Preview"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> 
                    <?php } ?>
                    <!-- <a href="<?php echo $rowData->file; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> -->
                    <?php if($rowData->status1 == 0 || ($rowData->status1 == 3 && $rowData->invoice_status == 2) || ($rowData->status1 == 1 && $rowData->invoice_status == 2)){ ?>
                        <a href="#" id="approve|payment-forms|<?php echo $rowData->idPP; ?>" role="button" title="Approve P"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
                    }else if(($rowData->status1 == 1 && ($rowData->invoice_status == 1 || $rowData->invoice_status == 4)) || ($rowData->status1 == 3 && ($rowData->invoice_status == 2 || $rowData->invoice_status == 4)) ){
                    ?>
                        <a href="#" id="edit|payment-forms|<?php echo $rowData->invId; ?>" role="button" title="Edit Invoice"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
                        }
                    ?>

                </div>
            </td>
			<td><a href="#" id="jurnal|journal-invoice|<?php echo $rowData->invId; ?>|<?php echo $rowData->invoice_status ?>" role="button"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /> View Journal</a></td>
            <td><?php echo $rowData->idPP; ?></td>
			<?php if($rowData->statuspengajuan == 'PENGAJUAN'){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->statuspengajuan;
                    echo "</td>";
                }else if($rowData->statuspengajuan == 'CANCELED' || $rowData->statuspengajuan == 'RETURN INVOICE' || $rowData->statuspengajuan == 'RETURN PAYMENT'){
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->statuspengajuan;
                    echo "</td>";
                }else if($rowData->statuspengajuan == 'REJECTED'){
                    echo "<td style='font_weight: bold; color: orange;'>"; 
                    echo $rowData->statuspengajuan;
                    echo "</td>";
                }else if($rowData->statuspengajuan == 'PAID' || $rowData->statuspengajuan == 'Settlement'){
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->statuspengajuan;
                    echo "</td>";
                }else if($rowData->statuspengajuan == 'APPROVED') {
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->statuspengajuan;
                    echo "</td>";
                }
            ?>
            <?php 
                    if($rowData->urgentType == 'URGENT'){
                        echo "<td style='font_weight: bold; color: red;'>"; 
                        echo $rowData->urgentType;
                        echo "</td>";
                    }else{
                        echo "<td style='font_weight: bold; color: blac;'>"; 
                        echo $rowData->urgentType;
                        echo "</td>";
                    }
            ?>
            
            <td><?php echo $rowData->urgentDate; ?></td>
            <td><?php echo $rowData->Payment; ?></td>
            <td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->invoice_no; ?></td>
            <td><?php echo $rowData->inv_notim_no; ?></td>
			<td><?php echo $rowData->vendorName; ?></td>			
            <td><?php echo $rowData->payment_For; ?></td>
            <td><?php echo number_format($rowData->total_dpp, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->total_ppn_amount, 2, ".", ","); ?></td>
            <td><?php echo number_format($rowData->total_pph_amount, 2, ".", ","); ?></td>
			<td><?php echo number_format($rowData->grand_total, 2, ".", ","); ?></td>
            <td><?php echo $rowData->entry_date; ?></td>
            <td><?php echo $rowData->entryby; ?></td>
           <td><?php echo $rowData->remarks; ?></td>
           <td><?php echo $rowData->remark1; ?></td>

        </tr>
        <?php
            
            }
        } else {
        ?>
        <tr>
            <td colspan="10">
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

<div id="addJurnalModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="jurnalModalLabel" aria-hidden="true">
        <form id="jurnalForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">×</button>
                <h3 id="addJurnalModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalTransactionId" id="modalTransactionId" value="<?php echo $rowData->invId; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addJurnalModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
</div>