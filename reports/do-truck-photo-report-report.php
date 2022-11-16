<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty1 = '';
$whereProperty2 = '';
$whereProperty3 = '';

$periodFrom = '';
$periodTo = '';

$whereCustomer = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];
    $whereProperty1 .= " >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}


$sql = "SELECT s.stockpile_name, COUNT(1) as count, MAX(ts.transaction_date) as latest
        FROM transaction_timbangan tt
            LEFT JOIN transaction_timbangan_sp ts ON tt.slip = ts.slip
            LEFT JOIN stockpile s ON s.stockpile_id = tt.stockpile_id
                WHERE tt.transaction_date
                {$whereProperty1}
                AND (ts.pic_truck IS NULL OR ts.pic IS NULL)
                    GROUP BY s.stockpile_id";
// echo $sql;
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
                    7: true
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

        $('#printCustomerActivity').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#CustomerActivity").printThis();
            //$("#transactionContainer").hide();
        });
		var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/do-truck-photo-report.php', {
                    periodFrom: $('input[id="periodFrom"]').val(),
                periodTo: $('input[id="periodTo"]').val()
                    

                }, iAmACallbackFunction2);
            }, 1000);
        });

});


</script>
<form method="post" id="downloadxls" action="reports/do-truck-photo-report-xls.php">
   <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
    <button class="btn btn-success">Download XLS</button>
	<!-- <button class="btn btn-info" id="printCustomerActivity">Print</button> -->
</form>

<div id = "CustomerActivity">
<li class="active">Report DO/Truck Photo</li>
<li class="active">Periode : <?php echo $periodFrom; ?> - <?php echo $periodTo; ?></li>
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
    <thead>
    <tr>
      <th style="text-align: center;">No.</th>
      <th style="text-align: center;">Stockpile</th>
      <th style="text-align: center;">Count</th>
      <th style="text-align: center;">Latest Transaction Date</th>
  </tr>
</thead>
<tbody>
<?php
  if($result !== false && $result->num_rows > 0) {
      $no = 0;
      while($row = $result->fetch_object()) {
          $no++;
          ?>
  <tr>
      
      <td style="text-align: center;"><?php echo $no; ?></td>
      <td style="text-align: center;"><?php echo ($row->stockpile_name);?></td>
      <td style="text-align: center;"><?php echo number_format($row->count, 1, ".", ","); ?></td>
      <td style="text-align: center;"><?php echo  date('d-M-Y', strtotime($row->latest)); ?></td>
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
</div>