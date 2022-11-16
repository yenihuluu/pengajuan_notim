<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';

$whereProperty1 = '';
$periodFrom = '';
$periodTo = '';
$stockpileId = '';
$value = '';
$baseOn = '';
$temp = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND tr.transaction_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $whereProperty1 .= " AND tr.transaction_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodTo = $_POST['periodTo'];
    $whereProperty1 .= " AND tr.transaction_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";
}


if(isset($_POST['baseOn']) && $_POST['baseOn'] != '') {
    $baseOn = $_POST['baseOn'];
    if($baseOn == 'daily'){
        $temp = $periodFrom;
    }else{
        $temp = $periodFrom.'-'.$periodTo;
    }
}

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {
    $stockpileId = $_POST['stockpileId'];
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $stockpileId[$i];
                        } else {
                            $stockpileIds .= ','. $stockpileId[$i];
                        }
                    }
    
    if($stockpileIds == ''){
        $stockpile_names = 'All';
		for ($i = 1; $i <= 18; $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= $i;
                        } else {
                            $stockpileIds .= ','. $i;
                        }
                    }
    
    }else{
        $stockpile_name = array();
        $sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if($result !== false && $result->num_rows > 0){
            while($row = mysqli_fetch_array($result)){
            $stockpile_name[] = $row['stockpile_name'];
                    
            $stockpile_names =  "'" . implode("','", $stockpile_name) . "'";
            $spName = implode($stockpile_name);				
        }
	}
    }
	

}


if(isset($_POST['value']) && $_POST['value'] != '') {
    $value = $_POST['value'];
}
?>

<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/dwm-report.php', {
					 periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value,
                    // stockpileId: document.getElementById('stockpileIds').value, 
                    value: document.getElementById('value').value,
                    baseOn: document.getElementById('baseOn').value
				}, iAmACallbackFunction2);
            }, 1000);
        });

    });
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    
    <div class="offset3 span3">
       
        <form class="form-horizontal" method="post" id="downloadxls" action="reports/dwm_report_xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="value" name="value" value="<?php echo $value; ?>" />
         <input type="hidden" id="baseOn" name="baseOn" value="<?php echo $baseOn; ?>" />
         <input type="hidden" id="stockpile_names" name="stockpile_names" value="<?php echo $stockpile_names; ?>" />
         <input type="hidden" id="stockpileIds" name="stockpileIds" value="<?php echo $stockpileIds; ?>" />
         <input type="hidden" id="spName" name="spName" value="<?php echo $spName; ?>" />
            <div class="control-group">
               <label class="control-label" for="stockpile_name">Stockpile</label>
                <div class="controls">
                 <input type="text" readonly id="stockpile_name" name="stockpile_name" value="<?php echo $stockpile_names; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="module_name2">Date</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $temp; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="coaNo">Value</label>
                <div class="controls">
                 <input type="text" readonly id="value" name="value" value="<?php echo $value; ?>" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button class="btn btn-success">Download XLS</button>
                   
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    
                </div>
            </div>
        </form>
    </div>
</div>