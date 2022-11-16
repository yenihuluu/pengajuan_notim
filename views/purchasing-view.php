<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$fileId = '';

if(isset($_POST['fileId']) && $_POST['fileId'] != '') {
    $fileId = $_POST['fileId'];
}else{
	echo "error";
}

?>
<script type="text/javascript">
     
    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $('#pageContent').load('views/purchasing.php', {}, iAmACallbackFunction);
    }
    
</script>

<body>
	<div class="row-fluid">
        <div class="span12 lightblue">            
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
	<div class="container">
	<iframe src="<?php echo $fileId; ?>" style="width:90%; height:600px;"></iframe>
	</div>
</body>
