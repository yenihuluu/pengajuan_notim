<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Freight Cost Data">

//$shipmentId = '';
//$transactionId = '';
$exchangeRate = '';
$price = '';


// </editor-fold>

if(isset($_POST['salesId']) && $_POST['salesId'] != '') {
    $salesId = $_POST['salesId'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Freight Cost Data">
    
$sql = "SELECT sl.sales_id, sl.exchange_rate, sl.price FROM sales sl WHERE sl.sales_id = {$salesId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
       
        //$shipmentId = $rowData->shipmentId;
        //$transactionId = $rowData->transactionId;
        $exchangeRate = $rowData->exchange_rate;
        $price = $rowData->price;
        
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
        echo "<option value=''>-- Please Select if Applicable --</option>";
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
    $(document).ready(function(){
		$('#exchangeRate').number(true, 2);
		$('#price').number(true, 2);
		
    });
</script>


<input type="hidden" id="salesId" name="salesId" value="<?php echo $salesId; ?>">

<div class="row-fluid"> 
    <div class="span12 lightblue">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="4" id="exchangeRateUpdate" name="exchangeRateUpdate" value="<?php echo $exchangeRate; ?>">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Price/KG <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="5" id="priceUpdate" name="priceUpdate" value="<?php echo $price; ?>">
    </div>
</div>



