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

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">

$vendorId = '';
$vendorCode = '';
$vendorName = '';
$vendorAddress = '';
$npwp = '';
$npwp_name = '';
$bankName = '';
$branch = '';
$accountNo = '';
$beneficiary = '';
$swiftCode = '';
$taxable = '';
$ppn = '';
$pph = '';
$active = '';
$nik = '';
$vendorGroupId = '';
$buttonText = '';
$codeCosting_id = $_POST['codeCosting_id'];

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['costingId']) && $_POST['costingId'] != '') {
    
    $costingId = $_POST['costingId'];
    
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
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
    $sql = "SELECT 
            mc.*
            FROM mst_costing_detail mc
            WHERE mc.mcd_id = {$costingId}
            ORDER BY mc.mcd_id ASC";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $stockpileId = $rowData->stockpile_id;
        $generalVendor = $rowData->general_vendor_id;
        $accountId = $rowData->account_id;
        $tipeBiayaId = $rowData->cost;
        $priceType = $rowData->price_type;
        $chargeCategory = $rowData->charge_category;
        $maxType = $rowData->max_type;
        $minType = $rowData->min_type;
        $maxCharge =  number_format($rowData->max_charge, 2, ".", ","); 
        $minCharge =  number_format($rowData->min_charge, 2, ".", ","); 
        $maxChargeP = $rowData->max_chargeP;
        $minChargeP = $rowData->min_chargeP;
        $priceMT =  number_format($rowData->priceMT, 2, ".", ","); 
        $currency = $rowData->currency;
        $exchangeRate =  number_format($rowData->exchange_rate, 2, ".", ","); 
        $qtyType = $rowData->qty_type;
        $uom1 = $rowData->uom;
        $active = $rowData->active;
    }
    $buttonText = 'UPDATE';
}else{
    $buttonText ='SUBMIT';
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
      // echo "<option value=''>-- Please Select --</option>";
        if($setvalue == 0) {
            echo "<option value='' selected>-- Please Select --</option>";
        } else {
            echo "<option value='0'>-- Please Select --</option>";
        }
       
    } else if($empty == 2) {
        echo "<option value='0'>NONE</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    echo "</SELECT>";
}


?>

<script type="text/javascript">
    $(document).ready(function(){
		$("select.select2combobox100").select2({
            width: "100%"
        });

        <?php if($costingId != ''){ ?>
            getAccountPrediksi(<?php echo $tipeBiayaId ?>);
            <?php if($currency != 1){ ?>
                $('#divexchange').show();
            <?php } ?>

            <?php if($priceType != 2){ ?>
                $('#divmax').show();
                $('#divmaxP').show();
                $('#divmin').show();
                $('#divminP').show();
                $('#divmaxType').show();
                $('#divminType').show();
            <?php } ?>

            <?php if($maxType == 0){ ?>
                document.getElementById('maxCharge').readOnly = true;
                document.getElementById('maxChargeP').readOnly = true;
            <?php } ?>

            <?php if($minType == 0){ ?>
                document.getElementById('minCharge').readOnly = true;
                document.getElementById('minChargeP').readOnly = true;
            <?php } ?>
        <?php } else { ?>
            $('#maxChargeP').val(0);
            $('#maxCharge').val(0);
            $('#minCharge').val(0);
            $('#minChargeP').val(0);

            document.getElementById('maxCharge').readOnly = true;
            document.getElementById('maxChargeP').readOnly = true;
            document.getElementById('minCharge').readOnly = true;
            document.getElementById('minChargeP').readOnly = true;
        <?php }?>
           
        $('#priceMT').number(true, 2);
        $('#exchangeRate').number(true, 0);
        $('#maxCharge').number(true, 2);
        $('#minCharge').number(true, 2);

        $('#currency').change(function() {
            $('#divexchange').hide();
            if(document.getElementById('currency').value == 1) {
                $('#exchangeRate').val(1);
            } else if(document.getElementById('currency').value != 1){
                $('#divexchange').show();
            }else{
                $('#divexchange').hide();
                $('#exchangeRate').val(0);
            }
        });

        $('#cost').change(function() {
            getAccountPrediksi($('select[id="cost"]').val());
        });

        //READ-ONLY when tipe VAR
        $('#maxType').change(function() {
            if(document.getElementById('maxType').value == 0){
                document.getElementById('maxCharge').readOnly = true;
                document.getElementById('maxChargeP').readOnly = true;
            }else{
                document.getElementById('maxCharge').readOnly = false;
                document.getElementById('maxChargeP').readOnly = false;
            }
        });

        $('#minType').change(function() {
            if(document.getElementById('minType').value == 0){
                document.getElementById('minCharge').readOnly = true;
                document.getElementById('minChargeP').readOnly = true;
            }else{
                document.getElementById('minCharge').readOnly = false;
                document.getElementById('minChargeP').readOnly = false;
            }
        });

        $('#priceType').change(function() {
                 $('#divmax').hide();
                $('#divmaxP').hide();
                $('#divmin').hide();
                $('#divminP').hide();
                $('#divmaxType').hide();
                $('#divminType').hide();
            if(document.getElementById('priceType').value == 2) {
                $('#divmax').hide();
                $('#divmaxP').hide();
                $('#divmin').hide();
                $('#divminP').hide();
                $('#divmaxType').hide();
                $('#divminType').hide();
            } else{
                $('#divmax').show();
                $('#divmaxP').show();
                $('#divmin').show();
                $('#divminP').show();
                $('#divmaxType').show();
                $('#divminType').show();
            }
        });
    

    });

    function getAccountPrediksi(biayaId) {
        $.ajax({
           url: 'get_data.php',
           method: 'POST',
           data: { action: 'getAccountPrediksi',
                biayaId: biayaId
            },
           success: function(data){
               var returnVal = data.split('|');
               if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('accountName').value = returnVal[1];
                    document.getElementById('accountId').value = returnVal[2];
               }
           }
       });
    }
