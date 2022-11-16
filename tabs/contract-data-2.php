<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'contract';

$readonlyProperty = '';
$readonly = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

$contractId = '';
$contractType = '';
$contract_no = '';
$vendorId = '';
$currencyId = '';
$price = '';
$qty = '';
$exchangeRate = '';
$contractSeq = '';
$notes = '';
$qty_rule = '';
// </editor-fold>

// If ID is in the parameter
if(isset($_POST['contractId']) && $_POST['contractId'] != '') {
    
    $contractId = $_POST['contractId'];
    
    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">
    
    $sql = "SELECT con.*, v.vendor_name, stockpile_name, cur.currency_code, pop.quantity AS qty_total, pop.currency_id, pop.stockpile_id,
(pop.quantity - (SELECT COALESCE(SUM(pc.quantity),0) FROM po_contract pc LEFT JOIN contract c ON pc.contract_id = c.contract_id WHERE pc.po_pks_id = pop.po_pks_id AND c.contract_status != 2)) AS balance, pop.`po_pks_id`,
DATE_FORMAT(con.entry_date, '%d/%m/%Y') AS contractDate
            FROM contract con
            LEFT JOIN po_contract poc ON poc.`contract_id` = con.`contract_id`
            LEFT JOIN po_pks pop ON pop.`po_pks_id` = poc.`po_pks_id`
            LEFT JOIN stockpile s ON s.`stockpile_id` = pop.`stockpile_id`
            LEFT JOIN vendor v ON v.`vendor_id` = con.`vendor_id`
            LEFT JOIN currency cur ON cur.`currency_id` = pop.`currency_id`
            WHERE con.contract_id = {$contractId}
            ORDER BY con.contract_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $contractType = $rowData->contract_type;
        $contract_no = $rowData->contract_no;
		$contract_no2 = $rowData->contract_no;
		$contractNo2 = substr($contract_no2, 0, -2);
        $vendorId = $rowData->vendor_id;
		$vendorName = $rowData->vendor_name;
        $currencyId = $rowData->currency_id;
		$currencyCode = $rowData->currency_code;
        $price = $rowData->price;
        $qty = $rowData->quantity;
		$qty_total = $rowData->qty_total;
        $exchangeRate = $rowData->exchange_rate;
        $generatedPoNo = $rowData->po_no;
		$notes = $rowData->notes;
		$balance = $rowData->balance;
		$stockpileName = $rowData->stockpile_name;
		$stockpile_id = $rowData->stockpile_id;
		$po_pks_id = $rowData->po_pks_id;
		$adjustment = $rowData->adjustment;
		$qty_rule = $rowData->qty_rule;
		$mutasiType = $rowData->mutasi_type;
		$contractDate = $rowData->contractDate;
		
		if($rowData->payment_status == 1){
		$readonly= ' readonly ';
		}
		
    }
    
    // </editor-fold>
    
} else {
    $generatedPoNo = "";
    if(isset($_SESSION['contract'])) {
        $contractType = $_SESSION['contract']['contractType'];
        $contract_no = $_SESSION['contract']['contract_no'];
		$contractNo2 = $_SESSION['contract']['contractNo2'];
        $vendorId = $_SESSION['contract']['vendorId'];
        $currencyId = $_SESSION['contract']['currencyId'];
        $price = $_SESSION['contract']['price'];
        $qty = $_SESSION['contract']['quantity'];
        $exchangeRate = $_SESSION['contract']['exchangeRate'];
        $stockpileId = $_SESSION['contract']['stockpileId'];
        $contractSeq = $_SESSION['contract']['contractSeq'];
		$notes = $_SESSION['contract']['notes'];
		$po_pks_id = $_SESSION['contract']['po_pks_id'];
		$adjustment = $_SESSION['contract']['adjustment'];
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
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
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
        if(strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }
    
    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
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
        
       $('#qty').number(true, 2);
	   $('#adjustment').number(true, 2);
        
      //  $('#price').number(true, 10);
        
       /* if(document.getElementById('vendorId').value == 'INSERT') {
            $('#vendorDetail').show();
            $('#vendorDetail2').show();
            $('#vendorDetail3').show();
        } else {
            $('#vendorDetail').hide();
            $('#vendorDetail2').hide();
            $('#vendorDetail3').hide();
        }*/
      /*  if(document.getElementById('po_pks_id').value != '') {
                    resetPo('po_pks_id',' ');
                    <?php
                   // if($_SESSION['contract']['po_pks_id'] != '') {
                    ?>
                    refreshPo(<?php echo $po_pks_id; ?>);
                    <?php
                  //  } else {
                    ?>
					 //setPo(0, $('select[id="po_pks_id"]').val(), 0);                            
                    <?php
                  //  }
					?>
				} */
        <?php
     //   if($generatedPoNo == "") {
        ?>
      /*  if(document.getElementById('contractType').value != "" ) {
            $.ajax({
                url: './get_data.php',
                method: 'POST',
                data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                success: function(data) {
                    document.getElementById('generatedPoNo').value = data;
                }
            });
        } else {
            document.getElementById('generatedPoNo').value = "";
        }*/
        <?php
       // }    
        ?>
       /* 
        if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
            $('#exchangeRate').hide();
        } else {
            $('#exchangeRate').show();
        }
        
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRate').hide();
            } else {
                $('#exchangeRate').show();
            }
        });
        */
        <?php
      //  if($generatedPoNo == "") {
        ?>
     /*   $('#contractSeq').change(function() {
            if(document.getElementById('contractType').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });
        
        $('#contractType').change(function() {
            if(document.getElementById('contractType').value != "") {
                $.ajax({
                    url: './get_data.php',
                    method: 'POST',
                    data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                    success: function(data) {
                        document.getElementById('generatedPoNo').value = data;
                    }
                });
            } else {
                document.getElementById('generatedPoNo').value = "";
            }
        });
        <?php
      //  }
        ?>
        
        $('#po_pks_id').change(function() {
            $('#summaryContract').hide();
			refreshPo($('select[id="po_pks_id"]').val());
               
            
        });*/
        
        $("#contractDataForm").validate({
            rules: {
                contractType: "required",
				qty_rule: "required"
                //vendorId: "required",
                //currencyId: "required",
                //exchangeRate: "required",
                //price: "required",
                //quantity: "required",
                //stockpileId: "required"
            },
            messages: {
                 contractType: "Contract Type is a required field.",
				qty_rule: "Quantity Rule is a required field."
                //vendorId: "Vendor is a required field.",
                //currencyId: "Currency is a required field.",
                //exchangeRate: "Exchange Rate is a required field.",
                //price: "Price is a required field.",
                //quantity: "Quantity is a required field.",
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#contractDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalContractId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/contract2.php', { contractId: returnVal[3] }, iAmACallbackFunction2);

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
	/*function refreshPo(po_pks_id) {
       // var ppnValue = 'NONE';
        //var pphValue = 'NONE';
        /*
        if(paymentMethod == 1) {
            if(ppn != 'NONE') {
                if(ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph != 'NONE') {
                if(pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        } 
        
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshPo',
                    po_pks_id: po_pks_id
                   // paymentMethod: paymentMethod,
                   // ppn: ppnValue,
                   // pph: pphValue
            },
            success: function(data){
                if(data != '') {
                    $('#summaryContract').show();
                    document.getElementById('summaryContract').innerHTML = data; 
					//refreshPo($('select[id="po_pks_id"]').val());
					generatedContractNo(document.getElementById('contract_no').value);
					<?php
                if($generatedPoNo == "") {
                ?>
                if(document.getElementById('contractType').value != "" ) {
					
                    $.ajax({
                        url: './get_data.php',
                        method: 'POST',
                        data: "action=getContractPO&contractType=" + document.getElementById('contractType').value + "&vendorId=" + document.getElementById('vendorId').value+ "&contractSeq=" + document.getElementById('contractSeq').value,
                        success: function(data) {
                            document.getElementById('generatedPoNo').value = data;
							
                        }
                    });
                } else {
                    document.getElementById('generatedPoNo').value = "";
                }
                <?php
                }
                ?>
                }
            }
        });
    }
	function generatedContractNo(contract_no) {
      
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getGeneratedContract',
                    contract_no: contract_no
                   // paymentMethod: paymentMethod,
                   // ppn: ppnValue,
                   // pph: pphValue
            },
            success: function(data){
                if(data != '') {
                   // $('#summaryContract').show();
                    document.getElementById('generatedContractNo').value = data; 
               
                }
            }
        });
    }*/
</script>

<form method="post" id="contractDataForm">
    <input type="hidden" name="action" id="action" value="contract_data" />
    <input type="hidden" name="contractId" id="contractId" value="<?php echo $contractId; ?>" />
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span4 lightblue">
            <label>Generated PO No</label>
            <input type="text" class="span12" readonly id="generatedPoNo" name="generatedPoNo" value="<?php echo $generatedPoNo; ?>">
        </div>
        <div class="span4 lightblue">
			<label>Contract No</label>
            <input type="text" class="span12" <?php echo $readonly;?> id="contractNo2" name="contractNo2" value="<?php echo $contractNo2; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Contract Date <span style="color: red;">*</span></label>
			<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="contractDate" name="contractDate" data-date-format="dd/mm/yyyy" class="datepicker" value="<?php echo $contractDate; ?>">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span12 lightblue">
        <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
        <tr>
        <th>Vendor</th>
        <th>Contract No.</th>
        <th>Stockpile</th>
        <th>Currency</th>
        <th>Unit Price</th>
        <th>Quantity</th>
        <th>Balance</th></tr>
        </thead>
        <tbody>
        <tr>
        <td style="text-align: right; width: 20%;"><?php echo $vendorName; ?></td>
        <td style="text-align: right; width: 20%;"><?php echo $contract_no; ?></td>
		<td style="text-align: right; width: 15%;"><?php echo $stockpileName ; ?></td>
		<td><?php echo $currencyCode; ?></td>
		<td style="text-align: right; width: 15%;"><?php echo number_format($price, 4, ".", ","); ?></td>
		<td style="text-align: right; width: 15%;"><?php echo number_format($qty_total, 2, ".", ","); ?></td>
		<td style="text-align: right; width: 15%;"><?php echo number_format($balance, 2, ".", ","); ?></td>
        <input type="hidden" class="span12" tabindex="" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>">
        <input type="hidden" class="span12" tabindex="" id="stockpile_id" name="stockpile_id" value="<?php echo $stockpile_id; ?>">
        <input type="hidden" class="span12" tabindex="" id="price" name="price" value="<?php echo $price; ?>">
        <input type="hidden" class="span12" tabindex="" id="currencyId" name="currencyId" value="<?php echo $currencyId; ?>">
        <input type="hidden" class="span12" tabindex="" id="contractType" name="contractType" value="<?php echo $contractType; ?>">
		<input type="hidden" class="span12" tabindex="" id="contractId" name="contractId" value="<?php echo $contractId; ?>">
        <input type="hidden" class="span12" tabindex="" id="contractNo" name="contractNo" value="<?php echo $contract_no; ?>">
        <input type="hidden" class="span12" tabindex="" id="balance" name="balance" value="<?php echo $balance; ?>">
        
		</tr>
			</tbody>
            </table>
			</div>
    
    </div>
   
        
    <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
            <label>Quantity</label>
            <input type="text" class="span12" <?php echo $readonly;?> tabindex="" id="qty" name="qty" value="<?php echo $qty; ?>">
        </div>
        <div class="span3 lightblue">
            <label>Adjustment</label>
            <input type="text" class="span12" <?php echo $readonly;?> tabindex="" id="adjustment" name="adjustment" value="<?php echo $adjustment; ?>">
        </div>
       <div class="span3 lightblue">
            <label>Quantity Rules <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Send Weight' as info UNION
                    SELECT '1' as id, 'Netto Weight' as info UNION
                    SELECT '2' as id, 'Lowest Weight' as info;", $qty_rule, '', "qty_rule", "id", "info", 
                "", 12);
            ?>
        </div>
		<div class="span3 lightblue">
        <label>Mutasi Barang / Langsir <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $mutasiType, "", "mutasiType", "id", "info",
                "", "", "select2combobox100");
            ?>

        
    </div>
   
	<div class="row-fluid">  
        <div class="span8 lightblue">
            <label>Notes <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" <?php echo $readonly;?> tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
        </div>
        
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
