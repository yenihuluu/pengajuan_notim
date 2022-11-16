<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT stockpile_id FROM user WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
       
	   $stockpile = $row->stockpile_id;
           	
    }
}

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

        $('#closeImportButton').click(function (e){
            e.preventDefault();
            
            $('#importButton').attr("disabled", true);
            $('#closeImportButton').attr("disabled", true);
            
            $('#importModal').modal('hide');
            $('#dataContent').load('contents/search-timbangan.php', {}, iAmACallbackFunction2);
        });
        
        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            $('#importButton').attr("disabled", true);
            $('#closeImportButton').attr("disabled", true);
            //$.blockUI({ message: '<h4>Please wait...</h4>' }); 

            $(this).ajaxSubmit({
                success:  showResponse //call function after success
            });
        });

    });

    function showResponse(responseText, statusText, xhr, $form)  { 
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
                $('#importModal').modal('hide');
                $('#dataContent').load('contents/search-timbangan.php', {}, iAmACallbackFunction2);

//                document.getElementById('successMsg').innerHTML = returnVal[2];
//                $("#successMsg").show();

            } else {
                //show error message
//                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
//                $("#modalErrorMsg").show();
                $('#importButton').attr("disabled", false);
                $('#closeImportButton').attr("disabled", false);
            }
        }

    } 

</script>

<form id="importForm" method="post" enctype="multipart/form-data" action="./data_processing.php">
    <input type="hidden" name="action" id="action" value="import_timbangan" />
	<input type="hidden" id="stockpile" name="stockpile" value="<?php echo $stockpile; ?>" />
    <!--<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="5000000">-->
    
    
    
    <!--<div class="row-fluid">-->
        <label class="control-label" for="imagefile">File (Acceptable format is XLS file) <span style="color: red;">*</span></label>
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="input-append">
                <div class="uneditable-input" style="min-width: 200px;">
                    <i class="icon-file fileupload-exists"></i> 
                    <span class="fileupload-preview"></span>
                </div>
                <span class="btn btn-file">
                    <span class="fileupload-new">Select file</span>
                    <span class="fileupload-exists">Change</span>
                    <input type="file" name="imagefile" id="imagefile" />
                </span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
            </div>
        </div>
    <!--</div>-->
    
    <div class="row-fluid">
        <button class="btn btn-success" id="importButton">Submit</button>
        <button class="btn btn-inverse" id="closeImportButton">Close</button>
    </div>
    
</form>