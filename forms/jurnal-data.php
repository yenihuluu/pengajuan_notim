<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'jurnalDetail';

$jurnalType = '';
$accountId = '';
$generatedJurnalNo = '';


if(isset($_SESSION['jurnalDetail'])) {
    $jurnalType = $_SESSION['jurnalDetail']['jurnalType'];
	$generatedJurnalNo = $_SESSION['jurnalDetail']['generatedJurnalNo'];
}
// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
		
        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
	} else if($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
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
$(document).ready(function() {
		$("select.select2combobox100").select2({
            width: "100%"
        });
        
        $("select.select2combobox50").select2({
            width: "50%"
        });
        
        $("select.select2combobox75").select2({
            width: "75%"
        });
});

</script>
<script type="text/javascript">


			 $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getJurnalNo'
                    //stockpilecontract_id: stockpilecontract_id,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
            },
            success: function(data){
                if(data != '') {
                    document.getElementById('generatedJurnalNo').value = data;
					//$('#addInvoice').hide();
					
                }
			setJurnalType(generatedJurnalNo);
            }
			
        });
function setJurnalType(generatedJurnalNo) {
        document.getElementById('jurnalType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('jurnalType').options.add(x);
        
       /*
            var x = document.createElement('option');
            x.value = '0';
            x.text = 'PKS Kontrak';
            document.getElementById('invoiceType').options.add(x);
            
            var x = document.createElement('option');
            x.value = '1';
            x.text = 'PKS Curah/Sales';
            document.getElementById('invoiceType').options.add(x);
            
            var x = document.createElement('option');
            x.value = '2';
            x.text = 'Freight Cost';
            document.getElementById('invoiceType').options.add(x);

            var x = document.createElement('option');
            x.value = '3';
            x.text = 'Unloading Cost';
            document.getElementById('invoiceType').options.add(x);
            */
            var x = document.createElement('option');
            x.value = '1';
            x.text = 'Debit';
            document.getElementById('jurnalType').options.add(x);
			
			var x = document.createElement('option');
            x.value = '2';
            x.text = 'Credit';
            document.getElementById('jurnalType').options.add(x);
            /*
            var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('invoiceType').options.add(x);
        */
        
       
        
        <?php
        if(isset($_SESSION['jurnalDetail']) && $_SESSION['jurnalDetail']['jurnalType'] != '') {
        ?>
        document.getElementById('jurnalType').value = <?php echo  $jurnalType; ?>;     
		
        <?php
        }
        ?>
    }
	

	
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
		
		
</script>
<script type="text/javascript">
$(document).ready(function() {
    //this calculates values automatically 
	
	$('#amount').number(true, 2);
	//$('#exchangeRate').number(true, 2);
   
});




</script>

<input type="hidden" id="action" name="action" value="jurnal_detail">


<div class="row-fluid">
    <div class="span4 lightblue">
        <label>Type</label>
		<?php createCombo("", $jurnalType, "", "jurnalType", "id", "info", "", "", "select2combobox100", 1);?>
    </div>
   
    <div class="span4 lightblue">
        <label>Account</label>
		<?php createCombo("SELECT b.account_id, CONCAT(b.account_no, ' - ', b.account_name) AS account_full FROM account b WHERE b.account_type != 0 
GROUP BY b.account_no ORDER BY b.account_no ASC", $accountId, "", "accountId", "account_id", "account_full", 
                    "", "", "select2combobox100");?>
    </div>
    
    <div class="span4 lightblue">
        <label>Stockpile</label>
		<?php createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", "", "", "stockpileId2", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);?>	
    </div>
</div>
<div class="row-fluid">
    
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT cur.*
                    FROM currency cur
                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
                    "", "", "select2combobox100");
            ?>
    </div>
   <div class="span4 lightblue" id="exchangeRate" style="display: none;">
            <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate; ?>">
        </div> 
        <div class="span4 lightblue">
         <label>Amount</label>
        <input type="text" class="span12"  tabindex="" id="amount" name="amount"  >
    </div> 
</div>


<div class="row-fluid">
	<div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span12" rows="1" tabindex="" id="notes"  name="notes"></textarea>
    </div>
</div>

<div class="row-fluid">
    
</div>


