<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$_SESSION['menu_name'] = 'invoice';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';
$date = new DateTime();
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">
$inputDate = $date->format('d/m/Y');
$invoiceId = '';
$invoiceNo = '';
$generalVendorId = '';
$generatedInvoiceNo = '';
$currencyId = '';
$invoiceMethod = '';
$price = '';
$quantity = '';
$amount = '';
$amountDP = 0;
$pph2 = 0;
$ppn2 = 0;
$exchangeRate = '';


// </editor-fold>

// If ID is in the parameter
if (isset($_POST['invoiceId']) && $_POST['invoiceId'] != '') {

    $invoiceId = $_POST['invoiceId'];

    $readonlyProperty = ' readonly ';
    $disabledProperty = ' disabled ';

    // <editor-fold defaultstate="collapsed" desc="Query for Contract Data">

    $sql = "SELECT inv.*, DATE_FORMAT(inv.invoice_date, '%d/%m/%Y') AS invoice_date2, DATE_FORMAT(inv.input_date, '%d/%m/%Y') AS input_date, DATE_FORMAT(inv.request_date, '%d/%m/%Y') AS request_date, DATE_FORMAT(inv.tax_date, '%d/%m/%Y') AS tax_date
            FROM invoice inv
            WHERE inv.invoice_id = {$invoiceId}
            ORDER BY inv.invoice_id ASC
            ";
//            echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $invoiceTax = $rowData->invoice_tax;
        $invoiceDate = $rowData->invoice_date2;
        $inputDate = $rowData->input_date;
        $requestDate = $rowData->request_date;
        $taxDate = $rowData->tax_date;
        $generatedInvoiceNo = $rowData->invoice_no;
        $generatedInvoiceNo2 = $rowData->invoice_no2;
        $stockpileId = $rowData->stockpileId;
        $stockpileContractId3 = $rowData->po_id;
        $remark = $rowData->remark;
        $generalVendorId2 = $rowData->generalVendorId;
    }

    // </editor-fold>

} else {
    $generatedInvoiceNo = "";
    if (isset($_SESSION['invoice'])) {
        $invoiceTax = $_SESSION['invoice']['invoiceTax'];
        $invoiceDate = $_SESSION['invoice']['invoiceDate'];
        $inputDate = $_SESSION['invoice']['inputDate'];
        $requestDate = $_SESSION['invoice']['requestDate'];
        $taxDate = $_SESSION['invoice']['taxDate'];
        $generatedInvoiceNo = $_SESSION['invoice']['generatedInvoiceNo'];
        $generatedInvoiceNo2 = $_SESSION['invoice']['generatedInvoiceNo2'];
        $stockpileId = $_SESSION['invoice']['stockpileId'];
        $stockpileContractId3 = $_SESSION['invoice']['stockpileContractId3'];
        $remark = $_SESSION['invoice']['remark'];
        $generalVendorId2 = $_SESSION['generalVendorId2']['remark'];
    }
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false)
{
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if ($empty == 1) {
        echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    } else if ($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } elseif ($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        if ($setvalue == '0') {
            echo "<option value='0' selected>NONE</option>";
        } else {
            echo "<option value='0'>NONE</option>";
        }
    } else if ($empty == 4) {
        echo "<option value=''>-- Please Select Type --</option>";
    }else if($empty == 5) {
		echo "<option value='' style='width:10%;'>-- Please Select --</option>";
    }

    if ($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }

    if ($boolAllow) {
        if (strtoupper($setvalue) == "INSERT") {
            echo "<option value='INSERT' selected>-- Insert New --</option>";
        } else {
            echo "<option value='INSERT'>-- Insert New --</option>";
        }
    }

    echo "</SELECT>";
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);

    $(document).ready(function () {

        $(".select2combobox100").select2({
            width: "100%"
        });

        $(".select2combobox50").select2({
            width: "50%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });
		$('#divsettlement').show();

        <?php
        if($generatedInvoiceNo == "") {
        ?>
        // if(document.getElementById('generalVendorId').value != "") {
        $.ajax({
            url: './get_data.php',
            method: 'POST',
            data: {
                action: 'getInvoiceNo'
                //stockpileContractId: stockpileContractId,
                //paymentMethod: paymentMethod,
                //ppn: ppnValue,
                //pph: pphValue
            },
            success: function (data) {
                if (data != '') {
                    document.getElementById('generatedInvoiceNo').value = data;
                    $('#addInvoice').hide();

                }
                //setInvoiceType(generatedInvoiceNo);
            }

        });
        /*     $.ajax({
              url: './get_data.php',
              method: 'POST',
            data: "action=getInvoiceNO"
              success: function(data) {
            document.getElementById('generatedInvoiceNo').value = data;
                 }
             });
         } else {
           document.getElementById('generatedInvoiceNo').value = "";
       }*/
        <?php
        }else{
        ?>

        //setInvoiceType(generatedInvoiceNo);
        <?php
        }
        ?>



        if (document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == '') {
            $('#generalVendorId2').hide();
        } else {
            $('#generalVendorId2').show();
        }

        $('#invoiceMethod').change(function () {
            if (document.getElementById('invoiceMethod').value == 2 || document.getElementById('invoiceMethod').value == '') {
                $('#generalVendorId2').hide();
            } else {
                $('#generalVendorId2').show();
            }
        });

        $('#invoiceMethod').change(function () {
            if (document.getElementById('invoiceMethod').value != '') {
                $('#addInvoice').show();
            } else {
                $('#addInvoice').hide();
            }
        });

        $('#settlementId').change(function () {
            if (document.getElementById('settlementId').value == 1) {
                $('#divpengajuan').show();
            } else if (document.getElementById('settlementId').value == 2) {
                $('#divpengajuan').hide();
            }
        });


        $("#InvoiceDataForm").validate({
            rules: {
                //contractType: "required",
                generalVendorId: "required",
                currencyId: "required",
                exchangeRate: "required",
                accountId: "required",
                invoiceType: "required",
                amount: "required",
                invoiceDate: "required",
				pengajuanNo: "required",
                settlementId: "required"
                //stockpileId: "required"
            },
            messages: {
                // contractType: "Contract Type is a required field.",
                generalVendorId: "Vendor is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                accountId: "Account is a required field.",
                invoiceType: "Invoice Type is a required field.",
                amount: "Amount is a required field.",
                invoiceDate: "Invoice Date is a required field.",
				 pengajuanNo: "Pengajuan No is a required field.",
                 settlementId: "settlementId is a required field."
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function (form) {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#InvoiceDataForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({
                                labels: {
                                    ok: "OK"
                                }
                            });
                            alertify.alert(returnVal[2]);

                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalInvoiceId').value = returnVal[3];

                                $('#dataContent').load('contents/invoice.php', {invoiceId: returnVal[3]}, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            }
                        }
                    }
                });
            }
        });
        $("#insertForm").validate({
            rules: {
                //contractType: "required",
                invMethod: "required",
                currencyId: "required",
                exchangeRate: "required",
                accountId: "required",
                invoiceType: "required",
                qty: "required",
                price: "required",
                pphTaxId: "required",
                generalVendorId: "required",
                amount: "required"
                //stockpileId: "required"
            },
            messages: {
                // contractType: "Contract Type is a required field.",
                invMethod: "Method is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
                accountId: "Account is a required field.",
                invoiceType: "Invoice Type is a required field.",
                qty: "Quantity Type is a required field.",
                price: "Price Type is a required field.",
                pphTaxId: "PPh Type is a required field.",
                generalVendorId: "Vendor Type is a required field.",
                amount: "Amount is a required field."
                //stockpileId: "Stockpile is a required field."
            },
            submitHandler: function (form) {
                $('#submitButton').attr("disabled", true);
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 

                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');
								if(returnVal[3] == 1){ //get InvMethod dari invoice_detail
									  $('#divsettlement').hide();
								}else{
									$('#divsettlement').show();
								}
                                setInvoiceDetail();
                                /* if (resultData[0] == 'INVOICE_DETAIL') {
                                     //setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
 //                                    resetFreight(' ');
 //                                    setFreight(0, resultData[1], 0);

                                 } */

                                $('#insertModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });


    });


</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#showTransaction').click(function (e) {
            e.preventDefault();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/invoice-data.php', {});
        });
		
	$('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
             if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_vendor_invoice',
                                    InvoiceDetailId: menu[2]
                            },
                            success: function(data){
                                var returnVal = data.split('|');
                                if(parseInt(returnVal[3])!=0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                    if(returnVal[1] == 'OK') {
                                       $('#dataContent').load('forms/invoice.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }
        });		
    });

