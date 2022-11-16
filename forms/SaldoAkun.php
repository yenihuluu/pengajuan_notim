<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Variable for Unloading Cost Data">


$sa_id = $_POST['sa_id'];

// </editor-fold>

if(isset($_POST['sa_id']) && $_POST['sa_id'] != '') {
    $sa_id = $_POST['sa_id'];
    
    // <editor-fold defaultstate="collapsed" desc="Query for Unloading Cost Data">
    
    $sql = "SELECT * FROM saldo_awal
            WHERE sa_id = {$sa_id}
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $amount = $rowData->amount;
		$periode = $rowData->payment_no;
		$account_no = $rowData->account_no;
		$account_name = $rowData->account_name;
        
    }
    
    // </editor-fold>
}



?>
<script type="text/javascript">
 $(document).ready(function(){
	 
	 $('#amount').number(true, 2);
});
</script>
<input type="hidden" id="sa_id" name="sa_id" value="<?php echo $sa_id; ?>">
<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Periode <span style="color: red;">*</span></label>
        <input type="text" readonly class="span6" tabindex="5" id="periode" name="periode" value="<?php echo $periode; ?>">
    </div>
</div>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Account No <span style="color: red;">*</span></label>
        <input type="text" readonly class="span6" tabindex="5" id="account_no" name="account_no" value="<?php echo $account_no; ?>">
    </div>
</div>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Account Name <span style="color: red;">*</span></label>
        <input type="text" readonly class="span6" tabindex="5" id="account_name" name="account_name" value="<?php echo $account_name; ?>">
    </div>
</div>
<div class="row-fluid">   
    <div class="span12 lightblue">
        <label>Amount <span style="color: red;">*</span></label>
        <input type="text" class="span6" tabindex="5" id="amount" name="amount" value="<?php echo $amount; ?>">
    </div>
</div>



