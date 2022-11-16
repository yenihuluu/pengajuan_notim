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

/*$sql = "SELECT l.logbook_id, 
        CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_id
            WHEN l.type_pengajuan = 2 THEN pg.invoice_id
        ELSE '-' END AS invoiceId, 
        CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_no
            WHEN l.type_pengajuan = 2 THEN inv.invoice_no
        ELSE '-' END AS invoiceNo,
        CASE WHEN l.type_pengajuan = 1 THEN
            DATE_FORMAT(a.entry_date,'%Y-%m-%d') 
            WHEN l.type_pengajuan = 2 THEN
            DATE_FORMAT(inv.entry_date,'%Y-%m-%d') 
        ELSE NULL END AS invoiceDate, 
        CASE WHEN l.type_pengajuan = 1 THEN a.amount
            WHEN l.type_pengajuan = 2 THEN pg.total_amount
            WHEN l.type_pengajuan = 6 THEN itf.amount
        ELSE NULL END AS amountPengajuan, 
        CASE WHEN l.type_pengajuan = 1 THEN a.entry_date
            WHEN l.type_pengajuan = 2 THEN pg.entry_date
            WHEN l.type_pengajuan = 6 THEN itf.entry_date
        ELSE NULL END AS entryDate,
        CASE WHEN l.type_pengajuan = 1 THEN 
            CASE WHEN a.vendor_id <> 0 THEN 'Curah'
                WHEN a.freightId <> 0 THEN 'OA'
                WHEN a.laborId <> 0 THEN 'OB'
                WHEN a.vendorHandlingId <> 0 THEN 'Handling'
                ELSE '' END 
            WHEN l.type_pengajuan = 2 THEN 'General'
            WHEN l.type_pengajuan = 6 THEN 'Internal Transfer'
            ELSE '' END
        AS kategori,
        CASE WHEN l.type_pengajuan = 1 THEN
            CASE WHEN a.vendor_id <> 0 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
                WHEN a.freightId <> 0 THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freightId)
                WHEN a.laborId <> 0 THEN (SELECT labor_name FROM labor WHERE labor_id = a.laborId)
                WHEN a.vendorHandlingId <> 0 THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendorHandlingId)
                ELSE '' END
            WHEN l.type_pengajuan = 2 THEN pgd.gvName
        ELSE '-' END  AS vendor,
        CASE WHEN l.type_pengajuan = 1 THEN b.remarks
            WHEN l.type_pengajuan = 2 THEN pg.remarks
            WHEN l.type_pengajuan = 6 THEN itf.remarks
        ELSE '-' END AS remarks1, 
        CASE WHEN l.type_pengajuan = 1 THEN a.return_remarks
            WHEN l.type_pengajuan = 2 THEN inv.return_remarks
        ELSE '-' END AS remarks2, 
        CASE WHEN l.type_pengajuan = 1 THEN (SELECT user_name FROM USER WHERE user_id = a.entry_by)
            WHEN l.type_pengajuan = 6 THEN (SELECT user_name FROM USER WHERE user_id = itf.entry_by)
        ELSE (SELECT user_name FROM USER WHERE user_id = pg.entry_by) END AS userName,
        CASE WHEN l.type_pengajuan = 1 THEN CASE WHEN (a.status_payment = 2 OR a.status_payment = 4 )THEN 'Return Payment' ELSE 'On Process' END
            WHEN l.type_pengajuan = 6 THEN  CASE WHEN (itf.status = 2 OR (itf.status = 3 AND l.status1 = 4)) THEN 'Return Payment' ELSE 'On Process' END
            WHEN l.type_pengajuan = 2 THEN  CASE WHEN (inv.payment_status = 2 OR inv.payment_status = 4 )THEN 'Return Payment' ELSE 'On Process' END
        ELSE NULL END AS status1,
        CASE WHEN l.type_pengajuan = 1 THEN CASE WHEN b.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END
            WHEN l.type_pengajuan = 6 THEN CASE WHEN itf.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END
        ELSE  CASE WHEN pg.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END END AS paymentType,
        CASE WHEN l.type_pengajuan = 1 THEN DATE_FORMAT(b.urgent_payment_date,'%Y-%m-%d') 
            WHEN l.type_pengajuan = 2 THEN DATE_FORMAT(pg.request_payment_date,'%Y-%m-%d') 
            WHEN l.type_pengajuan = 6 THEN DATE_FORMAT(itf.request_payment_date,'%Y-%m-%d')
        ELSE NULL END AS reqPayDay
        FROM logbook_new l
        LEFT JOIN invoice_notim a ON a.inv_notim_id = l.inv_notim_id
        LEFT JOIN pengajuan_payment b ON b.idPP = a.idPP
        LEFT JOIN USER c ON c.user_id = a.entry_by 
        LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = l.pgeneral_id
        LEFT JOIN invoice inv ON inv.invoice_id = l.inv_general_id
        LEFT JOIN payment p ON p.payment_id = l.payment_id
        LEFT JOIN (
            SELECT pg.pengajuan_general_id AS pg_id,
                gv.general_vendor_name AS gvName 
            FROM pengajuan_general pg 
                LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = pgd.general_vendor_id
                WHERE pg.status_pengajuan != '2'
            GROUP BY pg.pengajuan_general_id
        )AS pgd ON pgd.pg_id = pg.pengajuan_general_id
        LEFT JOIN pengajuan_internaltf itf ON itf.pengajuan_interalTF_id = l.internalTf_id
        WHERE (a.invoice_status = 1 AND (a.status_payment = 0 OR a.status_payment = 2))
        OR (inv.invoice_status = 1 AND (inv.payment_status = 0 OR inv.payment_status = 2)) 
        OR ((itf.status = 3 AND  l.status1 = 4) 
        OR (itf.status = 0 AND  l.status1 = 0))
        ORDER BY logbook_id DESC";*/
