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
$pksSourceId = '';
$Amount = '';
$testSP = '';

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


if(isset($_POST['pksSourceId']) && $_POST['pksSourceId'] != '') {
    $pksSourceId = $_POST['pksSourceId'];
}

if(isset($_POST['Amount']) && $_POST['Amount'] != '') {
    $Amount = $_POST['Amount'];
}

$testSP = $_POST['stockpileId'];

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
		for ($i = 1; $i < 15; $i++) {
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
?>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    
    <div class="offset3 span3">
       
        <form class="form-horizontal" method="post" action="reports/pksSource-Collection-xls.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="Amount" name="Amount" value="<?php echo $Amount; ?>" />
         <input type="hidden" id="pksSourceId" name="pksSourceId" value="<?php echo $pksSourceId; ?>" />
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
               <label class="control-label" for="pksSourceId">PKS Source Type</label>
                <div class="controls">
                 <input type="text" readonly id="pksSourceId" name="pksSourceId" value="<?php echo $pksSourceId; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="module_name2">Period</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $periodFrom .' - '. $periodTo; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="coaNo">Amount</label>
                <div class="controls">
                 <input type="text" readonly id="Amount" name="Amount" value="<?php echo $Amount; ?>" />
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