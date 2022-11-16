<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'dailyPks';

$date = new DateTime();
$paymentDate = $date->format('d/m/Y');
$stockpile_id = '';
$periode = '';
$shipment_id = '';
$screenedStock = '';
$sprayedStock = '';
$unscreenedStock = '';
$sales_add_id = '';
$notes = '';

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
    $(document).ajaxStop($.unblockUI);
           
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#amount').number(true, 2);
		
		$('#screenedStock').number(true, 2);
	   	$('#sprayedStock').number(true, 2);
		$('#unscreenedStock').number(true, 2);
		
	$(document).ready(function(){
	   
	   $('#stockpile_id').change(function() {
			if(document.getElementById('stockpile_id').value != '') {
			resetSalesId(' ');
            setSalesId(0, $('input[id="periode"]').val(), $('select[id="stockpile_id"]').val(), 0);
			} else {
                resetSalesId(' Sales ');
				
            }
        });
		
		<?php
        if(isset($_SESSION['dailyPks'])) {
        ?>         
            if(document.getElementById('periode').value != '' && document.getElementById('stockpile_id').value != '') {
                resetSalesId(' ');
                <?php
                if($_SESSION['dailyPks']['sales_id'] != '') {
                ?>
                setSalesId(1, $('input[id="periode"]').val(), $('select[id="stockpile_id"]').val(), <?php echo $_SESSION['dailyPks']['sales_id']; ?>);
                <?php
                } else {
                ?>
                setSalesId(0, $('input[id="periode"]').val(), $('select[id="stockpile_id"]').val(), 0);
				
                <?php
                }
                ?>
                
            } else {
                 resetSalesId(' Sales ');
				
            }
            
        <?php
        
		}
        ?>
		
	function resetSalesId(text) {
        document.getElementById('sales_id').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('sales_id').options.add(x);
    }
    
    function setSalesId(type, periode, stockpile_id) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getDestination',
					periode: periode,
                    stockpile_id: stockpile_id
					
            },
            success: function(data){
               //alert(data); 
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
                        document.getElementById('sales_id').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('sales_id').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('sales_id').options.add(x);
                    }
				
                if(type == 1) {
                        $('#sales_id').find('sales_id').each(function(i,e){
                            if($(e).val() == sales_id){
                                $('#sales_id').prop('selectedIndex',i);
                            }
                        });
				}
                }
            }
        });
    }
	
	 $("#dailyPksForm").validate({
            rules: {
              	periode: "required",
                stockpile_id: "required",
                sales_id: "required",
                
            },
            messages: {
               	periode: "Periode is a required field",
                stockpile_id: "Stockpile is a required field",
                sales_id: "Destination is a required field",
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#dailyPksForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('sales_add_id').value = returnVal[3];
                                
                                $('#dataContent').load('views/input-dailyPks.php', { sales_add_id: returnVal[3] }, iAmACallbackFunction2);

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
            minViewMode: 1,
            todayHighlight: true,
            autoclose: true,
            startView: 1
        });
    });
</script>

<form method="post" id="dailyPksForm">
    <input type="hidden" name="action" id="action" value="dailyPks_data" />
    <input type="hidden" name="sales_add_id" id="sales_add_id" value="<?php echo $sales_add_id; ?>" />
    <div class="row-fluid">   
        <div class="span4 lightblue">
            <label>Periode <span style="color: red;">*</span></label>
          <input type="text" placeholder="MM/YYYY" tabindex="" id="periode" name="periode"  value="<?php echo $periode; ?>" data-date-format="mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
           <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpile_id, "", "stockpile_id", "stockpile_id", "stockpile_full", 
                    "", "", "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
        	<label>Destination <span style="color: red;">*</span></label>
             <?php createCombo("", $sales_id, "", "sales_id", "id", "info", "", "", "select2combobox100", 2);	?>
        </div>
    </div>
    </br>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Screened Stock (Kg)</label>
            <input type="text" class="span12" tabindex="" id="screenedStock" name="screenedStock" value="<?php echo $screenedStock; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Screened + Sprayed Stock (Kg)</label>
            <input type="text" class="span12" tabindex="" id="sprayedStock" name="sprayedStock" value="<?php echo $sprayedStock; ?>">
        </div>
        <div class="span4 lightblue">
        <label>Unscreened Stock (Kg)</label>
            <input type="text" class="span12" tabindex="" id="unscreenedStock" name="unscreenedStock" value="<?php echo $unscreenedStock; ?>">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Less: Shipments (Kg)</label>
            <input type="text" class="span12" tabindex="" id="less_shipment" name="less_shipment" value="<?php echo $less_shipment; ?>">
        </div>
        <div class="span4 lightblue">
            <label>Less: Local Sales (Kg)</label>
            <input type="text" class="span12" tabindex="" id="less_local" name="less_local" value="<?php echo $less_local; ?>">
        </div>
        <div class="span4 lightblue">
        <label>Less: Susut (Moisture Loss) (Kg)</label>
            <input type="text" class="span12" tabindex="" id="less_susut" name="less_susut" value="<?php echo $less_susut; ?>">
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
         	<label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>   
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