$sql = "SELECT l.logbook_id, 
        CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_id
           -- WHEN l.type_pengajuan = 2 THEN pg.invoice_id
        ELSE '-' END AS invoiceId, 
        CASE WHEN l.type_pengajuan = 1 THEN a.inv_notim_no
          --  WHEN l.type_pengajuan = 2 THEN inv.invoice_no
        ELSE '-' END AS invoiceNo,
        CASE WHEN l.type_pengajuan = 1 THEN
            DATE_FORMAT(a.entry_date,'%Y-%m-%d') 
          --  WHEN l.type_pengajuan = 2 THEN
           -- DATE_FORMAT(inv.entry_date,'%Y-%m-%d') 
        ELSE NULL END AS invoiceDate, 
        CASE WHEN l.type_pengajuan = 1 THEN a.amount
            -- WHEN l.type_pengajuan = 2 THEN pg.total_amount
            WHEN l.type_pengajuan = 6 THEN itf.amount
        ELSE NULL END AS amountPengajuan, 
        CASE WHEN l.type_pengajuan = 1 THEN a.entry_date
           -- WHEN l.type_pengajuan = 2 THEN pg.entry_date
            WHEN l.type_pengajuan = 6 THEN itf.entry_date
        ELSE NULL END AS entryDate,
        CASE WHEN l.type_pengajuan = 1 THEN 
            CASE WHEN a.vendor_id <> 0 THEN 'Curah'
                WHEN a.freightId <> 0 THEN 'OA'
                WHEN a.laborId <> 0 THEN 'OB'
                WHEN a.vendorHandlingId <> 0 THEN 'Handling'
                ELSE '' END 
            WHEN l.type_pengajuan = 2 THEN 'General'
            WHEN l.type_pengajuan = 6 THEN 'Internal Transfer'
            ELSE '' END
        AS kategori,
        CASE WHEN l.type_pengajuan = 1 THEN
            CASE WHEN a.vendor_id <> 0 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = a.vendor_id)
                WHEN a.freightId <> 0 THEN (SELECT freight_supplier FROM freight WHERE freight_id = a.freightId)
                WHEN a.laborId <> 0 THEN (SELECT labor_name FROM labor WHERE labor_id = a.laborId)
                WHEN a.vendorHandlingId <> 0 THEN (SELECT vendor_handling_name FROM vendor_handling WHERE vendor_handling_id = a.vendorHandlingId)
                ELSE '' END
       --     WHEN l.type_pengajuan = 2 THEN pgd.gvName
        ELSE '-' END  AS vendor,
        CASE WHEN l.type_pengajuan = 1 THEN b.remarks
         --   WHEN l.type_pengajuan = 2 THEN pg.remarks
            WHEN l.type_pengajuan = 6 THEN itf.remarks
        ELSE '-' END AS remarks1, 
        CASE WHEN l.type_pengajuan = 1 THEN a.return_remarks
         --   WHEN l.type_pengajuan = 2 THEN inv.return_remarks
        ELSE '-' END AS remarks2, 
        CASE WHEN l.type_pengajuan = 1 THEN (SELECT user_name FROM USER WHERE user_id = a.entry_by)
            WHEN l.type_pengajuan = 6 THEN (SELECT user_name FROM USER WHERE user_id = itf.entry_by)
     --   ELSE (SELECT user_name FROM USER WHERE user_id = pg.entry_by) END AS userName,
          ELSE '-'  END AS userName,
        CASE WHEN l.type_pengajuan = 1 THEN CASE WHEN (a.status_payment = 2 OR a.status_payment = 4 )THEN 'Return Payment' ELSE 'On Process' END
            WHEN l.type_pengajuan = 6 THEN  CASE WHEN (itf.status = 2 OR (itf.status = 3 AND l.status1 = 4)) THEN 'Return Payment' ELSE 'On Process' END
          --  WHEN l.type_pengajuan = 2 THEN  CASE WHEN (inv.payment_status = 2 OR inv.payment_status = 4 )THEN 'Return Payment' ELSE 'On Process' END
        ELSE NULL END AS status1,
        CASE WHEN l.type_pengajuan = 1 THEN CASE WHEN b.urgent_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END
            WHEN l.type_pengajuan = 6 THEN CASE WHEN itf.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END
       -- ELSE  CASE WHEN pg.payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END END AS paymentType,
          ELSE '-' END AS paymentType,
        CASE WHEN l.type_pengajuan = 1 THEN DATE_FORMAT(b.urgent_payment_date,'%Y-%m-%d') 
          --  WHEN l.type_pengajuan = 2 THEN DATE_FORMAT(pg.request_payment_date,'%Y-%m-%d') 
            WHEN l.type_pengajuan = 6 THEN DATE_FORMAT(itf.request_payment_date,'%Y-%m-%d')
        ELSE NULL END AS reqPayDay
        FROM logbook_new l
        LEFT JOIN invoice_notim a ON a.inv_notim_id = l.inv_notim_id
        LEFT JOIN pengajuan_payment b ON b.idPP = a.idPP
        LEFT JOIN USER c ON c.user_id = a.entry_by 
      --  LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = l.pgeneral_id
     --   LEFT JOIN invoice inv ON inv.invoice_id = l.inv_general_id
        LEFT JOIN payment p ON p.payment_id = l.payment_id
    --    LEFT JOIN (
     --       SELECT pg.pengajuan_general_id AS pg_id,
    --            gv.general_vendor_name AS gvName 
     --       FROM pengajuan_general pg 
    --            LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
    --            LEFT JOIN general_vendor gv ON gv.general_vendor_id = pgd.general_vendor_id
    --            WHERE pg.status_pengajuan != '2'
   --         GROUP BY pg.pengajuan_general_id
    --    )AS pgd ON pgd.pg_id = pg.pengajuan_general_id
        LEFT JOIN pengajuan_internaltf itf ON itf.pengajuan_interalTF_id = l.internalTf_id
        WHERE (a.invoice_status = 1 AND (a.status_payment = 0 OR a.status_payment = 2))
    --    OR (inv.invoice_status = 1 AND (inv.payment_status = 0 OR inv.payment_status = 2)) 
        OR ((itf.status = 3 AND  l.status1 = 4) 
        OR (itf.status = 0 AND  l.status1 = 0))
        ORDER BY logbook_id DESC";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//echo $sql;
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
                
                $('#dataContent').load('forms/payment-forms.php', {logbookId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'delete') {
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
            <th>Request Type</th>
            <th>Status PV</th>
			<th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Type</th>
            <th>Vendor</th>
			<th>Amount</th>
			<th>Remarks</th>
            <th>Return remarks</th>
            <th>Request Date</th>
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
                    <a href="#" id="edit|payment-forms|<?php echo $rowData->logbook_id; ?>" role="button" title="Create new PV"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
            <?php if($rowData->paymentType == 'URGENT'){ 
                   echo "<td style='font_weight: bold; color: red;'>"; 
                   echo $rowData->paymentType;
                   echo "</td>";
            }else {
                echo "<td style='font_weight: blue;'>"; 
                echo $rowData->paymentType;
                echo "</td>";
            }
            ?>
            <?php if($rowData->status1 == 'Return Payment'){ 
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->status1;
                   echo "</td>";
            }else {
                echo "<td style='font_weight: bold; color: blue;'>"; 
                echo $rowData->status1;
                echo "</td>";
            }?>
			<td><?php echo $rowData->invoiceNo; ?></td>			
            <td><?php echo $rowData->invoiceDate; ?></td>
            <td><?php echo $rowData->kategori; ?></td>
            <td><?php echo $rowData->vendor; ?></td>
            <td><?php echo number_format($rowData->amountPengajuan, 2, ".", ","); ?></td>
            
			<td><?php echo $rowData->remarks1; ?></td>
            <td><?php echo $rowData->remarks2; ?></td>
            <td><?php echo $rowData->reqPayDay; ?></td>
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