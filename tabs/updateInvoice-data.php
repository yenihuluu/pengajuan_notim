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

$invoiceId = '';
$invoiceNo = '';
$invoiceDate = '';
$originalInvoiceNo = '';
$taxInvoiceNo = '';
$taxInvoiceDate = '';
$stockpileId = '';
$remarks = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['invoiceId']) && $_POST['invoiceId'] != '') {
    
    $invoiceId = $_POST['invoiceId'];
    
    //$readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for User Data">
    
    $sql = "SELECT inv.invoice_no, pg.pengajuan_general_id as pengajuanId, inv.invoice_no2, inv.invoice_tax, inv.stockpileId, inv.tax_date AS tax_date2, inv.invoice_date AS invoice_date2,
CASE WHEN DATE_FORMAT(inv.tax_date, '%d/%m/%Y') = '00/00/0000' THEN '' ELSE DATE_FORMAT(inv.tax_date, '%d/%m/%Y') END AS tax_date,
CASE WHEN DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') = '00/00/0000' THEN '' ELSE DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') END AS invoice_date,
inv.remarks 
FROM invoice inv
LEFT JOIN pengajuan_general pg ON pg.invoice_id = inv.invoice_id
WHERE inv.invoice_id = {$invoiceId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $invoiceNo = $rowData->invoice_no;
		$invoiceDate = $rowData->invoice_date;
        $originalInvoiceNo = $rowData->invoice_no2;
        $taxInvoiceNo = $rowData->invoice_tax;
        $taxInvoiceDate = $rowData->tax_date;
		$invoiceDate2 = $rowData->invoice_date2;
        $taxInvoiceDate2 = $rowData->tax_date2;
		$stockpileId = $rowData->stockpileId;
		$remarks = $rowData->remarks;
		$pengajuanNo = $rowData->pengajuanId;
		$oldPengajuan_id = $rowData->pengajuanId;
		
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
	
	$(".select2combobox100").select2({
            width: "100%"
        });
        
        
    
    $(document).ready(function(){
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#InvoiceDataForm").validate({
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
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#InvoiceDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('invoiceId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/updateInvoice.php', { invoiceId: returnVal[3] }, iAmACallbackFunction2);

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

<form method="post" id="InvoiceDataForm">
    <input type="hidden" name="action" id="action" value="update_invoice_data" />
    <input type="hidden" name="invoiceId" id="invoiceId" value="<?php echo $invoiceId; ?>" />
	<input type="hidden" name="originalInvoiceNo2" id="originalInvoiceNo2" value="<?php echo $originalInvoiceNo; ?>" />
	<input type="hidden" name="invoiceDate2" id="invoiceDate2" value="<?php echo $invoiceDate2; ?>" />
	<input type="hidden" name="taxInvoiceDate2" id="taxInvoiceDate2" value="<?php echo $taxInvoiceDate2; ?>" />
	<input type="hidden" name="taxInvoiceNo2" id="taxInvoiceNo2" value="<?php echo $taxInvoiceNo; ?>" />
	<input type="hidden" name="remarks2" id="remarks2" value="<?php echo $remarks; ?>" />
    <div class="row-fluid">
		<div class="span4 lightblue">
            <label>Pengajuan No</label>
			<?php
            createCombo("SELECT pengajuan_general_id, pengajuan_no 
						FROM pengajuan_general ORDER BY pengajuan_general_id DESC", $pengajuanNo, "", "pengajuanNo", "pengajuan_general_id", "pengajuan_no",
                        "", 1, "select2combobox100", 5);
            ?>  
            <input type="hidden" class="span12" readonly tabindex="1" id="oldPengajuan_id" name="oldPengajuan_id" value="<?php echo $oldPengajuan_id; ?>">
			
		</div>	
        <div class="span4 lightblue">
            <label>Invoice No</label>
            <input type="text" class="span12" readonly tabindex="1" id="invoiceNo" name="invoiceNo" value="<?php echo $invoiceNo; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Original Invoice No</label>
            <input type="text" class="span12" tabindex="1" id="originalInvoiceNo" name="originalInvoiceNo" value="<?php echo $originalInvoiceNo; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Invoice Date</label>
             <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate" value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
            <!--<label>Stockpile <span style="color: red;">*</span></label>-->
            <?php
            //createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
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
