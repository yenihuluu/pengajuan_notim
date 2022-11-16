<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

// PATH

require_once '../assets/include/path_variable.php';



// Session

require_once PATH_INCLUDE.DS.'session_variable.php';



// Initiate DB connection

require_once PATH_INCLUDE.DS.'db_init.php';



$whereProperty = '';


$periodFrom = '';

$periodTo = '';
$coaId = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodFrom = $_POST['periodFrom'];

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.gl_date BETWEEN STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') AND STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {

    $periodFrom = $_POST['periodFrom'];

    $whereProperty .= " AND a.gl_date >= STR_TO_DATE('{$periodFrom}', '%d/%m/%Y') ";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {

    $periodTo = $_POST['periodTo'];

    $whereProperty .= " AND a.gl_date <= STR_TO_DATE('{$periodTo}', '%d/%m/%Y') ";

}

if(isset($_POST['coaId']) && $_POST['coaId'] != '') {

    $coaId = $_POST['coaId'];
	
	for ($i = 0; $i < sizeof($coaId); $i++) {
                        if($coaNos == '') {
                            $coaNos .= $coaId[$i];
                        } else {
                            $coaNos .= ','. $coaId[$i];
                        }
                    }
	
	/*$coaNo = array();
	$sql = "SELECT GROUP_CONCAT(account_no) AS account_no FROM account WHERE account_no IN ({$coaIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$coaNo[] = $row['account_no'];
		
				
	$coaNos =  "'" . implode("','", $coaNo) . "'";				
	}
	}*/
	

}


?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/general-ledger-report-saldo.php', {
                    periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value,
					coaId:document.getElementById('coaNos').value
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
       
        <form class="form-horizontal" id="downloadxls" method="post" action="reports/general-ledger-report-xls-new-saldo.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 
            <div class="control-group">
               <label class="control-label" for="coaNo">COA</label>
                <div class="controls">
                 <input type="text" readonly id="coaNos" name="coaNos" value="<?php echo $coaNos; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="module_name2">Period</label>
                <div class="controls">
                  <input type="text" readonly id="module_name2" name="module_name2" value="<?php echo $periodFrom .' - '. $periodTo; ?>" />
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