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

// <editor-fold defaultstate="collapsed" desc="Variable for Vendor Data">

$vendorId = '';
$vendorCode = '';
$vendorName = '';
$vendorAddress = '';
$npwp = '';
$npwp_name = '';
$bankName = '';
$branch = '';
$accountNo = '';
$beneficiary = '';
$swiftCode = '';
$taxable = '';
$ppn = '';
$pph = '';
$active = '';
$nik = '';
$vendorGroupId = '';
$prediksiId = '';
$qtyVessel  = 0;
$readonly = '';


// </editor-fold>

// If ID is in the parameter
if(isset($_POST['prediksiId']) && $_POST['prediksiId'] != '') {
    
    $prediksiId = $_POST['prediksiId'];
    
    $sql = "SELECT * FROM `user` WHERE user_id = {$_SESSION['userId']}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
		//echo $row->user_id;
        if($row->user_id != 46 && $row->user_id != 22) {
            $readonlyProperty = 'readonly';
        }else{
			$readonlyProperty = '';
		}
    }
}
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vendor Data">
    
    $sql = "SELECT ap.*, DATE_FORMAT(ap.PEB_Date, '%d/%m/%Y') AS PEBDate, hc.code_costing
            FROM accrue_prediction ap 
            INNER JOIN header_costing hc ON hc.header_costing_id = ap.header_mst_costing
            WHERE prediction_id  = {$prediksiId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
 
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
        $Code = $rowData->prediction_code;
        $stockpileId = $rowData->stockpile_id;
        $shipmentId = $rowData->shipment_id;
        $customerId = $rowData->customer_id;
        $motherVessel = $rowData->mother_vessel;
        $pebdate = $rowData->PEBDate;
        $currency = $rowData->Kurs_PEB;
        $exchangeRate =  $rowData->exchange_rate; 
        $qtyVessel = $rowData->qty_vessel;
        $headerCostingID = $rowData->header_mst_costing;
        $headerCostingText = $rowData->code_costing;
        $buttonText = 'UPDATE';
        $_method = $buttonText;
        $status = $rowData->status_jurnal;
    }

    // Validasi untuk qty Vessel
    $sql1 = "SELECT * FROM accrue_prediction_detail 
            WHERE prediction_id  = {$prediksiId} AND (STATUS = 1 OR journal_status = 1)";
    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
    $jurnal = $result1->num_rows;
    if ($jurnal > 0){
        $readonly = 'readonly';
    }

}else{
    $buttonText = 'SUBMIT';
    $_method = 'INSERT';
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;

    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";

    if($empty == 1) {
       echo "<option value=''>-- Please Select --</option>";
      
    } else if($empty == 2) {
        echo "<option value=''></option>";
        if($setvalue == 0) {
            echo "<option value='0' selected>-- Please Select MST Code --</option>";
        } else {
            echo "<option value=''>-- Please Select --</option>";
        }
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

?>

<script type="text/javascript">
    $(document).ajaxStop($.unblockUI);
    
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

        $('#qtyVessel').number(true, 2);
        $('#exchangeRate').number(true, 2);
        $('#showMstCosting').hide();

        <?php if($prediksiId != ''){ ?>
            <?php if($currency != 1){ ?>
               $('#divexchange').show();
              
            <?php } ?>
            resetTempCosting('');
            getLoadingPort(<?php echo $headerCostingID ?>, <?php echo $shipmentId ?>, <?php echo $prediksiId ?>);
            $('#showMstCosting').show();
            $(':input[type="submit"]').prop('disabled', true); //submit disable jika detail sudah di approve semua
            insertTempCosting_Edit(<?php echo $prediksiId ?>);
            
       <?php } else  {?>
            getGenerateCodePrediksi();
       <?php } ?>
    
       $('#currency').change(function() {
            $('#divexchange').hide();
            if(document.getElementById('currency').value == 1) {
                $('#exchangeRate').val(1);
            } else if(document.getElementById('currency').value != 1){
                $('#divexchange').show();
            }else{
                $('#divexchange').hide(); 
                $('#exchangeRate').val(0);
            }
        });

    $('#headerCostingID').change(function() {
        $('#divmstcosting').hide();
		//alert('html from to nya');
        if(document.getElementById('headerCostingID').value != '') {
            $('#qtyVessel').val(0);
            insertTempCosting($('select[id="headerCostingID"]').val());
            getLoadingPort($('select[id="headerCostingID"]').val(), '', '');
            //getTempCosting();

            resetShipment(' ');
        }
    });

    
    $('#shipmentId1').change(function() {
        $('#divJournal').hide();
        $('#divCancel').hide();
        if(document.getElementById('shipmentId1').value != '') {
            resetQtyVessel(' ');
            getSalesPrediksi($('select[id="shipmentId1"]').val(), '');
        }else{
            resetQtyVessel('0');
        }
    });

    $('#qtyVessel').change(function() {
        if(document.getElementById('qtyVessel').value != '') {
            hitungCosting1($('input[id="qtyVessel"]').val());
        }
    });

    $('#showMstCosting').click(function (e) {
            e.preventDefault();
            $('#insertModal').modal('show');
            $('#insertModalForm').load('forms/acrrue-prediksi-modal.php', {
                headerCostingID: document.getElementById('headerCostingID').value,
                stockpileId: document.getElementById('stockpileId').value,
                qtyVessel: document.getElementById('qtyVessel').value,
                prediksiId: document.getElementById('prediksiId').value
               
            });
    });

        //SUBMIT FORM ADD NEW DATA
        $("#insertForm").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: './data_processing.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    var returnVal = data.split('|');
                    if (parseInt(returnVal[3]) != 0)	//if no errors
                    { 
                        //alert(parseInt(returnVal[3]));
                        if (returnVal[1] == 'OK') {
                            alertify.alert(returnVal[2]);
                            setTempCosting();
                            $('#insertModal').modal('hide');
                            } else {
                                alertify.alert(returnVal[2]);
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
    
        //SUBMIT DATA AWAL
        $("#prediksiDataForm").validate({
            rules: {
                kodeprediksi: "required",
                stockpileId: "required",
               shipmentId1: "required",
                customerId: "required",
				//motherVessel: "required",
				qtyVessel: "required"
            },
            messages: {
                kodeprediksi: "Prediction Code is a required field.",
                stockpileId: "Stockpile  is a required field.",
               shipmentId1: "Shipment Code is a required field.",
                customerId: "Buyer is a required field.",
             //  motherVessel: "Mother Vessel is a required field.",
                qtyVessel: "Qty Vessel is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#prediksiDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('generalprediksiId').value = returnVal[3];
                                
                                if(returnVal[3] > 0 ){
                                    $('#dataContent').load('forms/accrue-prediksi-forms.php', { prediksiId: returnVal[3] }, iAmACallbackFunction2);
                                }else{
                                    $('#dataContent').load('views/accrue-prediksi-views.php', { prediksiId: returnVal[3] }, iAmACallbackFunction2);
                                }

                            } 
                        }
                    }
                });
            }
        });

        //ADD QTY TIMBANGAN | TONGKANG
        $("#editCostingForm").validate({
            submitHandler: function (form) {
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#editCostingForm").serialize(),
                    success: function (data) {
                       // console.log(data);
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                alertify.set({
                                    labels: {
                                        ok: "OK"
                                    }
                                });
                              alertify.alert(returnVal[2]);
                                setTempCosting();
                              //  alert(returnval[3]);
                                $('#editCostingModal').modal('hide');
                            } else {
                                document.getElementById('modalErrorMsgInsert').innerHTML = returnVal[2];
                                $("#modalErrorMsgInsert").show();
                            }
                        }
                    }
                });
            }
        });
    });


    //INSERT QTY TIMBANGAN/TONGKANG
    function editDetail(tempID) {
        $("#modalErrorMsg").hide();
        $('#editCostingModal').modal('show');
        $('#editCostingModalForm').load('forms/qty-costing-modal.php', {tempID: tempID}, iAmACallbackFunction2);	//and hide the rotating gif
    }

    function checkCosting() {
        $('.check1').change(function (e) {
            document.getElementById('check2' + this.value).disabled = this.checked;
        });

    }

    function checkAll(all)
    {

            var tempCheck = document.getElementsByName("checkedCosting[id][]");
            var count = tempCheck.length;
            if (all.checked) {
                for (a=0;a<count;a++)
                {
                    tempCheck[a].checked=true;
                }
            }else{
                for (a=0;a<count;a++)
                {
                    tempCheck[a].checked=false;
                }
            }
      
    }

    function resetTempCosting(text) {
        document.getElementById('mstcosting').innerHTML = '';
    }

    function insertTempCosting(headerCostingID) { //insert data ke temp_mst_costing
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'insertTempCosting',
                headerCostingID: headerCostingID
                   
            },
            success: function(data){
                if(data != '') {
                    setTempCosting();
                }else{
                    setTempCosting();
                }
            }
        });
    }

    function insertTempCosting_Edit(prediksiId) { //insert data ke temp_mst_costing
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'insertTempCosting_Edit',
                prediksiId: prediksiId
                   
            },
            success: function(data){
                if(data != '') {
                    setTempCosting();
                }else{
                    setTempCosting();
                }
            }
        });
    }
    
    function setTempCosting() { //MENAMPILKAN  temp_mst_costing
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getTempCosting'
            },
            success: function (data) {
                if (data != '') {
                    $('#divmstcosting').show();
                    $(':input[type="submit"]').prop('disabled', false);  //submit enable jika detail masih ada
                    document.getElementById('mstcosting').innerHTML = data;
                } 
            }
        });
    } 

    function hitungCosting1(qtyVessel) { //HITUNG JIKA QTY VESSEL DI INPUT
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'hitungCosting1',
                    qtyVessel: qtyVessel
            },
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    setTempCosting(); // UNTUK MEREFRESH TABEL temp_mst_costing
                }
            }
        });
    }

    function resetShipment(text) {
        document.getElementById('shipmentId1').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId1').options.add(x);
    }

	function setShipmentCosting(type, stockpileId, shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentCosting',
                    stockpileId: stockpileId,
                    newshipment: shipmentId
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
                    if(returnValLength > 0) {
                        document.getElementById('shipmentId1').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId1').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId1').options.add(x);
                        
                    }

                    if(type == 1) {
                        $('#shipmentId1').find('option').each(function(i,e){
                            if($(e).val() == shipmentId){
                                $('#shipmentId1').prop('selectedIndex',i);
                                
                                $("#shipmentId1").select2({
                                    width: "100%",
                                    placeholder: shipmentId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetQtyVessel() {
        document.getElementById('qtyVessel').value = 0;
    }

    function getLoadingPort(headerCostingID, shipmentId, prediksiId) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getLoadingPort',
                    headerCostingID: headerCostingID
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('stockpileId').value = returnVal[1];
                        document.getElementById('stockpileValue').value = returnVal[2];
                        if(prediksiId != '' && shipmentId != ''){
                            setShipmentCosting(1, returnVal[1], shipmentId);
                            getSalesPrediksi(shipmentId, prediksiId);
                            document.getElementById('headerCostingID').value = headerCostingID;
                        }else{
                            setShipmentCosting(0, returnVal[1], 0);
                        }
                       
                       
                    }
                }
            });
    }

    function getSalesPrediksi(shipmentId, prediksiId) {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getSalesPrediksi',
                        shipmentId: shipmentId
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        if(prediksiId == ''){
                            document.getElementById('qtyVessel').value = returnVal[1];

                        }
                        document.getElementById('customerId').value = returnVal[2];
                        document.getElementById('customerName').value = returnVal[3];
                        document.getElementById('motherVessel').value = returnVal[4];
                        document.getElementById('pebdate').value = returnVal[5];
                        document.getElementById('currency').value = returnVal[6];
                        document.getElementById('currencyCode').value = returnVal[7];
                        document.getElementById('exchangeRate').value = returnVal[8];
                        document.getElementById('salesId').value = returnVal[9];
                        if( returnVal[6] > 1){
                            $('#divexchange').show();
                        }else{
                            $('#divexchange').hide();
                        }
                        if(prediksiId != '' && returnVal[5] != ''){
                            $('#divJournal').show();
                            $('#divCancel').show();
                        }else{
                            $('#divJournal').hide();
                            $('#divCancel').hide();
                        }

                        hitungCosting1($('input[id="qtyVessel"]').val());
                    }
                }
            });
    }

        function getGenerateCodePrediksi() {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getGenerateCodePrediksi',
                    },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	
                    {
                        document.getElementById('kodeprediksi').value = returnVal[1];
                    }
                }
            });
        }

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

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }
    
