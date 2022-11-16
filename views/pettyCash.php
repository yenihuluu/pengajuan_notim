<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'PettyCash';

$date = new DateTime();
$paymentDate = $date->format('d/m/Y');
$allowAccount = true;
$allowBank = true;
$allowGeneralVendor = true;

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

$sql = "SELECT GROUP_CONCAT(us.`stockpile_id`) AS stockpile_id FROM user_stockpile us LEFT JOIN stockpile s ON us.stockpile_id = s.stockpile_id WHERE us.user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
if($result->num_rows > 0) {
	 while($row = $result->fetch_object()) {
		 $stockpileId = $row->stockpile_id;
	 }
}

if(isset($_SESSION['PettyCash'])) {
    $paymentMethod = $_SESSION['PettyCash']['paymentMethod'];
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
        echo "<option value=''>-- Please Select Buyer --</option>";
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
        $('#amount').number(true, 10);
		
		
		 
        
//        $('#ppn1').number(true, 2);
//        
//        $('#pph1').number(true, 2);
        
        $('#insertModal').on('hidden', function () {
            // do something…
            if(document.getElementById('accountId').value == 'INSERT') {
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
            } else if(document.getElementById('bankId').value == 'INSERT') {
                //setBank(0, 0);
            } else if(document.getElementById('generalVendorId').value == 'INSERT') {
                setGeneralVendor(0, 0);
            } 
        });
        
        <?php
        if(isset($_SESSION['PettyCash'])) {
        ?>
        if(document.getElementById('paymentMethod').value != '') {
			//resetBank(' ');
            setPaymentLocation();
			setStockpileLocation();
			//setBank();
            <?php
            if($_SESSION['PettyCash']['paymentType'] != '') {
            ?>
            setPaymentType(1, <?php echo $_SESSION['PettyCash']['paymentType']; ?>,  $('select[id="paymentMethod"]').val());
            setPaymentFor(<?php echo $_SESSION['PettyCash']['paymentType']; ?>);
			setBank(1, <?php echo $_SESSION['PettyCash']['paymentType']; ?>, $('select[id="paymentMethod"]').val());
            
            if(document.getElementById('paymentFor').value != '') {
                resetAccount(' ');
                <?php
                //if($_SESSION['PettyCash']['accountId'] != '') {
                ?>
                setAccount(1, $('select[id="paymentFor"]').val(), <?php echo $_SESSION['PettyCash']['accountId']; ?>, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                <?php
               // } else {
                ?>
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                <?php
                //}
                ?>

                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                    if(document.getElementById('paymentFor').value == 10) {
                      
						$('#addDetail').show();
						$('#bankPaymentDetail').show();
						
                    }else if(document.getElementById('paymentFor').value == 2) {
                       if(document.getElementById('paymentMethod').value == 2){ 
							$('#freightDownPayment').show();
							//$('#freightDownPayment2').show();
						}else{
							$('#freightPayment').show();
						}
						setAmountPayment();
                    } else if(document.getElementById('paymentFor').value == 3) {
                        if(document.getElementById('paymentMethod').value == 2){ 
							$('#unloadingDownPayment').show();
						}else{
							$('#unloadingPayment').show();
						}
						setAmountPayment();
					} else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
						setAmountPayment();
                    }
                } else if(document.getElementById('paymentType').value == 1) {
                    // IN
					if(document.getElementById('paymentFor').value == 10) {
                      
						$('#addDetail').show();
						$('#bankPaymentDetail').show();
						
                    }else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
						setAmountPayment();
                    }
                }
		
		if(document.getElementById('stockpileId2').value != '') {
                     resetSupplier('freightId', ' ');
                    <?php
                    if($_SESSION['payment']['freightId'] != '') {
                    ?>
                    setSupplier(1, 'freightId', $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>);

                    <?php
                    if($_SESSION['payment']['vendorFreightId'] != '') {
                    ?>
                    setVendorFreight(1, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>, <?php echo $_SESSION['payment']['vendorFreightId']; ?>);
					
                    if(document.getElementById('paymentMethod').value == 2) {
                        //refreshSummaryFreight($('select[id="freightId"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        setSlipFreight(<?php echo $_SESSION['payment']['freightId']; ?>, <?php echo $_SESSION['payment']['vendorFreightId']; ?>, '', 'NONE', 'NONE');
                    }
                    <?php
                    } else {
                    ?>
                    setVendorFreight(0, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>, 0);                            
                    <?php
                    }
                    } else {
                    ?>
                    setSupplier(0, 'freightId', $('select[id="stockpileId2"]').val(), 0);
                    <?php
                    }
                    ?>
                    resetVendorFreight(' Freight ');
                }
                
                if(document.getElementById('stockpileId3').value != '') {
                    resetLabor(' ');
                    <?php
                    if($_SESSION['PettyCash']['laborId'] != '') {
                    ?>
                    setLabor(1, $('select[id="stockpileId3"]').val(), <?php echo $_SESSION['PettyCash']['laborId']; ?>);
                    
                    if(document.getElementById('paymentMethod').value == 2) {
                        //refreshSummaryUnloading($('select[id="freightId"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        setSlipUnloading($('select[id="stockpileId3"]').val(), <?php echo $_SESSION['PettyCash']['laborId']; ?>);
                    }
                    <?php
                    } else {
                    ?>
                    setLabor(0, $('select[id="stockpileId3"]').val(), 0);
                    <?php
                    }
                    ?>
                }
				
				if(document.getElementById('stockpileIdFcDp').value != '') {
                     resetSupplierFcDp('freightIdFcDp', ' ');
                    <?php
                    if($_SESSION['payment']['freightIdFcDp'] != '') {
                    ?>
                    setSupplierFcDp(1, 'freightIdFcDp', $('select[id="stockpileIdFcDp"]').val(), <?php echo $_SESSION['payment']['freightIdFcDp']; ?>);
                         
                    <?php
                    
                    } else {
                    ?>
                    setSupplierFcDp(0, 'freightIdFcDp', $('select[id="stockpileIdFcDp"]').val(), 0);
                    <?php
                    }
                    ?>
                    
                }
                if(document.getElementById('stockpileLaborDp').value != '') {
                     resetLaborDp('LaborDp', ' ');
                    <?php
                    if($_SESSION['payment']['LaborDp'] != '') {
                    ?>
                    setLaborDp(1, 'LaborDp', $('select[id="stockpileLaborDp"]').val(), <?php echo $_SESSION['payment']['LaborDp']; ?>);
                         
                    <?php
                    
                    } else {
                    ?>
                    setLaborDp(0, 'LaborDp', $('select[id="stockpileLaborDp"]').val(), 0);
                    <?php
                    }
                    ?>
                    
                }
               
            } else {
              //  resetAccount(' Payment For ');
            }
            <?php
            } else {
            ?>
            setPaymentType(0, 0, $('select[id="paymentMethod"]').val());
			setBank(0, 0, $('select[id="paymentMethod"]').val());
            <?php
            }
            ?>
            
        } 
        <?php
        }
        ?>
        
        $('#paymentMethod').change(function() {
            resetPaymentType(' Method ');
            resetPaymentFor(' Type ');
			resetBank(' Bank ');
			resetAmountPayment(' Amount ');
			resetBankDetail(' ');
          //  resetAccount(' Payment For ');
            
		   $('#addDetail').hide();
		   $('#bankPaymentDetail').hide();
           $('#freightPayment').hide();
			$('#freightDownPayment').hide();
           $('#unloadingPayment').hide();
		   $('#unloadingDownPayment').hide();
          // $('#divAmount').hide();
           $('#divInvoice').hide();
          document.getElementById('stockpileId2').value = '';
         	resetSupplier('freightId', ' Stockpile ');
			resetVendorFreight(' Stockpile ');
          document.getElementById('stockpileId3').value = '';
        	// resetLabor(' Stockpile ');
        	$('#slipPayment').hide();
            $('#paymentLocationLabel').hide();
			$('#stockpileLocationLabel').hide();
            
            if(document.getElementById('paymentMethod').value != '') {
                setPaymentType(0, 0, $('select[id="paymentMethod"]').val());
				setBank(0, 0, $('select[id="paymentMethod"]').val());
                setPaymentLocation();
				setStockpileLocation();
				//setBank();
                if(document.getElementById('paymentMethod').value == 1) {
                    $('#divInvoice').show();
//                    $('#divPaymentTo').show();
                }else if(document.getElementById('paymentMethod').value == 2) {
                    $('#divInvoice').show();
//                    $('#divPaymentTo').show();
                }
            } 
        });
        
        $('#paymentType').change(function() {
            resetPaymentFor(' Type ');
			resetAmountPayment(' Amount ');
			resetBankDetail(' ');
			//resetBank(' Bank ');
           // resetAccount(' Payment For ');
           
			$('#addDetail').hide();
			$('#bankPaymentDetail').hide();
           	$('#freightPayment').hide();
			$('#freightDownPayment').hide();
            $('#unloadingPayment').hide();
			$('#unloadingDownPayment').hide();
           // $('#divAmount').hide();
      		document.getElementById('stockpileId2').value = '';
            resetSupplier('freightId', ' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#paymentLocationLabel').hide();
            $('#stockpileLocationLabel').hide();
            if(document.getElementById('paymentType').value != '') {
                setPaymentFor($('select[id="paymentType"]').val());
                setPaymentLocation();
                setStockpileLocation();
				//setBank($('select[id="paymentType"]').val());
                if(document.getElementById('paymentType').value == 1) {
                    // IN
                    document.getElementById('currencyId').value = 1;
                    if(document.getElementById('paymentMethod').value == 2) {
                        // DP
                       // $('#divAmount').show();
                    }
                } else {
                    // OUT	
                    document.getElementById('currencyId').value = 1;
                }
            } 
        });
        
        $('#paymentFor').change(function() {
           
            resetBankDetail(' ');
            resetAmountPayment(' Amount ');
			$('#addDetail').hide();
			$('#bankPaymentDetail').hide();
            document.getElementById('stockpileId2').value = '';
            resetSupplier('freightId', ' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
			$('#freightPayment').hide();
			$('#freightDownPayment').hide();
            $('#unloadingPayment').hide();
			$('#unloadingDownPayment').hide();
			//$('#divAmount').hide();
			$('#slipPayment').hide();
            if(document.getElementById('paymentFor').value != '') {
                resetAccount(' ');
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                //setPaymentLocation();
               // setStockpileLocation();
			   if(document.getElementById('paymentMethod').value == 2) {
			   		if(document.getElementById('paymentType').value == 2) {
						if(document.getElementById('paymentFor').value == 0) {
							// $('#divTaxPKS').show();
							
						}
						}
						}
						
                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                   if(document.getElementById('paymentFor').value == 10) {
                      
						$('#addDetail').show();
						$('#bankPaymentDetail').show();
						
                    } else if(document.getElementById('paymentFor').value == 2) {
                        if(document.getElementById('paymentMethod').value == 2){ 
							$('#freightDownPayment').show();
							//$('#freightDownPayment2').show();
						}else{
							$('#freightPayment').show();
						}
						setAmountPayment();
                    } else if(document.getElementById('paymentFor').value == 3) {
                        if(document.getElementById('paymentMethod').value == 2){ 
							$('#unloadingDownPayment').show();
						}else{
							$('#unloadingPayment').show();
						}
						setAmountPayment();
                    } else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
						setAmountPayment();
                    }
					
                } else if(document.getElementById('paymentType').value == 1) {
                    // IN
					if(document.getElementById('paymentFor').value == 10) {
                      
						$('#addDetail').show();
						$('#bankPaymentDetail').show();
						
                    } else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
						setAmountPayment();
                    }
                }
                
//                if(document.getElementById('paymentFor').value == 1 && document.getElementById('paymentType').value == 1) {
//                    $('#buyerPayment').show();
//                    $('#vendorPayment').hide();
//                } else if(document.getElementById('paymentFor').value == 1 && document.getElementById('paymentType').value == 2) {
//                    $('#buyerPayment').hide();
//                    $('#vendorPayment').show();
//                } 
            } else {
                resetAccount(' Payment For ');
            }
        });
        
       
        $('#stockpileId2').change(function() {
            resetSupplier('freightId', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('stockpileId2').value != '') {
                resetSupplier(' ');
                setSupplier(0, $('select[id="stockpileId2"]').val(), 0);
            }
        });
		
        $('#stockpileIdFcDp').change(function() {
            resetSupplierFcDp('freightIdFcDp', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('stockpileIdFcDp').value != '') {
                resetSupplierFcDp(' ');
                setSupplierFcDp(0, $('select[id="stockpileIdFcDp"]').val(), 0);
            }
        });
		
		$('#freightIdFcDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            //resetBankDetail(' ');
			resetFreightBankDP ($('select[id="paymentFor"]').val());
			//alert('html from to nya');
			
            if(document.getElementById('freightIdFcDp').value != '') {
                 if(document.getElementById('paymentMethod').value == 2) {
                    //setSlipHandling($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
					getFreightBankDP(0,$('select[id="freightIdFcDp"]').val(), $('select[id="paymentFor"]').val());
					//getBankDetail($('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '01/01/2015', '01/01/2016');
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '2015-01-01', '2016-01-01');
					
                }
            }
        });
		
		$('#freightBankDp').change(function() {
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('freightBankDp').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="freightBankDp"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
		/*$('#freightId').change(function() {
            resetVendorFreight(' ');
			//resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), 0);
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });*/
		
       $('#freightId').change(function() {
            resetVendorFreight(' ');
			//resetBankDetail (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			
			resetVendorBank ($('select[id="paymentFor"]').val());
            
            if(document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), 0);
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
				 getVendorBank(0,$('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
            }
        });
		
		$('#freightBankId').change(function() {
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('freightBankId').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="freightBankId"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
		
		$('#gvPCBankId').change(function() {
			//alert ('test');
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('gvPCBankId').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="gvPCBankId"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
		
        $('#vendorFreightId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
			 resetBankDetail(' ');
			 
		
		//bikin alert dsini
		//alert('html from to nya');
         		
            if(document.getElementById('vendorFreightId').value != '' && document.getElementById('freightId').value != '') {
                if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryFreight($('select[id="freightId"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
					
					//alert(document.getElementById('vendorFreightId').value);
					
					
 					setSlipFreight($('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), $('select[id="vendorFreightId"]').val(), '', 'NONE', 'NONE',$('input[id="paymentFrom"]').val(),$('input[id="paymentTo"]').val());
						//alert(setSlipFreight);
					getBankDetail($('select[id="freightId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipFreight($('select[id="freightId"]').val(), '', 'NONE', 'NONE', '2016-02-01', '2016-02-05');
				
                }
            }
        });
		
        
         $('#stockpileId3').change(function() {
            resetLabor(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
			
            if(document.getElementById('stockpileId3').value != '') {
                resetLabor(' ');
                setLabor(0, $('select[id="stockpileId3"]').val(), 0);
            }
        });
        
       $('#laborId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetVendorBank(' ');
			//alert('html from to nya');
			
            if(document.getElementById('laborId').value != '') {
                if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryUnloading($('select[id="freightId"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
                    setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
					getVendorBank(0,$('select[id="laborId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '01/01/2015', '01/01/2016');
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '2015-01-01', '2016-01-01');
					
                }
            }
        });
		
		$('#laborBankId').change(function() {
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('laborBankId').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="laborBankId"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
		
        $('#stockpileLaborDp').change(function() {
            resetLaborDp(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
			
            if(document.getElementById('stockpileLaborDp').value != '') {
                resetLaborDp(' ');
                setLaborDp(0, $('select[id="stockpileLaborDp"]').val(), 0);
            }
        });
        
       $('#laborDp').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            //resetBankDetail(' ');
			resetLaborBankDP ($('select[id="paymentFor"]').val());
			//alert('html from to nya');
			
            if(document.getElementById('laborDp').value != '') {
                 if(document.getElementById('paymentMethod').value == 2) {
                    //setSlipHandling($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
					getLaborBankDP(0,$('select[id="laborDp"]').val(), $('select[id="paymentFor"]').val());
					//getBankDetail($('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '01/01/2015', '01/01/2016');
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '2015-01-01', '2016-01-01');
					
                }
            }
        });
		
		$('#laborBankDp').change(function() {
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail ($('select[id="paymentFor"]').val());
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('laborBankDp').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="laborBankDp"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
         
        $('#bankId').change(function() {
            //resetExchangeRate();
            
            if(document.getElementById('bankId').value != '' && document.getElementById('bankId').value != 'INSERT') {
                if(document.getElementById('currencyId').value != '' && document.getElementById('currencyId').value != 0) {
                    setExchangeRate($('select[id="bankId"]').val(), $('input[id="currencyId"]').val(), $('input[id="journalCurrencyId"]').val());
                } else {		 
                    if(document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 2) {
                        if(document.getElementById('accountId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                        }
                    }  else if(document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 1) {
                        if(document.getElementById('accountId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                        }
                    }else if(document.getElementById('paymentFor').value == 1 && document.getElementById('paymentType').value == 1) {
                        if(document.getElementById('salesId').value != '') {
                            setCurrencyId($('select[id="paymentType"]').val(), 0, $('select[id="salesId"]').val());
                        }
                    }
                }
            } else if(document.getElementById('bankId').value != '' && document.getElementById('bankId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/payment-bank.php', {});
            } 
        });
        
        
		
		
        
//        $('#currencyId').change(function() {
//            resetExchangeRate();
//            
//            if(document.getElementById('currencyId').value != '' && document.getElementById('bankId').value != '') {
//                setExchangeRate($('select[id="bankId"]').val(), $('select[id="currencyId"]').val());
//            } 
//        });
        
        $("#paymentDataForm").validate({
            rules: {
                paymentMethod: "required",
                paymentDate: "required",
                paymentType: "required",
				payment_type: "required",
                bankId: "required",
                paymentFor: "required",
                accountId: "required",
                stockpileId: "required"
                
            },
            messages: {
                paymentMethod: "Method is a required field.",
                paymentDate: "Payment Date is a required field.",
                paymentType: "Type is a required field.",
				payment_type: "Payment Type is a required field.",
                bankId: "Bank Account is a required field.",
                paymentFor: "Payment For is a required field.",
                accountId: "Account is a required field.",
                stockpileId: "Stockpile is a required field."
                
            },
            submitHandler: function(form) {
                $('#submitButton2').attr("disabled", true);
			//alert(ppnValue + "," + pphValue);
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#paymentDataForm").serialize(),
                    success: function(data) {
						
                        var returnVal = data.split('|');
						
                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
							
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
//                                $('#pageContent').load('views/payment.php', {}, iAmACallbackFunction);
                                $('#pageContent').load('forms/search-pettyCash.php', {paymentId: returnVal[3], direct: 1}, iAmACallbackFunction);
                            } 
                            
                            $('#submitButton2').attr("disabled", false);
                        }
                    }
                });
            }
        });
      
		$("#insertForm").validate({
			 rules: {
                //contractType: "required",
                pcMethod: "required",
                currencyId11: "required",
                exchangeRate11: "required",
                accountId11: "required",
                paymentCashType: "required",
				qty11: "required",
				price11: "required",
				generalVendorId11: "required",
				pphTaxId: "required",
				amount11: "required"
                //stockpileId: "required"
            },
            messages: {
               // contractType: "Contract Type is a required field.",
                pcMethod: "Method is a required field.",
                currencyId11: "Currency is a required field.",
                exchangeRate11: "Exchange Rate is a required field.",
                accountId11: "Account is a required field.",
                paymentCashType: "Type is a required field.",
				qty11: "Quantity Type is a required field.",
				price11: "Price Type is a required field.",
				generalVendorId11: "Vendor Type is a required field.",
				pphTaxId: "PPh Type is a required field.",
				amount11: "Amount is a required field."
                //stockpileId: "Stockpile is a required field."
            },
			 
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
                                 setPaymentDetail();
								 getVendorBank(0,$('select[id="generalVendorId11"]').val(), $('select[id="paymentFor"]').val());
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
	
	function resetFreightBankDP(paymentFor) {
		 if(paymentFor == 2){
			document.getElementById('freightBankDp').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('freightBankDp').options.add(x);
		}
    }
    
    function getFreightBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 2){
					if(returnValLength > 0) {
                        document.getElementById('freightBankDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightBankDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightBankDp').options.add(x);
                    }
				}
                    if(type == 1) {
						 if(paymentFor == 2){
						document.getElementById('freightBankDp').value = vendorId;
						}
                    }
                }
            }
        });
    }
	
	function resetLaborBankDP(paymentFor) {
		 if(paymentFor == 3){
			document.getElementById('laborBankDp').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('laborBankDp').options.add(x);
		}
    }
    
    function getLaborBankDP(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 3){
					if(returnValLength > 0) {
                        document.getElementById('laborBankDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborBankDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborBankDp').options.add(x);
                    }
				}
                    if(type == 1) {
						 if(paymentFor == 3){
						document.getElementById('laborBankDp').value = vendorId;
						}
                    }
                }
            }
        });
    }
	
	function resetVendorBank(paymentFor) {
		//alert (paymentFor);
		 if(paymentFor == 2){
			document.getElementById('freightBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('freightBankId').options.add(x);
		}else if(paymentFor == 3){
			document.getElementById('laborBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('laborBankId').options.add(x);
		}else if(paymentFor == 10){
			document.getElementById('gvPCBankId').options.length = 0;
			var x = document.createElement('option');
			x.value = '';
			x.text = '-- Please Select --';
			document.getElementById('gvPCBankId').options.add(x);
		}
    }
    
    function getVendorBank(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
                    vendorId: vendorId,
					paymentFor: paymentFor
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
				 if(paymentFor == 2){
					if(returnValLength > 0) {
                        document.getElementById('freightBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightBankId').options.add(x);
                    }
				}else if(paymentFor == 3){
					if(returnValLength > 0) {
                        document.getElementById('laborBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborBankId').options.add(x);
                    }
				} else if(paymentFor == 10){
					if(returnValLength > 0) {
                        document.getElementById('gvPCBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('gvPCBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('gvPCBankId').options.add(x);
                    }
				}
                    if(type == 1) {
						if(paymentFor == 2){
						document.getElementById('freightBankId').value = vendorId;
						}else if(paymentFor == 3){
						document.getElementById('laborBankId').value = vendorId;
						} else if(paymentFor == 10){
						document.getElementById('gvPCBankId').value = vendorId;
						}
                    }
                }
            }
        });
    }
	
	function resetVendorBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
		document.getElementById('rek').value = '';
		document.getElementById('swift').value = '';
    }
    
    function getVendorBankDetail(vendorBankId, paymentFor) {
		
        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getVendorBankDetail',
                        vendorBankId: vendorBankId,
                        paymentFor: paymentFor
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('beneficiary').value = returnVal[1];
                        document.getElementById('bank').value = returnVal[2];
						document.getElementById('rek').value = returnVal[3];
						document.getElementById('swift').value = returnVal[4];
                    }
                }
            });
        }
	}
    
	function resetBankDetail() {
        document.getElementById('beneficiary').value = '';
        document.getElementById('bank').value = '';
		document.getElementById('rek').value = '';
		document.getElementById('swift').value = '';
    }
    
    function getBankDetail(bankVendor, paymentFor) {
		
        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getBankDetail',
                        bankVendor: bankVendor,
                        paymentFor: paymentFor
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('beneficiary').value = returnVal[1];
                        document.getElementById('bank').value = returnVal[2];
						document.getElementById('rek').value = returnVal[3];
						document.getElementById('swift').value = returnVal[4];
                    }
                }
            });
        }
	}
	
	function checkAll(a) {
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
	 checkSlipFreight(freightId, ppn, pph, paymentFrom, paymentTo);
 }


	
    function checkSlipFreight(stockpileId, freightId, vendorFreights, ppn, pph, paymentFrom, paymentTo) {
		
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if(selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }
		
        
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
        if (typeof(ppn) != 'undefined' && ppn != null && typeof(pph) != 'undefined' && pph != null)
        {
            if(ppn != 'NONE') {
                if(ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph != 'NONE') {
                if(pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        //alert(ppnValue + "," + pphValue);

       // alert(vendorFreights);
        setSlipFreight(stockpileId, freightId, vendorFreights, selected, ppnValue, pphValue, paymentFrom, paymentTo);
    }
    
		function checkAllUC(a) {
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
	 checkSlipUnloading(stockpileId, laborId, ppn, pph, paymentFromUP, paymentToUP);
 }
    function checkSlipUnloading(stockpileId, laborId, ppn, pph, paymentFromUP, paymentToUP) {
//        var checkedSlips = document.forms[0].checkedSlips;
        var checkedSlips = document.getElementsByName('checkedSlips[]');
        var selected = "";
        for (var i = 0; i < checkedSlips.length; i++) {
            if (checkedSlips[i].checked) {
                if(selected == "") {
                    selected = checkedSlips[i].value;
                } else {
                    selected = selected + "," + checkedSlips[i].value;
                }
            }
        }
        
      //alert(ppn +', '+ pph);
        
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
        if (typeof(ppn) != 'undefined' && ppn != null && typeof(pph) != 'undefined' && pph != null)
        {
            if(ppn != 'NONE') {
                if(ppn.value != '') {
                    ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph != 'NONE') {
                if(pph.value != '') {
                    pphValue = pph.value.replace(new RegExp(",", "g"), "");
                }
            }
        }
        
        setSlipUnloading(stockpileId, laborId, selected, ppnValue, pphValue, paymentFromUP, paymentToUP);
    }
	
	 function setSlipFreight(stockpileId, freightId, vendorFreightIds, checkedSlips, ppn, pph, paymentFrom, paymentTo) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipFreight',
					stockpileId: stockpileId,
                    freightId: freightId,
					vendorFreightId: vendorFreightIds,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }
    
    
    function setSlipUnloading(stockpileId, laborId, checkedSlips, ppn, pph, paymentFromUP, paymentToUP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipUnloading',
                    stockpileId: stockpileId,
                    laborId: laborId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFromUP: paymentFromUP,
					paymentToUP: paymentToUP
					
            },
			
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }
   function resetExchangeRate() {
//        $('#exchangeRate').attr("readonly", true);
        $('#labelExchangeRate').hide();
        $('#inputExchangeRate').hide();
        document.getElementById('bankId').value = '';
        document.getElementById('exchangeRate').value = '';
//        document.getElementById('labelExchangeRate').innerHTML = '';
    }
    
    function setExchangeRate(bankId, currencyId, journalCurrencyId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setExchangeRate',
                    bankId: bankId,
                    currencyId: currencyId,
                    journalCurrencyId: journalCurrencyId
            },
            success: function(data){
//                alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
//                    alert(returnVal[2]);
//                    document.getElementById('labelExchangeRate').innerHTML = returnVal[1];
//                    document.getElementById('exchangeRate').value = returnVal[3];
                    document.getElementById('bankCurrencyId').value = returnVal[1];
                    if(returnVal[1] == 1 && returnVal[1] == returnVal[2] && returnVal[1] == returnVal[3]) {
                        $('#labelExchangeRate').hide();
                        $('#inputExchangeRate').hide();
                    } else {
                        $('#labelExchangeRate').show();
                        $('#inputExchangeRate').show();
                    }
                    
//                    if(returnVal[1] != returnVal[2]) {
//                        $('#labelExchangeRate').show();
//                        $('#inputExchangeRate').show();
//                        $('#exchangeRate').attr("readonly", true);
//                    } 
//                    else {
//                        $('#labelExchangeRate').hide();
//                        $('#inputExchangeRate').hide();
//                        $('#exchangeRate').attr("readonly", false);
//                    }
                }
            }
        });
    }
    function setCurrencyId(paymentType, accountId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setCurrencyId',
                    paymentType: paymentType,
                    accountId: accountId,
                    salesId: salesId
            },
            success: function(data){
//                alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    document.getElementById('currencyId').value = returnVal[1];
                    if(document.getElementById('bankId').value != '') {
                        setExchangeRate($('select[id="bankId"]').val(), returnVal[1], $('input[id="journalCurrencyId"]').val());
                    }
                }
            }
        });
    }
   
   	function deletePaymentDetail(payment_cash_id) {
		
        $.ajax({
            url: './data_processing.php',
            method: 'POST',
            data: { action: 'delete_payment_detail',
					payment_cash_id: payment_cash_id
				/*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo */
            },
            success: function(data){
                if(data != '') {
                     setPaymentDetail();
                } 
            }
        });
    }
	
	function setPaymentDetail() {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setPaymentDetail',
				/*	stockpileId: stockpileId,
                    freightId: freightId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom: paymentFrom,
					paymentTo: paymentTo */
            },
            success: function(data){
                if(data != '') {
                    $('#paymentDetail').show();
                    document.getElementById('paymentDetail').innerHTML = data;
                } else {
					$('#paymentDetail').hide();
				}
            }
        });
    }
    
    function setSlip(stockpileContractId, checkedSlips) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlip',
                    stockpileContractId: stockpileContractId,
                    checkedSlips: checkedSlips
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
    }
    
    
    
    
	
    function resetPaymentType(text) {
        document.getElementById('paymentType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('paymentType').options.add(x);
    }
    
    function setPaymentType(type, paymentType, paymentMethod) {
        document.getElementById('paymentType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('paymentType').options.add(x);
        
		if(paymentMethod == 1){
        
		var x = document.createElement('option');
        x.value = '1';
        x.text = 'IN / Credit';
        document.getElementById('paymentType').options.add(x);
		
		var x = document.createElement('option');
        x.value = '2';
        x.text = 'OUT / Debit';
        document.getElementById('paymentType').options.add(x);
		
		}else{
        
		var x = document.createElement('option');
        x.value = '2';
        x.text = 'OUT / Debit';
        document.getElementById('paymentType').options.add(x);
		}
        if(type == 1) {
            document.getElementById('paymentType').value = paymentType;
        }   
    }
    
    function resetPaymentFor(text) {
        document.getElementById('paymentFor').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('paymentFor').options.add(x);
    }
    
    function setPaymentFor(paymentType) {
        document.getElementById('paymentFor').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('paymentFor').options.add(x);
        
        if(paymentType == 1) {
           
            var x = document.createElement('option');
       		x.value = '10';
        	x.text = 'Cash Payment';
        	document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('paymentFor').options.add(x);
			
        } else if(paymentType == 2) {
            
			var x = document.createElement('option');
            x.value = '2';
            x.text = 'Freight Cost';
            document.getElementById('paymentFor').options.add(x);

            var x = document.createElement('option');
            x.value = '3';
            x.text = 'Unloading Cost';
            document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
       		x.value = '10';
        	x.text = 'Cash Payment';
        	document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('paymentFor').options.add(x);
			
        }
     
        <?php
        if(isset($_SESSION['PettyCash']) && $_SESSION['PettyCash']['paymentFor'] != '') {
        ?>
        document.getElementById('paymentFor').value = <?php echo $_SESSION['PettyCash']['paymentFor']; ?>;     
        <?php
        }
        ?>
    }
	
	function resetBank(text) {
        document.getElementById('bankId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('bankId').options.add(x);
    }
    
    function setBank(type, paymentType, paymentMethod) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getBankPC',
                    //paymentFor: paymentFor,
                    paymentMethod: paymentMethod,
                    //paymentType: paymentType
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
                        document.getElementById('bankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('bankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('bankId').options.add(x);
                    }

                    
                                        
                    if(type == 1) {
                        document.getElementById('bankId').value = bankId;
                    }
                }
            }
        });
    }
	
	function resetLaborDp(text) {
        document.getElementById('laborDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('laborDp').options.add(x);
    }
    
    function setLaborDp(type, stockpileId, laborId) {
		//alert('test');
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLaborPayment',
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
                        document.getElementById('laborDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborDp').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('laborDp').value = laborId;
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
    }
    
    function setLabor(type, stockpileId, laborId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getLaborPayment',
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
                        document.getElementById('laborId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('laborId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('laborId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('laborId').value = laborId;
                    }
                }
            }
        });
    }
    
   function resetSupplierFcDp(text) {
        document.getElementById('freightIdFcDp').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightIdFcDp').options.add(x);
    }
    
    function setSupplierFcDp(type, stockpileId, freightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightPayment',
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
                        document.getElementById('freightIdFcDp').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightIdFcDp').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightIdFcDp').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('freightIdFcDp').value = freightId;
                    }
                }
            }
        });
    }
	
    function resetSupplier(text) {
        document.getElementById('freightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('freightId').options.add(x);
    }
    
    function setSupplier(type, stockpileId, freightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getFreightPayment',
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
                        document.getElementById('freightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('freightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('freightId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('freightId').value = freightId;
                    }
                }
            }
        });
    }
