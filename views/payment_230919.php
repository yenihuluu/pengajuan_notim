<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$_SESSION['menu_name'] = 'Payments';

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

if(isset($_SESSION['payment'])) {
    $paymentMethod = $_SESSION['payment']['paymentMethod'];
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
    } else if($empty == 7) {
        echo "<option value=''>-- All --</option>";
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
        $('#amount').number(true, 2);
		
		
		 
        
//        $('#ppn1').number(true, 2);
//        
//        $('#pph1').number(true, 2);
        
        $('#insertModal').on('hidden', function () {
            // do something…
            if(document.getElementById('accountId').value == 'INSERT') {
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
            } else if(document.getElementById('bankId').value == 'INSERT') {
                setBank(0, 0);
            } else if(document.getElementById('generalVendorId').value == 'INSERT') {
                setGeneralVendor(0, 0);
            } 
        });
        
        <?php
        if(isset($_SESSION['payment'])) {
        ?>
        if(document.getElementById('paymentMethod').value != '') {
            setPaymentLocation();
			setStockpileLocation();
            <?php
            if($_SESSION['payment']['paymentType'] != '') {
            ?>
            setPaymentType(1, <?php echo $_SESSION['payment']['paymentType']; ?>);
            setPaymentFor(<?php echo $_SESSION['payment']['paymentType']; ?>);
            
            if(document.getElementById('paymentFor').value != '') {
                resetAccount(' ');
                <?php
                if($_SESSION['payment']['accountId'] != '') {
                ?>
                setAccount(1, $('select[id="paymentFor"]').val(), <?php echo $_SESSION['payment']['accountId']; ?>, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                <?php
                } else {
                ?>
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                <?php
                }
                ?>

                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                    if(document.getElementById('paymentFor').value == 0) {
                        $('#vendorPayment').show();
                    } else if(document.getElementById('paymentFor').value == 1) {
                        $('#curahPayment').show();
                    } else if(document.getElementById('paymentFor').value == 2) {
                        if(document.getElementById('paymentMethod').value == 2){ 
							$('#freightDownPayment').show();
							$('#freightDownPayment2').show();
						}else{
							$('#freightPayment').show();
						}
                    } else if(document.getElementById('paymentFor').value == 9) {
                        $('#handlingPayment').show();
                    } else if(document.getElementById('paymentFor').value == 3) {
                        $('#unloadingPayment').show();
                    } else if(document.getElementById('paymentFor').value == 4) {
                        $('#loadingPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 5) {
						$('#umumPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 6) {
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
						$('#divInvoice').hide();
						$('#invoicePayment').show();
						setStockpileLocation();
                    } else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
                    }
                } else if(document.getElementById('paymentType').value == 1) {
                    // IN
					if(document.getElementById('paymentFor').value == 4) {
                        $('#loadingPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 5) {
						$('#umumPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                   } else if(document.getElementById('paymentFor').value == 6) {
                        $('#divPaymentTo').show();
                        $('#divTax').show();
					} else if(document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
						$('#divInvoice').hide();
						$('#invoicePayment').show();
						setStockpileLocation();
					} else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
                    }else if(document.getElementById('paymentFor').value == 1) {
                        $('#buyerPayment').show();
                        document.getElementById('currencyId').value = 1;
                    }else if(document.getElementById('paymentFor').value == 0) {
                        $('#vendorPayment').show();
                    }else {
                        document.getElementById('currencyId').value = 0;
                    }
                }

                if(document.getElementById('stockpileId').value != '') {
                    resetVendor('vendorId', ' ');
                    <?php
                    if($_SESSION['payment']['vendorId'] != '') {
                    ?>
                    setVendor(1, 'vendorId', $('select[id="stockpileId"]').val(), 'P', <?php echo $_SESSION['payment']['vendorId']; ?>);
					//setVendorBank(1,
                    <?php
                    if($_SESSION['payment']['stockpileContractId'] != '') {
                    ?>
                    setContract(1, $('select[id="stockpileId"]').val(), <?php echo $_SESSION['payment']['vendorId']; ?>, <?php echo $_SESSION['payment']['stockpileContractId']; ?>, $('select[id="paymentType"]').val());
                    refreshSummary(<?php echo $_SESSION['payment']['stockpileContractId']; ?>, $('select[id="paymentMethod"]').val(), 'NONE', 'NONE', $('select[id="paymentType"]').val());
                    <?php
                    } else {
                    ?>
                    setContract(0, $('select[id="stockpileId"]').val(), <?php echo $_SESSION['payment']['vendorId']; ?>, 0,$('select[id="paymentType"]').val());                            
                    <?php
                    }
                    } else {
                    ?>
                    setVendor(0, 'vendorId', $('select[id="stockpileId"]').val(), 'P', 0);
                    <?php
                    }
                    ?>
                    resetContract(' Vendor ');
                }
				if(document.getElementById('gvId').value != '') {
                    resetInvoice('invoiceId',' ');
                    <?php
                    if($_SESSION['payment']['invoiceId'] != '') {
                    ?>
                    setInvoice(1, $('select[id="paymentFor"]').val(), $('select[id="gvId"]').val(), <?php echo $_SESSION['payment']['invoiceId']; ?>);
					refreshInvoice(<?php echo $_SESSION['payment']['invoiceId']; ?>, $('select[id="paymentMethod"]').val(),'NONE', 'NONE');
                    <?php
                    } else {
                    ?>
					 setInvoice(0, $('select[id="paymentFor"]').val(), $('select[id="gvId"]').val(), 0);                            
                    <?php
                    }
					?>
				} 
                
                if(document.getElementById('stockpileId1').value != '') {
                    resetVendor('vendorId1', ' ');
                    <?php
                    if($_SESSION['payment']['vendorId1'] != '') {
                    ?>
                    setVendor(1, 'vendorId1', $('select[id="stockpileId1"]').val(), 'C', <?php echo $_SESSION['payment']['vendorId1']; ?>);   
                    
                    if(document.getElementById('paymentMethod').value == 2) {
    //                    refreshSummaryCurah($('select[id="vendorId1"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        setSlipCurah(<?php echo $_SESSION['payment']['vendorId1']; ?>);
                    }
                    <?php
                    } else {
                    ?>
                    setVendor(0, 'vendorId1', $('select[id="stockpileId1"]').val(), 'C', 0);
                    <?php
                    }
                    ?>
                }
				if(document.getElementById('stockpileId4').value != '') {
                    resetVendorHandling(' ');
                    <?php
                    if($_SESSION['payment']['vendorHandlingId'] != '') {
                    ?>
                   setVendorHandling(1, $('select[id="stockpileId4"]').val(), <?php echo $_SESSION['payment']['vendorHandlingId']; ?>);
                    
                    if(document.getElementById('paymentMethod').value == 2) {
                        //refreshSummaryFreight($('select[id="freightId"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        setSlipHandling(<?php echo $_SESSION['payment']['vendorHandlingId']; ?>, '', 'NONE', 'NONE');
                    }
                    <?php
                    } else {
                    ?>
                    setVendorHandling(0, $('select[id="stockpileId4"]').val(), 0);
                    <?php
                    }
                    ?>
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
				if(document.getElementById('stockpileIdFcDp').value != '') {
                     resetSupplierFcDp('freightIdFcDp', ' ');
                    <?php
                    if($_SESSION['payment']['freightIdFcDp'] != '') {
                    ?>
                    setSupplierFcDp(1, 'freightIdFcDp', $('select[id="stockpileIdFcDp"]').val(), <?php echo $_SESSION['payment']['freightIdFcDp']; ?>);
                         
                    <?php
                    
                    } else {
                    ?>
                    setSupplierFcDp(0, 'freightId', $('select[id="stockpileId2"]').val(), 0);
                    <?php
                    }
                    ?>
                    
                }
               //if(document.getElementById('stockpileId2').value != '') {
                //     resetSupplier('freightId', ' ');
                    <?php
                //    if($_SESSION['payment']['freightId'] != '') {
                    ?>
                //    setSupplier(1, 'freightId', $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>);

                    <?php
               //     if($_SESSION['payment']['vendorFreightId'] != '') {
                    ?>
               //     setVendorFreight(1, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>, <?php echo $_SESSION['payment']['vendorFreightId']; ?>);
					
                //    if(document.getElementById('paymentMethod').value == 2) {
                        //refreshSummaryFreight($('select[id="freightId"]').val());
                //    } else if(document.getElementById('paymentMethod').value == 1) {
              //          setSlipFreight(<?php echo $_SESSION['payment']['freightId']; ?>, <?php echo $_SESSION['payment']['vendorFreightId']; ?>, '', 'NONE', 'NONE');
              //      }
                    <?php
               //     } else {
                    ?>
                //    setVendorFreight(0, $('select[id="stockpileId2"]').val(), <?php echo $_SESSION['payment']['freightId']; ?>, 0);                            
                    <?php
              //      }
              //      } else {
                    ?>
              //      setSupplier(0, 'freightId', $('select[id="stockpileId2"]').val(), 0);
                    <?php
             //       }
                    ?>
             //       resetVendorFreight(' Freight ');
             //   }
                
                if(document.getElementById('stockpileId3').value != '') {
                    resetLabor(' ');
                    <?php
                    if($_SESSION['payment']['laborId'] != '') {
                    ?>
                    setLabor(1, $('select[id="stockpileId3"]').val(), <?php echo $_SESSION['payment']['laborId']; ?>);
                    
                    if(document.getElementById('paymentMethod').value == 2) {
                        //refreshSummaryUnloading($('select[id="freightId"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        setSlipUnloading($('select[id="stockpileId3"]').val(), <?php echo $_SESSION['payment']['laborId']; ?>);
                    }
                    <?php
                    } else {
                    ?>
                    setLabor(0, $('select[id="stockpileId3"]').val(), 0);
                    <?php
                    }
                    ?>
                }
                
                if(document.getElementById('customerId').value != '') {
                    resetShipment(' ');
                    <?php
                    if($_SESSION['payment']['salesId'] != '') {
                    ?>
                    setShipment(1, $('select[id="customerId"]').val(), <?php echo $_SESSION['payment']['salesId']; ?>);      
                    
                    setCurrencyId(1, 0, <?php echo $_SESSION['payment']['salesId']; ?>);
                	
                    if(document.getElementById('paymentMethod').value == 2) {
                        // DP
                        setSlipShipment(<?php echo $_SESSION['payment']['salesId']; ?>);
                        //refreshSummaryUnloading($('select[id="freightId"]').val());
                    } else if(document.getElementById('paymentMethod').value == 1) {
                        //setSlipUnloading($('select[id="laborId"]').val());
                        refreshSummaryShipment(<?php echo $_SESSION['payment']['salesId']; ?>, '', 'NONE', 'NONE');
                    }
                    <?php
                    } else {
                    ?>
                    setShipment(0, $('select[id="customerId"]').val(), 0);
                    <?php
                    }
                    ?>
                }
            } else {
                resetAccount(' Payment For ');
            }
            <?php
            } else {
            ?>
            setPaymentType(0, 0);
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
            resetAccount(' Payment For ');
			resetBankDetail(' ');
            $('#buyerPayment').hide();
            $('#vendorPayment').hide();
			$('#invoicePayment').hide();
            $('#curahPayment').hide();
            $('#freightPayment').hide();
			$('#freightDownPayment').hide();
			$('#freightDownPayment2').hide();
			$('#handlingPayment').hide();
            $('#unloadingPayment').hide();
            $('#loadingPayment').hide();
			$('#umumPayment').hide();
			$('#qtyPayment').hide();
			$('#divTaxPKS').hide();
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
			resetInvoice('invoiceId', 'General Vendor');
            resetContract(' Stockpile ');
			document.getElementById('stockpileId4').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier(' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            document.getElementById('customerId').value = '';
            resetShipment(' Buyer ');
            resetExchangeRate();
            document.getElementById('currencyId').value = '';
            $('#divAmount').show();
            $('#divInvoice').hide();
            $('#divPaymentTo').hide();
            $('#divTax').hide();
			$('#slipPayment').hide();
            $('#summaryPayment').hide();
            $('#paymentLocationLabel').hide();
			$('#stockpileLocationLabel').hide();
            
            if(document.getElementById('paymentMethod').value != '') {
                setPaymentType(0, 0);
                setPaymentLocation();
				setStockpileLocation();
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
            resetAccount(' Payment For ');
			resetBankDetail(' ');
            $('#buyerPayment').hide();
            $('#vendorPayment').hide();
			$('#invoicePayment').hide();
            $('#curahPayment').hide();
            $('#freightPayment').hide();
			$('#freightDownPayment').hide();
			$('#freightDownPayment2').hide();
			$('#handlingPayment').hide();
            $('#unloadingPayment').hide();
            $('#loadingPayment').hide();
			$('#umumPayment').hide();
			$('#qtyPayment').hide();
			$('#divTaxPKS').hide();
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
			resetInvoice('invoiceId', 'General Vendor');
            resetContract(' Stockpile ');
			document.getElementById('stockpileId4').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
			document.getElementById('stockpileId4').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier( ' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            document.getElementById('customerId').value = '';
            resetShipment(' Buyer ');
            resetExchangeRate();
            document.getElementById('currencyId').value = '';
            $('#divAmount').show();
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            $('#divPaymentTo').hide();
            $('#divTax').hide();
            $('#paymentLocationLabel').hide();
            $('#stockpileLocationLabel').hide();
            if(document.getElementById('paymentType').value != '') {
                setPaymentFor($('select[id="paymentType"]').val());
                setPaymentLocation();
                setStockpileLocation();
                if(document.getElementById('paymentType').value == 1) {
                    // IN
                    document.getElementById('currencyId').value = 1;
                    if(document.getElementById('paymentMethod').value == 2) {
                        // DP
                        $('#divAmount').show();
                    }
                } else {
                    // OUT
                    document.getElementById('currencyId').value = 1;
                }
            } 
        });
        
        $('#paymentFor').change(function() {
            document.getElementById('stockpileId').value = '';
            document.getElementById('stockpileId1').value = '';
            resetVendor('vendorId', ' Stockpile ');
            resetVendor('vendorId1', ' Stockpile ');
			resetInvoice('invoiceId', 'General Vendor');
            resetContract(' Stockpile ');
			resetBankDetail(' ');
			document.getElementById('stockpileId4').value = '';
			resetVendorHandling('vendorHandlingId', ' Stockpile ');
            document.getElementById('stockpileId2').value = '';
            resetSupplier(' Stockpile ');
			resetVendorFreight(' Stockpile ');
            document.getElementById('stockpileId3').value = '';
            resetLabor(' Stockpile ');
            document.getElementById('customerId').value = '';
            resetShipment(' Buyer ');
//            document.getElementById('currencyId').value = '';
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            $('#buyerPayment').hide();
            $('#vendorPayment').hide();
			$('#invoicePayment').hide();
            $('#curahPayment').hide();
            $('#freightPayment').hide();
			$('#freightDownPayment').hide();
			$('#freightDownPayment2').hide();
			$('#handlingPayment').hide();
            $('#unloadingPayment').hide();
            $('#loadingPayment').hide();
			$('#umumPayment').hide();
			$('#qtyPayment').hide();
            $('#divPaymentTo').hide();
            $('#divTax').hide();
			$('#divTaxPKS').hide();
            //$('#paymentLocationLabel').hide();
            //$('#stockpileLocationLabel').hide();
            if(document.getElementById('paymentFor').value != '') {
                resetAccount(' ');
                setAccount(0, $('select[id="paymentFor"]').val(), 0, $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                //setPaymentLocation();
               // setStockpileLocation();
			   if(document.getElementById('paymentMethod').value == 2) {
			   		if(document.getElementById('paymentType').value == 2) {
						if(document.getElementById('paymentFor').value == 0) {
							 $('#divTaxPKS').show();
							
						}
						}
						}
						
                if(document.getElementById('paymentType').value == 2) {
                    // OUT
                    if(document.getElementById('paymentFor').value == 0) {
                        $('#vendorPayment').show();
                    } else if(document.getElementById('paymentFor').value == 1) {
                        $('#curahPayment').show();
                    } else if(document.getElementById('paymentFor').value == 2) {
                         if(document.getElementById('paymentMethod').value == 2){ 
							$('#freightDownPayment').show();
							$('#freightDownPayment2').show();
						}else{
							$('#freightPayment').show();
						}
                    } else if(document.getElementById('paymentFor').value == 9) {
                        $('#handlingPayment').show();
                    } else if(document.getElementById('paymentFor').value == 3) {
                        $('#unloadingPayment').show();
                    } else if(document.getElementById('paymentFor').value == 4) {
                        $('#loadingPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 5) {
						$('#umumPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 6) {
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                    } else if(document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
						$('#divInvoice').hide();
						$('#invoicePayment').show();
						//setStockpileLocation();
                    } else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
                    }
                } else if(document.getElementById('paymentType').value == 1) {
                    // IN
					if(document.getElementById('paymentFor').value == 4) {
                        $('#loadingPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
						document.getElementById('currencyId').value = 1;
                    } else if(document.getElementById('paymentFor').value == 5) {
						$('#umumPayment').show();
						$('#qtyPayment').show();
                        $('#divPaymentTo').show();
                        $('#divTax').show();
						document.getElementById('currencyId').value = 1;
                    } else if(document.getElementById('paymentFor').value == 6) {
                        $('#divPaymentTo').show();
                        $('#divTax').show();
                        document.getElementById('currencyId').value = 1;
					} else if(document.getElementById('paymentFor').value == 8) {
                        //$('#divPaymentTo').show();
                        //$('#divTax').show();
						$('#divInvoice').hide();
						$('#invoicePayment').show();
						//setStockpileLocation();
                        document.getElementById('currencyId').value = 1;
					} else if(document.getElementById('paymentFor').value == 7) {
                        document.getElementById('currencyId').value = 0;
                    }else if(document.getElementById('paymentFor').value == 1) {
                        $('#buyerPayment').show();
                        document.getElementById('currencyId').value = 0;
                    }else if(document.getElementById('paymentFor').value == 0) {
                        $('#vendorPayment').show();
                    } else {
                        document.getElementById('currencyId').value = 1;
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
        
        $('#amount').change(function(){
            
            if(document.getElementById('paymentFor').value == 4 || document.getElementById('paymentFor').value == 5 || document.getElementById('paymentFor').value == 6) {
                if(document.getElementById('generalVendorId').value != "" && document.getElementById('amount').value != "") {
                    getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
                }
            }
            
        });
        
        $('#stockpileId').change(function() {
            resetVendor('vendorId', ' Stockpile ');
            resetContract(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('stockpileId').value != '') {
                resetVendor('vendorId', ' ');
                setVendor(0, 'vendorId', $('select[id="stockpileId"]').val(), 'P', 0);
                resetContract(' Vendor ');
            }
        });
        
        $('#vendorId').change(function() {
            resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBank (' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('vendorId').value != '') {
                setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBank(0,$('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetContract(' Vendor ');
            }
        });
		
		 $('#vendorBankId').change(function() {
            //resetContract(' ');
			//resetBankDetail (' ');
			resetVendorBankDetail (' ');
            //$('#slipPayment').hide();
            //$('#summaryPayment').hide();
            
            if(document.getElementById('vendorBankId').value != '') {
                //setContract(0, $('select[id="stockpileId"]').val(), $('select[id="vendorId"]').val(), 0, $('select[id="paymentType"]').val());
				 getVendorBankDetail($('select[id="vendorBankId"]').val(), $('select[id="paymentFor"]').val());
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                //resetContract(' Vendor ');
            }
        });
        
        $('#stockpileContractId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('stockpileContractId').value != '') {
//                if(document.getElementById('paymentMethod').value == 2) 
				refreshSummary($('select[id="stockpileContractId"]').val(), $('select[id="paymentMethod"]').val(), 'NONE', 'NONE', $('select[id="paymentType"]').val());
//                } else if(document.getElementById('paymentMethod').value == 1) {
//                    setSlip($('select[id="stockpileContractId"]').val());
//                }
            }
        });
       
	    $('#gvId').change(function() {
            resetInvoice(' Invoice ');
            resetBankDetail(' ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('gvId').value != '') {
                resetInvoice(' Invoice ');
                setInvoice(0,$('select[id="paymentFor"]').val(), $('select[id="gvId"]').val(),  0);
				getBankDetail($('select[id="gvId"]').val(), $('select[id="paymentFor"]').val());
				
                //resetContract(' Vendor ');
            }
        });
		 
		 $('#invoiceId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('invoiceId').value != '') {
//                if(document.getElementById('paymentMethod').value == 2) {
                    refreshInvoice($('select[id="invoiceId"]').val(), $('select[id="paymentMethod"]').val(), 'NONE', 'NONE');
//                } else if(document.getElementById('paymentMethod').value == 1) {
//                    setSlip($('select[id="stockpileContractId"]').val());
//                }
            }
        });
        $('#stockpileId1').change(function() {
            resetVendor('vendorId1', ' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('stockpileId1').value != '') {
                resetVendor('vendorId1', ' ');
                setVendor(0, 'vendorId1', $('select[id="stockpileId1"]').val(), 'C', 0);
            }
        });
        
        $('#vendorId1').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('vendorId1').value != '') {
                if(document.getElementById('paymentMethod').value == 2) {
//                    refreshSummaryCurah($('select[id="vendorId1"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
                    setSlipCurah($('select[id="vendorId1"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFrom1"]').val(),$('input[id="paymentTo1"]').val());
                }
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
            
            if(document.getElementById('freightId').value != '') {
                setVendorFreight(0, $('select[id="stockpileId2"]').val(), $('select[id="freightId"]').val(), 0);
				 //getBankDetail($('select[id="vendorId"]').val(), $('select[id="paymentFor"]').val());
            } else {
                resetVendorFreight(' Stockpile ');
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
		$('#stockpileId4').change(function() {
            resetVendorHandling(' Stockpile ');
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
			
            if(document.getElementById('stockpileId4').value != '') {
                resetVendorHandling(' ');
                setVendorHandling(0, $('select[id="stockpileId4"]').val(), 0);
            }
        });
        
       $('#vendorHandlingId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            resetBankDetail(' ');
			//alert('html from to nya');
			
            if(document.getElementById('vendorHandlingId').value != '') {
                if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryUnloading($('select[id="freightId"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
                    setSlipHandling($('select[id="stockpileId4"]').val(), $('select[id="vendorHandlingId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromHP"]').val(), $('input[id="paymentToHP"]').val());
					getBankDetail($('select[id="vendorHandlingId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '01/01/2015', '01/01/2016');
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '2015-01-01', '2016-01-01');
					
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
            resetBankDetail(' ');
			//alert('html from to nya');
			
            if(document.getElementById('laborId').value != '') {
                if(document.getElementById('paymentMethod').value == 2) {
                    //refreshSummaryUnloading($('select[id="freightId"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
                    setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', $('input[id="paymentFromUP"]').val(), $('input[id="paymentToUP"]').val());
					getBankDetail($('select[id="laborId"]').val(), $('select[id="paymentFor"]').val());
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '01/01/2015', '01/01/2016');
					//setSlipUnloading($('select[id="stockpileId3"]').val(), $('select[id="laborId"]').val(), '', 'NONE', 'NONE', '2015-01-01', '2016-01-01');
					
                }
            }
        });
        
        $('#customerId').change(function() {
            resetShipment(' Buyer ');
            
            if(document.getElementById('customerId').value != '') {
                resetShipment(' ');
                setShipment(0, $('select[id="customerId"]').val(), 0);
            }
        });
        
        $('#salesId').change(function() {
            $('#slipPayment').hide();
            $('#summaryPayment').hide();
            
            if(document.getElementById('salesId').value != '') {
                setCurrencyId(1, 0, $('select[id="salesId"]').val());
                	
                if(document.getElementById('paymentMethod').value == 2) {
                    // DP
                    setSlipShipment($('select[id="salesId"]').val());
                    //refreshSummaryUnloading($('select[id="freightId"]').val());
                } else if(document.getElementById('paymentMethod').value == 1) {
                    //setSlipUnloading($('select[id="laborId"]').val());
                    refreshSummaryShipment($('select[id="salesId"]').val(), '', 'NONE', 'NONE');
                }
            }
        });
        
        $('#accountId').change(function() {
            if(document.getElementById('accountId').value != '' && document.getElementById('accountId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/payment-account.php', {paymentFor: $('select[id="paymentFor"]').val()});
            } else if(document.getElementById('accountId').value != '' && document.getElementById('accountId').value != 'INSERT') {
                if(document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 2) {
                    setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                } else if (document.getElementById('paymentFor').value == 7 && document.getElementById('paymentType').value == 1) {
                    setCurrencyId($('select[id="paymentType"]').val(), $('select[id="accountId"]').val(), 0);
                } 
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
        
        $('#generalVendorId').change(function() {
            resetBankDetail();
            resetGeneralVendorTax();
            
            if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value != 'INSERT') {
//                ppnValue = ppn.value.replace(new RegExp(",", "g"), "");
                getGeneralVendorTax($('select[id="generalVendorId"]').val(), $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
				getBankDetail($('select[id="generalVendorId"]').val(), $('select[id="paymentFor"]').val());
            } else if(document.getElementById('generalVendorId').value != '' && document.getElementById('generalVendorId').value == 'INSERT') {
                $("#modalErrorMsgInsert").hide();
                $('#insertModal').modal('show');
                $('#insertModalForm').load('forms/payment-general-vendor.php', {});
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
                stockpileId: "required",
                vendorId: "required",
                stockpileContractId: "required",
                customerId: "required",
                salesId: "required",
                currencyId: "required",
                exchangeRate: "required",
				generalVendorId: "required"
            },
            messages: {
                paymentMethod: "Method is a required field.",
                paymentDate: "Payment Date is a required field.",
                paymentType: "Type is a required field.",
				payment_type: "Payment Type is a required field.",
                bankId: "Bank Account is a required field.",
                paymentFor: "Payment For is a required field.",
                accountId: "Account is a required field.",
                stockpileId: "Stockpile is a required field.",
                vendorId: "Vendor is a required field.",
                stockpileContractId: "PO No. is a required field.",
                customerId: "Buyer is a required field.",
                salesId: "Sales Agreement is a required field.",
                currencyId: "Currency is a required field.",
                exchangeRate: "Exchange Rate is a required field.",
				generalVendorId: "Payment From/To is a required field."
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
                                $('#pageContent').load('forms/search-payment.php', {paymentId: returnVal[3], direct: 1}, iAmACallbackFunction);
                            } 
                            
                            $('#submitButton2').attr("disabled", false);
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
                                
                                if (resultData[0] == 'ACCOUNT') {
                                    setAccount(1, $('select[id="paymentFor"]').val(), resultData[1], $('select[id="paymentMethod"]').val(), $('select[id="paymentType"]').val());
                                } else if (resultData[0] == 'BANK') {
                                    setBank(1, resultData[1]);
                                    setExchangeRate(resultData[1], $('input[id="currencyId"]').val(), $('input[id="journalCurrencyId"]').val());
                                } else if (resultData[0] == 'GENERALVENDOR') {
                                    setGeneralVendor(1, resultData[1]);
                                    getGeneralVendorTax(resultData[1], $('input[id="amount"]').val().replace(new RegExp(",", "g"), ""));
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
	
    function resetVendorBank(text) {
        document.getElementById('vendorBankId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorBankId').options.add(x);
    }
    
    function getVendorBank(type, vendorId, paymentFor) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorBank',
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
                        document.getElementById('vendorBankId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorBankId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorBankId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('vendorBankId').value = vendorId;
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
	
    function refreshSummary(stockpileContractId, paymentMethod, ppn, pph, paymentType) {
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
        if(paymentMethod == 1) {
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
        
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshSummary',
                    stockpileContractId: stockpileContractId,
                    paymentMethod: paymentMethod,
                    ppn: ppnValue,
                    pph: pphValue,
					paymentType: paymentType
            },
            success: function(data){
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
                }
            }
        });
    }
    
    function refreshInvoice(invoiceId, paymentMethod, ppn1, pph1) {
        var ppnValue = 'NONE';
        var pphValue = 'NONE';
        
         if(paymentMethod == 1) {
            if(ppn1 != 'NONE') {
                if(ppn1.value != '') {
                    ppnValue = ppn1.value.replace(new RegExp(",", "g"), "");
                }
            }

            if(pph1 != 'NONE') {
                if(pph1.value != '') {
                    pphValue = pph1.value.replace(new RegExp(",", "g"), "");
                }
            }
		 }
        
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshInvoice',
                    invoiceId: invoiceId,
                    paymentMethod: paymentMethod,
                    ppn1: ppnValue,
                    pph1: pphValue
            },
            success: function(data){
				//alert(data);
                if(data != '') {
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = data;
                }
            }
        });
    }
	
	 function refreshSummaryShipment(salesId, checkedSlips, ppn, pph) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'refreshSummaryShipment',
                    salesId: salesId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph
            },
            success: function(data){
                if(data != '') {
                    var returnVal = data.split('|');
                    $('#summaryPayment').show();
                    document.getElementById('summaryPayment').innerHTML = returnVal[0];
                    document.getElementById('currencyId').value = returnVal[1];
                }
            }
        });
    }
    
    function checkSlip(stockpileContractId) {
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
        setSlip(stockpileContractId, selected);
    }
	
	function checkAllCurah(a) {
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
	 checkSlipCurah(vendorId, ppn, pph, paymentFrom1, paymentTo1);
 }
    
    function checkSlipCurah(vendorId, ppn, pph, paymentFrom1, paymentTo1) {
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
        
        setSlipCurah(vendorId, selected, ppnValue, pphValue, paymentFrom1, paymentTo1);
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
	
	function DPChecklist(count1,count2) {
		alert (count1 + "," + count2);
		var arr = new Array();
		 arr = document.getElementsByName('count1');
		 arr2 = document.getElementsByName('count2');
		var DPTotal = "";
	   
        for (var i = 0; i < arr.length; i++) 
		{
			for (var i = 0; i < arr2.length; i++)
			{
				
			var obj1 = document.getElementsByName(count1).item(i);
			var var1 = obj1.value;
			
			var obj2 = document.getElementsByName(count2).item(i);
			var var2 = obj2.value;
			
			alert (var1+ "," + var2);
           /* if (checkedDPs[i] != '') {
                if(DPTotal == "") {
                    DPTotal = checkedDPAmounts[i].value;
                } else {
                    DPTotal = DPTotal + checkedDPAmounts[i].value;
                }
            }*/
        }
	}
		//alert (DPTotal);
		//document.getElementById('totalDp').value = DPTotal;
    }
	
	function checkAllHandling(a) {
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
	 checkSlipHandling(vendorHandlingId, ppn, pph, paymentFrom, paymentTo);
 }


	
    function checkSlipHandling(stockpileId, vendorHandlingId, ppn, pph, paymentFrom, paymentTo) {
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

        
        setSlipHandling(stockpileId, vendorHandlingId, selected, ppnValue, pphValue, paymentFrom, paymentTo);
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
    
    function checkSlipShipment(salesId, ppn, pph) {
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
        
        refreshSummaryShipment(salesId, selected, ppnValue, pphValue);
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
    
    function setSlipCurah(vendorId, checkedSlips, ppn, pph, paymentFrom1, paymentTo1) {
		//alert(vendorId +', '+ ppn +', '+ pph);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipCurah',
                    vendorId: vendorId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFrom1: paymentFrom1,
					paymentTo1: paymentTo1
            },
            success: function(data){
                if(data != '') {
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = data;
                }
            }
        });
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
    
    function setSlipHandling(stockpileId, vendorHandlingId, checkedSlips, ppn, pph, paymentFromHP, paymentToHP) {
		//alert(stockpileId +', '+ laborId +', '+ ppn +', '+ pph +', '+ paymentFromUP +', '+ paymentToUP);
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipHandling',
                    stockpileId: stockpileId,
                    vendorHandlingId: vendorHandlingId,
                    checkedSlips: checkedSlips,
                    ppn: ppn,
                    pph: pph,
					paymentFromHP: paymentFromHP,
					paymentToHP: paymentToHP
					
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
    
    function setSlipShipment(salesId, checkedSlips) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'setSlipShipment',
                    salesId: salesId,
                    checkedSlips: checkedSlips
            },
            success: function(data){
                if(data != '') {
                    var returnVal = data.split('|');
                    $('#slipPayment').show();
                    document.getElementById('slipPayment').innerHTML = returnVal[0];
                    document.getElementById('currencyId').value = returnVal[1];
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
    
    function resetShipment(text) {
        document.getElementById('salesId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('salesId').options.add(x);
    }
    
    function setShipment(type, customerId, salesId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getShipmentPayment',
                    customerId: customerId
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
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('salesId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('salesId').value = salesId;
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
    function resetVendorHandling(text) {
        document.getElementById('vendorHandlingId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('vendorHandlingId').options.add(x);
    }
    
    function setVendorHandling(type, stockpileId, vendorHandlingId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getHandlingPayment',
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
                        document.getElementById('vendorHandlingId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('vendorHandlingId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('vendorHandlingId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('vendorHandlingId').value = vendorHandlingId;
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
    
    function resetVendor(elementId, text) {
        document.getElementById(elementId).options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById(elementId).options.add(x);
    }
    
    function setVendor(type, elementId, stockpileId, contractType, vendorId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getVendorPayment',
                    stockpileId: stockpileId,
                    contractType: contractType
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
                        document.getElementById(elementId).options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById(elementId).options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById(elementId).options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById(elementId).value = vendorId;
                    }
                }
            }
        });
    }
    
    function resetContract(text) {
        document.getElementById('stockpileContractId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('stockpileContractId').options.add(x);
    }
    
    function setContract(type, stockpileId, vendorId, stockpileContractId, paymentType) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getStockpileContractPayment',
                    stockpileId: stockpileId,
                    vendorId: vendorId,
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
                        document.getElementById('stockpileContractId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('stockpileContractId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('stockpileContractId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('stockpileContractId').value = stockpileContractId;
                    }
                }
            }
        });
    }
    
	function resetInvoice(text) {
        document.getElementById('invoiceId').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select' + text + '--';
        document.getElementById('invoiceId').options.add(x);
    }
    
    function setInvoice(type, paymentFor, gvId, invoiceId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getInvoice',
                    gvId: gvId,
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
                    if(returnValLength > 0) {
                        document.getElementById('invoiceId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('invoiceId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('invoiceId').options.add(x);
                    }
                    
                    if(type == 1) {
                        document.getElementById('invoiceId').value = invoiceId;
                    }
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
    
    function setPaymentType(type, paymentType) {
        document.getElementById('paymentType').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '-- Please Select --';
        document.getElementById('paymentType').options.add(x);
        
        var x = document.createElement('option');
        x.value = '1';
        x.text = 'IN / Credit';
        document.getElementById('paymentType').options.add(x);
        
        var x = document.createElement('option');
        x.value = '2';
        x.text = 'OUT / Debit';
        document.getElementById('paymentType').options.add(x);
        
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
            x.value = '1';
            x.text = 'Sales';
            document.getElementById('paymentFor').options.add(x);
           
		    var x = document.createElement('option');
            x.value = '0';
            x.text = 'PKS Kontrak';
            document.getElementById('paymentFor').options.add(x);
           /* 
            var x = document.createElement('option');
            x.value = '1';
            x.text = 'PKS Curah';
            document.getElementById('paymentFor').options.add(x);
            
            var x = document.createElement('option');
            x.value = '2';
            x.text = 'Freight Cost';
            document.getElementById('paymentFor').options.add(x);

            var x = document.createElement('option');
            x.value = '3';
            x.text = 'Unloading Cost';
            document.getElementById('paymentFor').options.add(x);
            */
            var x = document.createElement('option');
            x.value = '4';
            x.text = 'Loading';
            document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
			x.value = '5';
			x.text = 'Umum';
			document.getElementById('paymentFor').options.add(x);
            
            var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('paymentFor').options.add(x);
            
//            var x = document.createElement('option');
//            x.value = '7';
//            x.text = 'Return';
//            document.getElementById('paymentFor').options.add(x);
        } else if(paymentType == 2) {
            var x = document.createElement('option');
            x.value = '0';
            x.text = 'PKS Kontrak';
            document.getElementById('paymentFor').options.add(x);
            
            var x = document.createElement('option');
            x.value = '1';
            x.text = 'PKS Curah';
            document.getElementById('paymentFor').options.add(x);
            
            var x = document.createElement('option');
            x.value = '2';
            x.text = 'Freight Cost';
            document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
            x.value = '9';
            x.text = 'Handling Cost';
            document.getElementById('paymentFor').options.add(x);

            var x = document.createElement('option');
            x.value = '3';
            x.text = 'Unloading Cost';
            document.getElementById('paymentFor').options.add(x);
            
          /*  var x = document.createElement('option');
            x.value = '4';
            x.text = 'Loading';
            document.getElementById('paymentFor').options.add(x);
            */
            var x = document.createElement('option');
            x.value = '7';
            x.text = 'Internal Transfer';
            document.getElementById('paymentFor').options.add(x);
			
			var x = document.createElement('option');
            x.value = '8';
            x.text = 'Invoice';
            document.getElementById('paymentFor').options.add(x);
        }
        
       /* var x = document.createElement('option');
        x.value = '5';
        x.text = 'Umum';
        document.getElementById('paymentFor').options.add(x);
        
        var x = document.createElement('option');
        x.value = '6';
        x.text = 'HO';
        document.getElementById('paymentFor').options.add(x);
		*/
		
        
        <?php
        if(isset($_SESSION['payment']) && $_SESSION['payment']['paymentFor'] != '') {
        ?>
        document.getElementById('paymentFor').value = <?php echo $_SESSION['payment']['paymentFor']; ?>;     
        <?php
        }
        ?>
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
            data: { action: 'getAccount',
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
                    if($allowAccount) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('accountId').options.add(x);
//                    }                  
                    <?php
                    }
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
    
    function setBank(type, bankId) {
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getBank',
                    bankId: bankId
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

                    <?php
                    if($allowBank) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('bankId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#bankId').find('bankId').each(function(i,e){
                            if($(e).val() == bankId){
                                $('#bankId').prop('selectedIndex',i);
                            }
                        });
                    }
                }
            }
        });
    }
    
    function setGeneralVendor(type, generalVendorId) {
     
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getGeneralVendor',
                    generalVendorId: generalVendorId
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
                        document.getElementById('generalVendorId').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('generalVendorId').options.add(x);
                    }

                    for (i=0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('generalVendorId').options.add(x);
                    }

                    <?php
                    if($allowGeneralVendor) {
                    ?>
//                    if(returnValLength > 0) {
                        var x = document.createElement('option');
                        x.value = 'INSERT';
                        x.text = '-- Insert New --';
                        document.getElementById('generalVendorId').options.add(x);
//                    }                  
                    <?php
                    }
                    ?>
                                        
                    if(type == 1) {
                        $('#generalVendorId').find('option').each(function(i,e){
                            if($(e).val() == generalVendorId){
                                $('#generalVendorId').prop('selectedIndex',i);
                            }
                        });
                    }
                }
            }
        });
    }
    
    function resetGeneralVendorTax() {
        document.getElementById('ppn1').value = '';
        document.getElementById('pph1').value = '';
    }
    
    function getGeneralVendorTax(generalVendorId, amount) {
		
        if(amount != '') {
            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { action: 'getGeneralVendorTax',
                        generalVendorId: generalVendorId,
                        amount: amount
                },
                success: function(data){
                    var returnVal = data.split('|');
                    if(parseInt(returnVal[0])!=0)	//if no errors
                    {
                        document.getElementById('ppn1').value = returnVal[1];
                        document.getElementById('pph1').value = returnVal[2];
                    }
                }
            });
        } else {
            document.getElementById('ppn').value = '0';
            document.getElementById('pph').value = '0';
        }
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
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentDate" name="paymentDate" value="<?php if(isset($_SESSION['payment'])) { echo $_SESSION['payment']['paymentDate']; } else { echo $paymentDate;} ?>" data-date-format="dd/mm/yyyy" class="datepicker" >
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
    <div class="row-fluid" id="loadingPayment" style="display: none;">  
        <div class="span2 lightblue">
            <label>Shipment Code</label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT shipment_id, shipment_no
                    FROM shipment
                    ORDER BY shipment_id DESC", $_SESSION['payment']['shipmentId'], "", "shipmentId", "shipment_id", "shipment_no", 
                    "","","select2combobox100", 1);
            ?>
        </div> 
        <div class="span2 lightblue">
          <label>PO No</label>
        </div>
        <div class="span4 lightblue">
         <?php
            createCombo("SELECT sc.stockpile_contract_id, CONCAT(s.stockpile_code, ' - ',  c.po_no) AS po_no
                    FROM stockpile_contract sc INNER JOIN contract c ON sc.contract_id = c.contract_id
					INNER JOIN stockpile s ON sc.stockpile_id = s.stockpile_id
                    ORDER BY sc.stockpile_contract_id DESC", $_SESSION['payment']['stockpileContractId2'], "", "stockpileContractId2", "stockpile_contract_id", "po_no", 
                    "","","select2combobox100", 1);
            ?>
        </div>
     </div>
    <div class="row-fluid" id="umumPayment" style="display: none;">  
        <div class="span2 lightblue">
            <label>Shipment Code</label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT shipment_id, shipment_no
                    FROM shipment
                    ORDER BY shipment_id DESC", $_SESSION['payment']['shipmentId1'], "", "shipmentId1", "shipment_id", "shipment_no", 
                    "","","select2combobox100", 1);
            ?>
        </div> 
        <div class="span2 lightblue">
          <label>PO No</label>
        </div>
        <div class="span4 lightblue">
         <?php
            createCombo("SELECT sc.stockpile_contract_id, CONCAT(s.stockpile_code, ' - ',  c.po_no) AS po_no
                    FROM stockpile_contract sc INNER JOIN contract c ON sc.contract_id = c.contract_id
					INNER JOIN stockpile s ON sc.stockpile_id = s.stockpile_id
                    ORDER BY sc.stockpile_contract_id DESC", $_SESSION['payment']['stockpileContractId3'], "", "stockpileContractId3", "stockpile_contract_id", "po_no", 
                    "","","select2combobox100", 1);
            ?>
        </div>
    </div>
    <div class="row-fluid" id="qtyPayment" style="display: none;">
        <div class="span2 lightblue">
            <label>Price</label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="price" name="price" value="<?php echo $_SESSION['payment']['price']; ?>">
        </div>
        <div class="span2 lightblue">
            <label>Quantity</label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="qty" name="qty" value="<?php echo $_SESSION['payment']['qty']; ?>">
        </div>
    </div>
    <div class="row-fluid" id="buyerPayment" style="display: none;">  
        <div class="span2 lightblue">
            <label>Buyer <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT DISTINCT(cust.customer_id), cust.customer_name
                    FROM customer cust
                    INNER JOIN sales s
                        ON s.customer_id = cust.customer_id
                    ORDER BY cust.customer_name ASC", $_SESSION['payment']['customerId'], "", "customerId", "customer_id", "customer_name", 
                    "", 7);
            ?>
        </div> 
        <div class="span2 lightblue">
            <label>Shipment Code <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("", "", "", "salesId", "sales_id", "shipment_no", 
                        "", 8, "", 6);
            ?>
        </div>
    </div>
    <div class="row-fluid" id="vendorPayment" style="display: none;">  
        
        <div class="span3 lightblue">
		<label>Stockpile <span style="color: red;">*</span></label>
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId'], "", "stockpileId", "stockpile_id", "stockpile_full", 
                "", 9, "select2combobox100");
            ?>
        </div> 
        
        <div class="span3 lightblue">
		<label>Vendor <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorId", "vendor_id", "vendor_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div>
		
        <div class="span3 lightblue">
		<label>Bank <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "vendorBankId", "v_bank_id", "bank_name", 
                    "", 10, "select2combobox100", 2);
            ?>
        </div> 		
       
        <div class="span3 lightblue">
		<label>PO No. <span style="color: red;">*</span></label>
            <?php
            createCombo("", "", "", "stockpileContractId", "stockpile_contract_id", "po_no", 
                    "", 11, "select2combobox100", 2);
            ?>
        </div>
    </div>
     <div class="row-fluid" id="invoicePayment" style="display: none;">  
        <div class="span2 lightblue">
            <label>General Vendor <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT gv.general_vendor_id, gv.general_vendor_name
                    FROM general_vendor gv WHERE gv.active = 1
                    ORDER BY gv.general_vendor_name ASC", $_SESSION['payment']['gvId'], "", "gvId", "general_vendor_id", "general_vendor_name", 
                "", 9, "select2combobox75");
            ?>
        </div> 
        <div class="span2 lightblue">
            <label>Invoice No. <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("", "", "", "invoiceId", "invoice_id", "invoice_no", 
                    "", 10, "select2combobox100", 1);
            ?>
        </div> 
        <div class="span1 lightblue">
            
        </div>
        <div class="span3 lightblue">
            
        </div>
    </div>
    <div class="row-fluid" id="curahPayment" style="display: none;">  
        <div class="span2 lightblue">
            <label>Periode From<span style="color: red;">*</span></label>
            <br />
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
           <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom1" name="paymentFrom1"  data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId1'], "", "stockpileId1", "stockpile_id", "stockpile_full", 
                "", 12,"select2combobox75");
            ?>
        </div> 
        <div class="span2 lightblue">
          <label>Periode To<span style="color: red;">*</span></label>
            <br />
            <label>Vendor <span style="color: red;">*</span></label>
            
        </div>
        <div class="span4 lightblue">
        <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo1" name="paymentTo1"  data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("", "", "", "vendorId1", "vendor_id", "vendor_name", 
                    "", 13, "select2combobox100", 2);
            ?>
        </div> 
    </div>
	<div class="row-fluid" id="freightDownPayment" style="display: none;">
    	<div class="span2 lightblue">
            
			<label>Stockpile <span style="color: red;">*</span></label>
            
        </div>
        <div class="span4 lightblue">
            
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileIdFcDp'], "", "stockpileIdFcDp", "stockpile_id", "stockpile_full", 
                "", 14, "select2combobox75");
            ?>
			 
        </div>
        <div class="span2 lightblue">
            <label>Supplier <span style="color: red;">*</span></label>
            
        </div>
        <div class="span4 lightblue">
            
            <?php
            createCombo("", "", "", "freightIdFcDp", "freight_id", "freight_supplier", 
                    "", 15, "select2combobox75", 2);
            ?>
			
        </div>    
       </div>
	   <br />
	   <div class="row-fluid" id="freightDownPayment2" style="display: none;">
    	<div class="span1 lightblue">
            
            <label>Quantity<span style="color: red;">*</span></label>
            
        </div>
        <div class="span3 lightblue">
            
			<input type="text" class="span12" tabindex="" id="qtyFreight" name="qtyFreight" value="<?php echo $_SESSION['payment']['qtyFreight']; ?>" />
           
           
        </div>
        <div class="span1 lightblue">
            
			<label>Price<span style="color: red;">*</span></label>
        </div>
        <div class="span3 lightblue">
            
			<input type="text" class="span12" tabindex="" id="priceFreight" name="priceFreight" value="<?php echo $_SESSION['payment']['priceFreight']; ?>" />
        </div>
		<div class="span1 lightblue">
            
			<label>Termin<span style="color: red;">*</span></label>
        </div>
        <div class="span3 lightblue">
            
			<input type="text" class="span12" tabindex="" id="terminFreight" name="terminFreight" value="<?php echo $_SESSION['payment']['terminFreight']; ?>" />
        </div>    		
       </div>
	   <br />
    <div class="row-fluid" id="freightPayment" style="display: none;">
    	<div class="span2 lightblue">
            <label>Periode From<span style="color: red;">*</span></label>
            <br />
            <label>Stockpile <span style="color: red;">*</span></label>
            <br />
            <label>Vendor Freight <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFrom" name="paymentFrom"  data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId2'], "", "stockpileId2", "stockpile_id", "stockpile_full", 
                "", 14, "select2combobox75");
            ?>
            <br />
            <br />
            <?php
            createCombo("", "", "", "vendorFreightId", "vendor_id", "vendor_freight", 
                    "", 15, "select2combobox75", 7, "multiple");
            ?>
        </div>
        <div class="span2 lightblue">
            <label>Periode To<span style="color: red;">*</span></label>
            <br />
            <label>Supplier <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentTo" name="paymentTo" data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("", "", "", "freightId", "freight_id", "freight_supplier", 
                    "", 15, "select2combobox75", 2);
            ?>
        </div>    
       </div>
       <br />
    <div class="row-fluid" id="unloadingPayment" style="display: none;">
        <div class="span2 lightblue">
        	<label>Periode From<span style="color: red;">*</span></label>
            <br />
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
        	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromUP" name="paymentFromUP"  data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId3'], "", "stockpileId3", "stockpile_id", "stockpile_full", 
                "", 16,"select2combobox75");
            ?>
        </div> 
        <div class="span2 lightblue">
           <label>Periode To<span style="color: red;">*</span></label>
            <br />
            <label>Labor <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
        	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToUP" name="paymentToUP" data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("", "", "", "laborId", "labor_id", "labor_name", 
                    "", 17, "", 2);
            ?>
        </div> 
    </div>
	<div class="row-fluid" id="handlingPayment" style="display: none;">
        <div class="span2 lightblue">
        	<label>Periode From<span style="color: red;">*</span></label>
            <br />
            <label>Stockpile <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
        	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentFromHP" name="paymentFromHP"  data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("SELECT s.stockpile_id, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) AS stockpile_full
                    FROM stockpile s
                    ORDER BY s.stockpile_code ASC, s.stockpile_name ASC", $_SESSION['payment']['stockpileId4'], "", "stockpileId4", "stockpile_id", "stockpile_full", 
                "", 16,"select2combobox75");
            ?>
        </div> 
        <div class="span2 lightblue">
           <label>Periode To<span style="color: red;">*</span></label>
            <br />
            <label>Vendor Handling <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
        	<input type="text" placeholder="DD/MM/YYYY" tabindex="" id="paymentToHP" name="paymentToHP" data-date-format="dd/mm/yyyy" class="datepicker" >
            <?php
            createCombo("", "", "", "vendorHandlingId", "vendor_handling_id", "vendor_handling", 
                    "", 15, "select2combobox75", 2);
            ?>
        </div> 
    </div>
    <div class="row-fluid" id="slipPayment" style="display: none;">
        slip
    </div>
    <div class="row-fluid" id="summaryPayment" style="display: none;">
        summary
    </div>
    <div class="row-fluid">
      
        <div class="span2 lightblue">
            <label>From/To <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="hidden" name="currencyId" id="currencyId" value="<?php echo $_SESSION['payment']['currencyId']; ?>" />
            <input type="hidden" name="journalCurrencyId" id="journalCurrencyId" value="1" />
            <input type="hidden" name="bankCurrencyId" id="bankCurrencyId" value="<?php echo $_SESSION['payment']['bankCurrencyId']; ?>" />
            <?php
