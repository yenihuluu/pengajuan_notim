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
            $allowDelete = true;
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

$sql = "SELECT a.invoice_status, l.logbook_id, CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_id
ELSE NULL END AS invoiceId, 
CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_no
   
ELSE NULL END AS invoiceNo,
CASE WHEN l.type_pengajuan = 1 THEN
    DATE_FORMAT(a.entry_date,'%Y-%m-%d') 
    
ELSE NULL END AS invoiceDate, 
CASE WHEN l.type_pengajuan = 1 THEN a.amount
     
ELSE NULL END AS amountPengajuan, 
CASE WHEN l.type_pengajuan = 1 THEN a.entry_date
    
ELSE NULL END AS entryDate,
CASE WHEN l.type_pengajuan = 1 THEN 
    CASE WHEN a.vendor_id <> 0 THEN 'Curah'
        WHEN a.freightId <> 0 THEN 'OA'
        WHEN a.laborId <> 0 THEN 'OB'
        WHEN a.vendorHandlingId <> 0 THEN 'Handling'
        ELSE '' END 
     ELSE NULL END
AS kategori,
CASE WHEN l.type_pengajuan = 1 THEN
    CASE WHEN a.vendor_id <> 0 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
        WHEN a.freightId <> 0 THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freightId)
        WHEN a.laborId <> 0 THEN (SELECT labor_name FROM labor WHERE labor_id = a.laborId)
        WHEN a.vendorHandlingId <> 0 THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendorHandlingId)
		WHEN a.customer_id <> 0 THEN (SELECT customer_name FROM customer WHERE customer_id = a.customer_id)
        ELSE '' END
ELSE NULL END  AS vendor,
CASE WHEN l.type_pengajuan = 1 THEN b.remarks
ELSE NULL END AS remarks1, 
CASE WHEN l.type_pengajuan = 1 THEN (SELECT user_name FROM USER WHERE user_id = a.entry_by)
 ELSE NULL END AS userName,
 CASE WHEN l.type_pengajuan = 1 THEN
    CASE WHEN a.invoice_status = 2 THEN 'Invoice Returned'
    WHEN a.status_payment = 1 AND p.payment_status = 1 THEN 'Payment Returned'
    WHEN a.status_payment = 2 THEN 'Payment Returned'
    WHEN a.status_payment = 1 THEN 'Paid' ELSE 'On Process' END
ELSE '' END AS status1,a.status_payment, p.payment_no, p.payment_type,
(SELECT bank_code FROM bank WHERE bank_id = p.bank_id) AS bank_code, 
(SELECT bank_type FROM bank WHERE bank_id = p.bank_id) AS bank_type,
(SELECT currency_code FROM currency WHERE currency_id = p.currency_id) AS pcur_currency_code,
CASE WHEN p.payment_location = 0 THEN 'HOF'
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = p.payment_location) END AS payment_location, p.payment_id 
FROM logbook_new l
LEFT JOIN invoice_sales a ON a.idPP = l.ppayment_sales_id
LEFT JOIN pengajuan_payment_sales b ON b.idPP = a.idPP
LEFT JOIN USER c ON c.user_id = a.entry_by 
LEFT JOIN payment p ON p.invoice_sales_id = a.inv_notim_id
WHERE (a.invoice_status != 0)  
ORDER BY b.idPP DESC";
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
                    1: true,
                    6: true,
                    7: true
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

       $('#addNew').click(function (e) {
            e.preventDefault();
            loadContent($('#addNew').attr('href'));
        });
		
		//  $('#importPayment').click(function(e){
        //     e.preventDefault();
            
        //     $('#importModal').modal('show');
        //     $('#importModalForm').load('forms/payment-import.php');
        // });
        
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
                
                $('#dataContent').load('forms/payment-notim-sales.php', {logbookId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'print') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/search-payment-sales.php', {paymentId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            }else if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data-processing-payment.php',
                            method: 'POST',
                            data: { action: 'delete_pengajuan_payment',
                                idPP: menu[2]
                            },
                            success: function(data){
                                var returnVal = data.split('|');
                                if(parseInt(returnVal[3])!=0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                    if(returnVal[1] == 'OK') {
                                        $('#dataContent').load('contents/pengajuan-payment-contents.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
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
                                                             style="margin-bottom: 5px;"/> Add payment</a>
 -->
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Status</th>
            <th>Payment No</th>
			<th>Invoice No</th>
            <th>Invoice Date</th>
           
            <th>Vendor</th>
			<th>Amount</th>
			<th>Remarks</th>
            <th>Entry By</th>
            <th>Entry Date</th>
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
                <?php if(($rowData->status_payment == 0 || $rowData->status_payment == 2) && $rowData->invoice_status == 1){?>
                    <a href="#" id="edit|payment-forms|<?php echo $rowData->logbook_id; ?>" role="button" title="Payment"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php }else if ($rowData->status_payment != 0){?>
					<a href="#" id="print|payment-forms|<?php echo $rowData->payment_id; ?>" role="button" title="View"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
					<?php }?>
                </div>
            </td>
            <?php if($rowData->status1 == 'Payment Returned' || $rowData->status1 == 'Invoice Returned'){ 
                   echo "<td style='font_weight: bold; color: red;'>"; 
                   echo $rowData->status1;
                   echo "</td>";
            } else if($rowData->status1 == 'Paid'){ 
                echo "<td style='font_weight: bold; color: green;'>"; 
                echo $rowData->status1;
                echo "</td>";
            }else {
                echo "<td style='font_weight: bold; color: blue;'>"; 
                echo $rowData->status1;
                echo "</td>";
            }?>
            <td>
                <?php 
                $voucherCode = $rowData->payment_location .'/'. $rowData->bank_code .'/'. $rowData->pcur_currency_code;
                
                if($rowData->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($rowData->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($rowData->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }
                
                if($rowData->bank_type != 3) {
                    if($rowData->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }
                
                echo $voucherCode .' # '. $rowData->payment_no; 
                ?>
            </td>
			<td><?php echo $rowData->invoiceNo; ?></td>			
            <td><?php echo $rowData->invoiceDate; ?></td>
            
            <td><?php echo $rowData->vendor; ?></td>
            <td><?php echo number_format($rowData->amountPengajuan, 2, ".", ","); ?></td>
            
			<td><?php echo $rowData->remarks1; ?></td>
            <td><?php echo $rowData->userName; ?></td>
            <td><?php echo $rowData->entryDate; ?></td>
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

<div id="importModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="importModalLabel">Import Wizard <label id="approveDesc" /></h3>
    </div>
    <div class="alert fade in alert-error" id="modalErrorMsg4" style="display:none;">
        Error Message
    </div>
    <div class="modal-body" id="importModalForm" style="max-height: 450px;">
    </div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>-->
    </div>

</div>