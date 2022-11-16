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

$userId = '';
$nama = '';
$levelId = '';
$noRek = '';
$nik = '';
$status = '';
$joinDate = '';
$dept = '';
$divisi = '';



// </editor-fold>

// If ID is in the parameter
if(isset($_POST['userId']) && $_POST['userId'] != '') {
    
    $userId = $_POST['userId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Account Data">
    
    $sql = "SELECT a.*,DATE_FORMAT(a.join_date, '%d/%m/%Y') AS joinDate
            FROM perdin_user a
            WHERE a.id_user = {$userId}
            ORDER BY a.id_user ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        
		$userId = $rowData->id_user;
		$nama = $rowData->nama;
		$levelId = $rowData->level_id;
		$noRek = $rowData->no_rek;
		$nik = $rowData->nik;
		$status = $rowData->status;
		$joinDate = $rowData->joinDate;
		$deptId = $rowData->dept_id;
		$divId = $rowData->div_id;
		$stockpile_id = $rowData->stockpile_id;
        
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
		
		  $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
		
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
		//$('#amount').number(true, 2);
		
		<?php if($deptId != '') {?>
                  
		setDivisi(1, $('select[id="deptId"]').val(), <?php echo $divId; ?>);
   
		<?php }?>
		
		$('#deptId').change(function() {
            resetDivisi(' ');
            
            if(document.getElementById('deptId').value != '') {
				setDivisi(0,$('select[id="deptId"]').val(),0);
            } 
        });
		
		
		function resetDivisi(text) {
        document.getElementById('divId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('divId').options.add(x);
    }
		
		function setDivisi(type, deptId, divId) {

			$.ajax({
            url: './get_data.php',
            method: 'POST',
            data: { action: 'getDivisi',
					deptId:deptId
                    //stockpileContractId: stockpileContractId,
                    //paymentMethod: paymentMethod,
                    //ppn: ppnValue,
                    //pph: pphValue
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
                        document.getElementById('divId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('divId').options.add(x);

                        $("#divId").select({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('divId').options.add(x);
                    }

				if(type == 1) {
                        $('#divId').find('option').each(function(i,e){
                            if($(e).val() == divId){
                                $('#divId').prop('selectedIndex',i);
								
								  $("#divId").select({
                                    width: "100%",
                                    placeholder: divId
                                });
                            }
                        });
				}

                    
                }
				//setContract(contract);
            }

        });

		}
		
		
        $("#userDataForm").validate({
            rules: {
                
				
				nama:"required",
				levelId:"required",
				noRek:"required",
				nik:"required",
				status:"required",
				joinDate:"required",
				deptId:"required",
				divId:"required",
				stockpile_id:"required"
            },
            messages: {
                
				nama:"This is a required field.",
				levelId:"This is a required field.",
				noRek:"This is a required field.",
				nik:"This is a required field.",
				status:"This is a required field.",
				joinDate:"This is a required field.",
				deptId:"This is a required field.",
				divId:"This is a required field.",
				stockpile_id:"This is a required field."
                
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#userDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalUserId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/perdin-user.php', { userId: returnVal[3] }, iAmACallbackFunction2);

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
            startView: 0
        });
    });
</script>

<form method="post" id="userDataForm">
    <input type="hidden" name="action" id="action" value="perdin_user_data" />
    <input type="hidden" name="userId" id="userId" value="<?php echo $userId; ?>" />
	<div class="row-fluid">  
        <div class="span4 lightblue">
           <label>Nama<span style="color: red;">*</span></label>
           <input type="text" class="span12" tabindex="3" id="nama" name="nama" value="<?php echo $nama; ?>">
        </div>
        <div class="span4 lightblue">
		   <label>NIK<span style="color: red;">*</span></label>
           <input type="text" class="span12" tabindex="3" id="nik" name="nik" value="<?php echo $nik; ?>">
        </div>
        <div class="span4 lightblue">
			
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Departement <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM perdin_departement ORDER by dept ASC", $deptId, '', "deptId", "dept_id", "dept", "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
           <label>Divisi <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", $divId, "divId", "divId", "divisi", "", 2);
            ?>
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Level <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT * FROM perdin_level ORDER by level ASC", $levelId, '', "levelId", "level_id", "level", "", 1);
            ?>
        </div>
        <div class="span4 lightblue">
          <label>No. Rekening<span style="color: red;">*</span></label>
           <input type="text" class="span12" tabindex="3" id="noRek" name="noRek" value="<?php echo $noRek; ?>">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Join Date <span style="color: red;">*</span></label>
            <input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="joinDate" name="joinDate" value="<?php echo $joinDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
        <div class="span4 lightblue">
            <label>Status <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '0' as id, 'Active' as info UNION
                    SELECT '1' as id, 'Inactive' as info;", $status, '', "status", "id", "info", 
                "", 6);
            ?>
        </div>
        <div class="span4 lightblue">
		</div>
    </div>
	<div class="row-fluid">  
        
        <div class="span4 lightblue">
		<label>Stockpile </label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile ORDER BY stockpile_name ASC", $stockpile_id, "", "stockpile_id", "stockpile_id", "stockpile_name", "", "", "", 1);
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
