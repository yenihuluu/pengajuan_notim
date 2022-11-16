<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connectiongen
require_once PATH_INCLUDE.DS.'db_init.php';

$allowDelete = false;
$allowViewJurnal = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 25) {
            $allowViewJurnal = true;
        }elseif($row->module_id == 18) {
            $allowDelete = true;
        }
    }
}

$sql = "SELECT a.sa_id, a.sa_no, b.`nik`, b.`general_vendor_name`,c.`level`, d.`stockpile_name` AS origin2, e.stockpile_name AS destination2, a.invoice_status,
a.`date_from`, a.`date_to`, f.`nik` AS nik_andal, f.`full_name`, f.`date_from` AS from_andal, f.`date_to` AS to_andal, a.approval_status, a.print_status, a.total_amount, DATE_FORMAT(a.upload_date,'%d %M %Y') AS upload_date
FROM perdin_adv_settle a
LEFT JOIN general_vendor b ON a.`id_user` = b.`general_vendor_id`
LEFT JOIN perdin_level c ON c.`level_id` = b.`level_id`
LEFT JOIN stockpile d ON d.`stockpile_id` = a.`origin`
LEFT JOIN stockpile e ON e.`stockpile_id` = a.`destination`
LEFT JOIN perdin_andal f ON f.`sa_id` = a.`sa_id`
WHERE  a.`sa_method` = 1  AND a.approval_status != 2 AND upload_status = 1 ORDER BY a.sa_id DESC, a.approval_status ASC";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowImport = true;

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
        
        $('#importContract').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/contract-import.php');
        });
        
        $('#addNew').click(function(e){
            
            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
		
		$('#importDataAndal').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/import-andal.php');
        });
		
		$('#sinkronDataAndal').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=sinkron_data_andal',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('contents/perdin-approval.php', {}, iAmACallbackFunction2);

                        } 
                    }
                }
            });
        });
        
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
                
				if(menu[3] == 2){
                $('#dataContent').load('forms/print-perdin-adv.php', {sa_id: menu[2]}, iAmACallbackFunction2);
				}else{
				$('#dataContent').load('forms/print-perdin-settle.php', {sa_id: menu[2]}, iAmACallbackFunction2);	
				}
                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
                
            } else if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/perdin-approval.php', {sa_id: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'jurnal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/invoice-account.php', {invoiceId: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }else if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_perdin_adv',
                                    sa_id: menu[2]
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
                                        $('#dataContent').load('contents/perdin-approval.php', {}, iAmACallbackFunction2);
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

<!--<a href="#addNew|perdin-adv_settle" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Advance/Settlement Perdin</a>-->
<!--<a href="#" id="importDataAndal"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Import Data Andal</a>
<button class="btn btn-warning" id="sinkronDataAndal">Sinkron Data Andal</button>-->
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Settlement/Reimbursement No.</th>
			<th>NIK</th>
			<th>Name</th>
            <th>Submitted Date</th>
			<th>Total Amount</th>
            <th>From</th>
			<th>To</th>
			<th>Date From</th>
            <th>Date To</th>
            <!--<th>NIK (Andal)</th>
            <th>Nama (Andal)</th>
			<th>Date From (Andal)</th>
            <th>Date To (Andal)</th>-->
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
				
				
				<?php 
			if($rowData->approval_status != 1 && $rowData->print_status == 1){
				?>
                    <a href="#" id="edit|perdin|<?php echo $rowData->sa_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
			<?php }?>
                </div>
            </td>
			
            <td><?php echo $rowData->sa_no; ?></td>
			<td><?php echo $rowData->nik; ?></td>
			<td><?php echo $rowData->general_vendor_name; ?></td>
            <td><?php echo $rowData->upload_date; ?></td>
			<td><div style="text-align: right;"><?php echo number_format($rowData->total_amount, 2, ".", ","); ?></div></td>
            <td><?php echo $rowData->origin2; ?></td>
            <td><?php echo $rowData->destination2; ?></td>
			<td><?php echo $rowData->date_from; ?></td>
			<td><?php echo $rowData->date_to; ?></td>
            <!--<td><?php //echo $rowData->nik_andal; ?></td>
            <td><?php //echo $rowData->full_name; ?></td>
            <td><?php //echo $rowData->from_andal; ?></td>
			<td><?php //echo $rowData->to_andal; ?></td>-->
			 <?php 
			if($rowData->approval_status == 1 && $rowData->invoice_status == 1){
			echo "<td style='font_weight: bold; color: green;'>"; 
			echo "INVOICED";
			echo "</td>";
			}else if($rowData->approval_status == 1){
			echo "<td style='font_weight: bold; color: green;'>"; 
			echo "Approved";
			echo "</td>";
			} else if($rowData->approval_status == 3){
			echo "<td style='font_weight: bold; color: blue;'>"; 
			echo "Verified";
			echo "</td>";
			} else{
			echo "<td style='font_weight: bold; color: red;'>"; 
			echo "Submission";
			echo "</td>";
			}
			?>
			
        </tr>
        <?php
            
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
<div id="addJurnalModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="jurnalModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="jurnalForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">×</button>
                <h3 id="addJurnalModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalInvoiceId" id="modalInvoiceId" value="<?php echo $rowData->invoice_id; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addJurnalModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
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