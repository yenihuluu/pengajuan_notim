<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if($empty == 7) {
        echo "<option value=''>-- All --</option>";
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
   /*     $('#searchStockpileId').change(function() {
            if(document.getElementById('searchPaymentType').value != '') {
                setVendor($('select[id="searchPaymentType"]').val(), $('select[id="searchStockpileId"]').val());
            }
        });
     */   
      /*  $('#searchPurchaseType').change(function() {
            if(document.getElementById('searchPaymentType').value != '') {
                setVendor($('select[id="searchPaymentType"]').val(), $('select[id="searchStockpileId"]').val(), $('select[id="searchPurchaseType"]').val());
            }
        });*/
        
       /* $('#searchPaymentType').change(function() {
            resetVendor(' Type ');
//            var stockpileId = 0;
//            var purchaseType = 0;
            
            if(document.getElementById('searchPaymentType').value != '') {
//                if($('select[id="searchStockpileId"]').val() != '') {
//                    stockpileId = $('select[id="searchStockpileId"]').val();
//                }
//                
//                if($('select[id="searchPurchaseType"]').val() != '') {
//                    purchaseType = $('select[id="searchPurchaseType"]').val();
//                }
                
//                alert($('select[id="searchTransactionType"]').val()+','+stockpileId+','+purchaseType);
                setVendor($('select[id="searchPaymentType"]').val(), $('select[id="searchStockpileId"]').val());
            }
        });
        */
        $('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
            if($('select[id="searchVendorId"]').val() != '') {
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/wht-report.php', {
                    //stockpileId: $('select[id="searchStockpileId"]').val(), 
                    //paymentType: $('select[id="searchPaymentType"]').val(), 
                    // purchaseType: $('select[id="searchPurchaseType"]').val(), 
                    vendorId: $('select[id="searchVendorId"]').val(), 
                    periodFrom: $('input[id="searchPeriodFrom"]').val(),
                    periodTo: $('input[id="searchPeriodTo"]').val()
                }, iAmACallbackFunction2);
            } else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Vendor is required field.");
            }
        });
    });
    
  /*  function resetVendor(text) {
        document.getElementById('searchVendorId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('searchVendorId').options.add(x);
    }
    
    function setVendor(paymentType, stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorReport',
                    paymentType: paymentType,
                    stockpileId: stockpileId,
                    
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if(returnValLength > 0) {
                        document.getElementById('searchVendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- All --';
                        document.getElementById('searchVendorId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('searchVendorId').options.add(x);
                    }
                }
            }
        });
    }*/
    
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
	 $(document).ready(function(){
           
        
        $(".select2combobox100").select2({
            width: "225px"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#amount').number(true, 2);
	});
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
           
            <div class="control-group">
                <label class="control-label" for="searchVendorId">Vendor <span style="color: red;">*</span></label>
                <div class="controls">
                    <?php
						createCombo("SELECT * FROM
(SELECT general_vendor_id AS vendor_id, general_vendor_name AS vendor_name, active FROM general_vendor
UNION ALL
SELECT freight_id AS vendor_id, freight_supplier AS vendor_name, active FROM freight
UNION ALL
SELECT vendor_id, vendor_name, active FROM vendor
UNION ALL
SELECT labor_id AS vendor_id, labor_name AS vendor_name, active FROM labor
UNION ALL
SELECT vendor_handling_id AS vendor_id, vendor_handling_name AS vendor_name, active FROM vendor_handling
) a
WHERE vendor_name IS NOT NULL AND vendor_name != ''
GROUP BY vendor_name ORDER BY vendor_name ASC", "", "", "searchVendorId", "vendor_name", "vendor_name", "", 7, "select2combobox100", "", "multiple");
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodFrom">Payment Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="5" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Payment Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>