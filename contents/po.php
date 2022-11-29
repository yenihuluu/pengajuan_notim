<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$allowViewJurnal = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 25) {
            $allowViewJurnal = true;
        }
    }
}

$sql = "SELECT ph.idpo_hdr,ph.no_po ,gv.general_vendor_name, ph.no_penawaran,DATE_FORMAT(ph.tanggal, '%d %b %Y') as tanggal,ph.memo,ph.grandtotal,s.stockpile_name,sp.status, ph.status as status_id,
ph.totalppn, ph.totalpph, ph.totalall
FROM po_hdr ph
left join general_vendor gv on gv.general_vendor_id=ph.general_vendor_id
left join stockpile s on s.stockpile_id = ph.stockpile_id
left join status_po sp on sp.idstatus_po = ph.status
order by ph.idpo_hdr desc";


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
                
                $('#dataContent').load('forms/print-po.php', {POId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
                
            } else if (menu[0] == 'jurnal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addJurnalModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addJurnalModalForm').load('forms/invoice-account.php', {POId: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }else if (menu[0] == 'edit') {
                 $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/po.php', {POId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');
				 
            	//e.preventDefault();
//            	alert($('#addNew').attr('href'));
            	//loadContent($('#addNew').attr('href'));
            }
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<a href="#addNew|po" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add PO</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
    <thead>
        <tr>
			<th style="width: 100px;">Action</th>
			<th>Stockpile</th>
            <th>Nomor PO</th>
			<th>Tanggal</th>
			<th>Vendor</th>
			<th>No Penawaran</th>
			<th>DPP</th>
			<th>PPN</th>
			<th>PPH</th>
			<th>Total</th>
            <th>Status</th>
			</tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                
				$voucherCode = '';
				/*
			if($rowData->payment_no != '') {
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
                  }
				  */
        ?>
        <tr>
            <td>
                <div style="text-align: center;">
					<?php if($rowData->status_id !=4)
						{					
					?>
					
                    <a href="#" id="print|po|<?php echo $rowData->no_po; ?>" role="button" title="Print"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
					<?php
						} if($rowData->status_id !=4){
					?>
                    	<a href="#" id="edit|po|<?php echo $rowData->no_po; ?>" role="button" title="edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
					<?php }?>
                </div>
            </td>
			<td><?php echo $rowData->stockpile_name; ?></td>
			<td><?php echo $rowData->no_po; ?></td>
			<td><?php echo $rowData->tanggal; ?></td>
            <td><?php echo $rowData->general_vendor_name; ?></td>
            <td><?php echo $rowData->no_penawaran; ?></td>
			<td style="text-align: right"><?php echo number_format($rowData->grandtotal, 2, ".", ","); ?></td>
			<td style="text-align: right"><?php echo number_format($rowData->totalppn, 2, ".", ","); ?></td>
			<td style="text-align: right"><?php echo number_format($rowData->totalpph, 2, ".", ","); ?></td>
			<td style="text-align: right"><?php echo number_format($rowData->totalall, 2, ".", ","); ?></td>
            <?php if ($rowData->status_id == 0) { ?>
                <td style="color: blue;"><?php echo $rowData->status; ?></td>
            <?php } else if ($rowData->status_id == 1) { ?>
                <td style="color: #0e90d2;"><?php echo $rowData->status; ?></td>
            <?php } else if ($rowData->status_id == 2) { ?>
                <td style="color: green;"><?php echo $rowData->status; ?></td>
            <?php } else if ($rowData->status_id == 3 || $rowData->status_id == 4) { ?>
                <td style="color: red;"><?php echo $rowData->status; ?></td>
            <?php } else if ($rowData->status_id == 5) { ?>
                <td style="color: yellowgreen;"><?php echo $rowData->status; ?></td>
            <?php } else { ?>
                <td><?php echo $rowData->status; ?></td>
            <?php } ?>
            
         
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
           <input type="hidden" name="modalPOId" id="modalPOId" value="<?php echo $rowData->po_id; ?>" />
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