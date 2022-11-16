<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$idcateg = $_POST['idcateg'];
$stockpile = $_POST['stockpile'];
$periodFrom = $_POST['periodFrom'];
$periodTo = $_POST['periodTo'];

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "call SP_CashFlow_ShipmentCostDTL (STR_TO_DATE('{$periodFrom}','%d/%m/%Y'),STR_TO_DATE('{$periodTo}','%d/%m/%Y'), '{$idcateg}', '{$stockpile}')";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


// </editor-fold>

?>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
        <tr>
        
            
            <th style="text-align:center">Category Name</th>
			<th style="text-align:center">Name</th>
            <th style="text-align:center">Invoice No</th>
            <th style="text-align:center">Account</th>
            <th style="text-align:center">Stockpile</th>
			<th style="text-align:center">Amount</th>
			<th style="text-align:center">Remark</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
			

                
        ?>
        <tr>
         
            
            <td><?php echo $rowData->categname; ?></td>
			<td><?php echo $rowData->Ctgname; ?></td>
			<td><?php echo $rowData->InvoiceNo; ?></td>
			<td><?php echo $rowData->Account; ?></td>
			<td><?php echo $rowData->Stockpile; ?></td>
			<td style="text-align: right;"><?php echo number_format($rowData->Amount, 2, ".", ","); ?></td>
			<td><?php echo $rowData->Remarks; ?></td>
			
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
