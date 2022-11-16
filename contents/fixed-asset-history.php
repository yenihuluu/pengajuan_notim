<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$fixedAssetId = "";

if(isset($_POST['fixedAssetId']) && $_POST['fixedAssetId'] != '') {
    $fixedAssetId = $_POST['fixedAssetId'];
}

$sql = "SELECT tfa.CodeFa,tfa.AssetCode,tfa.AssetType,tfa.DepreDate,tfa.AmountAquisition,tfa.AmountDepre,tfa.BalanceDepre FROM trx_fixedasset tfa 
LEFT JOIN fixed_asset fa ON fa.`AssetCode`=tfa.assetcode where fa.fixed_asset_id = $fixedAssetId ORDER BY IDFA DESC";
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
        $("#tabTable").tablesorter({
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
                        
//                filter_functions : {
//                    5: true,
//                    6: true
//                },
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
        
        $('#tabTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#modalErrorMsg").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_vh_bank',
                                    vhBankId: menu[2]
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
                                        $('#vh-bank-data').load('tabs/vh-bank-data.php', {vendorHandlingId: $('input[id="generalVendorHandlingId"]').val()});
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
</script>

<div width="100%" style="overflow-x: auto; overflow-y: visible;">
    <table class="tablesorter" id="tabTable" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Code FA</th>
                <th>Asset Code</th>
                <th>Asset Type</th>
				<th>DepreDate</th>
				<th>Amount Aquisition</th>
				<th>Amount Depre</th>
				<th>Balance Depre</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($rowData = $resultData->fetch_object()) {
                ?>
            <tr>                
                <td><?php echo $rowData->CodeFa; ?></td>
                <td><?php echo $rowData->AssetCode; ?></td>
				<td><?php echo $rowData->AssetType; ?></td>
				<td><?php echo $rowData->DepreDate; ?></td>
				<td style="text-align: right"><?php echo number_format($rowData->AmountAquisition, 0, ".", ","); ?></td>
				<td style="text-align: right"><?php echo number_format($rowData->AmountDepre, 0, ".", ","); ?></td>
				<td style="text-align: right"><?php echo number_format($rowData->BalanceDepre, 0, ".", ","); ?></td>
            </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

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
<div class="row-fluid">
       <div class="span12 lightblue">           
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>