//            createCombo("SELECT cur.*
//                    FROM currency cur
//                    ORDER BY cur.currency_code ASC", $currencyId, "", "currencyId", "currency_id", "currency_code", 
//                    "", 5, "");
            ?>
            <?php
            createCombo("SELECT b.bank_id, CONCAT(b.bank_name, ' ', cur.currency_Code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full 
                    FROM bank b
                    INNER JOIN currency cur
                        ON cur.currency_id = b.currency_id
                    ORDER BY b.bank_name ASC, cur.currency_code ASC, b.bank_account_name", $_SESSION['payment']['bankId'], "", "bankId", "bank_id", "bank_full", 
                "", 18, "select2combobox100", 1, "", $allowBank);
            ?>
        </div>
        <div class="span2 lightblue" id="labelExchangeRate" style="display: none;">
            <label>Exchg Rate USD to IDR <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue" id="inputExchangeRate" style="display: none;">
            <input type="text" class="span12" tabindex="" id="exchangeRate" name="exchangeRate" value="<?php echo $_SESSION['payment']['exchangeRate']; ?>" />
        </div>
    </div>
    <div class="row-fluid" id="divAmount">   
        <div class="span2 lightblue">
            <label>Amount <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="amount" name="amount" value="<?php echo $_SESSION['payment']['amount']; ?>">
        </div>
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid" id="divPaymentTo" style="display: none;">   
        <div class="span2 lightblue">
            <label>Payment From/To <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <?php
            createCombo("SELECT * FROM general_vendor WHERE active = 1
                    ORDER BY general_vendor_name ASC", $_SESSION['payment']['generalVendorId'], "", "generalVendorId", "general_vendor_id", "general_vendor_name", 
                    "", 21, "select2combobox100", 1, "", $allowGeneralVendor);
            ?>
        </div> 
