<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y');

$whereProperty = '';
$shipmentId = '';
$shipmentCode1 = '';
//$shipmentId = 32039;
if(isset($_POST['shipmentId']) && $_POST['shipmentId'] != '') {
    $shipmentId = $_POST['shipmentId'];
}

    $sql1 = "SELECT shipment_code as shipmentCode FROM shipment where shipment_id = {$shipmentId}";
    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
    $row2 = $result1->fetch_object();
    $shipmentCode1 = $row2->shipmentCode;

    $sql = "CALL sp_jobCosting({$shipmentId})";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT); 

?>

<script type="text/javascript">
    $(document).ready(function () {	//executed after the page has loaded

        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table: 'table table-bordered',
            header: 'bootstrap-header', // give the header a gradient background
            footerRow: '',
            footerCells: '',
            icons: '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone: 'bootstrap-icon-unsorted',
            sortAsc: 'icon-chevron-up',
            sortDesc: 'icon-chevron-down',
            active: '', // applied when column is sorted
            hover: '', // use custom css here - bootstrap class may not override it
            filterRow: '', // filter row class
            even: '', // odd row zebra striping
            odd: ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets: ['zebra', 'filter', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra: ["even", "odd"],
            }
        }).tablesorterPager({
            container: $(".pager"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });

        // $('#addNew').click(function (e) {
        //     e.preventDefault();
        //     loadContent($('#addNew').attr('href'));
        // });

        $('#contentTable a').click(function (e) {
            e.preventDefault();
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
        });

            $("#contentTable").tablesorter({
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            widgets: ['zebra', '', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                zebra: ["even", "odd"],
            }
        }).tablesorterPager({
            container: $(".pager"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });
        
        //END TEST

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

</script>

<form method="post" action="reports/costing-report-xls.php">
    <input type="hidden" id="shipmentId" name="shipmentId" value="<?php echo $shipmentId; ?>" />
    <input type="hidden" id="shipmentCode" name="shipmentCode" value="<?php echo $shipmentCode1; ?>" />

    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
<thead>
        <tr>
            <!-- HEADER -->
            <th rowspan="2" >No.</th>
            <th rowspan="2">Currency</th>
            <th rowspan="2">Account Name</th>
            <th rowspan="2">Type Charge</th>
            <th rowspan="2">Type</th>
            <th rowspan="2">Jenis Qty</th>
            <th rowspan="2">Min Charge</th>
            <th rowspan="2">Max Charge</th>
            <th rowspan="2">Biaya</th>
            <th rowspan="2">PEB Date</th>
            <th rowspan="2">Vendor</th>
            <th rowspan="2">Mother Vessel</th>
            <th rowspan="2">Price/MT</th>
            <th colspan="5" ><center>Prediksi</center></th>
            <th colspan="10" ><center>Aktual</center></th>
        </tr>
        <tr>
            <!-- PREDIKSI -->
            <th>Qty</th>
            <th>Price/MT</th>
            <th>Total Amount</th>
            <th>Kurs</th>
            <th>In Rupiah</th>

            <!-- ACTUAL/INVOICE -->
            <th>No Invoice</th>
            <th>Tanggal Invoice</th>
            <th>Qty</th>
            <th>Price/MT</th>
            <th>Total Amount</th>
            <th>Kurs</th>
            <th>In Rupiah</th>
            <th>Status</th>
            <th>Tanggal Payment</th>
            <th>No Payment</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if($result === false) {
        echo 'wrong query';
    } else {
        $no = 1;
        $total_minC = 0;
        $total_maxC = 0;
        $totalP = 0;
        $total_tAmount = 0;
        while($row = $result->fetch_object()) {
            
        // echo $row1->nilai_process_invoice;
            ?>
            <tr>
               <!-- HEADER --> 
                <td><?php echo $no; ?></td> 
                <td><?php echo $row->currency_code; ?></td>
                <td><?php echo $row->accountName; ?></td>
                <td><?php echo $row->maxminC; ?></td>
                <td><?php echo $row->priceType; ?></td>
                <td><?php echo $row->qtyType; ?></td>
                <td><?php echo number_format($row->min_charge, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->max_charge, 0, ".", ","); ?></td>
                <td><?php echo $row->biaya; ?></td>
                <td><?php echo $row->pebDate; ?></td>
                <td><?php echo $row->general_vendor_name; ?></td>
                <td><?php echo $row->motherVessel; ?></td>
                <td><?php echo number_format($row->priceMT, 0, ".", ","); ?></td>


                 <!-- PREDIKSI -->
                <td><?php echo number_format($row->qty, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->priceMT, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->total_amount, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->exchange_rate, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->in_rupiah, 0, ".", ","); ?></td>

                <!-- INVOICE/ACTUAL Value -->
                <td><?php echo $row->invNo; ?></td>
                <td><?php echo $row->dateInv; ?></td>
                <td><?php echo number_format($row->qtyInv, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->priceInv, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->amountInv, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->exchange_rate, 0, ".", ","); ?></td>
                <td><?php echo number_format($row->inRP_Inv, 0, ".", ","); ?></td>
                <td><?php echo $row->methodText; ?></td>
                <td><?php echo $row->paymentDate; ?></td>
                <td><?php echo $row->payment_no; ?></td>


            </tr>
<?php $no++; } ?>
 <?php } ?>
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
