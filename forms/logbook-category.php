<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$boolShow = false;
$logbookCategoryId = '';

if(isset($_POST['logbookCategoryId']) && $_POST['logbookCategoryId'] != '') {
    $logbookCategoryId = $_POST['logbookCategoryId'];
    $boolShow = true;
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function(){	//executed after the page has loaded
        loadConfig();

        $('#myTab a').click(function (e) {
            e.preventDefault();
            //alert(this);
            $("#successMsg").hide();
            $("#errorMsg").hide();

            $(this).tab('show');

            loadConfig();
        });



    });

    function loadConfig() {
        var url = $('#myTab .active a').attr('href');
        //alert(url)
        var menu = url.split('#');

        $.blockUI({ message: '<h4>Please wait...</h4>' });
        //alert(menu[1]);
        $('#' + menu[1]).load('tabs/' + menu[1] + '.php', {logbookCategoryId: $('input[id="generalLogbookCategoryId"]').val()});
    }

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/logbook-category.php', {}, iAmACallbackFunction);
    }

</script>

<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#logbook-category-data" data-toggle="tab">Logbook Category Details</a></li>
	<?php
    if($boolShow) {
    ?>
    <li><a href="#logbook-sub-category-data" data-toggle="tab">Sub Category</a></li>
    <?php
    }
    ?>
</ul>

<div class="tab-content">
    <input type="hidden" id="generalLogbookCategoryId" value="<?php echo $logbookCategoryId; ?>" />
    <div class="alert">
        <b>Info:</b> <span style="color: red;">*</span> is required field.
    </div>
    <div class="alert fade in alert-success" id="successMsg" style="display:none;">
        Success Message
    </div>
    <div class="alert fade in alert-error" id="errorMsg" style="display:none;">
        Error Message
    </div>

    <div class="tab-pane active" id="logbook-category-data">

    </div>
	<?php
    if($boolShow) {
    ?>
    <div class="tab-pane" id="logbook-sub-category-data">

    </div>

    <?php
    }
    ?>

</div>