<!--        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="23" id="paymentNotes" name="paymentNotes">
        </div>-->
        <div class="span2 lightblue">
        </div>
        <div class="span4 lightblue">
        </div>
    </div>
    <div class="row-fluid" id="divTax" style="display: none;">   
        <div class="span2 lightblue">
            <label>PPN <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="ppn1" name="ppn1" value="<?php echo $_SESSION['payment']['ppn1']; ?>">
        </div>
        <div class="span2 lightblue">
            <label>PPh <span style="color: red;">*</span></label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="pph1" name="pph1" value="<?php echo $_SESSION['payment']['pph1']; ?>">
        </div>
    </div>
    <div class="row-fluid" id="divTaxPKS" style="display: none;">   
        <div class="span2 lightblue">
            <label>PPN</label>
        </div>
        <div class="span4 lightblue">
            <input type="text" class="span12" tabindex="" id="ppn2" name="ppn2" value="<?php echo $_SESSION['payment']['ppn2']; ?>">
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
            <input type="text" class="span12" tabindex="" id="taxInvoice" name="taxInvoice" value="<?php echo $_SESSION['payment']['taxInvoice']; ?>">
        </div>
        <div class="span1 lightblue">
            <label>Invoice No</label>
        </div>
        <div class="span3 lightblue">
            <input type="text" class="span12" tabindex="" id="invoiceNo" name="invoiceNo" value="<?php echo $_SESSION['payment']['invoiceNo']; ?>">
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
            <input type="text" class="span12" tabindex="" id="chequeNo" name="chequeNo" value="<?php echo $_SESSION['payment']['chequeNo']; ?>">
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
            <textarea class="span12" rows="3" tabindex="" id="remarks" name="remarks"><?php echo $_SESSION['payment']['remarks']; ?></textarea>
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