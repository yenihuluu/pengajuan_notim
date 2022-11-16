<?php

// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'setFormSession') {
    $return_value = '';
    
    if($_SESSION['menu_name'] == 'Transactions') {
        $return_value = '|OK|1|';
    } elseif($_SESSION['menu_name'] == 'Payments') {
        $return_value = '|OK|2|';
    } elseif($_SESSION['menu_name'] == 'Contracts') {
        $return_value = '|OK|3|';
    } elseif($_SESSION['menu_name'] == 'Sales Agreements') {
        $return_value = '|OK|4|';
    }
    
    echo $return_value;
    
}

elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'transaction_data') {
    $return_value = '';
    
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $transactionType = $myDatabase->real_escape_string($_POST['transactionType']);
    
    if($transactionType != '') {
        $_SESSION['transaction']['stockpileId'] = $stockpileId;
        $_SESSION['transaction']['transactionType'] = $transactionType;
        
        $_SESSION['transaction']['stockpileContractId'] = $myDatabase->real_escape_string($_POST['stockpileContractId']);
        $_SESSION['transaction']['salesId'] = $myDatabase->real_escape_string($_POST['salesId']);
        $_SESSION['transaction']['shipmentId'] = $myDatabase->real_escape_string($_POST['shipmentId']);
        $_SESSION['transaction']['transactionDate'] = $myDatabase->real_escape_string($_POST['transactionDate']);
        $_SESSION['transaction']['loadingDate'] = $myDatabase->real_escape_string($_POST['loadingDate']);
        $_SESSION['transaction']['transactionDate2'] = $myDatabase->real_escape_string($_POST['transactionDate2']);
        $_SESSION['transaction']['vehicleNo'] = $myDatabase->real_escape_string($_POST['vehicleNo']);
        $_SESSION['transaction']['vehicleNo2'] = $myDatabase->real_escape_string($_POST['vehicleNo2']);
        $_SESSION['transaction']['unloadingCostId'] = $myDatabase->real_escape_string($_POST['unloadingCostId']);
        $_SESSION['transaction']['unloadingDate'] = $myDatabase->real_escape_string($_POST['unloadingDate']);
        $_SESSION['transaction']['freightCostId'] = $myDatabase->real_escape_string($_POST['freightCostId']);
        $_SESSION['transaction']['permitNo'] = $myDatabase->real_escape_string($_POST['permitNo']);
        $_SESSION['transaction']['sendWeight'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['sendWeight']));
        $_SESSION['transaction']['sendWeight2'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['sendWeight2']));
        $_SESSION['transaction']['blWeight'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['blWeight']));
        $_SESSION['transaction']['brutoWeight'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['brutoWeight']));
        $_SESSION['transaction']['tarraWeight'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['tarraWeight']));
        $_SESSION['transaction']['nettoWeight'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['nettoWeight']));
        $_SESSION['transaction']['notes'] = $myDatabase->real_escape_string($_POST['notes']);
        $_SESSION['transaction']['driver'] = $myDatabase->real_escape_string($_POST['driver']);
        $_SESSION['transaction']['block'] = $myDatabase->real_escape_string($_POST['block']);
        $_SESSION['transaction']['vendorId'] = $myDatabase->real_escape_string($_POST['vendorId']);
        $_SESSION['transaction']['supplierId'] = $myDatabase->real_escape_string($_POST['supplierId']);
        $_SESSION['transaction']['laborId'] = $myDatabase->real_escape_string($_POST['laborId']);
        $_SESSION['transaction']['isTaxable'] = $myDatabase->real_escape_string($_POST['isTaxable']);
        $_SESSION['transaction']['pph'] = $myDatabase->real_escape_string($_POST['pph']);
        $_SESSION['transaction']['ppn'] = $myDatabase->real_escape_string($_POST['ppn']);
        $_SESSION['transaction']['customerId'] = $myDatabase->real_escape_string($_POST['customerId']);
        
        $return_value = '|OK||';
    } else {
        $return_value = '|FAIL||';
    }
    
    echo $return_value;
}

elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'contract_data') {
    $return_value = '';
    
    $contractId = $myDatabase->real_escape_string($_POST['contractId']);
            
    if($contractId == '') {
        $_SESSION['contract']['contractType'] = $myDatabase->real_escape_string($_POST['contractType']);
        $_SESSION['contract']['contractNo'] = $myDatabase->real_escape_string($_POST['contractNo']);
        $_SESSION['contract']['vendorId'] = $myDatabase->real_escape_string($_POST['vendorId']);
        $_SESSION['contract']['vendorCode'] = $myDatabase->real_escape_string($_POST['vendorCode']);
        $_SESSION['contract']['vendorName'] = $myDatabase->real_escape_string($_POST['vendorName']);
        $_SESSION['contract']['vendorAddress'] = $myDatabase->real_escape_string($_POST['vendorAddress']);
        $_SESSION['contract']['npwp'] = $myDatabase->real_escape_string($_POST['npwp']);
        $_SESSION['contract']['ppn'] = $myDatabase->real_escape_string($_POST['ppn']);
        $_SESSION['contract']['pph'] = $myDatabase->real_escape_string($_POST['pph']);
        $_SESSION['contract']['currencyId'] = $myDatabase->real_escape_string($_POST['currencyId']);
        $_SESSION['contract']['stockpileId'] = $myDatabase->real_escape_string($_POST['stockpileId']);
        $_SESSION['contract']['exchangeRate'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
        $_SESSION['contract']['price'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['price']));
        $_SESSION['contract']['quantity'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['quantity']));
        $_SESSION['contract']['contractSeq'] = $myDatabase->real_escape_string($_POST['contractSeq']);
       
        $return_value = '|OK||';
    } else {
        $return_value = '|FAIL||';
    }
    
    echo $return_value;
}

elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'sales_data') {
    $return_value = '';
    
    $salesId = $myDatabase->real_escape_string($_POST['salesId']);
            
    if($salesId == '') {
        $_SESSION['sales']['salesNo'] = $myDatabase->real_escape_string($_POST['salesNo']);
        $_SESSION['sales']['salesDate'] = $myDatabase->real_escape_string($_POST['salesDate']);
        $_SESSION['sales']['salesType'] = $myDatabase->real_escape_string($_POST['salesType']);
        $_SESSION['sales']['customerId'] = $myDatabase->real_escape_string($_POST['customerId']);
        $_SESSION['sales']['customerName'] = $myDatabase->real_escape_string($_POST['customerName']);
        $_SESSION['sales']['customerAddress'] = $myDatabase->real_escape_string($_POST['customerAddress']);
        $_SESSION['sales']['npwp'] = $myDatabase->real_escape_string($_POST['npwp']);
        $_SESSION['sales']['ppn'] = $myDatabase->real_escape_string($_POST['ppn']);
        $_SESSION['sales']['pph'] = $myDatabase->real_escape_string($_POST['pph']);
        $_SESSION['sales']['stockpileId'] = $myDatabase->real_escape_string($_POST['stockpileId']);
        $_SESSION['sales']['destination'] = $myDatabase->real_escape_string($_POST['destination']);
        $_SESSION['sales']['notes'] = $myDatabase->real_escape_string($_POST['notes']);
        $_SESSION['sales']['currencyId'] = $myDatabase->real_escape_string($_POST['currencyId']);
        $_SESSION['sales']['exchangeRate'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
        $_SESSION['sales']['price'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['price']));
        $_SESSION['sales']['quantity'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['quantity']));
        $_SESSION['sales']['totalShipment'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalShipment']));
       
        $return_value = '|OK||';
    } else {
        $return_value = '|FAIL||';
    }
    
    echo $return_value;
}

elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'payment_data') {
    $return_value = '';
    
    $paymentMethod = $myDatabase->real_escape_string($_POST['paymentMethod']);
            
    if($paymentMethod != '') {
        $_SESSION['payment']['paymentMethod'] = $paymentMethod;
        
        $_SESSION['payment']['paymentDate'] = $myDatabase->real_escape_string($_POST['paymentDate']);
        $_SESSION['payment']['paymentType'] = $myDatabase->real_escape_string($_POST['paymentType']);
        $_SESSION['payment']['bankId'] = $myDatabase->real_escape_string($_POST['bankId']);
        $_SESSION['payment']['paymentFor'] = $myDatabase->real_escape_string($_POST['paymentFor']);
        $_SESSION['payment']['accountId'] = $myDatabase->real_escape_string($_POST['accountId']);
        $_SESSION['payment']['stockpileId'] = $myDatabase->real_escape_string($_POST['stockpileId']);
        $_SESSION['payment']['vendorId'] = $myDatabase->real_escape_string($_POST['vendorId']);
        $_SESSION['payment']['stockpileContractId'] = $myDatabase->real_escape_string($_POST['stockpileContractId']);
        $_SESSION['payment']['stockpileId1'] = $myDatabase->real_escape_string($_POST['stockpileId1']);
        $_SESSION['payment']['vendorId1'] = $myDatabase->real_escape_string($_POST['vendorId1']);
        $_SESSION['payment']['stockpileId2'] = $myDatabase->real_escape_string($_POST['stockpileId2']);
        $_SESSION['payment']['freightId'] = $myDatabase->real_escape_string($_POST['freightId']);
        $_SESSION['payment']['stockpileId3'] = $myDatabase->real_escape_string($_POST['stockpileId3']);
        $_SESSION['payment']['laborId'] = $myDatabase->real_escape_string($_POST['laborId']);
        $_SESSION['payment']['customerId'] = $myDatabase->real_escape_string($_POST['customerId']);
        $_SESSION['payment']['salesId'] = $myDatabase->real_escape_string($_POST['salesId']);
        $_SESSION['payment']['shipmentId'] = $myDatabase->real_escape_string($_POST['shipmentId']);
        $_SESSION['payment']['generalVendorId'] = $myDatabase->real_escape_string($_POST['generalVendorId']);
        $_SESSION['payment']['taxInvoice'] = $myDatabase->real_escape_string($_POST['taxInvoice']);
        $_SESSION['payment']['invoiceNo'] = $myDatabase->real_escape_string($_POST['invoiceNo']);
        $_SESSION['payment']['currencyId'] = $myDatabase->real_escape_string($_POST['currencyId']);
        $_SESSION['payment']['bankCurrencyId'] = $myDatabase->real_escape_string($_POST['bankCurrencyId']);
        $_SESSION['payment']['journalCurrencyId'] = $myDatabase->real_escape_string($_POST['journalCurrencyId']);
        $_SESSION['payment']['exchangeRate'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
        $_SESSION['payment']['amount'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['amount']));
        $_SESSION['payment']['totalPrice'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalPrice']));
        $_SESSION['payment']['downPayment'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['downPayment']));
        $_SESSION['payment']['grandTotal'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['grandTotal']));
        $_SESSION['payment']['ppn'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppn']));
        $_SESSION['payment']['pph'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['pph']));
        $_SESSION['payment']['ppn1'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppn1']));
        $_SESSION['payment']['pph1'] = str_replace(",", "", $myDatabase->real_escape_string($_POST['pph1']));
        $_SESSION['payment']['paymentNotes'] = $myDatabase->real_escape_string($_POST['paymentNotes']);
        $_SESSION['payment']['remarks'] = $myDatabase->real_escape_string($_POST['remarks']);
        $_SESSION['payment']['paymentLocation'] = $myDatabase->real_escape_string($_POST['paymentLocation']);
        $_SESSION['payment']['chequeNo'] = $myDatabase->real_escape_string($_POST['chequeNo']);
       
        $return_value = '|OK||';
    } else {
        $return_value = '|FAIL||';
    }
    
    echo $return_value;
}