</script>

<form method="post" id="prediksiDataForm">
    <input type="hidden" name="action" id="action" value="prediksi_costing_data" />
    <input type="hidden" name="prediksiId" id="prediksiId" value="<?php echo $prediksiId; ?>" />
    <!-- <input type="hidden" name="_method" id="_method" value="<?php echo $_method; ?>" /> -->
    <input type="hidden" name="salesId" id="salesId"/>


    <div class="row-fluid">   
        <div class="span3 lightblue">
            <label>Generate code <span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="2" id="kodeprediksi" name="kodeprediksi" value="<?php echo $Code ?>">
        </div>
        <div class="span3 lightblue">
            <label>Code MST Costing<span style="color: red;">*</span></label>
            <?php
                 if($prediksiId == '' || $prediksiId == 0){
                    createCombo("SELECT header_costing_id, code_costing FROM header_costing", $headerCostingID, "", "headerCostingID", "header_costing_id", "code_costing",
                                "", 1, "select2combobox100");
                 }else{
            ?>
                <input type="hidden" class="span12" readonly tabindex="2"  id="headerCostingID" name="headerCostingID">
                <input type="text" class="span12"  tabindex="2" readonly id="headerCostingText" name="headerCostingText" value="<?php echo $headerCostingText ?>">
            <?php } ?>
        </div>
        <div class="span3 lightblue">
            <label>Shipment Code <span style="color: red;"></span></label>
            <?php
                createCombo("", "", "", "shipmentId1", "shipment_id", "shipment_no",
                "", 21, "select2combobox100",2);
            ?>             
             <input type="hidden" class="span12" id="oldshipmentId" name="oldshipmentId" value="<?php echo $shipmentId ?>">
        </div> 
      
    </div>
    <br>
    <div class="row-fluid">  
    <div class="span3 lightblue">
            <label>Loading Port <span style="color: red;">*</span></label>
            <input type="hidden" class="span12" readonly tabindex="2"  id="stockpileId" name="stockpileId">
            <input type="text" class="span12"  tabindex="2" readonly id="stockpileValue" name="stockpileValue">

    </div>
        <div class="span3 lightblue">
            <label>Buyer <span style="color: red;">*</span></label>
            <input type="hidden" class="span7"  tabindex="13" id="customerId" name="customerId">
            <input type="text" class="span12" readonly tabindex="13" id="customerName" name="customerName">
        </div>

        <div class="span3 lightblue">
            <label>Mother Vessel<span style="color: red;">*</span></label>
            <input type="text" class="span12" readonly tabindex="13" id="motherVessel" name="motherVessel" value="<?php echo $motherVessel ?>">
        </div>
    </div>

    <div class="row-fluid">  
        <div class="span3 lightblue">
            <label>PEB Date <span style="color: red;"></span></label>
            <input type="text" placeholder="DD/MM/YYYY" readonly tabindex="" id="pebdate" value = "<?php echo $pebdate; ?>" name="pebdate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>

        <div  class="span7">
        <div class="span3 lightblue">
            <label>PEB Kurs <span style="color: red;">*</span></label>
            <input type="hidden" class="span7"  tabindex="13" id="currency" name="currency">
            <input type="text" class="span7" readonly tabindex="13" id="currencyCode" name="currencyCode">      
        </div>
        <div class="span4 lightblue"  id="divexchange"  style="display: none;" >
            <label>Exchange Rate</label>
            <input type="text" class="span12" readonly tabindex="13" id="exchangeRate" name="exchangeRate" value="<?php echo $exchangeRate ?>">
        </div>
        <div class="span3 lightblue">
            <label> Qty Vessel <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="2" id="qtyVessel" name="qtyVessel" value="<?php echo $qtyVessel ?>" <?php echo $readonly ?>>
        </div>
    </div>
    </div>
    <br>
    <div class="row-fluid" id = "divmstcosting" style="display: none;">  
        <button class="btn btn-warning"  id="showMstCosting" style="display: none;">Add Data</button>
        <h4>Detail Costing</h4>
        <div class="row-fluid" id="mstcosting">
            detail costing
        </div>
    </div>

    <div class="row-fluid">
        <div class="span1 lightblue" style="width: 45px;">
            <button class="btn" type="button" onclick="back()">BACK</button>
        </div>
        <div class="span1 lightblue" style="width: 60px;">
            <input type="submit" class="btn btn-primary" id="_method" name="_method" value="<?php echo $_method ?>">
        </div>
            <div class="span1 lightblue" id = "divJournal" style="display: none; width: 100px;">
                <?php if($prediksiId != ''){ ?> <!-- Muncul jika Update/edit data -->
                    <input type="submit" class="btn btn-warning" id="_method1" name="_method1" value="Create Journal">
                <?php } ?>
            </div>
            <div class="span1 lightblue" id = "divCancel" style="display: none; width: 100px;">
                <?php if($prediksiId != ''){ ?> <!-- Muncul jika Update/edit data -->
                    <input type="submit" class="btn btn-danger" id="_method2" name="_method2" value="Cancel Prediksi">
                <?php } ?>
            </div>
       
    </div>
</form>

<div id="editCostingModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editCostingModalLabel"
     aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;">
    <form id="editCostingForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">×</button>
            <h3 id="editCostingModalLabel">Insert Qty</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsgInsert" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="editCostingModalForm">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeInsertModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

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
