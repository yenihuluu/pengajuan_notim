<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$sql = "SELECT a.*, con.po_no, con.contract_no, con.price, cur.currency_code, v.vendor_name AS vendor_full, c.quantity AS quantity2,
CASE WHEN con.contract_type = 'P' THEN 'PKS' ELSE 'Curah' END AS contract_type2, d.stockpile_name
FROM contract_adjustment a
LEFT JOIN contract con ON a.contract_id = con.contract_id
LEFT JOIN currency cur ON cur.currency_id = con.currency_id
LEFT JOIN vendor v ON v.vendor_id = con.vendor_id
LEFT JOIN stockpile_contract c ON con.contract_id = c.contract_id
LEFT JOIN stockpile d ON d.stockpile_id = c.stockpile_id
GROUP BY c.contract_id, a.adj_id
ORDER BY a.adj_id DESC LIMIT 2000
        ";
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
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();
                
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                
                $('#loading').css('visibility','visible');
                
                $('#dataContent').load('forms/adjustment.php', {adj_id: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } else if (menu[0] == 'view') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addAdjustmentModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addAdjustmentModalForm').load('forms/adjustment-account.php', {adjId: menu[2], contractId: menu[3]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            }
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<a href="#addNew|adjustment" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Add Adjustment</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Adjustment No.</th>
            <th>PO No.</th>
			<th>Stockpile</th>
            <th>Vendor</th>
            <th>Contract No.</th>
            <th>Currency</th>
            <th>Price/KG</th>
            <th>Quantity</th>
            <th>Adjustment</th>
			<th>Notes</th>
			<th>Journal</th>
            
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

                    <?php ?>	
                   <a href="#" id="edit|adjustment|<?php echo $rowData->adj_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
				   <?php ?>
				   
					
                    
                </div>
            </td>
            <td><?php echo $rowData->adj_no; ?></td>
            <td><?php echo $rowData->po_no; ?></td>
			<td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->vendor_full; ?></td>
            <td><?php echo $rowData->contract_no; ?></td>
            <td><?php echo $rowData->currency_code; ?></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->price, 2, ".", ","); ?></div></td>
            <td><div style="text-align: right;"><?php echo number_format($rowData->quantity2, 2, ".", ","); ?></div></td>
           	<td><div style="text-align: right;"><?php echo number_format($rowData->adjustment, 2, ".", ","); ?></div></td>
			<td><?php echo $rowData->adjustment_notes; ?></td>
			<td><a href="#" id="view|adjustment|<?php echo $rowData->adj_id; ?>|<?php echo $rowData->contract_id; ?>" role="button"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /> View Journal</a></td>
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
<div id="addAdjustmentModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="adjustmentModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
        <form id="adjustmentForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeAdjustmentModal">×</button>
                <h3 id="addAdjustmentModalLabel">Journal Account</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
           <input type="hidden" name="modalContractId" id="modalContractId" value="<?php echo $rowData->contract_id; ?>" />
            <!--<input type="hidden" name="action" id="action" value="user_stockpile_data" />-->
            <div class="modal-body" id="addAdjustmentModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeAdjustmentModal">Close</button>
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