<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


// PATH

require_once '../assets/include/path_variable.php';


// Session

require_once PATH_INCLUDE . DS . 'session_variable.php';


// Initiate DB connection

require_once PATH_INCLUDE . DS . 'db_init.php';


$whereProperty = '';

$glDateFrom = '';
$glDateTo = '';
$periodFrom = '';
$periodTo = '';
$module = '';

$sql = "SELECT * FROM gl_report WHERE status = 0";

if (isset($_POST['glDateFrom']) && $_POST['glDateFrom'] != '' && isset($_POST['glDateTo']) && $_POST['glDateTo'] != '') {
    $glDateFrom = $_POST['glDateFrom'];
    $glDateTo = $_POST['glDateTo'];

    $sql .= " AND gl_date BETWEEN '{$glDateFrom}' AND '{$glDateTo}'";
}

if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];

    $sql .= " AND DATE_FORMAT(entry_date,'%Y-%m-%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
}

if (isset($_POST['module']) && $_POST['module'] != '') {
    $module = $_POST['module'];

    $sql .= " AND general_ledger_module = '{$module}'";
    if ($module == 'Jurnal Memorial') {
        $sql .= " AND entry_by = '{$_SESSION['userId']}'";
    }
}


$sql .= " ORDER BY jurnal_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#sendForm').submit(function (e) {
            e.preventDefault();
            $('#dataSearch').load('searchs/journal-odoo.php');
            $('#dataContent').load('../contoh_jatim.php', {
                periodFrom: $('input[id="periodFrom"]').val(),
                periodTo: $('input[id="periodTo"]').val(),
                module: $('input[id="module"]').val(),
                glDateFrom: document.getElementById('glDateFrom').value,
                glDateTo: document.getElementById('glDateTo').value,
            }, iAmACallbackFunction2);
        });

    });
</script>

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

        $('#addNew').click(function (e) {
            e.preventDefault();
            loadContent($('#addNew').attr('href'));
        });

        $('#contentTable a').click(function (e) {
            e.preventDefault();
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/add-logbook.php', {logbookId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'delete') {
                alertify.set({
                    labels: {
                        ok: "Yes",
                        cancel: "No"
                    }
                });
                alertify.confirm("Are you sure want to delete this record?", function (e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: {
                                action: 'logbook_data',
                                delete: 'true',
                                logbookId: menu[2]
                            },
                            success: function (data) {
                                var returnVal = data.split('|');
                                if (parseInt(returnVal[3]) != 0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({
                                        labels: {
                                            ok: "OK"
                                        }
                                    });
                                    alertify.alert(returnVal[2]);
                                    if (returnVal[1] == 'OK') {
                                        $('#dataContent').load('contents/logbook.php', {}, iAmACallbackFunction2);
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

<div class="row-fluid">
    <div class="control-group">
        <form method="post" id="sendForm">
            <button class="btn btn-success">Send</button>
        </form>
    </div>
    <div class="control-group">
        <form method="post" action="reports/journal-odoo-xls.php">
            <button class="btn btn-success">Download XLS</button>
            <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>"/>
            <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>"/>
            <input type="hidden" id="glDateFrom" name="glDateFrom" value="<?php echo $glDateFrom; ?>"/>
            <input type="hidden" id="glDateTo" name="glDateTo" value="<?php echo $glDateTo; ?>"/>
            <input type="hidden" id="module" name="module" value="<?php echo $module; ?>"/>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
    <thead>
    <tr>
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
        <th>Kurs</th>
    </tr>

    </thead>

    <tbody>

    <?php

    if ($result === false) {
        echo 'wrong query';
        echo $sql;
    } else {
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
                <td style="text-align: right;"><?php echo number_format($row->exchange_rate, 0, ".", ",") ?></td>
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