function resetVendorFreight(text) {
        document.getElementById('vendorFreightId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorFreightId').options.add(x);
    }
    
    function setVendorFreight(type, stockpileId, freightId, vendorFreightId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorFreight',
                    stockpileId: stockpileId,
					freightId: freightId
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
                        document.getElementById('vendorFreightId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '0';
                        x.text = '-- ALL --';
                        document.getElementById('vendorFreightId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorFreightId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('vendorFreightId').value = vendorFreightId;
                    }
                }
            }
        });
    }
   function resetAccount(text) {
        document.getElementById('accountId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('accountId').options.add(x);
    }
    
    function setAccount(type, paymentFor, accountId, paymentMethod, paymentType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getAccountPC',
                    paymentFor: paymentFor,
                    paymentMethod: paymentMethod,
                    paymentType: paymentType
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
                        document.getElementById('accountId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('accountId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('accountId').options.add(x);
                    }

                    <?php
                   /* if($allowAccount) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('accountId').options.add(x);
//                    }                  
                    <?php
                    }*/
                    ?>
                                        
                    if(type == 1) {
                        $('#accountId').find('option').each(function(i,e){
                            if($(e).val() == accountId){
                                $('#accountId').prop('selectedIndex',i);
                            }
                        });
                    }
                }
            }
        });
    }
	
	 function resetAmountPayment(text) {
//        $('#exchangeRate').attr("readonly", true);
        $('#amountPaymentLabel').hide();
        //$('#amountPaymentDiv').hide();
	 	document.getElementById('amountPaymentDiv').innerHTML = '';
        //document.getElementById('bankId').value = '';
        //document.getElementById('exchangeRate').value = '';
//      document.getElementById('amountPaymentDiv').innerHTML = '';
    }
    
    
	function setAmountPayment() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setAmountPayment'
            },
            success: function(data){
				
		       //alert(data);
			    var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    if(returnVal[1] > 1) {
                        $('#amountPaymentLabel').show();
                    }
                    document.getElementById('amountPaymentDiv').innerHTML = returnVal[2];
                }
			  
                
            }
        });
    }
    function setPaymentLocation() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setPaymentLocation'
            },
            success: function(data){
				
		       //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    if(returnVal[1] > 1) {
                        $('#paymentLocationLabel').show();
                    }
                    document.getElementById('paymentLocationDiv').innerHTML = returnVal[2];
                }
            }
        });
    }
	function setStockpileLocation() {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setStockpileLocation'
            },
            success: function(data){
                //alert(data);
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    if(returnVal[1] > 1) {
                        $('#stockpileLocationLabel').show();
                    }
                    document.getElementById('stockpileLocationDiv').innerHTML = returnVal[2];
                }
            }
        });
    }
	function hitungPPN() {
    var a = $(".dpSales").val();
    //var b = $(".b2").val();
    c = a * 0.1; //a kali b
    $(".ppnSales").val(c);
}
</script>
<script type="text/javascript">
$(document).ready(function(){
 $('#showDetail').click(function(e){
            e.preventDefault();  
        $('#insertModal').modal('show');
        $('#insertModalForm').load('forms/pettyCash-data.php', {});
        });	
});

