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

$sql = "SELECT pp.*, 
CASE WHEN pp.payment_method = '1' THEN 'Payment' 
	WHEN pp.payment_method = '2' THEN 'Down Payment' ELSE NULL END AS Payment, 
CASE WHEN pp.payment_type = 2 THEN 'OUT' ELSE 'IN' END AS tipe, 
        CASE WHEN pp.vendor_id != 0 THEN ven.vendor_name 
            WHEN pp.freight_id != 0 THEN fr.freight_supplier 
            WHEN pp.vendor_handling_id != 0 THEN vh.vendor_handling_name 
            WHEN pp.labor_id != 0 THEN l.labor_name 
	    WHEN pp.customer_id != 0 THEN cus.customer_name 
	ELSE '' END AS vendorName, 
		
        CASE WHEN pp.payment_for = 0 THEN 'PKS Kontrak' 
            WHEN pp.payment_for = 1 THEN 'Local Sales' 
            WHEN pp.payment_for = 2 THEN 'Freight Cost' 
            WHEN pp.payment_for = 9 THEN 'Handling Cost' 
            WHEN pp.payment_for = 3 THEN 'Unloading Cost' ELSE NULL END AS payment_For, 
            sp.stockpile_name AS stockpileName, us.user_name AS entryby, 
        CASE WHEN pp.status = 0 THEN 'PENGAJUAN' 
             WHEN pp.status = 5 THEN 'REJECTED' 
             WHEN pp.status = 1 THEN 'RECEIVED' 
             WHEN pp.status = 2 THEN 'CANCELED' ELSE 'PAYMENT' END AS status1, 
        CASE WHEN pp.urgent_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS urgentType,
        CASE WHEN pp.urgent_payment_type = 1 THEN DATE_FORMAT(urgent_payment_date, '%d-%m-%Y') ELSE '-' END AS urgentDate, rp.remarks AS remaksReject1
       FROM pengajuan_payment_sales_oa pp 
        LEFT JOIN vendor ven ON ven.vendor_id = pp.vendor_id 
        LEFT JOIN vendor_bank vb ON vb.v_bank_id = pp.vendor_bank_id 
        LEFT JOIN freight_local_sales fr ON fr.freight_id = pp.freight_id 
        LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id 
        LEFT JOIN labor l ON l.labor_id = pp.labor_id 
        LEFT JOIN freight_bank fb ON fb.f_bank_id = pp.vendor_bank_id 
        LEFT JOIN labor_bank lb ON lb.l_bank_id = pp.vendor_bank_id 
        LEFT JOIN vendor_handling_bank vhb ON vhb.vh_bank_id = pp.vendor_bank_id 
        LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id 
      
        LEFT JOIN USER us ON us.user_id = pp.user
	LEFT JOIN reject_ppayment rp ON rp.idPP = pp.idPP
	LEFT JOIN customer cus ON cus.customer_id = pp.customer_id
        WHERE 1=1 AND us.user_id = {$_SESSION['userId']} ORDER BY idPP DESC, pp.status DESC";
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

       $('#addNew').click(function (e) {
            e.preventDefault();
            loadContent($('#addNew').attr('href'));
        });
		
		  $('#addNew1').click(function (e) {
            e.preventDefault();
            loadContent($('#addNew1').attr('href'));
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
            if (menu[0] == 'print') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/print-pengajuan-local-sales.php', {idpp: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
                
            }else if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/pengajuan-payment-sales.php', {idPP: menu[2], direct: 0}, iAmACallbackFunction2);

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
                            data: { action: 'delete_pengajuan_payment_sales_oa',
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
                                        $('#dataContent').load('contents/pengajuan-payment-sales.php', {}, iAmACallbackFunction2);
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

    // $("#pengajuanDataForm").validate({
    //         submitHandler: function(form) {
    //             $.ajax({
    //                 url: './reports/pengajuan-payment-xls.php',
    //                 method: 'POST',
    //                 data: $("#pengajuanDataForm").serialize(),
    //                 success: function(data) {
    //                     var returnVal = data.split('|');
    //                 }
    //             });
    //         }
    //     });

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }
    
</script>


<!--forms/add-request.php [UBAH]  --> 

<form class="form-horizontal" method="post" action="reports/pengajuan-payment-salesOA-xls.php" id="pengajuanDataForm">
    <div class="row-fluid">
        <a href="#addNew|pengajuan-payment-sales" id="addNew" class = "btn" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 0px;"/> Add pengajuan</a>
       		
		<button class="btn btn-success">Submit</button>   
    </div>


    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 100px;">Action</th>
               
                <th width="50px">
                    <div style="text-align: center">
                    <label><b>Checklist</b></label>
                        <input type="checkbox" onClick="toggle(this)" />
                    </div>
                </th>
                <th>No pengajuan</th>
				<th>Status Pengajuan</th>
               
                <th>Vendor</th>
                <th>Payment Method</th>
                <th>Payment For</th>
                <th>PPN</th>
				 <th>PPH</th>
                <th>Amount</th>
                <th>Entry Date</th>
                <th>Entry By</th>
                <th>Keterangan</th>
				<th>Keterangan Batal</th>
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
                    <a href="#" id="print|pengajuan-payment-sales|<?php echo $rowData->idPP; ?>" role="button" title="Print"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php //if ($rowData->status1 == 'REJECTED' || $rowData-> email_date == '') { ?>
                       <!--  <a href="#" id="edit|pengajuan-payment-sales|<?php echo $rowData->idPP; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>--> 
                    <?php
                    //}
                        if ($rowData->status == 0 && $rowData-> email_date == '') {
                            //if($allowDelete) {
                    ?>
                            <a href="#" id="delete|pengajuan-payment-sales|<?php echo $rowData->idPP; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
                           // }
                        }
                    ?>
                    </div>
                </td>

                <td>
                    <?php if ($rowData->email_date == '') { ?>
                        <div style="text-align: center">
                            <input type="checkbox" name="checks[]" value="<?php echo $rowData->idPP; ?>" />
                        </div>
                    <?php } ?>
					

                </td>
                <td><?php echo $rowData->idPP; ?></td>
				<?php 
                if($rowData->status1 == 'PENGAJUAN'){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }else if ($rowData->status1 == 'REJECTED') {
                    echo "<td style='font_weight: bold; color: orange;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }else if ($rowData->status1 == 'CANCELED') {
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }
                else if ($rowData->status1 == 'RECEIVED') {
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }else{
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }
                ?>
               
                <td><?php echo $rowData->vendorName; ?></td>			
                <td><?php echo $rowData->Payment; ?></td>
                <td><?php echo $rowData->payment_For; ?></td>
                <td><?php echo number_format($rowData->total_ppn_amount, 3, ".", ","); ?></td>
                <td><?php echo number_format($rowData->total_pph_amount, 3, ".", ","); ?></td>
                <td><?php echo number_format($rowData->grand_total, 3, ".", ","); ?></td>
                <td><?php echo $rowData->entry_date; ?></td>
                <td><?php echo $rowData->entryby; ?></td>
                
                <td><?php echo $rowData->remarks; ?></td>
				<td><?php echo $rowData->remaksReject1; ?></td>
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
</form>

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