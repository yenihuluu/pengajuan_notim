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
$disabledButton = '';
$whereProperty = '';
$readonly1 = '';

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
    
    if($detailId != ''){
        $sql = "SELECT apd.*, sp.stockpile_id, gv.general_vendor_name, mc.account_id, ap.prediction_code,
                        dp.invoice_no, mc.currency as mstCurr,
                        CASE WHEN mc.qty_type = 1 THEN 'Vessel' 
                            WHEN mc.qty_type = 2 THEN 'Tongkang'
                        ELSE '' END AS qtyText, apd.status as statusDetail,
                        CASE WHEN mc.price_type = 1 THEN 'VAR' else 'FIX' END as priceType,
                        dp2.invMethod, dp2.invMax, dp2.invMin, dp2.invQty, dp2.invPrice, dp2.invAmount, dp2.invExRate, 
                        dp2.invoice_no2, dp2.inputDate, dp2.requestDate, dp2.invoiceDate, dp2.taxDate, dp2.invoice_tax, dp2.remarks, dp.dpAmount, apd.remarks_reject
                FROM accrue_prediction_detail apd 
                LEFT JOIN accrue_prediction ap ON ap.prediction_id = apd.prediction_id
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
                LEFT JOIN stockpile sp ON sp.stockpile_code = SUBSTRING(apd.generate_code_detai, 1, 3) 
                LEFT JOIN mst_costing mc ON mc.mst_costing_id = apd.mst_costing_id
                LEFT JOIN(
                    SELECT DISTINCT(inv.prediction_detail_id) AS detailId, inv.invoice_no, SUM(invd.amount)  as dpAmount
                    FROM invoice inv
                    LEFT JOIN invoice_detail invd ON inv.invoice_id = invd.invoice_id
                    WHERE inv.prediction_detail_id = {$detailId} AND inv.invoice_status != 3
                    GROUP BY inv.invoice_id
                ) AS dp ON dp.detailId = apd.prediction_detail_id
                LEFT JOIN(
                    SELECT inv.prediction_detail_id AS detailId, invd.invoice_method_detail AS invMethod, invd.max_charge AS invMax, invd.min_charge AS invMin, 
                    invd.qty AS invQty, invd.price AS invPrice,  invd.amount AS invAmount, invd.exchange_rate as invExRate,
                    inv.invoice_no2, DATE_FORMAT(inv.`input_date`, '%d/%m/%Y') AS inputDate, DATE_FORMAT(inv.`request_date`, '%d/%m/%Y') AS requestDate, 
                    DATE_FORMAT(inv.`invoice_date`, '%d/%m/%Y') AS invoiceDate, DATE_FORMAT(inv.`tax_date`, '%d/%m/%Y') AS taxDate, inv.invoice_tax, inv.remarks
                    FROM invoice inv
                    LEFT JOIN invoice_detail invd ON inv.invoice_id = invd.invoice_id
                    WHERE inv.prediction_detail_id = {$detailId} and inv.invoice_status != 3
                    ORDER BY inv.invoice_id DESC, invd.invoice_detail_id DESC LIMIT 1
                ) AS dp2 ON dp2.detailId = apd.prediction_detail_id
                WHERE prediction_detail_id  = {$detailId}";
    }
   
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        if($rowData->invMethod != ''){
            //HEADER
            $originalInv = $rowData->invoice_no2;
            $inputDate = $rowData->inputDate;
            $reqDate = $rowData->requestDate;
            $invDate = $rowData->invoiceDate;
            $taxInv = $rowData->invoice_tax;
            $taxDate = $rowData->taxDate;
            // $remarks = $rowData->remarks;
            $readonly = 'readonly';
            //DETAIL
            $maxCharge = number_format($rowData->invMax, 0, ".", ",");
            $minCharge = number_format($rowData->invMin, 0, ".", ",");
            $qty = number_format($rowData->invQty, 0, ".", ",");
            $price = number_format($rowData->invPrice, 0, ".", ",");
            // $totalAmount = number_format($rowData->invAmount, 0, ".", ",");
            $exchangeRate = number_format($rowData->invExRate, 0, ".", ",");  
            $dpAmount = number_format($rowData->dpAmount, 0, ".", ",");  
        }else{
            $maxCharge = number_format($rowData->max_charge, 0, ".", ",");
            $minCharge = number_format($rowData->min_charge, 0, ".", ",");
            $qty = number_format($rowData->qty, 0, ".", ",");
            $price = number_format($rowData->priceMT, 0, ".", ",");
            // $totalAmount = number_format($rowData->total_amount, 0, ".", ",");
            $exchangeRate = number_format($rowData->exchange_rate, 0, ".", ",");

    
        }
        $generateInv1 = $rowData->invoice_no;
        $detailCode = $rowData->generate_code_detai;
        $biaya = $rowData->cost_name;
        $generalVendor = $rowData->general_vendor_name;
        $inRupiah = number_format($rowData->in_rupiah, 0, ".", ",");
        $spRemarks = strtoupper($rowData->stockpile_remarks);
        $stockpileId = $rowData->stockpile_id;
        $headerId = $rowData->prediction_id;
        $accountId = $rowData->account_id;      
        $qtyText = $rowData->qtyText;
        $priceType = $rowData->priceType;
        $mstCurr = $rowData->mstCurr;
        $totalAmount = number_format($rowData->total_amount, 0, ".", ",");
        $codePrediksiDT = $rowData->generate_code_detai;
        $codePrediksi = $rowData->prediction_code;
        $statusDetail = $rowData->statusDetail;
        $reject_remarks = $rowData->remarks_reject;

        $buttonText = 'SUBMIT';
        $insert = 'INSERT';

        //Nilai Invoice masih ada
        if($dpAmount > 0){
            $disabledProperty = "disabled";
        }
        //Reject
        if($statusDetail == 3){
            $disabledButton = "disabled";
            $disabledProperty = "disabled";
        }
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
        $('#dpAmount').number(true, 2);
        $('#maxCharge').number(true, 2);
        $('#minCharge').number(true, 2);
        $('#qty').number(true, 2);
        $('#priceMT').number(true, 2);
        $('#totalAmount').number(true, 2);
        $('#exchangeRate').number(true, 2);

        //INSERT
        <?php if($invId == '' && $generateInv1 == ''){ ?>
            getGenerateCodeInv($('select[id="stockpileId"]').val());
        <?php }?>


        <?php if($exchangeRate > 1) { ?>
            $('#divexchange').show();
        <?php } else { ?>
            $('#divexchange').hide();
        <?php } ?>

        $('#methodId').change(function() {
            if(document.getElementById('methodId').value == 2) {
                $("#divdp").show();
                $("#divdp1").show();
                document.getElementById("qty").readOnly = true; 
                document.getElementById("priceMT").readOnly = true; 
            } else {
                $("#divdp").hide();
                $("#dpAmount").val(0);
                document.getElementById("qty").readOnly = false; 
                document.getElementById("priceMT").readOnly = false; 
            }
        });

        $("#InvPrediksiDataForm").validate({
            rules: {
                detailId: "required",
                detailCode: "required",
                methodId: "required",
                stockpileId: "required",
                inputDate: "required",
				reqDate: "required",
                invDate: "required"
               
            },
            messages: {
                detailId: "Detail Prediksi is a required field.",
                detailCode: "Detail Code is a required field.",
                methodId: "Methode Invoice is a required field.",
                stockpileId: "Stockpile is a required field.",
                inputDate: "required field.",
                reqDate: "required field.",
                invDate: "required field."
				
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    _method: 'INSERT',
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

        $(".qty").on("keydown keyup", function () {
            getValueUpdate(<?php echo $detailId; ?>, $('input[id="priceMT"]').val(), $('input[id="qty"]').val());
        });

        $(".priceMT").on("keydown keyup", function () {
            getValueUpdate(<?php echo $detailId; ?>, $('input[id="priceMT"]').val(), $('input[id="qty"]').val());
        });
    });
    
    function getValueUpdate(detailId, priceMT, qty) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getValueUpdate',
                detailId: detailId,
                priceMT: priceMT,
                qty: qty
            },
            success: function (data) {
                var returnVal = data.split('|');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    document.getElementById('maxCharge').value = returnVal[1];
                    document.getElementById('minCharge').value = returnVal[2];
                    document.getElementById('tAmount').value = returnVal[3];
                    document.getElementById('inRupiah').value = returnVal[4];
                }
            }
        });
    }

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
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

    function reject2() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'inv_prediksi_data',
                _method: 'REJECT',
                detailId: document.getElementById('detailId').value,
                headerId: document.getElementById('headerId').value, 
                invId: document.getElementById('invId').value, 
                reject_remarks: document.getElementById('reject_remarks').value
            },
            success: function (data) {
                console.log(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    if (returnVal[1] == 'OK') {
                        $('#dataContent').load('views/invoice_prediksi.php', { invId: returnVal[3] }, iAmACallbackFunction2);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }
</script>

<form method="post" id="InvPrediksiDataForm">
    <input type="hidden" name="action" id="action" value="inv_prediksi_data" />
    <input type="hidden" name="_method" value="<?php echo $insert; ?>">
    <input type="hidden" name="detailId" id="detailId" value="<?php echo $detailId; ?>" />
    <input type="hidden" name="detailCode" id="detailCode" value="<?php echo $detailCode; ?>" />
    <input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>" />
    <input type="hidden" name="headerId" id="headerId" value="<?php echo $headerId; ?>" />
    <input type="hidden" name="inRupiah" id="inRupiah" value="<?php echo $inRupiah; ?>" /> 
    <input type="hidden" name="mstCurr" id="mstCurr" value="<?php echo $mstCurr; ?>" />  
    <input type="hidden" name="dpamount1" id="dpAmount1" value="<?php echo $dpAmount; ?>" /> 

    
    <div class="row-fluid">
        <div class="span4 lightblue">
        <label>Kode prediksi : <span><?php echo $codePrediksi; ?></span></label>
        </div>

        <?php if($statusDetail == 3 ) { ?>
            <div class="span4 lightblue">
                <label style="color: red;"><center><b>REJECT</center></b></label>
            </div>
        <?php } ?>
    </div>

    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Generate code <span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="2" id="genereteInv1" name="generateInv1" value="<?php echo $generateInv1; ?>"  >
        </div>
        <div class="span3 lightblue">
            <label> Original Invoice <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" <?php echo $readonly ?> id="originalInv" name="originalInv" value="<?php echo $originalInv ?>">
        </div>
        <div class="span3 lightblue">
            <label>Input Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY"  id="inputDate" <?php echo $readonly ?> value = "<?php echo $inputDate; ?>" name="inputDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div class="span3 lightblue">
            <label>Request Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY"  tabindex="2" id="reqDate" <?php echo $readonly ?> value = "<?php echo $reqDate; ?>" name="reqDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>    
    </div>
    <div class="row-fluid">  
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
            <label> General Vendor <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="gVendor"  name="gVendor" value="<?php echo $generalVendor ?>">
        </div>
        <div class="span3 lightblue">
            <label>Invoice Method <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT '1' as id, 'Full Payment' as info UNION
                            SELECT '2' as id, 'Down Payment' as info;", $methodId, "", "methodId", "id", "info","",21,"select2combobox100");
            ?>           
        </div> 
        <div class="span3 lightblue">
            <label>Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="invDate" <?php echo $readonly ?> value = "<?php echo $invDate; ?>" name="invDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>  
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Account Name  <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT DISTINCT(a.account_id), CONCAT(a.account_no, ' - ', a.account_name) as fullName, a.* FROM account a 
                                WHERE a.account_type = 4 order by a.account_id ASC", $accountId, '', "accountId", "account_id", "fullName", 
                    "", 21, "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label> Biaya <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="biaya" name="biaya" value="<?php echo $biaya ?>">
        </div>
          <div class="span3 lightblue" >
            <label>Tax Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="taxDate" <?php echo $readonly ?> value = "<?php echo $taxDate; ?>" name="taxDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span3 lightblue"  >
            <label>Tax Invoice No</label>
            <input type="text"   class="span12" tabindex="2"  id="taxInv" <?php echo $readonly ?> name="taxInv" value="<?php echo $taxInv ?>">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label> Max Charge <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="maxCharge" name="maxCharge" value="<?php echo $maxCharge ?>">
        </div>
        <div class="span3 lightblue">
            <label> Min Charge <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="minCharge" name="minCharge" value="<?php echo $minCharge ?>">
        </div>
        <div class="span3 lightblue">
            <label> Qty <span style="color: black;"><?php echo $qtyText; ?></span></label>
            <input type="text" class="qty"  tabindex="2"  id="qty" name="qty" value="<?php echo $qty ?>">
        </div>
        <div class="span3 lightblue">
            <label> Price Type</label>
            <input type="text" class="qty" readonly tabindex="2"   id="priceType" name="priceType" value="<?php echo $priceType ?>">
        </div>
       
    </div>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label> Price / MT <span style="color: red;">*</span></label>
            <input type="text" class="priceMT"  tabindex="2"  id="priceMT" name="priceMT" value="<?php echo $price ?>">
        </div>
        <div class="span3 lightblue">
            <label> Total Amount <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="tAmount" name="tAmount" value="<?php echo $totalAmount ?>">
        </div>
        <div class="span3 lightblue" id="divdp" style="display: none;">
            <label id="divdp1"> Nilai Dp <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="dpAmount" name="dpAmount">
        </div>
        <div class="span3 lightblue"  id="divexchange" style="display: none;">
            <label> Exchange Rate <span style="color: red;">*</span></label>
            <input type="text" class="span5" tabindex="2"  id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate ?>">
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6 lightblue">
            <label>Stockpile Remarks</label>
            <textarea class="span12" readonly rows="3" tabindex="" id="spRemarks"
                    name="spRemarks"><?php echo $spRemarks; ?></textarea>
        </div>
        <div class="span6 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex=""  id="remarks"
                    name="remarks"><?php echo $remarks; ?></textarea>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disabledButton; ?> ><?php echo $buttonText ?></button>
            <button class="btn" type="button" onclick="back()">BACK</button>
        </div>
    </div>
</form>
<hr>



<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-warning"  <?php echo $disabledProperty; ?> id="reject1" onclick= "reject2()" style="margin: 0px;">Reject</button>
        </div>
    </div>  
</div>
