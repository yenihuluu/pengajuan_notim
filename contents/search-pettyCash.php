<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowDelete = false;
//$whereProperty = '';
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

$whereProperty = "AND p.entry_by = {$_SESSION['userId']} AND (p.payment_cash_id IS NOT NULL OR p.freight_id IS NOT NULL OR p.labor_id IS NOT NULL OR a.account_type = 7)";

$sql = "SELECT stockpile_id
            FROM user_stockpile   
            WHERE user_id = {$_SESSION['userId']} ORDER BY stockpile_id DESC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	
	if ($result->num_rows > 0) {
        while($row = $result->fetch_object()) {
			 	//$stockpileId = $row->stockpile_id;
		if($row->stockpile_id == 10){
			$whereProperty = "AND (p.payment_cash_id IS NOT NULL OR a.account_type = 7 OR p.freight_id IS NOT NULL OR p.labor_id IS NOT NULL)";
		}
		//else{
			
		//}
	}
}

$sql = "SELECT p.*, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date2,
            DATE_FORMAT(p.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2, u.user_name,
            CASE WHEN p.payment_type = 1 THEN 'IN' ELSE 'OUT' END AS payment_type2,
            CASE WHEN p.payment_method = 1 THEN 'Payment' ELSE 'Down Payment' END AS payment_method2,
            CONCAT(a.account_no, ' - ', a.account_name) AS account_full,
            CONCAT(b.bank_name, ' ', bcur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full,
            CASE WHEN p.stockpile_contract_id IS NOT NULL THEN 'PKS Kontrak'
            WHEN p.vendor_id IS NOT NULL THEN 'PKS Curah'
            WHEN p.sales_id IS NOT NULL THEN 'Sales'
            WHEN p.freight_id IS NOT NULL THEN 'Freight Cost'
            WHEN p.labor_id IS NOT NULL THEN 'Unloading Cost'
            WHEN p.shipment_id IS NOT NULL OR p.general_vendor_id IS NOT NULL THEN 'Loading/Umum/HO'
            ELSE 'Cash Payment' END AS payment_for,
            b.bank_code, b.bank_type, pcur.currency_code AS pcur_currency_code, 
            CASE WHEN p.payment_location = 0 THEN 'HOF'
            ELSE s3.stockpile_code END AS payment_location2,
			CASE WHEN sc.stockpile_contract_id IS NOT NULL THEN 
            (SELECT con.contract_no FROM contract con LEFT JOIN stockpile_contract sc1 ON con.contract_id = sc1.contract_id WHERE sc1.stockpile_contract_id = sc.stockpile_contract_id)
            ELSE '' END AS contract_no,
            CASE WHEN sc.stockpile_contract_id IS NOT NULL THEN 
            (SELECT con.po_no FROM contract con LEFT JOIN stockpile_contract sc1 ON con.contract_id = sc1.contract_id WHERE sc1.stockpile_contract_id = sc.stockpile_contract_id)
            ELSE '' END AS po_no,
			CASE WHEN p.payment_status = 0 THEN 'SUCCESS'
            ELSE 'RETURNED' END AS payment_status, ur.user_name AS user_name2,
CASE WHEN p.invoice_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN invoice_detail id ON id.general_vendor_id = gv.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id WHERE i.invoice_id = p.invoice_id LIMIT 1)
     WHEN p.sales_id IS NOT NULL THEN cust.customer_name
     WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
     WHEN p.labor_id IS NOT NULL THEN l.labor_name
     WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
	 WHEN p.payment_cash_id IS NOT NULL THEN (SELECT gv.general_vendor_name FROM general_vendor gv LEFT JOIN payment_cash pc ON pc.general_vendor_id = gv.general_vendor_id WHERE pc.payment_id = p.payment_id LIMIT 1)
     ELSE a.account_name END AS supplier_name
        FROM payment p
        LEFT JOIN account a
            ON a.account_id = p.account_id
        LEFT JOIN bank b
            ON b.bank_id = p.bank_id
        LEFT JOIN currency bcur
            ON bcur.currency_id = b.currency_id
        LEFT JOIN currency pcur
            ON pcur.currency_id = p.currency_id
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = p.stockpile_contract_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = sc.stockpile_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor v
            ON v.vendor_id = p.vendor_id
        LEFT JOIN sales sl
            ON sl.sales_id = p.sales_id
        LEFT JOIN freight f
            ON f.freight_id = p.freight_id
        LEFT JOIN labor l
            ON l.labor_id = p.labor_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = p.general_vendor_id
        LEFT JOIN shipment sh
            ON sh.shipment_id = p.shipment_id
        LEFT JOIN sales sl2
            ON sl2.sales_id = sh.sales_id
        LEFT JOIN stockpile s2
            ON s2.stockpile_id = sl2.stockpile_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl2.customer_id
        LEFT JOIN user u
            ON u.user_id = p.entry_by
        LEFT JOIN stockpile s3
            ON s3.stockpile_id = p.payment_location
		LEFT JOIN user ur
			ON ur.user_id = p.edit_by
        
       WHERE 1=1 
	   	{$whereProperty}
        AND p.company_id = {$_SESSION['companyId']} 
        ORDER BY p.payment_id DESC limit 3000";
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
		
		 $('#importPayment').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/payment-import.php');
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
                
                $('#dataContent').load('forms/search-pettyCash.php', {paymentId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_payment_cash',
                                    paymentId: menu[2]
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
                                        $('#dataContent').load('contents/search-pettyCash.php', {}, iAmACallbackFunction2);
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


<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Type</th>
            <th>Voucher No.</th>
            <th>Payment Date</th>
            <th>Vendor Name</th>
            <th>Payment Method</th>
            <th>COA</th>
            <th>From Bank</th>
            <th>Payment For</th>
			<th>Remarks</th>
			<th>Status</th>
            <th>Entry By</th>
            <th>Entry Date</th>
			<th>Return By</th>
            <th>Return Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
		 //echo $sql;
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
                    <a href="#" id="edit|payment|<?php echo $rowData->payment_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
                    if($allowImport) {
                    ?>
                   <!-- <a href="#" id="delete|payment|<?php //echo $rowData->payment_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>!-->
                    <?php
                    }
                    ?>
                </div>
            </td>
            <td><?php echo $rowData->payment_type2; ?></td>
            <td>
                <?php 
                $voucherCode = $rowData->payment_location2 .'/'. $rowData->bank_code .'/'. $rowData->pcur_currency_code;
                
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
            <td><?php echo $rowData->payment_date2; ?></td>
            <td><?php echo $rowData->supplier_name; ?></td>
            <td><?php echo $rowData->payment_method2; ?></td>
            <td><?php echo $rowData->account_full; ?></td>
            <td><?php echo $rowData->bank_full; ?></td>
            <td><?php echo $rowData->payment_for; ?></td>
			<td><?php echo $rowData->remarks; ?></td>
			<?php 
			if($rowData->payment_status == 'SUCCESS'){
			echo "<td style='font_weight: bold; color: green;'>"; 
			echo $rowData->payment_status;
			echo "</td>";
			}else{
			echo "<td style='font_weight: bold; color: red;'>"; 
			echo $rowData->payment_status;
			echo "</td>";
			}
			?>
            <td><?php echo $rowData->user_name; ?></td>
            <td><?php echo $rowData->entry_date2; ?></td>
			<td><?php echo $rowData->user_name2; ?></td>
            <td><?php echo $rowData->edit_date; ?></td>
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