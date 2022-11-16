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
$accountId='';
$groupName='';
$groupitemId='';

// <editor-fold defaultstate="collapsed" desc="Variable for Vehicle Data">


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['groupitemId']) && $_POST['groupitemId'] != '') {
    
    $groupitemId = $_POST['groupitemId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vehicle Data">
    
    $sql = "select g.group_name, a.account_id 	
		from master_groupitem g left join 
		account a on a.account_id=g.account_id 
            WHERE g.idmaster_groupitem = {$groupitemId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$groupName = $rowData->group_name;
		$accountId = $rowData->account_id;
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
		/*
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountItem'
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
				
				               
                }
            }
        });
		*/
        $("#groupitemDataForm").validate({
            rules: {
                groupName: "required",
				accountId: "required",
            },
            messages: {
                groupName: "group name is a required field.",
				accountId: "account is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#groupitemDataForm").serialize(),
                    success: function(data) {
                        //window.alert(data)
						
						var returnVal = data.split('|');
						
                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('groupitemId').value = returnVal[3];
                                
                                $('#dataContent').load('contents/groupitem.php', { groupitemId: returnVal[3] }, iAmACallbackFunction2);

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
	
	$('#accountId').change(function() {
            if(document.getElementById('accountId').value != '') {
				 document.getElementById('groupName').value=
					 $(this).find('option:selected').text();
            } 
		});
        
	
</script>

<form method="post" id="groupitemDataForm">
    <input type="hidden" name="action" id="action" value="groupitem_data" />
    <input type="hidden" name="groupitemId" id="groupitemId" value="<?php echo $groupitemId; ?>" />
	 
    <div class="row-fluid">  
		
	<div class="span4 lightblue">
        <label>Account</label>
		<?php
            createCombo("SELECT account_id,account_name FROM account order by account_name asc", $accountId, "", "accountId", "account_id", "account_name", "", "", "select2combobox100", 1);
            ?>
	</div>
    
        <div class="span4 lightblue">
            <label>Group Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" readonly id="groupName" name="groupName" value="<?php echo $groupName; ?>">
		
        </div>
       
    </div>
	 
	<div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
   
</form>
