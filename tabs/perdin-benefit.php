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

// <editor-fold defaultstate="collapsed" desc="Variable for Account Data">

$benefitId = '';
$jenisBenefit = '';
$levelId = '';
$amount = '';
$keterangan = '';
$status = '';
$benefitType = '';
$account = '';



// </editor-fold>

// If ID is in the parameter
if(isset($_POST['benefitId']) && $_POST['benefitId'] != '') {
    
    $benefitId = $_POST['benefitId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Account Data">
    
    $sql = "SELECT a.*
            FROM perdin_benefit a
            WHERE a.benefit_id = {$benefitId}
            ORDER BY a.benefit_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        
		$benefitId = $rowData->benefit_id;
		$jenisBenefit = $rowData->jenis_benefit;
		$levelId = $rowData->level_id;
		$amount = $rowData->amount_benefit;
		$keterangan = $rowData->keterangan;
		$status = $rowData->status;
		$benefitType = $rowData->type;
		$accountId = $rowData->account_id;
        
    }
    
    // </editor-fold>
    
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
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){
		
		$("select.select2combobox100").select2({
            width: "100%"
        });

        $("select.select2combobox50").select2({
            width: "50%"
        });

        $("select.select2combobox75").select2({
            width: "75%"
        });
		
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
		$('#amount').number(true, 2);
		
		
        $("#benefitDataForm").validate({
            rules: {
                
				jenisBenefit: "required",
				levelId: "required",
				amount: "required",
				benefitType: "required",
				account: "required",
				status: "required"
            },
            messages: {
                
				jenisBenefit: "jenis benefit is a required field.",
				levelId: "level is a required field.",
				amount: "amount is a required field.",
				benefitType: "this is a required field.",
				account: "this is a required field.",
				status: "status is a required field."
                
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#benefitDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalBenefitId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/perdin-benefit.php', { benefitId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
            }
        });
		
		$('#benefitType').change(function() {
			if(document.getElementById('benefitType').value != '') {
			resetAccount(' ');
            setAccount(0, $('select[id="benefitType"]').val(), 0);
			} else {
                resetAccount(' Benefit Type ');
            }
        });
		
		<?php if($benefitId != '') {?>
            // alert('test');     
		setAccount(1, $('select[id="benefitType"]').val(), <?php echo $accountId; ?>);
		
		<?php }?>
    });
	
	
	function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }
    
    function setAccount(type, benefitType, accountId) {
		//alert(accountId);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountPerdin',
                    benefitType: benefitType
					
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
                        document.getElementById('accountId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountId').options.add(x);
                    }
				
				if(type == 1) {
                        $('#accountId').find('option').each(function(i,e){
                            if($(e).val() == accountId){
                                $('#accountId').prop('selectedIndex',i);
								
								  $("#accountId").select2({
                                    width: "100%",
                                    placeholder: accountId
                                });
                            }
                        });
				}
				
				
                }
            }
        });
    }
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

<form method="post" id="benefitDataForm">
    <input type="hidden" name="action" id="action" value="perdin_benefit_data" />
    <input type="hidden" name="benefitId" id="benefitId" value="<?php echo $benefitId; ?>" />
	<div class="row-fluid">  
        <div class="span4 lightblue">
           <label>Travel Expenses<span style="color: red;">*</span></label>
           <input type="text" class="span12" tabindex="3" id="jenisBenefit" name="jenisBenefit" value="<?php echo $jenisBenefit; ?>">
        </div>
        <div class="span4 lightblue">
		<label>Type<span style="color: red;">*</span></label>
		<?php 
        createCombo("SELECT '4' as id, 'Loading' as info UNION
                    SELECT '5' as id, 'Umum' as info UNION
					SELECT '6' as id, 'HO' as info;", $benefitType, "", "benefitType", "id", "info", 
                "", 11, "select2combobox100");?>
        </div>
        <div class="span4 lightblue">
			
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Level <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM perdin_level ORDER by level ASC", $levelId, '', "levelId", "level_id", "level", "", 1, "select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
           <label>Account<span style="color: red;">*</span></label>
		<?php createCombo("", $accountId, "", "accountId", "account_id", "account_full", "", "", "select2combobox100", 1);	?>
		 
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Amount <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="3" id="amount" name="amount" value="<?php echo $amount; ?>">
        </div>
        <div class="span4 lightblue">
          
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
					"", 6,"select2combobox100");
            ?>
        </div>
        <div class="span4 lightblue">
           
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Keterangan <span style="color: red;"></span></label>
            <textarea class="span12" rows="3" tabindex="4" id="keterangan" name="keterangan"><?php echo $keterangan; ?></textarea> 
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
