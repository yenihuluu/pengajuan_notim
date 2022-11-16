<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$headerCostingID = $_POST['codeCosting_id'];

$sql = "SELECT mc.mcd_id AS costingId, sp.stockpile_name, gv.general_vendor_name,  CONCAT(a.account_no, ' - ', a.account_name) AS accountName,
        CASE WHEN mc.max_type = 1 THEN 'Lowest'
             WHEN mc.max_type = 2 THEN 'Highest' ELSE '-' END AS maxType,
        CASE WHEN mc.min_type = 1 THEN 'Lowest' 
            WHEN mc.min_type = 2 THEN 'Highest' ELSE '-' END AS minType,
        CASE WHEN mc.price_type = 1 THEN 'Var' ELSE 'Fix' END AS priceType,
        CASE WHEN mc.qty_type = 1 THEN 'Vessel' 
            WHEN mc.qty_type = 2 THEN 'Tongkang' 
            WHEN mc.qty_type = 3 THEN 'Timbangan Darat'
            WHEN mc.qty_type = 4 THEN 'Volume' ELSE 'Others' END AS qtyType,
        u.uom_type, mc.priceMT, 
        CASE WHEN mc.active = 0 THEN 'Non Active' ELSE 'Active' END AS active, 
        (SELECT user_name FROM USER WHERE user_id = mc.entry_by) AS user1, mc.entry_date ,
        CASE WHEN mc.currency = 2 THEN '$'
            WHEN mc.currency = 3 THEN 'S$'
            WHEN mc.currency = 4 THEN '¥' ELSE 'Rp.' END AS curr
        FROM mst_costing_detail mc
        LEFT JOIN stockpile sp ON sp.stockpile_id = mc.stockpile_id
        LEFT JOIN general_vendor gv ON gv.general_vendor_id = mc.general_vendor_id
        LEFT JOIN uom u ON u.idUOM = mc.uom 
        LEFT JOIN account a ON a.account_id = mc.account_id
        WHERE header_costing_id = {$headerCostingID}
        ORDER BY mc.mcd_id DESC ";
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
                    4: true
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
                $("#modalErrorMsg").hide();
                $('#addDetailCostingModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailCostingModalForm').load('forms/detail-costing-modal.php', {costingId: menu[2], codeCosting_id: $('input[id="generalCodeCostingId"]').val()});                
            } 
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt; margin-top: 15px;">
    <thead>
        <tr>
            <th style="width: 100px;">Action</th>
            <th>Stockpile (Remarks)</th>
            <th>General Vendor</th>
            <th>Account Name</th>
            <th>Price Type</th>
            <th>Max Type</th>
            <th>Min Type</th>
            <th>Qty Type</th>
            <th>UOM</th>
            <th>Price</th>
            <th>User</th>
            <th>Datetime</th>
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
                    <a href="#" id="edit|job-costing|<?php echo $rowData->costingId; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
            <td><?php echo $rowData->stockpile_name; ?></td>
            <td><?php echo $rowData->general_vendor_name; ?></td>
            <td><?php echo $rowData->accountName; ?></td> 
            <td><?php echo $rowData->priceType; ?></td>
            <td><?php echo $rowData->maxType; ?></td>
            <td><?php echo $rowData->minType; ?></td>
            <td><?php echo $rowData->qtyType; ?></td>
            <td><?php echo $rowData->uom_type; ?></td>
            <td><?php echo $rowData->curr . '' .number_format($rowData->priceMT, 3, ".", ","); ?></td>
            <td><?php echo $rowData->user1; ?></td>
            <td><?php echo $rowData->entry_date; ?></td>
            <td><?php echo $rowData->active; ?></td>
        </tr>
        <?php
            }
        } else {
        ?>
        <tr>
            <td colspan="4">
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
