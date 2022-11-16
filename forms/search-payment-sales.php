<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';


$boolBack = true;
$boolInsert = false;

if(isset($_POST['direct']) && $_POST['direct'] == 1) {
    $boolBack = false;
    $boolInsert = true;
}


$sql = "SELECT p.*, DATE_FORMAT(p.payment_date, '%d %b %Y') AS payment_date2, pcur.currency_code AS pcur_currency_code,
			DATE_FORMAT(p.payment_date, '%d/%m/%Y') AS payment_date3,
			CASE WHEN p.invoice_id IS NOT NULL THEN DATE_FORMAT(i.invoice_date, '%d %b %Y')
			WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT due_date_inv FROM invoice_sales WHERE inv_notim_id = p.invoice_sales_id)
			WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT due_date_inv FROM invoice_sales_oa WHERE inv_notim_id = p.invoice_sales_oa_id)
			ELSE DATE_FORMAT(p.invoice_date, '%d %b %Y') END AS invoice_date,
            DATE_FORMAT(p.entry_date, '%d %b %Y %H:%i:%s') AS entry_date2, u.user_name, sp.stockpile_name,
            CONCAT(b.bank_name, ' ', bcur.currency_code, ' - ', b.bank_account_no, ' - ', b.bank_account_name) AS bank_full,
			CASE WHEN p.invoice_id IS NOT NULL THEN CONCAT(i.invoice_no, ' - ', i.invoice_no2)
			WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT inv_notim_no FROM invoice_sales WHERE inv_notim_id = p.invoice_sales_id)
			WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT inv_notim_no FROM invoice_sales_oa WHERE inv_notim_id = p.invoice_sales_oa_id)
			ELSE p.invoice_no END AS invoice_no,
			CASE WHEN p.invoice_id IS NOT NULL THEN i.invoice_tax
			ELSE p.tax_invoice END AS tax_invoice,
            CASE WHEN p.stockpile_contract_id IS NOT NULL THEN vcon.vendor_name
            WHEN p.vendor_id IS NOT NULL THEN v.vendor_name
            WHEN p.sales_id IS NOT NULL THEN cust.customer_name
            WHEN p.freight_id IS NOT NULL THEN f.freight_supplier
            WHEN p.freight_sales_id IS NOT NULL THEN fs.freight_supplier
	    WHEN p.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name
            WHEN p.labor_id IS NOT NULL THEN l.labor_name
            WHEN p.general_vendor_id IS NOT NULL THEN gv.general_vendor_name
	WHEN p.invoice_id IS NOT NULL THEN (SELECT DISTINCT(gv.general_vendor_name) FROM general_vendor gv
LEFT JOIN invoice_detail id ON id.`general_vendor_id` = gv.`general_vendor_id`
LEFT JOIN invoice i ON i.invoice_id = id.`invoice_id` WHERE i.invoice_id = p.invoice_id ORDER BY i.invoice_id DESC LIMIT 1)
            WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.customer_name FROM customer a LEFT JOIN invoice_sales b ON a.customer_id = b.customer_id WHERE b.inv_notim_id = p.invoice_sales_id)
            WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.freight_supplier FROM freight_local_sales a LEFT JOIN invoice_sales_oa b ON a.freight_id = b.freightId WHERE b.inv_notim_id = p.invoice_sales_oa_id)
            ELSE (SELECT vendor_name FROM vendor_pettycash WHERE account_no = a.account_no LIMIT 1)
            END AS vendor_name,
          CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.vendor_id IS NOT NULL THEN (SELECT bank_name FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.sales_id IS NOT NULL THEN cust.bank_name
            WHEN p.freight_id IS NOT NULL THEN (SELECT bank_name FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
            WHEN p.freight_sales_id IS NOT NULL THEN (SELECT bank_name FROM freight_local_sales_bank WHERE f_bank_id = p.vendor_bank_id)
			WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT bank_name FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
            WHEN p.labor_id IS NOT NULL THEN (SELECT bank_name FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
            WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.bank_name FROM customer_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.cust_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_id)
            WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.bank_name FROM freight_local_sales_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.f_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_oa_id) 
            WHEN p.general_vendor_id IS NOT NULL THEN gv.bank_name
		WHEN p.invoice_id IS NOT NULL THEN (SELECT bank_name FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
            ELSE (SELECT bank FROM vendor_pettycash WHERE account_no = a.account_no LIMIT 1) END AS bank_name,
		CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.vendor_id IS NOT NULL THEN (SELECT branch FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.sales_id IS NOT NULL THEN cust.branch
            WHEN p.freight_id IS NOT NULL THEN (SELECT branch FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
            WHEN p.freight_sales_id IS NOT NULL THEN (SELECT branch FROM freight_local_sales_bank WHERE f_bank_id = p.vendor_bank_id)

			WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT branch FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
            WHEN p.labor_id IS NOT NULL THEN (SELECT branch FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
            WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.branch FROM customer_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.cust_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_id)
	WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.bank_name FROM freight_local_sales_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.f_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_oa_id) 
            WHEN p.general_vendor_id IS NOT NULL THEN gv.branch
	WHEN p.invoice_id IS NOT NULL THEN (SELECT branch FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
            ELSE (SELECT branch FROM vendor_pettycash WHERE account_no = a.account_no LIMIT 1) END AS branch,
            CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT account_no FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.vendor_id IS NOT NULL THEN (SELECT account_no FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.sales_id IS NOT NULL THEN cust.account_no
            WHEN p.freight_id IS NOT NULL THEN (SELECT account_no FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
            WHEN p.freight_sales_id IS NOT NULL THEN (SELECT account_no FROM freight_local_sales_bank WHERE f_bank_id = p.vendor_bank_id)
			WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT account_no FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
            WHEN p.labor_id IS NOT NULL THEN (SELECT account_no FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
            WHEN p.general_vendor_id IS NOT NULL THEN gv.account_no
			WHEN p.invoice_id IS NOT NULL THEN (SELECT account_no FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
		WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.account_no FROM customer_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.cust_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_id)
		WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.account_no FROM freight_local_sales_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.f_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_oa_id) 
            ELSE (SELECT TRIM(REPLACE(REPLACE(REPLACE(no_rek,'-',''),'.',''),' ','')) FROM vendor_pettycash WHERE account_no = a.account_no LIMIT 1) END AS account_no,
            CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.vendor_id IS NOT NULL THEN (SELECT beneficiary FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.sales_id IS NOT NULL THEN cust.beneficiary
            WHEN p.freight_id IS NOT NULL THEN (SELECT beneficiary FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
            WHEN p.freight_sales_id IS NOT NULL THEN (SELECT beneficiary FROM freight_local_sales_bank WHERE f_bank_id = p.vendor_bank_id)
			WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT beneficiary FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
            WHEN p.labor_id IS NOT NULL THEN (SELECT beneficiary FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
            WHEN p.general_vendor_id IS NOT NULL THEN gv.beneficiary
			WHEN p.invoice_id IS NOT NULL THEN (SELECT beneficiary FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
		WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.beneficiary FROM freight_local_sales_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.f_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_oa_id) 
		WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.beneficiary FROM customer_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.cust_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_id)
            ELSE (SELECT beneficiary FROM vendor_pettycash WHERE account_no = a.account_no LIMIT 1) END AS beneficiary,
            CASE WHEN p.stockpile_contract_id IS NOT NULL THEN (SELECT swift_code FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.vendor_id IS NOT NULL THEN (SELECT swift_code FROM vendor_bank WHERE v_bank_id = p.vendor_bank_id)
            WHEN p.sales_id IS NOT NULL THEN cust.swift_code
            WHEN p.freight_id IS NOT NULL THEN (SELECT swift_code FROM freight_bank WHERE f_bank_id = p.vendor_bank_id)
            WHEN p.freight_sales_id IS NOT NULL THEN (SELECT swift_code FROM freight_local_sales_bank WHERE f_bank_id = p.vendor_bank_id)
			WHEN p.vendor_handling_id IS NOT NULL THEN (SELECT swift_code FROM vendor_handling_bank WHERE vh_bank_id = p.vendor_bank_id)
            WHEN p.labor_id IS NOT NULL THEN (SELECT swift_code FROM labor_bank WHERE l_bank_id = p.vendor_bank_id)
            WHEN p.general_vendor_id IS NOT NULL THEN gv.swift_code
			WHEN p.invoice_id IS NOT NULL THEN (SELECT swift_code FROM general_vendor_bank WHERE gv_bank_id = p.vendor_bank_id)
			WHEN p.invoice_sales_id IS NOT NULL THEN (SELECT a.swift_code FROM customer_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.cust_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_id)
		WHEN p.invoice_sales_oa_id IS NOT NULL THEN (SELECT a.swift_code FROM freight_local_sales_bank a LEFT JOIN pengajuan_payment_sales b ON b.vendor_bank_id = a.f_bank_id LEFT JOIN invoice_sales c ON b.idPP = c.idPP WHERE c.inv_notim_id = p.invoice_sales_oa_id) 
            ELSE '' END AS swift_code,
           
            b.bank_code, b.bank_type,

			CASE WHEN p.payment_location = 0 THEN 'HOF'
            ELSE s.stockpile_code END AS payment_location2,

			 			 CASE WHEN p.stockpile_contract_id_2 IS NOT NULL THEN (SELECT CONCAT(s.stockpile_code, ' - ',  c.po_no) AS po_no FROM stockpile_contract sc LEFT JOIN contract c ON sc.contract_id = c.contract_id
LEFT JOIN stockpile s ON sc.stockpile_id = s.stockpile_id WHERE sc.stockpile_contract_id = p.stockpile_contract_id_2)

            ELSE ' - ' END AS po_no_2,

            CASE WHEN p.shipment_id IS NOT NULL THEN (SELECT sh.shipment_no FROM shipment sh WHERE sh.shipment_id = p.shipment_id)
			WHEN p.invoice_id IS NOT NULL THEN (SELECT sh.shipment_no FROM shipment sh LEFT JOIN invoice i ON i.shipmentId = sh.shipment_id WHERE i.invoice_id = p.invoice_id)
            ELSE ' - ' END AS shipment_no,

			CASE WHEN a.account_type = 0 THEN 'PKS/Curah/Sales'
            WHEN a.account_type = 1 THEN 'PKS/Curah/Sales'
            WHEN a.account_type = 2 THEN 'Freight Cost'
            WHEN a.account_type = 3 THEN 'Unloading Cost'
            WHEN a.account_type = 4 THEN 'Loading'
            WHEN a.account_type = 5 THEN 'Umum'
            WHEN a.account_type = 6 THEN 'HO'
            WHEN a.account_type = 7 THEN 'Internal Transfer'
			WHEN a.account_type = 8 THEN 'Invoice'
			WHEN a.account_type = 9 THEN 'Handling Cost'
            ELSE '' END AS account_type2,

			CASE WHEN p.payment_type = 1 THEN 'IN'
            WHEN p.payment_type = 2 THEN 'OUT'
            ELSE '' END AS payment_type2,

			CASE WHEN p.payment_type2 = 1 THEN 'TT'
				WHEN p.payment_type2 = 2 THEN 'Cek/Giro'
				WHEN p.payment_type2 = 3 THEN 'Tunai'
				WHEN p.payment_type2 = 4 THEN 'Bill Payment'
				WHEN p.payment_type2 = 5 THEN 'Auto Debet'
			ELSE 'TT' END AS p_type,

			CASE WHEN p.payment_method = 1 AND p.invoice_id IS NOT NULL THEN (SELECT CASE WHEN invoice_method = 1 THEN 'Payment' ELSE 'Down Payment' END FROM invoice WHERE invoice_id = i.invoice_id)
			WHEN p.payment_method = 1 THEN 'Payment'
			ELSE 'Down Payment' END AS pMethod,

			p.qty, p.price, a.account_name, a.account_type, gv.pph_tax_id,
			CASE WHEN p.labor_id IS NOT NULL AND p.payment_method = 1 THEN p.amount_converted - p.original_amount_converted ELSE 0 END AS dpLabor,con.contract_no

        FROM payment p
        LEFT JOIN account a
            ON a.account_id = p.account_id
        LEFT JOIN bank b
            ON b.bank_id = p.bank_id
        LEFT JOIN currency bcur
            ON bcur.currency_id = b.currency_id
        LEFT JOIN currency pcur
            ON pcur.currency_id = p.currency_id
        LEFT JOIN USER u
            ON u.user_id = p.entry_by
        LEFT JOIN stockpile_contract sc
            ON sc.stockpile_contract_id = p.stockpile_contract_id
        LEFT JOIN contract con
            ON con.contract_id = sc.contract_id
        LEFT JOIN vendor vcon
            ON vcon.vendor_id = con.vendor_id
        LEFT JOIN vendor v
            ON v.vendor_id = p.vendor_id
        LEFT JOIN sales sl
            ON sl.sales_id = p.sales_id
        LEFT JOIN customer cust
            ON cust.customer_id = sl.customer_id
        LEFT JOIN freight f
            ON f.freight_id = p.freight_id
        LEFT JOIN labor l
            ON l.labor_id = p.labor_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = p.general_vendor_id
        LEFT JOIN stockpile s
            ON s.stockpile_id = p.payment_location
	LEFT JOIN stockpile sp
            ON sp.stockpile_id = p.stockpile_location
	LEFT JOIN invoice i
	    ON i.invoice_id = p.invoice_id
	LEFT JOIN vendor_handling vh
	    ON vh.vendor_handling_id = p.vendor_handling_id
	LEFT JOIN freight_local_sales fs
            ON fs.freight_id = p.freight_sales_id
        WHERE 1=1
        AND p.payment_id = {$_POST['paymentId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if($result !== false && $result->num_rows == 1) {
    $row = $result->fetch_object();
?>


<script type="text/javascript">

    $(document).ready(function(){	//executed after the page has loaded
        $('#printPayment').click(function(e){
            e.preventDefault();

            //$("#transactionContainer").show();
            // https://github.com/jasonday/printThis
            $("#paymentContainer").printThis();
//            $("#transactionContainer").hide();
        });

        $('#insertPayment').click(function(e){
            e.preventDefault();

            $('#pageContent').load('views/payment.php', {}, iAmACallbackFunction);
        });


        /*$('#returnPayment').click(function(e){
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this payment?", function(e) {
                    if (e) {
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=return_payment&paymentId=<?php echo $row->payment_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/search-payment.php', {paymentId: <?php echo $row->payment_id; ?>}, iAmACallbackFunction2);

                        }
                    }
                }
            });
		}
                    return false;
		});

     });*/
	 
	 $("#returnPayment").validate({
			rules: {
                returnPaymentDate: "required"
            },
            messages: {
                returnPaymentDate: "Return Date is a required field."
            },
			submitHandler: function(form) {
				$('#returnButton').attr("disabled", true);
			alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to RETURN this Payment?", function(form) {
                    if (form) {

            $.blockUI({ message: '<h4>Please wait...</h4>' });
			$('#loading').css('visibility','visible');
            
			$.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#returnPayment").serialize(),
                    success: function(data) {
                        var returnVal = data.split('|');

                        if (parseInt(returnVal[4]) != 0)	//if no errors
                        {
                            alertify.set({ labels: {
                                ok     : "OK"
                            } });
                            alertify.alert(returnVal[2]);
                            
                            if (returnVal[1] == 'OK') {
                                document.getElementById('paymentId').value = returnVal[3];
                                
                                $('#dataContent').load('forms/search-payment-sales.php', { paymentId: returnVal[3] }, iAmACallbackFunction2);

//                                document.getElementById('successMsg').innerHTML = returnVal[2];
//                                $("#successMsg").show();
                            } 
							$('#returnButton').attr("disabled", false);
                        }
                    }
                });

                $('#loading').css('visibility','hidden');
			}
                    return false;
		});
		}
        });


	$('#jurnalPayment').click(function(e){
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: 'action=jurnal_payment&paymentId=<?php echo $row->payment_id; ?>',
                success: function(data) {
                    var returnVal = data.split('|');

                    if (parseInt(returnVal[4]) != 0)	//if no errors
                    {
                        alertify.set({ labels: {
                            ok     : "OK"
                        } });
                        alertify.alert(returnVal[2]);

                        if (returnVal[1] == 'OK') {
                            $('#dataContent').load('forms/search-payment.php', {paymentId: <?php echo $row->payment_id; ?>}, iAmACallbackFunction2);

                        }
                    }
                }
            });
        });
    });

    function back() {
        $.blockUI({ message: '<h4>Please wait...</h4>' });
        $('#pageContent').load('views/payment-notim-sales.php', {}, iAmACallbackFunction);
    }
	
	$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
    });
</script>

<div id="paymentContainer">
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td colspan="6" style="text-align: left; font-size: 12pt; font-weight: 600;">
                PT. JATIM PROPERTINDO JAYA
            </td>
        </tr>

        <?php
          if($row->bank_type == 2) {
            echo '<tr><td colspan="6" style="text-align: center; font-size: 12pt; font-weight: 600;">PETTY CASH PAYMENT VOUCHER</td></tr>';
        }else{
			echo '<tr><td colspan="6" style="text-align: center; font-size: 12pt; font-weight: 600;">BANK PAYMENT VOUCHER</td></tr>';
		}
		?>
        <tr>
            <td colspan="6" style="text-align: center; font-size: 12pt;  font-weight: 600;">
                <?php

                $voucherCode = $row->payment_location2 .'/'. $row->bank_code .'/'. $row->pcur_currency_code;

                if($row->bank_type == 1) {
                    $voucherCode .= ' - B';
                } elseif($row->bank_type == 2) {
                    $voucherCode .= ' - P';
                } elseif($row->bank_type == 3) {
                    $voucherCode .= ' - CAS';
                }

                if($row->bank_type != 3) {
                    if($row->payment_type == 1) {
                        $voucherCode .= 'RV';
                    } else {
                        $voucherCode .= 'PV';
                    }
                }
                ?>
                <?php echo $voucherCode; ?> # <?php echo $row->payment_no; ?>
            </td>
        </tr>
    </table>
    <br/>
    <?php
//    if($row->payment_type == 2) {
    ?>
    <table width="100%" style="table-layout:fixed; font-size: 9pt;">
        <tr>
            <td width="20%"><b>Supplier</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->vendor_name; ?></td>
            <td width="20%"><b>Bank</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->bank_full; ?></td>
        </tr>
        <tr>
            <td width="20%"><b>Tanggal Invoice</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->invoice_date; ?></td>
            <!--<td width="20%"><b>Bank Opening</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php //echo number_format($row->bank_opening, 0, ".", ","); ?></td>-->
        </tr>
        <tr>
            <td width="20%"><b>No. Invoice/Kwitansi</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->invoice_no; ?></td>
            <!--<td width="20%"><b>Bank Ending</b></td>
            <td width="2%">:</td>
            <td width="28%">
                <?php
                /*if($row->payment_type == 2) {
                    echo number_format($row->bank_opening - $row->original_amount, 0, ".", ",");
                } else {
                    echo number_format($row->bank_opening + $row->original_amount, 0, ".", ",");
                }*/
                ?>
            </td>-->
        </tr>
        <tr>
        <td width="20%"><b>Tax Invoice</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->tax_invoice; ?></td>
            <td width="20%"><b>Cara Pembayaran</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->p_type; ?></td>
         </tr>
        <tr>

            <td width="20%"><b>Cheque No</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->cheque_no; ?></td>
            <td width="20%"><b>Tanggal</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->payment_date2; ?></td>

        </tr>
        <tr>
        	<?php if($row->freight_id != '' || $row->freight_sales_id != '' || $row->labor_id != '' || $row->vendor_handling_id != ''){

				if($row->payment_method == 2){
							$total = ($row->original_amount - $row->pph_journal) + $row->ppn_journal;
						}else{
							$total = $row->original_amount;
						}
			    //}else if($row->ppn_amount_converted > 0 && $row->pph_amount_converted > 0 ) {
                //$total = ($row->original_amount + $row->ppn_amount_converted)- $row->pph_amount_converted;
			    //}else if($row->ppn_amount_converted > 0) {
                //$total = $row->original_amount + $row->ppn_amount_converted;
				//}else if($row->pph_amount_converted > 0) {
                //$total = $row->original_amount - $row->pph_amount_converted;
		        } elseif($row->general_vendor_id != '' && $row->pph_tax_id == 21){
				   $total =	($row->original_amount + $row->ppn_amount)+ $row->pph_amount;
				}
				elseif($row->general_vendor_id != ''){
				   $total =	($row->original_amount + $row->ppn_amount)- $row->pph_amount;
				}else{
			    $total = $row->original_amount;
				}

				 ?>
            <td width="20%"><b>Stockpile Location</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->stockpile_name; ?></td>
            <td width="20%"><b>Kurs</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo 'IDR' . ' ' . number_format($row->exchange_rate, 2, ".", ","); ?></td>

        </tr>
        <tr>

            <td width="20%"><b>Type</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->pMethod .' - '. $row->payment_type2 .' - '. $row->account_type2; ?></td>
			<td width="20%"><b>Jumlah</b></td>
            <td width="2%">:</td>
            <td width="28%"><?php echo $row->pcur_currency_code . ' ' . number_format($total, 2, ".", ","); ?></td>
        </tr>

        <?php
        if($row->payment_status == 1) {
            echo '<tr><td colspan="6" style="font-size: 14pt; font_weight: bold; color: red; text-align: center;">Returned</td></tr>';
        }
        ?>
    </table>
    <br/>
	<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <tr>
    <td width="10%"><b>Remarks</b></td>
    <td><?php if($row->vendor_id != '' || $row->freight_id != '' || $row->freight_sales_id != '' || $row->vendor_handling_id != '' || $row->labor_id != '' || $row->sales_id != '' || $row->invoice_id != ''){
					echo $row->remarks;
				}elseif ($row->stockpile_contract_id != ''){
					echo '(Contract No : ' . $row->contract_no . ') - ' .$row->remarks; 
				}else{
					echo $row->remarks;
				}
				?>
     </td>
     </tr>
     </table>
    <table class="table table-bordered table-striped" style="font-size: 9pt;">
        <thead>
            <tr>

                <th>Reference Code</th>
                <th>PO NO</th>
                <th>No. Slip</th>
                <th>Quantity</th>
                <th>Harga</th>
				<th>Termin</th>
				<?php if($row->vendor_id != '' || $row->freight_id != '' || $row->freight_sales_id != '' || $row->vendor_handling_id != '' || $row->labor_id != '') {
                echo '<th>Amount</th>';
				echo '<th>PPN</th>';
				echo '<th>PPh</th>';
				echo '<th>DPP</th>';
				echo '<th>Shrink Qty Claim</th>';
				echo '<th>Shrink Price Claim</th>';
				echo '<th>Shrink Amount</th>';
				}else{

				echo '<th colspan="4">Keterangan</th>';
				echo '<th>DPP</th>';
				echo '<th>PPN</th>';
				echo '<th>PPh</th>';
				}?>
                <th>Nilai Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $downPayment = 0;
            if($row->stockpile_contract_id != '') {
                $sql = "SELECT '' AS slip_no,  con.price, con.po_no, con.contract_no,
							CASE WHEN p.payment_type = 1 THEN (SELECT SUM(`adjustment`) FROM contract_adjustment WHERE contract_id = con.contract_id)
							ELSE con.quantity END AS quantity,
                            CASE WHEN p.payment_method = 1 AND p.payment_type = 2 THEN con.quantity * con.price
                            WHEN p.payment_method = 2  AND p.payment_type = 2 THEN p.original_amount - ppn_amount
                            WHEN p.payment_method = 1 AND p.payment_type = 1 THEN (SELECT SUM(`adjustment`) FROM contract_adjustment WHERE contract_id = con.contract_id) * con.price
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('PKS Kontrak - ', con.contract_no) AS keterangan,
							 p.ppn_amount AS ppnDetail, p.pph_amount AS pphDetail
                        FROM payment p
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = p.stockpile_contract_id
                        LEFT JOIN contract con
                            ON con.contract_id = sc.contract_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.stockpile_contract_id = {$row->stockpile_contract_id}";
				if($row->payment_method == 1 && $row->payment_type == 2){
                $sqlDP = "SELECT COALESCE(SUM(p.original_amount), 0) AS total_dp FROM payment p
                        WHERE p.payment_method = 2 AND p.payment_status = 0
                        AND p.stockpile_contract_id = {$row->stockpile_contract_id}";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    $downPayment = $rowDP->total_dp;
                }
				}
            } elseif($row->invoice_id != '') {
				//$downPayment = 0;
				if($row->payment_method == 1){
                $sql = "SELECT i.invoice_no, id.invoice_detail_id, id.invoice_id, c.po_no,
CASE WHEN id.type = 4 THEN 'Loading'
WHEN id.type = 5 THEN 'Umum'
WHEN id.type = 6 THEN 'HO' ELSE '' END AS TYPE, a.account_name,  s.stockpile_name, id.notes,
CASE WHEN id.mutasi_detail_id IS NOT NULL THEN (SELECT a.kode_mutasi FROM mutasi_header a LEFT JOIN mutasi_detail b ON a.mutasi_header_id = b.mutasi_header_id WHERE b.mutasi_detail_id = id.mutasi_detail_id)
ELSE sh.shipment_no END AS shipment_no,
id.qty, id.price, id.termin, id.amount, id.ppn AS ppnDetail, id.pph AS pphDetail, id.tamount, gv.general_vendor_name,
CASE WHEN id.invoice_detail_id IS NOT NULL THEN (SELECT GROUP_CONCAT(invoice_id) FROM invoice_detail WHERE invoice_detail_dp = id.invoice_detail_id ) ELSE 0 END AS iddp,
CASE WHEN idUOM IS NOT NULL THEN (SELECT uom_type FROM uom WHERE idUOM = id.idUOM) ELSE '-' END AS uom
FROM invoice_detail id LEFT JOIN account a ON id.account_id = a.account_id
LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
LEFT JOIN invoice i ON i.invoice_id = id.invoice_id
LEFT JOIN payment p ON p.invoice_id = i.invoice_id
LEFT JOIN contract c ON c.contract_id = id.poId

                      WHERE 1=1

                        AND p.payment_id = {$row->payment_id}
                        AND p.invoice_id = {$row->invoice_id}";

				$sql2 = "SELECT id.invoice_detail_id AS invoice_id,
						CASE WHEN id.invoice_detail_id IS NOT NULL THEN (SELECT GROUP_CONCAT(invoice_detail_id)FROM invoice_dp WHERE invoice_detail_id = id.invoice_detail_id)
						ELSE 0 END AS invoiceDP, gv.ppn AS gv_ppn, gv.pph AS gv_pph
						FROM invoice_detail id
						LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
						LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id` WHERE i.`invoice_id` = {$row->invoice_id} ORDER BY invoiceDP DESC";

			    $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
				$iddp = array();
				while($row2 = mysqli_fetch_array($result2)){

                    //$row2= $result2->fetch_object();
					$ppn_dp = $row2['gv_ppn'];
					$pph_dp = $row2['gv_pph'];
                    $invoice_detail_id[] = $row2['invoice_id'];

                }
				$iddps =  implode(', ', $invoice_detail_id);

			if($row->payment_date >= '2018-08-20' ){
                $sqlDP = "SELECT SUM(idp.amount_payment) AS down_payment,
SUM(CASE WHEN id.`ppn` != 0 THEN idp.amount_payment * (ppn.`tax_value`/100) ELSE 0 END) AS ppn,
SUM(CASE WHEN id.pph != 0 THEN idp.amount_payment * (pph.`tax_value`/100) ELSE 0 END) AS pph
FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.`invoice_detail_dp`
LEFT JOIN tax ppn ON ppn.`tax_id` = id.`ppnID`
LEFT JOIN tax pph ON pph.`tax_id` = id.`pphID`
WHERE idp.status = 0 AND idp.invoice_detail_id IN ({$iddps}) ";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
					if($rowDP->ppn == 0){
					$dpPPN = 0;
					}else{
					//$dpPPN = $rowDP->down_payment * ($ppn_dp/100);
					$dpPPN = $rowDP->ppn ;
					}
					if($rowDP->pph == 0){
					$dpPPh = 0;
					}else{
					//$dpPPh = $rowDP->down_payment * ($pph_dp/100);
					$dpPPh = $rowDP->pph ;
					}
					//$dpPPh = $rowDP->down_payment * ($pph_dp/100);
                    $downPayment1 = ($rowDP->down_payment + $dpPPN) - $dpPPh;
                }
			}else{
				$sqlDP = "SELECT COALESCE(SUM(id.tamount), 0) AS down_payment FROM invoice_detail id
LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id`
WHERE id.invoice_detail_dp IN ({$iddps}) AND id.invoice_method_detail = 2 AND id.invoice_detail_status = 1";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    $downPayment1 = $rowDP->down_payment;
                }
			}
				$sqlDpPayment = "SELECT COALESCE(SUM(p.original_amount_converted), 0) AS dp FROM payment p WHERE p.invoice_id = {$row->invoice_id} AND p.payment_method = 2 AND p.payment_status = 0";
                $resultDpPayment = $myDatabase->query($sqlDpPayment, MYSQLI_STORE_RESULT);
                if($resultDpPayment !== false) {
                    $rowDpPayment = $resultDpPayment->fetch_object();
                    $downPayment2 = $rowDpPayment->dp;
                }
			//echo $downPayment1;

				$downPayment = $downPayment1 + $downPayment2;

			}

            }elseif($row->vendor_id != '') {
                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.quantity AS quantity, t.unit_price,
                            CASE WHEN p.payment_method = 1 THEN
                            CASE WHEN p.payment_status = 0 THEN (t.quantity * t.unit_price)
                            WHEN p.payment_status = 1 AND p.ppn_amount > 0 THEN  p.original_amount - p.ppn_amount ELSE p.original_amount END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('PKS Curah - ', con.po_no) AS keterangan,
                            v.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            v.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
							t.unit_price AS price
                        FROM payment p
                        LEFT JOIN transaction t
                            ON t.payment_id = p.payment_id
                        LEFT JOIN stockpile_contract sc
                            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        LEFT JOIN contract con
                            ON con.contract_id = sc.contract_id
                        LEFT JOIN vendor v
                            ON v.vendor_id = p.vendor_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = v.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = v.pph_tax_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.vendor_id = {$row->vendor_id}";
						
				if($row->payment_method == 1){
                $sqlDP = "SELECT COALESCE(SUM(p.original_amount), 0) AS total_dp FROM payment p
                        WHERE p.payment_method = 2 AND p.payment_status = 0
                         AND p.vendor_id = {$row->vendor_id}";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    $downPayment = $rowDP->total_dp;
                }
				}
            } elseif($row->invoice_sales_id != '') {
                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.quantity AS quantity, a.price AS unit_price,
                            CASE WHEN p.payment_method = 1 THEN
                            CASE WHEN p.payment_status = 0 THEN (t.quantity * a.price)
                            WHEN p.payment_status = 1 AND p.ppn_amount > 0 THEN  p.original_amount - p.ppn_amount ELSE p.original_amount END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            c.shipment_no AS keterangan,
                            e.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            e.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
							a.price, c.shipment_no,
							((t.quantity * a.price)* (COALESCE(txpph.tax_value,0)/100)) AS pphDetail,
							((t.quantity * a.price)* (COALESCE(txppn.tax_value,0)/100)) AS ppnDetail
                        FROM payment p
                        LEFT JOIN pengajuan_sales a ON a.inv_notim_id = p.invoice_sales_id
			LEFT JOIN invoice_sales d ON d.inv_notim_id = p.invoice_sales_id
                        LEFT JOIN TRANSACTION t
                            ON t.transaction_id = a.transaction_id
                        LEFT JOIN shipment c ON c.shipment_id = t.shipment_id
                        LEFT JOIN customer e ON e.customer_id = d.customer_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = e.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = e.pph_tax_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.invoice_sales_id = {$row->invoice_sales_id}";
						
				/*if($row->payment_method == 1){
                $sqlDP = "SELECT COALESCE(SUM(p.original_amount), 0) AS total_dp FROM payment p
                        WHERE p.payment_method = 2 AND p.payment_status = 0
                         AND p.vendor_id = {$row->vendor_id}";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    $downPayment = $rowDP->total_dp;
                }
				}*/
            }elseif($row->invoice_sales_oa_id != '') {
                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.freight_quantity AS quantity, t.freight_price,
                            CASE WHEN p.payment_method = 1 THEN
                            CASE WHEN p.payment_status = 0 THEN (t.freight_quantity * t.freight_price)
                            WHEN p.payment_status = 1 AND p.ppn_amount > 0 THEN  p.original_amount - p.ppn_amount ELSE p.original_amount END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            c.shipment_no AS keterangan,
                            e.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            e.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
							t.freight_price AS price, c.shipment_no,
			((t.freight_quantity * t.freight_price)* (COALESCE(txpph.tax_value,0)/100)) AS pphDetail,
			((t.freight_quantity * t.freight_price)* (COALESCE(txppn.tax_value,0)/100)) AS ppnDetail
                        FROM payment p
                        
			LEFT JOIN invoice_sales_oa d ON d.inv_notim_id = p.invoice_sales_oa_id
                        LEFT JOIN TRANSACTION t
                            ON t.fc_ppayment_id = d.idPP
                        LEFT JOIN shipment c ON c.shipment_id = t.shipment_id
                        LEFT JOIN freight_local_sales e ON e.freight_id = d.freightId
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = e.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = e.pph_tax_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.invoice_sales_oa_id = {$row->invoice_sales_oa_id}";
						
				/*if($row->payment_method == 1){
                $sqlDP = "SELECT COALESCE(SUM(p.original_amount), 0) AS total_dp FROM payment p
                        WHERE p.payment_method = 2 AND p.payment_status = 0
                         AND p.vendor_id = {$row->vendor_id}";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    $downPayment = $rowDP->total_dp;
                }
				}*/
            }elseif($row->freight_id == 26) {
				$sql = "select remarks from payment p where p.payment_id = {$row->payment_id}
                        AND p.freight_id = {$row->freight_id}";

			}elseif($row->freight_id != '' && $row->payment_method == 1) {
                 $sqlDP = "SELECT p.amount_converted, p.original_amount, p.original_amount_converted, p.pph_amount, p.ppn_amount,p.amount_journal, tx.`tax_category`, f.pph, f.ppn, p.payment_date 
FROM payment p
LEFT JOIN freight f ON f.`freight_id` = p.`freight_id`
LEFT JOIN tax tx ON tx.`tax_id` = f.`pph_tax_id`
                        WHERE p.freight_id = {$row->freight_id} AND p.payment_id = {$row->payment_id} AND p.payment_status = 0";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    if($rowDP->original_amount < 0 ){
						if($rowDP->tax_category == 0){
							if($rowDP->payment_date > '2020-12-31'){
							$dPayment = ($rowDP->amount_converted) - ($rowDP->original_amount_converted);
							}else{
							$dPayment = ($rowDP->amount_converted) - ($rowDP->amount_journal);	
							}
							$dPPH = $dPayment * ($rowDP->pph/100);
							$dPPN = $dPayment * ($rowDP->ppn/100);
							$downPayment = $dPayment + $dPPN - $dPPH;
						}else{
							if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
							$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->original_amount_converted);
							}else if($rowDP->payment_date > '2020-12-31'){
							$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->original_amount_converted);
							}else{
								$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->amount_journal);
							}
						$dPPH = $dPayment * ($rowDP->pph/100);
						$dPPN = $dPayment * ($rowDP->ppn/100);
						$downPayment = $dPayment + $dPPN - $dPPH;
						}
					}else{
						
						if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
							$dPayment = ($rowDP->amount_converted) - ($rowDP->original_amount_converted);
						}else if($rowDP->payment_date > '2020-12-31'){
							$dPayment = (($rowDP->original_amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->amount_converted);
						}else{
							$dPayment = $rowDP->amount_converted - $rowDP->amount_journal;
						}
							$dPPH = $dPayment * ($rowDP->pph/100);
							$dPPN = $dPayment * ($rowDP->ppn/100);
							if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
								$downPayment = $dPayment ;
							}else{
								$downPayment = $dPayment + $dPPN - $dPPH;
							}
					}
                }
				$sqlFQ = "SELECT
