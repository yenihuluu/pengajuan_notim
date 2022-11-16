<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

//$allowImport = false;
//$allowViewJurnal = false;
//$whereLimit = "";
/*$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
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
}*/

$whereLimit = "";
$sql1 = "SELECT stockpile_id FROM user WHERE user_id = {$_SESSION['userId']}";
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);

if($result1->num_rows > 0) {
    while($row1 = $result1->fetch_object()) {
        if($row1->stockpile_id != 10) {		//HO
            $whereLimit = 'AND tu.stockpile_id = '.$row1->stockpile_id.'';
		}
		
		
    }
}

//$allowImport = true;

$sql = "SELECT tu.*, s.stockpile_name, u.user_name FROM transaction_upload tu 
LEFT JOIN stockpile s ON s.stockpile_id = tu.stockpile_id
LEFT JOIN user u ON u.user_id = tu.upload_by
WHERE 1=1 AND tu.status IN (0,3){$whereLimit}
ORDER BY slip_id DESC ";
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
        
        $('#importTimbangan').click(function(e){
            e.preventDefault();
            
            $('#importModal').modal('show');
            $('#importModalForm').load('forms/timbangan-import.php');
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
                //$("#dataSearch").fadeOut();
                //$("#dataContent").fadeOut();
                
                //$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                //$('#loading').css('visibility','visible');
                
                //$('#dataContent').load('forms/search-timbangan.php', {slipId: menu[2]}, iAmACallbackFunction2);

                //$('#loading').css('visibility','hidden');	//and hide the rotating gif
				
				$('#editModal').modal('show');
				$('#editModalForm').load('forms/timbangan-do.php', {slipId: menu[2]}, iAmACallbackFunction2);
                
            } else if (menu[0] == 'print') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/print-timbangan.php', {slipId: menu[2]}, iAmACallbackFunction2);

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
                            data: { action: 'delete_transaction',
                                    slipId: menu[2]
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
//if($allowImport) {
?>
<a href="#" id="importTimbangan"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Import Data Timbangan</a>
<?php
//}
//$date_now = date('Y-m');

?>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Stockpile</th>
            <th>Slip No.</th>
			<th>Tanggal Masuk</th>
            <th>Tanggal Keluar</th>
			<th>No Kendaraan</th>
            <th>Nama Customer</th>
            <th>No DO</th>
            <th>Supir</th>
            <th>Timbang 1</th>
			<th>Timbang 2</th>
			<th>Brutto</th>
			<th>Netto</th>
			<th>User Input</th>
			<th>User Upload</th>
			<th>Upload Date</th>
		</tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                $boolDelete = false;
				//$edit_date = $rowData->edit_date;
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
					<?php
					
					if($rowData->status == 0){
					?>
					<a href="#" id="edit|timbangan|<?php echo $rowData->slip_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <?php
					}
					?>
					
					<!--<a href="#" id="print|timbangan|<?php //echo $rowData->slip_id; ?>" role="button" title="Print"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>-->
					
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
			
            <td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->no_slip; ?></td>
            <td><?php echo $rowData->tgl_masuk; ?></td>
			<td><?php echo $rowData->tgl_keluar; ?></td>
            <td><?php echo $rowData->no_kendaraan; ?></td>
            <td><?php echo $rowData->nama_customer; ?></td>
			<td><?php echo $rowData->no_do; ?></td>
			<td><?php echo $rowData->supir; ?></td>
            <td><?php echo number_format($rowData->timbang1, 0, ".", ","); ?></td>
            <td><?php echo number_format($rowData->timbang2, 0, ".", ","); ?></td>
			<td><?php echo number_format($rowData->bruto, 0, ".", ","); ?></td>
			<td><?php echo number_format($rowData->netto, 0, ".", ","); ?></td>
            <td><?php echo $rowData->user_input; ?></td>
            <td><?php echo $rowData->user_name; ?></td>
			<td><?php echo $rowData->upload_date; ?></td>
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
		//echo $sql;
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
        <h3 id="importModalLabel">Import Data Timbangan <label id="approveDesc" /></h3>
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
<div id="editModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">

    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>-->
        <h3 id="editModalLabel">Edit No DO <label id="approveDesc" /></h3>
    </div>
    <div class="alert fade in alert-error" id="modalErrorMsg4" style="display:none;">
        Error Message
    </div>
    <div class="modal-body" id="editModalForm" style="max-height: 450px;">
    </div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>-->
    </div>

</div>