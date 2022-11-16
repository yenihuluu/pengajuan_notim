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
		
		$('#searchVendorId').change(function() {
            resetPONo('poNO');
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('searchVendorId').value != '') {
                resetPONo(' ');
                setPONo($('select[id="searchVendorId"]').val());
            }
        });
	});
		
	function resetPONo(text) {
        document.getElementById('poNo').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('poNo').options.add(x);
    }
    
    function setPONo(vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getNumberPO',
                    vendorId: vendorId
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
                        document.getElementById('poNo').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('poNo').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('poNo').options.add(x);
                    }
                    
                   // if(type == 1) {
                  //      document.getElementById('freightId').value = freightId;
                  //  }
                }
            }
        });
    }
	
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/po-detail-report.php', {
                stockpileId: $('select[id="searchStockpileId"]').val(), 
                vendorId: $('select[id="searchVendorId"]').val(),
				poNo: $('select[id="poNo"]').val(),				
                periodFrom: $('input[id="searchPeriodFrom"]').val(),
                periodTo: $('input[id="searchPeriodTo"]').val()
            }, iAmACallbackFunction2);
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
	 
        $(".select2combobox100").select2({
            width: "250%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
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
                <label class="control-label" for="searchStockpileId">Stockpile</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", "", "", "searchStockpileId", "stockpile_id", "stockpile_full", 
                                "", 1, "", 7);
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchVendorId">Vendor</label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT DISTINCT v.vendor_id AS vendor_id, concat(v.vendor_code, ' - ', v.vendor_name) as vendor_name
                    FROM vendor v
                    INNER JOIN contract con
                        ON con.vendor_id = v.vendor_id
                    INNER JOIN stockpile_contract sc
                        ON sc.contract_id = con.contract_id
                    WHERE 1=1", "", "", "searchVendorId", "vendor_id", "vendor_name", 
                                "", 3, "select2combobox100", 7);
                    ?>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="poNo">PO No </label>
                <div class="controls">
                    <?php
						createCombo("", "", "", "poNo", "po_no", "po_no", "", 1, "select2combobox100", 1, "multiple");
					?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodFrom">Receive Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="5" id="searchPeriodFrom" name="searchPeriodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="searchPeriodTo">Receive Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="6" id="searchPeriodTo" name="searchPeriodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
<!--            <div class="control-group">
                <label class="control-label" for="searchStatus">Status</label>
                <div class="controls">
                    <?php 
//                    createCombo("SELECT 0 AS id, 'Open' AS info UNION
//                                SELECT 1 AS id, 'Closed' AS info UNION
//                                SELECT 2 AS id, 'Outstanding' AS info", "", "", "searchStatus", "id", "info", 
//                                "", 7, "", 7);
                    ?>
                </div>
            </div>-->
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>