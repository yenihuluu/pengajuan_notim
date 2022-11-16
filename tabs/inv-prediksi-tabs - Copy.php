<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$todayDate = $date->format('d/m/Y');

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$detailId = '';
$invId = '';
$qtyVessel  = 0;
$qtyTongkang  = 0;
$boolean = false;

$inputDate = $todayDate;
$invDate = $todayDate;


// </editor-fold>

// If ID is in the parameter
if((isset($_POST['detailId']) && $_POST['detailId'] != '') || (isset($_POST['invId']) && $_POST['invId'] != '')) {
    
    $detailId = $_POST['detailId'];
    $invId = $_POST['invId'];
    
    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		//echo $row->user_id;
        if($row->user_id != 46 && $row->user_id != 22) {
            $readonlyProperty = 'readonly';
        }else{
			$readonlyProperty = '';
		}
    }
}
    
//echo "TEST TEST " . $invId;
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    if($detailId != ''){
        $sql = "SELECT apd.*, sp.stockpile_id, gv.general_vendor_name FROM accrue_prediction_detail apd 
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
                LEFT JOIN stockpile sp on sp.stockpile_code = SUBSTRING(apd.generate_code_detai, 1, 3) 
                WHERE prediction_detail_id  = {$detailId}";
    }else if($invId != ''){
        $boolean = true;
        $sql = "SELECT inv.*,  gv.general_vendor_name, apd.cost_name, apd.max_charge, apd.min_charge, apd.qty, apd.priceMT, apd.total_amount, apd.exchange_rate,
                        apd.in_rupiah, apd.stockpile_remarks,
                        DATE_FORMAT(inv.input_date, '%d/%m/%Y') AS inputDate, DATE_FORMAT(inv.request_date, '%d/%m/%Y') AS requestDate, DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') AS invoiceDate,
                        DATE_FORMAT(inv.tax_date, '%d/%m/%Y') AS taxDate
                FROM invoice_prediksi inv 
                INNER JOIN accrue_prediction_detail apd ON apd.prediction_detail_id = inv.prediction_detail_id
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
                LEFT JOIN stockpile sp on sp.stockpile_code = inv.stockpile_id
                WHERE inv.invoice_id  = {$invId}";
    }
   
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        if($boolean){
            $detailId = $rowData->prediction_detail_id;
            $detailCode = $rowData->generate_code_detai;
            $generateInv1 = $rowData->generate_inv_code;
            $methodeId = $rowData->methode_inv;
            $originalInv = $rowData->original_inv;
            $inputDate = $rowData->inputDate;
            $reqDate = $rowData->requestDate;
            $invDate = $rowData->invoiceDate;
            $taxInv = $rowData->tax_invoice;
            $taxDate = $rowData->taxDate;
            $remarks = $rowData->remarks;
            $buttonText = 'UPDATE';
        }else{
            $buttonText = 'SUBMIT';
        }
        $detailCode = $rowData->generate_code_detai;
        $biaya = $rowData->cost_name;
        $generalVendor = $rowData->general_vendor_name;
        $maxCharge = number_format($rowData->max_charge, 0, ".", ",");
        $minCharge = number_format($rowData->min_charge, 0, ".", ",");
        $qty = number_format($rowData->qty, 0, ".", ",");
        $price = number_format($rowData->priceMT, 0, ".", ",");
        $totalAmount = number_format($rowData->total_amount, 0, ".", ",");
        $exchangeRate = number_format($rowData->exchange_rate, 0, ".", ",");
        $inRupiah = number_format($rowData->in_rupiah, 0, ".", ",");
        $spRemarks = $rowData->stockpile_remarks;
        $stockpileId = $rowData->stockpile_id;
        $headerId = $rowData->prediction_id;
       

    }

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
       echo "<option value=''>-- Please Select --</option>";
      
    } else if($empty == 2) {
        echo "<option value=''></option>";
        if($setvalue == 0) {
            echo "<option value='0' selected>-- Please Select Stockpile --</option>";
        } else {
            echo "<option value='0'>-- Please Select --</option>";
        }
    } 

    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    
    $(document).ready(function(){
        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $('#qtyTongkang').number(true, 2);
        
        <?php if($invId == ''){ ?>
            getGenerateCodeInv($('select[id="stockpileId"]').val());
        <?php } ?>

        $("#InvPrediksiDataForm").validate({
            rules: {
                detailId: "required",
                detailCode: "required",
                methodeId: "required",
                stockpileId: "required",
                inputDate: "required",
				reqDate: "required",
                invDate: "required"
               
            },
            messages: {
                detailId: "Detail Prediksi is a required field.",
                detailCode: "Detail Code is a required field.",
                methodeId: "Methode Invoice is a required field.",
                stockpileId: "Stockpile is a required field.",
                inputDate: "required field.",
                reqDate: "required field.",
                invDate: "required field."
				
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#InvPrediksiDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            <?php if($boolean){ ?>
                                if (returnVal[1] == 'OK') {
                                    document.getElementById('GeneralInvId').value = returnVal[3];
                                    $('#dataContent').load('views/search-invoice-prediksi.php', { invId: returnVal[3] }, iAmACallbackFunction2);
                                } 
                            <?php } else { ?>
                                if (returnVal[1] == 'OK') {
                                    document.getElementById('dt_id').value = returnVal[3];
                                    $('#dataContent').load('views/invoice_prediksi.php', { detailId: returnVal[3] }, iAmACallbackFunction2);
                                } 
                            <?php } ?>
                           
                        }
                    }
                });
            }
        });
    });
    

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });

    function getGenerateCodeInv(stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getGenerateCodeInv',
                    stockpileId: stockpileId
                },
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	
                {
                    document.getElementById('genereteInv1').value = returnVal[1];
                }
            }
        });
    }


    