CASE WHEN fc.freight_id = 278 THEN COALESCE(SUM(t.send_weight), 0)
WHEN fc.freight_id = 288 THEN COALESCE(SUM(t.send_weight), 0)
ELSE COALESCE(SUM(t.freight_quantity), 0) END AS freight_quantity, fc.freight_id
 FROM `transaction` t
						  LEFT JOIN freight_cost fc ON t.`freight_cost_id` = fc.`freight_cost_id`
						  WHERE fc.`freight_id` = {$row->freight_id} AND t.`fc_payment_id` = {$row->payment_id}";
                $resultFQ = $myDatabase->query($sqlFQ, MYSQLI_STORE_RESULT);
                if($resultFQ !== false) {
                    $rowFQ = $resultFQ->fetch_object();

                }

                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.freight_quantity AS quantity, fc.price,
                            CASE WHEN p.payment_method = 1 THEN
                            CASE WHEN p.payment_status = 0 AND t.transaction_date > '2015-10-05' AND sc.stockpile_id = 1
                            THEN t.quantity * t.freight_price
                            WHEN f.freight_id = 278 THEN t.send_weight * t.freight_price
							WHEN f.freight_id = 288 THEN t.send_weight * t.freight_price
							WHEN f.freight_id = 309 THEN t.send_weight * t.freight_price
							ELSE t.freight_quantity * t.freight_price  END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('Freight Cost - ', f.freight_code) AS keterangan,
                            f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            f.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, c.po_no, 
							
							ts.`trx_shrink_claim`, ts.amt_claim as amountClaim,
							
							ROUND(CASE WHEN ts.trx_shrink_tolerance_kg > 0 AND ((t.shrink * -1) - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - ts.trx_shrink_tolerance_kg) *-1
				
							WHEN ts.trx_shrink_tolerance_kg > 0 AND (t.shrink - ts.trx_shrink_tolerance_kg) > 0 AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - ts.trx_shrink_tolerance_kg
				
							WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL AND t.slip_retur IS NOT NULL THEN ((t.shrink *-1) - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id))*-1 
                
							WHEN ts.trx_shrink_tolerance_persen > 0 AND ((t.shrink/t.send_weight) * 100 > ts.trx_shrink_tolerance_persen) AND (SELECT transaction_id FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id) IS NOT NULL THEN t.shrink - (SELECT weight_persen FROM transaction_shrink_weight WHERE transaction_id = t.transaction_id)ELSE 0 END,10) AS qtyClaim
			
			
                        FROM payment p
                        LEFT JOIN TRANSACTION t
                            ON t.fc_payment_id = p.payment_id
                        LEFT JOIN freight_cost fc
                            ON fc.freight_cost_id = t.freight_cost_id
                        LEFT JOIN freight f
                            ON f.freight_id = p.freight_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = f.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = f.pph_tax_id
                        LEFT JOIN stockpile_contract sc
			            ON sc.stockpile_contract_id = t.stockpile_contract_id
						LEFT JOIN contract c
							ON c.contract_id = sc.contract_id
						LEFT JOIN stockpile s
							ON s.`stockpile_id` = sc.`stockpile_id`
						LEFT JOIN transaction_shrink_weight ts
							ON t.transaction_id = ts.transaction_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.freight_id = {$row->freight_sales_id}";
						
            } elseif($row->freight_sales_id != '' && $row->payment_method == 1) {
                 $sqlDP = "SELECT p.amount_converted, p.original_amount, p.original_amount_converted, p.pph_amount, p.ppn_amount,p.amount_journal, tx.`tax_category`, f.pph, f.ppn, p.payment_date 
FROM payment p
LEFT JOIN freight_local_sales f ON f.`freight_id` = p.`freight_sales_id`
LEFT JOIN tax tx ON tx.`tax_id` = f.`pph_tax_id`
                        WHERE p.freight_sales_id = {$row->freight_sales_id} AND p.payment_id = {$row->payment_id} AND p.payment_status = 0";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    if($rowDP->original_amount < 0 ){
						if($rowDP->tax_category == 0){
							if($rowDP->payment_date > '2020-12-31'){
							$dPayment = ($rowDP->amount_converted) - ($rowDP->original_amount_converted);
							}else{
							$dPayment = ($rowDP->amount_converted) - ($rowDP->amount_journal);	
							}
							$dPPH = $dPayment * ($rowDP->pph/100);
							$dPPN = $dPayment * ($rowDP->ppn/100);
							$downPayment = $dPayment + $dPPN - $dPPH;
						}else{
							if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
							$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->original_amount_converted);
							}else if($rowDP->payment_date > '2020-12-31'){
							$dPayment = (($rowDP->original_amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->amount_converted);
							}else{
								$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->amount_journal);
							}
						$dPPH = $dPayment * ($rowDP->pph/100);
						$dPPN = $dPayment * ($rowDP->ppn/100);
						$downPayment = $dPayment + $dPPN - $dPPH;
						}
					}else{
						
						if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
							$dPayment = ($rowDP->amount_converted) - ($rowDP->original_amount_converted);
						}else if($rowDP->payment_date > '2020-12-31'){
							$dPayment = (($rowDP->amount_converted + $rowDP->ppn_amount) - $rowDP->pph_amount) - ($rowDP->original_amount_converted);
						}else{
							$dPayment = $rowDP->amount_converted - $rowDP->amount_journal;
						}
							$dPPH = $dPayment * ($rowDP->pph/100);
							$dPPN = $dPayment * ($rowDP->ppn/100);
							if($rowDP->payment_date > '2020-12-31'  && $rowDP->ppn_amount > 0){
								$downPayment = $dPayment ;
							}else{
								$downPayment = $dPayment + $dPPN - $dPPH;
							}
					}
                }
				$sqlFQ = "SELECT COALESCE(SUM(t.freight_quantity), 0) END AS freight_quantity, fc.freight_id
						   FROM `transaction` t
						  LEFT JOIN freight_cost_local_sales fc ON t.`freight_cost_id` = fc.`freight_cost_id`
						  WHERE fc.`freight_id` = {$row->freight_sales_id} AND t.`fc_payment_id` = {$row->payment_id}";
                $resultFQ = $myDatabase->query($sqlFQ, MYSQLI_STORE_RESULT);
                if($resultFQ !== false) {
                    $rowFQ = $resultFQ->fetch_object();

                }

                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.freight_quantity AS quantity, fc.price,
                            CASE WHEN p.payment_method = 1 THEN
                            CASE WHEN p.payment_status = 0 AND t.transaction_date > '2015-10-05' AND c.stockpile_id = 1
                            THEN t.quantity * t.freight_price
                            WHEN f.freight_id = 278 THEN t.send_weight * t.freight_price
							WHEN f.freight_id = 288 THEN t.send_weight * t.freight_price
							WHEN f.freight_id = 309 THEN t.send_weight * t.freight_price
							ELSE t.freight_quantity * t.freight_price  END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('Freight Cost - ', f.freight_code) AS keterangan,
                            f.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            f.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value, c.sales_no AS po_no
							
						FROM payment p
                        LEFT JOIN TRANSACTION t
                            ON t.fc_payment_id = p.payment_id
                        LEFT JOIN freight_cost_local_sales fc
                            ON fc.freight_cost_id = t.freight_cost_id
                        LEFT JOIN freight_local_sales f
                            ON f.freight_id = p.freight_sales_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = f.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = f.pph_tax_id
                        LEFT JOIN shipment sc
			            ON sc.shipment_id = t.shipment_id
						LEFT JOIN sales c
							ON c.sales_id = sc.sales_id
						LEFT JOIN stockpile s
							ON s.`stockpile_id` = c.`stockpile_id`
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.freight_sales_id = {$row->freight_sales_id}";
						
            }elseif($row->vendor_handling_id != '' && $row->payment_method == 1) {
                 $sqlDP = "SELECT p.handlingDP, p.amount_converted, p.original_amount, p.pph_amount, vh.`pph`,vh.`ppn` FROM payment p
							LEFT JOIN vendor_handling vh ON vh.`vendor_handling_id` = p.`vendor_handling_id`
                        WHERE p.vendor_handling_id = {$row->vendor_handling_id} AND p.payment_id = {$row->payment_id}";
                $resultDP = $myDatabase->query($sqlDP, MYSQLI_STORE_RESULT);
                if($resultDP !== false) {
                    $rowDP = $resultDP->fetch_object();
                    if($rowDP->original_amount < 0 ){
					$dPayment = $rowDP->handlingDP ; //($rowDP->amount_converted - $rowDP->pph_amount) - ($rowDP->original_amount);
					$dPPH = $dPayment * ($rowDP->pph/100);
					$dPPN = $dPayment * ($rowDP->ppn/100);
					$downPayment = $dPayment + $dPPN - $dPPH;
					
					}else{
						
					$dPayment = $rowDP->handlingDP ; //$rowDP->amount_converted - $rowDP->original_amount;
					$dPPH = $dPayment * ($rowDP->pph/100);
					$dPPN = $dPayment * ($rowDP->ppn/100);
					$downPayment = $dPayment + $dPPN - $dPPH;
					}
                }

				$sqlHQ = "SELECT SUM(t.handling_quantity) AS handling_quantity FROM `transaction` t
						  WHERE t.hc_payment_id = {$row->payment_id}";
                $resultHQ = $myDatabase->query($sqlHQ, MYSQLI_STORE_RESULT);
                if($resultHQ !== false) {
                    $rowHQ = $resultHQ->fetch_object();

                }

                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.handling_quantity AS quantity, vhc.price,
                            CASE WHEN  p.payment_status = 0 THEN t.handling_quantity * vhc.price
							WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('Handling Cost - ', vh.vendor_handling_code) AS keterangan,
                            vh.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            vh.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
							'0' AS amountClaim
                        FROM payment p
                        LEFT JOIN TRANSACTION t
                            ON t.hc_payment_id = p.payment_id
                        LEFT JOIN vendor_handling_cost vhc
                            ON vhc.handling_cost_id = t.handling_cost_id
                        LEFT JOIN vendor_handling vh
                            ON vh.vendor_handling_id = p.vendor_handling_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = vh.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = vh.pph_tax_id
                        LEFT JOIN stockpile_contract sc
			            ON sc.stockpile_contract_id = t.stockpile_contract_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.vendor_handling_id = {$row->vendor_handling_id}";


            }elseif($row->labor_id != '') {
				
				$downPayment = $row->dpLabor;

				$sqlUQ = "SELECT COALESCE(SUM(t.quantity), 0) AS unloading_quantity FROM `transaction` t
						  WHERE t.`labor_id` = {$row->labor_id} AND t.`uc_payment_id` = {$row->payment_id}";
                $resultUQ = $myDatabase->query($sqlUQ, MYSQLI_STORE_RESULT);
                if($resultUQ !== false) {
                    $rowUQ = $resultUQ->fetch_object();

                }
                $sql = "SELECT CASE WHEN t.slip_retur IS NOT NULL THEN CONCAT(t.slip_no,'(',t.slip_retur,')') ELSE t.slip_no END AS slip_no, t.quantity AS quantity, uc.price,
                            CASE WHEN p.payment_method = 1 THEN
                            	CASE WHEN p.payment_status = 0 THEN t.unloading_price ELSE p.original_amount END
                            WHEN p.payment_method = 2 THEN p.original_amount
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('Unloading Cost - ', l.labor_name) AS keterangan,
                            l.ppn_tax_id, txppn.tax_category AS ppn_tax_category, txppn.tax_value AS ppn_tax_value,
                            l.pph_tax_id AS pph_tax_id, txpph.tax_category AS pph_tax_category, txpph.tax_value AS pph_tax_value,
							'0' AS amountClaim
                        FROM payment p
                        LEFT JOIN transaction t
                            ON t.uc_payment_id = p.payment_id
                        LEFT JOIN unloading_cost uc
                            ON uc.unloading_cost_id = t.unloading_cost_id
                        LEFT JOIN labor l
                            ON l.labor_id = p.labor_id
                        LEFT JOIN tax txppn
                            ON txppn.tax_id = l.ppn_tax_id
                        LEFT JOIN tax txpph
                            ON txpph.tax_id = l.pph_tax_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.labor_id = {$row->labor_id}";
            } elseif($row->sales_id != '') {
                $sql = "SELECT sh.shipment_code AS slip_no,
                            CASE WHEN p.payment_method = 2 THEN sl.quantity
                            WHEN p.payment_method = 1 THEN sh.quantity
                            ELSE '' END AS quantity,
                            sl.price AS price, sl.sales_no,
                            CASE WHEN p.payment_method = 1 THEN sh.quantity * sl.price
                            WHEN p.payment_method = 2 THEN p.original_amount - p.ppn_journal
                            ELSE '' END AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            CONCAT('Sales - ', sl.sales_no) AS keterangan,
                            sh.dp_amount, p.ppn_amount
                        FROM payment p
                        LEFT JOIN payment_detail pd
                            ON pd.payment_id = p.payment_id
                        LEFT JOIN shipment sh
                            ON sh.shipment_id = pd.shipment_id
                        LEFT JOIN sales sl
                            ON sl.sales_id = p.sales_id
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}
                        AND p.sales_id = {$row->sales_id}";
            } else {
                $sql = "SELECT '-' AS slip_no,
                            '' AS quantity,
                            '' AS price,
                            p.original_amount AS original_amount,
                            CASE WHEN p.payment_method = 1 THEN 'Payment'
                            WHEN p.payment_method = 2 THEN 'Down Payment'
                            ELSE '' END AS payment_method2,
                            p.remarks AS keterangan,
                            '0' AS dp_amount
                        FROM payment p
                        WHERE 1=1
                        AND p.payment_id = {$row->payment_id}";
            }

            $resultDetail = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $total = 0;
            while($rowDetail = $resultDetail->fetch_object()) {
                if($row->sales_id != '' && $rowDetail->ppn_amount !== 0 && $row->payment_method == 1) {
					$downPayment = $downPayment + $rowDetail->dp_amount / 1.1;
					}else if($row->payment_method == 1){
                   $downPayment = $downPayment + $rowDetail->dp_amount;
                }
            ?>
            <tr>

                <td><?php
				if($row->invoice_id != '') {
                        echo $rowDetail->shipment_no;
                    }elseif($row->invoice_sales_id != '' || $row->invoice_sales_oa_id != '') {
				echo $rowDetail->shipment_no;
				}else {
						echo $row->shipment_no;
					}?></td>
                <td><?php
				if($row->freight_id != '') {
				echo $rowDetail->po_no;
				}if($row->freight_sales_id != '') {
				echo $rowDetail->sales_no;
				}elseif($row->stockpile_contract_id != '') {
				echo $rowDetail->po_no;
				}elseif($row->invoice_id != '') {
				echo $rowDetail->po_no;
				}else{
				echo $row->po_no_2;
				}?></td>
                <td><?php echo $rowDetail->slip_no; ?></td>
                <td>
                    <?php
                    if($rowDetail->quantity != '') {
                        echo number_format($rowDetail->quantity, 2, ".", ",") . ' Kg';
                    } else if($row->qty != '' && $row->general_vendor_id != '') {
                        echo number_format($row->qty, 2, ".", ",");
                    } else if($row->invoice_id != '') {
                       echo number_format($rowDetail->qty, 2, ".", ",") .' '. $rowDetail->uom;
                    } else if($row->freight_id != '' && $row->payment_method = 2) {
                        echo number_format($row->qty, 2, ".", ",");
                    } else if($row->freight_sales_id != '' && $row->payment_method = 2) {
                        echo number_format($row->qty, 2, ".", ",");
                    }else if($row->vendor_handling_id != '' && $row->payment_method = 2) {
                        echo number_format($row->qty, 2, ".", ",");
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <div style="text-align: right;">
                    <?php
                    if($rowDetail->price != '') {
						 if($rowDetail->pph_tax_id == 0 || $rowDetail->pph_tax_id == '') {
                                $detailPrice = $rowDetail->price;
                            } else {
                                if($rowDetail->pph_tax_category == 1) {
                                    //$dppTotalPrice = $rowDetail->original_amount_converted;
                                    $detailPrice = ($rowDetail->price) / ((100 - $rowDetail->pph_tax_value) / 100);
                                } else {
                                    $detailPrice = $rowDetail->price;
                                }
							}
                        echo number_format($detailPrice, 3, ".", ",");
                    } elseif($row->price != '' && $row->general_vendor_id != '') {
                        echo number_format($row->price, 3, ".", ",");
                    } elseif($row->invoice_id != '') {
                        echo number_format($rowDetail->price, 3, ".", ",");
                    } else if($row->freight_id != '' && $row->payment_method = 2) {
                        echo number_format($row->price, 3, ".", ",");
                    } else if($row->freight_sales_id != '' && $row->payment_method = 2) {
                        echo number_format($row->price, 3, ".", ",");
                    }else if($row->vendor_handling_id != '' && $row->payment_method = 2) {
                        echo number_format($row->price, 3, ".", ",");
                    } else {
                        echo '-';
                    }
                    ?>
                    </div>
                </td>
				<td><?php
				if($row->invoice_id != ''){
				echo number_format($rowDetail->termin, 0, ".", ",");
                }else if($row->freight_id != '' && $row->payment_method = 2) {
                        echo number_format($row->termin, 0, ".", ",");
                }else if($row->freight_sales_id != '' && $row->payment_method = 2) {
                        echo number_format($row->termin, 0, ".", ",");
                }else{
                echo '100';
				}
				?>
                %</td>
				<?php if($row->vendor_id != '' || $row->freight_id != '' || $row->freight_sales_id != '' || $row->vendor_handling_id != '' || $row->labor_id != ''){
				if($rowDetail->original_amount != '') {
                        if($rowDetail->payment_method2 == 'Payment') {
                            if($rowDetail->pph_tax_id == 0 || $rowDetail->pph_tax_id == '') {
                                $dppTotalPrice = $rowDetail->original_amount;
                            } else {
                                if($rowDetail->pph_tax_category == 1) {
                                    //$dppTotalPrice = $rowDetail->original_amount_converted;
                                    $dppTotalPrice = (($rowDetail->original_amount) / ((100 - $rowDetail->pph_tax_value) / 100));

                                } else {
                                    $dppTotalPrice = $rowDetail->original_amount ;
                                }
                            }
                        }
					else {
                            $dppTotalPrice = $rowDetail->original_amount;
                        }
				}

						if($rowDetail->ppn_tax_id != 0 || $rowDetail->ppn_tax_id != ''){

						$ppnDetail = $rowDetail->original_amount * ($rowDetail->ppn_tax_value / 100);
						$ppnShrink = $rowDetail->amountClaim * ($rowDetail->ppn_tax_value / 100);
						}



						if($rowDetail->pph_tax_category == 1) {


								$amountClaim = $rowDetail->amountClaim / ((100 - $rowDetail->pph_tax_value) / 100);
								$shrinkClaim = $rowDetail->trx_shrink_claim / ((100 - $rowDetail->pph_tax_value) / 100);
								$pphShrink = $amountClaim * ($rowDetail->pph_tax_value / 100);
								$pphDetail = ($rowDetail->original_amount / ((100 - $rowDetail->pph_tax_value) / 100)) - $rowDetail->original_amount;
						}else{

								$amountClaim = $rowDetail->amountClaim ;
								$shrinkClaim = $rowDetail->trx_shrink_claim;
								$pphShrink = $amountClaim * ($rowDetail->pph_tax_value / 100);
								$pphDetail = ($rowDetail->original_amount) * ($rowDetail->pph_tax_value / 100);
						}


							$dppTotal = ($dppTotalPrice + $ppnDetail) - $pphDetail;

					?>
			<td><div style="text-align: right;"><?php echo number_format($dppTotalPrice, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($ppnDetail, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($pphDetail, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($dppTotal, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($rowDetail->qtyClaim, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($shrinkClaim, 2, ".", ",");?></div></td>
			<td><div style="text-align: right;"><?php echo number_format($amountClaim, 2, ".", ",");?></div></td>
			<?php }else{?>
                <td colspan="4"><?php

				    if($row->stockpile_contract_id != '' || $row->sales_di != ''){
				    echo $rowDetail->payment_method2 .' - '. $rowDetail->keterangan;
			     	}else{
					if($row->invoice_id != ''){
					 echo $rowDetail->stockpile_name .' - '. $rowDetail->notes;
				 	}else{
					echo $rowDetail->payment_method2 .' - '. $row->remarks;
				 }}?></td>

				 <td>
                    <div style="text-align: right;">
                    <?php

                    if($rowDetail->original_amount != '') {
                        if($rowDetail->payment_method2 == 'Payment') {
                            if($rowDetail->pph_tax_id == 0 || $rowDetail->pph_tax_id == '') {
                                $dppTotalPrice = $rowDetail->original_amount;
                            } else {
                                if($rowDetail->pph_tax_category == 1) {
                                    //$dppTotalPrice = $rowDetail->original_amount_converted;
                                    $dppTotalPrice = ($rowDetail->original_amount) / ((100 - $rowDetail->pph_tax_value) / 100);
                                } else {
                                    $dppTotalPrice = $rowDetail->original_amount;
                                }
                            }
                        }
					else {
                            $dppTotalPrice = $rowDetail->original_amount;
                        }

                        echo number_format($dppTotalPrice, 2, ".", ",");
                    }	elseif($row->invoice_id != '') {
							 if($row->payment_method == 1){
								 $dppTotalPrice = $rowDetail->amount;
								 $ppnDetail = $ppnDetail + $rowDetail->ppn;
								 $pphDetail = $pphDetail + $rowDetail->pph;
							 }else{
								 $dppTotalPrice = $row->original_amount_converted;
								 $ppnDetail = 0;
								 $pphDetail = 0;
							 }
							echo number_format($dppTotalPrice, 2, ".", ",");
						}else {
                        echo '-';
                    }
                    ?>
                    </div>
                </td>
				<td>
                    <div style="text-align: right;">
                    <?php

					echo number_format($rowDetail->ppnDetail, 2, ".", ",");

                    ?>
                    </div>
                </td>
				<td>
                    <div style="text-align: right;">
                    (<?php

					echo number_format($rowDetail->pphDetail, 2, ".", ",");

                    ?>)
                    </div>
                </td>
				<?php }?>

				<td>
                    <div style="text-align: right;">
                    <?php
					if($row->vendor_id != '' || $row->freight_id != '' || $row->freight_sales_id != '' || $row->vendor_handling_id != '' || $row->labor_id != ''){

					$amtTotal = $dppTotal - $amountClaim;

					}else{

                    $amtTotal = ($dppTotalPrice + $rowDetail->ppnDetail) - $rowDetail->pphDetail;

					}
                   echo number_format($amtTotal, 2, ".", ",");
                    ?>
                    </div>
                </td>
            </tr>
            <?php
			if($row->vendor_id != '' || $row->freight_id != '' || $row->freight_sales_id != ''  || $row->vendor_handling_id != '' || $row->labor_id != ''){

				if($row->pMethod == 'Payment'){
				$dpp = $dpp + $dppTotalPrice;

				$ppnDpp = $ppnDpp + $ppnDetail;
				$ppnSusut = $ppnSusut + $ppnShrink;
				$ppnAmount = $ppnDpp - $ppnSusut;

				$pphDpp = $pphDpp + $pphDetail;
				$pphSusut = $pphSusut + $pphShrink;
				$pphAmount = $pphDpp - $pphSusut;

				$shrink = $shrink + $amountClaim;
				$total = (($dpp - $shrink) + $ppnAmount) - $pphAmount;

				}else{
					$ppnAmount = $row->ppn_amount;
					$pphAmount = $row->pph_amount;
					$total = ($row->original_amount + $ppnAmount) - $pphAmount;
				}

			}else{
				$dpp = $dpp + $dppTotalPrice;
				$ppnAmount = $ppnAmount + $rowDetail->ppnDetail;
				$pphAmount = $pphAmount + $rowDetail->pphDetail;
				$total = $total + $amtTotal;
			}

				if($row->vendor_id != ''){
					$curahQuantity = $curahQuantity + $rowDetail->quantity;
				}

           }
            ?>
        </tbody>

        	<!--<tr>
                <td colspan="13" style="text-align: right;">DPP Total</td>
                <td><div style="text-align: right;"><?php //echo number_format($total, 2, ".", ","); ?></div></td>
            </tr>-->
           <?php


            if($row->vendor_id != '') {

                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Total Quantity</td>
                <td><div style="text-align: right;"><?php echo number_format($curahQuantity, 2, ".", ","); ?></div></td>
            </tr>
              <?php
            }

            if($row->freight_id != '') {

                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Total Quantity</td>
                <td><div style="text-align: right;"><?php echo number_format($rowFQ->freight_quantity, 2, ".", ","); ?></div></td>
            </tr>
                <?php

            }
			if($row->vendor_handling_id != '') {

                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Total Quantity</td>
                <td><div style="text-align: right;"><?php echo number_format($rowHQ->handling_quantity, 2, ".", ","); ?></div></td>
            </tr>
                <?php

            }
			if($row->labor_id != '') {

                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Total Quantity</td>
                <td><div style="text-align: right;"><?php echo number_format($rowUQ->unloading_quantity, 2, ".", ","); ?></div></td>
            </tr>
                <?php

            }

           /* if($row->ppn_amount > 0) {
                $total = $total + $row->ppn_amount;
			}

			if($row->payment_status == 1 && ($row->freight_id != '' || $row->labor_id != '' || $row->vendor_handling_id != '')){
				$ppn_amount = 0;
				$pph_amount = 0;

			}elseif($row->invoice_id != ''){
				$ppn_amount = $ppnDetail;
				$pph_amount = $pphDetail;
				$total = $total + $ppn_amount - $pph_amount;

			}
			else{
				$ppn_amount = $row->ppn_amount;
				$pph_amount = $row->pph_amount;
			}*/
                ?>
			<tr>
                <td colspan="13" style="text-align: right;">Total DPP</td>
                <td><div style="text-align: right;"><?php echo number_format($dpp, 2, ".", ","); ?></div></td>
            </tr>
			<?php if($row->freight_id != '' || $row->labor_id != ''){?>
			<tr>
                <td colspan="13" style="text-align: right;">Total Susut</td>
                <td><div style="text-align: right;">(<?php echo number_format($shrink, 2, ".", ","); ?>)</div></td>
            </tr>
			<?php }?>
            <tr>
                <td colspan="13" style="text-align: right;">Total PPN</td>
                <td><div style="text-align: right;"><?php echo number_format($ppnAmount, 2, ".", ","); ?></div></td>
            </tr>
                <?php


            /*if($row->pph_amount > 0 && $row->pph_tax_id == 21) {
                $total = $total + $row->pph_amount;
			}elseif($row->pph_amount > 0) {
                $total = $total - $row->pph_amount;
			}*/
                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Total PPh</td>
                <td><div style="text-align: right;">(<?php echo number_format($pphAmount, 2, ".", ","); ?>)</div></td>
            </tr>
             <?php
            if($downPayment > 0) {
                $total = $total - $downPayment;
                ?>
            <tr>
                <td colspan="13" style="text-align: right;">Down Payment</td>
                <td><div style="text-align: right;">(<?php echo number_format($downPayment, 2, ".", ","); ?>)</div></td>
            </tr>
                <?php
			}
            ?>

            <tr>
                <td colspan="13" style="text-align: right;">Total</td>
                <td><div style="text-align: right;"><?php echo number_format($total, 2, ".", ","); ?></div></td>
            </tr>

    </table>
    <?php
    } else {
    ?>

    <?php
    }
    ?>

    <!--<br/>-->
    <table width="100%">
        <tr>
            <td width="50%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt;">
                    <tr>
                        <td width="28%"><b>TT to</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $row->beneficiary; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Bank</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $row->bank_name; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Cabang</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $row->branch; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>No Rek.</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $row->account_no; ?></td>
                    </tr>
                    <tr>
                        <td width="28%"><b>Swift Code</b></td>
                        <td width="4%">:</td>
                        <td width="68%"><?php echo $row->swift_code; ?></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%" class="table table-bordered table-striped" style="font-size: 9pt; height: 186px;">
                    <thead>
                        <tr>
                            <th style="vertical-align: top; height: 30px;">Prepare By </th>
                            <th style="vertical-align: top; height: 30px;">Check By </th>
                            <th style="vertical-align: top; height: 30px;">Approval By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
						$sqlSignature = "SELECT us.signature, u.user_name FROM user_signature us LEFT JOIN payment p
						ON us.user_id = p.entry_by
						LEFT JOIN user u
							ON us.user_id = u.user_id
                        WHERE p.payment_id = {$row->payment_id} ORDER BY user_signature_id DESC LIMIT 1";
               	 $resultSignature = $myDatabase->query($sqlSignature, MYSQLI_STORE_RESULT);
                if($resultSignature == 1) {
                    $rowSignature = $resultSignature->fetch_object();
                    $signature = $rowSignature->signature;
					$user_name = $rowSignature->user_name;
                }
				?>
						<td style="width: 33%; height: 40px;"><!--<center><img src="import/signature/<?php //echo $signature; ?>" border="0" width="100" height="50"/>
							<br/><?php //echo $user_name; ?></center>--></td>
					 <?php
						$sqlSignature = "SELECT us.signature, u.user_name FROM user_signature us
										LEFT JOIN user u
										ON us.user_id = u.user_id
										WHERE u.user_id = 16 ORDER BY user_signature_id DESC LIMIT 1";
               	 $resultSignature = $myDatabase->query($sqlSignature, MYSQLI_STORE_RESULT);
                if($resultSignature == 1) {
                    $rowSignature = $resultSignature->fetch_object();
                    $signature2 = $rowSignature->signature;
					$user_name2 = $rowSignature->user_name;
                }
				?>
                            <td style="width: 33%; height: 40px;"><!--<center><img src="import/signature/<?php //echo $signature2; ?>" border="0" width="100" height="50"/>
							<br/><?php //echo $user_name2; ?></center>--></td>
                            <td style="width: 33%; height: 40px;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<?php
if($boolBack) {
?>
<button class="btn" type="button" onclick="back()">Back</button>
<?php
}

if($boolInsert) {
?>
<button class="btn" type="button" onclick="back()">Back</button>
<?php
}
?>
<button class="btn btn-info" id="printPayment">Print</button>
<?php
$sqlRetur = "SELECT * FROM batch_upload_detail WHERE payment_id = {$row->payment_id} GROUP BY payment_id";
             $resultRetur = $myDatabase->query($sqlRetur, MYSQLI_STORE_RESULT);
				if($resultRetur > 0) {
                    $rowRetur = $resultRetur->fetch_object();
                    $idPayment = $rowRetur->payment_id;
					$statusRetur = $rowRetur->status;
                }
if($row->payment_status == 0 && $idPayment == $row->payment_id && $statusRetur == 2 ) {
?>
<form method="post" id="returnPayment">
<input type="hidden" name="action" id="action" value="return_payment_sales" />
<input type="hidden" name="paymentId" id="paymentId" value="<?php echo $row->payment_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnPaymentDate" name="returnPaymentDate"  data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="returnButton">Return</button>
</div>
</div>
</form>
<?php
}else if($row->payment_status == 0 && $idPayment != $row->payment_id){
?>
<form method="post" id="returnPayment">
<input type="hidden" name="action" id="action" value="return_payment_sales" />
<input type="hidden" name="paymentId" id="paymentId" value="<?php echo $row->payment_id; ?>" />
<div class="row-fluid">  
<div class="span4 lightblue">
<label>Return Date <span style="color: red;">*</span></label>
<input type="text" placeholder="DD/MM/YYYY" tabindex="3" id="returnPaymentDate" name="returnPaymentDate"  data-date-format="dd/mm/yyyy" class="datepicker" >
</br>
<button class="btn btn-warning" id="returnButton">Return</button>
</div>
</div>
</form>
<?php
}
?>
<?php if($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 213) {
?>
<button class="btn btn-warning" id="jurnalPayment">JP</button>

<?php
}
