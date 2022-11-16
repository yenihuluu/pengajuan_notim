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
$disableProperty = '';
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
    
    $sql = "SELECT gv.general_vendor_name, apd.cost_name, invd.max_charge, invd.min_charge, invd.qty, invd.price AS priceMT, invd.amount AS total_amount, invd.exchange_rate,
            invd.tamount_converted AS in_rupiah, apd.stockpile_remarks, inv.stockpileId AS stockpile_id, invd.account_id,
            DATE_FORMAT(inv.input_date, '%d/%m/%Y') AS inputDate, DATE_FORMAT(inv.request_date, '%d/%m/%Y') AS requestDate, DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') AS invoiceDate,
            DATE_FORMAT(inv.tax_date, '%d/%m/%Y') AS taxDate, apd.prediction_id,
            inv.prediction_detail_id, apd.generate_code_detai, inv.invoice_no, inv.invoice_method,
            inv.invoice_no2, inv.invoice_tax, inv.remarks, inv.invoice_status, ap.prediction_code, inv.remarks_reject,
            CASE WHEN mc.qty_type = 1 THEN 'Vessel' 
                WHEN mc.qty_type = 2 THEN 'Tongkang'
            ELSE '' END AS qtyText
            FROM invoice inv
            INNER JOIN invoice_detail invd ON invd.invoice_id = inv.invoice_id 
            INNER JOIN accrue_prediction_detail apd ON apd.prediction_detail_id = inv.prediction_detail_id
            LEFT JOIN accrue_prediction ap ON ap.prediction_id = apd.prediction_id
            LEFT JOIN general_vendor gv ON gv.general_vendor_id = apd.general_vendor_id
            LEFT JOIN stockpile sp ON sp.stockpile_code = inv.stockpileId
            LEFT JOIN mst_costing mc ON mc.mst_costing_id = apd.mst_costing_id
            WHERE inv.invoice_id  = {$invId}";
    
   
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $detailId = $rowData->prediction_detail_id;
        $detailCode = $rowData->generate_code_detai;
        $generateInv1 = $rowData->invoice_no;
        $methodId = $rowData->invoice_method;
        $originalInv = $rowData->invoice_no2;
        $inputDate = $rowData->inputDate;
        $reqDate = $rowData->requestDate;
        $invDate = $rowData->invoiceDate;
        $taxInv = $rowData->invoice_tax;
        $taxDate = $rowData->taxDate;
        $remarks = $rowData->remarks;
        $qtyText = $rowData->qtyText;
        $biaya = $rowData->cost_name;
        $buttonText = 'UPDATE';
        $insert = 'UPDATE';
        $readonly = 'readonly';
        $generalVendor = $rowData->general_vendor_name;
        $spRemarks = strtoupper($rowData->stockpile_remarks);

        $detailCode = $rowData->generate_code_detai;
        $headerCode = $rowData->prediction_code;
        $stockpileId = $rowData->stockpile_id;
        $headerId = $rowData->prediction_id;
        $accountId = $rowData->account_id;   
        $invStatus = $rowData->invoice_status;   

        if($invStatus == 3){
            $disableProperty = "disabled";
            $remarks_reject = $rowData->remarks_reject;

        }
        
    }

    //DETAIL INVOICE
    $sqlD = "SELECT CASE WHEN invd.invoice_method_detail = 1 THEN 'Full Payment' ELSE 'Down Payment' END AS method1, DATE_FORMAT(invd.entry_date, '%d/%m/%Y') AS entryDate,
                CASE WHEN inv.invoice_status = 0 THEN invd.amount 
                     WHEN inv.invoice_status = 1 THEN invdp.amount_payment ELSE 0 END AS tempDp,
	        invd.* FROM invoice_detail invd 
            INNER JOIN invoice inv ON inv.invoice_id = invd.invoice_id
            LEFT JOIN invoice_dp invdp ON invdp.invoice_detail_dp = invd.invoice_detail_id
            WHERE invd.invoice_id = {$invId} ORDER BY invoice_detail_id ASC";
    $resultD = $myDatabase->query($sqlD, MYSQLI_STORE_RESULT);

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
        $("#available").hide(); 

        //INSERT
        <?php if($invId == '' && $generateInv1 == ''){ ?>
            getGenerateCodeInv($('select[id="stockpileId"]').val());
        <?php } else {?>
            $("#available").show(); 
        <?php } ?>

        //UPDATE
        <?php if($invId != '' && $availableAmount == 0){ ?>
             $("#available").hide(); 
        <?php } ?>

        <?php if($exchangeRate > 1) { ?>
            $('#divexchange').show();
        <?php } else { ?>
            $('#divexchange').hide();
        <?php } ?>

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
                            if (returnVal[1] == 'OK') {
                                document.getElementById('GeneralInvId').value = returnVal[3];
                                $('#dataContent').load('views/search-invoice-prediksi.php', { invId: returnVal[3] }, iAmACallbackFunction2);
                            } 
     
                           
                        }
                    }
                });
            }
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

    function returned() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'inv_prediksi_data',
                _method: 'RETURN',
                detailId: document.getElementById('detailId').value,
                invId: document.getElementById('invId').value,
                headerId: document.getElementById('headerId').value,
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
                        $('#pageContent').load('views/search-invoice-prediksi.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

</script>

<form method="post" id="InvPrediksiDataForm">
    <input type="hidden" name="action" id="action" value="inv_prediksi_data"/>
    <input type="hidden" name="_method" value="<?php echo $insert; ?>">
    <input type="hidden" name="detailId" id="detailId" value="<?php echo $detailId; ?>" />
    <input type="hidden" name="detailCode" id="detailCode" value="<?php echo $detailCode; ?>" />
    <input type="hidden" name="invId" id="invId" value="<?php echo $invId; ?>" />
    <input type="hidden" name="headerId" id="headerId" value="<?php echo $headerId; ?>" />

    <div class="row-fluid">
        <div class="span4 lightblue">
        <label>Kode prediksi : <span><?php echo $headerCode; ?></span></label>
        </div>

        <?php if($invStatus == 3 ) { ?>
            <div class="span4 lightblue">
                <label style="color: red;"><center><b>RETURNED</center></b></label>
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
            <input type="text" class="span12" tabindex="2" id="originalInv" name="originalInv" value="<?php echo $originalInv ?>">
        </div>
        <div class="span3 lightblue">
            <label>Input Date <span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY"  id="inputDate" value = "<?php echo $inputDate; ?>" name="inputDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Request Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY"  tabindex="2" id="reqDate" value = "<?php echo $reqDate; ?>" name="reqDate" data-date-format="dd/mm/yyyy" class="datepicker" >
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
            <label> General Vendor <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="gVendor" name="gVendor" value="<?php echo $generalVendor ?>">
        </div>
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="invDate" value = "<?php echo $invDate; ?>" name="invDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>  
        <div class="span3 lightblue" >
            <label>Tax Invoie Date<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="taxDate" value = "<?php echo $taxDate; ?>" name="taxDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span3 lightblue"  >
            <label>Tax Invoice No</label>
            <input type="text"   class="span12" tabindex="2"  id="taxInv" name="taxInv" value="<?php echo $taxInv ?>">
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
        <div class="span3 lightblue">
            <label> Tipe Qty <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" readonly id="qtyText" name="qtyText" value="<?php echo $qtyText ?>">
        </div> 
    </div>

    <div class="row-fluid">
        <div class="span9 lightblue">
            <label>Stockpile Remarks</label>
            <textarea class="span12" readonly rows="3" tabindex="" id="spRemarks"
                    name="spRemarks"><?php echo $spRemarks; ?></textarea>
        </div>
        <!-- <div class="span6 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks"
                    name="remarks"><?php echo $remarks; ?></textarea>
        </div> -->
    </div>

<h4>Detail Invoice</h4>
<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <thead>
        <tr>
            <th>No</th>
            <th>Code</th>
            <th>Entry Date</th>
            <th>Invoice Method</th>
            <th>Max Charge</th>
            <th>Min Charge</th>
            <th>Qty </th>
            <th>Price/MT</th>
            <th>Amount</th>
            <th>Dp Invoice</th>
            <th>Notes</th>
           
        </tr>
    </thead>
    <tbody>
    <?php
        if($resultD !== false && $resultD->num_rows > 0) {
            $no = 1;
            while ($rowD = $resultD->fetch_object()) {
                if($invStatus == 3){
                    $tempDp = 0;
                }else{
                    $tempDp = $rowD->tempDp;
                }
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $rowD->entryDate; ?></td>
            <td><?php echo $rowD->entryDate; ?></td>
            <td><?php echo $rowD->method1; ?></td>
            <td><?php echo number_format($rowD->max_charge, 0, ".", ","); ?></td>
            <td><?php echo number_format($rowD->min_charge, 0, ".", ","); ?></td>
            <td><?php echo number_format($rowD->qty, 0, ".", ","); ?></td>
            <td><?php echo number_format($rowD->price, 0, ".", ","); ?></td>
            <td><?php echo number_format($rowD->amount, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempDp, 0, ".", ","); ?></td>
            <td><?php echo $rowD->notes; ?></td>
           
           
        </tr>
        <?php
            $no++;
            }
        } 
        ?>
    </tbody>
</table>


    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>><?php echo $buttonText ?></button>
            <button class="btn" type="button" onclick="back()">BACK</button>
        </div>
    </div>
</form>

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span9 lightblue">
        <label>Return Remarks</label>
        <textarea class="span12" rows="3" tabindex="" <?php echo $disableProperty; ?> id="reject_remarks"
                  name="reject_remarks"><?php echo $remarks_reject; ?></textarea>
        <input type="hidden" placeholder="DD/MM/YYYY" tabindex="2" id="returnDate" value = "<?php echo $todayDate; ?>" name="returnDate" data-date-format="dd/mm/yyyy" class="datepicker" >

    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger"  <?php echo $disableProperty; ?> id="canceled" onclick= "returned()" style="margin: 0px;">RETURN</button>
        </div>
    </div>  
</div>

