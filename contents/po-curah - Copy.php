<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT DISTINCT po.*, v.vendor_name, s.stockpile_name, cur.currency_code, c.`payment_status`,
CASE
WHEN p.`payment_method`=2 THEN 'DP'
WHEN p.`payment_method`=1 THEN 'FULL'
ELSE 'UNPAID' END AS payment,
DATE_FORMAT(pc.entry_date, '%d %b %Y %H:%m:%s') AS entry_date,
CASE
WHEN po.`final_status`=1 THEN 'OK'
WHEN po.`final_status`=2 THEN 'SPB'
WHEN po.`final_status`=3 THEN 'INCOMPLETE'
END AS final_statuss,pc.*,po.price as poprice, po.quantity as poqty
		FROM po_pks po
		LEFT JOIN po_contract poc ON poc.`po_pks_id` = po.`po_pks_id`
		LEFT JOIN contract c ON c.`contract_id` = poc.`contract_id`
		LEFT JOIN vendor v ON v.vendor_id = po.vendor_id
		LEFT JOIN stockpile s ON s.stockpile_id = po.stockpile_id
		LEFT JOIN currency cur ON cur.currency_id = po.currency_id
		LEFT JOIN `stockpile_contract` sc ON sc.`contract_id`=c.`contract_id`
		LEFT JOIN payment p ON p.`stockpile_contract_id`=sc.`stockpile_contract_id`
		left join purchasing pc on pc.purchasing_id = po.purchasing_id
WHERE pc.type = 2
		GROUP BY po.po_pks_id
		order by pc.entry_date desc limit 1000";
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

       /* $('#importContract').click(function(e){
            e.preventDefault();

            $('#importModal').modal('show');
            $('#importModalForm').load('forms/contract-import.php');
        });
        */

				$('#approveContract').click(function(e){
					e.preventDefault();
		            $.ajax({
		                url: './data_processing.php',
		                method: 'POST',
		                data: $("#docForm").serialize(),
		                success: function(data) {
		                    var returnVal = data.split('|');

		                    if (parseInt(returnVal[3]) != 0)	//if no errors
		                    {
		                        alertify.set({ labels: {
		                            ok     : "OK"
		                        } });
		                        alertify.alert(returnVal[2]);

		                        if (returnVal[1] == 'OK') {
		                            $('#dataContent').load('views/purchasing.php', {}, iAmACallbackFunction2);
									 $('#addDocModal').modal('hide');
		                        }
		                    }
		                }
		            });
		        });

        $('#addNew').click(function(e){

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
		
			$('#addNew2').click(function(e){

		            e.preventDefault();
		//            alert($('#addNew').attr('href'));
		            loadContent($('#addNew2').attr('href'));
		        });


        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
						var purchasing_id = menu[2];
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({ message: '<h4>Please wait...</h4>' });

                $('#loading').css('visibility','visible');

                $('#dataContent').load('forms/po-curah.php', {po_pks_id: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif

            } else if (menu[0] == 'view') {
							/*
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({ message: '<h4>Please wait...</h4>' });

                $('#loading').css('visibility','visible');

                $('#dataContent').load('forms/purchasing-view2.php', {purchasingId: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
								*/
								e.preventDefault();

								$("#modalErrorMsg").hide();
								$('#addDocModal').modal('show');
		//            alert($('#addNew').attr('href'));
								$('#addDocModalForm').load('forms/purchasing-view2.php', {purchasingId: menu[2]}, iAmACallbackFunction2);
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
                            data: { action: 'delete_po_pks',
                                    po_pks_id: menu[2]
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
                                        $('#dataContent').load('contents/po-curah.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }else if (menu[0] == 'reject') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to reject this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'reject_po_pks',
                                    po_pks_id: menu[2]
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
                                        $('#dataContent').load('contents/po-curah.php', {}, iAmACallbackFunction2);
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

<a href="#addNew|po-curah" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Contract</a>
<!-- <a href="#addNew|po-pks-mutasi" id="addNew2" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Contract Mutasi</a> -->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>

            <th>Vendor</th>
            <th>Contract No.</th>
			<th>Notes</th>
            <th>Stockpile</th>
            <th>Currency</th>
            <th>Price/KG</th>
            <th>Quantity</th>
            <th>Status</th>
			<th>Payment</th>
			<th>Submit Date</th>
			<th>PO Status</th>
			<th>P.ID</th>

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
                    <?php if($rowData->payment_status == 0 && $rowData->reject_status == 0){ ?>
                    <a href="#" id="edit|po-curah|<?php echo $rowData->po_pks_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                    <!--<a href="#" id="delete|po-pks|<?php //echo $rowData->po_pks_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>-->
					<a href="#" id="reject|po-curah|<?php echo $rowData->po_pks_id; ?>" role="button" title="Delete">REJECT</a>
				   <?php }?>
					 <?php if($rowData->final_status != 1 && !empty($rowData->import2)){ ?>

									 <a href="#" id="view|po-pks|<?php echo $rowData->purchasing_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>

							<?php 	} ?>



                </div>
            </td>

            <td><?php echo $rowData->vendor_name; ?></td>
			<td><?php echo $rowData->contract_no; ?></td>
			<td><?php echo $rowData->notes; ?></td>
            <td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->currency_code; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->poprice, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->poqty, 2, ".", ","); ?></div></td>
            <?php
			if($rowData->po_status == 1){
			echo "<td style='font_weight: bold; color: green;'>";
			echo "CLOSED";
			echo "</td>";
			}elseif($rowData->reject_status == 1){
			echo "<td style='font_weight: bold; color: red;'>";
			echo "REJECTED";
			echo "</td>";
			}else{
			echo "<td style='font_weight: bold; color: red;'>";
			echo "OPEN";
			echo "</td>";
			}
			?>
          <td><?php echo $rowData->payment; ?></td>
					<td><?php echo $rowData->entry_date; ?></td>
					<td><?php echo $rowData->final_statuss; ?></td>
					<td><?php echo $rowData->purchasing_id; ?></td>
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
<div id="addDocModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="docModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="docForm" method="post" style="height:600px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDocModal">×</button>
                <h3 id="addDocModalLabel">Documents</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>

            <div class="modal-body" id="addDocModalForm">

            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDocModal">Close</button>
                <button class="btn btn-warning" id="approveContract">Approve</button>
            </div>
        </form>
    </div>
