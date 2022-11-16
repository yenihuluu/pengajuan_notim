<?php

header('Content-type: text/javascript');
require_once("ripcord/ripcord.php");


require_once 'assets/include/path_variable.php';
// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';
// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

// Authentication
//DEV
//$url = "http://210.210.175.1:8069";
//$db = "JATIM0709";
//$username = "admin";
//$password = "admin";
//PROD
$url = "http://210.210.175.1:8070";
$db = "JATIM_PROPERTINDO_JAYA";
$username = "admin";
$password = "superj4t1m!";
$common = ripcord::client("$url/xmlrpc/2/common");
$uid = $common->authenticate($db, $username, $password, array());


//Variable
$glDateFrom = '';
$glDateTo = '';
$periodFrom = '';
$periodTo = '';
$module = '';

//Query Select Data
$sql = "SELECT * FROM gl_report WHERE status = 0";

//Filter Condition
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

//SAVE DATA TO VAR $data
$data = [];
while ($rowData = $result->fetch_array()) {
    //ORIGINAL AMOUNT
    if ($rowData['debitAmount'] != 0) {
        $original_amount = $rowData['debitAmount'];
    } else {
        $original_amount = $rowData['creditAmount'];
    }

    //CUSTOM CURRENCY
    if ($rowData['exchange_rate'] > 10000) {
        $customCurrency = 'USD';
    } else {
        $customCurrency = 'IDR';
    }

    //JURNAL CODE
    if ($rowData['general_ledger_module'] == 'NOTA TIMBANG') {
        $jurnal_code = 'NOTIM';
    } elseif ($rowData['general_ledger_module'] == 'PAYMENT') {
        $jurnal_code = 'PAY';
    } elseif ($rowData['general_ledger_module'] == 'PAYMENT ADMIN') {
        $jurnal_code = 'PAYA';
    } elseif ($rowData['general_ledger_module'] == 'PETTY CASH') {
        $jurnal_code = 'PCH';
    } elseif ($rowData['general_ledger_module'] == 'RETURN INVOICE') {
        $jurnal_code = 'RINV';
    } elseif ($rowData['general_ledger_module'] == 'RETURN PAYMENT') {
        $jurnal_code = 'RPAY';
    } elseif ($rowData['general_ledger_module'] == 'STOCK TRANSIT') {
        $jurnal_code = 'STR';
    } elseif ($rowData['general_ledger_module'] == 'CONTRACT') {
        $jurnal_code = 'CTR';
    } elseif ($rowData['general_ledger_module'] == 'CONTRACT ADJUSTMENT') {
        $jurnal_code = 'CTRA';
    } elseif ($rowData['general_ledger_module'] == 'INVOICE DETAIL') {
        $jurnal_code = 'INV';
    } elseif ($rowData['general_ledger_module'] == 'Jurnal Memorial') {
        $jurnal_code = 'JM';
    } else {
        $jurnal_code = 'NULL';
    }

    //Data
    $data[] = [
        'gl_id' => $rowData['gl_id'],
        'date' => $rowData['gl_date'],
        'name' => $rowData['jurnal_no'],
        'journal_code' => $jurnal_code,
        'account_coa' => $rowData['account_no'],
        'partner' => $rowData['supplier_code'],
        'partner_name' => $rowData['supplier_name'],
        'remark' => $rowData['remarks'],
        'stockpile' => $rowData['stockpile'],
        'shipment_code' => $rowData['shipment_code'],
        'po_number' => $rowData['po_no'],
        'contract_number' => $rowData['contract_no'],
        'slip_number' => $rowData['slip_no'],
        'invoice_number' => $rowData['invoice_no'],
        'quantity' => $rowData['quantity'],
        'price_unit' => $rowData['price'],
        'debit' => $rowData['debitAmount'],
        'credit' => $rowData['creditAmount'],
        'method' => $rowData['general_ledger_method'],
        'ns_type' => $rowData['general_ledger_transaction_type2'],
        'original_invoice' => $rowData['invoice_no_2'],
        'tax_invoice' => $rowData['tax_invoice'],
        'nomor_cek' => $rowData['cheque_no'],
        'original_amount' => $original_amount,
        'custom_currency' => $customCurrency,
        'kurs' => $rowData['exchange_rate'],
    ];

}
//Wrap to Array
$data_jurnal = array($data);

#Model / Execution
$models = ripcord::client("$url/xmlrpc/2/object");
$result2 = $models->execute_kw(
    $db,
    $uid,
    $password,
    'account.move',
    'jatim_journal',
    $data_jurnal);
//    $reee2 = json_encode($result2, JSON_PRETTY_PRINT);
//    print_r($reee2);

//Update Status
$sql = "UPDATE gl_report SET  status = 1 WHERE status = 0";

//Filter Condition
if (isset($glDateFrom) && $glDateFrom != '' && isset($glDateTo) && $glDateTo != '') {
    $sql .= " AND gl_date BETWEEN '{$glDateFrom}' AND '{$glDateTo}'";
}
if (isset($periodFrom) && $periodFrom != '' && isset($periodTo) && $periodTo != '') {
    $sql .= " AND DATE_FORMAT(entry_date,'%Y-%m-%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
}
if (isset($module) && $module != '') {
    $sql .= " AND general_ledger_module = '{$module}'";
    if ($module == 'Jurnal Memorial') {
        $sql .= " AND entry_by = '{$_SESSION['userId']}'";
    }
}

$sql .= " ORDER BY jurnal_no ASC";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

//INSERT LOG
$date = date("Y-m-d h:i:s");
$status = $result2['type'];

if ($result2['type'] == 'success') {
    $journal_number = $module;
    $reason = $result2['data'];
    $sql = "INSERT INTO `odoo_journal_error_log`
				(`journal_number`,`status`,`reason`,`entry_date`)
				VALUES
				('{$journal_number}','{$status}','{$reason}','{$date}');";
    $myDatabase->query($sql, MYSQLI_STORE_RESULT);
} else {
    foreach ($result2['error_log'] as $error) {
        $journal_number = $error['No'];
        $reason = str_replace("'", '', $error['reason']);
        $sql = "INSERT INTO `odoo_journal_error_log`
					(`journal_number`,`status`,`reason`,`entry_date`)
					VALUES
					('{$journal_number}','{$status}','{$reason}','{$date}');";
        $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    }
}
?>
<!--Show Error Log-->
<?php if ($result2['type'] == 'success') { ?>
    <table class="table table-bordered table-striped" style="font-size: 8pt;">
        <thead>
        <tr>
            <th>Module</th>
            <th>Status</th>
            <th>Reason</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align: left;"><?php echo $module; ?></td>
            <td style="text-align: left;"><?php echo $status; ?></td>
            <td style="text-align: left;"><?php echo $result2['data']; ?></td>
        </tr>
        </tbody>
    </table>
<?php } else { ?>
    <table class="table table-bordered table-striped" style="font-size: 8pt;">
        <thead>
        <tr>
            <th>Journal Number</th>
            <th>Status</th>
            <th>Reason</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result2['error_log'] as $error) { ?>
            <tr>
                <td style="text-align: left;"><?php echo $error['No']; ?></td>
                <td style="text-align: left;"><?php echo $status; ?></td>
                <td style="text-align: left;"><?php echo $error['reason']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
