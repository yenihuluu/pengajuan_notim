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

// <editor-fold defaultstate="collapsed" desc="Variable for Module Data">

$ticketId = '';
$ticketNo = '';
$modulesupport_id = '';
$modulesupport_name = '';
$division = '';
$notes = '';
$urgent = '';
$generatedInvoiceNo = '';
$divisionId = '';
$screenshot = '';
$status = '';
$startDate = '';
//$generatedInvoiceNo = $rowData->invoice_no;
//$moduleDescription = '';
//$active = '';

// </editor-fold>

// If ID is in the parameter
if (isset($_POST['ticketId']) && $_POST['ticketId'] != '') {

    $ticketId = $_POST['ticketId'];

    $readonlyProperty = ' readonly ';

    // <editor-fold defaultstate="collapsed" desc="Query for Module Data">

    $sql = "SELECT *, DATE_FORMAT(expected_date, '%d/%m/%Y') as expected_date2,
            DATE_FORMAT(start_date, '%d/%m/%Y') as start_date2,
            DATE_FORMAT(target_date, '%d/%m/%Y') as target_date2
            FROM ticket_it_support
            WHERE ticket_id = {$ticketId}
            ORDER BY ticket_id ASC
            ";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $division = $rowData->division;
        $notes = $rowData->notes;
        $urgent = $rowData->urgent;
        $modulesupport_id = $rowData->modulesupport_id;
        $generatedTicketId = $rowData->ticket_no;
        $divisionId = $rowData->division;
        $expectedDate = $rowData->expected_date2;
        $screenshot = $rowData->ss_user;
        $status = $rowData->status;
        $user = $rowData->pic_id;
        $type = $rowData->type_id;
        $targetDate = $rowData->target_date2;
        $startDate = $rowData->start_date2;
        $screenshot_result = $rowData->ss_done;
        $finish_note = $rowData->finish_note;
    }

    // </editor-fold>

}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";

    if ($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }

    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";

        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }

    if ($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function() {
        jQuery.validator.addMethod("indonesianDate", function(value, element) {
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });



        /* $("#moduleDataForm").validate({
             rules: {
                 moduleSupportName: "required"
                 //active: "required"
             },
             messages: {
                 moduleSupportName: "Name is a required field."
                 //active: "Status is a required field."
             },
             submitHandler: function(form) {
                 //var formData = new FormData(this);
                 $.ajax({
                     url: './data_processing.php',
                     method: 'POST', 
                    // data: formData,
                    // _method: 'INSERT',
                     data: $("#moduleDataForm").serialize(),
                     success: function(data) {
                         var returnVal = data.split('|');

                         if (parseInt(returnVal[4]) != 0) //if no errors
                         {
                             alertify.set({
                                 labels: {
                                     ok: "OK"
                                 }
                             });
                             alertify.alert(returnVal[2]);

                             if (returnVal[1] == 'OK') {
                                // document.getElementById('generalTicketId').value = returnVal[3];

                                 $('#dataContent').load('forms/ticket.php', {
                                     ticketNo: returnVal[3]
                                 }, iAmACallbackFunction2);

                                 //                                document.getElementById('successMsg').innerHTML = returnVal[2];
                                 //                                $("#successMsg").show();
                             }
                         }
                     }
                 });
             }
         });*/

        //SUBMIT FORM
        $("#moduleDataForm").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                _method: 'INSERT',
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
                            $('#pageContent').load('views/ticket.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $("#moduleDataForm").validate({
            rules: {
                moduleSupportName: "required"
            },
            messages: {
                moduleSupportName: "Name is a required field."
            },
            submitHandler: function(form) {
                $('#submitButton2').attr("disabled", true);
            }
        });
    });
    <?php
    //if($generatedInvoiceNo == "") {
    if ($generatedTicketId == "") {
    ?>
        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getTicketId'

            },
            success: function(data) {
                if (data != '') {
                    //document.getElementById('generatedInvoiceNo').value = data;
                    document.getElementById('generatedTicketId').value = data;
                }
            }
        });
    <?php } else { ?>
        //setInvoiceType(generatedInvoiceNo);
    <?php } ?>

    /*function reopenTicket() {
        var r = confirm("Are You Sure?");
        if (r == true) {
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: {
                    action: 'reopen_ticket',
                    ticketId: document.getElementById('ticketId').value,
                    status: document.getElementById('status').value,
                },
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0) //if no errors
                    {

                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/ticket.php', {}, iAmACallbackFunction);
                        }

                        $('#submitButton2').attr("disabled", false);
                    }
                }
            });
        } else {
            alertify.set({
                labels: {
                    ok: "OK"
                }
            });
            alertify.alert('Canceled');
        }
    }*/
    $('#submitButtonreopen').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=reopen_ticket&ticketId=<?php echo $rowData->ticket_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                           // $('#dataContent').load('contents/invoice.php', {}, iAmACallbackFunction2);
                           $('#pageContent').load('views/ticket.php', {}, iAmACallbackFunction);
                        } 
                    }
                }
            });
        });
