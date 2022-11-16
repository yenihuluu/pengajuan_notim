<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {
    $vendorId = $_POST['vendorId'];
    
    //$disabledProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Stockpile Contract Data">
    
    $sql = "SELECT branch FROM vendor WHERE vendor_id = {$vendorId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $branchPKS = $rowData->branch;
        
    }
    
    // </editor-fold>
}


?>

<script type="text/javascript">
    $(document).ready(function(){	//executed after the page has loaded

        $('#closeEditButton').click(function (e){
            e.preventDefault();
            
            $('#EditButton').attr("disabled", true);
            $('#closeEditButton').attr("disabled", true);
            
            $('#editModal').modal('hide');
            $('#vendor-pks').load('tabs/vendor-pks.php', {}, iAmACallbackFunction2);
        });
        
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            $('#editButton').attr("disabled", true);
            $('#closeEditButton').attr("disabled", true);
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
                $('#editModal').modal('hide');
				
                $('#vendor-pks').load('tabs/vendor-pks.php', {}, iAmACallbackFunction2);

//                document.getElementById('successMsg').innerHTML = returnVal[2];
//                $("#successMsg").show();

            } else {
                //show error message
//                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
//                $("#modalErrorMsg").show();
                $('#editButton').attr("disabled", false);
                $('#closeEditButton').attr("disabled", false);
            }
        }

    } 
	
	

</script>

<form id="editForm" method="post" enctype="multipart/form-data" action="./data_processing.php">
    <input type="hidden" name="action" id="action" value="update_vendor_branch" />
	<input type="hidden" id="vendorId" name="vendorId" value="<?php echo $vendorId; ?>" />
    
	<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Branch<span style="color: red;">*</span></label>
        <input type="text" class="span9" tabindex="3" id="branchPKS" name="branchPKS" value="<?php echo $branchPKS; ?>">
    </div>
    
</div>
    
    <div class="row-fluid">
        <button class="btn btn-success" id="editButton">Update</button>
        <button class="btn btn-inverse" id="closeEditButton">Close</button>
    </div>
    
</form>