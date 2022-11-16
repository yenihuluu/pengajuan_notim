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

// <editor-fold defaultstate="collapsed" desc="Variable for Vehicle Data">

$fixedAssetId = '';
$dateofAcquisition='';
$nopol='';
$assetName = '';
$merk='';
$type='';
$rangka='';
$mesin='';
$unit='';
$acquisitionCost='';
$usageYear='';
$usageMonth='';
$depresitionType='';
$stockpileId='';
$masterAssettypeId='';

// </editor-fold>

// If ID is in the parameter
if(isset($_POST['fixedAssetId']) && $_POST['fixedAssetId'] != '') {
    
    $fixedAssetId = $_POST['fixedAssetId'];
    
    $readonlyProperty = ' readonly ';
    
    // <editor-fold defaultstate="collapsed" desc="Query for Vehicle Data">
    
    $sql = "select s.stockpile_name,ma.assettype,fa.fixed_asset_id,DATE_FORMAT(fa.DateofAcquisition,'%d/%m/%Y') as DateofAcquisition,
			fa.NoPol,fa.AssetName,fa.Merk,fa.Type,fa.Rangka,fa.Mesin,fa.Unit,fa.AcquisitionCost,fa.UsageYear,fa.UsageMonth,
			fa.DepresitionType,fa.stockpile_id,fa.master_assettype_id
			from fixed_asset fa
			left join `master_assettype` ma on ma.master_assettype_id = fa.master_assettype_id
			left join stockpile s on s.stockpile_id = fa.stockpile_id
			where fa.fixed_asset_id= {$fixedAssetId}";
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    if($resultData !== false && $resultData->num_rows > 0) {
        $rowData = $resultData->fetch_object();
		$fixedAssetId = $rowData->fixed_asset_id;
		$dateofAcquisition= $rowData->DateofAcquisition;
		$nopol= $rowData->NoPol;
		$assetName = $rowData->AssetName;
		$merk= $rowData->Merk;
		$type= $rowData->Type;
		$rangka= $rowData->Rangka;
		$mesin= $rowData->Mesin;
		$unit= $rowData->Unit;
		$acquisitionCost= $rowData->AcquisitionCost;
		$usageYear= $rowData->UsageYear;
		$usageMonth= $rowData->UsageMonth;
		$depresitionType= $rowData->DepresitionType;
		$stockpileId= $rowData->stockpile_id;
		$masterAssettypeId= $rowData->master_assettype_id;
		
    }
    
    // </editor-fold>
    
}

// <editor-fold defaultstate="collapsed" desc="Functions">

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "'>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Select if Applicable --</option>";
    }
    
    while ($combo_row = $result->fetch_object()) {
        if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
            $prop = "selected";
        else
            $prop = "";
        
        echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
    }
    
    if($empty == 2) {
        echo "<option value='OTHER'>Others</option>";
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
		
        jQuery.validator.addMethod("indonesianDate", function(value, element) { 
            //return Date.parseExact(value, "d/M/yyyy");
            return value.match(/^\d\d?\-\d\d?\-\d\d\d\d$/);
        });
        
        $("#fixedAssetDataForm").validate({
            rules: {                
				fixedAssetId: "required",
				dateofAcquisition: "required",
				assetName: "required",
				unit: "required",
				acquisitionCost: "required",
				usageYear: "required",
				usageMonth: "required",
				depresitionType: "required",
				stockpileId: "required",
				masterAssettypeId: "required"
            },
            messages: {
				dateofAcquisition: "Item name is a required field.",
				assetName: "Item name is a required field.",
				unit: "Item name is a required field.",
				acquisitionCost: "Item name is a required field.",
				usageYear: "Item name is a required field.",
				usageMonth: "Item name is a required field.",
				depresitionType: "Item name is a required field.",
				stockpileId: "Item name is a required field.",
				masterAssettypeId: "Item name is a required field."
            },
            submitHandler: function(form) {
                
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#fixedAssetDataForm").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('fixedAssetId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/fixed_asset.php', { fixedAssetId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
                        }
                    }
                });
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
            startView: 2
        });
    });
</script>

<form method="post" id="fixedAssetDataForm">
    <input type="hidden" name="action" id="action" value="fixedAsset_data" />
    <input type="hidden" name="fixedAssetId" id="fixedAssetId" value="<?php echo $fixedAssetId; ?>" />
    <div class="row-fluid">
		<div class="span3 lightblue">
        <label>Stockpile<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT stockpile_id, stockpile_name FROM stockpile", $stockpileId, "", "stockpileId", "stockpile_id", "stockpile_name", "", "", "select2combobox100", 1);
            ?>
      </div>  
		 <div class="span3 lightblue">
        <label>Invoice Date</label>
      	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="dateofAcquisition" name="dateofAcquisition" value="<?php echo $dateofAcquisition; ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        
        </div>
    </div>
	<div class="row-fluid">
		<div class="span3 lightblue">
        <label>Asset Type<span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT master_assettype_id, assettype FROM master_assettype", $masterAssettypeId, "", "masterAssettypeId", "master_assettype_id", "assettype", "", "", "select2combobox100", 1);
            ?>
      </div>
        <div class="span4 lightblue">
            <label>Asset Name <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="assetName" name="assetName" value="<?php echo $assetName; ?>">
        </div>        
    </div>
	 <div class="row-fluid">  
         <div class="span4 lightblue">
            <label>No Pol</label>
            <input type="text" class="span12" tabindex="1" id="nopol" name="nopol" value="<?php echo $nopol; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Merk</label>
            <input type="text" class="span12" tabindex="1" id="merk" name="merk" value="<?php echo $merk; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Type</label>
            <input type="text" class="span12" tabindex="1" id="type" name="type" value="<?php echo $type; ?>">
        </div>
		<div class="row-fluid">
		<div class="span4 lightblue">
            <label>Rangka</label>
            <input type="text" class="span12" tabindex="1" id="rangka" name="rangka" value="<?php echo $rangka; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Mesin</label>
            <input type="text" class="span12" tabindex="1" id="mesin" name="mesin" value="<?php echo $mesin; ?>">
        </div>
		</div>
    </div>
	<div class="row-fluid">  
         <div class="span4 lightblue">
            <label>Unit <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="unit" name="unit" value="<?php echo $unit; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Cost <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="acquisitionCost" name="acquisitionCost" value="<?php echo $acquisitionCost; ?>">
        </div>
	</div>
	<div class="row-fluid">  
		<div class="span4 lightblue">
            <label>Usage Year <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="usageYear" name="usageYear" value="<?php echo $usageYear; ?>">
        </div>
		<div class="span4 lightblue">
            <label>Usage Month <span style="color: red;">*</span></label>
            <input type="text" class="span12" tabindex="1" id="usageMonth" name="usageMonth" value="<?php echo $usageMonth; ?>">
        </div> 
	</div> 
    
	<div class="row-fluid">  
        <div class="span4 lightblue">
            <label>Depresition Type<span style="color: red;">*</span></label> <?php
            createCombo("SELECT 'DD' as depresitionId, 'Double Decline' as depresitionType UNION
                    SELECT 'SL' as depresitionId, 'Straight Line' as depresitionType;", $depresitionType, "", "depresitionId", "depresitionId", "depresitionType", "", "", "select2combobox100", 1);
            ?>
        </div>
    </div>
	<div class="row-fluid">
       <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?>>Submit</button>
            <button class="btn" type="button" onclick="back()">Back</button>
        </div>
    </div>
	
   
</form>
