<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

// <editor-fold defaultstate="collapsed" desc="Variable for Currency Data">

$currencyId = '';
$currencyCode = '';
$currencyName = '';
$isCountryCurrency = '';
$isPurchaseCurrency = '';
$isSalesCurrency = '';
$isReportCurrency = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['currencyId']) && $_POST['currencyId'] != '') {
    
    $currencyId = $_POST['currencyId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Currency Data">
    
    $sql = "SELECT cur.*
            FROM currency cur
            WHERE cur.currency_id = {$currencyId}
            ORDER BY cur.currency_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $currencyCode = $rowData->currency_code;
        $currencyName = $rowData->currency_name;
        $isCountryCurrency = $rowData->is_country_currency;
        $isPurchaseCurrency = $rowData->is_purchase_currency;
        $isSalesCurrency = $rowData->is_sales_currency;
        $isReportCurrency = $rowData->is_report_currency;
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
        
        $("#currencyDataForm").validate({
            rules: {
                currencyCode: "required",
                currencyName: "required",
                isCountryCurrency: "required",
                isPurchaseCurrency: "required",
                isSalesCurrency: "required",
                isReportCurrency: "required"
            },
            messages: {
                currencyCode: "Currency code is a required field.",
                currencyName: "Currency name is a required field.",
                isCountryCurrency: "Country currency is a required field.",
                isPurchaseCurrency: "Purchase currency is a required field.",
                isSalesCurrency: "Sales currency is a required field.",
                isReportCurrency: "Report currency is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#currencyDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalCurrencyId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/currency.php', { currencyId: returnVal[3] }, iAmACallbackFunction2);

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
            startView: 2
        });
    });
</script>

<form method="post" id="currencyDataForm">
    <input type="hidden" name="action" id="action" value="currency_data" />
    <input type="hidden" name="currencyId" id="currencyId" value="<?php echo $currencyId; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Currency Code <span style="color: red;">*</span></label>
            <input type="text" class="span6" tabindex="1" id="currencyCode" name="currencyCode" value="<?php echo $currencyCode; ?>" maxlength="3">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Currency Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="currencyName" name="currencyName" value="<?php echo $currencyName; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Country Currency? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $isCountryCurrency, '', "isCountryCurrency", "id", "info", 
                "", 3);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Purchase Currency? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $isPurchaseCurrency, '', "isPurchaseCurrency", "id", "info", 
                "", 4);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Sales Currency? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $isSalesCurrency, '', "isSalesCurrency", "id", "info", 
                "", 4);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Report Currency? <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Yes' as info UNION
                    SELECT '0' as id, 'No' as info;", $isReportCurrency, '', "isReportCurrency", "id", "info", 
                "", 5);
            ?>
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