</script>
<script type="text/javascript">
    function deleteInvoiceDetail(invoice_detail_id) {

        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: {
                action: 'delete_invoice_detail',
                invoiceDetailId: invoice_detail_id
                /*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
                    paymentFrom: paymentFrom,
                    paymentTo: paymentTo */
            },
            success: function (data) {
                if (data != '') {
                    setInvoiceDetail();
                }
            }
        });
    }
	
	 function editDetail(invoice_detail_id) {
        $("#modalErrorMsg").hide();
        $('#insertModal').modal('show');
        //            alert($('#addNew').attr('href'));
        $('#insertModalForm').load('forms/invoice-data.php', {invoiceId: invoice_detail_id}, iAmACallbackFunction2);	//and hide the rotating gif
    }

    function setInvoiceDetail() {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDetail',
                /*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
                    paymentFrom: paymentFrom,
                    paymentTo: paymentTo */
            },
            success: function (data) {
                if (data != '') {
                    $('#invoiceDetail').show();
                    document.getElementById('invoiceDetail').innerHTML = data;
                } else {
                    $('#invoiceDetail').hide();
                }
            }
        });
    }

    function checkSlipInvoice(generalVendorId, ppn1, pph1, invoiceMethod) {
//        var checkedSlips = document.forms[0].checkedSlips;

        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if (selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }

        var checkedSlips2 = document.getElementsByName('checkedSlips2[]');
        var selected2 = "";
        for (var i = 0; i < checkedSlips2.length; i++) {
            if (checkedSlips2[i].checked) {
                if (selected2 == "") {
                    selected2 = checkedSlips2[i].value;
                } else {
                    selected2 = selected2 + "," + checkedSlips2[i].value;
                }
            }
        }

        var ppnValue = 'NONE';
        var pphValue = 'NONE';

        if (typeof (ppn1) != 'undefined' && ppn1 != null && typeof (pph1) != 'undefined' && pph1 != null) {
            if (ppn1 != 'NONE') {
                if (ppn1.value != '') {
                    ppnValue = ppn1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if (pph1 != 'NONE') {
                if (pph1.value != '') {
                    pphValue = pph1.value.replace(new RegExp(",", "g"), "");
                }
            }
        }


        setInvoiceDP(generalVendorId, selected2, selected, ppnValue, pphValue, invoiceMethod);

        //alert(generalVendorId);
    }

    function checkAllInv(a) {
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        if (a.checked) {
            for (var i = 0; i < checkedSlips.length; i++) {
                if (checkedSlips[i].type == 'checkbox') {
                    checkedSlips[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkedSlips.length; i++) {
                console.log(i)
                if (checkedSlips[i].type == 'checkbox') {
                    checkedSlips[i].checked = false;
                }
            }
        }
        checkSlipInvoice(generalVendorId, ppn1, pph1, invoiceMethod);
    }


    function setInvoiceDP(generalVendorId, checkedSlips, checkedSlips2, ppn1, pph1, invoiceMethod) {

        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'setInvoiceDP',
                generalVendorId: generalVendorId,
                checkedSlips: checkedSlips,
                checkedSlips2: checkedSlips2,
                ppn1: ppn1,
                pph1: pph1,
                invoiceMethod: invoiceMethod

            },
            success: function (data) {
                if (data != '') {
                    $('#IDP').show();
                    document.getElementById('IDP').innerHTML = data;
                }
            }
        });
    }


</script>
<script type="text/javascript">
    $(function () {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
            orientation: "bottom auto",
            startView: 0
        });
        // // Session Storage Browser
        // Object.keys(sessionStorage).forEach((key) => {
        //     var newKey = key.split('.');
        //     if (newKey[0] == "invoiceData" && newKey[1] != "") {
        //         document.getElementById(newKey[1]).value = sessionStorage.getItem(key);
        //         $('#' + newKey[1]).trigger('change');
        //     }
        // });
        // $(":input").change(function () {
        //     sessionStorage.setItem("invoiceData." + this.id, this.value);
        // });
    });
