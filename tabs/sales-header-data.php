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

// <editor-fold defaultstate="collapsed" desc="Variable for Bank Data">

$salesHeaderId = '';
$dateFrom = '';
$dateTo = '';
$eta = '';
$salesNo = '';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['salesHeaderId']) && $_POST['salesHeaderId'] != '') {
    
    $salesHeaderId = $_POST['salesHeaderId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Bank Data">
    
    $sql = "SELECT sales_header_id,
			DATE_FORMAT(date_from, '%d/%m/%Y') as date_from,
			DATE_FORMAT(date_to, '%d/%m/%Y') as date_to,
			DATE_FORMAT(eta, '%d/%m/%Y') as eta,
			sales_no from sales_header
            WHERE sales_header_id = {$salesHeaderId}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $dateFrom = $rowData->date_from;
        $dateTo = $rowData->date_to;
        $eta = $rowData->eta;
		$salesNo = $rowData->sales_no;
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
		
        
        $("#salesHeaderDataForm").validate({
            rules: {
                dateFrom: "required",
                dateTo: "required",
                eta: "required",
                salesNo: "required"
            },
            messages: {
                dateFrom: "Date from is a required field.",
                dateTo: "Date to is a required field.",
                salesNo: "Sales No. is a required field.",
                eta: "ETA is a required field."
            },
            submitHandler: function(form) {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#salesHeaderDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('salesHeaderId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/sales-header.php', { salesHeaderId: returnVal[3] }, iAmACallbackFunction2);

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
</script>

<form method="post" id="salesHeaderDataForm">
    <input type="hidden" name="action" id="action" value="sales_header_data" />
    <input type="hidden" name="salesHeaderId" id="salesHeaderId" value="<?php echo $salesHeaderId; ?>" />
    <div class="row-fluid">  
       <div class="span4 lightblue">
		<label>Laycan From<span style="color: red;">*</span></label>
		   <input type="text"  placeholder="DD/MM/YYYY" tabindex="4" id="dateFrom" name="dateFrom" value="<?php echo $dateFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
		   </div>   
		   <div class="span4 lightblue">
		<label>Laycan To<span style="color: red;">*</span></label>
		   <input type="text"  placeholder="DD/MM/YYYY" tabindex="4" id="dateTo" name="dateTo" value="<?php echo $dateTo; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
		   </div>   
		   
    </div>
	<div class="row-fluid">  
	<div class="span4 lightblue">
		<label>ETA<span style="color: red;">*</span></label>
		   <input type="text"  placeholder="DD/MM/YYYY" tabindex="4" id="eta" name="eta" value="<?php echo $eta; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
		   </div>   
		   <div class="span4 lightblue">
            <label>Sales No </label>
            <input type="text" class="span12" tabindex="7" id="salesNo" name="salesNo" value="<?php echo $salesNo; ?>">
        </div>  
    </div>
    
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
