<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Notim';

$date = new DateTime();
$transactionDate = $date->format('d/m/Y');
$transactionDate2 = $date->format('d/m/Y');
//$unloadingDate = $date->format('d/m/Y');
$transactionType = 1;
$allowUnloading = false;
$allowContract = false;
$allowFreight = false;
$allowHandling = false;
$allowSales = false;
$allowShipment = false;
$allowVendor = false;
$allowCustomer = false;
$allowLabor = false;
$allowSupplier = false;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 7) {
            $allowUnloading = true;
        } elseif($row->module_id == 8) {
            $allowContract = true;
        } elseif($row->module_id == 9) {
            $allowFreight = true;
			$allowHandling = true;
        } elseif($row->module_id == 10) {
            $allowSales = true;
        } elseif($row->module_id == 11) {
            $allowVendor = true;
        } elseif($row->module_id == 12) {
            $allowCustomer = true;
        } elseif($row->module_id == 13) {
            $allowLabor = true;
        } elseif($row->module_id == 16) {
            $allowSupplier = true;
        }
    }
}

if(isset($_SESSION['transaction'])) {
    $stockpileId = $_SESSION['transaction']['stockpileId'];
    $transactionType = $_SESSION['transaction']['transactionType'];
    $unloadingCostId = $_SESSION['transaction']['unloadingCostId'];
    $vendorId = $_SESSION['transaction']['vendorId'];
    $freightCostId = $_SESSION['transaction']['freightCostId'];
	$handlingCostId = $_SESSION['transaction']['handlingCostId'];
    $stockpileContractId = $_SESSION['transaction']['stockpileContractId'];
	$contractPksDetailId = $_SESSION['transaction']['contractPksDetailId'];
    $customerId = $_SESSION['transaction']['customerId'];
    $salesId = $_SESSION['transaction']['salesId'];
    $shipmentId = $_SESSION['transaction']['shipmentId'];
	$idSuratTugas = $_SESSION['transaction']['idSuratTugas'];
	$laborId = $_SESSION['transaction']['laborId'];
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT style='width: 150px;' class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange >";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Tiket Timbangan --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select --</option>";
        //echo "<option value='NONE'>NONE</option>";
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
    
    $(document).ready(function(){	//executed after the page has loaded
        $('.combobox').combobox();
        
        $(".select2combobox100").select2({
            width: "100%"
        });
        
        $(".select2combobox75").select2({
            width: "75%"
        });
        
        $(".select2combobox50").select2({
            width: "50%"
        });
//        $('.select2combobox').selectize({
//            allowEmptyOption: true,
//            create: true,
//            sortField: 'text'
//        });
        
       // $('#sendWeight').number(true, 2);
        
       // $('#brutoWeight').number(true, 2);
        
       // $('#tarraWeight').number(true, 2);

       $('#qty_rsb').number(true, 2);
        $('#qty_ggl').number(true, 2);
        $('#qty_RG').number(true, 2);
        $('#qty_uncertified').number(true, 2);
        $('#availabel_rsb').number(true, 2);
        $('#availabel_ggl').number(true, 2);
        $('#availabel_uncertified').number(true, 2);
        $('#availabel_rsb_ggl').number(true, 2);

        
        $('#sendWeight2').number(true, 2);
        
        $('#blWeight').number(true, 2);
        
        $('#showTransaction').click(function(e){
            e.preventDefault();
            
            $("#transactionContainer").show();
            $("#hideTransaction").show();
            $("#printTransaction").show();
            $("#showTransaction").hide();
        });
        
        $('#hideTransaction').click(function(e){
            e.preventDefault();
            
            $("#transactionContainer").hide();
            $("#showTransaction").show();
            $("#printTransaction").hide();
            $("#hideTransaction").hide();
        });

        $('#rsb1').click(function(e){ //rsb
            e.preventDefault();
            if(document.getElementById('rsb1').value == 1 && document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#qty_rsb").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        $("#sendWeight2").val(a);  
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#qty_rsb, #qty_ggl").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat(a) + parseFloat(b); 
                        $("#sendWeight2").val(c);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_RG").show();
                $("#qty_rsb, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var c = parseFloat($("#qty_uncertified").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#qty_rsb, #qty_ggl, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val())
                        var d = parseFloat(a) + parseFloat(b) + parseFloat(c) ; 
                        $("#sendWeight2").val(e);  
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_uncertified").val())
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c); 
                        $("#sendWeight2").val(e);  
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_RG").val())
                        var c = parseFloat($("#qty_uncertified").val())
                        var d = parseFloat(a) + parseFloat(b) + parseFloat(c); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }
            else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val())
                        var d = parseFloat($("#qty_uncertified").val())
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }
            else{
                $("#div_qty_rsb").hide();
                $("#sendWeight2").val(0);  
                $("#qty_rsb").val(0);  
            }
        });

        $('#ggl1').click(function(e){ //GGL
            e.preventDefault();
            if(document.getElementById('rsb1').value == 0 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('ggl1').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_ggl").show();
                $("#qty_ggl").keyup( 
                    function(){
                        var b = parseFloat($("#qty_ggl").val());
                        $("#sendWeight2").val(b);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#qty_rsb, #qty_ggl").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat(a) + parseFloat(b); 
                        $("#sendWeight2").val(c);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#qty_ggl, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_ggl").show();
                $("#div_UN").show();
                $("#qty_ggl, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_uncertified").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            } else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e =  parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) + parseFloat(b) +  parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            } else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#qty_rsb, #qty_ggl, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) ; 
                        $("#sendWeight2").val(e);  
                    }
                );
            }
            else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else{
                $("#div_qty_ggl").hide();
                $("#sendWeight2").val(0);  
                $("#qty_ggl").val(0);  
            }
        });

        $('#rsb_ggl').click(function(e){ //RSB & GGL
            e.preventDefault();
            if(document.getElementById('rsb1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('ggl1').value == 0 && document.getElementById('uncertified').value == 0){
                $("#div_RG").show();
                $("#qty_RG").keyup( 
                    function(){
                        var b = parseFloat($("#qty_RG").val());
                        $("#sendWeight2").val(b);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#qty_RG, #qty_ggl").keyup( 
                    function(){
                        var a = parseFloat($("#qty_RG").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat(a) + parseFloat(b); 
                        $("#sendWeight2").val(c);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_RG").show();
                $("#qty_rsb, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_UN").show();
                $("#div_RG").show();
                $("#qty_uncertified, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_uncertified").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e =  parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) +  parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 0){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#qty_rsb, #qty_ggl, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) ; 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else{
                $("#div_RG").hide();
                $("#sendWeight2").val(0);  
                $("#qty_RG").val(0);  
            }
        });
        $('#uncertified').click(function(e){ //UNCERTIFIED
            e.preventDefault();
            if(document.getElementById('rsb1').value == 0 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('ggl1').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_UN").show();
                $("#qty_uncertified").keyup( 
                    function(){
                        var b = parseFloat($("#qty_uncertified").val());
                        $("#sendWeight2").val(b);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_UN").show();
                $("#div_qty_rsb").show();
                $("#qty_uncertified, #qty_rsb").keyup( 
                    function(){
                        var a = parseFloat($("#qty_uncertified").val());
                        var b = parseFloat($("#qty_rsb").val());
                        var c = parseFloat(a) + parseFloat(b); 
                        $("#sendWeight2").val(c);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_ggl").show();
                $("#div_UN").show();
                $("#qty_ggl, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_uncertified").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_uncertified, #qty_RG").keyup( 
                    function(){
                        var a = parseFloat($("#qty_uncertified").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat(a) + parseFloat(c); 
                        $("#sendWeight2").val(d);  
                
                    }
                );
            }else if(document.getElementById('rsb1').value == 0 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 0 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) +  parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 0 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else if(document.getElementById('rsb1').value == 1 &&  document.getElementById('ggl1').value == 1 && document.getElementById('rsb_ggl').value == 1 && document.getElementById('uncertified').value == 1){
                $("#div_qty_rsb").show();
                $("#div_qty_ggl").show();
                $("#div_RG").show();
                $("#div_UN").show();
                $("#qty_rsb, #qty_ggl, #qty_RG, #qty_uncertified").keyup( 
                    function(){
                        var a = parseFloat($("#qty_rsb").val());
                        var b = parseFloat($("#qty_ggl").val());
                        var c = parseFloat($("#qty_RG").val());
                        var d = parseFloat($("#qty_uncertified").val());
                        var e = parseFloat(a) + parseFloat(b) + parseFloat(c) + parseFloat(d); 
                        $("#sendWeight2").val(e);  
                    }
                );
            }else{
                $("#div_UN").hide();
                $("#sendWeight2").val(0);  
                $("#qty_uncertified").val(0);  
            }
        });

        // ZONA VALIDASI INPUTAN RSB, GGL, RSB-GGL, UNCERTIFIED

        $('#qty_rsb').change(function() {
            getValidationRSB($('select[id="stockpileId"]').val(), $('input[id="qty_rsb"]').val());
        });

        $('#qty_ggl').change(function() {
            getValidationGGL($('select[id="stockpileId"]').val(), $('input[id="qty_ggl"]').val());
        });
        $('#qty_RG').change(function() {
            getValidationRG($('select[id="stockpileId"]').val(), $('input[id="qty_RG"]').val());
        });

        $('#qty_uncertified').change(function() {
            getValidationUN($('select[id="stockpileId"]').val(), $('input[id="qty_uncertified"]').val(), $('input[id="transactionDate2"]').val());
        });

        
        $('#printTransaction').click(function(e){
            e.preventDefault();
            
            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#transactionContainer").printThis();
//            $("#transactionContainer").hide();
        });
        
        <?php
        if(isset($_SESSION['transaction']) && $transactionType == 1) {
            ?>
            if(document.getElementById('stockpileId').value != '') {
                
                
                <?php
                
                
                //if($vendorId != '') {
				if($idSuratTugas != '') {
                ?>
                //setVendor(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>);
                setSuratTugas(1, $('select[id="stockpileId"]').val(), <?php echo $idSuratTugas; ?>);
				setSuratTugasDetail(<?php echo $idSuratTugas; ?>);
				
				resetHandling('  ');
                resetFreight('  ');
                resetVehicle('  ');
				resetLabor('  ');
				resetContract('  ');
				resetContractPksDetail(' ');
				<?php
                if($unloadingCostId != '') {
                ?>
                setUnloading(1, $('select[id="stockpileId"]').val(), <?php echo $unloadingCostId; ?>);
                setUnloadingDetail(<?php echo $unloadingCostId; ?>);
                <?php
                } else {
                ?>
                setUnloading(0, $('select[id="stockpileId"]').val(), 0);
                
               // resetContract(' Contract ');
				//resetSuratTugas(' Vendor ');
                //resetFreight(' Contract ');
				//resetHandling(' Contract ');
                
                <?php
                }
				?>
				<?php
                if($laborId != '') {
                ?>
                setLabor(1, $('select[id="stockpileId"]').val(), <?php echo $laborId; ?>);
                //setUnloadingDetail(<?php echo $unloadingCostId; ?>);
                <?php
                } else {
                ?>
                setLabor(0, $('select[id="stockpileId"]').val(),0);
                
               // resetContract(' Contract ');
				//resetSuratTugas(' Vendor ');
                //resetFreight(' Contract ');
				//resetHandling(' Contract ');
                
                <?php
                }
				?>
				<?php if($vendorId != '') {
				//if($idSuratTugas != '') {
                ?>
                setVendor(1, $('select[id="idSuratTugas"]').val(), <?php echo $vendorId; ?>);
                //setSuratTugas(1, $('select[id="stockpileId"]').val(), <?php echo $idSuratTugas; ?>);
				//setSuratTugasDetail(<?php echo $idSuratTugas; ?>);
				
				//resetHandling(' ');
                //resetFreight(' ');
               // resetContract(' ');
				//resetSuratTugas(' ');
				<?php
					if($stockpileContractId != '') {
                ?>
				//setSuratTugas(1, $('select[id="stockpileId"]').val(), <?php echo $idSuratTugas; ?>);
                setContract(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $stockpileContractId; ?>);
				setContractDetail(<?php echo $stockpileContractId; ?>);
               // setSuratTugasDetail(<?php echo $idSuratTugas; ?>);
                <?php
                 } else {
                ?>
                setContract(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0); 
				//setSuratTugas(0, $('select[id="stockpileId"]').val(), 0); 				
                <?php
                   }
				   if($freightCostId != '') {
                ?>
                setFreight(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $freightCostId; ?>,document.getElementById('unloadingDate').value);
                setFreightDetail(<?php echo $freightCostId; ?>);
                <?php
                    } else {
                ?>
                setFreight(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0,document.getElementById('unloadingDate').value);
                
				 <?php
					}
					
                 if($handlingCostId != '') {
                ?>
                setHandling(1, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, <?php echo $handlingCostId; ?>);
                setHandlingDetail(<?php echo $handlingCostId; ?>);
                <?php
                    } else {
                ?>
                setHandling(0, $('select[id="stockpileId"]').val(), <?php echo $vendorId; ?>, 0);
                <?php
                    }
				}else{
				?>
				setVendor(0, $('select[id="idSuratTugas"]').val(), 0);
				
                <?php
				}
                    
                
                } else {
                ?>
                //setVendor(0, $('select[id="stockpileId"]').val(), 0);
				setSuratTugas(0, $('select[id="stockpileId"]').val(), 0); 	
                <?php
                }
                ?>
			}
            <?php
        } if(isset($_SESSION['transaction']) && $transactionType == 2) {
            ?>
            if(document.getElementById('customerId').value != '') {
                resetShipment(' Sales ');
                <?php
                if($salesId != '') {
                ?>
                resetShipment(' ');
                setSales(1, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), <?php echo $salesId; ?>);
                <?php
                    if($shipmentId != '') {
                ?>
                setShipment(1, <?php echo $salesId; ?>, <?php echo $shipmentId; ?>);
                setShipmentDetail(<?php echo $shipmentId; ?>);
                setFreightCostSales(1, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), $('select[id="salesId"]').val(),<?php echo $freightCostSalesId; ?>);
                <?php
                    } else {
                ?>
                setShipment(0, <?php echo $salesId; ?>, 0);
                setFreightCostSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), $('select[id="salesId"]').val(),0);
                <?php
                    }
                } else {
                ?>
                setSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), 0);
                <?php
                }
                ?>
            }
            <?php
        }
        ?>
                
        if(document.getElementById('transactionType').value == 1) {
            $('#inTransaction').show();
            $('#outTransaction').hide();
        } else if(document.getElementById('transactionType').value == 2) {
            $('#inTransaction').hide();
            $('#outTransaction').show();
        }
        
        $('#insertModal').on('hidden', function () {
            // do something…
            if(document.getElementById('vendorId').value == 'INSERT') {
                setVendor(0, $('select[id="stockpileId"]').val(), 0);
            } else if(document.getElementById('unloadingCostId').value == 'INSERT') {
                setUnloading(0, $('select[id="stockpileId"]').val(), 0);
            } else if(document.getElementById('laborId').value == 'INSERT') {
                setLabor(0, 0);
            } else if(document.getElementById('stockpileContractId').value == 'INSERT') {
               // setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('freightCostId').value == 'INSERT') {
                setFreight(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0,document.getElementById('unloadingDate').value);
            } else if(document.getElementById('handlingCostId').value == 'INSERT') {
                setHandling(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('supplierId').value == 'INSERT') {
                setSupplier(0, 0);
            } else if(document.getElementById('customerId').value == 'INSERT') {
                setCustomer(0, 0);
            } else if(document.getElementById('salesId').value == 'INSERT') {
                setSales(0, $('select[id="customerId"]').val(), 0);
            }
        });
       
        
        $('#transactionType').change(function() {
            resetVehicle('  ');
			resetUnloadingDetail();
            //resetVendor();
            resetFreight('  ');
            resetFreightDetail();
			resetHandling('  ');
            resetHandlingDetail();
            resetLabor('  ');
            resetContract('  ');
			resetContractDetail();
			resetSuratTugas(' ');
            resetSuratTugasDetail();
            resetSales(' Buyer ');
            resetShipment(' Buyer ');
            resetShipmentDetail();
			resetContractPksDetail(' PKS Detail ');
            resetFreightCostSalesDetail();
			resetFreightCostSales(' Buyer ');

            
//            alert(document.getElementById('transactionType').value);
            document.getElementById('stockpileId').value = '';
            document.getElementById('shipmentId').value = '';
            
            $("#stockpileId").select2({
                width: "75%",
                placeholder: document.getElementById('stockpileId').value
            });
            
            $("#shipmentId").select2({
                width: "100%",
                placeholder: document.getElementById('shipmentId').value
            });
            
            $('#inTransaction').hide();
            $('#outTransaction').hide();
            
            if(document.getElementById('transactionType').value == 1) {
                $('#inTransaction').show();
                $('#outTransaction').hide();
            } else if(document.getElementById('transactionType').value == 2) {
                $('#inTransaction').hide();
                $('#outTransaction').show();
            }
        });
        
        $('#stockpileId').change(function() {
//            $('#clientId').val('');
            resetVehicle('  ');
			resetUnloadingDetail();
            resetFreight('  ');
            resetFreightDetail();
			resetHandling('  ');
            resetHandlingDetail();
            resetLabor('  ');
			resetContract('  ');
			resetContractDetail();
			resetSuratTugas('  ');
            resetSuratTugasDetail();
			resetContractPksDetail(' PKS Detail ');
            resetFreightCostSalesDetail();
			resetFreightCostSales(' Buyer ');
            //alert ('test');
            if(document.getElementById('stockpileId').value != '') {
                
                setSuratTugas(0, $('select[id="stockpileId"]').val(), 0);
               // resetContract(' Vendor ');
                //resetVendor();
				//resetFreight('  ');
				//resetSuratTugas(' Vendor ');
				//resetHandling('  ');
				//resetVehicle('  ');
            }
        });
        
        $('#customerId').change(function() {
            resetSales(' Buyer ');
            resetShipment(' Sales ');
            resetShipmentDetail();
            
            
            if(document.getElementById('customerId').value != '' && document.getElementById('customerId').value != 'INSERT') {
                resetSales(' ');
                setSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), 0);
            } else if(document.getElementById('customerId').value != '' && document.getElementById('customerId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-customer.php', {});
            } 
        });
        
        $('#salesId').change(function() {
            resetShipment(' Sales ');
            resetShipmentDetail();
            resetFreightCostSales(' Buyer ');

            $("#div_qty_rsb").hide();
            $("#div_qty_ggl").hide();
            $("#div_RG").hide();
            $("#sendWeight2").val(0);  
            $("#qty_ggl").val(0);  
            $("#qty_rsb").val(0); 
            $("#qty_RG").val(0);  
            document.getElementById('ggl_rsb_shipment').innerHTML = '';

            
            if(document.getElementById('salesId').value != '' && document.getElementById('salesId').value != 'INSERT') {
                resetShipment(' ');
                resetFreightCostSales(' ');
                setShipment(0, $('select[id="salesId"]').val(), 0);
                getAvailableQtyAll($('select[id="stockpileId"]').val(),$('input[id="transactionDate2"]').val());
                setFreightCostSales(0, $('select[id="customerId"]').val(), $('select[id="stockpileId"]').val(), $('select[id="salesId"]').val(), 0);

            } else if(document.getElementById('salesId').value != '' && document.getElementById('salesId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-sales.php', {customerId: $('select[id="customerId"]').val()});
            }
        });
        
//        $('#shipmentId').change(function() {
//            if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
//                $("#modalErrorMsgInsert").hide();
//                $('#insertModal').modal('show');
//                $('#insertModalForm').load('forms/transaction-shipment.php', {});
//            }
//        });
        
        /*$('#vendorId').change(function() {
            resetFreight(' Contract ');
            resetFreightDetail();
			resetHandling(' Contract ');
            resetHandlingDetail();
            resetContract(' Vendor ');
			resetContractDetail();
			//resetSuratTugas(' Vendor ');
            //resetSuratTugasDetail();
            //document.getElementById('supplierId').value = '';
//            $('#supplierId').attr("disabled", true);
            
            if(document.getElementById('vendorId').value != '') {
                resetFreight(' ');
				resetHandling(' ');
                resetContract(' ');
				//alert('test');
				
                setFreight(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
				setHandling(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
                setContract(0, $('select[id="idSuratTugas"]').val(), $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
				//setSuratTugas(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0);
            } else if(document.getElementById('vendorId').value != '' && document.getElementById('vendorId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-vendor.php', {});
            } 
        });*/
        
        $('#idSuratTugas').change(function() {
            resetSuratTugasDetail();
			//resetVendor();
			resetLabor('  ');
			resetFreight('  ');
            resetFreightDetail();
			resetHandling('  ');
            resetHandlingDetail();
			resetVehicle('  ');
            resetUnloadingDetail();
			resetContract(' ');
			resetContractDetail();
			resetContractPksDetail(' PKS Detail ');
            refreshsetVendorGGL_SRB('');
            resetSpBlock('');

            if(document.getElementById('idSuratTugas').value != '' && document.getElementById('idSuratTugas').value != 'INSERT') {
                setSuratTugasDetail($('select[id="idSuratTugas"]').val());
				//setUnloading(0, $('select[id="stockpileId"]').val(), 0);
				//setVendor(0, $('select[id="idSuratTugas"]').val(), 0);
				//resetContract(' ');
				//resetContractDetail();
            } /*else if(document.getElementById('idSuratTugas').value != '' && document.getElementById('idSuratTugas').value == 'INSERT') {
//                resetFreight(' Contract ');
//                resetFreightDetail();
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-contract.php', {stockpileId: $('select[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } */
        });
		
		$('#stockpileContractId').change(function() {
            resetContractDetail();
			resetContractPksDetail(' PKS Detail ');
            if(document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value != 'INSERT') {
                setContractDetail($('select[id="stockpileContractId"]').val());
            } else if(document.getElementById('stockpileContractId').value != '' && document.getElementById('stockpileContractId').value == 'INSERT') {
//                resetFreight(' Contract ');
//                resetFreightDetail();
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-contract.php', {stockpileId: $('select[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });
        
        $('#shipmentId').change(function() {
            document.getElementById('ggl_rsb_shipment').innerHTML = '';
            if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value != 'INSERT') {
                setShipmentDetail($('select[id="shipmentId"]').val());
                setShipmentGGL_SRB($('select[id="salesId"]').val());
            } else if(document.getElementById('shipmentId').value != '' && document.getElementById('shipmentId').value == 'INSERT') {
                resetShipmentDetail();
                refreshsetVendorGGL_SRB();

                
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-shipment.php', {salesId: $('select[id="salesId"]').val()});
            } else {
                resetShipmentDetail();
                refreshsetVendorGGL_SRB();

            }
        });
        
        $('#unloadingCostId').change(function() {
            resetUnloadingDetail();
            if(document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value != 'INSERT') {
                setUnloadingDetail($('select[id="unloadingCostId"]').val());
            } else if(document.getElementById('unloadingCostId').value != '' && document.getElementById('unloadingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-unloading.php', {stockpileId: $('select[id="stockpileId"]').val()});
            } 
        });
        
        $('#freightCostId').change(function() {
            resetFreightDetail();
            if(document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value != 'INSERT') {
                setFreightDetail($('select[id="freightCostId"]').val());
            } else if(document.getElementById('freightCostId').value != '' && document.getElementById('freightCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-freight.php', {stockpileId: $('select[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });

        $('#freightCostSalesId').change(function () {
            resetFreightCostSalesDetail();
            if (document.getElementById('freightCostSalesId').value != '' && document.getElementById('freightCostSalesId').value != 'INSERT') {
                setFreightCostSalesDetail($('select[id="freightCostSalesId"]').val());
            } 
        });
		
		$('#handlingCostId').change(function() {
            resetHandlingDetail();
            if(document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value != 'INSERT') {
                setHandlingDetail($('select[id="handlingCostId"]').val());
            } else if(document.getElementById('handlingCostId').value != '' && document.getElementById('handlingCostId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-handling.php', {stockpileId: $('select[id="stockpileId"]').val(), vendorId: $('select[id="vendorId"]').val()});
            } 
        });
        
        $('#laborId').change(function() {
            if(document.getElementById('laborId').value != '' && document.getElementById('laborId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-labor.php', {});
            } 
        });
        
        /*$('#supplierId').change(function() {
            if(document.getElementById('supplierId').value != '' && document.getElementById('supplierId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/transaction-supplier.php', {});
            } 
        });*/
        
        $("#transactionDataForm").validate({
            rules: {
                stockpileId: "required",
                transactionType: "required",
                loadingDate: "required",
                stockpileContractId: "required",
                vehicleNo: "required",
                driver: "required",
                unloadingDate: "required",
                unloadingCostId: "required",
                permitNo: "required",
                freightCostId: "required",
				//handlingCostId: "required",
                sendWeight: "required",
                brutoWeight: "required",
                tarraWeight: "required",
                block: "required",
                vendorId: "required",
                transactionDate2: "required",
                sendWeight2: "required",
                blWeight: "required",
                vehicleNo2: "required",
                shipmentId: "required",
                freightCostSalesId: "required",
				doSales: "required",
				driverSales: "required",
				sim: "required"
            },
            messages: {
                stockpileId: "Stockpile is a required field.",
                transactionType: "Type is a required field.",
                loadingDate: "Loading Date is a required field.",
                stockpileContractId: "PO No. is a required field.",
                vehicleNo: "Vehicle No. is a required field.",
                driver: "Driver is a required field.",
                unloadingDate: "Unloading Date is a required field.",
                unloadingCostId: "Vehicle is a required field.",
                permitNo: "Permit No. is a required field.",
                freightCostId: "Supplier Freight is a required field.",
				//handlingCostId: "Handling Cost is a required field.",
                sendWeight: "Sent Weight is a required field.",
                brutoWeight: "Bruto Weight is a required field.",
                tarraWeight: "Tarra Weight is a required field.",
                block: "Block is a required field.",
                vendorId: "Contract Name is a required field.",
                transactionDate2: "Transaction Date is a required field.",
                sendWeight2: "Stockpile Weight is a required field.",
                blWeight: "BL Weight is a required field.",
                vehicleNo2: "Vessel Name is a required field.",
                shipmentId: "Sales Agreement No. is a required field.",
                freightCostSalesId: "This is a required field.",
				doSales: "This is a required field.",
				driverSales: "This is a required field.",
				sim: "This is a required field."
            },
            submitHandler: function(form) {
                $('#submitButton').attr("disabled", true);
				$.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#transactionDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[3]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                $('#pageContent').load('views/notim-new.php', {}, iAmACallbackFunction);
                            } 
                            $('#submitButton').attr("disabled", false);
                        }
                    }
                });
            }
        });
        
        $("#insertForm").validate({
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#insertForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            if (returnVal[1] == 'OK') {
                                var resultData = returnVal[3].split('~');
                                
                                if (resultData[0] == 'CONTRACT') {
                                    setContract(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
//                                    resetFreight(' ');
//                                    setFreight(0, resultData[1], 0);
                                    setContractDetail(resultData[1]);
                                } else if (resultData[0] == 'UNLOADING') {
                                    setUnloading(1, $('select[id="stockpileId"]').val(), resultData[1]);
                                    setUnloadingDetail(resultData[1]);
                                } else if (resultData[0] == 'FREIGHT') {
                                    setFreight(1, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), resultData[1]);
                                    setFreightDetail(resultData[1]);
                                } else if (resultData[0] == 'SALES') {
                                    setSales(1, $('select[id="customerId"]').val(),$('select[id="stockpileId"]').val(), resultData[1]);
                                    resetShipment(' ');
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'SHIPMENT') {
                                    setShipment(1, $('select[id="salesId"]').val(), resultData[1]);
                                    setShipmentDetail(resultData[1]);
                                } else if (resultData[0] == 'VENDOR') {
                                    setVendor(1, $('select[id="stockpileId"]').val(), resultData[1]);
                                    resetFreight(' ');
                                    //resetContract(' ');
                                    setFreight(1, $('select[id="stockpileId"]').val(), resultData[1], 0);
                                    setContract(1, $('select[id="stockpileId"]').val(), resultData[1], 0);
                                } else if (resultData[0] == 'CUSTOMER') {
                                    setCustomer(1, resultData[1]);
                                    setShipment(0, resultData[1], 0);
                                } else if (resultData[0] == 'LABOR') {
                                    setLabor(1, resultData[1]);
                                } else if (resultData[0] == 'SUPPLIER') {
                                    setSupplier(1, resultData[1]);
                                }
                                
                                $('#insertModal').modal('hide');
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
    
    function resetVehicle(text) {
        document.getElementById('unloadingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('unloadingCostId').options.add(x);
        
        $("#unloadingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select  --"
        });
    }
    
    function setUnloading(type, stockpileId, unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUnloadingCost',
                    stockpileId: stockpileId
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
                        document.getElementById('unloadingCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('unloadingCostId').options.add(x);
                        
                        $("#unloadingCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('unloadingCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('unloadingCostId').options.add(x);
                    }

                    <?php
                    if($allowUnloading) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('unloadingCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#unloadingCostId').find('option').each(function(i,e){
                            if($(e).val() == unloadingCostId){
                                $('#unloadingCostId').prop('selectedIndex',i);
                                
                                $("#unloadingCostId").select2({
                                    width: "100%",
                                    placeholder: unloadingCostId
                                });
                            }
                        });
						
						setUnloadingDetail($('select[id="unloadingCostId"]').val());
                    }
                }
            }
        });
    }
    
    /*function resetVendor() {
        document.getElementById('vendorId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Tiket Timbangan--';
        document.getElementById('vendorId').options.add(x);
        
        $("#vendorId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }*/
    
    /*function setVendor(type, idSuratTugas, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendor',
                    tiketId: idSuratTugas,
                    newVendorId: vendorId
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
                    if(returnValLength > 1) {
                        document.getElementById('vendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorId').options.add(x);
                        
						for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorId').options.add(x);
						} 
						$("#vendorId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
						
                    }else{
						document.getElementById('vendorId').options.length = 0;
						for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorId').options.add(x);
						}
						$("#vendorId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
					}
						
                    

                    <?php
                    if($allowVendor) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                       // x.text = '-- Insert New --';
                        document.getElementById('vendorId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#vendorId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#vendorId').prop('selectedIndex',i);
                                
                                $("#vendorId").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }*/

    function refreshsetVendorGGL_SRB() {
        document.getElementById('rsb').value = '';
		document.getElementById('ggl').value = '';	
        document.getElementById('rg').value = '';
		document.getElementById('un').value = '';	
        $('#ggl_rsb').hide();
        document.getElementById('ggl_rsb').innerHTML = '';	
    }

    function setVendorGGL_SRB(vendor_id) {
       
        //if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getVendorGGL_SRB',
                        vendor_id: vendor_id
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('rsb').value = returnVal[1];
						document.getElementById('ggl').value = returnVal[2];
                        document.getElementById('rg').value = returnVal[3];
						document.getElementById('un').value = returnVal[4];
                        $('#ggl_rsb').show();
                        document.getElementById('ggl_rsb').innerHTML = returnVal[5];

                        setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], returnVal[3], returnVal[4], 0);
                    }
                }
            });
        //}
	}

    function resetSpBlock() {
        document.getElementById('block').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select Stockpile --';
        document.getElementById('block').options.add(x);
        
        $("#block").select2({
            width: "100%",
            placeholder: "-- Please Select Stockpile --"
        });
    }

    function setSpBlock(type, stockpileId, ggl, rsb, rg, un, block) {
      //  alert(ggl,rsb);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSpBlockData',
                    stockpileId: stockpileId,
                    ggl: ggl,
                    rsb: rsb,
                    rg: rg,
                    un: un,
                    newblock: block
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
                        document.getElementById('block').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('block').options.add(x);
                        
                        $("#block").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('block').options.add(x);
                    }

                    <?php
                    if($allowVendor) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('block').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#block').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#block').prop('selectedIndex',i);
                                
                                $("#block").select2({
                                    width: "100%",
                                    placeholder: block
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function getAvailableQtyAll(stockpileId,transactionDate2) {
        $.ajax({
           url: 'get_data.php',
           method: 'POST',
           data: { action: 'getAvailableQtyAll',
                stockpileId: stockpileId,
				transactionDate2: transactionDate2
            },
           success: function(data){
               var returnVal = data.split('|');
               if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('availabel_rsb').innerHTML = returnVal[1];
                   document.getElementById('availabel_ggl').innerHTML = returnVal[2];
                   document.getElementById('availabel_rsb_ggl').innerHTML = returnVal[3];
                   document.getElementById('availabel_uncertified').innerHTML = returnVal[4];
                    //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
               }
           }
       });
    }


    function getValidationRSB(stockpileId, qty_rsb) {
            //alert(idPP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getValidationRSB',
                qty_rsb: qty_rsb,
                stockpileId: stockpileId
                },
                success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                   
                    document.getElementById('qtyRSB_error').innerHTML = returnVal[2];
                    document.getElementById('qtyRSB_error1').value = returnVal[1];

                }
            }
        });
    }


    function getValidationGGL(stockpileId, qty_ggl) {
            //alert(idPP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getValidationGGL',
                qty_ggl: qty_ggl,
                stockpileId: stockpileId
                },
                success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                   
                    document.getElementById('qtyGGL_error').innerHTML = returnVal[2];
                    document.getElementById('qtyGGL_error1').value = returnVal[1];

                }
            }
        });
    }

    function getValidationRG(stockpileId, qty_RG) {
            //alert(idPP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getValidationRG',
                qty_RG: qty_RG,
                stockpileId: stockpileId
                },
                success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                   
                    document.getElementById('qtyRG_error').innerHTML = returnVal[2];
                    document.getElementById('qtyRG_error1').value = returnVal[1];

                }
            }
        });
    }

    function getValidationUN(stockpileId, sendWeight2, transactionDate) {
            //alert(idPP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getValidationUN',
                qty_UN: sendWeight2,
                stockpileId: stockpileId,
				transactionDate: transactionDate
                },
                success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                   
                    document.getElementById('qtyUN_error').innerHTML = returnVal[2];
                    document.getElementById('qtyUN_error1').value = returnVal[1];
                }
            }
        });
    }

    function setShipmentGGL_SRB(salesId) {
       
       //if(amount != '') {
           $.ajax({
               url: 'get_data.php',
               method: 'POST',
               data: { action: 'getShipmentGGL_SRB',
                    salesId: salesId
               },
               success: function(data){
                   var returnVal = data.split('|');
                   if(parseInt(returnVal[0])!=0)	//if no errors
                   {
                       document.getElementById('rsb').value = returnVal[1];
                       document.getElementById('ggl').value = returnVal[2];
                       document.getElementById('rg').value = returnVal[4];
                       document.getElementById('un').value = returnVal[5];
                       $('#ggl_rsb_shipment').show();
                       document.getElementById('ggl_rsb_shipment').innerHTML = returnVal[3];
                       document.getElementById('rsb1').value = returnVal[1];
                       document.getElementById('ggl1').value = returnVal[2];
                       document.getElementById('rsb_ggl').value = returnVal[4];
                       document.getElementById('uncertified').value = returnVal[5];

                       if(document.getElementById('rsb').value == 1){
                            $("#div_qty_rsb").show();
                            $("#qty_rsb").keyup( 
                                function(){
                                    var a = parseFloat($("#qty_rsb").val());
                                    $("#sendWeight2").val(a);  
                                }
                            );

                       }else if(document.getElementById('ggl').value == 1){
                            $("#div_qty_ggl").show();
                            $("#qty_ggl").keyup( 
                                function(){
                                    var b = parseFloat($("#qty_ggl").val());
                                    $("#sendWeight2").val(b);  
                                }
                            );
                       }
                       
                       else if(document.getElementById('rsb_ggl').value == 1){
                            $("#div_RG").show();
                            $("#qty_RG").keyup( 
                                function(){
                                    var c = parseFloat($("#qty_RG").val());
                                    $("#sendWeight2").val(c);  
                                }
                            );
                       }
                       else if(document.getElementById('uncertified').value == 1){
                            $("#div_UN").show();
                            $("#qty_uncertified").keyup( 
                                function(){
                                    var c = parseFloat($("#qty_uncertified").val());
                                    $("#sendWeight2").val(c);  
                                }
                            );
                       }

                    //    setSpBlock(0, $('select[id="stockpileId"]').val(), returnVal[2], returnVal[1], 0);
                   }
               }
           });
       //}
   }

    
    function addCommas(nStr)
    {
        nStr = nStr.replace(',', '');
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
    }
    
    function resetFreight(text) {
        document.getElementById('freightCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightCostId').options.add(x);
        
        $("#freightCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setFreight(type, stockpileId, vendorId, freightCostId, trxDate, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightCost',
                    stockpileId: stockpileId,
                    vendorId: vendorId,
					trxDate: trxDate,
					stockpileContractId: stockpileContractId
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
                        document.getElementById('freightCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightCostId').options.add(x);
                        
                        $("#freightCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('freightCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightCostId').options.add(x);
                    }

                    <?php
                    if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#freightCostId').find('option').each(function(i,e){
                            if($(e).val() == freightCostId){
                                $('#freightCostId').prop('selectedIndex',i);
                                
                                $("#freightCostId").select2({
                                    width: "100%",
                                    placeholder: freightCostId
                                });
                            }
                        });
						setFreightDetail($('select[id="freightCostId"]').val());
                    }
                }
            }
        });
    }
    
    function resetFreightDetail() {
        $('#labelFreightCost').hide();
        document.getElementById('labelFreightCost').innerHTML = '';
    }
	
	function resetHandling(text) {
        document.getElementById('handlingCostId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('handlingCostId').options.add(x);
        
        $("#handlingCostId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setHandling(type, stockpileId, vendorId, handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingCost',
                    stockpileId: stockpileId,
                    vendorId: vendorId
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
                        document.getElementById('handlingCostId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('handlingCostId').options.add(x);
                        
                        $("#handlingCostId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('handlingCostId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('handlingCostId').options.add(x);
                    }

                    <?php
                   // if($allowFreight) {
                    ?>
//                    if(returnValLength > 0) {
                     //   var x = document.createElement('option');
                     //   x.value = 'INSERT';
                     //   x.text = '-- Insert New --';
                     //   document.getElementById('freightCostId').options.add(x);
//                    }                  
                    <?php
                  //  }
                    ?>
                                        
                    if(type == 1) {
                        $('#handlingCostId').find('option').each(function(i,e){
                            if($(e).val() == handlingCostId){
                                $('#handlingCostId').prop('selectedIndex',i);
                                
                                $("#handlingCostId").select2({
                                    width: "100%",
                                    placeholder: handlingCostId
                                });
                            }
                        });
						setHandlingDetail($('select[id="handlingCostId"]').val());
                    }
                }
            }
        });
    }
    
    function resetHandlingDetail() {
        $('#labelHandlingCost').hide();
        document.getElementById('labelHandlingCost').innerHTML = '';
    }
    
    function resetUnloadingDetail() {
        $('#labelUnloadingCost').hide();
        document.getElementById('labelUnloadingCost').innerHTML = '';
    }
    
    function resetContract(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);
        
        $("#stockpileContractId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function setContract(type, stockpileId, vendorId, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getStockpileContract',
					//tiketId: idSuratTugas,
                    stockpileId: stockpileId,
                    vendorId: vendorId
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
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);
                        
						$("#stockpileContractId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
						
						for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }
                        
                    }else{
						document.getElementById('stockpileContractId').options.length = 0;
						for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }
                        $("#stockpileContractId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
					}

                    

                    <?php
                    if($allowContract) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New2 --';
                        document.getElementById('stockpileContractId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#stockpileContractId').find('option').each(function(i,e){
                            if($(e).val() == stockpileContractId){
                                $('#stockpileContractId').prop('selectedIndex',i);
                                
                                $("#stockpileContractId").select2({
                                    width: "100%",
                                    placeholder: stockpileContractId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
	
	function resetContractPksDetail(text) {
        document.getElementById('contractPksDetailId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('contractPksDetailId').options.add(x);

        $("#contractPksDetailId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }

    function setContractPksDetail(type, stockpileId, vendorId, stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getPoNoCurah',
                stockpileId: stockpileId,
                vendorId: vendorId,
                stockpileContractId: stockpileContractId,
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('contractPksDetailId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select PKS --';
                        document.getElementById('contractPksDetailId').options.add(x);

                        $("#contractPksDetailId").select2({
                            width: "100%",
                            placeholder: "-- Please Select PKS --"
                        });
                    }

                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('contractPksDetailId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('contractPksDetailId').options.add(x);
                    }


                    if (type == 1) {
                        $('#contractPksDetailId').find('option').each(function (i, e) {
                            if ($(e).val() == contractPksDetailId) {
                                $('#contractPksDetailId').prop('selectedIndex', i);

                                $("#contractPksDetailId").select2({
                                    width: "100%",
                                    placeholder: contractPksDetailId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
	
	function resetSuratTugas(text) {
        document.getElementById('idSuratTugas').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('idSuratTugas').options.add(x);
        
        $("#idSuratTugas").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    function setSuratTugas(type, stockpileId, idSuratTugas) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSuratTugas',
                    stockpileId: stockpileId
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
                        document.getElementById('idSuratTugas').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('idSuratTugas').options.add(x);
                        
                        $("#idSuratTugas").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('idSuratTugas').options.add(x);
                    }

                    <?php
                    //if($allowContract) {
                    ?>
//                    if(returnValLength > 0) {
                        //var x = document.createElement('option');
                       // x.value = 'INSERT';
                       // x.text = '-- Insert New --';
                        //document.getElementById('idSuratTugas').options.add(x);
//                    }                  
                    <?php
                   // }
                    ?>
                                        
                    if(type == 1) {
                        $('#idSuratTugas').find('option').each(function(i,e){
                            if($(e).val() == idSuratTugas){
                                $('#idSuratTugas').prop('selectedIndex',i);
                                
                                $("#idSuratTugas").select2({
                                    width: "100%",
                                    placeholder: idSuratTugas
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    function resetShipment(text) {
        document.getElementById('shipmentId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('shipmentId').options.add(x);
        
        $("#shipmentId").select2({
            width: "100%",
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function resetSales(text) {
        document.getElementById('salesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('salesId').options.add(x);
        
        $("#salesId").select2({
            placeholder: "-- Please Select" + text + "--"
        });
    }
    
    function resetContractDetail() {
        //$('#labelQuantityContract').hide();
        //document.getElementById('labelQuantityContract').innerHTML = '';
        //document.getElementById('contractNo').value = '';
        //document.getElementById('supplierId').value = '';
        //$('#supplierId').attr("disabled", true);
        document.getElementById('contractNo').value = '';
		document.getElementById('qtyBalance').value = '';
		
       /* $("#supplierId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });*/
    }
    
    function setContractDetail(stockpileContractId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getContractDetail',
                    stockpileContractId: stockpileContractId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');
                    
                    if(returnVal[0] == 'C') {
//                        $('#supplierId').attr("disabled", false);
                       // setSupplier(0, 0);
                    } else if(returnVal[0] == 'P') {
                        $('#labelQuantityContract').show();
                      //  document.getElementById('labelQuantityContract').innerHTML = 'Quantity balance is ' + returnVal[2] + ' KG';
//                        $('#supplierId').attr("disabled", true);
                    }
					document.getElementById('qtyBalance').value = returnVal[2];
                    document.getElementById('contractNo').value = returnVal[1];
					setContractPksDetail(0, $('select[id="stockpileId"]').val(), returnVal[3], $('select[id="stockpileContractId"]').val());
                }
            }
        });
    }
	
	function resetSuratTugasDetail() {
        //$('#labelQuantityContract').hide();
        //document.getElementById('labelQuantityContract').innerHTML = '';
		document.getElementById('stockpileContractId').value = '';
        //document.getElementById('poNo').value = '';
		//document.getElementById('contractNo').value = '';
		//document.getElementById('qtyBalance').value = '';
		document.getElementById('vehicleNo').value = '';
		document.getElementById('driver').value = '';
		document.getElementById('vendorName').value = '';
		document.getElementById('sendWeight').value = '';
		document.getElementById('brutoWeight').value = '';
		document.getElementById('tarraWeight').value = '';
		document.getElementById('nettoWeight').value = '';
        document.getElementById('freightCostId').value = '';
		document.getElementById('vendorId').value = '';
		document.getElementById('unloadingCostId').value = '';
		document.getElementById('loadingDate').value = '';
		document.getElementById('handlingCostId').value = '';
		document.getElementById('laborId').value = '';
		document.getElementById('permitNo').value = '';
		document.getElementById('persenPecahSlip').value = '';
//        $('#supplierId').attr("disabled", true);
        
      /*  $("#supplierId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });*/
    }
    
    function setSuratTugasDetail(idSuratTugas) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSuratTugasDetail',
                    idSuratTugas: idSuratTugas
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');
                    
                    /*if(returnVal[0] == 'C') {
//                        $('#supplierId').attr("disabled", false);
                        setSupplier(0, 0);
                    } else if(returnVal[0] == 'P') {
                        $('#labelQuantityContract').show();
                        document.getElementById('labelQuantityContract').innerHTML = 'Quantity balance is ' + returnVal[2] + ' KG';
//                        $('#supplierId').attr("disabled", true);
                    }*/

                    //document.getElementById('contractNo').value = returnVal[1];
					document.getElementById('stockpileContractId').value = returnVal[0];
					//document.getElementById('poNo').value = returnVal[1];
					//document.getElementById('contractNo').value = returnVal[2];
					document.getElementById('vehicleNo').value = returnVal[3];
					document.getElementById('driver').value = returnVal[4];
					document.getElementById('sendWeight').value = returnVal[5];
					document.getElementById('brutoWeight').value = returnVal[6];
					document.getElementById('tarraWeight').value = returnVal[7];
					//document.getElementById('qtyBalance').value = returnVal[8];
					document.getElementById('nettoWeight').value = returnVal[9];
					document.getElementById('vendorId').value = returnVal[10];
					document.getElementById('supplierId').value = returnVal[10];
					document.getElementById('vendorName').value = returnVal[11];
					document.getElementById('loadingDate').value = returnVal[12];
					document.getElementById('unloadingCostId').value = returnVal[13];
					document.getElementById('freightCostId').value = returnVal[14];
					document.getElementById('laborId').value = returnVal[15];
					document.getElementById('handlingCostId').value = returnVal[16];
					document.getElementById('permitNo').value = returnVal[17];
					document.getElementById('persenPecahSlip').value = returnVal[19];
					//document.getElementById('unloadingDate').value = returnVal[20];
				
				
				setUnloading(1, $('select[id="stockpileId"]').val(), returnVal[13]);
				
				setFreight(1, $('select[id="stockpileId"]').val(), returnVal[10], returnVal[14],document.getElementById('unloadingDate').value, returnVal[0]);
				
				setLabor(1, $('select[id="stockpileId"]').val(),returnVal[15]);
				setHandling(1, $('select[id="stockpileId"]').val(), returnVal[10], returnVal[16]);
				
                setContract(1, $('select[id="stockpileId"]').val(), returnVal[10], returnVal[0]);
				setContractDetail(returnVal[0]);
				setContractPksDetail(0, $('select[id="stockpileId"]').val(), returnVal[10], returnVal[0]);
                setVendorGGL_SRB(returnVal[10]);    
            }
            }
        });
    }
    
    function setFreightDetail(freightCostId) {
         $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightDetail',
                freightCostId: freightCostId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    result = data.split('||');
                    result.price = result[0];
                    result.tolerance = result[1];
                    result.claim = result[2];
                    result.persen = result[3];
                    result.taxCategory = result[4];
                    result.taxValue = result[5];
                    $('#labelFreightCost').show();
                    $('#labelShrinkTolerance').show();
                    $('#labelShrinkClaim').show();
                    document.getElementById('labelFreightCost').innerHTML = 'Freight cost/KG is ' +  result.price;
                    if(result.tolerance > 0){
                        document.getElementById('labelShrinkTolerance').innerHTML = 'Shrink Tolerance (KG) : ' +  result.tolerance;
                        document.getElementById('shrinkToleranceText').value = result.tolerance;
                    }else if(result.persen > 0){
                        document.getElementById('labelShrinkTolerance').innerHTML = 'Shrink Tolerance (%) : ' +  result.persen;
                        document.getElementById('shrinkTolerancePersenText').value = result.persen;
                    }
                    document.getElementById('labelShrinkClaim').innerHTML = 'Shrink Price Claim : ' +  result.claim;
                    document.getElementById('shrinkClaimText').value = result.claim;
                    document.getElementById('taxCategory').value = result.taxCategory;
                    document.getElementById('taxValue').value = result.taxValue;
					//end add new detail standard shrink - eva
				}
            }
        });
    }

    function resetFreightCostSalesDetail() {
        $('#labelFreightCostSales').hide();
        document.getElementById('labelFreightCostSales').innerHTML = '';
    }

    function setFreightCostSalesDetail(freightCostSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightCostSalesDetail',
                freightCostSalesId: freightCostSalesId
            },
            success: function (data) {
//                        alert(data);
                if (data != '') {
                    $('#labelFreightCostSales').show();
                    document.getElementById('labelFreightCostSales').innerHTML = 'Freight cost/KG is ' + data;
                }
            }
        });
    }
	
	function setHandlingDetail(handlingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingDetail',
                    handlingCostId: handlingCostId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    $('#labelHandlingCost').show();
                    document.getElementById('labelHandlingCost').innerHTML = 'Handling cost/KG is ' + data;
                }
            }
        });
    }
    
    function setUnloadingDetail(unloadingCostId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getUnloadingDetail',
                    unloadingCostId: unloadingCostId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    $('#labelUnloadingCost').show();
                    document.getElementById('labelUnloadingCost').innerHTML = 'Unloading cost is ' + data;
                }
            }
        });
    }
    
    <!-- Start Edit by Eva -->
	//start add new detail standard shrink - eva
    function getNettoWeight(brutoWeight, tarraWeight, sendWeight, shrinkToleranceText, shrinkClaimText, shrinkTolerancePersenText, taxCategory, taxValue) {
        if (brutoWeight.value != '' && tarraWeight.value != '') {
            netto = brutoWeight.value.replace(new RegExp(",", "g"), "") - tarraWeight.value.replace(new RegExp(",", "g"), "");
            document.getElementById('nettoWeight').value = netto;
            $('#standardShrink').show();
            $('#standardShrinkAmount').show();
            if(netto > 0 && shrinkToleranceText.value > 0){
                stdShrink = sendWeight.value.replace(new RegExp(",", "g"), "") - netto - shrinkToleranceText.value.replace(new RegExp(",", "g"), "");
            }else if(netto > 0 && shrinkTolerancePersenText.value > 0){
                stdShrink = sendWeight.value.replace(new RegExp(",", "g"), "") - netto - (sendWeight.value.replace(new RegExp(",", "g"), "")*(shrinkTolerancePersenText.value.replace(new RegExp(",", "g"), "") / 100));
            }else{
                stdShrink = 0;
            }

            if(stdShrink > 0){
			    document.getElementById('standardShrink').innerHTML = 'Shrink Qty Claim (KG) : '+ stdShrink;
            }else{
                document.getElementById('standardShrink').innerHTML = 'Shrink Qty Claim (KG) : 0';
            }

            if(taxCategory.value == 1){
                tax = (100 - taxValue.value)/100;
                shrinkAmount = (stdShrink  * shrinkClaimText.value.replace(new RegExp(",", "g"), "")) / tax; //kurang
            }else{
                shrinkAmount = stdShrink  * shrinkClaimText.value.replace(new RegExp(",", "g"), "");
            }

            if(shrinkAmount > 0){
				document.getElementById('standardShrinkAmount').innerHTML = 'Standard Shrink Amount: ' + shrinkAmount;
			}else{
				document.getElementById('standardShrinkAmount').innerHTML = 'Standard Shrink Amount: 0';
			}        } else {
            document.getElementById('nettoWeight').value = '';
        }
    }
	//end add new detail standard shrink - eva
	<!-- end Edit by Eva -->
	
	<!-- Start Add by Eva -->
	function getAmountClaim(qtyAddShrink, priceAddShrink) {
        if (qtyAddShrink.value != '' && priceAddShrink.value != '') {
            document.getElementById('newAmountClaim').value = qtyAddShrink.value.replace(new RegExp(",", "g"), "") * priceAddShrink.value.replace(new RegExp(",", "g"), "");
        } else {
            document.getElementById('newAamountClaim').value = '';
        }
    }
	<!-- End Add by Eva -->
    
    function resetCustomer() {
        document.getElementById('customerId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('customerId').options.add(x);
        
        $("#customerId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
    
    function setCustomer(type, customerId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getCustomer'
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
                        document.getElementById('customerId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('customerId').options.add(x);
                        
                        $("#customerId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('customerId').options.add(x);
                    }

                    <?php
                    if($allowCustomer) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('customerId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#customerId').find('option').each(function(i,e){
                            if($(e).val() == customerId){
                                $('#customerId').prop('selectedIndex',i);
                                
                                $("#customerId").select2({
                                    width: "100%",
                                    placeholder: customerId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
	 function resetLabor(text) {
        document.getElementById('laborId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('laborId').options.add(x);
        
        $("#laborId").select2({
            width: "100%",
            placeholder: "-- Please Select --"
        });
    }
	
    function setLabor(type, stockpileId, laborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLabor',
                    stockpileId: stockpileId
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
//                    if(returnValLength > 0) {
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                        
                        $("#laborId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
//                    }
                    
                    var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('laborId').options.add(x);

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }

                    <?php
                    if($allowUnloading) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('laborId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#laborId').find('option').each(function(i,e){
                            if($(e).val() == laborId){
                                $('#laborId').prop('selectedIndex',i);
                                
                                $("#laborId").select2({
                                    width: "100%",
                                    placeholder: laborId
                                });
                            }
                        });
                    }
                }
            }
        });
    }

    function resetFreightCostSales(text) {
        document.getElementById('freightCostSalesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightCostSalesId').options.add(x);

        $("#freightCostSalesId").select2({
            placeholder: "-- Please Select" + text + "--"
        });
    }
	
	function setFreightCostSales(type, customerId, stockpileId, salesId, freightCostSalesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: {
                action: 'getFreightCostSales',
				salesId: salesId,
                customerId: customerId,
                stockpileId: stockpileId
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength > 0) {
                        document.getElementById('freightCostSalesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightCostSalesId').options.add(x);

                        $("#freightCostSalesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }
					
					var x = document.createElement('option');
                    x.value = 'NONE';
                    x.text = 'NONE';
                    document.getElementById('freightCostSalesId').options.add(x);

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightCostSalesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                    var x = document.createElement('option');
                    x.value = 'INSERT';
                    x.text = '-- Insert New --';
                    document.getElementById('freightCostSalesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>

                    if (type == 1) {
                        $('#freightCostSalesId').find('option').each(function (i, e) {
                            if ($(e).val() == freightCostSalesId) {
                                $('#freightCostSalesId').prop('selectedIndex', i);

                                $("#freightCostSalesId").select2({
                                    width: "100%",
                                    placeholder: freightCostSalesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setSales(type, customerId,stockpileId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSales',
                customerId: customerId,
				stockpileId: stockpileId
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
                        document.getElementById('salesId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('salesId').options.add(x);
                        
                        $("#salesId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesId').options.add(x);
                    }

                    <?php
                    if($allowSales) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('salesId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#salesId').find('option').each(function(i,e){
                            if($(e).val() == salesId){
                                $('#salesId').prop('selectedIndex',i);
                                
                                $("#salesId").select2({
                                    width: "100%",
                                    placeholder: salesId
                                });
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setShipment(type, salesId, shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipment',
                salesId: salesId,
                shipmentId: shipmentId
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
                        document.getElementById('shipmentId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentId').options.add(x);
                        
                        $("#shipmentId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentId').options.add(x);
                    }
                    
                    if(type == 1) {
                        $('#shipmentId').find('option').each(function(i,e){
                            if($(e).val() == salesId){
                                $('#shipmentId').prop('selectedIndex',i);
                                
                                $("#shipmentId").select2({
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
    
    function resetShipmentDetail() {
//        document.getElementById('customerName').value = '';
        document.getElementById('quantityAvailable').value = '';
    }
    
    function setShipmentDetail(shipmentId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentDetail',
                    shipmentId: shipmentId
            },
            success: function(data){
//                        alert(data);
                if(data != '') {
                    var returnVal = data.split('||');

//                    document.getElementById('customerName').value = returnVal[0];
                    document.getElementById('quantityAvailable').value = returnVal[1];
                }
            }
        });
    }
    
    /*function setSupplier(type, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getSupplier',
                    newVendorId: vendorId
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
                        document.getElementById('supplierId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('supplierId').options.add(x);
                        
                        $("#supplierId").select2({
                            width: "100%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('supplierId').options.add(x);
                    }

                    <?php
                    if($allowSupplier) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('supplierId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#supplierId').find('option').each(function(i,e){
                            if($(e).val() == vendorId){
                                $('#supplierId').prop('selectedIndex',i);
                                
                                $("#supplierId").select2({
                                    width: "100%",
                                    placeholder: vendorId
                                });
                            }
                        });
                    }
                }
            }
        });
    }*/

    $('#showPhoto').click(function () {
        $('#snapPhotoModal').modal('show');
        // $('#snapPhotoModal').innerHTML = '<img id="base64image" src="'+sessionStorage.getItem("file")+'"/>';
        $('#snapPhotoModal').load('views/notim-new-showImage.php', {transactionId: $('#idSuratTugas').val(),type: 'pic'});

    });

    $('#showTruck').click(function () {
        $('#snapPhotoModal').modal('show');
        // $('#snapPhotoModal').innerHTML = '<img id="base64image" src="'+sessionStorage.getItem("file")+'"/>';
        $('#snapPhotoModal').load('views/notim-new-showImage.php', {transactionId: $('#idSuratTugas').val(),type: 'truck'});
    });
</script>

<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<?php
$sql = "SELECT t.*, 
DATE_FORMAT(t.transaction_date, '%d %b %Y') AS transaction_date2,
DATE_FORMAT(t.loading_date, '%d %b %Y') AS loading_date2,
DATE_FORMAT(t.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2,
DATE_FORMAT(t.unloading_date, '%d %b %Y') AS unloading_date2,
CASE WHEN t.transaction_type = 1 THEN 'IN' ELSE 'OUT' END AS transaction_type2, 
(SELECT user_name FROM `user` WHERE user_id = t.entry_by) AS user_name,
(SELECT c.contract_no FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id)) AS contract_no,
(SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id))) AS vendor_name,
CASE WHEN t.transaction_type = 1 
THEN (SELECT c.po_no FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id)) 
ELSE (SELECT shipment_code FROM shipment WHERE shipment_id = t.shipment_id) END AS po_no,
CASE WHEN t.transaction_type = 1 
THEN (SELECT stockpile_name FROM stockpile WHERE stockpile_id = (SELECT stockpile_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id)) 
ELSE (SELECT stockpile_name FROM stockpile WHERE stockpile_id = (SELECT stockpile_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))) END AS stockpile_name,
(SELECT vehicle_name FROM vehicle WHERE vehicle_id = (SELECT vehicle_id FROM unloading_cost WHERE unloading_cost_id = t.unloading_cost_id)) AS vehicle_name,
(SELECT freight_code FROM freight WHERE freight_id = (SELECT freight_id FROM freight_cost WHERE freight_cost_id = t.freight_cost_id)) AS freight_code, 
(SELECT price FROM freight_cost WHERE freight_cost_id = t.freight_cost_id) AS freight_cost, 
(SELECT price FROM unloading_cost WHERE unloading_cost_id = t.unloading_cost_id) AS unloading_cost,
(SELECT vendor_handling_code FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = t.handling_cost_id)) AS vendor_handling_code,
(SELECT price FROM vendor_handling_cost WHERE handling_cost_id = t.handling_cost_id) AS handling_price,
(SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id)) AS sales_no,
(SELECT customer_name FROM customer WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))) AS customer_name
 FROM `transaction` t 
 WHERE 1=1
 AND (
(SELECT stockpile_id FROM stockpile_contract WHERE stockpile_contract_id = t.stockpile_contract_id) IN (SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']})
 OR
(SELECT stockpile_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = t.shipment_id))IN (SELECT stockpile_id FROM user_stockpile WHERE user_id = {$_SESSION['userId']})
)
 AND t.company_id = {$_SESSION['companyId']}
 ORDER BY t.transaction_id DESC LIMIT 1";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
    
    // <editor-fold defaultstate="collapsed" desc="Last Transaction & Print Container">
?>

<h4>Last Transaction</h4>

<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <thead>
        <tr>
            <th>Type</th>
            <th>Slip No.</th>
            <th>Transaction Date</th>
            <th>Vehicle No./Vessel Name</th>
            <th>PO No./Shipment Code</th>
            <th>Weight (KG)</th>
            <!--<th>Total Price</th>-->
            <th>Entry By</th>
            <th>Entry Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $row->transaction_type2; ?></td>
            <td><?php echo $row->slip_no; ?></td>
            <td><?php echo $row->unloading_date2; ?></td>
            <td><?php echo $row->vehicle_no; ?></td>
            <td><?php echo $row->po_no; ?></td>
            <td><?php echo number_format($row->quantity, 0, ".", ","); ?></td>
            <!--<td><div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price), 0, ".", ","); ?></div></td>-->
            <td><?php echo $row->user_name; ?></td>
            <td><?php echo $row->entry_date2; ?></td>
            <td>
                <button class="btn btn-warning" id="showTransaction">Show</button>
                <button class="btn btn-danger" id="hideTransaction" style="display: none;">Hide</button>
                <button class="btn btn-info" id="printTransaction" style="display: none;">Print</button>
            </td>
        </tr>
    </tbody>
</table>

<div id="transactionContainer" style="display: none;">
    <?php
    if($row->transaction_type == 1) {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Stockpile</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_name; ?></td>
            <td width="24%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_type2; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Slip No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->slip_no; ?></td>
            <td width="24%"><b>PO No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->po_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Receive Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->unloading_date2; ?></td>
            <td width="24%"><b>Contract Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vendor_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vehicle No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_no; ?></td>
            <td width="24%"><b>Supplier</b></td>
            <td width="2%">:</td>
            <td width="24%"></td>
        </tr>
        <tr>
            <td width="24%"><b>Driver</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->driver; ?></td>
            <td width="24%"><b>Contract No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->contract_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Loading Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->loading_date2; ?></td>
            <td width="24%"><b>Delivery Notes No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->permit_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vehicle</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_name; ?></td>
            <td width="24%"><b>Sent Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"><b>Supplier Freight</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->freight_code; ?></td>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Bruto Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->bruto_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Tarra Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->tarra_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Netto Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->netto_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
		<tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Shrink</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->shrink, 0, ".", ","); ?> Kg</div></td>
        </tr>
    </table>
    <br/>
    <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Expense</th>
                <th>Quantity (KG)</th>
                <th>Price/KG</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Freight Cost</td>
                <td><?php echo number_format($row->quantity, 0, ".", ","); ?> Kg</td>
                <td><div style="text-align: right;"><?php echo number_format($row->freight_cost, 0, ".", ","); ?></div></td>
                <td><div style="text-align: right;"><?php echo number_format(($row->freight_price * $row->quantity), 0, ".", ","); ?></div></td>
            </tr>
            <tr>
                <td>Unloading Cost</td>
                <td></td>
                <td></td>
                <td><div style="text-align: right;"><?php echo number_format($row->unloading_price, 0, ".", ","); ?></div></td>
            </tr>
			<tr>
                <td>Handling Cost</td>
                <td><?php echo number_format($row->handling_quantity, 0, ".", ","); ?> Kg</td>
                <td><div style="text-align: right;"><?php echo number_format($row->handling_price, 0, ".", ","); ?></div></td>
                <td><div style="text-align: right;"><?php echo number_format(($row->handling_price * $row->handling_quantity), 0, ".", ","); ?></div></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price + ($row->handling_price * $row->handling_quantity)), 0, ".", ","); ?></div></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td><div style="text-align: right;"><?php echo number_format((($row->freight_price * $row->quantity) + $row->unloading_price), 0, ".", ","); ?></div></td>
            </tr>
        </tfoot>
    </table>
    <?php
    } else {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="24%"><b>Stockpile</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->stockpile_name; ?></td>
            <td width="24%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_type2; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Slip No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->slip_no; ?></td>
            <td width="24%"><b>Shipment Code</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->po_no; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Transaction Date</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->transaction_date2; ?></td>
            <td width="24%"><b>Buyer</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->customer_name; ?></td>
        </tr>
        <tr>
            <td width="24%"><b>Vessel Name</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->vehicle_no; ?></td>
            <td width="24%"><b>Sales Agreement No.</b></td>
            <td width="2%">:</td>
            <td width="24%"><?php echo $row->sales_no; ?></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>Stockpile Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->send_weight, 0, ".", ","); ?> Kg</div></td>
        </tr>
        <tr>
            <td width="24%"></td>
            <td width="2%"></td>
            <td width="24%"></td>
            <td width="24%"><b>B/L Weight</b></td>
            <td width="2%">:</td>
            <td width="24%"><div style="text-align: right;"><?php echo number_format($row->quantity, 0, ".", ","); ?> Kg</div></td>
        </tr>
    </table>
    <br>
    <?php
    }
    ?>
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td <?php if($row->notes == '') echo 'style="height: 40px;"'; ?>><?php echo $row->notes; ?></td>
            </tr>
        </tbody>
    </table>
    <!--<br/>-->
    <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Driver</th>
                <th>Scaler</th>
                <th>Acknowledge</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 33%; height: 40px;"></td>
                <td style="width: 33%; height: 40px;"></td>
                <td style="width: 33%; height: 40px;"></td>
            </tr>
        </tbody>
    </table>
</div>

<hr>

<?php
    // </editor-fold>
}
?>

<!--<h4>Transaction Form</h4>-->

<form method="post" id="transactionDataForm">
    <input type="hidden" name="action" id="action" value="transaction_data" />
    <input type="hidden" placeholder="DD/MM/YYYY" id="transactionDate" name="transactionDate" value="<?php echo $transactionDate; ?>" >
	<input type="hidden" readonly class="span12" tabindex="15" id="persenPecahSlip" name="persenPecahSlip">
    <input type="hidden" name="rsb" id="rsb" />
    <input type="hidden" name="ggl" id="ggl" />
    <input type="hidden" name="rg" id="rg" />
    <input type="hidden" name="un" id="un" />
    <div class="row-fluid" style="margin-bottom: 7px;">   
        
        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT '1' as id, 'IN' as info UNION
                    SELECT '2' as id, 'OUT' as info;", $transactionType, "", "transactionType", "id", "info", 
                "", 11, "select2combobox50");
            ?>
        </div>
		<div class="span2 lightblue">
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM user_stockpile us
                    INNER JOIN stockpile s
                        ON s.stockpile_id = us.stockpile_id
                    WHERE us.user_id = {$_SESSION['userId']}
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_full", 
                    "", 1, "select2combobox75");
            ?>
        </div>
    </div>
    
    <div id="inTransaction">
    
        <div class="row-fluid" style="margin-bottom: 7px;">   
             <div class="span2 lightblue">
                <label>Receive Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="unloadingDate" name="unloadingDate" value="<?php if(isset($_SESSION['transaction'])) { echo $_SESSION['transaction']['unloadingDate']; } else { echo $unloadingDate;} ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
            </div>
			<div class="span2 lightblue">
                <label>No Tiket Timbangan <span style="color: red;">*</span></label>
            </div>
            <div class="span3 lightblue">
                <?php
                createCombo("", "", "", "idSuratTugas", "transaction_id", "slip", 
                    "", 13, "select2combobox100", 1);
                ?>
				<input type="hidden" readonly class="span12" tabindex="15" id="slipUpload" name="slipUpload">
                <!--<span class="help-block" id="labelQuantityContract" style="display: none;"></span>-->
            </div>
            <div id="tt_pictures" style="display: none;">

            </div>
            <!-- <div class="span2 lightblue">
                <a id="showPhoto" class="btn btn-success">Show Photo</a>
            </div>

            <div class="span2 lightblue">
                <a id="showTruck" class="btn btn-success">Photo Truck</a>
            </div> -->
			
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Loading Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
			                <input type="text" placeholder="DD/MM/YYYY" tabindex="4" id="loadingDate" name="loadingDate" value="<?php if(isset($_SESSION['transaction'])) { echo $_SESSION['transaction']['loadingDate']; } ?>" data-date-format="dd/mm/yyyy" class="datepicker" >

              <!--  <input type="text" readonly class="span12" tabindex="15" id="loadingDate" name="loadingDate">-->
            </div>
            <div class="span2 lightblue">
                <label>Vendor Name <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
				<?php 
				//createCombo("", "", "", "vendorId", "vendor_id", "vendor_name", 
                //    "", 2, "select2combobox100", 2);
				?>
				<input type="hidden" readonly class="span12" tabindex="15" id="supplierId" name="supplierId">
				<input type="hidden" readonly class="span12" tabindex="15" id="vendorId" name="vendorId">
                <input type="text" readonly class="span12" tabindex="15" id="vendorName" name="vendorName">
                <span class="help-block" id="ggl_rsb" style="display: none; color: blue;"></span>
            </div>
        </div>
		 <div class="row-fluid" id="contractPks" style="margin-bottom: 7px">
            <div class="span2 lightblue">
                <label></label>
            </div>
            <div class="span4 lightblue">
                <label></label>
            </div>
            <div class="span2 lightblue">
                <label>Contract PKS <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "contractPksDetailId", "contract_pks_detail_id", "contract_pks",
                    "", 2, "select2combobox100", 3);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Vehicle <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "unloadingCostId", "unloading_cost_id", "vehicle_name", 
                    "", "", "select2combobox100",2);
                ?>
                <span class="help-block" id="labelUnloadingCost" style="display: none;"></span>
            </div>
			<div class="span2 lightblue">
                <label>PO No.</label>
            </div>
            <div class="span4 lightblue">
			<!--<input type="hidden" readonly class="span12" tabindex="15" id="stockpileContractId" name="stockpileContractId">
            <input type="text" readonly class="span12" tabindex="15" id="poNo" name="poNo">-->
			<?php
                createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no", "", 2, "select2combobox100", 2);
                ?>	
            </div>
            
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Supplier Freight <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "freightCostId", "freight_cost_id", "freight_full", 
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
			<div class="span2 lightblue">
                <label>Contract No.</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="15" id="contractNo" name="contractNo">
            </div>
            
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Unloading Org <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
				createCombo("", "", "", "laborId", "labor_id", "labor_name", 
                    "", 6, "select2combobox100", 2);
                //createCombo("SELECT * FROM labor WHERE active = 1 ORDER BY labor_name ASC", $_SESSION['transaction']['laborId'], "", "laborId", "labor_id", "labor_name", 
                //    "", 7, "select2combobox100", 2, "", $allowLabor);
                ?>
                <span class="help-block" id="labelFreightCost" style="display: none;"></span>
            </div>
			<div class="span2 lightblue">
                <label>Qty Balance</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="15" id="qtyBalance" name="qtyBalance">
            </div>
			
            
        </div>
        <div class="row-fluid">  
            <div class="span2 lightblue">
                <label>Handling Cost <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "handlingCostId", "handling_cost_id", "handling_full", 
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelHandlingCost" style="display: none;"></span>
            </div>
			<div class="span2 lightblue">
                <label>Vehicle No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12"  tabindex="16" id="vehicleNo" name="vehicleNo">
            </div>
			
            
        </div>
        <div class="row-fluid">  
          <div class="span2 lightblue">
            <label>Stockpile Block <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                        createCombo("", "", "", "block", "sp_block_id", "block_name", 
                        "", 2, "select2combobox100", 2);
                ?>
            </div>
			<div class="span2 lightblue">
			
                <label>Driver <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12"  tabindex="17" id="driver" name="driver" >
            </div>
            
        </div>
		<div class="row-fluid">  
         <div class="span2 lightblue">
            
            </div>
            <div class="span4 lightblue">
               
            </div>
            <div class="span2 lightblue">
                <label>Delivery Notes No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="18" id="permitNo" name="permitNo" >
                <!--<input type="hidden" class="span12" readonly tabindex="18" id="permitNo" name="permitNo" >-->
            </div>
        </div>
		
        <!-- Start Add by Eva -->
        <hr>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div> -->
            <div class="span2 lightblue">
                <label>Sent Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="29" id="sendWeight" name="sendWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['sendWeight'];
                       } ?>" onblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                    document.getElementById('tarraWeight'),
                                                    this,
                                                    document.getElementById('shrinkToleranceText'),
                                                    document.getElementById('shrinkClaimText'),
                                                    document.getElementById('shrinkTolerancePersenText'),
                                                    document.getElementById('taxCategory'),
                                                    document.getElementById('taxValue'));">
            </div>
			<div class="span4 lightblue">
                <span class="help-block" id="labelShrinkTolerance" style="display: none;"></span>
				
                <input type="hidden" class="span12" tabindex="32" id="shrinkToleranceText" name="shrinkToleranceText"
                       value="" nblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                        document.getElementById('tarraWeight'),
                                                        document.getElementById('sendWeight'),
                                                        this,
                                                        document.getElementById('shrinkClaimText'),
                                                        document.getElementById('shrinkTolerancePersenText'),
                                                        document.getElementById('taxCategory'),
                                                        document.getElementById('taxValue'));">
				
				<input type="hidden" class="span12" tabindex="32" id="shrinkTolerancePersenText" name="shrinkTolerancePersenText"
                       value="" onblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                        document.getElementById('tarraWeight'),
                                                        document.getElementById('sendWeight'),
                                                        document.getElementById('shrinkToleranceText'),
                                                        document.getElementById('shrinkClaimText'),
                                                        this,
                                                        document.getElementById('taxCategory'),
                                                        document.getElementById('taxValue'));">
				
                <input type="hidden" class="span12" tabindex="32" id="shrinkClaimText" name="shrinkClaimText"
                       value="" onblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                        document.getElementById('tarraWeight'),
                                                        document.getElementById('sendWeight'),
                                                        document.getElementById('shrinkToleranceText'),
                                                        this,
                                                        document.getElementById('shrinkTolerancePersenText'),
                                                        document.getElementById('taxCategory'),
                                                        document.getElementById('taxValue'));">
														
				<input type="hidden" class="span12" tabindex="32" id="taxCategory" name="taxCategory"
                       value="" onblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                        document.getElementById('tarraWeight'),
                                                        document.getElementById('sendWeight'),
                                                        document.getElementById('shrinkToleranceText'),
                                                        document.getElementById('shrinkClaimText'),
                                                        document.getElementById('shrinkTolerancePersenText'),
                                                        this,
                                                        document.getElementById('taxValue'));">

                <input type="hidden" class="span12" tabindex="32" id="taxValue" name="taxValue"
                       value="" onblur="getNettoWeight(document.getElementById('brutoWeight'),
                                                        document.getElementById('tarraWeight'),
                                                        document.getElementById('sendWeight'),
                                                        document.getElementById('shrinkToleranceText'),
                                                        document.getElementById('shrinkClaimText'),
                                                        document.getElementById('shrinkTolerancePersenText'),
                                                        document.getElementById('taxCategory'),
                                                        this);">
                       
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div> -->
            <div class="span2 lightblue">
                <label>Bruto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="30" id="brutoWeight" name="brutoWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['brutoWeight'];
                       } ?>" onblur="getNettoWeight(this, 
                                                    document.getElementById('tarraWeight'),
                                                    document.getElementById('sendWeight'),
                                                    document.getElementById('shrinkToleranceText'),
                                                    document.getElementById('shrinkClaimText'),
                                                    document.getElementById('shrinkTolerancePersenText'),
                                                    document.getElementById('taxCategory'),
                                                    document.getElementById('taxValue'));">
            </div>
			<div class="span4 lightblue">
                <span class="help-block" id="labelShrinkClaim" style="display: none;"></span>
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div> -->
            <div class="span2 lightblue">
                <label>Tarra Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="31" id="tarraWeight" name="tarraWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['tarraWeight'];
                       } ?>" onblur="getNettoWeight(document.getElementById('brutoWeight'), 
                                                    this, 
                                                    document.getElementById('sendWeight'),
                                                    document.getElementById('shrinkToleranceText'),
                                                    document.getElementById('shrinkClaimText'),
                                                    document.getElementById('shrinkTolerancePersenText'),
                                                    document.getElementById('taxCategory'),
                                                    document.getElementById('taxValue'));">
            </div>
			<div class="span4 lightblue">
                <span class="help-block" id="standardShrink" style="display: none;"></span>
            </div>
        </div>
        <hr>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue">
            </div>
            <div class="span4 lightblue">
            </div> -->
            <div class="span2 lightblue">
                <label>Netto Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="32" id="nettoWeight" name="nettoWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['nettoWeight'];
                       } ?>">
            </div>
			<div class="span4 lightblue">
                <span class="help-block" id="standardShrinkAmount" style="display: none;"></span>
            </div>
        </div>
		<!-- <div class="row-fluid">
            <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div>
            <div class="span2 lightblue">
                <label>Standard Shrink (KG) <span style="color: red;"></span></label>
            </div>
            <div class="span4 lightblue">
                 <input type="text" readonly class="span12" tabindex="32" id="standardShrink" name="standardShrink"
                       value="">
            </div>
        </div> -->
		<!-- End Add by Eva -->
		<!-- Start Add by Eva -->
		<hr>
		<div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span5 lightblue">
                <label><span style="color: blue;">** </span>Additional Shrink (Additional Value from Standard Shrinkage)<span style="color: red;"></span></label>
            </div>
        </div>
		<div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Quantity <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="30" id="qtyAddShrink" name="qtyAddShrink" value="<?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['qtyAddShrink'];
                    } ?>" onblur="getAmountClaim(this, document.getElementById('priceAddShrink'));">
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Price <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="31" id="priceAddShrink" name="priceAddShrink" value="<?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['priceAddShrink'];
                    } ?>" onblur="getAmountClaim(document.getElementById('qtyAddShrink'), this);">
            </div>
        </div>
        <div class="row-fluid">
            <!-- <div class="span2 lightblue"></div>
            <div class="span4 lightblue"></div> -->
            <div class="span2 lightblue">
                <label>Amount Claim (Additional Shrink) <span style="color: blue;">**</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="32" id="newAmountClaim" name="newAmountClaim" value="<?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['newAmountClaim'];
                    } ?>">
            </div>
        </div>
		<br>
		<!-- End Add by Eva -->
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes" name="notes"><?php if(isset($_SESSION['transaction'])) { echo $_SESSION['transaction']['notes']; } ?></textarea>
            </div>
        </div>
    
    </div>
    
    <div id="outTransaction" style="display: none;">
    <input type="hidden" name="_method" value="INSERT_PREVIEW">
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Transaction Date <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" placeholder="DD/MM/YYYY" tabindex="2" id="transactionDate2" name="transactionDate2"
                       value="<?php echo $transactionDate2; ?>" data-date-format="dd/mm/yyyy" class="datepicker">
            </div>
            <div class="span2 lightblue">
                <label>Buyer</label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("SELECT cust.customer_id, cust.customer_name
                            FROM customer cust ORDER BY cust.customer_name ASC", $customerId, "", "customerId", "customer_id", "customer_name",
                    "", 11, "select2combobox100", 1, "", $allowCustomer);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Vessel Name/Vehicle No <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="3" id="vehicleNo2" name="vehicleNo2"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['vehicleNo2'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Sales Agreement No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "salesId", "sales_id", "sales_no",
                    "", 12, "select2combobox100", 1, "", $allowSales);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Stockpile Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="4" id="sendWeight2" name="sendWeight2"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['sendWeight2'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Shipment Code <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "shipmentId", "shipment_id", "shipment_no",
                    "", 13, "select2combobox100", 1, "", $allowShipment);
                ?>
                <span class="help-block" id="ggl_rsb_shipment" style="display: none; color: blue;"></span>

            </div>
        </div>
                <!-- RSB -->
                <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Available Qty<span style="color: red;">*</span></label>
            </div>
            <div class="span2 lightblue">
                <label style="color: blue;"><b>RSB : <span id="availabel_rsb"></span></b></label>
            </div>
            <div class="span2 lightblue">
                <label  style="color: blue;"><b>GGL : <span id="availabel_ggl" ></span></b></label>
            </div>

            <div class="span2 lightblue">
                <label>RSB <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                    createCombo("SELECT 0 as id, 'NONE' as info UNION
                                SELECT 1 as id, 'YES' as info;", $rsb1, '', "rsb1", "id", "info", 
                        "", 6);
                ?>
            </div>
            <div  class="span2 lightblue" id="div_qty_rsb" style="display: none;">
                <input type="text" class="span12" tabindex="5" id="qty_rsb" name="qty_rsb" value="">
                <label  style="color: red;" id="qtyRSB_error"></label>
                <input type="hidden" id="qtyRSB_error1" name="qtyRSB_error1" >

            </div>
        </div>
        
        <!-- GGL -->
        <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span2 lightblue">
                <label  style="color: blue;"><b>Uncertified : <span id="availabel_uncertified" ></span></b></label>
            </div>
            <div class="span2 lightblue">
                <label  style="color: blue;"><b>RSB-GGL : <span id="availabel_rsb_ggl" ></span></b></label>
            </div>
            <div class="span2 lightblue">
                <label>GGL <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $ggl1, '', "ggl1", "id", "info", 
                    "", 6);
                ?>

            </div>
            <div  class="span2 lightblue" id="div_qty_ggl" style="display: none;">
                <input type="text" class="span12" tabindex="5" id="qty_ggl" name="qty_ggl" value="">
                <label  style="color: red;" id="qtyGGL_error"></label>
                <input type="hidden" id="qtyGGL_error1" name="qtyGGL_error1" >
            </div>
        </div>
       
        <!-- RSB-GGL -->
        <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>RSB & GGL <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $rsb_ggl, '', "rsb_ggl", "id", "info", 
                    "", 6);
                ?>
            </div>
            <div  class="span2 lightblue" id="div_RG" style="display: none;">
                <input type="text" class="span12" tabindex="5" id="qty_RG" name="qty_RG" value="">
                <label  style="color: red;" id="qtyRG_error"></label>
                <input type="hidden" id="qtyRG_error1" name="qtyRG_error1" >
            </div>
        </div>

        <!-- UNCERTIFIED -->
        <div class="row-fluid">
            <div class="span2 lightblue">
               
            </div>
            <div class="span4 lightblue">
            </div>
            <div class="span2 lightblue">
                <label>Uncertified <span style="color: red;"></span></label>
            </div>
            <div class="span2 lightblue">
                <?php
                createCombo("SELECT '0' as id, 'NONE' as info UNION
                             SELECT '1' as id, 'YES' as info;", $uncertified, '', "uncertified", "id", "info", 
                    "", 6);
                ?>
            </div>
            <div  class="span2 lightblue" id="div_UN" style="display: none;">
                <input type="text" class="span12" tabindex="5" id="qty_uncertified" name="qty_uncertified" value="">
                <label  style="color: red;" id="qtyUN_error"></label>
                <input type="hidden" id="qtyUN_error1" name="qtyUN_error1" >
            </div>
        </div>

        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>B/L Weight (KG) <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="5" id="blWeight" name="blWeight"
                       value="<?php if (isset($_SESSION['transaction'])) {
                           echo $_SESSION['transaction']['blWeight'];
                       } ?>">
            </div>
            <div class="span2 lightblue">
                <label>Quantity Agreed (KG)</label>
            </div>
            <div class="span4 lightblue">
                <input type="text" readonly class="span12" tabindex="14" id="quantityAvailable"
                       name="quantityAvailable">
                <input type="hidden" readonly class="span12" tabindex="14" id="idSuratTugas2" name="idSuratTugas2">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Sales Freight <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <?php
                createCombo("", "", "", "freightCostSalesId", "freight_cost_id", "freight_full",
                    "", 6, "select2combobox100", 2);
                ?>
                <span class="help-block" id="labelFreightCostSales" style="display: none;"></span>
            </div>
            <div class="span2 lightblue">
                <label>DO No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="doSales" name="doSales"
                       value="">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2 lightblue">
                <label>Driver <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="driverSales" name="driverSales"
                       value="">
            </div>
            <div class="span2 lightblue">
                <label>Driving License No. <span style="color: red;">*</span></label>
            </div>
            <div class="span4 lightblue">
                <input type="text" class="span12" tabindex="16" id="sim" name="sim"
                       value="">
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12 lightblue">
                <label>Notes</label>
                <textarea class="span12" rows="3" tabindex="40" id="notes2"
                          name="notes2"><?php if (isset($_SESSION['transaction'])) {
                        echo $_SESSION['transaction']['notes2'];
                    } ?></textarea>
            </div>
        </div>
    </div>
    
    
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton">Submit</button>
        </div>
    </div>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" >
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


<div id="snapPhotoModal" class="modal hide fade" role="dialog" aria-labelledby="snapPhotoModalLabel" aria-hidden="true"
     style="width:1000px; height:600px; margin-left:-500px;">
</div>
