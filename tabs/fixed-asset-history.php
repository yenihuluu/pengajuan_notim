<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$fixedAssetId = '';

// If ID is in the parameter
if(isset($_POST['fixedAssetId']) && $_POST['fixedAssetId'] != '') {
    
    $fixedAssetId = $_POST['fixedAssetId'];
    
    ?>

    <script type="text/javascript">
        // unblock when ajax activity stops 
        $(document).ajaxStop($.unblockUI);

        $(document).ready(function(){	//executed after the page has loaded
            
            $('#tabContent3').load('contents/fixed-asset-history.php', { fixedAssetId: <?php echo $fixedAssetId; ?> }, iAmACallbackFunction3);

            
        });

        function iAmACallbackFunction3() {
            $('#tabContent3').fadeIn();
        }
    </script>
     
    <div id="tabContent3">
        
    </div>
 
    <?php

}

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';

