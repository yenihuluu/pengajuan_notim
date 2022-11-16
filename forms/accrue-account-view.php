<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$accrueId = $_POST['accrueId'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT gl.*, apd.journal_status, DATE_FORMAT(apd.cancel_jurnal_date, '%d/%m/%Y') AS cancelDate, 
			DATE_FORMAT(gl.gl_date, '%d/%m/%Y') AS journalDate FROM gl_report gl
		LEFT JOIN accrue_prediction_detail apd on apd.prediction_detail_id = gl.accrue_id WHERE accrue_id IN ({$accrueId}) AND regenerate = 0";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


  /*$sql = "SELECT * FROM invoice WHERE invoice_id = {$invoiceId}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if($result !== false && $result->num_rows == 1) {
        $row = $result->fetch_object();
        $invoiceNo = $row->invoice_no;
		//$po_no = $rowData->po_no;
    }*/
// </editor-fold>

?>

<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            <th style="width: 40%;">Journal No</th>
            <th style="width: 40%;">Account Name</th>
			<th style="width: 20%;">Account No</th>
			<th style="width: 20%;">Journal/Cancel Date</th>
            <th>Debit</th>
            <th>Credit</th>
            
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
			

                
        ?>
        <tr>
         
            <td style="width: 40%;"><?php echo $rowData->jurnal_no; ?></td>
            <td style="width: 40%;"><?php echo $rowData->account_name; ?></td>
			<td style="width: 20%;"><?php echo $rowData->account_no;  ?></td>
			<?php if($rowData->journal_status == 1){
				echo "<td>";
				echo $rowData->journalDate;
				echo "</td>";
			}else if($rowData->journal_status == 2){
				echo "<td>";
				echo $rowData->cancelDate;
				echo "</td>";
			} ?>
    
			
			<td style="text-align: right;"><?php echo number_format($rowData->debitAmount, 2, ".", ","); ?></td>
    
			
			<td style="text-align: right;"><?php echo number_format($rowData->creditAmount, 2, ".", ","); ?></td>
        </tr>
        <?php
            
            }
        } else {
        ?>
        <tr>
            <td colspan="7">
                No data to be shown.
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
    </div>
    <div class="span6 lightblue">
    </div>
</div>