</script>

<script type="text/javascript">
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
        // // Session Storage Browser
        // Object.keys(sessionStorage).forEach((key) => {
        //     var newKey = key.split('.');
        //     if (newKey[0] == "poData" && newKey[1] != "") {
        //         document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
        //         $('#' + newKey[1]).trigger('change');
        //     }
        // });
        // $(":input").change(function () {
        //     sessionStorage.setItem("poData." + this.id, this.value);
        // });
    });
</script>
<?php
if ($status == 0 || $status == 3) {
?>
    <form method="post" id="moduleDataForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="action" value="ticket" />
        <input type="hidden" name="ticketId" id="ticketId" value="<?php echo $ticketId; ?>" />
        <input type="hidden" name="status" id="status" value="<?php echo $status; ?>" />
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Ticket ID</span></label>
                <input type="text" readonly class="span12" tabindex="" id="generatedTicketId" name="generatedTicketId" value="<?php echo $generatedTicketId; ?>">
            </div>
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Module <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT modulesupport_id, modulesupport_name FROM modulesupport
        			ORDER BY modulesupport_name ASC",
                    $modulesupport_id,
                    "",
                    "modulesupport_id",
                    "modulesupport_id",
                    "modulesupport_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Urgent <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT '1' as id, 'YES' as info UNION
                            SELECT '0' as id, 'NO' as info;",
                    $urgent,
                    '',
                    "urgent",
                    "id",
                    "info",
                    "",
                    1
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Upload Screenshot <span style="color: red;">*</span></label>
                <input type="file" placeholder="File" tabindex="" id="file" name="file" class="span12">
                <?php if ($screenshot != '') { ?>
                    <a href="<?php echo $screenshot; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                <?php } ?>
            </div>
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Division <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT division_id, division_name FROM division
        			ORDER BY division_name ASC",
                    $divisionId,
                    "",
                    "division_id",
                    "division_id",
                    "division_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Expected Date <span style="color: red;">*</span></label>
                <input type="text" placeholder="DD/MM/YYYY" tabindex="4" id="expectedDate" value="<?php echo $expectedDate; ?>" name="expectedDate" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span4 lightblue">
                <label>Notes</span></label>
                <textarea class="span12" rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton2">Submit</button>
                <button class="btn" type="button" onclick="back()">Back</button>
            </div>
        </div>
    </form>
<?php
} else if ($status == 1 || $status == 2 || $status == 4) {
?>
    <form method="post" id="moduleDataForm" enctype="multipart/form-data">
        <input type="hidden" name="action" id="action" value="ticket" />
        <input type="hidden" name="ticketId" id="ticketId" value="<?php echo $ticketId; ?>" />
        <input type="hidden" name="status" id="status" value="<?php echo $status; ?>" />
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Ticket ID</span></label>
                <input type="text" readonly class="span12" tabindex="" id="generatedTicketId" name="generatedTicketId" value="<?php echo $generatedTicketId; ?>">
            </div>
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Module <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT modulesupport_id, modulesupport_name FROM modulesupport
        			ORDER BY modulesupport_name ASC",
                    $modulesupport_id,
                    "readonly",
                    "modulesupport_id",
                    "modulesupport_id",
                    "modulesupport_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Urgent <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT '1' as id, 'YES' as info UNION
                            SELECT '0' as id, 'NO' as info;",
                    $urgent,
                    'readonly',
                    "urgent",
                    "id",
                    "info",
                    "",
                    1
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Preview Screenshot <span style="color: red;">*</span></label>
                <?php if ($screenshot != '') { ?>
                    <a href="<?php echo $screenshot; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                <?php } ?>
            </div>
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Division <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT division_id, division_name FROM division
        			ORDER BY division_name ASC",
                    $divisionId,
                    "readonly",
                    "division_id",
                    "division_id",
                    "division_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>Expected Date <span style="color: red;">*</span></label>
                <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="4" id="expectedDate" value="<?php echo $expectedDate; ?>" name="expectedDate" data-date-format="dd/mm/yyyy">
            </div>
            <div class="span4 lightblue">
                <label>Notes</span></label>
                <textarea class="span12" readonly rows="3" tabindex="" id="notes" name="notes"><?php echo $notes; ?></textarea>
            </div>
        </div>
        <div class="row-fluid" style="margin-bottom: 7px;">
            <div class="span4 lightblue">
                <label>Type <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT type_id, type_name FROM ticket_type
		        	ORDER BY type_name ASC",
                    $type,
                    "disabled",
                    "type_id",
                    "type_id",
                    "type_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
            <div class="span4 lightblue">
                <label>PIC <span style="color: red;">*</span></label>
                <?php
                createCombo(
                    "SELECT user_id, user_name FROM user
		        	ORDER BY user_name ASC",
                    $user,
                    "disabled",
                    "user_id",
                    "user_id",
                    "user_name",
                    "",
                    "",
                    "select2combobox100"
                );
                ?>
            </div>
        </div>
        <?php
        if ($targetDate != '') {
        ?>
            <div class="row-fluid" style="margin-bottom: 7px;">
                <div class="span4 lightblue">
                    <label>Start Date <span style="color: red;">*</span></label>
                    <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="4" id="startDate" value="<?php echo $startDate; ?>" name="startDate" data-date-format="dd/mm/yyyy">
                </div>
                <div class="span4 lightblue">
                    <label>Target Date <span style="color: red;">*</span></label>
                    <input type="text" readonly placeholder="DD/MM/YYYY" tabindex="4" id="targetDate" value="<?php echo $targetDate; ?>" name="targetDate" data-date-format="dd/mm/yyyy">
                </div>
            <?php
        }
            ?>
            <?php
            if ($status == 2 || $status == 4) {
            ?>
                <div class="row-fluid" style="margin-bottom: 7px;">
                    <div class="span4 lightblue">
                        <label>Screenshot Result <span style="color: red;">*</span></label>
                        <?php if ($screenshot_result != '') { ?>
                            <a href="<?php echo $screenshot_result; ?>" target="_blank" role="button" title="view screenshot result"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                        <?php } ?>
                    </div>
                    <div class="span4 lightblue">
                        <label>Finish Notes<span style="color: red;">*</span></label>
                        <textarea readonly class="span12" rows="3" tabindex="" id="finish_note" name="finish_note"><?php echo $finish_note; ?></textarea>
                    </div>
                <?php
            }
                ?>
                </div>
                <div class="row-fluid">
                    <div class="span12 lightblue">
                        <br>
                        <?php
                        if ($status == 2) {
                        ?>
                            <label>Ticket Done, please click the "Close" button for confirmation</label>
                            <button class="btn btn-success" <?php echo $disableProperty; ?> id="submitButton2">Close</button>
                            <button class="btn btn-primary" type="button" id="submitButtonreopen">Reopen</button>
                        <?php
                        } //else if ($status == 4) {
                        ?>
                        <!--    <label>Ticket Closed, Click the "Reopen" button to open again </label>
                            <button class="btn btn-success" <?php echo $disableProperty; ?> id="submitButton2">Reopen</button>
                        -->
                        <?php
                        //}
                        ?>
                        <button class="btn" type="button" onclick="back()">Back</button>
                    </div>
                </div>
    </form>
<?php
} else {
    echo "FORM ERROR";
}
?>