</script>

    <input type="hidden" name="costingId" id="costingId" value="<?php echo $costingId; ?>" /> <!-- Detail Master nya -->
    <input type="hidden" name="codeCosting_id" id="codeCosting_id" value="<?php echo $codeCosting_id; ?>" /> <!-- Master nya -->


    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Stockpile (Remarks) <span style="color: red;">*</span></label>
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
            <label>general vendor  <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM general_vendor order by general_vendor_name ASC", $generalVendor, '', "generalVendor", "general_vendor_id", "general_vendor_name", 
                "", 21, "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label> Biaya <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT a.id, a.tipe_biaya, a.* FROM mst_tipe_biaya a order by a.tipe_biaya ASC", $tipeBiayaId, '', "cost", "id", "tipe_biaya", 
                "", 21, "select2combobox100");
            ?>
        </div>
     
    </div>
<br>
    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Account Name  <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" readonly id="accountName" name="accountName">
            <input type="hidden" class="span12" tabindex="3" id="accountId" name="accountId">
        </div>
        <div class="span3 lightblue">
            <label>Price Type <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT '1' as id, 'Var' as info UNION
                            SELECT '2' as id, 'Fix' as info;", $priceType, "", "priceType", "id", "info","",21,"select2combobox100");
            ?>     
        </div>

     
    </div>

    <!-- MAXIMUM -->
    <div class="row-fluid">  
        <div class="span3 lightblue" id="divmaxType"  style="display: none;">
            <label>Maximum Type </label>
            <?php
                createCombo("SELECT '1' as id, 'Lowest' as info UNION
                            SELECT '2' as id, 'Highest' as info;", $maxType, "", "maxType", "id", "info","",21,"select2combobox100", 2);
            ?>     
        </div>

        <div class="span3 lightblue" id="divmax"  style="display: none;">
                <label>Max Charge <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="3" id="maxCharge" name="maxCharge" value="<?php echo $maxCharge ?>">
        </div>

        <div class="span3 lightblue" id="divmin"  style="display: none;">
                <label>Max Charge % <span style="color: red;">*</span></label>
                <input type="text" class="span12" tabindex="3" id="maxChargeP" name="maxChargeP" value="<?php echo $maxChargeP ?>">
        </div>
    </div>
  
    <!-- MINIMUM -->
    <div class="row-fluid">  
        <div class="span3 lightblue" id="divminType"  style="display: none;">
            <label>Minimum Type</label>
            <?php
                createCombo("SELECT '1' as id, 'Lowest' as info UNION
                            SELECT '2' as id, 'Highest' as info;", $minType, "", "minType", "id", "info","",21,"select2combobox100", 2);
            ?>     
        </div>

        <div class="span3 lightblue" id="divmaxP"  style="display: none;"> 
            <label>Min Charge <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="minCharge" name="minCharge" value="<?php echo $minCharge ?>">
        </div>
      

        <div class="span3 lightblue" id="divminP"  style="display: none;"> 
            <label>Min Charge % <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="minChargeP" name="minChargeP" value="<?php echo $minChargeP ?>">
        </div>
    </div>

    <div class="row-fluid"> 
        <div class="span3 lightblue">
            <label> Price <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="priceMT" name="priceMT" value="<?php echo $priceMT ?>">
        </div>
        <div class="span3 lightblue">
            <label>Currency <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM currency", $currency, '', "currency", "currency_id", "currency_code", 
                "", 21, "select2combobox100");
            ?>
        </div>
		<div class="span3 lightblue" id="divexchange"  style="display: none;">
            <label>Exchange Rate</label>
            <input type="text" class="span7" tabindex="13" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate ?>">
        </div>
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>Qty Type<span style="color: red;">*</span></label>
            <?php
                 createCombo("SELECT '1' as id, 'Vessel' as info UNION
                    SELECT '2' as id, 'Tongkang' as info UNION
                 SELECT '3' as id, 'Timbang Darat' as info UNION
                 SELECT '4' as id, 'Volume' as info UNION
                 SELECT '5' as id, 'Others' as info ;", $qtyType, "", "qtyType", "id", "info","",21,"select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
          <label>UOM <span style="color: red;">*</span></label>
          <?php
          
          createCombo("SELECT * FROM uom", $uom1, '', "uom1", "idUOM", "uom_type", 
              "", 21, "select2combobox100");
          ?>
      </div>
        <div class="span3 lightblue">
            <label>Active<span style="color: red;">*</span></label>
            <?php
                 createCombo("SELECT '0' as id, 'Non Active' as info UNION
                 SELECT '1' as id, 'Active' as info;", $active, "", "active", "id", "info","",21,"select2combobox100");
            ?>
        </div>
  </div>

