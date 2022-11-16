<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Pengajuan Internal Transfer';

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
            DATE_FORMAT(tf.request_payment_date, '%d/%m/%Y') AS requestPaymentDate,
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
        $stockpileLocation = $rowData->stockpile;
        $bankId = $rowData->bank_id;
        $requestpaymentType = $rowData->request_payment_type;
        $requestPaymentDate = $rowData->requestPaymentDate;
        //echo $requestPaymentDate;
    }

    $method = 'UPDATE';
    if($status1 == 2){
        $disableProperty1 = 'disabled';
    }
   
    $readonly1 = 'readonly';
}else{
    $method = 'INSERT';
    $temp = 0;
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
        $('#divrequestPaymentDate').hide();
       
        

    //setStockpileLocation(); //DIPINDAH KESINI
    setPaymentType(1, <?php echo $paymentType; ?>);
    setPaymentMethod(1, <?php echo $paymentMethod; ?>);
    setPaymentFor(1, <?php echo $paymentFor; ?>);
    

    <?php if($internalTF_id != ''){ ?>
        setBankITF(1,$('select[id="stockpileLocation"]').val(), <?php echo $bankId ?>)
    <?php } ?>

    <?php if(isset($requestpaymentType) && $requestpaymentType == 1) { ?>
        $('#divrequestPaymentDate').show();
        $('#requestPaymentDate1').show();
        $('#requestPaymentDate').hide();
    <?php }else if(isset($requestpaymentType) && ($requestpaymentType == 0)) {?>
        $('#divrequestPaymentDate').show();
        $('#requestPaymentDate1').hide();
        $('#requestPaymentDate').show();
    <?php }else{ ?>
        setnormalpayment(1, <?php echo $temp; ?>);
    <?php $requestpaymentType = $temp; } ?>

    $('#requestpaymentType').change(function () {
       // console.log(this.value)
       $('#divrequestPaymentDate').show();
        if (this.value == 1) {
            $('#requestPaymentDate1').show();
            $('#requestPaymentDate').hide();
        } else if (this.value == 0) {
            reqpaymentdate();
        }
    });

    $('#stockpileLocation').change(function() {
            resetBankITF(' ');
			//alert('html from to nya');

            if(document.getElementById('stockpileLocation').value != '') {
                setBankITF(0,$('select[id="stockpileLocation"]').val(), 0);
            }
    });
            
        //SUBMIT FORM
        $("#PengajuanInternalTF_form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                _method: 'INSERT',
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
                            $('#pageContent').load('views/pengajuan-internal-transfer.php', {}, iAmACallbackFunction);
                        }
                        $('#submitButton2').attr("disabled", false);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

       $("#PengajuanInternalTF_form").validate({
                rules: {
                    paymentMethod: "required",
                    paymentType: "required",
                    paymentFor: "required",
                    stockpileId: "required",
                    requestPaymentDate1: "required"
                    // file: {required: true, filesize: 1048576}
                },
                messages: {
                    paymentMethod: "Method is a required field.",
                    paymentType: "Type is a required field.",
                    paymentFor: "Payment For is a required field.",
                    stockpileId: "Stockpile is a required field.",
                    requestPaymentDate1: "Request payment date is a required field.",
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

    function setnormalpayment(type, normal) {
        document.getElementById('requestpaymentType').value = 0;
        $('#divrequestPaymentDate').show();
        reqpaymentdate();
    }

    function resetBankITF() {
        document.getElementById('bankId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('bankId').options.add(x);
        
        $("#bankId").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
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

    function reqpaymentdate() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                    action: 'setPlanPayDate',
            },
            success: function (data) {
                var returnVal = data.split('|');
                $('#requestPaymentDate').show();
                $('#requestPaymentDate1').hide();
                $('#requestPaymentDate').val(returnVal[1]);
            }
        });
    }

    function canceled() {
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'internalTF_data',
                _method: 'CANCEL',
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
                        $('#pageContent').load('views/pengajuan-internal-transfer.php', {}, iAmACallbackFunction);
                    }
                    $('#submitButton').attr("disabled", false);
                }
            }
        });
    }

</script>

