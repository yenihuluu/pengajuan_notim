<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$periodFrom = '';
$periodTo = '';



if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    
} 

$sql = "CALL SP_CashFlow_ShipmentCost(STR_TO_DATE('{$periodFrom}','%d/%m/%Y'),STR_TO_DATE('{$periodTo}','%d/%m/%Y'))";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>
<script type="text/javascript">
    $(document).ready(function(){
		
	$('#contentTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');

			if (menu[0] == 'detail') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/shipment-cost-detail.php', 
					{	idcateg: menu[2], 
						stockpile: menu[3],
						periodFrom: menu[4],
						periodTo: menu[5]
					},
						iAmACallbackFunction2);	//and hide the rotating gif
                
            }
			if (menu[0] == 'detailTotal') {
                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#addDetailModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#addDetailModalForm').load('forms/shipment-cost-total-detail.php', 
					{	idcateg: menu[2], 
						stockpile: menu[3],
						periodFrom: menu[4],
						periodTo: menu[5]
					},
						iAmACallbackFunction2);	//and hide the rotating gif
                
            }
			
		});
	});
	function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
</script>		

<table id="contentTable" class="table table-bordered table-striped" style="font-size: 8pt;">
    <thead>
        <tr>
            <th style="text-align:center">Category Name</th>
            <th style="text-align:center">Padang</th>
            <th style="text-align:center">Jambi</th>
            <th style="text-align:center">Maredan</th>
            <th style="text-align:center">Bengkulu</th>
            <th style="text-align:center">Rengat</th>
            <th style="text-align:center">Sampit</th>
            <th style="text-align:center">Dumai</th>
            <th style="text-align:center">Tayan</th>
            <th style="text-align:center">Tanjung Buton</th>
            <th style="text-align:center">Total Amount</th>
            
        </tr>
    </thead>
    <tbody>
        <?php
        if($result === false) {
            echo 'wrong query';
        } else {
			
            while($row = $result->fetch_object()) {
                
                ?>
        <tr>
           
				<td style="font-weight:bold"><?php echo $row->Ctgname; ?></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Padang|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Padang, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Jambi|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Jambi, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Maredan|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Maredan, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Bengkulu|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Bengkulu, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Rengat|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Rengat, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Sampit|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Sampit, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Dumai|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Dumai, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Tayan|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Tayan, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detail|shipmentCost|<?php echo $row->idcateg; ?>|Tanjung Buton|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->Buton, 2, ".", ","); ?></a></td>
				<td style="text-align:right"><a href="#" id="detailTotal|shipmentCost|<?php echo $row->idcateg; ?>|Total Amount|<?php echo $periodFrom;?>|<?php echo $periodTo;?>" role="button"><?php echo number_format($row->TotAmount, 2, ".", ","); ?></a></td>
				
        </tr>
                <?php
                
            }
        }
        ?>
    </tbody>
</table>
<div id="addDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="DetailModalLabel" aria-hidden="true" style="width:1100px; height:500px; margin-left:-550px;">
        <form id="DetailForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>
                <h3 id="addDetailModalLabel">Shipment Cost Detail</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
          
           
            <div class="modal-body" id="addDetailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>
<div id="addDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="DetailModalLabel" aria-hidden="true" style="width:1100px; height:300px; margin-left:-550px;">
        <form id="DetailForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">×</button>
                <h3 id="addDetailModalLabel">Shipment Cost Detail</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
          
           
            <div class="modal-body" id="addDetailModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>
	<div id="addDetailTotalModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="DetailTotalModalLabel" aria-hidden="true" style="width:1100px; height:500px; margin-left:-550px;">
        <form id="DetailTotalForm" method="post" style="margin: 0px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closeDetailTotalModal">×</button>
                <h3 id="addDetailTotalModalLabel">Shipment Cost Detail</h3>
            </div>
            <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
                Error Message
            </div>
          
           
            <div class="modal-body" id="addDetailTotalModalForm">
                
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeDetailTotalModal">Close</button>
                <!--<button class="btn btn-primary">Submit</button>-->
            </div>
        </form>
    </div>