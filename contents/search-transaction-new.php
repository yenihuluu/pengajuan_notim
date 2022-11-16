<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowImport = false;
$allowViewJurnal = false;
$allowEditNotim = false;
$whereLimit = "";
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 17) {
            $allowImport = true;
        }elseif($row->module_id == 21) {
            $allowEditNotim = true;
        }elseif($row->module_id == 25) {
            $allowViewJurnal = true;
        }
		
		
    }
}

$notim = '';
$periodFrom = 'NULL';
$periodTo = 'NULL';
if(isset($_POST['notim']) && $_POST['notim'] != '') {
    $notim = $_POST['notim'];
}
if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '') {
    $searchPeriodFrom = $_POST['periodFrom'];
	$periodFrom  = "STR_TO_DATE('{$searchPeriodFrom}','%d/%m/%Y')";
}
if(isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $searchPeriodTo = $_POST['periodTo'];
	$periodTo = "STR_TO_DATE('{$searchPeriodTo}','%d/%m/%Y')";
}
$allowImport = true;

$sql = "CALL SearchNotimNew({$_SESSION['userId']},{$_SESSION['companyId']}, {$periodFrom}, {$periodTo}, '{$notim}');";
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
                    1: true
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
        
        $('#importTransaction').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/transaction-import.php');
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
                
                $('#dataContent').load('forms/search-transaction-new.php', {transactionId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'print') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/print-transaction-new.php', {transactionId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'jurnal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/transaction-account.php', {transactionId: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }else if (menu[0] == 'reference') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addReferenceModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addReferenceModalForm').load('forms/transaction-reference.php', {slipRetur: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
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
                            data: { action: 'delete_transaction',
                                    transactionId: menu[2]
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
                                        $('#dataContent').load('contents/search-transaction.php', {}, iAmACallbackFunction2);
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

<?php
if($allowImport) {
?>
<a href="#" id="importTransaction"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Import Data Transaction</a>
<?php
}
$date_now = date('Y-m');

?>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
			<?php if($allowViewJurnal) {?>
			<th>Journal</th>
			<?php } ?>
            <th>Type</th>
            <th>Slip No.</th>
			<th>Slip Reference No.</th>
            <th>Transaction Date</th>
			<th>Vehicle No</th>
            <th>PO No./Shipment Code</th>
            <th>Weight (KG)</th>
            <th>Entry By</th>
            <th>Entry Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                $boolDelete = false;
				$edit_date = $rowData->edit_date;
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
					<?php
					
					if($allowEditNotim && $date_now == $edit_date && $rowData->transaction_type == 1 && $rowData->delivery_status == 0 && $rowData->payment_id == '' && $rowData->fc_payment_id == '' && $rowData->uc_payment_id == '' && $rowData->slip_retur == '' && $rowData->notim_status != 1){
					?>	
                    <a href="#" id="edit|transaction|<?php echo $rowData->transaction_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
					}elseif($allowEditNotim && $rowData->transaction_type == 1 && $rowData->delivery_status == 0 && $rowData->payment_id == '' && $rowData->fc_payment_id == '' && $rowData->uc_payment_id == '' &&  $rowData->slip_retur == '' && $rowData->notim_status != 1){
					?>	
                    <a href="#" id="edit|transaction|<?php echo $rowData->transaction_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
					}elseif($allowEditNotim && $rowData->transaction_type == 2 && $rowData->slip_retur == '' && $rowData->notim_status != 1){
					?>
					<a href="#" id="edit|transaction|<?php echo $rowData->transaction_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
					}
					?>
					
					<a href="#" id="print|transaction|<?php echo $rowData->transaction_id; ?>" role="button" title="Print"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
					
                    <?php
                   /* if($rowData->transaction_type == 1 && $rowData->delivery_status == 0 && $rowData->payment_id == '' && $row->fc_payment_id == '' && $row->uc_payment_id == '') {
                        $boolDelete = true;
                    } else if($rowData->transaction_type == 2 && $rowData->sh_payment_id == '') {
                        $boolDelete = false;
                    }
                    
                    if($boolDelete && $date_now == $edit_date) {
                    ?>
                   <!-- <a href="#" id="delete|transaction|<?php //echo $rowData->transaction_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>-->
                    <?php
                    }*/
                    ?>
                </div>
            </td>
			<?php if($allowViewJurnal) {?>
			<td><a href="#" id="jurnal|transaction|<?php echo $rowData->transaction_id; ?>" role="button"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /> View Journal</a></td>
			<?php } ?>
            <td><?php echo $rowData->transaction_type2; ?></td>
            <td><?php echo $rowData->slip_no; ?></td>
			<td><a href="#" id="reference|transaction|<?php echo $rowData->slip_retur2; ?>" role="button"><?php echo $rowData->slip_retur; ?></a></td>
            <td><?php echo $rowData->unloading_date2; ?></td>
            <td><?php echo $rowData->vehicle_no; ?></td>
            <td><?php echo $rowData->po_no; ?></td>
            <td><?php echo number_format($rowData->quantity, 0, ".", ","); ?></td>
            <!--<td><div style="text-align: right;"><?php //echo number_format((($rowData->freight_price * $rowData->quantity) + $rowData->unloading_price), 0, ",", "."); ?></div></td>-->
            <td><?php echo $rowData->user_name; ?></td>
            <td><?php echo $rowData->entry_date2; ?></td>
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
           <input type="hidden" name="modalTransactionId" id="modalTransactionId" value="<?php echo $rowData->transaction_id; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addJurnalModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeJurnalModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>
<div id="addReferenceModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="refrenceModalLabel" aria-hidden="true" style="width:1000px; height:300px; margin-left:-400px;">
        <form id="referenceForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeReferenceModal">×</button>
                <h3 id="addReferenceModalLabel">Slip Reference</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalSlipRetur" id="modalSlipRetur" value="<?php echo $rowData->slip_retur2; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addReferenceModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeReferenceModal">Close</button>
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