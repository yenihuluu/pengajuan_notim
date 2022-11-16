<?php

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
    $(document).ready(function(){	//executed after the page has loaded

        $('#closeExportButton').click(function (e){
            e.preventDefault();
            
            $('#exportButton').attr("disabled", true);
            $('#closeExportButton').attr("disabled", true);
            
            $('#exportModal').modal('hide');
            $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);
        });
        
        $('#exportForm').on('submit', function(e) {
            e.preventDefault();

            $('#exportButton').attr("disabled", true);
            $('#closeExportButton').attr("disabled", true);
            //$.blockUI({ message: '<h4>Please wait...</h4>' }); 

            $(this).ajaxSubmit({
                success:  showResponse //call function after success
            });
        });

    });

   /* function showResponse(responseText, statusText, xhr, $form)  { 
        // for normal html responses, the first argument to the success callback 
        // is the XMLHttpRequest object's responseText property 

        // if the ajaxSubmit method was passed an Options Object with the dataType 
        // property set to 'xml' then the first argument to the success callback 
        // is the XMLHttpRequest object's responseXML property 

        // if the ajaxSubmit method was passed an Options Object with the dataType 
        // property set to 'json' then the first argument to the success callback 
        // is the json data object returned by the server 

//        alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
//            '\n\nThe output div should have already been updated with the responseText.'); 

        var returnVal = responseText.split('|');
//        alert(returnVal);        
        if(parseInt(returnVal[3])!=0)	//if no errors
        {
//            alert(responseText);
            alertify.set({ labels: {
                ok     : "OK"
            } });
            alertify.alert(returnVal[2]);
            if(returnVal[1] == 'OK') {
                //show success message
                $('#exportModal').modal('hide');
                $('#dataContent').load('contents/contract.php', {}, iAmACallbackFunction2);

//                document.getElementById('successMsg').innerHTML = returnVal[2];
//                $("#successMsg").show();

            } else {
                //show error message
//                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
//                $("#modalErrorMsg").show();
                $('#exportButton').attr("disabled", false);
                $('#closeExportButton').attr("disabled", false);
            }
        }

    } */

</script>

<form id="exportForm" method="post" enctype="multipart/form-data" action="reports/management-report-xls.php">

	 <input type="hidden" id="stockpileId" name="stockpileId" value="7" />
    <input type="text" id="periodTo" name="periodTo" value="2015-11-12" />
    <button class="btn btn-success">Download XLS</button>
</form>