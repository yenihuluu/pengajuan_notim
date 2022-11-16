<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$userId = '';

// If ID is in the parameter
if (isset($_POST['ticketId']) && $_POST['ticketId'] != '') {
    $ticketId = $_POST['ticketId'];
}

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function() { //executed after the page has loaded

        $("#userModuleDataForm").validate({
            submitHandler: function(form) {
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#userModuleDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[3]) != 0) //if no errors
                        {
                            alertify.set({
                                labels: {
                                    ok: "OK"
                                }
                            });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                $('#user-module-data').load('tabs/user-module-data.php', {
                                    userId: $('input[id="generalUserId"]').val()
                                });
                            }
                        }
                    }
                });
            }
        });

    });

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for (var i = 0, n = checkboxes.length; i < n; i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>

<form method="post" id="userModuleDataForm">
    <div class="row-fluid">
        <table class="table table-bordered table-striped" style="font-size: 9pt;" id="ticket-target-date">
            <thead>
                <tr>
                    <td style="text-align: center"><b>Change No</b></td>
                    <td><b>Target Date</b></td>
                    <td><b>Date Changed</b></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT *
                        FROM history_ticket_td
                        WHERE ticket_id = $ticketId
                        ORDER BY change_no DESC";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                //$no = 1;
                while ($row = $result->fetch_object()) {
                ?>
                    <tr id="<?php echo $row->change_no; ?>">
                        <td>
                            <div style="text-align: center">
                                <?php echo $row->change_no; ?>
                            </div>
                        </td>
                        <td><?php echo $row->target_date_old; ?></td>
                        <td>
                            <div style="word-wrap:break-word;"><?php echo $row->entry_date; ?></div>
                        </td>
                    </tr>
                <?php
                   // $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="row-fluid">
        <input type="hidden" name="userId" id="userId" value="<?php echo $userId; ?>" />
        <input type="hidden" name="action" id="action" value="user_module_data" />
    </div>
</form>