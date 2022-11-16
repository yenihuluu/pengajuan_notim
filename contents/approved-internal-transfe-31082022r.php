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

$sql = "SELECT sp.stockpile_name as stockpileName, 
               CASE WHEN tf.status = 0 then 'Pengajuan' 
                    WHEN tf.status = 1 then 'In-Process'
                    WHEN tf.status = 2 then 'Rejected'
                else NULL END as status1,
				CASE WHEN tf.request_payment_type = 0 THEN 'NORMAL' ELSE 'URGENT' END AS requestType,
                us.user_name as username,
                (select us1.user_name from user us1 where  us1.user_id = tf.user_HO) as userHO,
            DATE_FORMAT(tf.periode_from, '%d/%m/%Y') AS dateFrom, 
            DATE_FORMAT(tf.periode_to, '%d/%m/%Y') AS dateTo,
			DATE_FORMAT(tf.request_payment_date, '%d/%m/%Y') AS PayDate,
        tf.* FROM pengajuan_internalTF tf
        LEFT JOIN stockpile sp ON sp.stockpile_id = tf.stockpile 
        left join user us on us.user_id = tf.entry_by
        where tf.status is not null
        ORDER BY pengajuan_interalTF_id DESC, tf.status DESC";
//echo $sql;
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
                        
               // filter_functions : {
                 //   1: true,
                   // 6: true,
                    //7: true
                //},
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
                
                $('#dataContent').load('forms/approved-internalTF-forms.php', {internalTF_id: menu[2], direct: 0}, iAmACallbackFunction2);

                $('#loading').css('visibility','hidden');	//and hide the rotating gif
                
            } 
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

    
</script>


<!--forms/add-request.php [UBAH]  --> 

<form class="form-horizontal" method="post" id="pengajuanDataForm">
    </br>

    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
            <tr>
                <th style="width: 100px;">Action</th>
               
                <th>No pengajuan</th>
				<th>Status Pengajuan</th>
                <th>Stockpile</th>
                <th>Periode From</th>
                <th>Periode To</th>
                <th>Amount</th>
				<th>Payment Date</th>
				<th>Request Type</th>
                <th>Entry Date</th>
                <th>Entry By</th>
                <th>Document</th>
                <th>Keterangan</th>
                <th>user HO</th>
				<th>Keterangan Batal</th>
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
                         <a href="#" id="edit|approved-internalTF-forms|<?php echo $rowData->pengajuan_interalTF_id; ?>" role="button" title="Approve"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>                      
                    </div>
                </td>

                <td><?php echo $rowData->pengajuan_interalTF_id; ?></td>
				<?php 
                if($rowData->status1 == 'Pengajuan'){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }else if ($rowData->status1 == 'Rejected') {
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }else if ($rowData->status1 == 'In-Process') {
                    echo "<td style='font_weight: bold; color: green;'>"; 
                    echo $rowData->status1;
                    echo "</td>";
                }
                ?>
                <td><?php echo $rowData->stockpileName; ?></td>
                <td><?php echo $rowData->dateFrom; ?></td>
                <td><?php echo $rowData->dateTo; ?></td>
                <td><?php echo number_format($rowData->amount, 2, ".", ","); ?></td>
				<td><?php echo $rowData->PayDate; ?></td>
				<?php 
                if($rowData->requestType == 'NORMAL'){
                    echo "<td style='font_weight: bold; color: blue;'>"; 
                    echo $rowData->requestType;
                    echo "</td>";
                }else if ($rowData->requestType == 'URGENT') {
                    echo "<td style='font_weight: bold; color: red;'>"; 
                    echo $rowData->requestType;
                    echo "</td>";
                }
				?>
				
                <td><?php echo $rowData->entry_date; ?></td>
                <td><?php echo $rowData->username; ?></td>   
                <td><a href="<?php echo $rowData->file; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> </td>
                <td><?php echo $rowData->remarks; ?></td>      
                <td><?php echo $rowData->userHO; ?></td>      
				<td><?php echo $rowData->remaks_reject; ?></td>
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