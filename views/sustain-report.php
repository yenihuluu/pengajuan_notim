<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Sustain Report';

?>

<script type="text/javascript">
    $(document).ready(function(){
        $('#dataSearch').load('searchs/sustain-report.php');
    });
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<div class="alert fade in alert-success" id="successMsgAll" style="display:none;">
    Success Message
</div>
<div class="alert fade in alert-error" id="errorMsgAll" style="display:none;">
    Error Message
</div>

<div id="dataSearch">
    
</div>

<div id="dataContent">
    
</div>