</script>
<ul class="breadcrumb">
    <li class="active"><?php echo $_SESSION['menu_name']; ?></li>
</ul>

<form method="post" id="paymentDataForm">
    <input type="hidden" name="action" id="action" value="payment_data" />
    <div class="row-fluid">   
        <div class="span2 lightblue">
            <label>Method <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT '1' as id, 'Payment' as info UNION
                    SELECT '2' as id, 'Down Payment' as info;", $paymentMethod, "", "paymentMethod", "id", "info", 
                "", 2);
            ?>
        </div>
        <div class="span2 lightblue">
            <label>Payment Date <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentDate" name="paymentDate" value="<?php if(isset($_SESSION['PettyCash'])) { echo $_SESSION['PettyCash']['paymentDate']; } else { echo $paymentDate;} ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
    </div>
    <div class="row-fluid">  
        <div class="span2 lightblue">
            <label>Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("", "", "", "paymentType", "id", "info", 
                "", 3, "", 5);
            ?>
        </div>
        <div class="span2 lightblue">
            <label id="paymentLocationLabel" style="display: none;">Payment Location <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="paymentLocationDiv">
            
        </div>
        </div>
        <div class="row-fluid">
        <div class="span2 lightblue">
            <label id="stockpileLocationLabel" style="display: none;">Stockpile Location <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="stockpileLocationDiv">
            
        </div>
		<div class="span2 lightblue">
            <label>Payment Type <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
           createCombo("SELECT '1' as id, 'TT' as info UNION
                    SELECT '2' as id, 'Cek/Giro' as info UNION
					SELECT '3' as id, 'Tunai' as info UNION
					SELECT '4' as id, 'Bill Payment' as info UNION
					SELECT '5' as id, 'Auto Debet' as info;", $payment_type, "", "payment_type", "id", "info", 
                "", 2);
            ?>
        </div>
    </div>
   
    <div class="row-fluid">   
        <div class="span2 lightblue">
            <label>Payment For <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
