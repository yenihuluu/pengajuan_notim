<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$userId = '';

// If ID is in the parameter
if(isset($_POST['userId']) && $_POST['userId'] != '') {
    
    $userId = $_POST['userId'];
    
    $whereProperty = " AND um.user_id = ". $userId ." ";
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
    $(document).ready(function(){	//executed after the page has loaded
        
        $("#userModuleDataForm").validate({

            submitHandler: function(form) {
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#userModuleDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[3]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#user-module-data').load('tabs/user-module-data.php', {userId: $('input[id="generalUserId"]').val()});
                            } 
                        }
                    }
                });
            }
        });
        
    });
    
    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }
    
    
</script>

<form method="post" id="userModuleDataForm">
    <p>
        <b>Please choose the privilege(s) for using the system:</b>
    </p>
        
    <div class="row-fluid">  
        <table class="table table-bordered table-striped" style="font-size: 9pt;" id="checkTable">
            <thead>
                <tr>
                    <td width="50px">
                        <div style="text-align: center">
                            <input type="checkbox" onClick="toggle(this)" />
                        </div>
                    </td>
                    <td><b>Privilege(s)</b></td>
                    <td><b>Description</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT m.*, um.`user_module_id`
                        FROM module m
                        LEFT JOIN user_module um
                            ON um.module_id = m.module_id
                            $whereProperty
                        WHERE m.active = 1
                        ORDER BY m.module_name ASC";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                $no = 1;
                while($row = $result->fetch_object()) {
                    ?>
                <tr id="<?php echo $no; ?>">
                    <td>
                        <div style="text-align: center">
                            <input type="checkbox" name="checks[]" value="<?php echo $row->module_id ?>" <?php if(isset($row->user_module_id)) echo 'checked=true' ?> />
                        </div>
                    </td>
                    <td><?php echo $row->module_name; ?></td>
                    <td><div style="word-wrap:break-word;"><?php echo $row->module_description; ?></div></td>
                </tr>
                    <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid">
        <input type="hidden" name="userId" id="userId" value="<?php echo $userId; ?>" />
        <input type="hidden" name="action" id="action" value="user_module_data" />
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