</script>
<?php
$sql = "SELECT a.invoice_detail_id, b.`general_vendor_name`, a.`qty`, a.`price`,a.`termin`,a.`tamount_converted`,a.`notes`
FROM invoice_detail a
LEFT JOIN general_vendor b ON a.`general_vendor_id` = b.`general_vendor_id`
WHERE a.`invoice_id` IS NULL AND a.`entry_by` = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);



    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
    ?>

    <h4>Input Pending Vendor</h4>

    <table class="table table-bordered table-striped" style="font-size: 9pt;" id="contentTable">
        <thead>
        <tr>
            <th>Vendor Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Termin</th>
            <th>Amount</th>
            <th>Notes</th>
            <!--<th>Total Price</th>-->
            <!--<th>Entry By</th>-->
            <!--<th>Entry Date</th>-->
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
		<?php
		if ($result !== false && $result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
	   ?>
        <tr>
            <td width="20%"><?php echo $row->general_vendor_name; ?></td>
			<td width="5%" style="text-align: right;"><?php echo number_format($row->qty, 0, ".", ","); ?></td>
			<td width="10%" style="text-align: right;"><?php echo number_format($row->price, 0, ".", ","); ?></td>
			<td width="5%" style="text-align: right;"><?php echo number_format($row->termin, 0, ".", ","); ?></td>
			<td width="10%" style="text-align: right;"><?php echo number_format($row->tamount_converted, 0, ".", ","); ?></td>
            <td width="40%"><?php echo $row->notes; ?></td>
            <td width="5%">
                <div style="text-align: center;">
     
                    <a href="#" id="delete|invoice|<?php echo $row->invoice_detail_id; ?>" role="button" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
            </td>
        </tr>
		<?php
   }
        }
        ?>
        </tbody>
    </table>
	