<form method="post" id="PengajuanInternalTF_form" enctype="multipart/form-data">
    <input type="hidden" name="action" id="action" value="internalTF_data" />
    <input type="hidden" name="_method" value="<?php echo $method; ?>">
    <input type="hidden" id="paymentForVal" value="<?php echo $paymentFor ?>" />
    <input type="hidden" id="paymentTypeVal" value="<?php echo $paymentType ?>" />
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

        <!-- Stockpile Location  -->
        <!-- <div class="span1 lightblue" style="display: none;">
            <label id="stockpileLocationLabel" style="display: none;">Stockpile Location <span style="color: red;">*</span></label>
        </div>
        <div class="span3 lightblue" id="stockpileLocationDiv" style="display: none;">
        </div> -->
       
        <!-- END -->

        <div class="span3 lightblue">
            <label>Payment For<span style="color: red;">*</span></label>
                <?php   
                createCombo("SELECT type_transaction_id, type_transaction_name
                            FROM type_transaction", $paymentFor, "", "paymentFor", "type_transaction_id", "type_transaction_name",
                    "", 3, "",5);
                ?>
        </div>

        <div class="span3 lightblue">
            <label>Upload Document</label>
            <input type="file" placeholder="File" tabindex="" id="file" name="file" class="span12">
            <?php if($file_invoice != ''){ ?>
                <a href="<?php echo $file_invoice; ?>" target="_blank" role="button" title="view file"><img src="assets/ico/file.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
            <?php } ?>
        </div>
    </div>

	<div class="row-fluid">
        <div class="span3 lightblue">
            <label>Stockpile<span style="color: red;">*</span> </label>
            <?php
                createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                            FROM user_stockpile us
                        INNER JOIN stockpile s
                            ON s.stockpile_id = us.stockpile_id
                        WHERE us.user_id = {$_SESSION['userId']}
                        ORDER BY s.stockpile_code ASC, s.stockpile_name ASC","$stockpileLocation", "", "stockpileLocation", "stockpile_id", "stockpile_full",
                "", 9, "select2combobox100");
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Periode From<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="periodeFrom" name="periodeFrom" value = "<?php echo $periodeFrom; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div class="span3 lightblue">
            <label>Periode To<span style="color: red;">*</span></label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="periodeTo" value = "<?php echo $periodeTo; ?>" name="periodeTo" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
	</div>

    <div class="row-fluid">
        <div class="span3 lightblue">
            <label>Bank<span style="color: red;">*</span></label>
            <?php
                // createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full
                //         FROM bank b
                //         INNER JOIN currency cur
                //             ON cur.currency_id = b.currency_id WHERE cur.currency_id = 1
                //         ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $bankId, "", "bankId", "bank_id", "bank_full",
                //     "", 18, "select2combobox100", 1, "");
                createCombo("", "", "", "bankId", "bank_id", "bank_full",
                "", 10, "select2combobox100", 2);

            ?>
        </div>
        
        <div class="span2 lightblue" >
            <label>Amount<span style="color: red;">*</span></label>
                <input type="text" class="span12"  tabindex="" id="amount" name="amount" value = " <?php echo $amount ?>"> 
        </div>

        <div class="span3 lightblue">
            <label>Payment Type</label>
            <?php
            createCombo("SELECT '0' as id, 'Normal' as info UNION 
                        SELECT '1' as id, 'Urgent' as info;", $requestpaymentType, "", "requestpaymentType", "id", "info",
                "", 11, "select2combobox100");
            ?>
        </div>

        <div class="span3 lightblue" id="divrequestPaymentDate">
            <label>Request Payment Date</label>
            <input type="text" name="requestPaymentDate" id="requestPaymentDate"  style="display: none"  value="<?php echo $requestPaymentDate; ?>" readonly>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" name="requestPaymentDate1" id="requestPaymentDate1"
                   value="<?php echo $requestPaymentDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker" style="display: none">
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
            <button class="btn btn-primary" <?php echo $disableProperty1 ?> id="submitButton2">Kirim</button>
            <button class="btn" type="button" onclick="back()">Back</button>

        </div>
    </div>
</form>

<hr>



<div class="row-fluid" style="margin-bottom: 7px;">
    <div class="span8 lightblue">
        <label>Return Remarks</label>
        <textarea class="span12" rows="3" tabindex="" id="reject_remarks"
                  name="reject_remarks"><?php echo $reject_remarks; ?></textarea>
    </div>

</div>

    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-danger"  id="canceled" onclick= "canceled()" style="margin: 0px;">Cancel</button>
        </div>
    </div>  


