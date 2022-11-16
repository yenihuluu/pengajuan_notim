<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';

$vendorId = '';

$stockpileId = '';


if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
	
	$stockpileNames = array();
	$sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
	
				
	$stockpileNames =  "'" . implode("','", $stockpile_name) . "'";				
	}
	}

}

if(isset($_POST['vendorId']) && $_POST['vendorId'] != '') {

    $vendorId = $_POST['vendorId'];
	
	for ($i = 0; $i < sizeof($vendorId); $i++) {
                        if($vendorIds == '') {
                            $vendorIds .= "'". $vendorId[$i] ."'";
                        } else {
                            $vendorIds .= ','. "'". $vendorId[$i] ."'" ;
                        }
                    }
	
	$vendorNames = array();
	$sql = "SELECT vendor_name FROM vendor WHERE vendor_id IN ({$vendorIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$vendorName[] = $row['vendor_name'];
		
				
	$vendorNames =  "'" . implode("','", $vendorName) . "'";				
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
       
        <form class="form-horizontal" method="post" action="reports/purchasing-summary-xls.php" >
        
            <div class="control-group">
               <label class="control-label" for="vendorName">Vendor</label>
                <div class="controls">
                 <input type="text" readonly id="vendorName" name="vendorName" value="<?php echo $vendorNames; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="stockpileName">Stockpile</label>
                <div class="controls">
                 <input type="text" readonly id="stockpileName" name="stockpileName" value="<?php echo $stockpileNames; ?>" />
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