<form method="post" id="InvoiceDataForm">
    <input type="hidden" name="action" id="action" value="invoice_data"/>
    <input type="hidden" name="invoiceId" id="invoiceId" value="<?php echo $invoiceId; ?>"/>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Generated Invoice No.</label>
            <input type="text" class="span12" readonly id="generatedInvoiceNo" name="generatedInvoiceNo"
                   value="<?php echo $generatedInvoiceNo; ?>">
        </div>
        <div class="span3 lightblue" id="divsettlement"  style="display: none;">
		    <label>Settlement <span style="color: red;">*</span></label>
            <?php
                createCombo("SELECT '1' as id, 'No' as info UNION
                        SELECT '2' as id, 'Yes' as info;", $settlementId, "", "settlementId", "id", "info",
                    "", 2, "select2combobox100");
                ?>
        </div>
		<div class="span3 lightblue" id="divpengajuan"  style="display: none;">
		    <label>Pengajuan No <span style="color: red;">*</span></label>
			<?php
            createCombo("SELECT pengajuan_general_id, pengajuan_no 
						FROM pengajuan_general WHERE invoice_id IS NULL AND status_pengajuan = 1 ORDER BY pengajuan_general_id DESC", $pengajuanNo, "", "pengajuanNo", "pengajuan_general_id", "pengajuan_no",
                        "", 1, "select2combobox100", 5);
            ?>
        </div>
        <div class="span3 lightblue">
            <label>Input Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="inputDate" name="inputDate"
                   value="<?php echo $inputDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>


    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Request Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate"
                   value="<?php echo $requestDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate"
                   value="<?php echo $invoiceDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">

        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <label>Original Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="generatedInvoiceNo2" name="generatedInvoiceNo2"
                   value="<?php echo $generatedInvoiceNo2; ?>">

        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
        </div>
        <div class="span1 lightblue">
        </div>
        <div class="span3 lightblue">
            <!--<label>Invoice Method <span style="color: red;">*</span></label>-->
            <?php
            //createCombo("SELECT '1' as id, 'Full Payment' as info UNION
            //      SELECT '2' as id, 'Down Payment' as info;", $invoiceMethod, "", "invoiceMethod", "id", "info", "", "", "select2combobox100",1);
            ?>
            <label>Tax Invoice No.</label>
            <input type="text" class="span12" tabindex="" id="invoiceTax" name="invoiceTax"
                   value="<?php echo $invoiceTax; ?>">
        </div>
        <div class="span1 lightblue">

        </div>
        <div class="span3 lightblue">
            <label>Tax Invoice Date</label>
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="taxDate" name="taxDate"
                   value="<?php echo $taxDate; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
        </div>
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <label>Invoice Method <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT '1' as id, 'Full Payment' as info UNION
              		   SELECT '2' as id, 'Down Payment' as info;", $invoiceMethod, "", "invoiceMethod", "id", "info", "", "", "select2combobox100", 1);
            ?>

        </div>
    </div>
    <div id="addInvoice" class="row-fluid" style="margin-bottom: 7px;">
        <div class="span3 lightblue">
            <button class="btn btn-warning" id="showTransaction">Add Data</button>

        </div>
        <div class="span1 lightblue">

        </div>
        <div class="span3 lightblue">
        </div>
        <div class="span1 lightblue">
        </div>

    </div>
    <div class="row-fluid" id="invoiceDetail" style="display: none;">
        invoice detail
    </div>


    <div class="row-fluid" id="IDP1" style="display: none;">
        Invoice DP
    </div>
    <div class="row-fluid" style="margin-bottom: 7px;">
        <div class="span8 lightblue">
            <label>Remarks</label>
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
</form>
<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel"
     aria-hidden="true" style="width:1200px; height:600px; margin-left:-600px;">
    <form id="insertForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="insertModalLabel">Insert New</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="insertModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>