</script>

<form method="post" id="InvPrediksiDataForm">
    <input type="hidden" name="action" id="action" value="inv_prediksi_data" />
    <input type="hidden" name="detailId" id="detailId" value="<?php echo $detailId; ?>" />
    <input type="hidden" name="detailCode" id="detailCode" value="<?php echo $detailCode; ?>" />
    <input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>" />
    <input type="hidden" name="headerId" id="headerId" value="<?php echo $headerId; ?>" />

    <div class="row-fluid">  
        <table class="table table-bordered table-striped" style="font-size: 9pt;">
            <thead>
                <tr>
                    <th>Detail Code</th>
                    <th>Biaya</th>
                    <th>General Vendor</th>
                    <th>Max Charge</th>
                    <th>Min Charge</th>
                    <th>Qty</th>
                    <th>Price/MT</th>
                    <th>Total Amount</th>
                    <th>Exchange Rate</th>
                    <th>Rupiah</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $detailCode; ?></td>
                    <td><?php echo $biaya; ?></td>
                    <td><?php echo $generalVendor; ?></td>
                    <td><?php echo $maxCharge; ?></td>
                    <td><?php echo $minCharge; ?></td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo $price; ?></td>
                    <td><?php echo $totalAmount; ?></td>
                    <td><?php echo $exchangeRate; ?></td>
                    <td><?php echo $inRupiah; ?></td>
                    <td><?php echo $spRemarks; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Generate code <span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="2" id="genereteInv1" name="generateInv1" value="<?php echo $generateInv1; ?>"  >
        </div>
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full",
                                "", 1, "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Invoice Method <span style="color: red;"></span>*</label>
            <?php
                createCombo("SELECT '1' as id, 'Payment' as info UNION
                            SELECT '2' as id, 'Down Payment' as info;", $methodeId, "", "methodeId", "id", "info","",21,"select2combobox100");
            ?>           
        </div> 
      
    </div>
    <br>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label> Original Invoice <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="originalInv" name="originalInv" value="<?php echo $originalInv ?>">
        </div>

        <div class="span3 lightblue">
            <label>Input Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="inputDate" value = "<?php echo $inputDate; ?>" name="inputDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div class="span3 lightblue">
            <label>Request Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="reqDate" value = "<?php echo $reqDate; ?>" name="reqDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invDate" value = "<?php echo $invDate; ?>" name="invDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
     
        <div class="span3 lightblue"  >
            <label>Tax Invoice No</label>
            <input type="text"  tabindex="" id="taxInv" name="taxInv" value="<?php echo $taxInv ?>">
        </div>
        <div class="span3 lightblue" >
            <label>Tax Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="taxDate" value = "<?php echo $taxDate; ?>" name="taxDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span9 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks"
                    name="remarks"><?php echo $remarks; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>><?php echo $buttonText ?></button>
            <button class="btn" type="button" onclick="back()">BACK</button>
        </div>
    </div>
</form>
