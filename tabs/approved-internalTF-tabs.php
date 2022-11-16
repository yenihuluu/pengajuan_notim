<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Approved Internal Transfer';

date_default_timezone_set('Asia/Jakarta');

$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$todayDate = $date->format('d/m/Y');
$currentYear = $date->format('Y');
$currentYearMonth = $date->format('my');

$allowAccount = true;
$allowBank = true;
$allowGeneralVendor = true;

$internalTF_id = '';
$stockpileId = '';
$paymentType = '';
$paymentFor = '';

$paymentFrom = '';
$paymentTo = '';
$paymentMethod = '';

$currencyId = '';
$amount = '';
$remarks = '';
$paymentMethods = '';
$paymentFor_1 = '';
$method = '';


$whereproperty = '';
$readonly = '';
$disableProperty1 = '';


$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 14) {
            $allowAccount = true;
        } elseif($row->module_id == 15) {
            $allowBank = true;
        } elseif($row->module_id == 11) {
            $allowGeneralVendor = true;
        }
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Supplier --</option>";
    } else if($empty == 7) {
        echo "<option value=''>-- Please Select No Kontrak --</option>";
    }else if($empty == 8) {
        echo "<option value=''>-- Please Select Kontrak PKHOA --</option>";
    }else if($empty == 9) {
        echo "<option value=''>-- Please Select Vendor --</option>";
    }

    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }

    echo "</SELECT>";
    }


// </editor-fold>
if(isset($_POST['internalTF_id']) && $_POST['internalTF_id'] != '') {
    $internalTF_id = $_POST['internalTF_id'];

    $sql = "SELECT CASE WHEN tf.payment_method = 1 THEN 'Payment' ELSE NULL END AS paymentMethod,
            CASE WHEN tf.payment_type = 2 THEN 'OUT/DEBIT' ELSE NULL END AS paymentType,
            CASE WHEN tf.payment_for = 7 THEN 'Internal Transfer' ELSE NULL END AS paymentFor,
            DATE_FORMAT(tf.periode_from, '%d/%m/%Y') AS dateFrom, 
            DATE_FORMAT(tf.periode_to, '%d/%m/%Y') AS dateTo,
            sp.stockpile_name,
            CASE WHEN tf.request_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS reqpaymentType,
            DATE_FORMAT(tf.request_payment_date, '%d/%m/%Y') AS reqpaymentDate,
        tf.* FROM pengajuan_internalTF tf
        LEFT JOIN stockpile sp ON sp.stockpile_id = tf.stockpile
        WHERE tf.pengajuan_interalTF_id = {$internalTF_id}";
        //echo $sql;
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result !== false && $result->num_rows > 0) {
        $rowData = $result->fetch_object();
        $paymentType = $rowData->payment_type;
        $paymentFor = $rowData->payment_for;
        $paymentMethod = $rowData->payment_method;
        $status1 = $rowData->status;

        $periodeFrom = $rowData->dateFrom;
        $periodeTo = $rowData->dateTo;
        $file_invoice = $rowData->file;
        $amount = $rowData->amount;
        $remarks = $rowData->remarks;
        $bankId = $rowData->bank_id;
        $stockpileId = $rowData->stockpile;
        $spName = $rowData->stockpile_name;
        $reqpaymentType = $rowData->reqpaymentType;
        $reqpaymentDate = $rowData->reqpaymentDate;


    }

    $method = 'UPDATE';
    if($status1 == 2 || $status1 == 1){
        $disableProperty1 = 'disabled';
        $reject_remarks = $rowData->remaks_reject;
    }
   
    $readonly1 = 'readonly';
}else{
    $method = 'INSERT';
}

?>
<input type="hidden"  id="stockpileIdVal" value="<?php echo $stockpileId; ?>" >
<input type="hidden" readonly class="span12" tabindex="" id="todayDate" name="todayDate" value="<?php echo $todayDate ?>">


