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

$module_name = 'All';

$stockpileId = '';

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
}




?>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
       
        <form class="form-horizontal" method="post" action="reports/daily-jurnal-report-xls.php" id="dailyJournalForm">
         <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    	 <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
		 <input type="hidden" id="module" name="module" value="<?php echo $module; ?>" />
            <div class="control-group">
               <label class="control-label" for="module_name">Module</label>
                <div class="controls">
                 <input type="text" readonly id="module_name" name="module_name" value="<?php echo $module_name; ?>" />
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