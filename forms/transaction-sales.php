<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$allowSales = true;
$customerId = $_POST['customerId'];

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
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
        $('#currencyId').change(function() {
            if(document.getElementById('currencyId').value == 1 || document.getElementById('currencyId').value == '') {
                $('#exchangeRateSales').hide();
            } else {
                $('#exchangeRateSales').show();
            }
        });
        
        $('#customerId').change(function() {
            if(document.getElementById('customerId').value == 'INSERT') {
                $('#customerDetail1').show();
            } else {
                $('#customerDetail1').hide();
            }
        });
    });
</script>

<input type="hidden" id="action" name="action" value="transaction_sales_data">
<input type="hidden" id="customerId" name="customerId" value="<?php echo $customerId; ?>">

<!--<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Buyer <span style="color: red;">*</span></label>
        <?php
//        createCombo("SELECT cust.customer_id, cust.customer_name
//                FROM customer cust
//                ORDER BY cust.customer_name ASC", "", "", "customerId", "customer_id", "customer_name", 
//                "", 1, "span10", 1, "", $allowShipment);
        ?>
    </div>
</div>-->

<div class="row-fluid" id="customerDetail1" style="display: none;">
    <div class="span12 lightblue">
        <label>Customer Name <span style="color: red;">*</span></label>
        <input type="text" class="span10" tabindex="2" id="customerName" name="customerName">
    </div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Sales Agreement Code <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="3" id="salesNo" name="salesNo" maxlength="20">
    </div>
</div>

<div class="row-fluid">   
    <div class="span4 lightblue">
        <label>Currency <span style="color: red;">*</span></label>
        <?php
        createCombo("SELECT cur.*
                FROM currency cur
                ORDER BY cur.currency_code ASC", "", "", "currencyId", "currency_id", "currency_code", 
                "", 4, "span12");
        ?>
    </div>
    <div class="span8 lightblue" id="exchangeRateSales" style="display: none;">
        <label>Exchange Rate to IDR <span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="4" id="exchangeRate" name="exchangeRate">
    </div>
</div>

<div class="row-fluid">   
    <div class="span5 lightblue">
        <label>Price/KG <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="5" id="price" name="price">
    </div>
    <div class="span5 lightblue">
        <label>Quantity (KG) <span style="color: red;">*</span></label>
        <input type="text" class="span12" tabindex="6" id="quantity" name="quantity">
    </div>
    <div class="span2 lightblue"></div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Total Shipment <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="7" id="totalShipment" name="totalShipment">
    </div>
</div>

<div class="row-fluid">   
    <div class="span5 lightblue">
        <label>Loading</label>
        <!--<input type="text" class="span12" tabindex="8" id="portLoading" name="portLoading">-->
        <?php
        createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                FROM user_stockpile us
                INNER JOIN stockpile s
                    ON s.stockpile_id = us.stockpile_id
                WHERE us.user_id = {$_SESSION['userId']}
                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "portLoading", "stockpile_id", "stockpile_full", 
                "", 8, "span12");
        ?>
    </div>
    <div class="span5 lightblue">
        <label>Destination</label>
        <input type="text" class="span12" tabindex="9" id="destination" name="destination">
    </div>
    <div class="span2 lightblue"></div>
</div>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Notes</label>
        <textarea class="span10" rows="3" tabindex="8" id="notes" name="notes"></textarea>
    </div>
</div>