<script type="text/javascript">
    $(document).ready(function(){


        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });
        $('#amount').number(true, 2);
        $('#paymentType').attr("readonly", true); 
        $('#paymentMethod').attr("readonly", true); 
        $('#paymentFor').attr("readonly", true); 
        $('#bankId').attr("readonly", true); 


    setPaymentType(1, <?php echo $paymentType; ?>);
    setPaymentMethod(1, <?php echo $paymentMethod; ?>);
    setPaymentFor(1, <?php echo $paymentFor; ?>);
    setBankITF(1,<?php echo $stockpileId ?>, <?php echo $bankId ?>)



            
        //SUBMIT FORM
        $("#ApprovedInternalTF_form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.blockUI({ message: '<h4>Please wait...</h4>' }); 
            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                _method: 'APPROVED',
                success: function (data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    {
                        alertify.set({
                            labels: {
                                ok: "OK"
                            }
                        });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#pageContent').load('views/approved-internal-transfer.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

       $("#ApprovedInternalTF_form").validate({
                rules: {
                    paymentMethod: "required",
                    paymentType: "required",
                    paymentFor: "required",
                    stockpileId: "required",
                    // file: {required: true, filesize: 1048576}
                },
                messages: {
                    paymentMethod: "Method is a required field.",
                    paymentType: "Type is a required field.",
                    paymentFor: "Payment For is a required field.",
                    stockpileId: "Stockpile is a required field.",
                    // file: "invoice file   is a required field."
                },
            submitHandler: function (form) {
                $('#submitButton2').attr("disabled", true);
            }
        });
    });

    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });

    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').value = 2;
    }

    function setPaymentMethod(type, paymentMethod) {
        document.getElementById('paymentMethod').value = 1;
    }

    function setPaymentFor(type, paymentFor) {
        document.getElementById('paymentFor').value = 7;
    }



    function reject2() {
		$.blockUI({ message: '<h4>Please wait...</h4>' }); 
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'internalTF_data',
                _method: 'REJECT',
                internalTF_id: document.getElementById('internalTF_id').value,
                reject_remarks: document.getElementById('reject_remarks').value
            },
            success: function (data) {
                console.log(data);
                var returnVal = data.split('|');
                if (parseInt(returnVal[3]) != 0)	//if no errors
                {
                    alertify.set({
                        labels: {
                            ok: "OK"
                        }
                    });
                    alertify.alert(returnVal[2]);

                    if (returnVal[1] == 'OK') {
                        $('#pageContent').load('views/approved-internal-transfer.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

    function setBankITF(type, stockpileLocation, bankId) {
        $.ajax({
            url: 'get-data-Ppayment.php',
            method: 'POST',
            data: { action: 'getBankITF',
                    stockpileLocation: stockpileLocation,
                    newbank: bankId
            },
            success: function(data){
                var returnVal = data.split('~');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if(returnVal[1] == '') {
                        returnValLength = 0;
                    } else if(returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }
                    
                    //alert(isResult);
                    if(returnValLength >= 0) {
                        document.getElementById('bankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('bankId').options.add(x);
                        
                        $("#bankId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('bankId').options.add(x);
                    }
                                        
                    if(type == 1) {
                        $('#bankId').find('option').each(function(i,e){
                            if($(e).val() == bankId){
                                $('#bankId').prop('selectedIndex',i);
                                
                                $("#bankId").select2({
                                    width: "100%",
                                    placeholder: bankId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
</script>

<form method="post" id="ApprovedInternalTF_form" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="internalTF_data" />
    <input type="hidden" name="_method" value="APPROVED">
    <input type="hidden" id="internalTF_id" name="internalTF_id" value="<?php echo $internalTF_id ?>" />

    <?php if($internalTF_id != '') { ?>
    <div class="row-fluid">
        <div class="span2 lightblue">
        </div>
        <div class="span8 lightblue">
             <?php if($status1 == 2) { ?>
                 <label style="color: red;"><center><b>REJECT</center></b></label>
            <?php } ?>
                
        </div>
    </div>
    <?php } ?>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Metode Pembayaran<span style="color: red;">*</span> </label>
                <?php 
                    createCombo("SELECT '1' as id, 'Payment' as info;", $paymentMethod, "", "paymentMethod", "id", "info",
                        "","","",1);
                 ?>
        </div>

        <div class="span3 lightblue">
            <label>Jenis Pembayaran<span style="color: red;">*</span> </label>
            <?php
                createCombo("SELECT '2' as id, 'OUT / Debit' as info;", $paymentType, "", "paymentType", "id", "info",
            "");
            ?>
        </div>

        <div class="span3 lightblue">
            <label>Payment For<span style="color: red;">*</span></label>
                <?php   
                createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction", $paymentFor, "", "paymentFor", "type_transaction_id", "type_transaction_name",
                    "", 3, "",5);
                ?>
        </div>

        <div class="span3 lightblue">
            <label>Document</label>
            <?php if($file_invoice != ''){ ?>
                <a href="<?php echo $file_invoice; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <?php } ?>
        </div>
    </div>

	<div class="row-fluid">
             <!-- Stockpile Location  -->
        <div class="span3 lightblue">
            <label>Stockpile Location <span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="" id="stockpileLocation" name="stockpileLocation" value = " <?php echo $spName ?>"> 
        </div>
        <!-- END -->
        <div class="span3 lightblue">
            <label>Periode From<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" readonly tabindex="" id="periodeFrom" name="periodeFrom" value = "<?php echo $periodeFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div class="span3 lightblue">
            <label>Periode To<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" readonly tabindex="" id="periodeTo" value = "<?php echo $periodeTo; ?>" name="periodeTo" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

      

	</div>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Bank<span style="color: red;">*</span></label>
            <?php
                createCombo("", "", "", "bankId", "bank_id", "bank_full",
                "", 10, "select2combobox100", 2);
            ?>
        </div>
        <div class="span2 lightblue" >
            <label>Amount<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="amount" name="amount" value = " <?php echo $amount ?>"> 
        </div>
        <div class="span2 lightblue" >
            <label>Request payment type<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="reqpaymentType" name="reqpaymentType" value = " <?php echo $reqpaymentType ?>"> 
        </div>
        <div class="span2 lightblue" >
            <label>Request payment date<span style="color: red;">*</span></label>
                <input type="text" class="span12" readonly tabindex="" id="reqpaymentDate" name="reqpaymentDate" value = " <?php echo $reqpaymentDate ?>"> 
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <label>Catatan</label>
            <textarea class="span12" rows="3" tabindex=""  <?php echo $readonly ?> id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>

        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty1; ?> id="submitButton2">Approve</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>

<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Reject Remarks</label>
        <textarea class="span12" rows="3" tabindex="" <?php echo $disableProperty1; ?> id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>
</div>

<!-- <?php //if($status1 == '') { ?> -->
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger"  <?php echo $disableProperty1; ?> id="reject1" onclick= "reject2()" style="margin: 0px;">Reject</button>
        </div>
    </div>  
<!-- <?php// } ?> -->


