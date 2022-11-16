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

$amount = '';

$module = '';

$stockpileId = '';

$coaId = '';
$jurnalNo = '';

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

if(isset($_POST['module']) && $_POST['module'] != '') {
    $module = $_POST['module'];
for ($i = 0; $i < sizeof($module); $i++) {
                        if($module_name == '') {
                            $module_name .= "'". $module[$i] ."'";
                        } else {
                            $module_name .= ','. "'". $module[$i] ."'";
                        }
                    }
			}
			

/*    
    if($module == 0) {
        $whereProperty .= " AND a.general_ledger_module = 'CONTRACT' ";
		$module_name = 'CONTRACT';
    } elseif($module == 1) {
        $whereProperty .= " AND a.general_ledger_module = 'NOTA TIMBANG' ";
		$module_name = 'NOTA TIMBANG';
    } elseif($module == 2) {
        $whereProperty .= " AND a.general_ledger_module = 'PAYMENT' ";
		$module_name = 'PAYMENT';
    } elseif($module == 3) {
        $whereProperty .= " AND a.general_ledger_module = 'INVOICE' ";
		$module_name = 'INVOICE';
    } elseif($module == 4) {
        $whereProperty .= " AND a.general_ledger_module = 'JURNAL MEMORIAL' ";
		$module_name = 'JURNAL MEMORIAL';
    } elseif($module == 5) {
        $whereProperty .= " AND a.general_ledger_module = 'PETTY CASH' ";
		$module_name = 'PETTY CASH';
    } elseif($module == 6) {
        $whereProperty .= " AND a.general_ledger_module = 'PAYMENT ADMIN' ";
		$module_name = 'PAYMENT ADMIN';
    }
}*/

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileId = $_POST['stockpileId'];
	
	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
	
	$stockpile_name = array();
	$sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id IN ({$stockpileIds})";
	$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	if($result !== false && $result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
		$stockpile_name[] = $row['stockpile_name'];
		
	/*	for ($i = 0; $i < sizeof($stockpile_name); $i++) {
                        if($stockpile_names == '') {
                            $stockpile_names .= "'". $stockpile_name[$i] ."'";
                        } else {
                            $stockpile_names .= ','. "'". $stockpile_name[$i] ."'";
                        }
                    }*/
				
	$stockpile_names =  "'" . implode("','", $stockpile_name) . "'";				
	}
	}

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
if(isset($_POST['jurnalNo']) && $_POST['jurnalNo'] != '') {

    $jurnalNo = $_POST['jurnalNo'];

}

?>
<script type="text/javascript">
 $(document).ready(function () {
	  var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/general-ledger-report.php', {
                    periodFrom: document.getElementById('periodFrom').value,
                    periodTo: document.getElementById('periodTo').value,
                     module: document.getElementById('module').value, 
                    stockpileId: document.getElementById('stockpileId').value,
                    coaId: document.getElementById('coaNos').value,
					jurnalNo: document.getElementById('jurnalNo').value
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
       
        <form class="form-horizontal" id="downloadxls" method="post" action="reports/general-ledger-report-xls-new.php" >
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="module" name="module" value="<?php echo $module_name; ?>" />
         <input type="hidden" id="stockpile_names" name="stockpile_names" value="<?php echo $stockpile_names; ?>" />
         <input type="hidden" id="stockpileId" name="stockpileId" value="<?php echo $stockpileIds; ?>" />
		 <input type="hidden" id="jurnalNo" name="jurnalNo" value="<?php echo $jurnalNo; ?>" />
            <div class="control-group">
               <label class="control-label" for="module_name">Module</label>
                <div class="controls">
                 <input type="text" readonly id="module_name" name="module_name" value="<?php echo $module_name; ?>" />
                </div>
            </div>
            <div class="control-group">
               <label class="control-label" for="stockpile_name">Stockpile</label>
                <div class="controls">
                 <input type="text" readonly id="stockpile_name" name="stockpile_name" value="<?php echo $stockpile_names; ?>" />
                </div>
            </div>
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
                <label class="control-label" for="jurnalNo2">Jurnal No</label>
                <div class="controls">
                  <input type="text" readonly id="jurnalNo2" name="jurnalNo2" value="<?php echo $jurnalNo; ?>" />
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