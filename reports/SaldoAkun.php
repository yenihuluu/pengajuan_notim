<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty = '';
$payment_no = '';
$account_no = '';

if(isset($_POST['payment_no']) && $_POST['payment_no'] != '') {
    $payment_no = $_POST['payment_no'];
	$paymentNo = "";
	for ($i = 0; $i < sizeof($payment_no); $i++) {
                        if($paymentNo == '') {
                            $paymentNo .= "'". $payment_no[$i] ."'";
                        } else {
                            $paymentNo .= ','. "'". $payment_no[$i] ."'";
                        }
                    }
	$whereProperty .= "AND payment_no IN ({$paymentNo})";				
					
}
if(isset($_POST['account_no']) && $_POST['account_no'] != '') {
    $account_no = $_POST['account_no'];
	$accountNo = "";
	for ($i = 0; $i < sizeof($account_no); $i++) {
                        if($accountNo == '') {
                            $accountNo .= "'". $account_no[$i] ."'";
                        } else {
                            $accountNo .= ','. "'". $account_no[$i] ."'";
                        }
                    }
	$whereProperty .= "AND account_no IN ({$accountNo})";				
}



$sql = "SELECT * FROM saldo_awal WHERE 1=1 {$whereProperty}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

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
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addSaldoModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addSaldoModalForm').load('forms/SaldoAkun.php', {sa_id: menu[2]}, iAmACallbackFunction2);	//and hide the rotating gif
                
            } 
        });

    });
    
    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
    
</script>
<!--
<form method="post" action="reports/complete-report-xls.php">
    <input type="hidden" id="paymentNo" name="paymentNo" value="<?php echo $paymentNo; ?>" />
    <input type="hidden" id="accountNo" name="accountNo" value="<?php echo $accountNo; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>-->
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
			<th>Action</th>
            <th>Periode</th>
            <th>Account No</th>
            <th>Account Name</th>
            <th>Amount</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo $sql;
        } else {
			while($row = $result->fetch_object()) {
			?>
        <tr>
            <td><div style="text-align: center;"><a href="#" id="edit|SaldoAwal|<?php echo $row->sa_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a></div></td>
            <td><?php echo $row->payment_no; ?></td>
            <td><?php echo $row->account_no; ?></td>
            <td><?php echo $row->account_name; ?></td>
            <td><?php echo number_format($row->amount, 2, ".", ","); ?></td>
           
        </tr>
                <?php
                
            }
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
