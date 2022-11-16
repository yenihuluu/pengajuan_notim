<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$purchasingId = '';
$vendorName = '';

if(isset($_POST['purchasingId']) && $_POST['purchasingId'] != '') {
    $purchasingId = $_POST['purchasingId'];
}

if(isset($_POST['vendorName']) && $_POST['vendorName'] != '') {
    $vendorName = $_POST['vendorName'];
}

$sql = "CALL SearchPurchasing ('{$purchasingId}','{$vendorName}')";
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
                    //4: true
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

        $('#addNew').click(function(e){

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });

		$('#importContract').click(function(e){
            e.preventDefault();

            $('#importModal').modal('show');
            $('#importModalForm').load('forms/purchase-import.php');
        });

		$('#rejectContract').click(function(e){
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
						
		$('#exportExcel').click(function (e) {
            $("#printIcomplete").submit(); // Submit the for
            <?php $action = 'action="print_incomplete.php"'; ?>

        });

        $('#exportExcel2').click(function (e) {
            $("#printIcomplete").submit(); // Submit the for
            <?php $action = 'action="print-purchasing.php"'; ?>
        });

        $('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
			var fileId = menu[2];
            if (menu[0] == 'view') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addDocModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDocModalForm').load('forms/purchasing-view.php', {purchasingId: menu[2]}, iAmACallbackFunction2);

            } else if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({ message: '<h4>Please wait...</h4>' });

                $('#loading').css('visibility','visible');

                $('#dataContent').load('forms/purchasing.php', {purchaseId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif

            }
						else if (menu[0] == 'view2') {
							e.preventDefault();

							$("#modalErrorMsg").hide();
							$('#addDocModal').modal('show');
	//            alert($('#addNew').attr('href'));
							$('#addDocModalForm').load('forms/purchasing-view2.php', {purchasingId: menu[2]}, iAmACallbackFunction2);

            }
        });

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }

</script>
    <button id="exportExcel" class="btn btn-success">Print Incomplete Status</button>
    <button id="exportExcel2" class="btn btn-success">Download xls</button>

</br>
Yellow = Incomplete need to follow up kacab, Red=reimport, admin need approve.
<form method="POST" <?php echo $action; ?> id="printIcomplete">
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 100px;">
                    <div style="text-align: center">
                        <input type="checkbox" onClick="toggle(this)" />
                    </div>
                </th>

                <th style="width: 100px;">Action</th>
                <th>Number</th>
                <th>Stockpile</th>
                <th>Contract Type</th> 
				<th>Type</th> 
                <th>Vendor Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>PPN</th>
                <th>Freight</th>
                <th>Entry Date</th>
                <th>Payment Type</th>
                <th>Request payment date</th>
                <th>admin Date</th>
                <th>Status</th>
                <th>Approve</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($resultData !== false && $resultData->num_rows > 0) {
                while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td>
                    <div style="text-align: center">
                        <input type="checkbox" name="checks[]" value="<?php echo $rowData->purchasing_id; ?>" />
                    </div>
                </td>
                <td>
                    <div style="text-align: center;">
                        <!--<a href="#" id="edit|purchasing|<?php //echo $rowData->purchasing_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>-->

                        <a href="#" id="view|purchasing|<?php echo $rowData->purchasing_id; ?>" role="button" title="Attachment"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                                            <?php if(!empty($rowData->import2)){ ?>

                                            <a href="#" id="view2|purchasing|<?php echo $rowData->purchasing_id; ?>" role="button" title="New"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>

                                                <?php 	} ?>

                                    </div>
                </td>
                <td style="text-align: center"><?php echo $rowData->purchasing_id; ?></td>
                <?php if($rowData->final_status != 1 && !empty($rowData->import2)){ ?>

                                <td><font color="red"><?php echo $rowData->stockpile_name; ?></font></td>
                            <?php 	} elseif($rowData->final_status != 1 && empty($rowData->import2)){?>
                                        <td><font color="yellow"><?php echo $rowData->stockpile_name; ?></font></td>
                                <?php 	} else {?>
                                    <td><?php echo $rowData->stockpile_name; ?></td>
                                <?php }	 ?>
                <td><?php echo $rowData->contract_type2; ?></td>
				<td><?php echo $rowData->contractType; ?></td>
                <td><?php echo $rowData->vendor_name; ?></td>
                <td><div style="text-align: right;"><?php echo number_format($rowData->price, 2, ".", ","); ?></div></td>
                <td><div style="text-align: right;"><?php echo number_format($rowData->quantity, 2, ".", ","); ?></div></td>
                <td><div style="text-align: right;"><?php echo number_format($rowData->totalAmount, 2, ".", ","); ?></div></td>
                <td><?php echo $rowData->ppn; ?></td>
                <td><?php echo $rowData->freight; ?></td>
                <td><?php echo $rowData->entry_date; ?></td>
                <?php
                    if($rowData->payment_type == 0){
                ?>
                        <td><?php echo "Normal"; ?></td>
                <?php
                    }else if($rowData->payment_type == 1){
                ?>
                        <td><font color="red"><?php echo "Urgent"; ?></td>
                <?php
                    }
                ?>
                <td><?php echo $rowData->reqPaymentDate; ?></td>
                <td><?php echo $rowData->admin_input; ?></td>
                <?php
                if($rowData->status == 0 && $rowData->admin_input != ''){
                echo "<td style='font_weight: bold; color: green;'>";
                echo "OK";
                echo "</td>";
                }else if($rowData->status == 0 && $rowData->admin_input == ''){
                echo "<td style='font_weight: bold; color: blue;'>";
                echo "On Check";
                echo "</td>";
                }else if($rowData->status == 1){
                echo "<td style='font_weight: bold; color: red;'>";
                echo "Reject";
                echo "</td>";
                }
                ?>
                <td><?php echo $rowData->approve; ?></td>
            </tr>
            <?php
                }
            } else {
            ?>
            <tr>
                <td colspan="3">
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

								<button class="btn btn-primary" id="approveContract">Approve</button>

								<button class="btn btn-warning" id="rejectContract">Reject</button>


            </div>
        </form>
    </div>
