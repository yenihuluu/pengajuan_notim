<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
 $_SESSION['menu_name'] = 'hdoa';

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
  /*  $(document).ready(function(){
		 <?php
		if(isset($_SESSION['hdoa'])) {
			?>
				if(document.getElementById('stockpileId').value != '') {
                     
					resetSupplier('freightId', ' ');
                    <?php
                    if($_SESSION['hdoa']['freightId'] != '') {
                    ?>
                    setSupplier('freightId', $('select[id="stockpileId"]').val(), <?php echo $_SESSION['hdoa']['freightId']; ?>);

                    <?php
                    if($_SESSION['hdoa']['vendorFreightId'] != '') {
                    ?>
                    setVendorFreight($('select[id="stockpileId"]').val(), <?php echo $_SESSION['hdoa']['freightId']; ?>, <?php echo $_SESSION['hdoa']['vendorFreightId']; ?>);
					
                    <?php
                    } else {
                    ?>
                    setVendorFreight($('select[id="stockpileId"]').val(), <?php echo $_SESSION['hdoa']['freightId']; ?>, 0);                            
                    <?php
                    }
                    } else {
                    ?>
                    setSupplier('freightId', $('select[id="stockpileId"]').val(), 0);
                    <?php
                    }
                    ?>
                    resetVendorFreight(' Freight ');
                }
				<?php
		}?>
		    
		$('#stockpileId').change(function() {
            resetSupplier('freightId', ' Stockpile ');
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('stockpileId').value != '') {
                resetSupplier(' ');
                setSupplier($('select[id="stockpileId"]').val(), 0);
            }
        });
        
		$('#freightId').change(function() {
            resetVendorFreight(' ');
			//resetBankDetail (' ');
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('freightId').value != '') {
                setVendorFreight($('select[id="stockpileId"]').val(), $('select[id="freightId"]').val(), 0);
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });
	});
		
		function resetSupplier(text) {
        document.getElementById('freightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightId').options.add(x);
    }
    
    function setSupplier(stockpileId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightReport',
                    stockpileId: stockpileId
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
                        document.getElementById('freightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightId').options.add(x);
                    }
                    
                   // if(type == 1) {
                  //      document.getElementById('freightId').value = freightId;
                  //  }
                }
            }
        });
    }
	

	
	function resetVendorFreight(text) {
        document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }
    
    function setVendorFreight(stockpileId, freightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorFreight',
                    stockpileId: stockpileId,
					freightId: freightId
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
                        document.getElementById('vendorFreightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('vendorFreightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorFreightId').options.add(x);
                    }
                    
                   // if(type == 1) {
                  //      document.getElementById('vendorFreightId').value = vendorFreightId;
                   // }
                }
            }
        });
    
}*/
</script>
<script type="text/javascript">
    $(document).ready(function(){
		
	
        
		$('#searchForm').submit(function(e){
            e.preventDefault();
//            alert('tes');
			// if($('select[id="stockpileId"]').val() != '') {
			 $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $('#dataContent').load('reports/invoiceDP-report.php', {
                periodFrom: $('input[id="periodFrom"]').val(),
				periodTo: $('input[id="periodTo"]').val(),
				//paymentFrom: $('input[id="paymentFrom"]').val(),
				//paymentTo: $('input[id="paymentTo"]').val(),
				generalVendorId: $('select[id="generalVendorId"]').val(),
				stockpileId: $('select[id="stockpileId"]').val()
				//vendorFreightId: $('select[id="vendorFreightId"]').val()
            }, iAmACallbackFunction2);
			/*} else {
                alertify.set({ labels: {
                    ok     : "OK"
                } });
                alertify.alert("Stockpile & Vendor OA is required field.");
            }*/
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
            width: "300%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        $('#amount').number(true, 2);
		

		
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" id="searchForm" method="post">
            <div class="control-group">
                <label class="control-label" for="searchStockpileId">Stockpile <span style="color: red;">*</span></label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                                FROM user_stockpile us
                                INNER JOIN stockpile s
                                    ON s.stockpile_id = us.stockpile_id
                                WHERE us.user_id = {$_SESSION['userId']}
                                ORDER BY s.stockpile_id ASC, s.stockpile_name ASC", "", "", "stockpileId", "stockpile_id", "stockpile_full", 
                                "", 1, "select2combobox100", 7, "multiple");
                    ?>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="searchStockpileId">Vendor</label>
                <div class="controls">
                    <?php
						createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                        FROM general_vendor gv WHERE gv.active = 1 ORDER BY gv.general_vendor_name ASC", "", "", "generalVendorId", "general_vendor_id", "general_vendor_name", "", 1, "select2combobox100", 7, "multiple");
					?>
                </div>
            </div>
			<div class="control-group">
                <label class="control-label" for="periodFrom">Payment Date From</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="periodFrom" name="periodFrom" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="periodTo">Payment Date To</label>
                <div class="controls">
                    <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="periodTo" name="periodTo" data-date-format="dd/mm/yyyy" class="datepicker" >
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn" id="preview">Preview</button>
                    <!--<button class="btn btn-success" id="generate">Generate XLS</button>-->
                </div>
            </div>
        </form>
    </div>
</div>