//            createCombo("SELECT '1' as id, 'Inventory' as info UNION
//                    SELECT '2' as id, 'Non Inventory' as info;", "", "", "paymentFor", "id", "info", 
//                "", 1);
            createCombo("", "", "", "paymentFor", "id", "info", "", 4, "", 3);
            ?>
        </div>
        <div class="span2 lightblue">
            <label>Account <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
//            createCombo("", "", "", "accountId", "id", "info", 
//                "", 2);
            createCombo("", "", "", "accountId", "id", "info", "", 5, "select2combobox100", 4);
            ?>
        </div>
    </div>
     
     <div id="addDetail" class="row-fluid" style="display: none;">
     	<div class="span2 lightblue">
            
        </div>  
     	<div class="span2 lightblue">
        <button class="btn btn-warning" id="showDetail">Add Data</button>
        </div>
         <div class="span2 lightblue">
        </div>
        <div class="span3 lightblue">
         </div>
        <div class="span3 lightblue">
        </div>
        
    </div>
	<div class="row-fluid" id="freightDownPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Quantity <span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="" id="qtyFreight" name="qtyFreight" value="" />
        </div> 
		<div class="span4 lightblue">
		<label>Price<span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="" id="priceFreight" name="priceFreight" value="" />
        </div> 
		<div class="span4 lightblue">
		<label>Termin <span style="color: red;">*</span></label>
          <input type="text" class="span12" tabindex="" id="terminFreight" name="terminFreight" value="" />
        </div> 	
		</div>

		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
         <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full FROM user_stockpile us LEFT JOIN stockpile s ON us.stockpile_id = s.stockpile_id WHERE us.user_id = {$_SESSION['userId']} 
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileIdFcDp'], "", "stockpileIdFcDp", "stockpile_id", "stockpile_full", 
                "", 14, "select2combobox100");
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Freight Supplier <span style="color: red;">*</span></label>
        <?php
             createCombo("", "", "", "freightIdFcDp", "freight_id", "freight_supplier", 
                    "", 15, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "freightBankDp", "f_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 	
		</div>
       
		
    </div>  
    <div class="row-fluid" id="freightPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span4 lightblue">
			<label>Periode From<span style="color: red;">*</span></label>
			<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom"  data-date-format="dd/mm/yyyy" class="datepicker" >
        </div> 
		<div class="span4 lightblue">
			<label>Periode To<span style="color: red;">*</span></label>
			<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div> 
		<div class="span4 lightblue">
			<label>Stockpile <span style="color: red;">*</span></label>
			<?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full FROM user_stockpile us LEFT JOIN stockpile s ON us.stockpile_id = s.stockpile_id WHERE us.user_id = {$_SESSION['userId']} 
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId2'], "", "stockpileId2", "stockpile_id", "stockpile_full", 
                "", 14, "select2combobox100");
            ?>
        </div> 	
		</div>

		<div class="row-fluid">
		<div class="span4 lightblue">
		 <label>Vendor Freight <span style="color: red;">*</span></label>
         <?php
            createCombo("", "", "", "freightId", "freight_id", "freight_supplier", 
                    "", 15, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "freightBankId", "f_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Supplier <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorFreightId", "vendor_id", "vendor_freight", 
                    "", 15, "select2combobox100", 7, "multiple");
            ?>
        </div> 	
		</div>
    	
       </div>
    <div class="row-fluid" id="unloadingDownPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Quantity <span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="" id="qtyLabor" name="qtyLabor" value="" />
        </div> 
		<div class="span4 lightblue">
		<label>Price<span style="color: red;">*</span></label>
			<input type="text" class="span12" tabindex="" id="priceLabor" name="priceLabor" value="" />
        </div> 
		<div class="span4 lightblue">
		<label>Termin <span style="color: red;">*</span></label>
          <input type="text" class="span12" tabindex="" id="terminLabor" name="terminLabor" value="" />
        </div> 	
		</div>

		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
         <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full FROM user_stockpile us LEFT JOIN stockpile s ON us.stockpile_id = s.stockpile_id WHERE us.user_id = {$_SESSION['userId']} 
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileLaborDp'], "", "stockpileLaborDp", "stockpile_id", "stockpile_full", 
                "", 14, "select2combobox100");
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Labor <span style="color: red;">*</span></label>
        <?php
             createCombo("", "", "", "laborDp", "labor_id", "labor_name", 
                    "", 15, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "laborBankDp", "l_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 	
		</div>
       
		
    </div>  
	<div class="row-fluid" id="unloadingPayment" style="display: none;">
		<div class="row-fluid">
		<div class="span2 lightblue">
           <label>Periode From<span style="color: red;">*</span></label>
        </div>
		<div class="span4 lightblue">
		<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" name="paymentFromUP"  data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		<div class="span2 lightblue">
           <label>Periode To<span style="color: red;">*</span></label>
        </div>		
		<div class="span4 lightblue">
		<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div> 
		</div>
		<div class="row-fluid">
		<div class="span4 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
        <?php
		
			
			createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full FROM user_stockpile us LEFT JOIN stockpile s ON us.stockpile_id = s.stockpile_id WHERE us.user_id = {$_SESSION['userId']} 
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId3'], "", "stockpileId3", "stockpile_id", "stockpile_full", 
                "", 16,"select2combobox100");
            
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Labor <span style="color: red;">*</span></label>
        <?php
            createCombo("", "", "", "laborId", "labor_id", "labor_name",
                    "", 15, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "laborBankId", "l_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 	
		</div>
       
		
    </div>
	</br>
    <div class="row-fluid" id="slipPayment" style="display: none;">
        slip
    </div>
 <div class="row-fluid" id="paymentDetail" style="display: none;">
        Payment Detail
    </div>   
    
   <div class="row-fluid" id="bankPaymentDetail" style="display: none;">  
        
        <div class="span2 lightblue">
			<label>Vendor Bank <span style="color: red;">*</span></label>
           
        </div> 
		 <div class="span4 lightblue">
			
           <?php
            createCombo("", "", "", "gvPCBankId", "gv_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 
		<div class="span4 lightblue">
		   
        </div> 
        
        <div class="span4 lightblue">
			
        </div> 
        
    </div>
	</br>
    <div class="row-fluid">
      
        <div class="span2 lightblue">
            <label>From/To <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="hidden" name="currencyId" id="currencyId" value="<?php echo $_SESSION['PettyCash']['currencyId']; ?>" />
            <input type="hidden" name="journalCurrencyId" id="journalCurrencyId" value="1" />
            <input type="hidden" name="bankCurrencyId" id="bankCurrencyId" value="<?php echo $_SESSION['PettyCash']['bankCurrencyId']; ?>" />
            <?php
            createCombo("", "", "", "bankId", "bank_id", "bank_full", "", 5, "select2combobox100", 5);
            ?>
           
            <?php
          /*  createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full 
                    FROM bank b
                    INNER JOIN currency cur
                        ON cur.currency_id = b.currency_id
					WHERE b.bank_type = 2 AND b.stockpile_id IN ($stockpileId)
                    ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $_SESSION['PettyCash']['bankId'], "", "bankId", "bank_id", "bank_full", 
                "", 18, "select2combobox100", 1, "", $allowBank); */
            ?>
        </div>
        <div class="span2 lightblue" id="labelExchangeRate" style="display: none;">
            <label>Exchg Rate USD to IDR <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="inputExchangeRate" style="display: none;">
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $_SESSION['PettyCash']['exchangeRate']; ?>" />
        </div>
    </div>
   	</br>
    
     <div class="row-fluid">   
        <div class="span2 lightblue">
            <label id="amountPaymentLabel" style="display: none;">Amount <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="amountPaymentDiv">
            
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid" id="divInvoice" style="display: none;">   
        <div class="span1 lightblue">
            <label>Tax Invoice</label>
        </div>
        <div class="span3 lightblue">
            <input type="text" class="span12" tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $_SESSION['PettyCash']['taxInvoice']; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Invoice No</label>
        </div>
        <div class="span3 lightblue">
            <input type="text" class="span12" tabindex="" id="invoiceNo" name="invoiceNo" value="<?php echo $_SESSION['PettyCash']['invoiceNo']; ?>">
        </div>
       
        <div class="span1 lightblue">
            <label>Invoice Date</label>
        </div>
        <div class="span3 lightblue">
        <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="invoiceDate" name="invoiceDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
       
    </div>
    <div class="row-fluid">   
        <div class="span2 lightblue">
            <label>Cheque No</label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="chequeNo" name="chequeNo" value="<?php echo $_SESSION['PettyCash']['chequeNo']; ?>">
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span2 lightblue">
            <label>Remarks</label>
        </div>
        <div class="span4 lightblue">
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $_SESSION['PettyCash']['remarks']; ?></textarea>
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid">   
        <div class="span1 lightblue">
            <label>Beneficiary</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="beneficiary" name="beneficiary" value="<?php echo $beneficiary; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Bank</label>
        </div>
        <div class="span5 lightblue">
            <input type="text" readonly class="span12" tabindex="" id="bank" name="bank" value="<?php echo $bank; ?>">
        </div>
      
    </div>
    <div class="row-fluid">   

        <div class="span1 lightblue">
            <label>No Rek.</label>
        </div>
        <div class="span5 lightblue">
       		<input type="text" readonly class="span12" tabindex="" id="rek" name="rek" value="<?php echo $rek; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Swift Code</label>
        </div>
        <div class="span5 lightblue">
        	<input type="text" readonly class="span12" tabindex="" id="swift" name="swift" value="<?php echo $swift; ?>">
        </div>
       
    </div>
    <div class="row-fluid">
        <div class="span12 lightblue">
            <button class="btn btn-primary" <?php echo $disableProperty; ?> id="submitButton2">Submit</button>
        </div>
    </div>
</form>

<div id="insertModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true" style="width:1000px; height:500px; margin-left:-500px;" >
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