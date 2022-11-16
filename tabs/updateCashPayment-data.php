<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for User Data">

$paymentId = '';
$paymentNo = '';
$paymentDate = '';
$InvoiceNo = '';
$InvoiceDate = '';
$taxInvoiceNo = '';
$taxInvoiceDate = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['paymentId']) && $_POST['paymentId'] != '') {
    
    $paymentId = $_POST['paymentId'];
    
    //$readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT p.payment_id, p.payment_no, p.remarks, b.bank_code, b.bank_type, pcur.currency_code AS pcur_currency_code, p.payment_type,
CASE WHEN p.payment_location = 0 THEN 'HOF' ELSE s.stockpile_code END AS payment_location2, 
p.invoice_no, p.tax_invoice, DATE_FORMAT(p.tax_invoice_date, '%d/%m/%Y') AS tax_invoice_date, u.user_name, DATE_FORMAT(p.entry_date, '%d/%m/%Y') AS entry_date,
CASE WHEN DATE_FORMAT(p.invoice_date, '%d/%m/%Y') = '00/00/0000' THEN '' ELSE DATE_FORMAT(p.invoice_date, '%d/%m/%Y') END AS invoice_date,
CASE WHEN DATE_FORMAT(p.payment_date, '%d/%m/%Y') = '00/00/0000' THEN '' ELSE DATE_FORMAT(p.payment_date, '%d/%m/%Y') END AS payment_date
FROM payment p
LEFT JOIN bank b ON b.bank_id = p.bank_id
LEFT JOIN currency pcur ON pcur.currency_id = p.currency_id
LEFT JOIN stockpile s ON s.stockpile_id = p.payment_location
LEFT JOIN `user` u ON u.`user_id` = p.entry_by
WHERE payment_id = {$paymentId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        
		$voucherCode = $rowData->payment_location2 .'/'. $rowData->bank_code .'/'. $rowData->pcur_currency_code;
                
                if($rowData->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($rowData->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($rowData->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }
                
                if($rowData->bank_type != 3) {
                    if($rowData->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }
                
        $paymentNo =  $voucherCode .' # '. $rowData->payment_no;
		$paymentDate = $rowData->payment_date;	
		$invoiceNo = $rowData->invoice_no;
		$invoiceDate = $rowData->invoice_date;
        $taxInvoiceNo = $rowData->tax_invoice;
        $taxInvoiceDate = $rowData->tax_invoice_date;
		$paymentDate = $rowData->payment_date;
		$remarks = $rowData->remarks;
		
    }
    
    // </editor-fold>
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#PaymentDataForm").validate({
           /* rules: {
                userName: "required",
                userPassword: "required",
                confirmPassword: "required",
                userEmail: {
                    required: true,
                    email: true
                },
                active: "required",
				stockpileId: "required"
            },
            messages: {
                userName: "Name is a required field.",
                userPassword: "Password is a required field.",
                confirmPassword: "Confirm Password is a required field.",
                userEmail: {
                 required: "Email is a required field.",
                    email: "Invalid email."
                },
                active: "Status is a required field.",
				stockpileId: "Stockpile is a required field."
            },*/
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#PaymentDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('paymentId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/updateCashPayment.php', { paymentId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
                    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>

<form method="post" id="PaymentDataForm">
    <input type="hidden" name="action" id="action" value="update_cash_payment_data" />
    <input type="hidden" name="paymentId" id="paymentId" value="<?php echo $paymentId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Payment No</label>
            <input type="text" class="span12" readonly tabindex="1" id="paymentNo" name="paymentNo" value="<?php echo $paymentNo; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Invoice No</label>
            <input type="text" class="span12" tabindex="1" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNo; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Payment Date</label>
            <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="paymentDate" name="paymentDate" value="<?php echo $paymentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
            <label>Invoice Date</label>
            <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate" value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Tax Invoice No</label>
             <input type="text" class="span12" tabindex="1" id="taxInvoiceNo" name="taxInvoiceNo" value="<?php echo $taxInvoiceNo; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Tax Invoice Date</label>
            <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="taxInvoiceDate" name="taxInvoiceDate" value="<?php echo $taxInvoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>
        <div class="span4 lightblue">
            
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
