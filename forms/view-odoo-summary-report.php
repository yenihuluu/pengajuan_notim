<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$module = $_POST['module'];
$From = $_POST['periodFrom'];
$End = $_POST['periodTo'];
$status = $_POST['status'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT  *, CASE WHEN quantity = '' THEN 0 ELSE quantity END as quantity,
CASE WHEN price = '' THEN 0 ELSE price END as price,
CASE WHEN creditAmount = '' THEN 0 ELSE creditAmount END as creditAmount,
CASE WHEN debitAmount = '' THEN 0 ELSE debitAmount END as debitAmount
FROM `gl_report` WHERE `gl_date` >= '$From' AND `gl_date` <= '$End' AND general_ledger_module = '$module' AND `status` = '$status'";
// echo $sql;
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
        $("#contentTable2").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets: ['zebra', 'filter', 'uitheme'],

            headers: {0: {sorter: true, filter: true}},

            widgetOptions: {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra: ["even", "odd"],

                filter_functions: {
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
                cssGoto: ".pagenum",

                // remove rows from the table to speed up the sort of large tables.
                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                removeRows: false,
                // output string - default is '{page}/{totalPages}';
                // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

            });

        $('#addNew').click(function (e) {

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });

        $('#contentTable2 a').click(function (e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');

        });

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

</script>
<input type="hidden" name="module" value="<?php echo $module; ?>">
<input type="hidden" name="periodFrom" value="<?php echo $From; ?>">
<input type="hidden" name="periodTo" value="<?php echo $End; ?>">
<input type="hidden" name="status" value="<?php echo $status; ?>">

<div class="row-fluid">
    <table class="table table-bordered table-striped" id="contentTable2" style="font-size: 8pt;">
        <thead>
        <tr>
            <th>No</th>
            <th>GL Date</th>
            <th>Journal No</th>
            <th>Journal Code</th>
            <th>Account No</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th>Remarks</th>
            <th>Stockpile</th>
            <th>Shipment Code</th>
            <th>PO NO</th>
            <th>Contract No</th>
            <th>Slip No</th>
            <th>Invoice No</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Debit Amount</th>
            <th>Credit Amount</th>
            <th>Method</th>
            <th>Transaction Type 2</th>
            <th>Original Invoice</th>
            <th>Tax Invoice</th>
            <th>Cheque No</th>
            <th>Original Amount</th>
            <th>Currency</th>
            <!-- <th>Kurs</th> -->
        </tr>

        </thead>

        <tbody>

        <?php

        if ($result === false) {
            echo 'wrong query';
            echo $sql;

        } else {
            $no = 1;
            while ($row = $result->fetch_object()) {
                ?>
                <?php
                //ORIGINAL AMOUNT
                if ($row->debitAmount != 0) {
                    $original_amount = $row->debitAmount;
                } else {
                    $original_amount = $row->creditAmount;
                }
                //CUSTOM CURRENCY
                if ($row->exchange_rate > 10000) {
                    $customCurrency = 'USD';
                } else {
                    $customCurrency = 'IDR';
                }
                //JURNAL CODE

                if ($row->general_ledger_module == 'NOTA TIMBANG') {
                    $jurnal_code = 'NOTIM';
                } elseif ($row->general_ledger_module == 'PAYMENT') {
                    $jurnal_code = 'PAY';
                } elseif ($row->general_ledger_module == 'PAYMENT ADMIN') {
                    $jurnal_code = 'PAYA';
                } elseif ($row->general_ledger_module == 'PETTY CASH') {
                    $jurnal_code = 'PCH';
                } elseif ($row->general_ledger_module == 'RETURN INVOICE') {
                    $jurnal_code = 'RINV';
                } elseif ($row->general_ledger_module == 'RETURN PAYMENT') {
                    $jurnal_code = 'RPAY';
                } elseif ($row->general_ledger_module == 'STOCK TRANSIT') {
                    $jurnal_code = 'STR';
                } elseif ($row->general_ledger_module == 'CONTRACT') {
                    $jurnal_code = 'CTR';
                } elseif ($row->general_ledger_module == 'CONTRACT ADJUSTMENT') {
                    $jurnal_code = 'CTRA';
                } elseif ($row->general_ledger_module == 'INVOICE DETAIL') {
                    $jurnal_code = 'INV';
                } elseif ($row->general_ledger_module == 'Jurnal Memorial') {
                    $jurnal_code = 'JM';
                } else {
                    $jurnal_code = 'NULL';
                }
                ?>
                <tr>
                    <td style="text-align: left;"><?php echo $no++; ?></td>
                    <td style="text-align: left;"><?php echo $row->gl_date; ?></td>
                    <td style="text-align: left;"><?php echo $row->jurnal_no; ?></td>
                    <td style="text-align: left;"><?php echo $jurnal_code ?></td>
                    <td style="text-align: left;"><?php echo $row->account_no; ?></td>
                    <td style="text-align: left;"><?php echo $row->supplier_code; ?></td>
                    <td style="text-align: left;"><?php echo $row->supplier_name; ?></td>
                    <td style="text-align: left;"><?php echo $row->remarks; ?></td>
                    <td style="text-align: left;"><?php echo $row->stockpile; ?></td>
                    <td style="text-align: left;"><?php echo $row->shipment_code; ?></td>
                    <td style="text-align: left;"><?php echo $row->po_no; ?></td>
                    <td style="text-align: left;"><?php echo $row->contract_no; ?></td>
                    <td style="text-align: left;"><?php echo $row->slip_no; ?></td>
                    <td style="text-align: left;"><?php echo $row->invoice_no; ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ",") ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->price, 0, ".", ",") ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->debitAmount, 0, ".", ",") ?></td>
                    <td style="text-align: right;"><?php echo number_format($row->creditAmount, 0, ".", ",") ?></td>
                    <td style="text-align: left;"><?php echo $row->general_ledger_method; ?></td>
                    <td style="text-align: left;"><?php echo $row->general_ledger_transaction_type2; ?></td>
                    <td style="text-align: left;"><?php echo $row->invoice_no_2; ?></td>
                    <td style="text-align: left;"><?php echo $row->tax_invoice; ?></td>
                    <td style="text-align: left;"><?php echo $row->cheque_no; ?></td>
                    <td style="text-align: right;"><?php echo number_format($original_amount, 0, ".", ",") ?></td>
                    <td style="text-align: left;"><?php echo $customCurrency; ?></td>
                    <!-- <td style="text-align: right;"><?php echo $row->exchange_rate ?></td> -->
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


