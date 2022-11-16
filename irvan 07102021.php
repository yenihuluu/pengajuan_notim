<?php
// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_INCLUDE . DS . 'Bcrypt.php';

global $myDatabase;

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('m-y');
$currentYearMonth = $date->format('ym');
$todayDate = $date->format('Y-m-d');
$currentYear = $date->format('y');

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'pengajuan_general_data') {
    // <editor-fold defaultstate="collapsed" desc="CRUD PENGAJUAN GENERAL DATA">

    // <editor-fold defaultstate="collapsed" desc="POST VARIABLE">
    $return_value = '';
    $addMessage = '';
    $boolNew = false;
    $boolContinue = true;
    $boolUpdate = false;
    $boolUpdateInvoice = false;
    $boolUpdateInvoiceDetail = false;
    $boolInsertVendor = false;
    $boolVendorExists = false;
    $boolRecalculate = false;
    $boolPriceUp = false;
    $boolPriceDown = false;
    $boolQuantityUp = false;
    $boolQuantityDown = false;

    $invoiceMethod = 1;
    $generatedInvoiceNo = $myDatabase->real_escape_string($_POST['generatedInvoiceNo']);
    $invoiceNo2 = $myDatabase->real_escape_string($_POST['generatedInvoiceNo2']);
    $grandTotal = $myDatabase->real_escape_string($_POST['grandTotal']);
    $invoiceDate = $myDatabase->real_escape_string($_POST['invoiceDate']);
    $inputDate = $myDatabase->real_escape_string($_POST['inputDate']);
    $requestDate = $myDatabase->real_escape_string($_POST['requestDate']);
    $taxDate = $myDatabase->real_escape_string($_POST['taxDate']);
    $invoiceTax = $myDatabase->real_escape_string($_POST['invoiceTax']);
    $remarks = $myDatabase->real_escape_string($_POST['remarks']);
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $transaksiMutasi = $myDatabase->real_escape_string($_POST['transaksiMutasi']);
    $transaksiPO = $myDatabase->real_escape_string($_POST['transaksiPO']);
    $amount = $grandTotal;
    $amountConverted = $grandTotal;
    $amount_ori = $grandTotal;
    $amount_ori_conv = $grandTotal;
    $rejectRemarks = $_POST['rejectRemarks'];

    // </editor-fold>

    if ($_POST['_method'] == 'DELETE') {
        // <editor-fold defaultstate="collapsed" desc="DELETE PENGAJUAN GENERAL">
        $pgId = $myDatabase->real_escape_string($_POST['pgId']);
        if ($pgId != '') {
            $sql = "DELETE FROM `pengajuan_general` WHERE pengajuan_general_id = {$pgId}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            if ($result !== false) {
                $return_value = '|OK|Pengajuan General has successfully deleted.|';
            } else {
                $return_value = '|FAIL|Delete Pengajuan General failed.|';
            }
        } else {
            $return_value = '|FAIL|Record not found.|';
        }
        echo $return_value;
        // </editor-fold>
    } else if ($_POST['_method'] == 'CANCEL') {
        // <editor-fold defaultstate="collapsed" desc="Cancel Pengajuan General">
        $pgId = $_POST['pgId'];
        $transaksiMutasi = '';
        //Update Status Pengajuan General
        $sqlUpdatePG = "UPDATE `pengajuan_general` SET invoice_no2 = '-', reject_remarks = '{$rejectRemarks}', status_pengajuan = 5, reject_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') WHERE pengajuan_general_id = {$pgId}";
        $updatePG = $myDatabase->query($sqlUpdatePG, MYSQLI_STORE_RESULT);

        //GET Transaction Type & PO ID
        $sqlPG = "SELECT * FROM pengajuan_general WHERE pengajuan_general_id = {$pgId}";
        $pg = $myDatabase->query($sqlPG, MYSQLI_STORE_RESULT);
        while ($rowPG = $pg->fetch_object()) {
            $transaksiMutasi = $rowPG->transaction_type;
            $po_id = $rowPG->po_id;
            $transactionPO = $rowPG->transaction_po;
        }
        if ($transactionPO == 2) {
            $sqlUpdateStatusPO = "UPDATE po_hdr SET status = 0 where idpo_hdr = {$po_id}";
            $resUpdateSTPO = $myDatabase->query($sqlUpdateStatusPO, MYSQLI_STORE_RESULT);
        }

        $updateLogbookQuery = "UPDATE logbook_new SET status1= 2 WHERE pinvoice_id = {$pgId}";
        $updateLogbook = $myDatabase->query($updateLogbookQuery, MYSQLI_STORE_RESULT);

        if ($transaksiMutasi == 2) {
            // $sqlPGD = "SELECT * FROM pengajuan_general_detail WHERE pg_id = {$pgId}";
            // $pgd = $myDatabase->query($sqlPGD, MYSQLI_STORE_RESULT);

            // while ($rowPGD = $pgd->fetch_object()) {
            //     $sqlMutasiQTY = "SELECT * FROM mutasi_qty_price WHERE pgd_id = {$rowPGD->pgd_id}";
            //     $mutasiQTY = $myDatabase->query($sqlMutasiQTY, MYSQLI_STORE_RESULT);

            //     while ($rowMutasiQTY = $mutasiQTY->fetch_object()) {
            //         $pphAmt = isset($rowMutasiQTY->pphAmt) ? $rowMutasiQTY->pphAmt : 0;
            //         $ppnAmt = isset($rowMutasiQTY->ppnAmt) ? $rowMutasiQTY->ppnAmt : 0;
            //         $invoiceDetailId = isset($rowMutasiQTY->invoice_detail_id) ? $rowMutasiQTY->invoice_detail_id : 0;
            //         $pphId = isset($rowMutasiQTY->pphId) ? $rowMutasiQTY->pphId : 0;
            //         $ppnId = isset($rowMutasiQTY->ppnId) ? $rowMutasiQTY->ppnId : 0;
            //         $qtyEstimasi = isset($rowMutasiQTY->qtyEstimasi) ? $rowMutasiQTY->qtyEstimasi : 0;

            //         $insertLog = "INSERT INTO mutasi_qty_price_log (pgd_id,mutasi_detail_id,termin,qtyInvoice,invoice_detail_id,ppnId,ppnAmt,pphAmt,pphId,qtyEstimasi,reject_by,reject_date) VALUES
            //                                    ({$rowMutasiQTY->pgd_id},{$rowMutasiQTY->mutasi_detail_id},{$rowMutasiQTY->termin},{$rowMutasiQTY->qtyInvoice},{$invoiceDetailId},
            //                                    {$ppnId},{$ppnAmt},{$pphAmt},{$pphId},{$qtyEstimasi},{$_SESSION['userId']},STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
            //         echo $insertLog;
            //         $log = $myDatabase->query($insertLog, MYSQLI_STORE_RESULT);

            //         if ($log == true) {
            //             $deleteQTY = "DELETE FROM mutasi_qty_price WHERE pgd_id = {$rowMutasiQTY->pgd_id}";
            //             $delQTY = $myDatabase->query($deleteQTY, MYSQLI_STORE_RESULT);

            //             if ($delQTY == true) {
            //                 $return_value = '|OK|. Cancel has successfully||';
            //             } else {
            //                 $return_value = '|Fail|. Cancel fail ||';
            //             }
            //         } else {
            //             $return_value = '|Fail|. Cancel fail2 ||';
            //         }
            //     }
            // }
            $return_value = '|OK|. Cancel has successfully||';
        } else {
            if ($updatePG == true) {
                $return_value = '|OK|. Cancel has successfully ||';
            } else {
                $return_value = '|Fail|. Cancel fail2 ||';
            }
        }
        echo $return_value;
        die();
        // </editor-fold>
    } else if ($_POST['_method'] == 'REJECT') {
        // <editor-fold defaultstate="collapsed" desc="Reject Pengajuan General">
        $pgId = $_POST['pgId'];
        $sqlUpdatePG = "UPDATE `pengajuan_general` SET status_pengajuan = 4 , reject_remarks = '{$rejectRemarks}', reject_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') WHERE pengajuan_general_id = {$pgId}";
        echo $sqlUpdatePG;
        $updatePG = $myDatabase->query($sqlUpdatePG, MYSQLI_STORE_RESULT);

        $updateLogbookQuery = "UPDATE logbook_new SET status1= 4 WHERE pinvoice_id = {$pgId}";
        echo $updateLogbookQuery;
        $updateLogbook = $myDatabase->query($updateLogbookQuery, MYSQLI_STORE_RESULT);

        if ($updatePG == true && $updateLogbook == true) {
            $return_value = '|OK|. Reject has successfully||';

        } else {
            $return_value = '|Fail|. Reject FAIL!||';

        }
        echo $return_value;
        die();
        // </editor-fold>
    } else {
        // <editor-fold defaultstate="collapsed" desc="INSERT Pengajuan General">
        if ($grandTotal != '' && $boolContinue) {
            //Get Stockpile Name
            $sql = "SELECT stockpile_name FROM stockpile WHERE stockpile_id = $stockpileId ";
            $resultStockpile = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($resultStockpile->num_rows == 1) {
                $rowStockpile = $resultStockpile->fetch_object();
                $stockpileName = $rowStockpile->stockpile_name;
            }

            if ($grandTotal <= 0) {
                $boolContinue = true;
            }
            if ($boolContinue) {

                // <editor-fold defaultstate="collapsed" desc="Check Pengajuan No">

                if ($_POST['_method'] == 'UPDATE') {
                    $pgId = $_POST['pgId'];
                    $sqlPGNO = "SELECT pengajuan_no,stockpileId FROM pengajuan_general where pengajuan_general_id = {$pgId}";
                    echo $sqlPGNO;
                    $resPGNO = $myDatabase->query($sqlPGNO, MYSQLI_STORE_RESULT);

                    if ($resPGNO->num_rows == 1) {
                        $rowPG = $resPGNO->fetch_object();
                        $stockpileId = $rowPG->stockpileId;
                        $pengajuanNo = $rowPG->pengajuan_no;
                    }

                    $sqlStockpile = "SELECT stockpile_name FROM stockpile WHERE stockpile_id = $stockpileId ";
                    $resultStockpile = $myDatabase->query($sqlStockpile, MYSQLI_STORE_RESULT);

                    if ($resultStockpile->num_rows == 1) {
                        $rowStockpile = $resultStockpile->fetch_object();
                        $stockpileName = $rowStockpile->stockpile_name;
                    }

                } else {
                    $checkInvoiceNo = 'PGJ/JPJ/' . $currentYearMonth;
                    $sql = "SELECT pengajuan_no FROM pengajuan_general WHERE company_id = {$_SESSION['companyId']} AND pengajuan_no LIKE '{$checkInvoiceNo}%' ORDER BY pengajuan_general_id DESC LIMIT 1";
                    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    if ($resultInvoice->num_rows == 1) {
                        $rowInvoice = $resultInvoice->fetch_object();
                        $splitInvoiceNo = explode('/', $rowInvoice->pengajuan_no);
                        $lastExplode = count($splitInvoiceNo) - 1;
                        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
                        $pengajuanNo = $checkInvoiceNo . '/' . $nextInvoiceNo;
                    } else {
                        $pengajuanNo = $checkInvoiceNo . '/1';

                    }
                }
                // </editor-fold>

                // <editor-fold defaultstate="collapsed" desc="Upload File">
                if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
                    $allowed = array('png', 'jpg', 'pdf', 'doc', 'docs', 'xls', 'xlsx');
                    $fileName = $_FILES['file']['name'];
                    $x = explode('.', $fileName);
                    $ekstensi = strtolower(end($x));
                    $ukuran = $_FILES['file']['size'];
                    $file_tmp = $_FILES['file']['tmp_name'];

                    if (in_array($ekstensi, $allowed) === true) {

                        $attachmentPath = "./import/pengajuan_general/" . $stockpileName . "/" . $todayDate . '/';
                        if ($ukuran < 115044070) {

                            if (!is_dir($attachmentPath) && !file_exists($attachmentPath))
                                $temp = mkdir($attachmentPath, 0755, TRUE);
                            else
                                $temp = TRUE;

                            if ($temp === TRUE) {
                                $attachmentPath .= str_ireplace('/', '-', $pengajuanNo) . '.' . $ekstensi;
                                if (!move_uploaded_file($file_tmp, $attachmentPath)) {
                                    echo '|FAIL|Error while uploading file.|';
                                    die();
                                }
                            } else {
                                echo '|FAIL|Error while creating directory.|';
                                die();
                            }
                        } else {
                            echo '|FAIL|UKURAN FILE TERLALU BESAR.|';
                            die();
                        }
                    } else {
                        echo '|FAIL|EKSTENSI FILE YANG DI UPLOAD TIDAK DI PERBOLEHKAN.|';
                        die();
                    }
                } else {
                    $attachmentPath = 'NULL';
                }
                // </editor-fold>

                $result = false;
                $paymentType = isset($_POST['paymentType']) && $_POST['paymentType'] != '' ? $_POST['paymentType'] : 0;

                // if($paymentType == 2){
                //     $sqlPaymentDate = "CALL PlanPayDate('{$todayDate}')";
                //     $resultPlanPayDate = $myDatabase->query($sqlPaymentDate, MYSQLI_STORE_RESULT);
                //     while($rowPD = $resultPlanPayDate->fetch_object()){
                //         $date = date_create($rowPD->tglBayar);
                //         $newDate = date_format($date,"d/m/Y");
                //         $reqPaymentDate = "STR_TO_DATE('{$newDate}', '%d/%m/%Y')";
                //     } 
                // }else{
                //     $reqPaymentDate = isset($_POST['requestPaymentDate']) && $_POST['requestPaymentDate'] != '' ? "STR_TO_DATE('{$_POST['requestPaymentDate']}', '%d/%m/%Y')" : 'NULL';
                // }
                $reqPaymentDate = isset($_POST['requestPaymentDate']) && $_POST['requestPaymentDate'] != '' ? "STR_TO_DATE('{$_POST['requestPaymentDate']}', '%d/%m/%Y')" : 'NULL';

                // echo $reqPaymentDate;
                if ($_POST['_method'] == 'INSERT') {
                    $transaksiPO = $_POST['transaksiPO'];
                    $terminHeader = $_POST['termin'];

                    $sqlCheckInvoiceNo2 = "SELECT invoice_no2 FROM pengajuan_general WHERE invoice_no2 != '-' AND status_pengajuan != 5 AND invoice_no2 ='{$invoiceNo2}'";
                    echo $sqlCheckInvoiceNo2;
                    $checkInvoiceNo2 = $myDatabase->query($sqlCheckInvoiceNo2, MYSQLI_STORE_RESULT);

                    if ($checkInvoiceNo2->num_rows > 0) {
                        $invoiceNo2Check = false;
                    } else {
                        $invoiceNo2Check = true;
                    }

                    if ($invoiceNo2Check) {
                        //TransaksiPO = 2 True
                        if (isset($transaksiPO) && $transaksiPO == 2) {
                            $idPOHDR = $_POST['idPOHDR'];

                            // <editor-fold defaultstate="collapsed" desc="Query for GET PO HEADER">
                            $sql = "SELECT gv.general_vendor_name as vendor_name,p.*,DATE_FORMAT(p.tanggal,'%d/%m/%Y') as date,s.*FROM po_hdr p 
                            LEFT JOIN general_vendor gv ON gv.general_vendor_id = p.general_vendor_id
                            LEFT JOIN stockpile s ON p.stockpile_id = s.stockpile_id WHERE idpo_hdr = {$idPOHDR}";
                            echo 'SQL GET PO HDR = ' . $sql;
                            $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                            if ($resultData !== false && $resultData->num_rows > 0) {
                                $rowData = $resultData->fetch_object();
//                                $requestDate = $rowData->date;
                                $stockpile = $rowData->stockpile_name;
                                $stockpileId = $rowData->stockpile_id;
                                $noPO = $rowData->no_po;
                                $generalVendorId = $rowData->general_vendor_id;
                                $vendorName = $rowData->vendor_name;
                                $gvBankId = $rowData->bank_id;
                                $currencyId = $rowData->currency_id;
                                $rate = $rowData->exchangerate;
                            }
                            // </editor-fold>

                            // <editor-fold defaultstate="collapsed" desc="Query for INSERT Pengajuan General">

                            //SQL CHECK TERMIN
                            $sql = "SELECT pg.termin FROM pengajuan_general pg WHERE pg.po_id = {$idPOHDR} AND pg.status_pengajuan IN (0,1) ";
                            $resCheckTermin = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                            $termin = 0;
                            if ($resCheckTermin->num_rows > 0) {
                                while ($row = $resCheckTermin->fetch_object()) {
                                    $termin += $row->termin;
                                }
                            }
                            $termin = $termin + $terminHeader;
                            echo "Satu" . $sql;
                            echo 'termin = ' . $termin;

                            //Check Termin
                            $transaksiMutasi = 1;
                            if ($termin > 100) {
                                echo '|FAIL| Termin Lebih Dari 100%';
                                die();
                            } else {
                                $sql = "INSERT INTO `pengajuan_general` (transaction_po,termin,invoice_date,transaction_type,payment_type,request_payment_date,request_date, tax_date, invoice_method, stockpileId, pengajuan_no, invoice_no2, invoice_tax, remarks,po_id, file, company_id, entry_by, entry_date) VALUES ("
                                    . "{$transaksiPO},{$terminHeader},STR_TO_DATE('{$invoiceDate}', '%d/%m/%Y'),{$transaksiMutasi},{$paymentType},{$reqPaymentDate},STR_TO_DATE('{$requestDate}', '%d/%m/%Y'), STR_TO_DATE('{$taxDate}', '%d/%m/%Y'), {$invoiceMethod},  {$stockpileId}, '{$pengajuanNo}', '{$invoiceNo2}', '{$invoiceTax}', '{$remarks}',{$idPOHDR},"
                                    . "'{$attachmentPath}', {$_SESSION['companyId']}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                echo $sql;
                                $pgId = $myDatabase->insert_id;
                            }


                            //Update Status PO
                            if ($termin == 100) {
                                $sqlUpdateStatusPO = "UPDATE po_hdr SET status = 5 where idpo_hdr = {$idPOHDR}";
                                $resUpdateSTPO = $myDatabase->query($sqlUpdateStatusPO, MYSQLI_STORE_RESULT);
                            }
                            // </editor-fold>

                            // <editor-fold defaultstate="collapsed" desc="Query for INSERT Pengajuan General Detail">
                            $sqlPODetail = "SELECT a.account_id as accountId,a.account_type as accountType,pd.*, i.item_name, (case when pd.pphstatus = 1 then pd.pph else 0 end) as pph,
			                    (case when pd.ppnstatus = 1 then pd.ppn else 0 end) as ppn,
			                    (pd.amount+(case when pd.ppnstatus = 1 then pd.ppn else 0 end)-(case when pd.pphstatus = 1 then pd.pph else 0 end)) as grandtotal,
                                u.uom_type,s.`stockpile_name`, sh.`shipment_no`, pd.idpo_detail
                                from po_detail pd
                                left join master_item i on i.idmaster_item = pd.item_id
								LEFT JOIN master_groupitem mg ON mg.idmaster_groupitem = i.group_itemid
											LEFT JOIN ACCOUNT a ON a.account_id = mg.account_id
                                left join uom u on u.idUOM = i.uom_id
                                LEFT JOIN stockpile s ON s.`stockpile_id` = pd.`stockpile_id`
                                LEFT JOIN shipment sh ON sh.`shipment_id` = pd.`shipment_id`
                                WHERE no_po = '{$noPO}' ORDER BY idpo_detail ASC";

                            $resultPODetail = $myDatabase->query($sqlPODetail, MYSQLI_STORE_RESULT);

                            $tamount = 0;
                            $index = 0;
                            while ($row = $resultPODetail->fetch_object()) {
                                $invoiceMethodDetail = $row->method;
                                $invoiceType = isset($row->accountType) ? $row->accountType : 0;
                                $accountId = isset($row->accountId) ? $row->accountId : 0;
                                $poId = 0;
                                $poDetailId = $row->idpo_detail;
                                $shipmentId1 = isset($row->shipment_id) ? $row->shipment_id : 0;
                                $stockpileId2 = isset($row->stockpile_id) ? $row->stockpile_id : 0;
                                $qty = $row->qty;
                                $price = $row->harga;
                                $termin = $terminHeader;
                                $amount2 = $row->amount;
                                $amountConverted = $amount2;
                                if ($currencyId == 1) {
                                    $exchangeRate = 1;
                                } else {
                                    $exchangeRate = $rate;
                                }
                                $ppnID = $row->ppn_id;
                                $ppn2 = $row->ppn;
                                $ppnConverted = $ppn2;
                                $pphID = $row->pph_id;
                                $pph2 = $row->pph;
                                $pphConverted = $pph2;
                                $tamount = ($amount2 + $ppn2 - $pph2);
                                $tamount = $tamount * $termin / 100;
                                $tamountConverted = $tamount;
                                $dpAmount = 0;
                                $vendorType = 'General';

                                $notes = $row->notes;
                                $gvBankId = isset($gvBankId) ? $gvBankId : 0;

                                $sql = "INSERT INTO `pengajuan_general_detail` (po_detail_id,pg_id,invoice_method_detail, type, account_id, poId, vendor_type ,vendor_name,general_vendor_id, shipment_id, stockpile_remark, qty, price, termin, amount, amount_converted, currency_id, exchange_rate, ppnID, ppn, ppn_converted, pphID, pph, pph_converted, tamount, tamount_converted, dp_amount, notes,gv_bank_id, entry_by, entry_date) VALUES ("
                                    . "{$poDetailId},{$pgId},{$invoiceMethodDetail},{$invoiceType},{$accountId}, {$poId},'{$vendorType}','{$vendorName}', {$generalVendorId}, {$shipmentId1}, {$stockpileId2}, '{$qty}', '{$price}', '{$termin}', '{$amount2}', '{$amountConverted}', {$currencyId}, '{$exchangeRate}', {$ppnID} ,'{$ppn2}', '{$ppnConverted}', {$pphID}, '{$pph2}', '{$pphConverted}', '{$tamount}', '{$tamountConverted}', '{$dpAmount}', '{$notes}',{$gvBankId} ,{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
								echo "DUA " . $sql;
                            }
                            // </editor-fold>

                        } else {
                            $transaksiMutasi = 1;
                            // <editor-fold defaultstate="collapsed" desc="Query for INSERT Pengajuan General">
                            $sql = "INSERT INTO `pengajuan_general` (transaction_po,invoice_date,transaction_type,payment_type,request_payment_date,request_date, tax_date, invoice_method, stockpileId, pengajuan_no, invoice_no2, invoice_tax, remarks,po_id, file, company_id, entry_by, entry_date) VALUES ("
                            . "{$transaksiPO},STR_TO_DATE('{$invoiceDate}', '%d/%m/%Y'),{$transaksiMutasi},{$paymentType},{$reqPaymentDate},STR_TO_DATE('{$requestDate}', '%d/%m/%Y'), STR_TO_DATE('{$taxDate}', '%d/%m/%Y'), {$invoiceMethod},  {$stockpileId}, '{$pengajuanNo}', '{$invoiceNo2}', '{$invoiceTax}','{$remarks}',NULL,"
                            . "'{$attachmentPath}', {$_SESSION['companyId']}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                            $pgId = $myDatabase->insert_id;
                            
                            $sqlUpdatePGD = "UPDATE pengajuan_general_detail SET pg_id = $pgId WHERE pg_id IS NULL AND entry_by = {$_SESSION['userId']}";
                            $result = $myDatabase->query($sqlUpdatePGD, MYSQLI_STORE_RESULT);

                        }
                    } else {
                        echo '|FAIL| Original Invoice No Tidak Boleh Sama!';
                        die();
                    }

                } elseif ($_POST['_method'] == 'UPDATE') {
                    // <editor-fold defaultstate="collapsed" desc="Update Pengajuan General">
                  
                    $pgId = $_POST['pgId'];
                    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
                        $sql = "UPDATE `pengajuan_general` SET "
                            . "invoice_date = STR_TO_DATE('{$invoiceDate}', '%d/%m/%Y'), "
                            . "tax_date = STR_TO_DATE('{$taxDate}', '%d/%m/%Y'), "
                            . "stockpileId = '{$stockpileId}', "
                            . "invoice_no2 = '{$invoiceNo2}', "
                            . "payment_type = '{$paymentType}', "
                            . "request_payment_date = {$reqPaymentDate}, "
                            . "request_date = STR_TO_DATE('{$requestDate}', '%d/%m/%Y'), "
                            . "invoice_tax = '{$invoiceTax}', "
                            . "file = '{$attachmentPath}', "
                            . "remarks = '{$remarks}', "
                            . "status_pengajuan = 0 "
                            . "WHERE pengajuan_general_id = {$pgId}";
                    } else {
                        $sql = "UPDATE `pengajuan_general` SET "
                            . "invoice_date = STR_TO_DATE('{$invoiceDate}', '%d/%m/%Y'), "
                            . "tax_date = STR_TO_DATE('{$taxDate}', '%d/%m/%Y'), "
                            . "stockpileId = '{$stockpileId}', "
                            . "invoice_no2 = '{$invoiceNo2}', "
                            . "invoice_tax = '{$invoiceTax}', "
                            . "payment_type = '{$paymentType}', "
                            . "request_date = STR_TO_DATE('{$requestDate}', '%d/%m/%Y'), "
                            . "request_payment_date = {$reqPaymentDate}, "
                            . "remarks = '{$remarks}', "
                            . "status_pengajuan = 0 "
                            . "WHERE pengajuan_general_id = {$pgId}";
                    }
                    if ($_POST['generalVendorId'] != '' || $_POST['gvBankId']) {
                        $sqlUpdatePGD = "UPDATE pengajuan_general_detail SET general_vendor_id = {$_POST['generalVendorId']}, gv_bank_id = {$_POST['gvBankId']} WHERE pg_id = {$pgId}";
                        $resultPGD = $myDatabase->query($sqlUpdatePGD, MYSQLI_STORE_RESULT);
                    }
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                    // </editor-fold>
                } elseif ($_POST['_method'] == 'APPROVE') {
                    // <editor-fold defaultstate="collapsed" desc="Approve Pengajuan General">

                    $pgId = $_POST['pgId'];

                    $sqlUpdatePG = "UPDATE `pengajuan_general` SET input_date = STR_TO_DATE('{$inputDate}', '%d/%m/%Y'), status_pengajuan = 1,
                                    approved_by = {$_SESSION['userId']}, approved_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')
                                    WHERE pengajuan_general_id = {$pgId}";
                    $updatePG = $myDatabase->query($sqlUpdatePG, MYSQLI_STORE_RESULT);

//                    $sql = "select DATE_FORMAT(STR_TO_DATE('{$inputDate}', '%d/%m/%Y'), '%y%m') AS slip_prefix from dual";
//                    $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//                    $rowSlip = $resultSlip->fetch_object();
//                    $invoiceYearMonth = $rowSlip->slip_prefix;

//                    $checkInvoiceNo = 'INV/JPJ/' . $currentYearMonth;

//                    $sql = "SELECT invoice_no FROM invoice WHERE company_id = {$_SESSION['companyId']} AND invoice_no LIKE '{$checkInvoiceNo}%' ORDER BY invoice_id DESC LIMIT 1";
//                    $resultInvoice = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//                    if ($resultInvoice->num_rows == 1) {
//                        $rowInvoice = $resultInvoice->fetch_object();
//                        $splitInvoiceNo = explode('/', $rowInvoice->invoice_no);
//                        $lastExplode = count($splitInvoiceNo) - 1;
//                        $nextInvoiceNo = ((float)$splitInvoiceNo[$lastExplode]) + 1;
//                        $invoiceNo = $checkInvoiceNo . '/' . $nextInvoiceNo;
//                    } else {
//                        $invoiceNo = $checkInvoiceNo . '/1';
//                    }

//                    $sqlGetPengajuan = "SELECT * FROM pengajuan_general WHERE pengajuan_general_id = {$pgId}";
//                    $pg = $myDatabase->query($sqlGetPengajuan, MYSQLI_STORE_RESULT);
// //                    //Insert TO Invoice
//                    while ($rowPG = $pg->fetch_object()) {
//                        $poId = isset($rowPG->po_id) ? $rowPG->po_id : 'NULL';
//                        $sqlInsertPg = "INSERT INTO `invoice` (invoice_date,request_date, tax_date, invoice_method,po_id,
//                                        stockpileId, invoice_no, invoice_no2, invoice_tax, remarks, file, company_id, entry_by, entry_date) VALUES ("
//                            . "STR_TO_DATE('{$rowPG->invoice_date}', '%Y-%m-%d'),STR_TO_DATE('{$rowPG->request_date}', '%Y-%m-%d'), STR_TO_DATE('{$rowPG->tax_date}', '%Y-%m-%d'),
//                            {$rowPG->invoice_method},{$poId},{$rowPG->stockpileId}, '{$invoiceNo}', '{$rowPG->invoice_no2}', '{$rowPG->invoice_tax}', '{$rowPG->remarks}',"
//                            . "'{$rowPG->file}', {$_SESSION['companyId']}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
//                        $resPG = $myDatabase->query($sqlInsertPg, MYSQLI_STORE_RESULT);

//                        $invId = $myDatabase->insert_id;
//                    }

// //                    //edit by yeni
// //                    //Update status Logbook
//                     $updateLog = "UPDATE `logbook_new` SET invoice_id = {$invId}, status1 = 1 WHERE pinvoice_id = {$pgId}";
//                     $resultUL = $myDatabase->query($updateLog, MYSQLI_STORE_RESULT);
// //                    //Update Status Invoice
//                    $updatePG = "UPDATE `pengajuan_general` SET invoice_id = {$invId}, invoice_status = 1 WHERE pengajuan_general_id = {$pgId}";
//                    $resultPG = $myDatabase->query($updatePG, MYSQLI_STORE_RESULT);

//                    $sqlGetPengajuanDetail = "SELECT * FROM pengajuan_general_detail WHERE pg_id = {$pgId}";
//                    $pgd = $myDatabase->query($sqlGetPengajuanDetail, MYSQLI_STORE_RESULT);
//                    $result = false;
// //                    //Insert Invoice Detail
//                    while ($rowPGD = $pgd->fetch_object()) {
//                        $mutasiDetailId = isset($rowPGD->mutasi_detail_id) ? $rowPGD->mutasi_detail_id : 'NULL';

//                        $pgdPOID = isset($rowPGD->poId) ? $rowPGD->poId : 0;
//                        $pgdShipmentID = isset($rowPGD->shipment_id) ? $rowPGD->shipment_id : 0;
//                        $gvBankId = isset($rowPGD->gv_bank_id) ? $rowPGD->gv_bank_id : 0;

//                        $sqlID = "INSERT INTO `invoice_detail` (pgd_id,invoice_id,invoice_method_detail, type, account_id, poId,vendor_type,vendor_name, general_vendor_id, shipment_id,
//                                    stockpile_remark, qty, price, termin, amount, amount_converted, currency_id, exchange_rate, ppnID, ppn, ppn_converted, pphID, pph,
//                                    pph_converted, tamount, tamount_converted, dp_amount, notes,gv_bank_id, entry_by, entry_date, mutasi_detail_id) VALUES ("
//                            . "{$rowPGD->pgd_id},{$invId},{$rowPGD->invoice_method_detail}, {$rowPGD->type}, {$rowPGD->account_id},{$pgdPOID},'{$rowPGD->vendor_type}','{$rowPGD->vendor_name}', {$rowPGD->general_vendor_id},
//                                 {$pgdShipmentID}, $rowPGD->stockpile_remark,$rowPGD->qty,$rowPGD->price, {$rowPGD->termin}, {$rowPGD->amount}, {$rowPGD->amount_converted}, '{$rowPGD->currency_id}',
//                                  {$rowPGD->exchange_rate} ,'{$rowPGD->ppnID}', '{$rowPGD->ppn}',{$rowPGD->ppn_converted},'{$rowPGD->pphID}', '{$rowPGD->pph}', '{$rowPGD->pph_converted}', '{$rowPGD->tamount}', '{$rowPGD->tamount_converted}', '{$rowPGD->dp_amount}', '{$rowPGD->notes}',{$gvBankId},{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$mutasiDetailId})";

//                        $resultID = $myDatabase->query($sqlID, MYSQLI_STORE_RESULT);

//                        $invoiceId = $myDatabase->insert_id;

//                        //    insertGeneralLedger($myDatabase, 'INVOICE DETAIL', $invoiceId);
//                    }

                    $result = true;
                    // </editor-fold>
                }

                if ($result !== false) {
                    if ($_POST['_method'] == 'INSERT') {
                        $pg_id = $myDatabase->insert_id;
                        //add by yeni
                        $sqlLog = "INSERT INTO logbook_new (pinvoice_id, type_pengajuan, entry_date, status1) values ({$pgId}, 2, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),0)";
                        $resultLog = $myDatabase->query($sqlLog, MYSQLI_STORE_RESULT);
                        if ($resultLog !== false) {
                            $logbook_id = $myDatabase->insert_id;
                        }

                    }
                    $return_value = '|OK|Pengajuan has successfully inserted/updated.' . $addMessage . '||';
                    // unset($_SESSION['invoice']);

                } else {
                    $return_value = '|FAIL|Insert/update invoice failed.||';
                }
            } else {
                $return_value = '|FAIL|Please insert correct amount.' . $addMessage . '||';
            }
        } else {
            $return_value = '|FAIL|Please fill the required fields ||';
        }
        // </editor-fold>
    }
    echo $return_value;
// </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'pengajuan_general_detail') {
    // <editor-fold defaultstate="collapsed" desc="CRUD PENGAJUAN GENERAL DETAIL">

    if (isset($_POST['_method']) && $_POST['_method'] == 'DELETE') {
        // <editor-fold defaultstate="collapsed" desc="DELETE PENGAJUAN GENERAL DETAIL">
        if (isset($_POST['pgdId']) && $_POST['pgdId'] != '') {

            $deleteQTY = "DELETE FROM mutasi_qty_price WHERE pgd_id = {$_POST['pgdId']}";
            $delQTY = $myDatabase->query($deleteQTY, MYSQLI_STORE_RESULT);
            $result = false;
            if ($delQTY !== false) {
                $sql = "DELETE FROM `pengajuan_general_detail` WHERE pgd_id = {$_POST['pgdId']}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            }

            if ($result !== false) {
                $return_value = '|OK|Pengajuan General Detail has successfully deleted.|';
            } else {
                $return_value = '|FAIL|Delete Pengajuan General Detail failed.|';
            }
        } else {
            $return_value = '|FAIL|Record not found.|';
        }
        echo $return_value;
        // </editor-fold>
    } elseif (isset($_POST['_method']) && $_POST['_method'] == 'UPDATE') {
        // <editor-fold defaultstate="collapsed" desc="UPDATE PENGAJUAN GENERAL DETAIL">
        if (isset($_POST['pgdId']) && $_POST['pgdId'] != '') {
            $poId = isset($_POST['poId']) && $_POST['poId'] != '' ? $_POST['poId'] : 0;
            $shipmentId = isset($_POST['shipmentId1']) && $_POST['shipmentId1'] != '' ? $_POST['shipmentId1'] : 0;

            $sql = "UPDATE pengajuan_general_detail SET account_id = {$_POST['accountId']}, type = {$_POST['invoiceType']},"
                . "stockpile_remark = {$_POST['stockpileId2']},"
                . "shipment_id = {$shipmentId},"
                . "poId = {$poId},"
                . "qty = {$_POST['qty']},"
                . "price = {$_POST['price']},"
                . "termin = {$_POST['termin']},"
                . "amount = {$_POST['amount']},"
                . "notes = '{$_POST['notes']}'"
                . " WHERE pgd_id = {$_POST['pgdId']}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            echo $sql;
            if ($result !== false) {
                $return_value = '|OK|Pengajuan General Detail has successfully to Update data.|';
            } else {
                $return_value = '|FAIL|Pengajuan General Detail failed to Update data.|';
            }
        } else {
            $return_value = '|FAIL|Record not found.|';
        }
        echo $return_value;
        // </editor-fold>
    } else {
        // <editor-fold defaultstate="collapsed" desc="INSERT PENGAJUAN GENERAL DETAIL">

        $return_value = '';
        $boolShipment1 = false;
        $boolPO = false;
        $boolVendor = false;
        //$grandTotal = 0;
        // <editor-fold defaultstate="collapsed" desc="POST variables">
        $invMethod = 2;
        $invoiceType = $myDatabase->real_escape_string($_POST['invoiceType']);
        $accountId = $myDatabase->real_escape_string($_POST['accountId']);
        $generalVendorId = $myDatabase->real_escape_string($_POST['generalVendorId']);
        $shipmentId1 = $myDatabase->real_escape_string($_POST['shipmentId1']);
        $stockpileId2 = $myDatabase->real_escape_string($_POST['stockpileId2']);
        $qty = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty']));
        $price = str_replace(",", "", $myDatabase->real_escape_string($_POST['price']));
        $termin = str_replace(",", "", $myDatabase->real_escape_string($_POST['terminDetail']));
        $amount = str_replace(",", "", $myDatabase->real_escape_string($_POST['amount']));
        $currencyId = $myDatabase->real_escape_string($_POST['currencyId']);
        $exchangeRate = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
        $ppn = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppn1']));
        //$pph = str_replace(",", "", $myDatabase->real_escape_string($_POST['pph1']));
        $pphID = $myDatabase->real_escape_string($_POST['pphTaxId']);
        $ppnID = $myDatabase->real_escape_string($_POST['ppnID']);
        $pphDP2 = $myDatabase->real_escape_string($_POST['pphDP2']);
        $ppnDP2 = $myDatabase->real_escape_string($_POST['ppnDP2']);
        //$grandTotal = str_replace(",", "", $myDatabase->real_escape_string($_POST['grandTotal']));
        //$DP = $grandTotal;
        $dp_total = str_replace(",", "", $myDatabase->real_escape_string($_POST['dp_total']));
        $DP = $dp_total;
        $notes = $myDatabase->real_escape_string($_POST['notes']);
        $invoiceMethodDetail = $myDatabase->real_escape_string($_POST['invoiceMethodDetail']);
        $poId = $myDatabase->real_escape_string($_POST['poId']);
        $gvBankId = $myDatabase->real_escape_string($_POST['gvBankId']);
        $slipNos = "";
        $slipNos2 = "";
        $transaksiMutasi = $myDatabase->real_escape_string($_POST['transaksiMutasi']);
        $transaksiPO = $myDatabase->real_escape_string($_POST['transaksiPO']);
        $mutasiValue = "";
        $mutasiChecked = "";
        // </editor-fold>
        if ($shipmentId1 == '') {
            $shipmentId1 = 'NULL';
        } else {

        }
        if ($poId == '') {
            $poId = 'NULL';
        }
        if ($stockpileId2 == '') {
            $stockpileId2 = 'NULL';
        }

        if ($exchangeRate == '') {
            $exchangeRate = 1;
        }
        if ($pphDP2 != 0) {
            $sqlPPH = "SELECT tax_value FROM tax WHERE tax_id = {$pphID}";
            $resultPPH = $myDatabase->query($sqlPPH, MYSQLI_STORE_RESULT);
            if ($resultPPH !== false && $resultPPH->num_rows > 0) {
                $rowPPH = $resultPPH->fetch_object();
                $pph1 = $rowPPH->tax_value;
                $dp_pph = $DP * ($pph1 / 100);
            }
        } else {
            $dp_pph = 0;
        }
        if ($ppnDP2 != 0) {
            $sqlPPN = "SELECT tax_value FROM tax WHERE tax_id = {$ppnID}";
            $resultPPN = $myDatabase->query($sqlPPN, MYSQLI_STORE_RESULT);
            if ($resultPPN !== false && $resultPPN->num_rows > 0) {
                $rowPPN = $resultPPN->fetch_object();
                $ppn1 = $rowPPN->tax_value;
                $dp_ppn = $DP * ($ppn1 / 100);
            }
        } else {
            $dp_ppn = 0;
        }

        $dp_total = ($DP + $dp_ppn) - $dp_pph;

        $pph = 0;
        if ($pphID != 0) {
            $sqlP = "SELECT tax_value FROM tax WHERE tax_id = {$pphID}";
            $resultP = $myDatabase->query($sqlP, MYSQLI_STORE_RESULT);
            if ($resultP !== false && $resultP->num_rows > 0) {
                $rowP = $resultP->fetch_object();
                $pphTax = $rowP->tax_value;

                $pph = $amount * ($pphTax / 100);
            }
        }

        if ($invMethod == 1) {
            $amount2 = $amount * -1;
            $ppn2 = $ppn * -1;
            $pph2 = $pph * -1;
        } else {
            $amount2 = $qty * $price;
            $ppn2 = $ppn;
            $pph2 = $pph;
        }

        $sqlShipment = "SELECT account_no FROM account WHERE account_id = {$accountId}";
        $resultShipment = $myDatabase->query($sqlShipment, MYSQLI_STORE_RESULT);
        if ($resultShipment !== false && $resultShipment->num_rows > 0) {
            $rowShipment = $resultShipment->fetch_object();
            $acc = $rowShipment->account_no;
            $sub_acc = substr($acc, 0, 2);
        } else {
            $acc = 0;
        }

        if ($sub_acc == 51 && $shipmentId1 == 'NULL') {
            $boolShipment1 = false;
        } else {
            $boolShipment1 = true;
            //echo 'SALAH';
        }
        if (($acc == 520900 || $acc == 521000) && ($poId == 'NULL' && $shipmentId1 == 'NULL')) {
            $boolPO = false;
        } elseif (($acc == 520900 || $acc == 521000) && ($poId != 'NULL' || $shipmentId1 != 'NULL')) {
            $boolPO = true;
        } else {
            $boolPO = true;
        }

        //cek vendor
        $gvId = '';
        $sqlv = "SELECT general_vendor_id FROM invoice_detail WHERE invoice_id IS NULL AND entry_by = {$_SESSION['userId']} GROUP BY general_vendor_id";
        $resultv = $myDatabase->query($sqlv, MYSQLI_STORE_RESULT);
        if ($resultv !== false && $resultv->num_rows == 1) {
            $rowv = $resultv->fetch_object();
            $gvId = $rowv->general_vendor_id;
            if ($gvId == $generalVendorId) {
                $boolVendor = true;
            }
        }
        if ($gvId == '') {
            $boolVendor = true;
        }

        if ($transaksiMutasi == 2) {

            if (isset($_POST['checkedMutasi1'])) {
                $mutasi1 = $_POST['checkedMutasi1'];
                if (isset($_POST['checkedMutasi'])) {
                    $mutasi = $_POST['checkedMutasi'];
                    if (isset($_POST['checkedMutasi3'])) {
                        $mutasi3 = $_POST['checkedMutasi3'];
                    } else {
                        $mutasi3 = '';
                    }
                    if (isset($_POST['checkedMutasi4'])) {
                        $mutasi4 = $_POST['checkedMutasi4'];
                    } else {
                        $mutasi4 = '';
                    }
                    if (isset($_POST['checkedMutasi5'])) {
                        $mutasi5 = $_POST['checkedMutasi5'];
                    } else {
                        $mutasi5 = '';
                    }
                    if (isset($_POST['checkedMutasi6'])) {
                        $mutasi6 = $_POST['checkedMutasi6'];
                    } else {
                        $mutasi6 = '';
                    }
                } else {
                    $mutasi = '';
                }

                for ($i = 0; $i < sizeof($mutasi1); $i++) {
                    if ($mutasi[$i] != '') {
                        if ($mutasiChecked == '') {
                            $mutasiChecked .= $mutasi[$i];
                            if ($mutasi4[$i] == '') {
                                $mutasi4[$i] = 0;
                                $ppnAmt = 0;
                            } else {
                                //$gvId = '';
                                $sqlPrice = "SELECT a.price,a.ppn,
											IFNULL((SELECT SUM(ppnAmt) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumppn,
											IFNULL((SELECT SUM(qtyInvoice) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumqty FROM mutasi_detail a WHERE a.mutasi_detail_id = " . $mutasi[$i];
                                $resultPrice = $myDatabase->query($sqlPrice, MYSQLI_STORE_RESULT);
                                if ($resultPrice !== false && $resultPrice->num_rows == 1) {
                                    $rowPrice = $resultPrice->fetch_object();
                                    $total_per_termin = $rowPrice->total_per_termin;
                                    $ppn = $rowPrice->ppn;
                                    $sumppn = $rowPrice->sumppn;
                                    $price = $rowPrice->price;
                                    $sumqty = $rowPrice->sumqty;
                                    $ppnPlus = $price * $mutasi3[$i];

                                    $ppnAmt1 = (($sumqty * $price) + $ppnPlus) * ($ppn / 100);
                                    $ppnAmt = $ppnAmt1 - $sumppn;
                                }

                            }
                            if ($mutasi5[$i] == '') {
                                $mutasi5[$i] = 0;
                                $pphAmt = 0;
                            } else {

                                $sqlPrice1 = "SELECT a.price,a.pph,
											IFNULL((SELECT SUM(pphAmt) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumpph,
											IFNULL((SELECT SUM(qtyInvoice) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumqty FROM mutasi_detail a WHERE a.mutasi_detail_id = " . $mutasi[$i];
                                $resultPrice1 = $myDatabase->query($sqlPrice1, MYSQLI_STORE_RESULT);
                                if ($resultPrice1 !== false && $resultPrice1->num_rows == 1) {
                                    $rowPrice1 = $resultPrice1->fetch_object();
                                    $total_per_termin = $rowPrice1->total_per_termin;
                                    $pph = $rowPrice1->pph;
                                    $sumpph = $rowPrice1->sumpph;
                                    $price = $rowPrice1->price;
                                    $sumqty = $rowPrice1->sumqty;
                                    $pphPlus = $price * $mutasi3[$i];
                                    $pphAmt1 = (($sumqty * $price) + $pphPlus) * ($pph / 100);
                                    $pphAmt = $pphAmt1 - $sumpph;

                                }
                            }
                            if ($mutasi6[$i] == '') {
                                $mutasi6[$i] = 0;
                            } else {
                                $mutasi6[$i] = 1;
                            }


                            $dataCek = $mutasi[$i];
                            $mutasiValue .= '(' . $mutasi[$i] . ',' . $mutasi1[$i] . ',' . $mutasi3[$i] . ',' . $mutasi4[$i] . ',' . $mutasi5[$i] . ',' . $ppnAmt . ',' . $pphAmt . ',' . $mutasi6[$i] . ')';
                            $terminCek .= $mutasi1[$i];
                            $query .= 'SELECT IFNULL((IFNULL(SUM(termin),0)+' . $mutasi1[$i] . '),0) AS totalTermin FROM mutasi_qty_price WHERE mutasi_detail_id = ' . $mutasi[$i];


                        } else {
                            $mutasiChecked .= ',' . $mutasi[$i];
                            if ($mutasi4[$i] == '') {
                                $mutasi4[$i] = 0;
                                $ppnAmt = 0;
                            } else {
                                //$gvId = '';
                                $sqlPrice = "SELECT a.price,a.ppn,
											IFNULL((SELECT SUM(ppnAmt) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumppn,
											IFNULL((SELECT SUM(qtyInvoice) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumqty FROM mutasi_detail a WHERE a.mutasi_detail_id = " . $mutasi[$i];
                                $resultPrice = $myDatabase->query($sqlPrice, MYSQLI_STORE_RESULT);
                                if ($resultPrice !== false && $resultPrice->num_rows == 1) {
                                    $rowPrice = $resultPrice->fetch_object();
                                    $total_per_termin = $rowPrice->total_per_termin;
                                    $ppn = $rowPrice->ppn;
                                    $sumppn = $rowPrice->sumppn;
                                    $price = $rowPrice->price;
                                    $sumqty = $rowPrice->sumqty;
                                    $ppnPlus = $price * $mutasi3[$i];


                                    $ppnAmt1 = (($sumqty * $price) + $ppnPlus) * ($ppn / 100);
                                    $ppnAmt = $ppnAmt1 - $sumppn;

                                }

                            }
                            if ($mutasi5[$i] == '') {
                                $mutasi5[$i] = 0;
                                $pphAmt = 0;
                            } else {

                                $sqlPrice1 = "SELECT a.price,a.pph,
											IFNULL((SELECT SUM(pphAmt) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumpph,
											IFNULL((SELECT SUM(qtyInvoice) FROM mutasi_qty_price  WHERE mutasi_detail_id = a.mutasi_detail_id),0) AS sumqty FROM mutasi_detail a WHERE a.mutasi_detail_id = " . $mutasi[$i];
                                $resultPrice1 = $myDatabase->query($sqlPrice1, MYSQLI_STORE_RESULT);
                                if ($resultPrice1 !== false && $resultPrice1->num_rows == 1) {
                                    $rowPrice1 = $resultPrice1->fetch_object();
                                    $total_per_termin = $rowPrice1->total_per_termin;
                                    $pph = $rowPrice1->pph;
                                    $sumpph = $rowPrice1->sumpph;
                                    $price = $rowPrice1->price;
                                    $sumqty = $rowPrice1->sumqty;
                                    $pphPlus = $price * $mutasi3[$i];
                                    $pphAmt1 = (($sumqty * $price) + $pphPlus) * ($pph / 100);
                                    $pphAmt = $pphAmt1 - $sumpph;

                                }
                            }

                            if ($mutasi6[$i] == '') {
                                $mutasi6[$i] = 0;
                            } else {
                                $mutasi6[$i] = 1;
                            }
                            $dataCek = $mutasi[$i];
                            $mutasiValue .= '(' . $mutasi[$i] . ',' . $mutasi1[$i] . ',' . $mutasi3[$i] . ',' . $mutasi4[$i] . ',' . $mutasi5[$i] . ',' . $ppnAmt . ',' . $pphAmt . ',' . $mutasi6[$i] . ')';
                            $terminCek .= $mutasi1[$i];
                            $query .= ' UNION SELECT IFNULL((IFNULL(SUM(termin),0)+' . $mutasi1[$i] . '),0) AS totalTermin FROM mutasi_qty_price WHERE mutasi_detail_id = ' . $mutasi[$i];
                        }
                    }
                }
            }

            $boolTermin = true;
            $sqlTermin = "{$query}";
            $resultTermin = $myDatabase->query($sqlTermin, MYSQLI_STORE_RESULT);
            if ($resultTermin !== false && $resultTermin->num_rows > 0) {
                while ($rowT = $resultTermin->fetch_object()) {

                    $totalTermin = $rowT->totalTermin;
                    if ($totalTermin > 100) {
                        $boolTermin = false;
                        break;
                    } else {
                        $boolTermin = true;
                    }

                }
            }

            if ($boolTermin) {

                $sql1 = "SELECT a.general_vendor_id
				FROM mutasi_detail a 
				WHERE a.mutasi_detail_id IN ({$mutasiChecked})";
                $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                $gvId = array();
                while ($rowCheck = $result1->fetch_array()) {
                    $gvId[] = $rowCheck['general_vendor_id'];
                }

                if (count(array_unique($gvId)) === 1) {
                    $sqlQP = "INSERT INTO `mutasi_qty_price` (mutasi_detail_id, termin, qtyInvoice,ppnId,pphId,ppnAmt,pphAmt,qtyEstimasi) VALUES {$mutasiValue}";
                    $resultQP = $myDatabase->query($sqlQP, MYSQLI_STORE_RESULT);
                    if ($resultQP !== false) {

                        $mutasiPrice = $myDatabase->insert_id;

                        $boolTax = false;

                        $sqlCek = "SELECT * FROM mutasi_qty_price WHERE mutasi_detail_id = {$dataCek}";
                        $resultCek = $myDatabase->query($sqlCek, MYSQLI_STORE_RESULT);
                        if ($resultCek !== false && $resultCek->num_rows > 0) {
                            while ($rowCek = $resultCek->fetch_object()) {

                                $boolTax = true;

                            }
                        }

                        if ($boolTax) {
                            $whereProperty = "AND aa.id != {$mutasiPrice}";
                        } else {
                            $whereProperty = "";
                        }
                    }

                    $sql1 = "SELECT a.*, c.termin, c.qtyInvoice, c.id, c.ppnId AS ppnIdInvoice, c.pphId AS pphIdInvoice, IFNULL(d.tax_value,0) AS pphinvoice, IFNULL(e.tax_value,0) AS ppninvoice, (a.price * c.qtyInvoice) AS amount1234, (a.price * c.qtyInvoice) AS amount2,
    (IFNULL((SELECT SUM(a.price * aa.qtyInvoice) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id ),0)) AS amount,
    (((a.total_per_termin + a.ppn_converted) - a.pph_converted) - 
    ((IFNULL((SELECT SUM(a.price * aa.qtyInvoice) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id ),0)) + 
    (IFNULL((SELECT SUM(aa.ppnAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id ),0)))+ 
    (IFNULL((SELECT SUM(aa.pphAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id ),0))) AS availableAmount, 
    (SELECT SUM(termin) FROM mutasi_qty_price WHERE  mutasi_detail_id = a.mutasi_detail_id) AS terminTotal,
    (IFNULL((SELECT SUM(aa.ppnAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id {$whereProperty}),0)) AS ppnAmount,
    (IFNULL((SELECT SUM(aa.pphAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id {$whereProperty}),0)) AS pphAmount,
    (IFNULL((SELECT SUM(aa.ppnAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id),0)) AS ppnAmount2,
    (IFNULL((SELECT SUM(aa.pphAmt) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id),0)) AS pphAmount2,
    (SELECT stockpile_from FROM mutasi_header WHERE mutasi_header_id = a.mutasi_header_id GROUP BY a.mutasi_header_id) AS stockpileId2,
    (a.price * (IFNULL((SELECT SUM(aa.qtyInvoice) FROM mutasi_qty_price aa WHERE aa.mutasi_detail_id = a.mutasi_detail_id ),0))) AS updateTotal, c.qtyEstimasi
    FROM mutasi_detail a
				LEFT JOIN termin_detail b ON a.termin_detail_id = b.id 
				LEFT JOIN mutasi_qty_price c ON c.mutasi_detail_id = a.mutasi_detail_id
				LEFT JOIN tax d ON c.`pphId` = d.`tax_id`
				LEFT JOIN tax e ON c.`ppnId` = e.`tax_id`
				WHERE a.mutasi_detail_id IN ({$mutasiChecked}) AND c.pgd_id IS NULL";
                    echo $sql1;
                    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);
                    if ($result1 !== false && $result1->num_rows > 0) {
                        while ($row1 = $result1->fetch_object()) {


                            $id = $row1->id;
                            $mutasiDetailId = $row1->mutasi_detail_id;
                            $availableAmount = $row1->availableAmount;
                            $qty = $row1->qtyInvoice;
                            $price = $row1->price;
                            $termin = $row1->termin;
                            $terminTotal = $row1->terminTotal;
                            $amount = $row1->amount;
                            $amount2 = $row1->amount2;
                            $generalVendorId = $row1->general_vendor_id;
                            $ppnID = $row1->ppnIdInvoice;
                            $pphID = $row1->pphIdInvoice;
                            $ppn2 = $row1->ppnAmount2 - $row1->ppnAmount;
                            $pph2 = $row1->pphAmount2 - $row1->pphAmount;
                            $stockpileId2 = $row1->stockpileId2;
                            $updateTotal = $row1->updateTotal;
                            $qtyEstimasi = $row1->qtyEstimasi;
                            $currencyId = $row1->currency_id;

                            //$notes = $row1->biaya;
                            $dp_total = 0;
                            $dpAmount = 0;
                            $invoiceType = 4;
                            $accountId = $row1->account_id;
                            $invoiceMethodDetail = 1;
                            $mutasiHeaderId = $row1->mutasi_header_id;


                            $t_amount = $amount2 + $ppn2 - $pph2;
                            $tamount = $t_amount - $dp_total;
                            $amountConverted = $exchangeRate * $amount2;
                            $ppnConverted = $exchangeRate * $ppn2;
                            $pphConverted = $exchangeRate * $pph2;
                            $tamountConverted = $exchangeRate * $tamount;


                            if ($termin == 100 && $qtyEstimasi == 0 /*|| $availableAmount == 0*/) {
                                $sqlM = "UPDATE mutasi_detail SET status = 1 WHERE mutasi_detail_id = {$mutasiDetailId}";
                                $resultM = $myDatabase->query($sqlM, MYSQLI_STORE_RESULT);

                                $sqlMA = "SELECT * FROM mutasi_detail WHERE status = 0 AND mutasi_detail_id = {$mutasiDetailId}";
                                $resultMA = $myDatabase->query($sqlMA, MYSQLI_STORE_RESULT);
                                if ($resultMA->num_rows == 0) {
                                    $rowMA = $resultMA->fetch_object();

                                    //$poId = $row->poId;

                                    $sqldp = "UPDATE mutasi_detail SET "
                                        . "total_per_termin = {$updateTotal} "
                                        . " WHERE mutasi_detail_id = {$mutasiDetailId}";
                                    $resultdp = $myDatabase->query($sqldp, MYSQLI_STORE_RESULT);

                                    $sqlCC = "SELECT SUM(total_per_termin) AS total FROM mutasi_detail WHERE mutasi_header_id = {$mutasiHeaderId}";
                                    $resultCC = $myDatabase->query($sqlCC, MYSQLI_STORE_RESULT);
                                    if ($resultCC->num_rows == 1) {
                                        $rowCC = $resultCC->fetch_object();

                                        $total = $rowCC->total;

                                        $sqldd = "UPDATE mutasi_header SET "
                                            . "total = {$total} "
                                            . " WHERE mutasi_header_id = {$mutasiHeaderId}";
                                        $resultdd = $myDatabase->query($sqldd, MYSQLI_STORE_RESULT);

                                    }
                                }
                            }

                            if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
                                $pgId = $_POST['pgId'];
                                $sqlDeletePGD = "DELETE FROM pengajuan_general_detail WHERE  pg_id = {$pgId} AND mutasi_detail_id IS NULL";
                                $resDelete = $myDatabase->query($sqlDeletePGD, MYSQLI_STORE_RESULT);
                            } else {
                                $pgId = 'NULL';
                            }
                            $invoiceMethodDetail = 1;

                            $sql = "INSERT INTO `pengajuan_general_detail` (pg_id,invoice_method_detail, type,account_id, poId, vendor_type,vendor_name,general_vendor_id, shipment_id, stockpile_remark, qty, price, termin, amount, amount_converted, currency_id, exchange_rate, ppnID, ppn, ppn_converted, pphID, pph, pph_converted, tamount, tamount_converted, dp_amount, notes, entry_by, entry_date, mutasi_detail_id) VALUES ("
                                . "{$pgId},{$invoiceMethodDetail}, {$invoiceType},{$accountId},{$poId},'{$row1->vendor_type}','{$row1->vendor}', {$generalVendorId}, {$shipmentId1}, {$stockpileId2}, $qty,$price,$termin, {$amount2}, {$amountConverted}, {$currencyId}, '{$exchangeRate}', {$ppnID} ,'{$ppn2}', '{$ppnConverted}', {$pphID}, '{$pph2}', '{$pphConverted}', '{$tamount}', '{$tamountConverted}', '{$dpAmount}', '{$notes}',{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$mutasiDetailId})";

                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
							echo "Empat ". $sql;
                            if ($result !== false) {
                                $pgd_id = $myDatabase->insert_id;


//                                $sqlN = "DELETE FROM  mutasi_qty_price WHERE id = {$id}";
//                                $resultN = $myDatabase->query($sqlN, MYSQLI_STORE_RESULT);

                                $sqlN = "UPDATE mutasi_qty_price SET pgd_id = {$pgd_id} WHERE id = {$id}";
                                $resultN = $myDatabase->query($sqlN, MYSQLI_STORE_RESULT);
                                $return_value = '|OK|Data has successfully inserted.||';

                            } else {
                                echo $sql;
                            }
                        }
                    }

                } else {
                    $return_value = '|FAIL|Vendor Harus Sama';
                }
            } else {
                $return_value = '|FAIL|Termin lebih dari 100%' . $totalTermin;
            }

        } else {
            // if ($boolVendor) {
            // INSERT PO DETAIL NON MUTASI
            // echo $invoiceType.'-'.$accountId.'-'.$qty.'-'.$price.'-'.$termin.'-'.$amount;
            $invoiceMethodDetail = 1;
            if ($qty != '' && $price != '' && $termin != '' && $amount != '' && $currencyId != '' && $boolShipment1 && $boolPO) {
                $gvBankId = $_POST['gvBankId'];
                $t_amount = $amount2 + $ppn2 - $pph2;
                $tamount = $t_amount - $dp_total;
                $amountConverted = $exchangeRate * $amount2;
                $ppnConverted = $exchangeRate * $ppn2;
                $pphConverted = $exchangeRate * $pph2;
                $tamountConverted = $exchangeRate * $tamount;

                if ($invoiceMethodDetail == 2) {
                    $dpAmount = $tamountConverted;
                } else {
                    $dpAmount = 0;
                }
                if (isset($_POST['pgId']) && $_POST['pgId'] != '') {
                    $pgId = $_POST['pgId'];
                } else {
                    $pgId = 'NULL';
                }
                $sqlVendor = "SELECT * FROM general_vendor WHERE general_vendor_id = {$generalVendorId}";
                $resVendor = $myDatabase->query($sqlVendor, MYSQLI_STORE_RESULT);
                while ($rowVendor = $resVendor->fetch_object()) {
                    $vendorName = $rowVendor->general_vendor_name;
                    $vendorType = 'General';
                }

                $sql = "INSERT INTO `pengajuan_general_detail` (pg_id,gv_bank_id,invoice_method_detail, poId, vendor_type,vendor_name,general_vendor_id, shipment_id, stockpile_remark, qty, price, termin, amount, amount_converted, currency_id, exchange_rate, ppnID, ppn, ppn_converted, pphID, pph, pph_converted, tamount, tamount_converted, dp_amount, notes, entry_by, entry_date) VALUES ("
                    . "{$pgId},{$gvBankId},{$invoiceMethodDetail},{$poId},'{$vendorType}','{$vendorName}', {$generalVendorId}, {$shipmentId1}, {$stockpileId2}, $qty,$price,$termin, {$amount2}, {$amountConverted}, {$currencyId}, '{$exchangeRate}', {$ppnID} ,'{$ppn2}', '{$ppnConverted}', {$pphID}, '{$pph2}', '{$pphConverted}', '{$tamount}', '{$tamountConverted}', '{$dpAmount}', '{$notes}',{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";

                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

				echo "LIMA " . $sql;
                if ($result !== false) {
                    $invoiceId = $myDatabase->insert_id;

                    $return_value = '|OK|Data has successfully inserted.||';
                    //$checks2 = 0;
//                        if ($poId != 'NULL') {
//                            $sql = "UPDATE `contract` SET invoice_status = 1 WHERE contract_id = {$poId}";
//                            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//                        }

//                        if (isset($_POST['checkedSlips2'])) {
//                            $checks2 = $_POST['checkedSlips2'];
//                            if (isset($_POST['checkedSlips'])) {
//                                $checks = $_POST['checkedSlips'];
//                            } else {
//                                $checks = '';
//                            }
//
//                            for ($i = 0; $i < sizeof($checks2); $i++) {
//                                if ($checks[$i] != '') {
//                                    if ($slipNos2 == '') {
//                                        $slipNos2 .= '(' . $invoiceId . ',' . $checks[$i] . ',' . $checks2[$i] . ')';
//                                    } else {
//                                        $slipNos2 .= ',' . '(' . $invoiceId . ',' . $checks[$i] . ',' . $checks2[$i] . ')';
//                                    }
//                                }
//                            }
//                        }

//                        $sql = "INSERT INTO invoice_dp (invoice_detail_id, invoice_detail_dp, amount_payment) VALUES {$slipNos2}";
//                        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//                        if ($result === false) {
//                            echo 'salah';
//                        }

//                        insertGeneralLedger($myDatabase, 'INVOICE DETAIL', $invoiceId);

                } else {
                    echo $sql;
                    $return_value = '|FAIL|Insert Data failed.||';

                }

            } else {
                $return_value = '|FAIL|Please fill the required fields.||';
            }
            // } else {
            //     $return_value = '|FAIL|Please Choose Same Vendor.||';
            // }
        }

        echo $return_value;
        // </editor-fold>
    }
    // </editor-fold>
} elseif (isset($_POST['action']) && $_POST['action'] == 'get_pengajuan_general_detail') {
    // <editor-fold defaultstate="collapsed" desc="get_pengajuan_general_detail">
    $returnValue = '';
    $sql = "SELECT id.pgd_id,id.vendor_name,
CASE WHEN id.type = 4 THEN 'Loading'
WHEN id.type = 5 THEN 'Umum'
WHEN id.type = 6 THEN 'HO' ELSE '' END AS `type2`, 
CASE WHEN id.poId IS NOT NULL THEN c.po_no
WHEN id.shipment_id IS NOT NULL THEN  sh.shipment_no
ELSE '' END AS po_shipment, s.stockpile_name, id.notes, cur.currency_code, id.exchange_rate,
a.account_name,
id.qty, id.price, id.termin, id.amount, id.ppn, id.pph, id.tamount, gv.general_vendor_name, gv.pph AS gv_pph, gv.ppn AS gv_ppn,id.termin
FROM pengajuan_general_detail id
LEFT JOIN account a ON id.account_id = a.account_id
LEFT JOIN shipment sh ON id.shipment_id = sh.shipment_id
LEFT JOIN stockpile s ON id.stockpile_remark = s.stockpile_id
LEFT JOIN general_vendor gv ON id.general_vendor_id = gv.general_vendor_id
LEFT JOIN contract c ON c.contract_id = id.poId
LEFT JOIN currency cur ON cur.currency_id = id.currency_id
WHERE id.pg_id = {$_POST['pgId']} ORDER BY id.pgd_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoiceDetail">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Action</th><th>Type</th><th>Vendor</th><th>Account Name</th><th>PO No/Shipment Code</th><th>Remark (SP)</th><th>Notes</th><th>Qty</th><th>Currency(Rate)</th><th>Unit Price</th><th>Termin</th><th>Amount</th><th>PPN</th><th>PPh</th><th>Total Amount Pengajuan</th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        while ($row = $result->fetch_object()) {
            $returnValue .= '<tr>';

            $termin = $row->termin;
            $ppn = $row->ppn * $termin / 100;
            $pph = $row->pph * $termin / 100;
            $amount = $row->amount * $termin / 100;
            $tamount = $amount + $ppn - $pph;
            $totalPrice += $tamount;
            $returnValue .= '<td style="text-align: right; width: 8%;">';
            $returnValue .= '<a href="#" id="update|pg|' . $row->pgd_id . '" role="button" title="Edit" onclick="editDetail(' . $row->pgd_id . ');"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>';
//            $returnValue .= '<a href="#" id="delete|pg|' . $row->pgd_id . '" role="button" title="Delete" onclick="deleteInvoiceDetail(' . $row->pgd_id . ');"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>';
            $returnValue .= '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->type2 . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->vendor_name . '</td>';
            $returnValue .= '<td style="width: 8%;">' . $row->account_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->po_shipment . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->stockpile_name . '</td>';
            $returnValue .= '<td style="text-align: right; width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->qty, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . $row->currency_code . '(' . number_format($row->exchange_rate, 0, ".", ",") . ')' . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->price, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($row->termin, 0, ".", ",") . '%</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($amount, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($ppn, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($pph, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 8%;">' . number_format($tamount, 2, ".", ",") . '</td>';
            $returnValue .= '</tr>';
        }

        $returnValue .= '</tbody>';

        $grandTotal = $totalPrice;

        $returnValue .= '<tfoot>';
        $returnValue .= '<tr>';
        $returnValue .= '<td colspan="14" style="text-align: right;">Grand Total</td>';
        $returnValue .= '<td colspan="1" style="text-align: right;">' . number_format($grandTotal, 2, ".", ",") . '</td>';
        $returnValue .= '</tr>';
        $returnValue .= '</tfoot>';

        $returnValue .= '</table>';
        $returnValue .= '</form>';
        // $returnValue .= '<input type="hidden" id="pph2" name="pph2" value="'. round($total_pph, 2) .'" />';
        // $returnValue .= '<input type="hidden" id="ppn2" name="ppn2" value="'. round($total_ppn, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        $returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="' . round($grandTotal, 2) . '" />';
    }

    echo $returnValue;
    // </editor-fold>
} elseif (isset($_POST['action']) && $_POST['action'] == 'journal_odoo') {
    // <editor-fold defaultstate="collapsed" desc="UPDATE STATUS JOURNAL ODOO">
    if (isset($_POST['checks']) && $_POST['checks'] != '') {
        $checks = $_POST['checks'];
        for ($i = 0; $i < sizeof($checks); $i++) {
            $glId = $checks[$i];

            if ($selectedCheck == '') {
                $selectedCheck .= $glId;
            } else {
                $selectedCheck .= ', ' . $glId;
            }
        }
        $sql = "UPDATE gl_report SET "
            . "status = 0 "
            . " WHERE gl_id IN ($selectedCheck)";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false) {
            $return_value = '|OK|Success Update Status.|';
        } else {
            $return_value = '|FAIL|Fail Update Status.|';
        }
    } else {
        $return_value = '|FAIL|Record not found.|';
    }
    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'PO_data') {
    // <editor-fold defaultstate="collapsed" desc="PO DATA">
    $return_value = '';
    $addMessage = '';
    $boolNew = false;
    $boolContinue = true;
    $boolUpdate = false;
    $boolUpdateInvoice = false;
    $boolUpdateInvoiceDetail = false;
    $boolInsertVendor = false;
    $boolVendorExists = false;
    $boolRecalculate = false;
    $boolPriceUp = false;
    $boolPriceDown = false;
    $boolQuantityUp = false;
    $boolQuantityDown = false;

    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $id = $myDatabase->real_escape_string($_POST['POId']);
    $POId = $myDatabase->real_escape_string($_POST['generatedPONo']);
    $idPOHDR = $myDatabase->real_escape_string($_POST['idPOHDR']);

    //$invoiceType = $myDatabase->real_escape_string($_POST['invoiceType']);
    //$accountId = $myDatabase->real_escape_string($_POST['accountId']);
    //$invoiceMethod = $myDatabase->real_escape_string($_POST['invoiceMethod']);
    //$invoiceDP = $myDatabase->real_escape_string($_POST['invoice_dp']);
    //$generatedPONo = $myDatabase->real_escape_string($_POST['generatedInvoiceNo']);
    ///$invoiceNo = $generatedInvoiceNo;
    //$invoiceNo2 = $myDatabase->real_escape_string($_POST['generatedInvoiceNo2']);
    $grandTotal = $myDatabase->real_escape_string($_POST['grandTotal']);
    $generalVendorId = $myDatabase->real_escape_string($_POST['generalVendorId']);
    //$stockpileContractId3 = $myDatabase->real_escape_string($_POST['stockpileContractId3']);
    //$shipmentId1 = $myDatabase->real_escape_string($_POST['shipmentId1']);
    //$invoiceDate = $myDatabase->real_escape_string($_POST['invoiceDate']);
    //$inputDate = $myDatabase->real_escape_string($_POST['inputDate']);
    $tanggalpo = $myDatabase->real_escape_string($_POST['requestDate']);
    //$taxDate = $myDatabase->real_escape_string($_POST['taxDate']);
    //$invoiceTax = $myDatabase->real_escape_string($_POST['invoiceTax']);
    //$ppnID = $myDatabase->real_escape_string($_POST['ppnID']);
    //$ppn1 = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppn1']));
    $nopenawaran = str_replace(",", "", $myDatabase->real_escape_string($_POST['inputnopenawaran']));
    //$ppn2 = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppn2']));
    $remarks = $myDatabase->real_escape_string($_POST['remarks']);
    //$ppnConverted = $ppn;
    //$pphID = $myDatabase->real_escape_string($_POST['pphID']);
    //$pph1 = str_replace(",", "", $myDatabase->real_escape_string($_POST['pph1']));
    //$pph2 = str_replace(",", "", $myDatabase->real_escape_string($_POST['pph2']));
    //$dppPrice = $myDatabase->real_escape_string($_POST['dppPrice']);
    $currencyId = $myDatabase->real_escape_string($_POST['currencyId']);
    //$stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $exchangeRate = str_replace(",", "", $myDatabase->real_escape_string($_POST['exchangeRate']));
    $totalppn = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalppn']));
    $totalpph = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalpph']));
    $totalall = str_replace(",", "", $myDatabase->real_escape_string($_POST['totalall']));
    $toc = $myDatabase->real_escape_string($_POST['toc']);
    $signId = $myDatabase->real_escape_string($_POST['signId']);
    $gvBankId = $myDatabase->real_escape_string($_POST['gvBankId']);

    $idPOHDR = $myDatabase->real_escape_string($_POST['idPOHDR']);

    //$quantity = str_replace(",", "", $myDatabase->real_escape_string($_POST['quantity']));
    //$amount = $grandTotal;
    //$amountConverted = $grandTotal;
    //$amount_ori = $grandTotal;
    //$amount_ori_conv = $grandTotal;

    // </editor-fold>

    if ($currencyId == 1) {
        $exchangeRate = 0;

    }


    $sql2 = "SELECT stockpile_id FROM USER WHERE USER_ID = {$_SESSION['userId']}";
    $resultsql2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
    if ($resultsql2->num_rows == 1) {
        $rowsql2 = $resultsql2->fetch_object();
        $stockpileId = $rowsql2->stockpile_id;
    }

    $sql3 = "SELECT stockpile_code FROM stockpile WHERE stockpile_id = {$stockpileId}";
    $resultsql3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
    if ($resultsql3->num_rows == 1) {
        $rowsql3 = $resultsql3->fetch_object();
        $stockpilename = $rowsql3->stockpile_code;
    }


    $checkPONo = 'PO-' . $stockpilename . '/' . $currentYearMonth;

    $sql = "SELECT no_po FROM po_hdr WHERE no_po LIKE '{$checkPONo}%' ORDER BY idpo_hdr DESC LIMIT 1";
    $resultPO = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    if ($resultPO->num_rows == 1) {
        $rowPO = $resultPO->fetch_object();
        $splitPONo = explode('/', $rowPO->no_po);
        $lastExplode = count($splitPONo) - 1;
        $nextPONo = ((float)$splitPONo[$lastExplode]) + 1;
        $PO_number = $checkPONo . '/' . $nextPONo;
    } else {
        $PO_number = $checkPONo . '/1';
    }

    if (!isset($idPOHDR) || $idPOHDR == '') {
        $sql = "INSERT INTO `po_hdr`
					(`no_po`,`general_vendor_id`,`no_penawaran`,`tanggal`,`memo`,`entry_by`,`entry_date`,`currency_id`,`exchangerate`,`grandtotal`,`stockpile_id`,`toc`,`sign_id`,
					`totalppn`,`totalpph`,`totalall`,`bank_id`)
				VALUES
					('{$PO_number}',{$generalVendorId},'{$nopenawaran}',STR_TO_DATE('{$tanggalpo}','%d/%m/%Y'),'{$remarks}',{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$currencyId},{$exchangeRate},{$grandTotal},{$stockpileId},'{$toc}',{$signId},{$totalppn},{$totalpph},{$totalall},{$gvBankId});";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            $id = $myDatabase->insert_id;
            $sqlUpdatePODETAIl = "UPDATE po_detail SET po_hdr_id = {$id}, no_po = '{$PO_number}' WHERE po_hdr_id IS NULL AND DATE_FORMAT(entry_date,'%Y-%m-%d') = '{$todayDate}' AND entry_by = {$_SESSION['userId']}";
            $result = $myDatabase->query($sqlUpdatePODETAIl, MYSQLI_STORE_RESULT);
            $return_value = '|OK|PO has successfully inserted/updated.' . $addMessage . '|' . $id . '|' . $POId . '|';
            // unset($_SESSION['PO']);
        } else {
            $return_value = '|FAIL|Insert/update PO failed.' . $addMessage . '||';
        }
    } else {
        $sql = "update `po_hdr` set `general_vendor_id` = {$generalVendorId},`no_penawaran` = '{$nopenawaran}',`tanggal` = STR_TO_DATE('{$tanggalpo}','%d/%m/%Y') ,
			`memo` = '{$remarks}' ,`entry_by` = {$_SESSION['userId']} ,`entry_date` = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),`currency_id` = {$currencyId},
			`exchangerate` = '{$exchangeRate}',`grandtotal` = {$grandTotal},`stockpile_id` = {$stockpileId},`toc` = '{$toc}',`sign_id` = {$signId},
			`totalppn` = {$totalppn},`totalpph` = {$totalpph},`totalall` = {$totalall} where `idpo_hdr` = {$idPOHDR}";

        $sqlUpdatePODETAIl = "UPDATE po_detail SET po_hdr_id = {$idPOHDR} WHERE no_po = '{$POId}' AND entry_by = {$_SESSION['userId']}";
        $result = $myDatabase->query($sqlUpdatePODETAIl, MYSQLI_STORE_RESULT);
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            echo $sql;
            $id = $myDatabase->insert_id;
            $return_value = '|OK|PO has successfully inserted/updated.' . $addMessage . '|' . $id . '|';
            unset($_SESSION['PO']);
        } else {
            $return_value = '|FAIL|Insert/update PO failed.' . $addMessage . '||';
            echo $sql;
        }

    }

    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'PO_detail') {
    // <editor-fold defaultstate="collapsed" desc="PO DETAIL">
    $return_value = '';

    // <editor-fold defaultstate="collapsed" desc="POST VARIABLE">
    $generalVendorId = $myDatabase->real_escape_string($_POST['generalVendorId']);
    $qty = str_replace(",", "", $myDatabase->real_escape_string($_POST['qty']));
    $price = str_replace(",", "", $myDatabase->real_escape_string($_POST['price']));
    $amount = str_replace(",", "", $myDatabase->real_escape_string($_POST['amount']));
    $ppnpo = str_replace(",", "", $myDatabase->real_escape_string($_POST['ppnPO1']));

    $ppnpoID = $myDatabase->real_escape_string($_POST['ppnPOID']);
    $pphpoID = $myDatabase->real_escape_string($_POST['pphTaxId']);
    $POID = $myDatabase->real_escape_string($_POST['generatedPONo']);
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $ppnpostatus = $myDatabase->real_escape_string($_POST['ppnpostatus']);
    $itemId = $myDatabase->real_escape_string($_POST['itemId']);
    $shipmentId = $myDatabase->real_escape_string($_POST['shipmentId']);
    $requestDate = $myDatabase->real_escape_string($_POST['requestDate']);
    $noPO = $myDatabase->real_escape_string($_POST['noPO']);
    $idPOHDR = $myDatabase->real_escape_string($_POST['idPOHDR']);
    $notes = $myDatabase->real_escape_string($_POST['notes']);

    // </editor-fold>


    if ($pphpoID == 0) {
        $pphstatus = 0;
        $pphpo = 0;
    } else {
        $pphstatus = 1;
        $sqlTax = "SELECT tax_value FROM tax WHERE tax_id = {$pphpoID}";
        $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);
        if ($resultTax->num_rows == 1) {
            $sqlTax = $resultTax->fetch_object();
            $tax_value = $sqlTax->tax_value;

            $pphpo = ($tax_value / 100) * $amount;
        }
    }

    $date = explode('/', $requestDate);
    $month = (int)$date[1];
    $day = $date[0];
    $year = $date[2];

    if ($shipmentId == '') {
        $shipmentId = 0;
    }

    $sql = "SELECT * FROM `po_hdr` ph
          LEFT JOIN po_detail pd on ph.no_po = pd.no_po
		  LEFT JOIN general_vendor gv on gv.general_vendor_id = ph.general_vendor_id
          WHERE ph.general_vendor_id = {$generalVendorId} and year(ph.tanggal)=$year and month(ph.tanggal)=$month and pd.stockpile_id=$stockpileId and gv.monthly_pay=1 AND ph.status = 0 AND ph.no_po != {$POID}";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows == 0) {
        if (isset($idPOHDR) && $idPOHDR != '') {
            $sql = "INSERT INTO `po_detail`
            (`no_po`,`po_hdr_id`,`qty`,`harga`,`amount`,`ppn`,`pph`,`ppn_id`,`pph_id`,`entry_by`,`entry_date`,`pphstatus`,`stockpile_id`,`item_id`,`shipment_id`, `ppnstatus`,`notes`)
            VALUES
            ('{$noPO}',{$idPOHDR},{$qty},{$price},{$amount},{$ppnpo},{$pphpo},
            {$ppnpoID},{$pphpoID},{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$pphstatus},{$stockpileId},{$itemId},{$shipmentId},{$ppnpostatus},'{$notes}');";
        } else {
            $sql = "INSERT INTO `po_detail`
            (`qty`,`harga`,`amount`,`ppn`,`pph`,`ppn_id`,`pph_id`,`entry_by`,`entry_date`,`pphstatus`,`stockpile_id`,`item_id`,`shipment_id`, `ppnstatus`,`notes`)
            VALUES
            ({$qty},{$price},{$amount},{$ppnpo},{$pphpo},
            {$ppnpoID},{$pphpoID},{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$pphstatus},{$stockpileId},{$itemId},{$shipmentId},{$ppnpostatus},'{$notes}');";
        }
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false) {
            $return_value = '|OK|Data has successfully inserted.||';
        } else {
            echo $sql;
            $return_value = '|FAIL|Insert Data failed.||';
        }
    } else {
        $return_value = '|FAIL|Same PO for vendor already created.||';
    }
    //$return_value;
    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'pengajuan_return_data') {
    // <editor-fold defaultstate="collapsed" desc="CRUD PENGAJUAN RETURN DATA">
    $return_value = '';
    $prId = '';
    $slipLama = '';
    $stockpileId = '';
    $tanggalNotim = '';
    $tanggalReturn = '';
    $slipBaru = '';
    $remarks = '';

    // <editor-fold defaultstate="collapsed" desc="POST VARIABLE">
    $prId = $myDatabase->real_escape_string($_POST['prId']);
    $slipLama = $myDatabase->real_escape_string($_POST['slipLama']);
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $tanggalNotim = $myDatabase->real_escape_string($_POST['tanggalNotim']);
    $tanggalReturn = $myDatabase->real_escape_string($_POST['tanggalReturn']);
    $slipBaru = $myDatabase->real_escape_string($_POST['slipBaru']);
    $remarks = $myDatabase->real_escape_string($_POST['remarks']);
    $method = $_POST['_method'];
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Upload File">
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $allowed = array('png', 'jpg', 'pdf', 'doc', 'docs', 'xls', 'xlsx', 'zip');
        $fileName = $_FILES['file']['name'];
        $x = explode('.', $fileName);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['file']['size'];
        $file_tmp = $_FILES['file']['tmp_name'];

        if (in_array($ekstensi, $allowed) === true) {

            $attachmentPath = "./import/pengajuan_retur/" . $todayDate . '/';
            if ($ukuran < 115044070) {

                if (!is_dir($attachmentPath) && !file_exists($attachmentPath))
                    $temp = mkdir($attachmentPath, 0755, TRUE);
                else
                    $temp = TRUE;

                if ($temp === TRUE) {
                    $attachmentPath .= str_ireplace('/', '-', $slipLama . '-' . $stockpileId) . '.' . $ekstensi;
                    if (!move_uploaded_file($file_tmp, $attachmentPath)) {
                        echo '|FAIL|Error while uploading file.|';
                        die();
                    }
                } else {
                    echo '|FAIL|Error while creating directory.|';
                    die();
                }
            } else {
                echo '|FAIL|UKURAN FILE TERLALU BESAR.|';
                die();
            }
        } else {
            echo '|FAIL|EKSTENSI FILE YANG DI UPLOAD TIDAK DI PERBOLEHKAN.|';
            die();
        }
    } else {
        $attachmentPath = 'NULL';
    }
    // </editor-fold>

    if (isset($prId) && $prId != '') {
        if ($method == 'APPROVE') {

            // $date = '25/05/2010';
            $date = str_replace('/', '-', $tanggalReturn);
            $newDate = date('Y-m-d', strtotime($date));
           
            $checkClosingDate=  explode('-', closingDate($newDate,'Nota Timbang - Return'));
            $boolClosing = $checkClosingDate[0];
            $closingDate = $checkClosingDate[1];

            if($boolClosing){
                $sql = "UPDATE pengajuan_retur SET remarks = '{$remarks}', tanggal_return = STR_TO_DATE('{$tanggalReturn}', '%d/%m/%Y'),
                           approved_remarks = '{$_POST['remarksApprove']}' WHERE id_p_retur = {$prId}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                if ($result !== false) {
                    returnNotim($slipLama, $tanggalReturn);
                    //Update Status Menjadi Approved
                    $sql = "UPDATE pengajuan_retur SET status = 1, 
                            approved_by = {$_SESSION['userId']}, approved_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') WHERE id_p_retur = {$prId}";
                    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                    $return_value = '|OK| Approve Successfully!';
                } else {
                    $return_value = '|FAIL| Approve FAILED!';
                }
            }else{
                $return_value = $closingDate;
            }
        } elseif ($method == 'FINISH') {
            $sql = "UPDATE pengajuan_retur SET slip_baru = '{$slipBaru}', status = 2 WHERE id_p_retur = {$prId}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            echo $sql;
            if ($result !== false) {
                //Update Status Menjadi Finish
//                $sql = "UPDATE pengajuan_retur SET status = 2 WHERE id_p_retur = {$prId}";
//                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                $return_value = '|OK|Finish Successfully!';
            } else {
                $return_value = '|FAIL|Finish FAILED!';
            }
        } elseif ($method == 'UPDATE') {
            if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {

                $sql = "UPDATE pengajuan_retur SET stockpile_id = {$stockpileId}, slip_lama = '{$slipLama}', file = '{$attachmentPath}',
                           tanggal_notim =  STR_TO_DATE('{$tanggalNotim}', '%d/%m/%Y'), remarks = '{$remarks}'
                    WHERE id_p_retur = {$prId}";
            } else {
                $sql = "UPDATE pengajuan_retur SET stockpile_id = {$stockpileId}, slip_lama = '{$slipLama}',
                           tanggal_notim =  STR_TO_DATE('{$tanggalNotim}', '%d/%m/%Y'), remarks = '{$remarks}'
                    WHERE id_p_retur = {$prId}";
            }
            echo $sql;
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($result !== false) {
                $return_value = '|OK|Update Successfully!';
            } else {
                $return_value = '|FAIL|Update FAILED!';
            }
        } else {
            $sql = "DELETE FROM pengajuan_retur WHERE id_p_retur = {$prId}";
            echo $sql;
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($result !== false) {
                $return_value = '|OK|DELETE Successfully!';
            } else {
                $return_value = '|FAIL|DELETE FAILED!';
            }
        }
    } else {

        // $sqlCheckRetur = "SELECT * FROM pengajuan_retur WHERE stockpile_id = {$stockpileId} AND status != 2";
        // $resultData = $myDatabase->query($sqlCheckRetur, MYSQLI_STORE_RESULT);

        // if ($resultData->num_rows >= 3) {
        //     echo '|FAIL|Stockpile tersebut sudah mengajukan retur lebih dari 3 data!';
        //     die();
        // }

        $statusNotim = '';
        $sqlCheck = "SELECT t.delivery_status, t.payment_id, t.fc_payment_id, t.hc_payment_id, t.uc_payment_id, id.shipment_id
                    FROM transaction t
                    LEFT JOIN delivery d ON d.transaction_id = t.transaction_id
                    LEFT JOIN invoice_detail id ON id.shipment_id = d.shipment_id
                    where t.transaction_id = {$slipLama} LIMIT 1 ";
        $resultData = $myDatabase->query($sqlCheck, MYSQLI_STORE_RESULT);

        if ($resultData !== false && $resultData->num_rows > 0) {
            $rowData = $resultData->fetch_object();
            $deliveryStatus = $rowData->delivery_status;
            $paymentId = $rowData->payment_id;
            $fcPaymentId = $rowData->fc_payment_id;
            $hcPaymentId = $rowData->hc_payment_id;
            $ucPaymentId = $rowData->uc_payment_id;
            $shipmentId = $rowData->shipment_id;
        }
        $paymentValidate = 0;
        if (isset($paymentId) || isset($fcPaymentId) || isset($hcPaymentId) || isset($ucPaymentId)) {
            $paymentValidate = 1;
        } else {
            $paymentValidate = 0;
        }

        if ($deliveryStatus != 0 && isset($shipmentId) && $paymentValidate == 1) {
            $statusNotim = 1;
        } elseif ($deliveryStatus != 0 && isset($shipmentId) && $paymentValidate == 0) {
            $statusNotim = 2;
        } elseif ($deliveryStatus != 0 && !isset($shipmentId) && $paymentValidate == 1) {
            $statusNotim = 3;
        } elseif ($deliveryStatus == 0 && $paymentValidate == 1 && !isset($shipmentId)) {
            $statusNotim = 4;
        } elseif ($deliveryStatus == 0 && $paymentValidate == 0 && !isset($shipmentId)) {
            $statusNotim = 5;
        } else {
            $statusNotim = 0;
        }

        $sql = "INSERT INTO pengajuan_retur (stockpile_id,slip_lama,tanggal_notim,remarks,file,status_notim,entry_by,entry_date)
                VALUES({$stockpileId},'{$slipLama}', STR_TO_DATE('{$tanggalNotim}', '%d/%m/%Y'),
                '{$remarks}','{$attachmentPath}',{$statusNotim},{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";

        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            $return_value = '|OK|Insert Pengajuan Retur Successfully!';
        } else {
            echo $sql;
            $return_value = '|FAIL|Insert Pengajuan Retur FAILED!';
        }
    }

    echo $return_value;


// </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'setPengajuanDP') {
    // <editor-fold defaultstate="collapsed" desc="SET PENGAJUAN DP">
    $returnValue = '';

    $generalVendorId = $_POST['generalVendorId'];

    $sql = "SELECT i.*, id.*,gv.`pph` AS dp_pph, gv.`ppn` AS dp_ppn,
(SELECT SUM(amount_payment) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND status = 0) AS total_dp
FROM pengajuan_general i 
LEFT JOIN pengajuan_general_detail id ON i.`invoice_id` = id.`invoice_id`
LEFT JOIN general_vendor gv ON gv.`general_vendor_id` = id.`general_vendor_id`
WHERE id.general_vendor_id = {$generalVendorId} AND id.invoice_method_detail = 2 AND id.invoice_detail_status = 0 AND i.invoice_status = 0 AND i.company_id = {$_SESSION['companyId']} 
 AND (id.`amount` - (SELECT COALESCE(SUM(amount_payment),0) FROM invoice_dp WHERE invoice_detail_dp = id.invoice_detail_id AND `status` = 0)) > 0
ORDER BY i.pengajuan_general_id ASC, id.pg_id ASC";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        $returnValue = '<div class="span12 lightblue">';
        $returnValue .= '<form id = "invoice">';
        //$returnValue .= '<input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", true);" value="I like them all!" /><input type="button" onclick="SetAllCheckBoxes("myForm", "myCheckbox", false);" value="I don't like any of them!" />';
        $returnValue .= '<h5>Invoice DP <span style="color: red;">(MASUKAN AMOUNT DPP & CENTANG DP)</span></h5>';
        $returnValue .= '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        $returnValue .= '<thead><tr><th>Invoice No.</th><th>Original Invoice No.</th><th>Notes</th><th>Amount DPP</th><th>PPN</th><th>PPh</th><th>Available DP</th><th>Amount</th><th></th></tr></thead>';
        $returnValue .= '<tbody>';

        $totalPrice = 0;
        $totalPPN = 0;
        $totalPPh = 0;
        $dp_ppn = 0;
        $dp_pph = 0;
        $count = 0;
        while ($row = $result->fetch_object()) {

            $dppTotalPrice = $row->amount;

            if ($row->ppn != 0 && $row->ppn != '') {
                $totalPPN = $row->ppn;
            } else {
                $totalPPN = 0;
            }

            if ($row->pph != 0 && $row->pph != '') {
                $totalPPh = $row->pph;
            } else {
                $totalPPh = 0;
            }

            if ($row->dp_ppn != 0 && $row->ppn != 0) {
                $dp_ppn = $row->total_dp * ($row->dp_ppn / 100);
            } else {
                $dp_ppn = 0;
            }

            if ($row->dp_pph != 0 && $row->pph != 0) {
                $dp_pph = $row->total_dp * ($row->dp_pph / 100);
            } else {
                $dp_pph = 0;
            }

            $total = ($dppTotalPrice + $totalPPN) - $totalPPh;
            $total2 = ($row->total_dp + $dp_ppn) - $dp_pph;
            $total_dp = $total - $total2;

            $returnValue .= '<tr>';


            $returnValue .= '<td style="width: 20%;">' . $row->invoice_no . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->invoice_no2 . '</td>';
            $returnValue .= '<td style="width: 20%;">' . $row->notes . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($dppTotalPrice, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPN, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($totalPPh, 2, ".", ",") . '</td>';
            $returnValue .= '<td style="text-align: right; width: 15%;">' . number_format($total_dp, 2, ".", ",") . '</td>';
            //$returnValue .= '<td style="text-align: right; width: 15%;">'. number_format($total_dp, 2, ".", ",") .'</td>';
            $returnValue .= '<td style="text-align: right;"><input type="text" id="paymentTotal" name="checkedSlips2[' . $count . ']" /></td>';
            if ($checkedSlips != '') {
                $pos = strpos($checkedSlips, $row->invoice_detail_id);

                if ($pos === false) {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"   value="' . $row->invoice_detail_id . '" /></td>';
                } else {
                    //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" checked /></td>';
                    $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"  value="' . $row->invoice_detail_id . '" checked /></td>';

                    //$dppPrice = $dppPrice + $dppTotalPrice;
                    //$totalPrice = $totalPrice + $total;
                    //$total_ppn = $total_ppn + $totalPPN;
                    //$total_pph = $total_pph + $totalPPh;

                }
            } else {
                //$returnValue .= '<td><input type="checkbox" name="checkedSlips[]" id="inv" value="'. $row->invoice_detail_id .'" onclick="checkSlipInvoice('. $row->general_vendor_id .', \'NONE\', \'NONE\', '. $invoiceMethod .');" /></td>';
                $returnValue .= '<td><input type="checkbox" name="checkedSlips[' . $count . ']" id="inv"  value="' . $row->invoice_detail_id . '" /></td>';
            }
            $returnValue .= '</tr>';
            $count = $count + 1;
        }

        $returnValue .= '</tbody>';
        /* if($checkedSlips != '') {

			$grandTotal = $totalPrice;
			if($grandTotal < 0) {
                $grandTotal = 0;
            }*/
        /*$returnValue .= '<tfoot>';
            $returnValue .= '<tr>';
            $returnValue .= '<td colspan="8" style="text-align: right;">Grand Total</td>';
            //$returnValue .= '<td style="text-align: right;">'. number_format($grandTotal, 2, ".", ",") .'</td>';
			$returnValue .= '<td style="text-align: right;"><input type="text" readonly class="span12" id="dp_total" name="dp_total"></td>';
            $returnValue .= '</tr>';
    		$returnValue .= '</tfoot>';*/
        /* }else{
			$grandTotal = 0;
			$total_pph = 0;
			$total_ppn = 0;
		}*/
        $returnValue .= '</table>';
        $returnValue .= '</form>';
        $returnValue .= '<input type="hidden" id="available_dp" name="available_dp	" value="' . number_format($total_dp, 2, ".", ",") . '" />';
        $returnValue .= '<input type="hidden" id="ppnDP2" name="ppnDP2" value="' . number_format($totalPPN, 2, ".", ",") . '" />';
        $returnValue .= '<input type="hidden" id="pphDP2" name="pphDP2" value="' . number_format($totalPPh, 2, ".", ",") . '" />';
        //$returnValue .= '<input type="hidden" id="dppPrice" name="dppPrice" value="'. round($dppPrice, 2) .'" />';
        //$returnValue .= '<input type="hidden" id="grandTotal" name="grandTotal" value="'. round($grandTotal, 2) .'" />';
        $returnValue .= '</div>';
    }

    echo $returnValue;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_slip_no') {
    // <editor-fold defaultstate="collapsed" desc="GET SLIP NO">
    $returnValue = '';
    $sql = "SELECT transaction_id, slip_no FROM transaction t 
            LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
            WHERE sc.stockpile_id = {$_POST['stockpileId']} AND DATE_FORMAT(t.transaction_date,'%d/%m/%Y') = '{$_POST['tanggalNotim']}'";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            if ($returnValue == '') {
                $returnValue = '~' . $row->transaction_id . '||' . $row->slip_no;
            } else {
                $returnValue = $returnValue . '{}' . $row->transaction_id . '||' . $row->slip_no;
            }
        }
    }

    if ($returnValue == '') {
        $returnValue = '~';
    }
    echo $returnValue;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'vendor_group_data') {
    // <editor-fold defaultstate="collapsed" desc="vendor_group_data">
    $vendorGroupId = $myDatabase->real_escape_string($_POST['vendorGroupId']);
    $groupName = $myDatabase->real_escape_string($_POST['groupName']);

    if ($_POST['method'] == 'DELETE') {
        $sql = "DELETE FROM vendor_group WHERE vendor_group_id = {$vendorGroupId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        echo '|OK|Vendor Group has successfully deleted!';
        die();
    }
    $boolNew = false;

    if ($vendorGroupId == '') {
        $boolNew = true;
    }
    if ($groupName != '') {
        if ($boolNew) {
            $sql = "INSERT INTO vendor_group (group_name,entry_by,entry_date) VALUES ('{$groupName}',{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
        } else {
            $sql = "UPDATE `vendor_group` SET "
                . "group_name = '{$groupName}' "
                . "WHERE vendor_group_id = {$vendorGroupId}";
        }
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            if ($boolNew) {
                $vendorGroupId = $myDatabase->insert_id;
            }
            $return_value = '|OK|Vendor Group has successfully inserted/updated.|' . $vendorGroupId . '|';
        } else {
            $return_value = '|FAIL|Insert/update vendor group failed.||';
        }
    } else {
        $return_value = '|FAIL|Please fill the required fields.||';
    }

    echo $return_value;
    // </editor-fold>

} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'approve_pengajuan_freight_cost') {
    // <editor-fold defaultstate="collapsed" desc="approve_freight_cost">

    $return_value = '';
    $boolNew = false;

    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $pFreightCostId = $myDatabase->real_escape_string($_POST['pFreightCostId']);
    $approvedDate = $myDatabase->real_escape_string($_POST['approvedDate']);
    // </editor-fold>

    $sqlPFreightCost = "SELECT * FROM pengajuan_freight_cost WHERE p_freight_cost_id = {$pFreightCostId}";
    $pFreightCost = $myDatabase->query($sqlPFreightCost, MYSQLI_STORE_RESULT);
    while ($row = $pFreightCost->fetch_object()) {
        $cara_pembayaran = $row->cara_pembayaran;
        $attachmentPath2 = $row->file1;
        $freightId = $row->freight_id;
        $stockpileId = $row->stockpile_id;
        $vendorId = $row->vendor_id;
        $currencyId = $row->currency_id;
        $exchangeRate = $row->exchange_rate;
        $price = $row->price;
        $priceConverted = $row->price_converted;
        $paymentNotes = $row->payment_notes;
        $remarks = $row->remarks;
        $contractPKHOA = $row->no_pkhoa;
        $shrink_tolerance_kg = $row->shrink_tolerance_kg;
        $shrink_tolerance_persen = $row->shrink_tolerance_persen;
        $shrink_claim = $row->shrink_claim;
        $active_from = $row->active_from;
    }
    $sql = "INSERT INTO freight_cost(cara_pembayaran,file1,freight_id, stockpile_id, vendor_id, currency_id, exchange_rate, price, "
        . "price_converted, payment_notes, remarks, company_id, entry_by, entry_date,contract_pkhoa,shrink_tolerance_kg,shrink_tolerance_persen,shrink_claim,active_from) VALUES ("
        . "{$cara_pembayaran},'{$attachmentPath2}',{$freightId}, {$stockpileId}, {$vendorId}, {$currencyId}, {$exchangeRate}, {$price}, {$priceConverted}, "
        . "'{$paymentNotes}', '{$remarks}', {$_SESSION['companyId']}, {$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),"
        . "'{$contractPKHOA}','{$shrink_tolerance_kg}','{$shrink_tolerance_persen}','{$shrink_claim}','{$active_from}')";
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
	echo $sql;
    if ($result !== false) {
        $freightCostId = $myDatabase->insert_id;
        $sql = "UPDATE pengajuan_freight_cost SET status = 2, freight_cost_id = {$freightCostId}, approved_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')
                WHERE p_freight_cost_id = {$pFreightCostId} ";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            $return_value = '|OK|Freight Cost has successfully Approved.|';
        } else {
            echo $sql;
            $return_value = '|Fail|Freight Cost has failed update.|';
        }
    } else {
        $return_value = '|FAIL|Approve freight cost failed.||';
    }

    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'cancel_pengajuan_freight_cost') {
    // <editor-fold defaultstate="collapsed" desc="approve_freight_cost">

    $return_value = '';
    $boolNew = false;

    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $actionType = $myDatabase->real_escape_string($_POST['actionType']);
    $pFreightCostId = $myDatabase->real_escape_string($_POST['pFreightCostId']);
    // </editor-fold>
    $sqlCancel = "UPDATE pengajuan_freight_cost SET status = 3, p_freight_cost_id = {$pFreightCostId}, cancel_date = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s')
    WHERE p_freight_cost_id = {$pFreightCostId}";
    $result = $myDatabase->query($sqlCancel, MYSQLI_STORE_RESULT);

        if ($result !== false) {
            $return_value = '|OK|Freight Cost has successfully Canceled.|';
        } else {
            echo $sql;
            $return_value = '|Fail|Freight Cost has failed to cancel.|';
        }

    echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete_pengajuan_freight_cost') {
    // <editor-fold defaultstate="collapsed" desc="approve_freight_cost">

    $return_value = '';
    $boolNew = false;

    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $actionType = $myDatabase->real_escape_string($_POST['actionType']);
    $pFreightCostId = $myDatabase->real_escape_string($_POST['pFreightCostId']);
    // </editor-fold>
        $sqlPFreightCost = "SELECT * FROM pengajuan_freight_cost WHERE p_freight_cost_id = {$pFreightCostId}";
        $pFreightCost = $myDatabase->query($sqlPFreightCost, MYSQLI_STORE_RESULT);
        if ($pFreightCost !== false && $pFreightCost->num_rows > 0) {
            $row = $pFreightCost->fetch_object();
            $noPkhoa = $row->no_pkhoa;
            $contractNoPks = $row->contract_no_pks;
            $freightId = $row->freight_id;
            $stockpileId = $row->stockpile_id;
            $vendorId = $row->vendor_id;
            $currencyId = $row->currency_id;
            $exchangeRate = $row->exchange_rate;
            $price = $row->price;
            $priceConverted = $row->price_converted;
            $paymentNotes = $row->payment_notes;
            $remarks = $row->remarks;
            $company = $row->company_id;
            $shrink_tolerance_kg = $row->shrink_tolerance_kg;
            $shrink_tolerance_persen = $row->shrink_tolerance_persen;
            $shrink_claim = $row->shrink_claim;
            $active_from = $row->active_from;
            $file1 = $row->file1;
            $syarat_pembayaran = $row->syarat_pembayaran;
            $cara_pembayaran = $row->cara_pembayaran;        
            $freightBank = $row->f_bank_id;
            $ApproveDate = $row->approved_date;
            $modifyBy = $row->modify_by;
            $entryBy = $row->entry_by;
            $modifyDate = $row->modify_date;
            $entryDate = $row->entry_date;
            $cancelDate = $row->cancel_date;
            $sqlDelete = "INSERT INTO log_pengajuan_freight_cost(action_by,action_type, action_date,p_freight_cost_id,no_pkhoa,contract_no_pks,freight_id, stockpile_id, vendor_id, currency_id, exchange_rate, price, "
            . "price_converted, payment_notes, remarks, company_id,shrink_tolerance_kg, shrink_tolerance_persen, shrink_claim, active_from, file1, cara_pembayaran, f_bank_id, "
            . "entry_by, entry_date, cancel_date) VALUES ("
            . "'{$_SESSION['userId']}', '{$actionType}', STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'), {$pFreightCostId},'{$noPkhoa}','{$contractNoPks}',{$freightId}, {$stockpileId}, {$vendorId}, {$currencyId}, {$exchangeRate}, {$price}, {$priceConverted}, "
            . "'{$paymentNotes}', '{$remarks}', {$company}, '{$shrink_tolerance_kg}','{$shrink_tolerance_persen}','{$shrink_claim}','{$active_from}', '{$file1}', "
            . "'{$cara_pembayaran}','{$freightBank}','{$entryBy}','{$entryDate}','{$cancelDate}');";
            $sqlDelete .= "DELETE FROM pengajuan_freight_cost WHERE p_freight_cost_id = {$pFreightCostId}";
            $result = $myDatabase->multi_query($sqlDelete);
            echo $sqlDelete;

        }
        if ($result !== false) {
                $return_value = '|OK|Freight Cost has successfully Deleted.|';
            } else {
                echo $sql;
                $return_value = '|Fail|Freight Cost has failed to delete.|';
            }
    
        echo $return_value;
    // </editor-fold>
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'closing_date_data') {
    // <editor-fold defaultstate="collapsed" desc="closing_date_data">

    $closingDate = $myDatabase->real_escape_string($_POST['closingDate']);
    $startPeriod = $myDatabase->real_escape_string($_POST['startPeriod']);
    $endPeriod = $myDatabase->real_escape_string($_POST['endPeriod']);

    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);

    if(isset($_REQUEST['_method']) && $_REQUEST['_method'] == 'AllStockpile'){
        $active = $_POST['status'] ;
        $sql = "UPDATE `closing_date` set active = {$active} WHERE stockpile_id IS NOT NULL; ";

        if($active){
            $active = 0;
        }else{
            $active = 1;
        }
        $sql .= "UPDATE `closing_date` set active = {$active} WHERE stockpile_id IS NULL;";
        $result = $myDatabase->multi_query($sql);

        if ($result !== false) {
            $return_value = '|OK|Closing Date has successfully Active/Deactive All Stockpile.|' . $sqlCheckbox . '|';
        } else {
            $return_value = '|FAIL|Insert/update Closing Date failed.|' . $sqlCheckbox1;
        }
        echo $return_value;
        die();
    }

        if (isset($_POST['closings'])) {
            $result = false;
            // <editor-fold defaultstate="collapsed" desc="Data">
            $arrayClosing = $_POST['closings'];
    
    
            $newArrayClosings = array();
            foreach (array_keys($arrayClosing) as $fieldKey) {
                foreach ($arrayClosing[$fieldKey] as $key => $value) {
                    $newArrayClosings[$key][$fieldKey] = $value;
                }
            }

            $closingDates = array();
    
            if(isset($startPeriod) && $startPeriod != '' && isset($endPeriod) && $endPeriod != '' OR isset($closingDate) && $closingDate != ''){

                $startPeriod = $startPeriod != '-' ? "'".$startPeriod."'" : 'NULL';
                $endPeriod = $endPeriod != '-' ? "'".$endPeriod."'" : 'NULL';
                $closingDate = $closingDate != '-' ? "'".$closingDate."'" : 'NULL';

                foreach ($newArrayClosings as $row) {
                    if (isset($row['id']) && $row['id'] != '') {
                        $id = $row['id'];
                        
                        $sqlCheckbox = "UPDATE `closing_date` SET ";
                        $sqlCheckbox .= "start_period = {$startPeriod},";
                        $sqlCheckbox .= "end_period = {$endPeriod},";
                        $sqlCheckbox .= "closing_date = {$closingDate},".
                            "updated_by =  {$_SESSION['userId']}," .
                            "updated_at = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') WHERE closing_date_id = {$id}";
                        $action = 'UPDATE';
                        $result = $myDatabase->query($sqlCheckbox, MYSQLI_STORE_RESULT);
        
                        $sqlCheckbox1 = "INSERT INTO closing_date_log (closing_date_id, closing_date, start_period,end_period, `action`, entry_by, entry_date) values
                                ({$id}, {$closingDate},{$startPeriod},{$endPeriod},'{$action}', '{$_SESSION['userId']}', STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabase->query($sqlCheckbox1, MYSQLI_STORE_RESULT);
                        $closingDates[] = ['id' => $id];
                    }
                }
            }else{
                foreach ($newArrayClosings as $row) {
                    if (isset($row['id']) && $row['id'] != '') {
                        $closingDate = (isset($row['closingDate']) && $row['closingDate'] != '') ? "'".$row['closingDate']."'" : 'NULL';
                        $startPeriod = (isset($row['startPeriod']) && $row['startPeriod'] != '') ?"'". $row['startPeriod']."'" : 'NULL';
                        $endPeriod = (isset($row['endPeriod']) && $row['endPeriod'] != '') ? "'".$row['endPeriod']."'" : 'NULL';
                        // $status = (isset($row['status']) && $row['status'] != 0) ? 1 : 0;
                        $id = $row['id'];
        
                        $sqlCheckbox = "UPDATE `closing_date` SET ";
                        $sqlCheckbox .= "start_period = {$startPeriod},";
                        $sqlCheckbox .= "end_period = {$endPeriod},";
                        $sqlCheckbox .= "closing_date = {$closingDate},".
                        // $sqlCheckbox .= "status = $status," .
                            "updated_by =  {$_SESSION['userId']}," .
                            "updated_at = STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s') WHERE closing_date_id = {$id}";
                        $action = 'UPDATE';
                        $result = $myDatabase->query($sqlCheckbox, MYSQLI_STORE_RESULT);
        
                        $sqlCheckbox1 = "INSERT INTO closing_date_log (closing_date_id, closing_date, start_period,end_period, `action`, entry_by, entry_date) values
                                ({$id}, {$closingDate},{$startPeriod},{$endPeriod}, '{$action}', '{$_SESSION['userId']}', STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
                        $result = $myDatabase->query($sqlCheckbox1, MYSQLI_STORE_RESULT);
                        $closingDates[] = ['id' => $id];
                    }
                }
            }

            // </editor-fold>
            $countCD = count($closingDates);
            if ($countCD == 0) {
                echo '|FAIL|Please Checklist some data!.|';
                die();
            }
    
            if ($result !== false) {
                $return_value = '|OK|Closing Date has successfully inserted/updated.|' . $sqlCheckbox . '|';
            } else {
                $return_value = '|FAIL|Insert/update Closing Date failed.|' . $sqlCheckbox;
            }
    
            echo $return_value;
        } else {
            echo '|FAIL|Please checklist some data!.|';
        }

    // </editor-fold>
}elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'shrink_tolerance_freight_data') {
    // <editor-fold defaultstate="collapsed" desc="vendor_group_data">
    $stockpileId = $myDatabase->real_escape_string($_POST['stockpileId']);
    $shrinkToleranceFreightId = $myDatabase->real_escape_string($_POST['shrinkToleranceFreightId']);
    $shrinkToleranceKG = $myDatabase->real_escape_string($_POST['shrinkToleranceKG']);
    $shrinkTolerancePersen = $myDatabase->real_escape_string($_POST['shrinkTolerancePersen']);
    $shrinkClaim = $myDatabase->real_escape_string($_POST['shrinkClaim']);
    
    $boolNew = false;

    if ($shrinkToleranceFreightId == '') {
        $boolNew = true;
    }

    if ($shrinkToleranceKG != '' && $shrinkTolerancePersen !='' && $shrinkClaim != '') {
        if ($boolNew) {
            $sql = "INSERT INTO shrink_tolerance_freight (stockpile_id,shrink_tolerance_kg,shrink_tolerance_persen,shrink_claim,entry_by,entry_date)
             VALUES ({$stockpileId},{$shrinkToleranceKG},{$shrinkTolerancePersen},{$shrinkClaim},{$_SESSION['userId']}, STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
        } else {
            $sql = "UPDATE `shrink_tolerance_freight` SET "
                . "shrink_tolerance_kg = {$shrinkToleranceKG}, "
                . "shrink_tolerance_persen = {$shrinkTolerancePersen}, "
                . "shrink_claim = {$shrinkClaim} "
                . "WHERE shrink_tolerance_freight_id = {$shrinkToleranceFreightId}";
        }
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result !== false) {
            if ($boolNew) {
                $vendorGroupId = $myDatabase->insert_id;
            }
            $return_value = '|OK|Shrink Tolerance has successfully inserted/updated.|' . $vendorGroupId . '|';
        } else {
            $return_value = '|FAIL|Insert/update Shrink Tolerance failed.||';
        }
    } else {
        $return_value = '|FAIL|Please fill the required fields.||';
    }

    echo $return_value;
    // </editor-fold>

}

function closingDate($transactionDate, $module)
{
    // <editor-fold defaultstate="collapsed" desc="closingDate">

    global $myDatabase;
  

    $sqlClosingDate = "SELECT DATE_FORMAT(closing_date,'%d %M %Y') as cd_format,
    DATE_FORMAT(start_period,'%d %M %Y') as sp_format, 
    DATE_FORMAT(end_period,'%d %M %Y') as ep_format,
    closing_date,start_period,end_period,cd.`status`,cd.`active`,cdl.label,s.stockpile_name,s.stockpile_id
    FROM closing_date cd 
    LEFT JOIN closing_date_label cdl ON cdl.closing_date_label_id = cd.closing_date_label_id
    LEFT JOIN stockpile s ON s.stockpile_id = cd.stockpile_id
    LEFT JOIN user_stockpile us ON us.stockpile_id = cd.stockpile_id
    WHERE cd.active = 1 AND cdl.label = '{$module}' AND us.user_id = {$_SESSION['userId']}
    AND (closing_date <= '{$transactionDate}' OR start_period <= '{$transactionDate}' AND end_period >= '{$transactionDate}') LIMIT 1 ";

    $resultClosingDate = $myDatabase->query($sqlClosingDate, MYSQLI_STORE_RESULT);
    if ($resultClosingDate !== false && $resultClosingDate->num_rows > 0) {
        $rowClosingDate = $resultClosingDate->fetch_object();
    }else{
        $sqlClosingDate = "SELECT DATE_FORMAT(closing_date,'%d %M %Y') as cd_format,
        DATE_FORMAT(start_period,'%d %M %Y') as sp_format, 
        DATE_FORMAT(end_period,'%d %M %Y') as ep_format,
        closing_date,start_period,end_period,cd.`status`,cd.`active`,cdl.label,s.stockpile_name,s.stockpile_id
        FROM closing_date cd 
        LEFT JOIN closing_date_label cdl ON cdl.closing_date_label_id = cd.closing_date_label_id
        LEFT JOIN stockpile s ON s.stockpile_id = cd.stockpile_id
        WHERE cdl.label = '{$module}' AND cd.stockpile_id IS NULL AND cd.active = 1
        AND (closing_date <= '{$transactionDate}' OR start_period <= '{$transactionDate}' AND end_period >= '{$transactionDate}') LIMIT 1 ";
        $resultClosingDate = $myDatabase->query($sqlClosingDate, MYSQLI_STORE_RESULT);
        $rowClosingDate = $resultClosingDate->fetch_object();
    }
    $closing = false;

    if ($resultClosingDate !== false && $resultClosingDate->num_rows > 0) {
        // $closingDate = $rowClosingDate->closing_date;
        // $startPeriod = $rowClosingDate->start_period;
        // $endPeriod = $rowClosingDate->end_period;
        // $status = $rowClosingDate->status;
        // $active = $rowClosingDate->active;
        // $closingDateFormat = $rowClosingDate->cd_format;
        // $startPeriodFormat = $rowClosingDate->sp_format;
        // $endPeriodFormat = $rowClosingDate->ep_format;
        $closing = '1-Sukses';
    }else{
        $closing = '0-|Fail|Transaction has been closed.';
    }

    return $closing;

    // </editor-fold>
}

function returnNotim($transactionId, $returnInDate)
{
    global $myDatabase;
    // <editor-fold defaultstate="collapsed" desc="delete_sales">

    $return_value = '';
    $date = new DateTime();
    $currentDate = $date->format('d/m/Y H:i:s');
    // <editor-fold defaultstate="collapsed" desc="POST variables">
    $transactionId2 = $transactionId;
//    $returnInDate = $myDatabase->real_escape_string($_POST['returnInDate']);
    $t_date = str_replace('/', '-', $returnInDate);
    $currentYear2 = date('y', strtotime($t_date));

    // </editor-fold>


    if ($transactionId2 != '') {
        $sqlR = "SELECT t.*, SUBSTRING(t.slip_no,1,3) AS stockpileCode FROM `transaction` t WHERE t.transaction_id = {$transactionId2}";
        $resultR = $myDatabase->query($sqlR, MYSQLI_STORE_RESULT);
        if ($resultR !== false && $resultR->num_rows == 1) {
            $rowR = $resultR->fetch_object();
            //$R = "R";
            //$U = "-U";

            //$slipU = $rowR->slip_no .''. $U;
            $checkSlipNo = $rowR->stockpileCode . '-' . $currentYear2;
            $sql = "SELECT LPAD(RIGHT(slip_no, 10) + 1, 10, '0') AS next_id FROM transaction WHERE company_id = {$_SESSION['companyId']} AND slip_no LIKE '{$checkSlipNo}%' ORDER BY transaction_id DESC LIMIT 1";
            $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            if ($resultSlip->num_rows == 0) {
                $sql = "SELECT LPAD(1, 10, '0') AS next_id FROM dual";
                $resultSlip = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            }
            $rowSlipNo = $resultSlip->fetch_object();
            $nextSlipNo = $rowSlipNo->next_id;
            $slipNo = $checkSlipNo . '-' . $nextSlipNo;
            $slipR = $rowR->slip_no . '-R';
            $slipU = $rowR->slip_no . '-U';
            $dateR = $date->format('Y-m-d');
            $sendW = $rowR->send_weight * -1;
            $brutoW = $rowR->bruto_weight * -1;
            $tarraW = $rowR->tarra_weight * -1;
            $nettoW = $rowR->netto_weight * -1;
            $handlingW = $rowR->handling_quantity * -1;
            $freightW = $rowR->freight_quantity * -1;
            $quantityW = $rowR->quantity * -1;
            $shrinkW = $rowR->shrink * -1;
            $unloadingP = $rowR->unloading_price * -1;

            if ($rowR->shipment_id == '') {
                $shipment_id = 'NULL';
            } else {
                $shipment_id = $rowR->shipment_id;
            }
            if ($rowR->labor_id == '') {
                $labor_id = 'NULL';
            } else {
                $labor_id = $rowR->labor_id;
            }
            if ($rowR->unloading_cost_id == '') {
                $unloading_cost_id = 'NULL';
            } else {
                $unloading_cost_id = $rowR->unloading_cost_id;
            }
            if ($rowR->handling_cost_id == '') {
                $handling_cost_id = 'NULL';
            } else {
                $handling_cost_id = $rowR->handling_cost_id;
            }
            if ($rowR->freight_cost_id == '') {
                $freight_cost_id = 'NULL';
            } else {
                $freight_cost_id = $rowR->freight_cost_id;
            }
            if ($rowR->permit_no == '') {
                $permit_no = 'NULL';
            } else {
                $permit_no = $rowR->permit_no;
            }
            if ($rowR->vendor_id == '') {
                $vendor_id = 'NULL';
            } else {
                $vendor_id = $rowR->vendor_id;
            }
            if ($rowR->cust_tax_id == '') {
                $cust_tax_id = 'NULL';
            } else {
                $cust_tax_id = $rowR->cust_tax_id;
            }
            if ($rowR->curah_tax_id == '') {
                $curah_tax_id = 'NULL';
            } else {
                $curah_tax_id = $rowR->curah_tax_id;
            }
            if ($rowR->uc_tax_id == '') {
                $uc_tax_id = 'NULL';
            } else {
                $uc_tax_id = $rowR->uc_tax_id;
            }
            if ($rowR->fc_tax_id == '') {
                $fc_tax_id = 'NULL';
            } else {
                $fc_tax_id = $rowR->fc_tax_id;
            }
            if ($rowR->block == '') {
                $block = 'NULL';
            } else {
                $block = $rowR->block;
            }
            //if($rowR->payment_id == ''){
            $payment_id = 'NULL';

            $fc_payment_id = 'NULL';

            $hc_payment_id = 'NULL';

            $uc_payment_id = 'NULL';

            if ($rowR->notes == '') {
                $notes = 'NULL';
            } else {
                $notes = $rowR->notes;
            }
            $delivery_status = 0;
        }

        $sqlLog = "INSERT INTO `transaction`
(slip_no,slip_retur,product_id,stockpile_contract_id,shipment_id,transaction_date,loading_date,vehicle_no,labor_id,unloading_cost_id,unloading_date,handling_cost_id,freight_cost_id,permit_no,
transaction_type,vendor_id,send_weight,bruto_weight,tarra_weight,netto_weight,notes,driver,handling_quantity,freight_quantity,quantity,shrink,freight_price,handling_price,unloading_price,unit_price,
inventory_value,cust_tax_id,curah_tax_id,uc_tax_id,fc_tax_id,delivery_status,block,payment_id,fc_payment_id,hc_payment_id,uc_payment_id,sync_status,company_id,entry_by,entry_date,modify_by,modify_date)
VALUES
('{$slipNo}','{$slipR}',{$rowR->product_id},{$rowR->stockpile_contract_id},{$shipment_id},STR_TO_DATE('{$returnInDate}', '%d/%m/%Y'),STR_TO_DATE('{$returnInDate}', '%d/%m/%Y'),'{$rowR->vehicle_no}',{$labor_id},{$unloading_cost_id},STR_TO_DATE('{$returnInDate}', '%d/%m/%Y'),{$handling_cost_id},{$freight_cost_id},'{$permit_no}',{$rowR->transaction_type},{$vendor_id},'{$sendW}','{$brutoW}','{$tarraW}','{$nettoW}','{$notes}','{$rowR->driver}','{$handlingW}','{$freightW}','{$quantityW}','{$shrinkW}','{$rowR->freight_price}','{$rowR->handling_price}','{$unloadingP}','{$rowR->unit_price}','{$rowR->inventory_value}',{$cust_tax_id},{$curah_tax_id},{$uc_tax_id},{$fc_tax_id},{$delivery_status},'{$block}',{$payment_id},{$fc_payment_id},{$hc_payment_id},{$uc_payment_id},{$rowR->sync_status},{$rowR->company_id},{$_SESSION['userId']},STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'),{$_SESSION['userId']},STR_TO_DATE('$currentDate', '%d/%m/%Y %H:%i:%s'))";
        echo $sqlLog;
        echo 'insert tr';
        $resultLog = $myDatabase->query($sqlLog, MYSQLI_STORE_RESULT);
        if ($resultLog !== false) {
            //echo $sqlLog;
            $transactionId = $myDatabase->insert_id;

            $sql = "UPDATE transaction_timbangan SET notim_status = 0 WHERE transaction_id = '{$rowR->t_timbangan}'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            $sql = "UPDATE `transaction` SET notim_status = 1 WHERE transaction_id = {$transactionId2}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            $sql = "CALL sp_shrink_weight_retur({$transactionId},{$transactionId2})";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

            insertGeneralLedger($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);
            insertReportGL($myDatabase, 'NOTA TIMBANG', "NULL", "NULL", "NULL", $transactionId);


            $return_value = '|OK|Nota Timbang has successfully Returned.|' . $transactionId2 . '|';
        } else {
            $return_value = '|FAIL|Returned Nota Timbang failed.|' . $sqlLog . '';
        }
    } else {
        $return_value = '|FAIL|Record not found.|';
    }

    echo $return_value;
    // </editor-fold>
}

function insertReportGL($myDatabase, $source, $invoiceId = "NULL", $i_id = "NULL", $contractId = "NULL", $transactionId = "NULL", $paymentId = "NULL", $jurnalId = "NULL", $returnId = "NULL")
{

    $whereCondition = '';


    if ($contractId != 'NULL' && $source != 'STOCK TRANSIT' && $source == 'CONTRACT ADJUSTMENT') {

        $sqlDelete = "DELETE FROM gl_report WHERE contract_id = {$contractId} AND general_ledger_module = 'CONTRACT ADJUSTMENT'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.contract_id = {$contractId} AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT'";

    } elseif ($contractId != 'NULL' && $source == 'STOCK TRANSIT' && $source != 'CONTRACT ADJUSTMENT') {

        $sqlDelete = "DELETE FROM gl_report WHERE contract_id = {$contractId} AND general_ledger_module = 'STOCK TRANSIT'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.contract_id = {$contractId} AND gl.general_ledger_module = 'STOCK TRANSIT'";

    } elseif ($contractId != 'NULL' && $source != 'STOCK TRANSIT' && $source != 'CONTRACT ADJUSTMENT') {

        $sqlDelete = "DELETE FROM gl_report WHERE contract_id = {$contractId} AND general_ledger_module = 'CONTRACT'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.contract_id = {$contractId} AND gl.general_ledger_module = 'CONTRACT'";

    } elseif ($jurnalId != 'NULL') {

        $sqlDelete = "DELETE FROM gl_report WHERE jurnal_id = {$jurnalId} AND general_ledger_module = 'JURNAL MEMORIAL'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.jurnal_id = {$jurnalId} AND gl.general_ledger_module = 'JURNAL MEMORIAL'";

    } elseif ($invoiceId != 'NULL' && $source == 'RETURN INVOICE') {

        $sqlDelete = "DELETE FROM gl_report WHERE invoice_id = {$invoiceId} AND general_ledger_module = 'RETURN INVOICE'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.invoice_id = {$invoiceId} AND gl.general_ledger_module = 'RETURN INVOICE'";

    } elseif ($invoiceId != 'NULL' && $source == 'INVOICE DETAIL') {

        $sqlDelete = "DELETE FROM gl_report WHERE invoice_id = {$invoiceId} AND general_ledger_module = 'INVOICE DETAIL'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.invoice_id = {$invoiceId} AND gl.general_ledger_module = 'INVOICE DETAIL'";

    } elseif ($transactionId != 'NULL' && $source == 'NOTA TIMBANG2') {

        $sqlDelete = "DELETE FROM gl_report WHERE transaction_id = {$transactionId} AND general_ledger_module = 'NOTA TIMBANG2'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.transaction_id = {$transactionId} AND gl.general_ledger_module = 'NOTA TIMBANG2'";

    } elseif ($transactionId != 'NULL' && $source == 'NOTA TIMBANG') {

        $sqlDelete = "DELETE FROM gl_report WHERE transaction_id = {$transactionId} AND general_ledger_module = 'NOTA TIMBANG'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.transaction_id = {$transactionId} AND gl.general_ledger_module = 'NOTA TIMBANG'";

    } elseif ($paymentId != 'NULL' && $source == 'PAYMENT') {

        $sqlDelete = "DELETE FROM gl_report WHERE payment_id = {$paymentId} AND general_ledger_module = 'PAYMENT'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.payment_id = {$paymentId} AND gl.general_ledger_module = 'PAYMENT'";

    } elseif ($paymentId != 'NULL' && $source == 'PAYMENT ADMIN') {

        $sqlDelete = "DELETE FROM gl_report WHERE payment_id = {$paymentId} AND general_ledger_module = 'PAYMENT ADMIN'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.payment_id = {$paymentId} AND gl.general_ledger_module = 'PAYMENT ADMIN'";

    } elseif ($paymentId != 'NULL' && $source == 'PETTY CASH') {

        $sqlDelete = "DELETE FROM gl_report WHERE payment_id = {$paymentId} AND general_ledger_module = 'PETTY CASH'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.payment_id = {$paymentId} AND gl.general_ledger_module = 'PETTY CASH'";

    } elseif ($paymentId != 'NULL' && $source == 'RETURN PAYMENT') {

        $sqlDelete = "DELETE FROM gl_report WHERE payment_id = {$paymentId} AND general_ledger_module = 'RETURN PAYMENT'";
        $result = $myDatabase->query($sqlDelete, MYSQLI_STORE_RESULT);

        $whereCondition .= "AND gl.payment_id = {$paymentId} AND gl.general_ledger_module = 'RETURN PAYMENT'";

    }

    $sqla = "SELECT gl.*,
(SELECT `amount` FROM  payment p WHERE p.payment_id = gl.payment_id  ) AS amountPayment , 
(SELECT `pph_journal` FROM  payment p WHERE p.payment_id = gl.payment_id  ) AS pph_journal ,
(SELECT `ppn_journal` FROM  payment p WHERE p.payment_id = gl.payment_id  ) AS ppn_journal,
(SELECT `account_no` FROM  account a WHERE a.account_id = gl.account_id) AS account_no,
(SELECT `account_name` FROM  account a WHERE a.account_id = gl.account_id) AS account_name,
	
	CASE WHEN gl.general_ledger_transaction_type = 1 THEN 'IN'
		WHEN gl.general_ledger_transaction_type = 2 THEN 'OUT'
		ELSE '' END AS general_ledger_transaction_type2,
                
	CASE WHEN gl.general_ledger_for = 1 THEN 'PKS Kontrak'
		WHEN gl.general_ledger_for = 2 THEN 'PKS Curah'
		WHEN gl.general_ledger_for = 3 THEN 'Freight Cost'
		WHEN gl.general_ledger_for = 4 THEN 'Unloading Cost'
		WHEN gl.general_ledger_for = 5 THEN 'Other'
		WHEN gl.general_ledger_for = 6 THEN 'Internal Transfer'
		WHEN gl.general_ledger_for = 7 THEN 'Retur'
		WHEN gl.general_ledger_for = 8 THEN 'Umum, HO'
		WHEN gl.general_ledger_for = 9 THEN 'Sales'
		WHEN gl.general_ledger_for = 10 THEN 'Invoice'
		WHEN gl.general_ledger_for = 11 THEN 'Jurnal Memorial'
		WHEN gl.general_ledger_for = 13 THEN 'Handling Cost'
		WHEN gl.general_ledger_for = 14 THEN 'Freight Cost Shrink'
		ELSE '' END AS general_ledger_for2,
        
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id
 WHERE sc.contract_id = gl.contract_id AND sc.quantity > 0 ORDER BY sc.`stockpile_contract_id` ASC LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN invoice i ON i.stockpileId = s.stockpile_id LEFT JOIN invoice_detail id ON i.`invoice_id` = id.`invoice_id` WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) !=0 THEN (SELECT s.stockpile_name FROM stockpile s WHERE s.stockpile_code = (SELECT SUBSTR(slip_no,1,3) FROM TRANSACTION WHERE transaction_id = gl.transaction_id))
		WHEN gl.transaction_id IS NOT NULL AND gl.account_id != 8 AND gl.account_id != 51 AND gl.general_ledger_for = 2 AND (SELECT return_shipment FROM contract c WHERE c.contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id  = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id)))
		THEN (SELECT stockpile_name FROM stockpile WHERE stockpile_id = 
			(SELECT stockpile_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = 
			(SELECT return_shipment_id FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = 
			(SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))))))
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14)
		THEN (SELECT s.stockpile_name FROM stockpile_contract sc INNER JOIN stockpile s ON s.stockpile_id = sc.stockpile_id INNER JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE gl.transaction_id = t.transaction_id)
		
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT s.stockpile_name FROM stockpile s INNER JOIN sales sl ON s.stockpile_id = sl.stockpile_id INNER JOIN shipment sh ON sh.sales_id = sl.sales_id INNER JOIN `transaction` t ON t.shipment_id = sh.shipment_id WHERE gl.transaction_id = t.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) THEN (SELECT s.stockpile_name FROM 					stockpile s INNER JOIN payment p ON p.stockpile_location = s.stockpile_id WHERE gl.payment_id = p.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT s.stockpile_name FROM stockpile s LEFT JOIN gl_detail jd ON jd.stockpile_id = s.stockpile_id WHERE jd.gl_detail_id = gl.jurnal_id) 
		ELSE '' END AS stockpile_name2,
			
	CASE WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) 
		THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13)
		THEN (SELECT t.slip_no FROM `transaction` t WHERE t.transaction_id = gl.t_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT t.slip_no FROM `transaction` t LEFT JOIN gl_add jm ON jm.transaction_id = t.transaction_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT t.slip_no FROM `transaction` t LEFT JOIN payment_cash pc ON pc.transaction_id = t.transaction_id WHERE pc.payment_cash_id = gl.cash_id)
		ELSE '' END AS slip_no,
			  
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT vendor_id FROM contract WHERE contract_id = gl.contract_id))
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) != 0 THEN 'ADJUSTMENT AUDIT'	
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2) THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE t.transaction_id = gl.transaction_id))
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 3 OR gl.general_ledger_for = 14) THEN (SELECT `freight_supplier` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM freight_cost WHERE freight_cost_id = (SELECT freight_cost_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT vh.`vendor_handling_name` FROM vendor_handling vh WHERE vh.vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = (SELECT handling_cost_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_name FROM labor WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_name FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`))))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id` LEFT JOIN payment p ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE p.`payment_id` = gl.payment_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT vendor_name FROM vendor WHERE vendor_id = (SELECT vendor_id FROM payment WHERE payment_id = gl.payment_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT `freight_supplier` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.`payment_id`))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT vh.`vendor_handling_name` FROM vendor_handling vh WHERE vh.vendor_handling_id = (SELECT vendor_handling_id FROM payment WHERE payment_id = gl.`payment_id`))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_name FROM labor WHERE labor_id = (SELECT labor_id FROM `payment` WHERE payment_id = gl.`payment_id`))   
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 8) THEN (SELECT gv.general_vendor_name FROM general_vendor gv WHERE gv.general_vendor_id = (SELECT p.general_vendor_id FROM payment p WHERE p.payment_id = gl.payment_id))                
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 6 THEN (SELECT ap.account_name FROM account ap WHERE ap.account_id = (SELECT p.account_id FROM payment p WHERE p.payment_id = gl.payment_id ))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_name FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM payment  WHERE payment_id = gl.payment_id)))				
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DISTINCT(gv.general_vendor_name) FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.invoice_id = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT DISTINCT(gv.general_vendor_name) FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT general_vendor_name FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id))
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 AND (SELECT general_vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id = gl.jurnal_id)) IS NOT NULL THEN (SELECT general_vendor_name FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM payment  WHERE payment_id = gl.payment_id))
		ELSE '' END AS supplier_name,
		
		CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_code FROM vendor WHERE vendor_id = (SELECT vendor_id FROM contract WHERE contract_id = gl.contract_id))
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) != 0 THEN 'ADJ'	
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2) THEN (SELECT vendor_code FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE t.transaction_id = gl.transaction_id))
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 3 OR gl.general_ledger_for = 14) THEN (SELECT `freight_code` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM freight_cost WHERE freight_cost_id = (SELECT freight_cost_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT vh.`vendor_handling_code` FROM vendor_handling vh WHERE vh.vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = (SELECT handling_cost_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`)))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_code FROM labor WHERE labor_id = (SELECT labor_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`))
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_code FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.`transaction_id`))))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT vendor_code FROM vendor WHERE vendor_id = (SELECT c.vendor_id FROM contract c LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id` LEFT JOIN payment p ON sc.`stockpile_contract_id` = p.`stockpile_contract_id` WHERE p.`payment_id` = gl.payment_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT vendor_code FROM vendor WHERE vendor_id = (SELECT vendor_id FROM payment WHERE payment_id = gl.payment_id))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT `freight_code` FROM freight f WHERE f.freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.`payment_id`))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT vh.`vendor_handling_code` FROM vendor_handling vh WHERE vh.vendor_handling_id = (SELECT vendor_handling_id FROM payment WHERE payment_id = gl.`payment_id`))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT labor_code FROM labor WHERE labor_id = (SELECT labor_id FROM `payment` WHERE payment_id = gl.`payment_id`))   
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 8) THEN (SELECT gv.general_vendor_code FROM general_vendor gv WHERE gv.general_vendor_id = (SELECT p.general_vendor_id FROM payment p WHERE p.payment_id = gl.payment_id))                
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 6 THEN (SELECT ap.account_name FROM account ap WHERE ap.account_id = (SELECT p.account_id FROM payment p WHERE p.payment_id = gl.payment_id ))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT cust.customer_code FROM customer cust WHERE customer_id = (SELECT customer_id FROM sales WHERE sales_id = (SELECT sales_id FROM payment  WHERE payment_id = gl.payment_id)))				
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DISTINCT(gv.general_vendor_code) FROM general_vendor gv LEFT JOIN invoice_detail id ON gv.general_vendor_id = id.general_vendor_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.invoice_id = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT DISTINCT(gv.general_vendor_code) FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT general_vendor_code FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id))
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 AND (SELECT general_vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id = gl.jurnal_id)) IS NOT NULL THEN (SELECT general_vendor_code FROM general_vendor  WHERE general_vendor_id = (SELECT general_vendor_id FROM payment  WHERE payment_id = gl.payment_id))
		ELSE '' END AS supplier_code,
				 
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT contract_no FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14)
		THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 13) THEN (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.t_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN payment_cash pc ON pc.transaction_id = t.transaction_id WHERE pc.payment_cash_id = gl.cash_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN  (SELECT contract_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT sales_no FROM sales WHERE sales_id = (SELECT sales_id FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT i.entry_date FROM invoice i LEFT JOIN payment p ON i.invoice_id = p.invoice_id WHERE p.payment_id = gl.payment_id), '%Y-%m-%d') < '2018-09-24' THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT i.entry_date FROM invoice i LEFT JOIN payment p ON i.invoice_id = p.invoice_id WHERE p.payment_id = gl.payment_id), '%Y-%m-%d') >= '2018-09-24' THEN (SELECT c.contract_no FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT entry_date FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id), '%Y-%m-%d') < '2018-09-24'  THEN (SELECT c.contract_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN invoice_detail id ON i.`invoice_id` = id.invoice_id WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT entry_date FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id), '%Y-%m-%d') >= '2018-09-24' THEN (SELECT c.contract_no FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT c.contract_no FROM contract c LEFT JOIN gl_add jm ON jm.contract_id = c.contract_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS contract_no,

	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT po_no FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14)
		THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.transaction_id))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id)))
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 13) THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM `transaction` WHERE transaction_id = gl.t_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN payment_cash pc ON pc.transaction_id = t.transaction_id WHERE pc.payment_cash_id = gl.cash_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT po_no FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id)))
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT i.entry_date FROM invoice i LEFT JOIN payment p ON i.invoice_id = p.invoice_id WHERE p.payment_id = gl.payment_id), '%Y-%m-%d') < '2018-09-24' THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT i.entry_date FROM invoice i LEFT JOIN payment p ON i.invoice_id = p.invoice_id WHERE p.payment_id = gl.payment_id), '%Y-%m-%d') >= '2018-09-24' THEN (SELECT c.po_no FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT entry_date FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id), '%Y-%m-%d') < '2018-09-24'  THEN (SELECT c.po_no FROM contract c LEFT JOIN stockpile_contract sc ON c.contract_id = sc.contract_id LEFT JOIN invoice i ON i.`po_id` = sc.`stockpile_contract_id` LEFT JOIN invoice_detail id ON i.`invoice_id` = id.invoice_id WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND DATE_FORMAT((SELECT entry_date FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id), '%Y-%m-%d') >= '2018-09-24' THEN (SELECT c.po_no FROM contract c LEFT JOIN invoice_detail id ON c.contract_id = id.poId WHERE id.`invoice_detail_id` = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT c.po_no FROM contract c LEFT JOIN gl_add jm ON jm.contract_id = c.contract_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS po_no,
			
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) THEN (SELECT invoice_no FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN gl_add jm ON jm.invoice_id = i.invoice_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS invoice_no,
			
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT tax_invoice FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_tax FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_tax FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id ORDER BY id.invoice_detail_id DESC LIMIT 1)
		ELSE '' END AS tax_invoice,
                
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) THEN (SELECT invoice_no FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id ORDER BY id.invoice_detail_id DESC LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT i.invoice_no2 FROM invoice i LEFT JOIN gl_add jm ON jm.invoice_id = i.invoice_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS invoice_no_2,
				
	CASE WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.payment_status FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id
		WHERE id.invoice_detail_id = gl.invoice_id)
		ELSE '' END AS invoice_payment,
               
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT cheque_no FROM payment WHERE payment_id = gl.payment_id)
		ELSE '' END AS cheque_no,
                
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 8) THEN  (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM payment WHERE payment_id = gl.payment_id)) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT GROUP_CONCAT(sh2.shipment_no) FROM payment_detail pd2 LEFT JOIN shipment sh2 ON sh2.shipment_id = pd2.shipment_id WHERE pd2.payment_id = gl.payment_id GROUP BY pd2.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DISTINCT(sh.shipment_no) FROM shipment sh LEFT JOIN invoice_detail id ON sh.shipment_id = id.shipment_id LEFT JOIN invoice i ON i.invoice_id = id.invoice_id LEFT JOIN payment p ON p.invoice_id = i.invoice_id WHERE p.payment_id = gl.payment_id LIMIT 1)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT DISTINCT(sh.shipment_no) FROM shipment sh LEFT JOIN payment_cash pc ON sh.shipment_id = pc.shipment_id WHERE pc.payment_cash_id = gl.cash_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL AND (SELECT adjustmentAudit_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) !=0 THEN (SELECT sh.shipment_no FROM shipment sh LEFT JOIN adjustment_audit aa ON sh.shipment_id = aa.shipment_id LEFT JOIN `transaction` t ON t.adjustmentAudit_id = audit_id WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT mutasi_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT mh.kode_mutasi FROM mutasi_header mh LEFT JOIN TRANSACTION t ON t.mutasi_id = mh.mutasi_header_id WHERE t.transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) AND (SELECT c.return_shipment FROM contract c LEFT JOIN stockpile_contract sc ON c.`contract_id` = sc.`contract_id` LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id` = sc.`stockpile_contract_id` WHERE t.`transaction_id` = gl.transaction_id LIMIT 1) IS NOT NULL THEN (SELECT sh.shipment_no FROM shipment sh LEFT JOIN contract c ON sh.shipment_id = c.return_shipment_id LEFT JOIN stockpile_contract sc ON sc.contract_id = c.contract_id LEFT JOIN `transaction` t ON t.stockpile_contract_id = sc.stockpile_contract_id WHERE t.transaction_id = gl.transaction_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM `transaction` WHERE transaction_id = gl.transaction_id ))
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND (SELECT mutasi_detail_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id) IS NOT NULL THEN (SELECT mh.kode_mutasi FROM mutasi_header mh LEFT JOIN mutasi_detail md ON mh.mutasi_header_id = md.mutasi_header_id LEFT JOIN invoice_detail id ON id.mutasi_detail_id = md.mutasi_detail_id WHERE id.invoice_detail_id = gl.invoice_id LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT shipment_no FROM shipment WHERE shipment_id = (SELECT shipment_id FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id))
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT sh.shipment_no FROM shipment sh LEFT JOIN gl_add jm ON jm.shipment_id = sh.shipment_id LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT mh.kode_mutasi FROM mutasi_header mh LEFT JOIN mutasi_contract mc ON mh.mutasi_header_id = mc.mutasi_header_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = mc.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)  
		ELSE '' END AS shipment_code,
				
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN (SELECT adjustment FROM contract WHERE contract_id = gl.contract_id)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT quantity FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'STOCK TRANSIT' THEN (SELECT st.send_weight FROM stock_transit st LEFT JOIN stockpile_contract sc ON st.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 8 THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 52 THEN (SELECT shrink FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.account_id = 147 THEN (SELECT send_weight FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND(gl.general_ledger_for = 2 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9) THEN (SELECT quantity FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT freight_quantity FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT handling_quantity FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.qty FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT quantity FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id ))) 
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 2 OR gl.general_ledger_for = 4) THEN (SELECT COALESCE(SUM(t2.quantity), 0) FROM `transaction` t2 WHERE t2.transaction_id = gl.t_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT COALESCE(SUM(t2.freight_quantity), 0) FROM `transaction` t2 WHERE t2.transaction_id = gl.t_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT COALESCE(SUM(t2.handling_quantity), 0) FROM `transaction` t2 WHERE t2.hc_payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT COALESCE(SUM(sh2.quantity), 0) FROM payment_detail pd2 LEFT JOIN shipment sh2 ON sh2.shipment_id = pd2.shipment_id WHERE pd2.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8) THEN (SELECT qty FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.quantity FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT qty FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		ELSE '' END AS quantity,
				
	 CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT price_converted FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 9) THEN (SELECT unit_price FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT freight_price FROM `transaction` WHERE transaction_id = gl.transaction_id)  
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT GROUP_CONCAT(vhc.price_converted) FROM vendor_handling_cost vhc LEFT JOIN `transaction` t ON vhc.handling_cost_id = t.handling_cost_id WHERE t.transaction_id = gl.transaction_id)  
		WHEN gl.transaction_id IS NOT NULL AND gl.general_ledger_for = 4 THEN (SELECT unloading_price FROM `transaction` WHERE transaction_id = gl.transaction_id) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.price FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 13 THEN (SELECT GROUP_CONCAT(vhc.price_converted) FROM vendor_handling_cost vhc LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = vhc.vendor_handling_id LEFT JOIN payment p ON p.vendor_handling_id = vh.vendor_handling_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 3 THEN (SELECT GROUP_CONCAT(t2.freight_price) FROM `transaction` t2 WHERE t2.transaction_id = gl.t_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 2 THEN (SELECT GROUP_CONCAT(t2.unit_price) FROM `transaction` t2 WHERE t2.transaction_id = gl.t_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 1 THEN (SELECT price_converted FROM contract WHERE contract_id = (SELECT contract_id FROM stockpile_contract WHERE stockpile_contract_id = (SELECT stockpile_contract_id FROM payment WHERE payment_id = gl.payment_id ))) 
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 9 THEN (SELECT GROUP_CONCAT(t2.unit_price) FROM `transaction` t2 WHERE t2.payment_id = gl.payment_id GROUP BY t2.payment_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 8) THEN (SELECT price FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT price FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.price FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS price,
				
	 CASE WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT notes FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT i.remarks FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 12 THEN (SELECT pc.notes FROM payment_cash pc WHERE pc.`payment_cash_id` = gl.cash_id)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN (SELECT adjustment_notes FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.contract_id IS NOT NULL THEN (SELECT notes FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.transaction_id IS NOT NULL THEN (SELECT CONCAT(slip_retur,' - ',notes) FROM `transaction` WHERE transaction_id = gl.transaction_id)    
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jd.notes FROM gl_detail jd WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE (SELECT remarks FROM payment WHERE payment_id = gl.payment_id )  END AS remarks,
									
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN DATE_FORMAT((SELECT adjustment_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 AND (SELECT contract_status FROM contract WHERE contract_id = gl.contract_id) = 2 THEN DATE_FORMAT((SELECT reject_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_for = 1 THEN DATE_FORMAT((SELECT entry_date FROM contract WHERE contract_id = gl.contract_id), '%Y-%m-%d')
		WHEN gl.transaction_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 13 OR gl.general_ledger_for = 14) THEN (SELECT unloading_date FROM `transaction` WHERE transaction_id = gl.transaction_id)
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) AND gl.general_ledger_module = 'RETURN PAYMENT' THEN DATE_FORMAT((SELECT edit_date FROM payment WHERE payment_id = gl.payment_id),'%Y-%m-%d')
		WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) THEN (SELECT payment_date FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.input_date, '%Y-%m-%d') FROM invoice i LEFT JOIN payment p ON p.`invoice_id` = i.invoice_id WHERE p.payment_id = gl.payment_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 AND gl.general_ledger_module = 'RETURN INVOICE' THEN (SELECT DATE_FORMAT(i.sync_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT DATE_FORMAT(i.invoice_date, '%Y-%m-%d') FROM invoice i LEFT JOIN invoice_detail id ON id.invoice_id = i.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT DATE_FORMAT(jm.gl_add_date, '%Y-%m-%d') FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS gl_date,
				
	CASE WHEN gl.payment_id IS NOT NULL AND (gl.general_ledger_for = 1 OR gl.general_ledger_for = 2 OR gl.general_ledger_for = 3 OR gl.general_ledger_for = 4 OR gl.general_ledger_for = 5 OR gl.general_ledger_for = 6 OR gl.general_ledger_for = 7 OR gl.general_ledger_for = 8 OR gl.general_ledger_for = 9 OR gl.general_ledger_for = 10 OR gl.general_ledger_for = 12 OR gl.general_ledger_for = 13) THEN (SELECT exchange_rate FROM payment WHERE payment_id = gl.payment_id )
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_for = 10 THEN (SELECT exchange_rate FROM invoice_detail WHERE invoice_detail_id = gl.invoice_id)
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jd.exchange_rate FROM gl_detail jd WHERE jd.gl_detail_id = gl.jurnal_id)
		ELSE '' END AS exchange_rate,
               
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_code FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id ))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND (SELECT payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code,
				
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT bank_type FROM bank WHERE bank_id = (SELECT bank_id FROM payment WHERE payment_id = gl.payment_id )) 
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND (SELECT payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type,
				
	CASE WHEN gl.payment_id IS NOT NULL THEN (SELECT currency_code FROM currency WHERE currency_id = (SELECT currency_id FROM payment WHERE payment_id = gl.payment_id))
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)		
		WHEN gl.transaction_id IS NOT NULL AND (SELECT payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code,
				
	CASE WHEN (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id ) = 0 THEN 'HOF'
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.contract_id IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = p.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.transaction_id IS NOT NULL AND (SELECT payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN 'HOF'
		ELSE (SELECT stockpile_code FROM stockpile WHERE stockpile_id = (SELECT payment_location FROM payment WHERE payment_id = gl.payment_id )) END AS payment_location2,
                
	CASE WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_module = 'STOCK TRANSIT' THEN (SELECT st.kode_stock_transit FROM stock_transit st LEFT JOIN stockpile_contract sc ON st.stockpile_contract_id = sc.stockpile_contract_id WHERE sc.contract_id = gl.contract_id LIMIT 1)
		WHEN gl.contract_id IS NOT NULL AND gl.general_ledger_module = 'CONTRACT ADJUSTMENT' THEN (SELECT CONCAT(po_no,'-A') FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.contract_id IS NOT NULL THEN (SELECT po_no FROM contract WHERE contract_id = gl.contract_id) 
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_module = 'RETURN INVOICE' THEN (SELECT CONCAT(i.invoice_no,'-R') FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.invoice_id IS NOT NULL AND gl.general_ledger_module = 'INVOICE DETAIL' THEN (SELECT i.invoice_no FROM invoice i LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id)
		WHEN gl.payment_id IS NOT NULL AND gl.general_ledger_module = 'RETURN PAYMENT' THEN (SELECT CONCAT(payment_no,'-R') FROM payment WHERE payment_id = gl.payment_id)
		WHEN gl.payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.gl_add_no FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.transaction_id IS NOT NULL THEN (SELECT slip_no FROM TRANSACTION WHERE transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no,
		
	CASE WHEN gl.contract_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN stockpile_contract sc ON p.stockpile_contract_id = sc.stockpile_contract_id LEFT JOIN contract c ON c.contract_id = sc.contract_id WHERE c.contract_id = gl.contract_id AND p.payment_status = 0 LIMIT 1)
		WHEN gl.invoice_id IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN invoice i ON i.invoice_id = p.invoice_id LEFT JOIN invoice_detail id ON i.invoice_id = id.invoice_id WHERE id.invoice_detail_id = gl.invoice_id AND p.payment_status = 0 LIMIT 1 )
		WHEN gl.payment_id IS NOT NULL THEN (SELECT payment_no FROM payment WHERE payment_id = gl.payment_id ) 
		WHEN gl.jurnal_id IS NOT NULL AND gl.general_ledger_for = 11 THEN (SELECT jm.gl_add_no FROM gl_add jm LEFT JOIN gl_detail jd ON jd.gl_add_id = jm.gl_add_id WHERE jd.gl_detail_id = gl.jurnal_id)
		WHEN gl.transaction_id IS NOT NULL AND (SELECT payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no2,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_code FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_code_hc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_fc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_uc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT b.bank_type FROM bank b LEFT JOIN payment p ON p.bank_id = b.bank_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS bank_type_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT pcur.currency_code FROM currency pcur LEFT JOIN payment p ON p.currency_id = pcur.currency_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS pcur_currency_code_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_fc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_uc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT CASE WHEN p.payment_location = 0 THEN 'HOF'
		ELSE sp.stockpile_code END AS sc FROM payment p LEFT JOIN stockpile sp ON p.payment_location = sp.stockpile_id LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_location_hc,
	
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT fc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.fc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_fc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT uc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.uc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_uc,
		
	CASE WHEN gl.transaction_id IS NOT NULL AND (SELECT hc_payment_id FROM TRANSACTION WHERE transaction_id = gl.transaction_id) IS NOT NULL THEN (SELECT p.payment_no FROM payment p LEFT JOIN TRANSACTION t ON t.hc_payment_id = p.payment_id WHERE t.transaction_id = gl.transaction_id)
		ELSE '' END AS payment_no_hc,
		
(SELECT freight_id FROM payment WHERE payment_id = gl.payment_id) AS freight_id,
(SELECT vendor_handling_id FROM payment WHERE payment_id = gl.payment_id) AS vendor_handling_id,  
(SELECT labor_id FROM payment WHERE payment_id = gl.payment_id) AS labor_id, 
(SELECT payment_status FROM payment WHERE payment_id = gl.payment_id) AS payment_status, 
(SELECT payment_date FROM payment WHERE payment_id = gl.payment_id) AS payment_date,
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS fc_tax_category, 
(SELECT tax_value FROM tax WHERE tax_id = (SELECT pph_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS fc_tax,
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM payment WHERE payment_id = gl.payment_id ))) AS vhc_tax_category, 
(SELECT tax_value FROM tax WHERE tax_id = (SELECT pph_tax_id FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM payment WHERE payment_id = gl.payment_id ))) AS vhc_tax,  
(SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )  AS gv_id,
(SELECT t.fc_tax_id FROM `transaction` t WHERE t.transaction_id = gl.transaction_id) AS fc_tax_id,
(SELECT vh.pph_tax_id FROM vendor_handling vh LEFT JOIN vendor_handling_cost vhc ON vhc.vendor_handling_id = vh.vendor_handling_id LEFT JOIN TRANSACTION t ON t.handling_cost_id = vhc.handling_cost_id WHERE t.transaction_id = gl.transaction_id) AS vhc_tax_id, 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT fc_tax_id FROM `transaction` WHERE transaction_id = gl.transaction_id))  AS tf_tax_category,
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = (SELECT handling_cost_id FROM `transaction` WHERE transaction_id = gl.transaction_id))))  AS tvh_tax_category,
(SELECT pph_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )) AS gv_pph_id, 
(SELECT ppn_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id ))  AS gv_ppn_id, 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT pph_tax_id FROM general_vendor WHERE general_vendor_id = (SELECT general_vendor_id FROM payment WHERE payment_id = gl.payment_id )))  AS gv_pph_category, 
(SELECT tax_category FROM tax WHERE tax_id = (SELECT ppn_tax_id FROM freight WHERE freight_id = (SELECT freight_id FROM payment WHERE payment_id = gl.payment_id ))) AS gv_ppn_category, 
(SELECT account_type FROM account WHERE account_id = gl.account_id) AS  account_type, 
(SELECT tax_value FROM tax WHERE tax_id = (SELECT fc_tax_id FROM `transaction` t WHERE t.transaction_id = gl.transaction_id)) AS fc_tax_value,
(SELECT tax_value FROM tax WHERE tax_id = (SELECT pph_tax_id FROM vendor_handling WHERE vendor_handling_id = (SELECT vendor_handling_id FROM vendor_handling_cost WHERE handling_cost_id = (SELECT handling_cost_id FROM `transaction` WHERE transaction_id = gl.transaction_id))))  AS vhc_tax_value,
(SELECT general_vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id= gl.jurnal_id )) AS general_vendor_id, 
(SELECT vendor_id FROM gl_add WHERE gl_add_id = (SELECT gl_add_id FROM gl_detail WHERE gl_detail_id= gl.jurnal_id )) AS vendor_id, 
(SELECT payment_method FROM payment WHERE payment_id = gl.payment_id ) AS payment_method,
(SELECT payment_type FROM payment WHERE payment_id = gl.payment_id ) AS payment_type
 FROM general_ledger gl 
 WHERE  gl.amount > 0 {$whereCondition}";
    $resulta = $myDatabase->query($sqla, MYSQLI_STORE_RESULT);
    while ($row = $resulta->fetch_object()) {

        $sql2 = "SELECT s.stockpile_name FROM stockpile s INNER JOIN stockpile_contract sc ON s.`stockpile_id` = sc.`stockpile_id` 
INNER JOIN contract c ON sc.`contract_id` = c.`contract_id` WHERE sc.`quantity` > 0 AND c.`contract_no` = '$row->contract_no'";
        $result2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
        while ($row2 = $result2->fetch_object()) {
            $stockpileName2 = $row2->stockpile_name;
        }

        //$rowActive++;
        if ($row->transaction_id != NULL && $row->account_id == 147 && $row->general_ledger_for == 1) {
            $stockpileName = $stockpileName2;
        } else {
            $stockpileName = $row->stockpile_name2;
        }


        if ($row->general_ledger_id == '' && $row->amount < 0) {
            $credit_amount = $row->amount * -1;
            $debit_amount = 0;
        } elseif ($row->general_ledger_id == '' && $row->amount > 0) {
            $debit_amount = $row->amount;
            $credit_amount = 0;
        }
        if ($row->general_ledger_id != '' && ($row->general_ledger_module == 'NOTA TIMBANG' || ($row->general_ledger_module == 'CONTRACT' && ($row->general_ledger_for == 1 || $row->general_ledger_for == 2)) || $row->general_ledger_module == 'STOCK TRANSIT' || $row->general_ledger_module == 'CONTRACT ADJUSTMENT' || $row->general_ledger_module == 'JURNAL MEMORIAL')) {
            $debit_amount = $row->amount;
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'INVOICE DETAIL' && $row->general_ledger_for == 10 && $row->invoice_id != 0) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN INVOICE' && $row->general_ledger_for == 10 && $row->invoice_id != 0) {
            $credit_amount = $row->amount;
        }
//==CREDIT NOTIM==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 3 || $row->general_ledger_for == 13 || $row->general_ledger_for == 14) && $row->account_no == 230204) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 3 || $row->general_ledger_for == 13 || $row->general_ledger_for == 14) && $row->account_no == 210103) {
            $credit_amount = $row->amount;
        }
//==END CREDIT NOTIM==//

//==DEBIT NOTIM==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 3 || $row->general_ledger_for == 13) && $row->account_no == 140000) {
            $debit_amount = $row->amount * ((100 - $row->tf_tax_value) / 100);
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 3 || $row->general_ledger_for == 13) && $row->account_no == 230204 && $row->quantity < 0) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 3 || $row->general_ledger_for == 13) && $row->account_no == 230204) {
            $debit_amount = 0;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'NOTA TIMBANG' && $row->tf_tax_category == 0 && ($row->general_ledger_for == 14) && $row->account_no == 230204) {
            $debit_amount = $row->amount;;
        }
//==END DEBIT NOTIM==//

//==PAYMENT CREDIT===//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 1) {
            $debit_amount = $row->amount;
//$credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 1) {
//$debit_amount = $row->amount;
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 1) {
            $debit_amount = $row->amount;
//$credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 1) {
//$debit_amount = $row->amount;
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PETTY CASH' && $row->general_ledger_for == 12) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT ADMIN') {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 12) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 12) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 10 && $row->payment_id != 0) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 10 && $row->payment_id != 0) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->labor_id != 0 && $row->pph_journal != 0 && $row->payment_status != 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->pph_journal;
        }
//==FREIGHT==//

        if ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3 && $row->payment_method == 2) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->account_id == 5 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003)) {
            $credit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->payment_method == 2 && $row->freight_id != 0 && $row->pph_journal != 0 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->payment_method == 1 && $row->freight_id != 0 && $row->ppn_journal != 0 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && ($row->account_no == 150410)) {
            $credit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->general_ledger_method == 'DP' && $row->payment_status == 1 && ($row->fc_tax_category == 0 || $row->fc_tax_category == 1) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 0 && $row->payment_status == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = 0;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 1 && $row->payment_status == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = 0;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->general_ledger_method == 'Down Payment' && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status == 1) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->fc_tax_category == 0 && $row->amountPayment < 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->payment_status == 1 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->payment_status == 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status == 1) {
            $credit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status == 1) {
            $credit_amount = $row->amount;
        }
//==END FREIGHT==//

//==VENDOR HANDLING==//

        if ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13 && $row->payment_method == 2) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->account_id == 397 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->payment_method == 2 && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $credit_amount = $row->amount * ((100 - $row->vhc_tax) / 100);
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $credit_amount = $row->amount;
        }


        if ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->general_ledger_method == 'DP' && $row->payment_status == 1 && ($row->vhc_tax_category == 0 || $row->vhc_tax_category == 1) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 0 && $row->payment_status == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = 0;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 1 && $row->payment_status == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = 0;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->general_ledger_method == 'Down Payment' && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130006) && $row->payment_status == 1) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && ($row->account_no == 210106 || $row->account_no == 130006) && $row->payment_status == 1) {
            $credit_amount = $row->amount * ((100 - $row->vhc_tax) / 100);
        } elseif ($row->general_ledger_type == 2 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130003) && $row->payment_status == 1) {
            $credit_amount = $row->amount;
        }
//==END VENDOR HANDLING==//

//==GENERAL VENDOR==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->account_no == 150410 || $row->account_no == 230100)) {
            $credit_amount = $row->ppn_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_type == 7 && $row->gv_pph_id == 21 && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $credit_amount = $row->amount + $row->ppn_journal + $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $credit_amount = ($row->amount + $row->ppn_journal) - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0) {
            $credit_amount = $row->amount + $row->ppn_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->account_type == 7) {
            $credit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0) {
            $credit_amount = $row->amount;
        }
//elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT'){
//$credit_amount = $row->amount;
//}

        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201 || $row->account_no == 150440) && $row->pph_journal != 0) {
            $credit_amount = $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $credit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal) + $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $credit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal) - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $credit_amount = $row->amount + $row->ppn_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->pph_journal != 0 && $row->account_type == 7) {
            $credit_amount = $row->amount + $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->account_type == 7) {
            $credit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0) {
            $credit_amount = $row->amount;
        }


//elseif($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT'){
//$credit_amount = $row->amount;
//}
//==END GENERAL VENDOR==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_type == 7 && ($row->general_ledger_for == 1)) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 150410 && $row->general_ledger_for == 1) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 4 && $row->account_type == 7 && $row->payment_status != 1) {
            $credit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 6 && $row->account_type == 7 && $row->general_ledger_for2 == 'Internal Transfer') {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 6 && $row->account_type == 7 && $row->general_ledger_for2 == 'Internal Transfer') {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 130001 && $row->general_ledger_for == 1) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_type == 7 && $row->pph_journal != 0 && $row->ppn_journal != 0 && $row->payment_status != 1 && $row->general_ledger_method == 'Down Payment') {
            $credit_amount = $row->amount + $row->ppn_journal - $row->pph_journal;
        }
//==UNLOADING COST==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for2 == 'Unloading Cost' && $row->account_type == 7) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 210104) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 130004) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_no == 210104) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_no == 130004) {
            $credit_amount = $row->amount;
        }
//==END UNLOADING COST==//
//==CURAH==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 2 && ($row->account_no == 210101 || $row->account_no == 130002)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 2 && $row->account_type == 7) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 2) {
            $credit_amount = $row->amount;
        }
//==END CURAH==//
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && ($row->general_ledger_for == 5)) {
            $credit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && ($row->general_ledger_for == 5)) {
            $credit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && ($row->general_ledger_for == 5)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && ($row->general_ledger_for == 5)) {
            $debit_amount = $row->amount;
        }
//==END PAYMENT CREDIT===//

        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'INVOICE DETAIL' && $row->general_ledger_for == 10 && $row->invoice_id != 0) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN INVOICE' && $row->general_ledger_for == 10 && $row->invoice_id != 0) {
            $debit_amount = $row->amount;
        }

//==PAYMENT DEBIT==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PETTY CASH' && $row->general_ledger_for == 12) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT ADMIN') {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 12) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 12) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 10 && $row->payment_id != 0) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 10 && $row->payment_id != 0) {
            $debit_amount = $row->amount;
        }
        //==FREIGHT==//

        if ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->general_ledger_method == 'DP' && ($row->fc_tax_category == 0 || $row->fc_tax_category == 1) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = 0;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->fc_tax_category == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = 0;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->general_ledger_method == 'Down Payment' && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && $row->amountPayment < 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003)) {
            $debit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PAYMENT ADMIN' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003)) {
            $debit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->freight_id != 0 && ($row->account_no == 150410)) {
            $debit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->payment_status == 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3 && $row->payment_method == 2) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->pph_journal != 0 && $row->payment_status == 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->payment_status == 1 && $row->fc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->payment_status == 1 && $row->fc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->payment_status == 1 && $row->account_id == 5 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->payment_method == 2 && $row->freight_id != 0 && $row->pph_journal != 0 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } /*elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->payment_method == 2 && $row->freight_id != 0 && $row->payment_status == 1 && $row->pph_journal != 0 && $row->general_ledger_for == 3){
$debit_amount = $row->amount ;
}
elseif($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->payment_method == 1 && $row->freight_id != 0 && $row->payment_status == 1 && $row->ppn_journal != 0 && $row->general_ledger_for == 3){
$debit_amount = $row->amount;
}*/ elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->payment_method == 1 && $row->freight_id != 0 && $row->ppn_journal != 0 && $row->general_ledger_for == 3) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && $row->fc_tax_category == 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status == 1) {
            $debit_amount = $row->amount * ((100 - $row->fc_tax) / 100);
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->freight_id != 0 && ($row->account_no == 210103 || $row->account_no == 130003) && $row->payment_status == 1) {
            $debit_amount = $row->amount;
        }
//==END FREIGHT==//
//==VENDOR HANDLING==//

        if ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->general_ledger_method == 'DP' && ($row->vhc_tax_category == 0 || $row->vhc_tax_category == 1) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = 0;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->vhc_tax_category == 1 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = 0;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->general_ledger_method == 'Down Payment' && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $debit_amount = $row->amount * ((100 - $row->vhc_tax) / 100);
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $debit_amount = $row->amount;
        }

        if ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13 && $row->payment_method == 2) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->pph_journal != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 1 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->vhc_tax_category == 0 && $row->account_type == 7 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->account_id == 397 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT') && $row->payment_method == 2 && $row->vendor_handling_id != 0 && $row->payment_status == 1 && $row->pph_journal != 0 && $row->general_ledger_for == 13) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && $row->vhc_tax_category == 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $debit_amount = $row->amount * ((100 - $row->vhc_tax) / 100);
        } elseif ($row->general_ledger_type == 1 && ($row->general_ledger_module == 'RETURN PAYMENT' || $row->general_ledger_module == 'PETTY CASH') && $row->vendor_handling_id != 0 && ($row->account_no == 210106 || $row->account_no == 130006)) {
            $debit_amount = $row->amount;
        }
//==END VENDOR HANDLING==//
//==UC==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for2 == 'Unloading Cost' && $row->account_type == 7 /*&& $row->payment_status != 1*/) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_no == 210104) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_no == 130004) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 210104) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 130004) {
            $debit_amount = $row->amount;
        }
//==END UC==//

//==PKS KONTRAK//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->account_no == 210102 && $row->payment_status != 1) {
            $debit_amount = $row->amount;
        }
//==END PKS KONTRAK//
//==GENERAL VENDOR==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201 || $row->account_no == 150440) && $row->pph_journal != 0) {
            $debit_amount = $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8) && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $debit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal) + $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $debit_amount = ($row->amount + $row->ppn_journal + $row->ppn_journal) - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->general_ledger_for == 5 || $row->general_ledger_for == 8)) {
            $debit_amount = $row->amount + $row->ppn_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->gv_pph_id == 21 && $row->pph_journal != 0 && $row->account_type == 7) {
            $debit_amount = $row->amount + $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->account_type == 7) {
            $debit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->gv_id != 0) {
            $debit_amount = $row->amount;
        }
//elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT'){
//$debit_amount = $row->amount;
//}

        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0 && ($row->account_no == 150410 || $row->account_no == 230100)) {
            $debit_amount = $row->ppn_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_transaction_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_type == 7 && $row->gv_pph_id == 21 && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $debit_amount = $row->amount + $row->ppn_journal + $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->ppn_journal != 0) {
            $debit_amount = ($row->amount + $row->ppn_journal) - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->ppn_journal != 0) {
            $debit_amount = $row->amount + $row->ppn_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0 && $row->pph_journal != 0 && $row->account_type == 7) {
            $debit_amount = $row->amount - $row->pph_journal;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->gv_id != 0) {
            $debit_amount = $row->amount;
        }
//elseif($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT'){
//$debit_amount = $row->amount;
//}
//==END GENERAL VENDOR==//
//==CURAH==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 2) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 2 && ($row->account_no == 210101 || $row->account_no == 130002)) {
            $debit_amount = $row->amount;
        } elseif ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 2 && $row->account_type == 7) {
            $debit_amount = $row->amount;
        }
//==END CURAH==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && ($row->account_no == 150410 || $row->account_no == 230100)) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->account_type == 7 && $row->general_ledger_for == 6 && $row->general_ledger_for2 == 'Internal Transfer') {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->account_type == 7 && $row->general_ledger_for == 6 && $row->general_ledger_for2 == 'Internal Transfer') {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->account_no == 130001 && $row->general_ledger_for == 1) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->payment_status != 1 && $row->supplier_name == 'Kas Negara' && ($row->account_no == 230202 || $row->account_no == 230203 || $row->account_no == 230207 || $row->account_no == 230208 || $row->account_no == 230204 || $row->account_no == 230205 || $row->account_no == 230206 || $row->account_no == 230201)) {
            $debit_amount = $row->amount;
        }
//==SALES==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 9 && $row->payment_status != 1) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 9 && $row->payment_status != 1) {
            $credit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 9 && $row->payment_status == 1) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'RETURN PAYMENT' && $row->general_ledger_for == 9 && $row->payment_status == 1) {
            $credit_amount = $row->amount;
        }
//==END SALES==//
        if ($row->general_ledger_type == 1 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 10 && $row->payment_status != 1) {
            $debit_amount = $row->amount;
        }
        if ($row->general_ledger_type == 2 && $row->general_ledger_module == 'PAYMENT' && $row->general_ledger_for == 10 && $row->payment_status != 1) {
            $credit_amount = $row->amount;
        }
        if ($row->payment_status == 1 && $row->gl_date < '2018-04-01') {
            $debit_amount = 'RETURN';
            $credit_amount = 'RETURN';
        }
        if ($row->invoice_payment == 2 && $row->gl_date < '2018-04-01') {
            $debit_amount = 'RETURN';
            $credit_amount = 'RETURN';
        }

//$general_ledger_id = $row->general_ledger_id;

        $voucherNo = "";
        $paymentNo = "";
        if ($row->contract_id != '' && $row->general_ledger_module == 'CONTRACT ADJUSTMENT') {
            $voucherNo = $row->payment_no;
            $paymentNo = 'JV-PO-' . $row->payment_no2;
        } elseif ($row->contract_id != '' && $row->general_ledger_module != 'CONTRACT ADJUSTMENT') {
            $voucherCode = $row->payment_location2 . '/' . $row->bank_code . '/' . $row->pcur_currency_code;


            if ($row->bank_type == 1) {
                $voucherCode .= ' - B';
            } elseif ($row->bank_type == 2) {
                $voucherCode .= ' - P';
            } elseif ($row->bank_type == 3) {
                $voucherCode .= ' - CAS';
            }

            if ($row->bank_type != 3) {
                if ($row->payment_type == 1) {
                    $voucherCode .= 'RV';
                } else {
                    $voucherCode .= 'PV';
                }
            }


            $voucherNo = $row->payment_no;
            $paymentNo = $voucherCode . ' # ' . $row->payment_no2;
        } elseif ($row->payment_id != '') {


            $voucherCode = $row->payment_location2 . '/' . $row->bank_code . '/' . $row->pcur_currency_code;


            if ($row->bank_type == 1) {
                $voucherCode .= ' - B';
            } elseif ($row->bank_type == 2) {
                $voucherCode .= ' - P';
            } elseif ($row->bank_type == 3) {
                $voucherCode .= ' - CAS';
            }

            if ($row->bank_type != 3) {
                if ($row->payment_type == 1) {
                    $voucherCode .= 'RV';
                } else {
                    $voucherCode .= 'PV';
                }
            }

            $voucherNo = $voucherCode . ' # ' . $row->payment_no;
            $paymentNo = $voucherCode . ' # ' . $row->payment_no2;

        } elseif ($row->invoice_id != '') {


            $voucherCode = $row->payment_location2 . '/' . $row->bank_code . '/' . $row->pcur_currency_code;


            if ($row->bank_type == 1) {
                $voucherCode .= ' - B';
            } elseif ($row->bank_type == 2) {
                $voucherCode .= ' - P';
            } elseif ($row->bank_type == 3) {
                $voucherCode .= ' - CAS';
            }

            if ($row->bank_type != 3) {
                if ($row->payment_type == 1) {
                    $voucherCode .= 'RV';
                } else {
                    $voucherCode .= 'PV';
                }
            }

            $voucherNo = $row->payment_no;
            $paymentNo = $voucherCode . ' # ' . $row->payment_no2;
        } else {
            $voucherNo = $row->payment_no;
            $paymentNo = $row->payment_no2;
        }

        $general_ledger_id = $row->general_ledger_id;
        $stockpile = $row->stockpile_name2;
        $gl_date = $row->gl_date;
        $general_ledger_module = $row->general_ledger_module;
        $general_ledger_method = $row->general_ledger_method;
        $general_ledger_transaction_type2 = $row->general_ledger_transaction_type2;
        $supplier_name = $row->supplier_name;
        $supplier_code = $row->supplier_code;
        $po_no = $row->po_no;
        $contract_no = $row->contract_no;
        $slip_no = $row->slip_no;
        $invoice_no = $row->invoice_no;
        $invoice_no_2 = $row->invoice_no_2;
        $tax_invoice = $row->tax_invoice;
        $cheque_no = $row->cheque_no;
        $remarks = $row->remarks;
        $shipment_code = $row->shipment_code;
        $quantity = $row->quantity;
        $price = $row->price;
        $account_no = $row->account_no;
        $account_name = $row->account_name;
        $exchange_rate = $row->exchange_rate;

        $date = new DateTime();
        $currentDate = $date->format('d/m/Y H:i:s');

        $debitAmount = 0;
        if ($row->general_ledger_type == 1) {
            $debitAmount = $debit_amount;
        } elseif ($row->general_ledger_type == '') {
            $debitAmount = $debit_amount;
        }
        $creditAmount = 0;
        if ($row->general_ledger_type == 2) {
            $creditAmount = $credit_amount;
        } elseif ($row->general_ledger_type == '') {
            $creditAmount = $credit_amount;
        }

        $sqlb = "INSERT INTO gl_report (general_ledger_id, contract_id, invoice_id, transaction_id, jurnal_id, payment_id, source, stockpile,gl_date, jurnal_no, general_ledger_module, general_ledger_method, general_ledger_transaction_type2, supplier_code,supplier_name, po_no, contract_no, slip_no, invoice_no, invoice_no_2, tax_invoice, cheque_no, remarks, shipment_code,quantity,price,account_no,account_name,exchange_rate,debitAmount,creditAmount, entry_date, entry_by) VALUES ({$general_ledger_id}, {$contractId}, {$invoiceId}, {$transactionId}, {$jurnalId}, {$paymentId}, 'GL', '{$stockpile}','{$gl_date}','{$voucherNo}', '{$general_ledger_module}', '{$general_ledger_method}', '{$general_ledger_transaction_type2}', '{$supplier_code}','{$supplier_name}', '{$po_no}', '{$contract_no}', '{$slip_no}', '{$invoice_no}', '{$invoice_no_2}', '{$tax_invoice}', '{$cheque_no}', '{$remarks}', '{$shipment_code}','{$quantity}','{$price}','{$account_no}','{$account_name}','{$exchange_rate}',{$debitAmount},{$creditAmount}, STR_TO_DATE('{$currentDate}', '%d/%m/%Y %H:%i:%s'),{$_SESSION['userId']})";
        $resultb = $myDatabase->query($sqlb, MYSQLI_STORE_RESULT);

        echo $sqlb;
    }

}

function insertGeneralLedger($myDatabase, $source, $invoiceId = "NULL", $i_id = "NULL", $contractId = "NULL", $transactionId = "NULL", $paymentId = "NULL", $jurnalId = "NULL", $returnId = "NULL")
{

    $insertValues = "";
    $boolContinue = true;

    if ($contractId != 'NULL' && $source != 'STOCK TRANSIT' && $source == 'CONTRACT ADJUSTMENT') {
        // <editor-fold defaultstate="collapsed" desc="contract">
        $sql = "DELETE FROM general_ledger WHERE general_ledger_module = 'CONTRACT ADJUSTMENT' AND contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        $adjustment = 0;
        $ppn = 0;
        $totalAdjustment = 0;
        $adjustment1 = 0;
        $ppn1 = 0;
        $totalAdjustment1 = 0;
        $sql = "SELECT con.*, v.*, a.account_no FROM contract con 
LEFT JOIN vendor v ON con.`vendor_id` = v.`vendor_id` LEFT JOIN account a ON a.account_id = con.adjustment_acc WHERE con.contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            if ($row->contract_type == 'P') {

                $adjustment = $row->price_converted * $row->adjustment;
                if ($row->adjustment_ppn == 1) {
                    $ppn = $adjustment * ($row->ppn / 100);
                } else {
                    $ppn = 0;
                }
                $totalAdjustment = $adjustment + $ppn;
                $totalAdjustment1 = $totalAdjustment;
                if ($adjustment < 0) {
                    $adjustment1 = $adjustment * -1;
                    $totalAdjustment1 = $totalAdjustment * -1;
                    $ppn1 = $ppn * -1;
                }
                $accountNo = $row->account_no;

                $sqlAccount1 = "SELECT account_id, account_no, account_name FROM account WHERE account_type = 1 AND account_no IN (130100, 150410, $accountNo)";
                $resultAccount1 = $myDatabase->query($sqlAccount1, MYSQLI_STORE_RESULT);

                if ($resultAccount1 !== false && $resultAccount1->num_rows > 0) {
                    while ($rowAccount1 = $resultAccount1->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }
                        if ($adjustment != 0 && $adjustment != '') {
                            if ($rowAccount1->account_no == 130100) {
                                if ($adjustment < 0) {
                                    $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$adjustment1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$adjustment})";
                                }
                            } elseif ($rowAccount1->account_no == 150410) {
                                if ($adjustment < 0) {
                                    $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$ppn1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$ppn})";
                                }
                            } elseif ($rowAccount1->account_no == 210102) {
                                if ($adjustment < 0) {
                                    $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                }
                            } elseif ($rowAccount1->account_no == 520100) {
                                if ($adjustment < 0) {
                                    $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                }
                            } elseif ($rowAccount1->account_no == 150120) {
                                if ($adjustment < 0) {
                                    $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$totalAdjustment1})";
                                }
                            }
                        }

                    }
                } else {
                    $boolContinue = false;
                    echo 'FALSE1';
                }
            } else {
                $boolContinue = false;
                echo 'FALSE2';
            }
        } else {
            $boolContinue = false;
            echo 'FALSE3';
        }

        // </editor-fold>
    } elseif ($contractId != 'NULL' && $source == 'STOCK TRANSIT' && $source != 'CONTRACT ADJUSTMENT') {


        echo 'TRANSIT';


        // <editor-fold defaultstate="collapsed" desc="contract">

        $sql = "DELETE FROM general_ledger WHERE general_ledger_module = 'STOCK TRANSIT' AND contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


        $sql = "SELECT a.*, (c.`price_converted` * a.send_weight) AS amt_stock_transit FROM stock_transit a 
LEFT JOIN stockpile_contract b ON a.`stockpile_contract_id` = b.`stockpile_contract_id`
LEFT JOIN contract c ON b.`contract_id` = c.`contract_id` WHERE c.contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            $amt_stock_transit = $row->amt_stock_transit;
            //$amt_ospks = $row->amt_ospks;

            $sqlAccount1 = "SELECT account_id, account_no, account_name FROM account WHERE account_type = 1 AND account_no IN (130100,141000)";
            $resultAccount1 = $myDatabase->query($sqlAccount1, MYSQLI_STORE_RESULT);

            if ($resultAccount1 !== false && $resultAccount1->num_rows > 0) {
                while ($rowAccount1 = $resultAccount1->fetch_object()) {
                    if ($insertValues != "") {
                        $insertValues .= ", ";
                    }
                    if ($amt_stock_transit != 0 && $amt_stock_transit != '') {
                        if ($rowAccount1->account_no == 130100) {
                            if ($amt_stock_transit < 0) {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$amt_stock_transit})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$amt_stock_transit})";
                            }
                        } elseif ($rowAccount1->account_no == 141000) {
                            if ($amt_stock_transit < 0) {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$amt_stock_transit})";
                            } else {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount1->account_id}, NULL, {$amt_stock_transit})";
                            }
                        }
                    }

                }
            } else {
                $boolContinue = false;
                echo 'FALSE1';
            }

        } else {
            $boolContinue = false;
            echo 'FALSE3';
            echo $sql;
        }

        // </editor-fold>
    } elseif ($contractId != 'NULL' && $source != 'STOCK TRANSIT' && $source != 'CONTRACT ADJUSTMENT') {
        // <editor-fold defaultstate="collapsed" desc="contract">

        //update surya-----------------------------------------------------------------------------------------------------
        $sql = "SELECT contract_status FROM contract
       WHERE contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            $contractStatus = $row->contract_status;
        }
        //----------------------------------------------------------------------------------------------------------------------------------


        if ($contractStatus != 2) {
            $sql = "DELETE FROM general_ledger WHERE contract_id = {$contractId}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        }

        $sql = "SELECT con.*, v.* FROM contract con 
LEFT JOIN vendor v ON con.`vendor_id` = v.`vendor_id`  WHERE con.contract_id = {$contractId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();


            if ($row->contract_type == 'P') {

                $amount = $row->price_converted * $row->quantity;

                if ($row->ppn != 0) {
                    $totalAmount = $amount * 1.1;
                } else {
                    $totalAmount = $amount;
                }
                $ppn = $totalAmount - $amount;


                $sqlAccount = "SELECT account_id, account_no, account_name FROM account WHERE account_type = 1 AND account_no IN (130100, 210102, 150410)";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        if ($contractStatus != 2) {
                            if ($rowAccount->account_no == 130100) {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, 'Kontrak beli cangkang kontrak', {$amount})";
                            } elseif ($rowAccount->account_no == 210102) {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalAmount})";
                            } elseif ($rowAccount->account_no == 150410) {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";
                            }
                        } else {
                            if ($rowAccount->account_no == 130100) {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, 'cancel kontrak', {$amount})";
                            } elseif ($rowAccount->account_no == 210102) {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalAmount})";
                            } elseif ($rowAccount->account_no == 150410) {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";
                            }
                        }
                    }
                } else {
                    $boolContinue = false;
                    echo 'FALSE1';
                }
            } else {
                $boolContinue = false;
                echo 'FALSE2';
            }
        } else {
            $boolContinue = false;
            echo 'FALSE3';
        }

        // </editor-fold>
    } elseif ($jurnalId != 'NULL') {
        // <editor-fold defaultstate="collapsed" desc="contract">

        $sql = "DELETE FROM general_ledger WHERE jurnal_id = {$jurnalId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


        $sql = "SELECT * FROM gl_detail WHERE gl_detail_id = {$jurnalId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();


            $sqlAccount = "SELECT account_id, account_no, account_name FROM account WHERE account_id = {$row->account_id}";
            $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

            if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                while ($rowAccount = $resultAccount->fetch_object()) {
                    if ($insertValues != "") {
                        $insertValues .= ", ";
                    }

                    if ($row->gl_type == 1) {
                        $insertValues .= "(1, '{$source}', NULL, NULL, 11, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_converted})";
                    } elseif ($row->gl_type == 2) {
                        $insertValues .= "(2, '{$source}', NULL, NULL, 11, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_converted})";
                    }
                }
            } else {
                $boolContinue = false;
            }


        } else {
            $boolContinue = false;
            echo 'FALSE3';
            echo $jurnalId;
        }

        // </editor-fold>
    } elseif ($invoiceId != 'NULL' && $source == 'RETURN INVOICE') {
        // <editor-fold defaultstate="collapsed" desc="invoiceDetail">
        $sql = "DELETE FROM general_ledger WHERE invoice_id = {$invoiceId} AND general_ledger_module = 'RETURN INVOICE'";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        /// echo 'test';
        $sql88 = "SELECT invoice_detail_id FROM invoice_detail WHERE invoice_detail_id IN ({$invoiceId})";
        $result88 = $myDatabase->query($sql88, MYSQLI_STORE_RESULT);
        if ($result88 !== false && $result88->num_rows >= 0) {
            while ($row88 = $result88->fetch_object()) {

                $invoiceId = $row88->invoice_detail_id;


                $sql = "SELECT id.*,i.*, a.`account_no`, a.`account_type`, apph.`account_no` AS a_pph FROM invoice_detail id
				LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id`
				LEFT JOIN account a ON id.`account_id` = a.`account_id`
				LEFT JOIN tax txpph ON id.`pphID` = txpph.`tax_id`
				LEFT JOIN tax txppn ON id.`ppnID` = txppn.`tax_id`
				LEFT JOIN account apph ON apph.`account_id` = txpph.`account_id` 
				WHERE id.invoice_detail_id = {$invoiceId}";
                $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

                if ($result !== false && $result->num_rows == 1) {
                    $row = $result->fetch_object();

                    $amount = $row->amount_converted;
                    $ppn = $row->ppn_converted;
                    $pph = $row->pph_converted;
                    $totalAmount = $row->tamount_converted;
                    $account_no = $row->account_no;
                    $invoiceMethod = $row->invoice_method_detail;
                    $gvId = $row->general_vendor_id;
                    $paymentStatus = $row->payment_status;
                    $invoiceStatus = $row->invoice_status;

                    if ($invoiceMethod = 1) {/* DP ADMIN BANK*/
                        $sql9 = "SELECT idp.*, id.invoice_id, aid.`account_no` AS accNo, adp.`account_no` AS acc_pph, dptx.tax_value AS pph_dp, id.pph AS dpPPh,
adpppn.`account_no` AS acc_ppn, dpppn.tax_value AS ppn_dp, id.ppn AS dpPPn FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.invoice_detail_dp
LEFT JOIN tax dptx ON id.`pphID` = dptx.`tax_id`
LEFT JOIN account adp ON dptx.`account_id` = adp.`account_id`
LEFT JOIN tax dpppn ON id.`ppnID` = dpppn.`tax_id`
LEFT JOIN account adpppn ON dpppn.`account_id` = adpppn.`account_id`  
LEFT JOIN account aid ON aid.`account_id` = id.`account_id`       
WHERE idp.invoice_detail_id = {$invoiceId} AND aid.`account_no` = 700080 ORDER BY id.invoice_id ASC";
                        $result9 = $myDatabase->query($sql9, MYSQLI_STORE_RESULT);

                        if ($result9 !== false && $result9->num_rows >= 0) {
                            while ($row9 = $result9->fetch_object()) {
                                //echo 'test2';
                                $tamount2 = $row9->amount_payment;
                                $i_id2 = $row9->invoice_id;
                                $accNo2 = $row9->accNo;
                                //$accountNo = $row8->accNo;
                                //$ppnDP = $row8->ppn_converted;
                                //$pphDP = $row8->pph_converted;
                                $acc_ppn2 = $row9->acc_ppn;
                                $acc_pph2 = $row9->acc_pph;
                                if ($row9->dpPPn == 0) {
                                    $ppnDP2 = 0;
                                } else {
                                    $ppnDP2 = $tamount2 * ($row9->ppn_dp / 100);
                                }
                                if ($row9->dpPPh == 0) {
                                    $pphDP2 = 0;
                                } else {
                                    $pphDP2 = $tamount2 * ($row9->pph_dp / 100);
                                }
                                echo $accNo2;


                                $sqlAccountDP2 = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in ('{$acc_pph2}', 150410 , 700080)";
                                $resultAccountDP2 = $myDatabase->query($sqlAccountDP2, MYSQLI_STORE_RESULT);

                                if ($resultAccountDP2 !== false && $resultAccountDP2->num_rows > 0) {
                                    while ($rowAccountDP2 = $resultAccountDP2->fetch_object()) {
                                        if ($insertValues != "") {
                                            $insertValues .= ", ";
                                        }

                                        if ($rowAccountDP2->account_no == 700080) {

                                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$tamount2})";

                                        } elseif ($rowAccountDP2->account_no == 150410) {

                                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$ppnDP2})";

                                        } elseif ($rowAccountDP2->account_no == $acc_pph2) {

                                            $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$pphDP2})";

                                        }
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;

                            echo 'FALSE2';
                            echo $gvId;

                        }
                    }

                    if ($invoiceMethod = 1) {

                        $totalAmountDp = 0;
                        $sql8 = "SELECT idp.*, id.invoice_id, aid.`account_no` AS accNo, adp.`account_no` AS acc_pph, dptx.tax_value AS pph_dp, id.pph AS dpPPh,
adpppn.`account_no` AS acc_ppn, dpppn.tax_value AS ppn_dp, id.ppn AS dpPPn FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.invoice_detail_dp
LEFT JOIN tax dptx ON id.`pphID` = dptx.`tax_id`
LEFT JOIN account adp ON dptx.`account_id` = adp.`account_id`
LEFT JOIN tax dpppn ON id.`ppnID` = dpppn.`tax_id`
LEFT JOIN account adpppn ON dpppn.`account_id` = adpppn.`account_id`  
LEFT JOIN account aid ON aid.`account_id` = id.`account_id`       
WHERE idp.invoice_detail_id = {$invoiceId} AND aid.`account_no` != 700080 ORDER BY id.invoice_id ASC";
                        $result8 = $myDatabase->query($sql8, MYSQLI_STORE_RESULT);

                        if ($result8 !== false && $result8->num_rows >= 0) {
                            while ($row8 = $result8->fetch_object()) {
                                //echo 'test2';
                                $tamount = $row8->amount_payment;
                                $i_id = $row8->invoice_id;
                                $accNo = $row8->accNo;
                                //$accountNo = $row8->accNo;
                                //$ppnDP = $row8->ppn_converted;
                                //$pphDP = $row8->pph_converted;
                                $acc_ppn = $row8->acc_ppn;
                                $acc_pph = $row8->acc_pph;
                                if ($row8->dpPPn == 0) {
                                    $ppnDP = 0;
                                } else {
                                    $ppnDP = $tamount * ($row8->ppn_dp / 100);
                                }
                                if ($row8->dpPPh == 0) {
                                    $pphDP = 0;
                                } else {
                                    $pphDP = $tamount * ($row8->pph_dp / 100);
                                }
                                echo $accNo;

                                $amountDp = ($tamount + $ppnDP) - $pphDP;
                                $totalAmountDp = $totalAmountDp + $amountDp;


                                $sqlAccountDP = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in ('{$acc_pph}', 150410 ,'{$accNo}')";
                                $resultAccountDP = $myDatabase->query($sqlAccountDP, MYSQLI_STORE_RESULT);

                                if ($resultAccountDP !== false && $resultAccountDP->num_rows > 0) {
                                    while ($rowAccountDP = $resultAccountDP->fetch_object()) {
                                        if ($insertValues != "") {
                                            $insertValues .= ", ";
                                        }

                                        if ($rowAccountDP->account_no == $accNo) {

                                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$tamount})";

                                        } elseif ($rowAccountDP->account_no == 150410) {

                                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$ppnDP})";

                                        } elseif ($rowAccountDP->account_no == $acc_pph) {

                                            $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$pphDP})";

                                        }
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;

                            echo 'FALSE3';
                            echo $gvId;

                        }
                    }


                    if ($invoiceMethod = 1) {

                        if ($pph == 0) {
                            $a_pph = 0;
                        } else {
                            $a_pph = $row->a_pph;
                        }

                        //echo $row->account_type;
                        //echo $account_no;
                        //echo $a_pph;
                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in (210105, 150410, '{$account_no}', '{$a_pph}')";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }
                                if ($rowAccount->account_no == $account_no) {
                                    if ($amount < 0) {
                                        $amount1 = $amount * -1;
                                        $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                    }

                                } elseif ($rowAccount->account_no == 210105) {
                                    $totalAmount = $totalAmount - $totalAmountDp;
                                    if ($totalAmount < 0) {
                                        $totalAmount1 = $totalAmount * -1;
                                        $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount1})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                                    }

                                } elseif ($rowAccount->account_no == 150410) {
                                    if ($ppn < 0) {
                                        $ppn1 = $ppn * -1;
                                        $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn1})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";
                                    }
                                } elseif ($rowAccount->account_no == $a_pph) {
                                    if ($pph < 0) {
                                        $pph1 = $pph * -1;
                                        $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph1})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }
                    } elseif ($invoiceMethod = 2) {
                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in (210105, 150410, '{$account_no}', '{$a_pph}')";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == $account_no) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";

                                } elseif ($rowAccount->account_no == 210105) {

                                    $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";

                                } elseif ($rowAccount->account_no == 150410) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";

                                } elseif ($rowAccount->account_no == $a_pph) {

                                    $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph})";

                                }
                            }
                        } else {
                            $boolContinue = false;
                        }
                    }

                } else {
                    $boolContinue = false;
                }
            }
        }

        // </editor-fold>
    } elseif ($invoiceId != 'NULL' && $source == 'INVOICE DETAIL') {
        // <editor-fold defaultstate="collapsed" desc="invoiceDetail">

        $sql = "DELETE FROM general_ledger WHERE invoice_id = {$invoiceId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        $sql = "SELECT id.*,i.*, a.`account_no`, a.`account_type`, apph.`account_no` AS a_pph FROM invoice_detail id
				LEFT JOIN invoice i ON i.`invoice_id` = id.`invoice_id`
				LEFT JOIN account a ON id.`account_id` = a.`account_id`
				LEFT JOIN tax txpph ON id.`pphID` = txpph.`tax_id`
				LEFT JOIN tax txppn ON id.`ppnID` = txppn.`tax_id`
				LEFT JOIN account apph ON apph.`account_id` = txpph.`account_id` 
				WHERE id.invoice_detail_id = {$invoiceId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            /*if($row->amount_converted == 0 && $row->amount == 0 && $row->pph_converted == 0 && $row->ppn_converted != 0){
					$amount = $row->tamount_converted;
				}else{*/
            $amount = $row->amount_converted;
            //}
            $ppn = $row->ppn_converted;
            $pph = $row->pph_converted;
            $totalAmount = $row->tamount_converted;
            $account_no = $row->account_no;
            $invoiceMethod = $row->invoice_method_detail;
            $gvId = $row->general_vendor_id;
            $paymentStatus = $row->payment_status;
            $invoiceStatus = $row->invoice_status;

            if ($invoiceMethod = 1) {/* DP ADMIN BANK*/


                $sql9 = "SELECT idp.*, id.invoice_id, aid.`account_no` AS accNo, adp.`account_no` AS acc_pph, dptx.tax_value AS pph_dp, id.pph AS dpPPh,
adpppn.`account_no` AS acc_ppn, dpppn.tax_value AS ppn_dp, id.ppn AS dpPPn FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.invoice_detail_dp
LEFT JOIN tax dptx ON id.`pphID` = dptx.`tax_id`
LEFT JOIN account adp ON dptx.`account_id` = adp.`account_id`
LEFT JOIN tax dpppn ON id.`ppnID` = dpppn.`tax_id`
LEFT JOIN account adpppn ON dpppn.`account_id` = adpppn.`account_id`  
LEFT JOIN account aid ON aid.`account_id` = id.`account_id`       
WHERE idp.invoice_detail_id = {$invoiceId} AND aid.`account_no` = 700080 ORDER BY id.invoice_id ASC";
                $result9 = $myDatabase->query($sql9, MYSQLI_STORE_RESULT);

                if ($result9 !== false && $result9->num_rows >= 0) {
                    while ($row9 = $result9->fetch_object()) {
                        //echo 'test2';
                        $tamount2 = $row9->amount_payment;
                        $i_id2 = $row9->invoice_id;
                        $accNo2 = $row9->accNo;
                        //$accountNo = $row8->accNo;
                        //$ppnDP = $row8->ppn_converted;
                        //$pphDP = $row8->pph_converted;
                        $acc_ppn2 = $row9->acc_ppn;
                        $acc_pph2 = $row9->acc_pph;
                        if ($row9->dpPPn == 0) {
                            $ppnDP2 = 0;
                        } else {
                            $ppnDP2 = $tamount2 * ($row9->ppn_dp / 100);
                        }
                        if ($row9->dpPPh == 0) {
                            $pphDP2 = 0;
                        } else {
                            $pphDP2 = $tamount2 * ($row9->pph_dp / 100);
                        }
                        echo $accNo2;


                        $sqlAccountDP2 = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in ('{$acc_pph2}', 150410 , 700080)";
                        $resultAccountDP2 = $myDatabase->query($sqlAccountDP2, MYSQLI_STORE_RESULT);

                        if ($resultAccountDP2 !== false && $resultAccountDP2->num_rows > 0) {
                            while ($rowAccountDP2 = $resultAccountDP2->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccountDP2->account_no == 700080) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$tamount2})";

                                } elseif ($rowAccountDP2->account_no == 150410) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$ppnDP2})";

                                } elseif ($rowAccountDP2->account_no == $acc_pph2) {

                                    $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id2}, 0, {$rowAccountDP2->account_id}, NULL, {$pphDP2})";

                                }
                            }
                        }
                    }
                } else {
                    $boolContinue = false;

                    echo 'FALSE2';
                    echo $gvId;

                }
            }

            if ($invoiceMethod = 1) {


                $sql8 = "SELECT idp.*, id.invoice_id, aid.`account_no` AS accNo, adp.`account_no` AS acc_pph, dptx.tax_value AS pph_dp, id.pph AS dpPPh,
adpppn.`account_no` AS acc_ppn, dpppn.tax_value AS ppn_dp, id.ppn AS dpPPn FROM invoice_dp idp
LEFT JOIN invoice_detail id ON id.`invoice_detail_id` = idp.invoice_detail_dp
LEFT JOIN tax dptx ON id.`pphID` = dptx.`tax_id`
LEFT JOIN account adp ON dptx.`account_id` = adp.`account_id`
LEFT JOIN tax dpppn ON id.`ppnID` = dpppn.`tax_id`
LEFT JOIN account adpppn ON dpppn.`account_id` = adpppn.`account_id`  
LEFT JOIN account aid ON aid.`account_id` = id.`account_id`       
WHERE idp.invoice_detail_id = {$invoiceId} AND aid.`account_no` != 700080 ORDER BY id.invoice_id ASC";
                $result8 = $myDatabase->query($sql8, MYSQLI_STORE_RESULT);

                if ($result8 !== false && $result8->num_rows >= 0) {
                    while ($row8 = $result8->fetch_object()) {
                        //echo 'test2';
                        $tamount = $row8->amount_payment;
                        $i_id = $row8->invoice_id;
                        $accNo = $row8->accNo;
                        //$accountNo = $row8->accNo;
                        //$ppnDP = $row8->ppn_converted;
                        //$pphDP = $row8->pph_converted;
                        $acc_ppn = $row8->acc_ppn;
                        $acc_pph = $row8->acc_pph;
                        if ($row8->dpPPn == 0) {
                            $ppnDP = 0;
                        } else {
                            $ppnDP = $tamount * ($row8->ppn_dp / 100);
                        }
                        if ($row8->dpPPh == 0) {
                            $pphDP = 0;
                        } else {
                            $pphDP = $tamount * ($row8->pph_dp / 100);
                        }
                        echo $accNo;

                        $amountDp = ($tamount + $ppnDP) - $pphDP;
                        $totalAmountDp = $totalAmountDp + $amountDp;


                        $sqlAccountDP = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in ('{$acc_pph}', 150410 ,'{$accNo}')";
                        $resultAccountDP = $myDatabase->query($sqlAccountDP, MYSQLI_STORE_RESULT);

                        if ($resultAccountDP !== false && $resultAccountDP->num_rows > 0) {
                            while ($rowAccountDP = $resultAccountDP->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccountDP->account_no == $accNo) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$tamount})";

                                } elseif ($rowAccountDP->account_no == 150410) {

                                    $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$ppnDP})";

                                } elseif ($rowAccountDP->account_no == $acc_pph) {

                                    $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, {$i_id}, 0, {$rowAccountDP->account_id}, NULL, {$pphDP})";

                                }
                            }
                        }
                    }
                } else {
                    $boolContinue = false;

                    echo 'FALSE3';
                    echo $gvId;

                }
            }


            if ($invoiceMethod = 1) {

                if ($pph == 0) {
                    $a_pph = 0;
                } else {
                    $a_pph = $row->a_pph;
                }

                //echo $row->account_type;
                //echo $account_no;
                //echo $a_pph;
                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in (210105, 150410, '{$account_no}', '{$a_pph}')";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        if ($rowAccount->account_no == $account_no) {
                            if ($amount < 0) {
                                $amount1 = $amount * -1;
                                $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                            } else {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }

                        } elseif ($rowAccount->account_no == 210105) {
                            $totalAmount = $totalAmount - $totalAmountDp;
                            if ($totalAmount < 0) {
                                $totalAmount1 = $totalAmount * -1;
                                $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount1})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                            }

                        } elseif ($rowAccount->account_no == 150410) {
                            if ($ppn < 0) {
                                $ppn1 = $ppn * -1;
                                $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn1})";
                            } else {
                                $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";
                            }
                        } elseif ($rowAccount->account_no == $a_pph) {
                            if ($pph < 0) {
                                $pph1 = $pph * -1;
                                $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph1})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph})";
                            }
                        }
                    }
                } else {
                    $boolContinue = false;
                }
            } elseif ($invoiceMethod = 2) {
                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = {$row->account_type} AND account_no in (210105, 150410, '{$account_no}', '{$a_pph}')";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        if ($rowAccount->account_no == $account_no) {

                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";

                        } elseif ($rowAccount->account_no == 210105) {

                            $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";

                        } elseif ($rowAccount->account_no == 150410) {

                            $insertValues .= "(1, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn})";

                        } elseif ($rowAccount->account_no == $a_pph) {

                            $insertValues .= "(2, '{$source}', NULL, NULL, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph})";

                        }
                    }
                } else {
                    $boolContinue = false;
                }
            }

        } else {
            $boolContinue = false;
        }

        // </editor-fold>
    } elseif ($transactionId != 'NULL' && $source == 'NOTA TIMBANG2') {

        $sql = "DELETE FROM general_ledger WHERE account_id IN (8,52,147) AND transaction_id = {$transactionId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


        $sql = "SELECT t.*, con.contract_type, fc.freight_id, a.`account_no` AS sales_account, sl.`price_converted` AS sales_price_converted
,sh.`shipment_code`, cust.ppn AS ppn_buyer, sl.price, sl.exchange_rate, f.freight_rule, vh.vendor_handling_rule
					FROM `transaction` t 
					LEFT JOIN stockpile_contract sc
						ON sc.stockpile_contract_id = t.stockpile_contract_id
					LEFT JOIN contract con
						ON con.contract_id = sc.contract_id
					LEFT JOIN freight_cost fc
						ON t.`freight_cost_id` = fc.`freight_cost_id`
					LEFT JOIN shipment sh
						ON t.`shipment_id` = sh.`shipment_id`
					LEFT JOIN sales sl
						ON sh.`sales_id` = sl.`sales_id`
					LEFT JOIN customer cust
						ON sl.`customer_id` = cust.`customer_id`
					LEFT JOIN account a
						ON sl.`account_id` = a.`account_id`
					LEFT JOIN freight f
						ON fc.freight_id = f.freight_id
					LEFT JOIN vendor_handling_cost vhc
						ON vhc.handling_cost_id = t.handling_cost_id
					LEFT JOIN vendor_handling vh
						ON vh.vendor_handling_id = vhc.vendor_handling_id
					WHERE t.transaction_id = {$transactionId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            if ($row->contract_type == 'P') {
                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (140000, 520100, 130100)";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }
                        if ($rowAccount->account_no == 140000) {
                            if ($row->netto_weight < $row->send_weight) {
                                $amount = $row->quantity * $row->unit_price;
                            } else {
                                $amount = $row->send_weight * $row->unit_price;
                            }
                            if ($row->send_weight < 0) {
                                $amount1 = $amount * -1;
                                $insertValues .= "(2, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                            } else {
                                $insertValues .= "(1, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }
                        } elseif ($rowAccount->account_no == 520100) {
                            if ($row->netto_weight < $row->send_weight) {
                                $amount = $row->shrink * $row->unit_price;
                            } else {
                                $amount = 0;
                            }
                            if ($row->send_weight < 0) {
                                $amount1 = $amount * -1;
                                $insertValues .= "(2, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                            } else {
                                $insertValues .= "(1, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }
                        } elseif ($rowAccount->account_no == 130100) {
                            $amount = $row->send_weight * $row->unit_price;
                            if ($row->send_weight < 0) {
                                $amount1 = $amount * -1;
                                $insertValues .= "(1, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                            } else {
                                $insertValues .= "(2, 'NOTA TIMBANG', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }
                        }
                    }
                } else {
                    $boolContinue = false;
                }
            }

        } else {
            $boolContinue = false;
        }

    } elseif ($transactionId != 'NULL') {
        // <editor-fold defaultstate="collapsed" desc="transaction">
        /*if($transactionId != 257943){
		$sqlCek = "SELECT transaction_type FROM `transaction` WHERE transaction_id = {$transactionId}";
		$resultCek = $myDatabase->query($sqlCek, MYSQLI_STORE_RESULT);
        if($resultCek !== false && $resultCek->num_rows == 1) {
            $rowCek = $resultCek->fetch_object();

		if($rowCek->transaction_type != 2){*/
        $sql = "DELETE FROM general_ledger WHERE transaction_id = {$transactionId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        $aFreight = '';
        $sql = "SELECT t.*, con.contract_type, fc.freight_id, a.`account_no` AS sales_account, sl.`price_converted` AS sales_price_converted
,sh.`shipment_code`, cust.ppn AS ppn_buyer, sl.price, sl.exchange_rate, f.freight_rule, vh.vendor_handling_rule,sl.`sales_status`,
af.`account_no` AS aFreight,con.`return_shipment`, con.`contract_id`,sl.`sales_type`
					FROM `transaction` t 
					LEFT JOIN stockpile_contract sc
						ON sc.stockpile_contract_id = t.stockpile_contract_id
					LEFT JOIN contract con
						ON con.contract_id = sc.contract_id
					LEFT JOIN freight_cost fc
						ON t.`freight_cost_id` = fc.`freight_cost_id`
					LEFT JOIN shipment sh
						ON t.`shipment_id` = sh.`shipment_id`
					LEFT JOIN sales sl
						ON sh.`sales_id` = sl.`sales_id`
					LEFT JOIN customer cust
						ON sl.`customer_id` = cust.`customer_id`
					LEFT JOIN account a
						ON sl.`account_id` = a.`account_id`
					LEFT JOIN freight f
						ON fc.freight_id = f.freight_id
					LEFT JOIN vendor_handling_cost vhc
						ON vhc.handling_cost_id = t.handling_cost_id
					LEFT JOIN vendor_handling vh
						ON vh.vendor_handling_id = vhc.vendor_handling_id
					LEFT JOIN tax ftx ON ftx.`tax_id` = t.`fc_tax_id`
					LEFT JOIN account af ON af.`account_id` = ftx.`account_id`
					WHERE t.transaction_id = {$transactionId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            if ($row->adjustmentAudit_id != '' && $row->adjustmentAudit_id != 'NULL') {
                //$totalCogsPrice = 0;
                $totalCogsHandling = 0;
                $totalCogsFC = 0;
                $totalCogsUC = 0;
                $totalCogsStock = 0;
                $sqlCogs1 = "SELECT SUM(a.cogs_pks) AS cogs_pks, SUM(a.cogs_oa) AS cogs_oa, SUM(a.cogs_ob) AS cogs_ob, SUM(a.cogs_handling) AS cogs_handling, SUM(a.total_amount) AS total_amount FROM adjustment_audit_qty a WHERE a.`audit_id` =  {$row->adjustmentAudit_id}";
                $resultCogs1 = $myDatabase->query($sqlCogs1, MYSQLI_STORE_RESULT);

                if ($resultCogs1 !== false && $resultCogs1->num_rows > 0) {
                    while ($rowCogs1 = $resultCogs1->fetch_object()) {

                        $totalCogsHandling = $rowCogs1->cogs_handling;
                        $totalCogsFC = $rowCogs1->cogs_oa;
                        $totalCogsUC = $rowCogs1->cogs_ob;
                        $totalCogsStock = $rowCogs1->cogs_pks;
                        $totalCogs = $rowCogs1->total_amount;

                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (510101, 510102, 510103, 510104, 140000)";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == 510101) {
                                    if ($row->send_weight < 0) {
                                        //$total_pks21 = $total_pks2 * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsStock})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsStock})";
                                    }
                                } /*elseif($rowAccount->account_no == 510201) {
							if($qty < 0){
								//$shrink1 = $shrink * -1;
                            $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$shrink})";
							}else{
							$insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$shrink})";
							}
                        } */ elseif ($rowAccount->account_no == 510102) {
                                    if ($row->send_weight < 0) {
                                        //$total_fc1 = $total_fc * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsFC})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsFC})";
                                    }
                                } elseif ($rowAccount->account_no == 510103) {
                                    if ($row->send_weight < 0) {
                                        //$total_uc1 = $total_uc * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsUC})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsUC})";
                                    }
                                } elseif ($rowAccount->account_no == 510104) {
                                    if ($row->send_weight < 0) {
                                        //$total_hc1 = $total_hc * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsHandling})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogsHandling})";
                                    }
                                } elseif ($rowAccount->account_no == 140000) {
                                    if ($row->send_weight < 0) {
                                        //$total_cogs1 = $total_cogs * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogs})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalCogs})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }

                    }
                } else {
                    echo 'mm';
                }

            } elseif ($row->stock_transit_id != 0 && $row->stock_transit_id != 'NULL') {


                $amt_stock = $row->unit_cost * $row->netto_weight;
                $amt_stock_transit = $row->unit_cost * $row->netto_weight;


                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (141000, 140000)";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        if ($rowAccount->account_no == 140000) {

                            $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amt_stock})";

                        } elseif ($rowAccount->account_no == 141000) {

                            $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amt_stock_transit})";

                        }
                    }
                } else {
                    $boolContinue = false;
                }


            } else {
                if ($row->transaction_type == 1) {
                    $aFreight = $row->aFreight;
                    // <editor-fold defaultstate="collapsed" desc="nota timbang">

                    if ($row->contract_type == 'P') {
                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (140000, 520100, 130100)";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                /*if($rowAccount->account_no == 140000) {
                                if($row->netto_weight < $row->send_weight) {
                                    $amount = $row->quantity * $row->unit_price;
                                } else {
                                    $amount = $row->send_weight * $row->unit_price;
                                }
                                $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            } elseif($rowAccount->account_no == 520100) {
                                if($row->netto_weight < $row->send_weight) {
                                    $amount = $row->shrink * $row->unit_price;
                                } else {
                                    $amount = 0;
                                }
                                $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            } elseif($rowAccount->account_no == 130100) {
                                $amount = $row->send_weight * $row->unit_price;
                                $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }*/
                                if ($rowAccount->account_no == 140000) {
                                    if ($row->netto_weight < $row->send_weight) {
                                        $amount = $row->quantity * $row->unit_price;
                                    } else {
                                        $amount = $row->send_weight * $row->unit_price;
                                    }
                                    if ($row->send_weight < 0) {
                                        $amount1 = ($row->quantity * $row->unit_price) * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                    }
                                } elseif ($rowAccount->account_no == 520100) {
                                    if ($row->netto_weight < $row->send_weight) {
                                        $amount = $row->shrink * $row->unit_price;
                                    } else {
                                        $amount = 0;
                                    }
                                    if ($row->send_weight < 0) {
                                        $amount1 = ($row->shrink * $row->unit_price) * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                    }
                                } elseif ($rowAccount->account_no == 130100) {
                                    $amount = $row->send_weight * $row->unit_price;
                                    if ($row->send_weight < 0) {
                                        $amount1 = $amount * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                            echo 'b';
                        }
                    } elseif ($row->contract_type == 'C') {

                        if ($row->return_shipment == 0) {

                            $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (140000, 210101)";
                            $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                            if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                                $amount = $row->quantity * $row->unit_price;

                                while ($rowAccount = $resultAccount->fetch_object()) {
                                    if ($insertValues != "") {
                                        $insertValues .= ", ";
                                    }

                                    /*if($rowAccount->account_no == 140000) {
                                $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            } elseif($rowAccount->account_no == 210101) {
                                $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                            }*/
                                    if ($rowAccount->account_no == 140000) {
                                        if ($row->send_weight < 0) {
                                            $amount1 = $amount * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                        }
                                    } elseif ($rowAccount->account_no == 210101) {
                                        if ($row->send_weight < 0) {
                                            $amount1 = $amount * -1;
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                        } else {
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                        }
                                    }
                                }
                            } else {
                                $boolContinue = false;
                                echo 'c';
                            }
                        } else {

                            $sqlA = "SELECT sl.*, a.account_no AS shipmentAccount, cust.`ppn` AS ppn_buyer  FROM account a
							LEFT JOIN sales sl ON a.`account_id` = sl.`account_id`
							LEFT JOIN shipment sh ON sh.`sales_id` = sl.`sales_id`
							LEFT JOIN contract c ON c.`return_shipment_id` = sh.`shipment_id`
							LEFT JOIN customer cust ON cust.`customer_id` = sl.`customer_id`
							WHERE c.`contract_id` = {$row->contract_id}
							LIMIT 1";
                            $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT);
                            if ($resultA !== false && $resultA->num_rows > 0) {
                                $rowA = $resultA->fetch_object();
                                $shipmentAccount = $rowA->shipmentAccount;
                                $newSalesPriceA = round($rowA->price * $row->send_weight, 2);
                                $newSalesPriceB = round($newSalesPriceA * $rowA->exchange_rate, 0);
                                //$salesPrice = $row->quantity * $row->sales_price_converted;
                                $taxValueA = ($rowA->ppn_buyer / 100 * $newSalesPriceB);
                                $totalSalesPriceA = $newSalesPriceB + $taxValueA;
                            }

                            $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (0,1) AND account_no in (120000, 230100, {$shipmentAccount}) GROUP BY account_no";
                            $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);
                            if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                                while ($rowAccount = $resultAccount->fetch_object()) {

                                    if ($insertValues != "") {
                                        $insertValues .= ", ";
                                    }

                                    if ($rowAccount->account_no == 120000) {
                                        if ($row->quantity < 0) {
                                            $totalSalesPrice11 = $totalSalesPriceA * -1;
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalSalesPrice1})";
                                        } else {
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalSalesPriceA})";
                                        }
                                    } elseif ($rowAccount->account_no == 230100) {
                                        if ($row->quantity < 0) {
                                            $taxValue11 = $taxValueA * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$taxValue11})";
                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$taxValueA})";
                                        }
                                    } elseif ($rowAccount->account_no == $shipmentAccount) {
                                        if ($row->quantity < 0) {
                                            $newSalesPriceB1 = $newSalesPriceB * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$newSalesPriceB1})";
                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$newSalesPriceB})";
                                        }
                                    }
                                }
                            } else {
                                $boolContinue = false;
                            }

                            $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1) AND account_no in (510100, 140000)";
                            $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);
                            $amount = $row->quantity * $row->unit_price;
                            if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                                while ($rowAccount = $resultAccount->fetch_object()) {
                                    if ($insertValues != "") {
                                        $insertValues .= ", ";
                                    }

                                    if ($rowAccount->account_no == 140000) {
                                        if ($row->send_weight < 0) {
                                            $amount1 = $amount * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                        }
                                    } elseif ($rowAccount->account_no == 510100) {
                                        if ($row->send_weight < 0) {
                                            $amount1 = $amount * -1;
                                            $insertValues .= "(1, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                        } else {
                                            $insertValues .= "(2, '{$source}', NULL, 1, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                        }
                                    }
                                }
                            } else {
                                $boolContinue = false;
                            }


                        }
                    } else {
                        $boolContinue = false;
                        echo 'd';
                    }

                    // </editor-fold>

                    // <editor-fold defaultstate="collapsed" desc="fc">
                    echo $aFreight;
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 2 AND account_no in (140000, '{$aFreight}', 210103)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);
                    //$taxValue = 0;
                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        if ($row->fc_tax_id != '' && $row->fc_tax_id != 0 && $row->freight_price != '' && $row->freight_price > 0) {
                            $sqlTax = "SELECT * FROM tax WHERE tax_id = {$row->fc_tax_id}";
                            $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);

                            if ($resultTax !== false && $resultTax->num_rows > 0) {
                                $rowTax = $resultTax->fetch_object();
                                $taxValue = $rowTax->tax_value;

                                if ($row->freight_rule == 1) {
                                    $totalFreight_Price = $row->freight_price * $row->send_weight;

                                } else {
                                    $totalFreight_Price = $row->freight_price * $row->freight_quantity;

                                }
                                if ($rowTax->tax_category == 1) {
                                    $totalFreightPrice = ($totalFreight_Price) / ((100 - $taxValue) / 100);
                                    $creditFreightPrice = $totalFreight_Price;
                                } else {
                                    $totalFreightPrice = $totalFreight_Price;
                                    $creditFreightPrice = $totalFreight_Price - ($totalFreight_Price) * ($taxValue / 100);
                                }
                            } else {
                                $totalFreightPrice = $row->freight_price * $row->freight_quantity;
                                $creditFreightPrice = ($row->freight_price * $row->freight_quantity) - (($row->freight_price * $row->freight_quantity) * ($taxValue / 100));
                                $taxValue = 0;
                            }
                        } else if ($row->freight_price != '' && $row->freight_price > 0) {
                            if ($row->freight_rule == 1) {
                                $totalFreightPrice = $row->freight_price * $row->send_weight;
                                $creditFreightPrice = $totalFreightPrice;
                            } else {
                                $totalFreightPrice = $row->freight_price * $row->freight_quantity;
                                $creditFreightPrice = $totalFreightPrice;
                            }
                            $taxValue = 0;
                        } else {
                            $totalFreightPrice = 0;
                            $creditFreightPrice = 0;
                            $taxValue = 0;
                        }

                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 140000) {
                                if ($row->send_weight < 0) {
                                    $totalFreightPrice1 = $totalFreightPrice * -1;
                                    $insertValues .= "(2, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalFreightPrice1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalFreightPrice})";
                                }
                            } elseif ($rowAccount->account_no == $aFreight) {
                                $amount = $totalFreightPrice * ($taxValue / 100);
                                if ($row->send_weight < 0) {
                                    $amount1 = $amount * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                }
                            } elseif ($rowAccount->account_no == 210103) {
                                if ($row->send_weight < 0) {
                                    $creditFreightPrice1 = $creditFreightPrice * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$creditFreightPrice1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$creditFreightPrice})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                        echo 'e';
                    }

                    // <editor-fold defaultstate="collapsed" desc="fc shrink">
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 2 AND account_no in (140000, '{$aFreight}', 210103)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        //if($row->fc_tax_id != '') {
                        $sqlTax = "SELECT a.*, c.`tax_category`, c.`tax_value`,
COALESCE(ROUND(CASE WHEN c.tax_category = 1 THEN a.amt_claim / ((100 - COALESCE(c.tax_value,0))/100) ELSE a.amt_claim END,10),0) AS freight_susut_credit,
COALESCE(ROUND(CASE WHEN c.tax_category = 1 THEN a.amt_claim  ELSE a.amt_claim - (a.amt_claim * (COALESCE(c.tax_value,0)/100)) END,10),0) AS freight_susut_debit,
COALESCE(ROUND((CASE WHEN c.tax_category = 1 THEN a.amt_claim / ((100 - COALESCE(c.tax_value,0))/100) ELSE a.amt_claim END) - 
(CASE WHEN c.tax_category = 1 THEN a.amt_claim  ELSE a.amt_claim - (a.amt_claim * (COALESCE(c.tax_value,0)/100)) END),10),0) AS tax_susut_debit,
b.`freight_cost_id`,
b.`fc_tax_id`
FROM transaction_shrink_weight a
LEFT JOIN `transaction` b ON a.`transaction_id` = b.`transaction_id`
LEFT JOIN tax c ON c.`tax_id` = b.`fc_tax_id`  
WHERE a.`transaction_id` = {$transactionId}";
                        $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);

                        if ($resultTax !== false && $resultTax->num_rows > 0) {
                            $rowTax = $resultTax->fetch_object();

                            if ($rowTax->freight_cost_id != '') {
                                $totalFreightShrink = $rowTax->freight_susut_debit;
                                $creditFreightShrink = $rowTax->freight_susut_credit;
                                $taxShrink = $rowTax->tax_susut_debit;
                            } else {
                                $totalFreightShrink = 0;
                                $creditFreightShrink = 0;
                                $taxShrink = 0;
                            }
                        } else {
                            $totalFreightShrink = 0;
                            $creditFreightShrink = 0;
                            $taxShrink = 0;
                        }

                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 140000) {
                                if ($row->send_weight < 0) {
                                    $creditFreightShrink1 = $creditFreightShrink * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$creditFreightShrink1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$creditFreightShrink})";
                                }
                            } elseif ($rowAccount->account_no == $aFreight) {
                                //$amount = $totalFreightPrice * ($taxValue / 100);
                                if ($row->send_weight < 0) {
                                    $taxShrink1 = $taxShrink * -1;
                                    $insertValues .= "(2, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$taxShrink1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$taxShrink})";
                                }
                            } elseif ($rowAccount->account_no == 210103) {
                                if ($row->send_weight < 0) {
                                    $totalFreightShrink1 = $totalFreightShrink * -1;
                                    $insertValues .= "(2, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalFreightShrink1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 14, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalFreightShrink})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                        echo 'f';
                    }
                    // </editor-fold>
                    // <editor-fold defaultstate="collapsed" desc="hc">
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 9 AND account_no in (140000, 230204, 210106)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        if ($row->handling_cost_id != '') {
                            $sqlTax = "SELECT tx.* FROM tax tx
LEFT JOIN vendor_handling vh ON vh.`pph_tax_id` = tx.`tax_id`
LEFT JOIN vendor_handling_cost vhc ON vhc.`vendor_handling_id` = vh.`vendor_handling_id`
WHERE vhc.`handling_cost_id` = {$row->handling_cost_id}";
                            $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);

                            if ($resultTax !== false && $resultTax->num_rows > 0) {
                                $rowTax = $resultTax->fetch_object();
                                $taxValue = $rowTax->tax_value;

                                if ($row->vendor_handling_rule == 1) {
                                    $totalHandling_Price = $row->handling_price * $row->send_weight;

                                } else {
                                    $totalHandling_Price = $row->handling_price * $row->handling_quantity;

                                }
                                if ($rowTax->tax_category == 1) {
                                    $totalHandlingPrice = ($totalHandling_Price) / ((100 - $taxValue) / 100);
                                    $creditHandlingPrice = $totalHandling_Price;
                                } else {
                                    $totalHandlingPrice = $totalHandling_Price;
                                    $creditHandlingPrice = $totalHandling_Price - ($totalHandling_Price) * ($taxValue / 100);
                                }
                            } else {
                                $totalHandlingPrice = $row->handling_price * $row->handling_quantity;
                                $creditHandlingPrice = ($row->handling_price * $row->handling_quantity) - (($row->handling_price * $row->handling_quantity) * ($taxValue / 100));
                                $taxValue = 0;
                            }
                        } else {
                            $totalHandlingPrice = 0;
                            $creditHandlingPrice = 0;
                            $taxValue = 0;
                        }

                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 140000) {
                                if ($row->send_weight < 0) {
                                    $totalHandlingPrice1 = $totalHandlingPrice * -1;
                                    $insertValues .= "(2, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalHandlingPrice1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalHandlingPrice})";
                                }
                            } elseif ($rowAccount->account_no == 230204) {
                                $amount = $totalHandlingPrice * ($taxValue / 100);
                                if ($row->send_weight < 0) {
                                    $amount1 = $amount * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount})";
                                }
                            } elseif ($rowAccount->account_no == 210106) {
                                if ($row->send_weight < 0) {
                                    $creditHandlingPrice1 = $creditHandlingPrice * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$creditHandlingPrice1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$creditHandlingPrice})";
                                }
                            }
                        }
                    } else {
                        echo 'test2';
                        $boolContinue = false;
                    }

                    // </editor-fold>

                    // <editor-fold defaultstate="collapsed" desc="uc">
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 3 AND account_no in (140000, 230203, 210104)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        if ($row->uc_tax_id != '') {
                            $sqlTax = "SELECT * FROM tax WHERE tax_id = {$row->uc_tax_id}";
                            $resultTax = $myDatabase->query($sqlTax, MYSQLI_STORE_RESULT);

                            if ($resultTax !== false && $resultTax->num_rows > 0) {
                                $rowTax = $resultTax->fetch_object();
                                $taxValue = 0;

                                if ($rowTax->tax_category == 1) {
                                    $totalUnloadingPrice = $row->unloading_price / ((100 - $taxValue) / 100);
                                    $creditUnloadingPrice = $row->unloading_price;
                                } else {
                                    $totalUnloadingPrice = $row->unloading_price;
                                    $creditUnloadingPrice = $row->unloading_price - $row->unloading_price * ($taxValue / 100);
                                }
                            } else {
                                $totalUnloadingPrice = $row->unloading_price;
                                $creditUnloadingPrice = $row->unloading_price;
                                $taxValue = 0;
                            }
                        } else {
                            $totalUnloadingPrice = 0;
                            $creditUnloadingPrice = 0;
                            $taxValue = 0;
                        }

                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 140000) {
                                //echo 'betul betul';
                                if ($row->send_weight < 0) {
                                    $totalUnloadingPrice1 = $totalUnloadingPrice * -1;
                                    $insertValues .= "(2, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalUnloadingPrice1})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalUnloadingPrice})";
                                }
                            } elseif ($rowAccount->account_no == 230203) {
                                $amount = $totalUnloadingPrice * ($taxValue / 100);
                                if ($row->send_weight < 0) {
                                    $amount1 = $amount * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amount1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amount})";
                                }
                            } elseif ($rowAccount->account_no == 210104) {
                                if ($row->send_weight < 0) {
                                    $creditUnloadingPrice1 = $creditUnloadingPrice * -1;
                                    $insertValues .= "(1, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$creditUnloadingPrice1})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$creditUnloadingPrice})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                        echo 'g';
                    }

                    // </editor-fold>

                } elseif ($row->transaction_type == 2) {

                    if ($row->sales_status == 3) {
                        $shipment_code = $row->shipment_code;
                        $shipment_code1 = '-S-';
                        $pos1 = strpos($shipment_code, $shipment_code1);
                        // <editor-fold defaultstate="collapsed" desc="nota timbang">
                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (0,1) AND account_no in (120000, 230100, {$row->sales_account}) GROUP BY account_no";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            if ($pos1 !== FALSE) {

                                $newSalesPrice2 = 0;
                                $taxValue = 0;
                                $totalSalesPrice = 0;

                            } else {
                                $newSalesPrice1 = round($row->price * $row->quantity, 2);
                                $newSalesPrice2 = round($newSalesPrice1 * $row->exchange_rate, 0);
                                //$salesPrice = $row->quantity * $row->sales_price_converted;
                                $taxValue = ($row->ppn_buyer / 100 * $newSalesPrice2);
                                $totalSalesPrice = $newSalesPrice2 + $taxValue;
                            }
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == 120000) {
                                    if ($row->quantity < 0) {
                                        $totalSalesPrice1 = $totalSalesPrice * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalSalesPrice1})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalSalesPrice})";
                                    }
                                } elseif ($rowAccount->account_no == 230100) {
                                    if ($row->quantity < 0) {
                                        $taxValue1 = $taxValue * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$taxValue1})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$taxValue})";
                                    }
                                } elseif ($rowAccount->account_no == $row->sales_account) {
                                    if ($row->quantity < 0) {
                                        $newSalesPrice21 = $newSalesPrice2 * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$newSalesPrice21})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$newSalesPrice2})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }
                    }
                    // </editor-fold>

                    // <editor-fold defaultstate="collapsed" desc="cogs">

                    $totalCogsPrice = 0;
                    $totalCogsShrink = 0;
                    $totalCogsFC = 0;
                    $totalCogsUC = 0;
                    $totalCogsStock = 0;
                    $sqlCogs = "SELECT d.*, c.price_converted, t.shrink, t.freight_quantity, t.freight_price, t.unloading_price, t.handling_quantity, vhc.price_converted,
			f.ppn_tax_id AS fc_ppn_tax_id, f.ppn AS fc_ppn, fctxppn.tax_category AS fc_ppn_tax_category, t2.shrink AS susut,
            f.pph_tax_id AS fc_pph_tax_id, f.pph AS fc_pph, fctxpph.tax_category AS fc_pph_tax_category,
            l.ppn_tax_id AS uc_ppn_tax_id, l.ppn AS uc_ppn, uctxppn.tax_category AS uc_ppn_tax_category,
            l.pph_tax_id AS uc_pph_tax_id, l.pph AS uc_pph, uctxpph.tax_category AS uc_pph_tax_category,
			vh.ppn_tax_id AS vh_ppn_tax_id, vh.ppn AS vh_ppn, vhtxppn.tax_category AS vh_ppn_tax_category,
            vh.pph_tax_id AS vh_pph_tax_id, vh.pph AS vh_pph, vhtxpph.tax_category AS vh_pph_tax_category,
			d.quantity * c.price_converted AS cogs_amount,
			CASE WHEN t.delivery_status = 2 THEN (d.percent_taken / 100) * (t.quantity * t.freight_price)
			ELSE (d.percent_taken / 100) * (t.freight_quantity * t.freight_price) END AS freight_total,
			CASE WHEN t.delivery_status = 2 AND t.freight_cost_id IS NOT NULL THEN (d.quantity/t.freight_quantity) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0)
	    WHEN t.freight_cost_id IS NOT NULL THEN (d.percent_taken / 100) * COALESCE((SELECT amt_claim FROM transaction_shrink_weight WHERE transaction_id = d.transaction_id),0) ELSE 0 END AS freight_shrink,
			(d.percent_taken / 100) * t.unloading_price AS unloading_total, sh.shipment_code, (d.percent_taken / 100) * (t.handling_quantity * vhc.price_converted) AS handling_total, t2.quantity AS bl
            FROM `transaction` t2
                LEFT JOIN `delivery` d 
                    ON d.shipment_id = t2.shipment_id
				LEFT JOIN shipment sh
					ON d.shipment_id = sh.shipment_id
                LEFT JOIN `transaction` t
                    ON t.transaction_id = d.transaction_id
                LEFT JOIN stockpile_contract sc
					ON sc.`stockpile_contract_id` = t.`stockpile_contract_id`
			    LEFT JOIN contract c
					ON c.`contract_id` = sc.`contract_id`
			    LEFT JOIN freight_cost fc
					ON fc.`freight_cost_id` = t.`freight_cost_id`
			    LEFT JOIN freight f
					ON fc.`freight_id` = f.`freight_id`
			    LEFT JOIN labor l
					ON l.`labor_id` = t.`labor_id`
			    LEFT JOIN tax fctxpph
					ON fctxpph.tax_id = f.pph_tax_id
			    LEFT JOIN tax fctxppn
					ON fctxppn.tax_id = f.ppn_tax_id
			    LEFT JOIN tax uctxpph
			        ON uctxpph.tax_id = l.pph_tax_id
			    LEFT JOIN tax uctxppn
					ON uctxppn.tax_id = l.ppn_tax_id
				LEFT JOIN vendor_handling_cost vhc
					ON vhc.handling_cost_id = t.handling_cost_id
				LEFT JOIN vendor_handling vh
					ON vh.vendor_handling_id = vhc.vendor_handling_id
				LEFT JOIN tax vhtxpph
					ON vhtxpph.tax_id = vh.pph_tax_id
				LEFT JOIN tax vhtxppn
					ON vhtxppn.tax_id = vh.ppn_tax_id
				
                WHERE t2.transaction_id =  {$transactionId}";
                    $resultCogs = $myDatabase->query($sqlCogs, MYSQLI_STORE_RESULT);

                    if ($resultCogs !== false && $resultCogs->num_rows > 0) {
                        while ($rowCogs = $resultCogs->fetch_object()) {

                            $shipmentCode11 = $rowCogs->shipment_code;
                            $shipmentCode12 = '-S-';
                            $pos12 = strpos($shipmentCode11, $shipmentCode12);

                            if ($rowCogs->fc_pph_tax_category == 1 && $rowCogs->fc_pph_tax_id != '') {
                                $pphfc2 = ($rowCogs->freight_total / ((100 - $rowCogs->fc_pph) / 100)) - $rowCogs->freight_total;
                                $pphfcShrink2 = ($rowCogs->freight_shrink / ((100 - $rowCogs->fc_pph) / 100)) - $rowCogs->freight_shrink;

                            } elseif ($rowCogs->fc_pph_tax_category == 0 && $rowCogs->fc_pph_tax_id != '') {
                                $pphfc2 = 0;
                                $pphfcShrink2 = 0;
                                //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
                            } else {
                                $pphfc2 = 0;
                                $pphfcShrink2 = 0;
                            }

                            $freightTotal2 = ($rowCogs->freight_total + $ppnfc2 + $pphfc2) - ($rowCogs->freight_shrink + $pphfcShrink2);

                            if ($rowCogs->vh_pph_tax_category == 1 && $rowCogs->vh_pph_tax_id != '') {
                                $pphvh2 = ($rowCogs->handling_total / ((100 - $rowCogs->vh_pph) / 100)) - $rowCogs->handling_total;

                            } elseif ($rowCogs->vh_pph_tax_category == 0 && $rowCogs->vh_pph_tax_id != '') {
                                $pphvh2 = 0;
                                //$pphfc =  $row->freight_total - ($row->freight_total * ((100 - $fc_pph) / 100));
                            } else {
                                $pphvh2 = 0;
                            }

                            $handlingTotal2 = $rowCogs->handling_total + $pphvh2;

                            if ($rowCogs->uc_pph_tax_category == 1 && $rowCogs->uc_pph_tax_id != '') {
                                $pphuc2 = ($rowCogs->unloading_total / ((100 - $rowCogs->uc_pph) / 100)) - $rowCogs->unloading_total;

                            } elseif ($rowCogs->uc_pph_tax_category == 0 && $rowCogs->uc_pph_tax_id != '') {
                                $pphuc2 = 0;
                                //$pphuc =  $row->unloading_total - ($row->unloading_total * ((100 - $row->uc_pph) / 100));
                            } else {
                                $pphuc2 = 0;
                            }

                            $unloadingTotal2 = $rowCogs->unloading_total + $ppnuc2 + $pphuc2;

                            $totalCogs2 = $rowCogs->cogs_amount + $freightTotal2 + $unloadingTotal2 + $handlingTotal2;

                            $quantity_total = $rowCogs->quantity;
                            $total_quantity = $quantity_total + $total_quantity;

                            $pks_total = $rowCogs->cogs_amount;
                            $total_pks = $pks_total + $total_pks;

                            $fc_total = $freightTotal2;
                            $total_fc = $fc_total + $total_fc;

                            $uc_total = $unloadingTotal2;
                            $total_uc = $uc_total + $total_uc;

                            $hc_total = $handlingTotal2;
                            $total_hc = $hc_total + $total_hc;

                            $cogs_total = $totalCogs2;
                            $total_cogs = $cogs_total + $total_cogs;


                            $amtUnit = $total_cogs / $total_quantity;
                            $shrink = $amtUnit * $rowCogs->susut;
                            $total_pks2 = $total_pks - $shrink;

                            $qty = $rowCogs->bl;
                        }
                    }

                    if ($pos12 !== FALSE) {

                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1) AND account_no in (510201, 140000)";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == 510201) {
                                    if ($qty < 0) {
                                        //$total_cogs1 = $total_cogs *-1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    }
                                } elseif ($rowAccount->account_no == 140000) {
                                    if ($qty < 0) {
                                        //$total_cogs1 = $total_cogs * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }
                    } else if ($row->sales_type == 3) {

                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1) AND account_no in (141000, 140000)";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == 141000) {
                                    if ($qty < 0) {
                                        //$total_cogs1 = $total_cogs *-1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    }
                                } elseif ($rowAccount->account_no == 140000) {
                                    if ($qty < 0) {
                                        //$total_cogs1 = $total_cogs * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }
                    } else {

                        $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 1 AND account_no in (510101, 510201, 510102, 510103, 510104, 140000)";
                        $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                        if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                            while ($rowAccount = $resultAccount->fetch_object()) {
                                if ($insertValues != "") {
                                    $insertValues .= ", ";
                                }

                                if ($rowAccount->account_no == 510101) {
                                    if ($qty < 0) {
                                        //$total_pks21 = $total_pks2 * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_pks2})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_pks2})";
                                    }
                                } elseif ($rowAccount->account_no == 510201) {
                                    if ($qty < 0) {
                                        //$shrink1 = $shrink * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$shrink})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$shrink})";
                                    }
                                } elseif ($rowAccount->account_no == 510102) {
                                    if ($qty < 0) {
                                        //$total_fc1 = $total_fc * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_fc})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_fc})";
                                    }
                                } elseif ($rowAccount->account_no == 510103) {
                                    if ($qty < 0) {
                                        //$total_uc1 = $total_uc * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_uc})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_uc})";
                                    }
                                } elseif ($rowAccount->account_no == 510104) {
                                    if ($qty < 0) {
                                        //$total_hc1 = $total_hc * -1;
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_hc})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_hc})";
                                    }
                                } elseif ($rowAccount->account_no == 140000) {
                                    if ($qty < 0) {
                                        //$total_cogs1 = $total_cogs * -1;
                                        $insertValues .= "(1, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$total_cogs})";
                                    }
                                }
                            }
                        } else {
                            $boolContinue = false;
                        }

                    }  // </editor-fold>
                }
            }

        } else {
            $boolContinue = false;
        }

        // </editor-fold>
    } elseif ($paymentId != 'NULL') {
        // <editor-fold defaultstate="collapsed" desc="payment">
        if ($source == 'PAYMENT' || $source == 'PETTY CASH' || $source == 'PAYMENT ADMIN') {
            $sql = "DELETE FROM general_ledger WHERE payment_id = {$paymentId}";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        } elseif ($source == 'RETURN PAYMENT') {
            $sql = "DELETE FROM general_ledger WHERE payment_id = {$paymentId} AND general_ledger_module = 'RETURN PAYMENT'";
            $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
            //echo 'benar';
        }
        $sql = "SELECT p.*, a.account_no, ap.account_type AS ap_account_type, ap.account_no AS ap_account_no,
b.currency_id AS b_currency_id, ta.account_no AS gv_account_no, gv.pph_tax_id AS gv_pph_tax_id, ta.account_no AS at_account_no,
f.pph_tax_id AS f_pph_tax_id, f.pph AS f_pph_tax, f.ppn AS f_ppn_tax, atf.account_no AS f_account_no, u.pph_tax_id AS u_pph_tax_id, atu.account_no AS u_account_no, tfc.tax_category AS fc_tax_category,
vh.pph_tax_id AS vh_pph_tax_id, vh.pph AS vh_pph_tax, atvh.account_no AS vh_account_no, tvhc.tax_category AS vhc_tax_category
                FROM payment p 
                INNER JOIN bank b
                    ON b.bank_id = p.bank_id
                INNER JOIN account a
                    ON a.account_id = b.account_id
                INNER JOIN account ap
                    ON ap.account_id = p.account_id 
                LEFT JOIN general_vendor gv
                    ON gv.general_vendor_id = p.general_vendor_id
                LEFT JOIN tax t
                    ON t.tax_id = gv.pph_tax_id
                LEFT JOIN account ta
                    ON ta.account_id = t.account_id	
                LEFT JOIN freight f
                    ON f.freight_id = p.freight_id
                LEFT JOIN tax tfc
                    ON tfc.tax_id = f.pph_tax_id
                LEFT JOIN account atf
                    ON atf.account_id = tfc.account_id
                LEFT JOIN labor u
                    ON u.labor_id = p.labor_id
                LEFT JOIN tax tu
                    ON tu.tax_id = u.pph_tax_id
                LEFT JOIN account atu
                    ON atu.account_id = tu.account_id
                LEFT JOIN vendor_handling vh
                    ON vh.vendor_handling_id = p.vendor_handling_id
                LEFT JOIN tax tvhc
                    ON tvhc.tax_id = vh.pph_tax_id
                LEFT JOIN account atvh
                    ON atvh.account_id = tvhc.account_id
                WHERE p.payment_id = {$paymentId}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            $paymentStatus = $row->payment_status;
            $payment_type = $row->payment_type;

            if ($row->stockpile_contract_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment PKS kontrak">
                if ($row->payment_method == 1) {
                    $totalContractAmount = 0;
                    $totalContractDP = 0;

                    $sqlContract = "SELECT sc.*, con.price_converted, con.quantity, 
                                        (
                                            SELECT COALESCE(SUM(amount_journal), 0) FROM payment
                                            WHERE stockpile_contract_id = sc.stockpile_contract_id
                                            AND payment_method = 2 AND payment_status = 0
                                        ) AS total_dp,
                                        (
                                            SELECT COALESCE(SUM(ppn_journal), 0) FROM payment
                                            WHERE stockpile_contract_id = sc.stockpile_contract_id
                                            AND payment_method = 2 AND payment_status = 0
                                        ) AS total_ppn,
										(
                                            SELECT a.account_no FROM account a LEFT JOIN payment p
											ON p.account_id = a.account_id
                                            WHERE p.stockpile_contract_id = sc.stockpile_contract_id
                                            AND p.payment_method = 2 AND p.payment_status = 0 LIMIT 1
                                        ) AS account_no
                                    FROM stockpile_contract sc
                                    INNER JOIN contract con
                                        ON con.contract_id = sc.contract_id
                                    WHERE sc.stockpile_contract_id = {$row->stockpile_contract_id}";
                    $resultContract = $myDatabase->query($sqlContract, MYSQLI_STORE_RESULT);

                    if ($resultContract !== false && $resultContract->num_rows == 1) {
                        $rowContract = $resultContract->fetch_object();

                        $totalContractAmount = ceil($rowContract->price_converted * $rowContract->quantity);
                        $totalContractPPN = $rowContract->total_ppn;
                        if ($rowContract->account_no == 210102) {
                            $totalContractDP = 0;
                        } else {
                            $totalContractDP = $rowContract->total_dp - $totalContractPPN;
                        }

                    }

                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (0, 7) AND account_no in (210102, 130001, {$row->account_no}, 230204)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 210102) {
                                $amountJournal = $row->amount_journal + $totalContractDP;
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', 'Payment', 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', 'Payment', 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                    }
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal}), (1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal}), (1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal}), (2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal}), (2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    }
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL,  {$totalContractPPN})";
                                    }
                                }
                            } elseif ($rowAccount->account_no == 130001) {
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalContractDP})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalContractDP})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalContractDP})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$totalContractDP})";
                                    }
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                    }
                                }
                            } elseif ($rowAccount->account_no == 230204) {
                                if ($paymentStatus == 1) {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(1, '{$source}', NULL, 1, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                    }
                                } else {
                                    if ($payment_type == 1) {
                                        $insertValues .= "(2, '{$source}', NULL, 1, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (0, 7) AND account_no in ({$row->ap_account_no}, 150410, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->ap_account_no) {
                                $amountJournal = $row->amount_journal - $row->ppn_journal;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 1, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0,{$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->payment_cash_id != '') {
                echo 'blt';
                // <editor-fold defaultstate="collapsed" desc="payment PKS kontrak">
                if ($row->payment_method == 1) {

                    $cashId = 0;
                    $pc_id = 0;
                    $acc_dp_pph = 'NULL';
                    $acc_dp_ppn = 'NULL';
                    $sqlcashDP = "SELECT pcdp.payment_cash_id, pcdp.payment_cash_dp, pcdp.`amount_payment` AS down_payment,
(SELECT gv.ppn FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_cash_id = pcdp.payment_cash_dp) AS gv_ppn,
(SELECT gv.pph FROM general_vendor gv LEFT JOIN payment_cash pc ON gv.general_vendor_id = pc.general_vendor_id WHERE pc.payment_cash_id = pcdp.payment_cash_dp) AS gv_pph,
(SELECT account_id FROM payment_cash WHERE payment_cash_id = pcdp.payment_cash_dp) AS account_id,
(SELECT tx.account_id FROM tax tx LEFT JOIN payment_cash pc ON pc.ppnID = tx.tax_id WHERE pc.payment_cash_id = pcdp.payment_cash_dp) AS acc_dp_ppn,
(SELECT tx.account_id FROM tax tx LEFT JOIN payment_cash pc ON pc.pphID = tx.tax_id WHERE pc.payment_cash_id = pcdp.payment_cash_dp) AS acc_dp_pph,  
(SELECT pph FROM payment_cash WHERE payment_cash_id = pcdp.payment_cash_dp) AS pph,
(SELECT ppn FROM payment_cash WHERE payment_cash_id = pcdp.payment_cash_dp) AS ppn,
(SELECT `type` FROM payment_cash WHERE payment_cash_id = pcdp.payment_cash_dp) AS `type`
FROM payment_cash_dp pcdp 
LEFT JOIN payment_cash pc ON pc.`payment_cash_id` = pcdp.`payment_cash_id` 
WHERE pc.payment_id = {$paymentId}";
                    $resultCashDP = $myDatabase->query($sqlcashDP, MYSQLI_STORE_RESULT);

                    if ($resultCashDP !== false && $resultCashDP->num_rows > 0) {
                        $dp_type = array();
                        while ($rowCashDP = mysqli_fetch_array($resultCashDP)) {

                            $accountDP = $rowCashDP['account_id'];

                            if ($rowCashDP['acc_dp_ppn'] != '') {
                                $acc_dp_ppn = $rowCashDP['acc_dp_ppn'];
                            } else {
                                $acc_dp_ppn = 'NULL';
                            }

                            //$acc_dp_ppn = $rowCashDP['acc_dp_ppn'];
                            if ($rowCashDP['acc_dp_pph'] != '') {
                                $acc_dp_pph = $rowCashDP['acc_dp_pph'];
                            } else {
                                $acc_dp_pph = 'NULL';
                            }
                            $ppn_dp = $rowCashDP['gv_ppn'];
                            $pph_dp = $rowCashDP['gv_pph'];
                            $ppn = $rowCashDP['ppn'];
                            $pph = $rowCashDP['pph'];
                            $cashId = $rowCashDP['payment_cash_dp'];
                            $down_payment = $rowCashDP['down_payment'];
                            $dp_type[] = $rowCashDP['type'];
                            $pc_dp_type = implode(', ', $dp_type);


                            if ($ppn == 0) {
                                $dpPPN = 0;
                            } else {
                                $dpPPN = $down_payment * ($ppn_dp / 100);
                            }
                            if ($pph == 0) {
                                $dpPPh = 0;
                            } else {
                                $dpPPh = $down_payment * ($pph_dp / 100);
                            }
                            //$downPayment = ($down_payment + $dpPPN) - $dpPPh;


                            if ($acc_dp_ppn != '' || $acc_dp_ppn != 0 || $acc_dp_ppn != 'NULL') {
                                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$pc_dp_type}) AND account_no IN (150410)";
                                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                                if ($resultAccount !== false && $resultAccount->num_rows == 1) {
                                    $rowAccount = $resultAccount->fetch_object();
                                    $accountId_dp = $rowAccount->account_id;

                                }
                            }


                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }


                            if ($cashId != '' || $cashId != 0 || $cashId != 'NULL') {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountDP}, NULL, {$down_payment}),";
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$acc_dp_pph}, NULL, {$dpPPh}),";
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId_dp}, NULL,  {$dpPPN})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountDP}, NULL, {$down_payment}),";
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$acc_dp_pph}, NULL, {$dpPPh}),";
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId_dp}, NULL,  {$dpPPN})";
                                }
                            }

                        }
                    }
                    /*}else {
                   // $boolContinue = false;

				echo 'FALSE2';
				//echo '</br>';
				//echo $payment_cash_id;
				//echo '</br>';
				//echo $PCashDP;
				//echo '</br>';
				//echo $pc_id;

                }*/
                }

                if ($row->payment_method == 1) {
                    $totalCashAmount = 0;
                    $totalCashtDP = 0;


                    $sqlPC = "SELECT pc.*, a.account_no, a.account_name, apph.account_id AS apph_id, apph.account_no AS apph_no, apph.account_name AS apph_name
							  FROM payment_cash pc
								LEFT JOIN account a ON a.`account_id` = pc.`account_id` 
								LEFT JOIN tax txpph ON txpph.`tax_id` = pc.`pphID`
								LEFT JOIN account apph ON apph.`account_id` = txpph.`account_id`
								WHERE pc.`payment_id` = {$paymentId}";
                    $resultPC = $myDatabase->query($sqlPC, MYSQLI_STORE_RESULT);


                    if ($resultPC !== false && $resultPC->num_rows > 0) {
                        while ($rowPC = $resultPC->fetch_object()) {

                            $cashId = $rowPC->payment_cash_id;
                            $amountConv = $rowPC->amount_converted;

                            if ($rowPC->apph_id == '' || $rowPC->apph_id == 0 || $rowPC->apph_id == 'NULL') {
                                $apph_id = 'NULL';
                            } else {
                                $apph_id = $rowPC->apph_id;
                            }

                            if ($rowPC->ppnID != '' || $rowPC->ppnID != 0 || $rowPC->ppnID != 'NULL') {
                                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$rowPC->type}) AND account_no IN (150410)";
                                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                                if ($resultAccount !== false && $resultAccount->num_rows == 1) {
                                    $rowAccount = $resultAccount->fetch_object();
                                    $accountId = $rowAccount->account_id;

                                }
                            }
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }
                            if ($row->payment_type == 1) {
                                if ($rowPC->payment_cash_id != '' || $rowPC->payment_cash_id != 0 || $rowPC->payment_cash_id != 'NULL') {
                                    if ($paymentStatus == 1) {
                                        if ($rowPC->amount_converted < 0) {
                                            $amount_converted1 = $rowPC->amount_converted * -1;
                                            $pph_converted1 = $rowPC->pph_converted * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$amount_converted1}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$pph_converted1})";
                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted})";
                                        }
                                    } else {
                                        if ($rowPC->amount_converted < 0) {
                                            $amount_converted1 = $rowPC->amount_converted * -1;
                                            $pph_converted1 = $rowPC->pph_converted * -1;
                                            $insertValues .= "(1, '{$source}', NULL, 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$amount_converted1}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$pph_converted1})";
                                        } else {
                                            $insertValues .= "(2, '{$source}', NULL, 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted})";
                                        }
                                    }
                                }
                            } else {
                                if ($rowPC->payment_cash_id != '' || $rowPC->payment_cash_id != 0 || $rowPC->payment_cash_id != 'NULL') {
                                    if ($paymentStatus == 1) {
                                        if ($rowPC->amount_converted < 0) {
                                            $amount_converted1 = $rowPC->amount_converted * -1;
                                            $pph_converted1 = $rowPC->pph_converted * -1;
                                            $ppn_converted1 = $rowPC->ppn_converted * -1;
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$amount_converted1}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$pph_converted1}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$ppn_converted1})";
                                        } else {
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$rowPC->ppn_converted})";
                                        }
                                    } else {
                                        if ($rowPC->amount_converted < 0) {
                                            $amount_converted1 = $rowPC->amount_converted * -1;
                                            $pph_converted1 = $rowPC->pph_converted * -1;
                                            $ppn_converted1 = $rowPC->ppn_converted * -1;
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$amount_converted1}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$pph_converted1}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$ppn_converted1})";

                                        } else {
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                            $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted}),";
                                            $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$rowPC->ppn_converted})";
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }

                }
                if ($row->payment_method == 1) {


                    $sqlPayment = "SELECT b.account_id, amount_journal FROM payment p LEFT JOIN bank b ON p.bank_id = b.bank_id WHERE p.payment_id = {$paymentId}";
                    $resultPayment = $myDatabase->query($sqlPayment, MYSQLI_STORE_RESULT);

                    if ($resultPayment !== false && $resultPayment->num_rows > 0) {
                        while ($rowPayment = $resultPayment->fetch_object()) {


                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }
                            if ($row->payment_type == 1) {
                                if ($rowPayment->account_id != '' || $rowPayment->account_id != 0 || $rowPayment->account_id != 'NULL') {
                                    if ($paymentStatus == 1) {
                                        $insertValues .= "(2, '{$source}', 'Payment', 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 1, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                    }
                                }
                            } else {

                                if (($rowPayment->account_id != '' || $rowPayment->account_id != 0 || $rowPayment->account_id != 'NULL') && $row->amount_journal < 0) {
                                    $amountJournal = $row->amount_journal * -1;
                                    if ($paymentStatus == 1) {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$amountJournal})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$amountJournal})";
                                    }
                                } elseif ($rowPayment->account_id != '' || $rowPayment->account_id != 0 || $rowPayment->account_id != 'NULL') {
                                    if ($paymentStatus == 1) {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }

                }

                if ($row->payment_method == 2) {
                    $totalCashAmount = 0;
                    $totalCashtDP = 0;


                    $sqlPC = "SELECT pc.*, a.account_no, a.account_name, apph.account_id AS apph_id, apph.account_no AS apph_no, apph.account_name AS apph_name
							  FROM payment_cash pc
								LEFT JOIN account a ON a.`account_id` = pc.`account_id` 
								LEFT JOIN tax txpph ON txpph.`tax_id` = pc.`pphID`
								LEFT JOIN account apph ON apph.`account_id` = txpph.`account_id`
								WHERE pc.`payment_id` = {$paymentId}";
                    $resultPC = $myDatabase->query($sqlPC, MYSQLI_STORE_RESULT);

                    if ($resultPC !== false && $resultPC->num_rows > 0) {
                        while ($rowPC = $resultPC->fetch_object()) {

                            $cashId = $rowPC->payment_cash_id;
                            $amountConv = $rowPC->amount_converted;

                            if ($rowPC->apph_id == '' || $rowPC->apph_id == 0 || $rowPC->apph_id == 'NULL') {
                                $apph_id = 'NULL';
                            } else {
                                $apph_id = $rowPC->apph_id;
                            }

                            if ($rowPC->ppnID != '' || $rowPC->ppnID != 0 || $rowPC->ppnID != 'NULL') {
                                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$rowPC->type}) AND account_no IN (150410)";
                                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                                if ($resultAccount !== false && $resultAccount->num_rows == 1) {
                                    $rowAccount = $resultAccount->fetch_object();
                                    $accountId = $rowAccount->account_id;

                                }
                            }
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowPC->payment_cash_id != '' || $rowPC->payment_cash_id != 0 || $rowPC->payment_cash_id != 'NULL') {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted}),";
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$rowPC->ppn_converted})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$rowPC->account_id}, NULL, {$rowPC->amount_converted}),";
                                    $insertValues .= "(2, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$apph_id}, NULL, {$rowPC->pph_converted}),";
                                    $insertValues .= "(1, '{$source}', NULL, 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0,  {$cashId}, {$accountId}, NULL,  {$rowPC->ppn_converted})";
                                }
                            }

                        }
                    } else {
                        $boolContinue = false;
                    }

                }
                if ($row->payment_method == 2) {


                    $sqlPayment = "SELECT b.account_id, amount_journal FROM payment p LEFT JOIN bank b ON p.bank_id = b.bank_id WHERE p.payment_id = {$paymentId}";
                    $resultPayment = $myDatabase->query($sqlPayment, MYSQLI_STORE_RESULT);

                    if ($resultPayment !== false && $resultPayment->num_rows > 0) {
                        while ($rowPayment = $resultPayment->fetch_object()) {


                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowPayment->account_id != '' || $rowPayment->account_id != 0 || $rowPayment->account_id != 'NULL') {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 12, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, {$cashId}, {$rowPayment->account_id}, NULL, {$rowPayment->amount_journal})";
                                }
                            }

                        }
                    } else {
                        $boolContinue = false;
                    }

                }

            } elseif ($row->invoice_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment PKS kontrak">
                if ($row->payment_method == 1) {
                    $totalInvoiceAmount = 0;
                    $totalInvoicetDP = 0;

                    $sqlInvoice = "SELECT i.*,
                                        (
                                            SELECT COALESCE(SUM(amount_journal), 0) FROM payment
                                            WHERE invoice_id = i.invoice_id
                                            AND payment_method = 2 AND payment_status = 0
                                        ) AS total_dp
                                    FROM invoice i
                                    WHERE i.invoice_id = {$row->invoice_id}";
                    $resultInvoice = $myDatabase->query($sqlInvoice, MYSQLI_STORE_RESULT);

                    if ($resultInvoice !== false && $resultInvoice->num_rows == 1) {
                        $rowInvoice = $resultInvoice->fetch_object();

                        // $totalInvoiceAmount = $rowInvoice->amount;
                        // $totalContractPPN = $rowContract->total_ppn;
                        $totalInvoiceDP = $rowInvoice->total_dp;

                    } else {
                        echo 'false1';
                    }
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}, 7) AND account_no in ({$row->account_no},{$row->ap_account_no}, 130005)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->account_no && $row->amount_converted < 0) {
                                $amountConverted = $row->amount_converted * -1;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Payment', 1, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountConverted})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Payment', 1, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountConverted})";
                                }
                            } elseif ($rowAccount->account_no == $row->ap_account_no && $row->amount_journal < 0) {
                                $amountJournal = $row->amount_journal * -1;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$amountJournal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$row->amount_converted})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$row->amount_converted})";
                                }
                            } elseif ($rowAccount->account_no == $row->ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 10, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL,  {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 130005) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalInvoiceDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalInvoiceDP})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                        echo 'false2';
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}, 7) AND account_no in ({$row->ap_account_no}, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 10, {$contractId},  {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->vendor_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment PKS curah">
                if ($row->payment_method == 1) {
                    // $totalAmount = 0;
                    //  $totaDP = 0;

                    $sqlAccount2 = "SELECT t.transaction_id, a.account_id, a.account_no,(t.unit_price * t.quantity) AS total_amount
                FROM `transaction` t 
                LEFT JOIN payment p
                    ON t.payment_id = p.payment_id
                 LEFT JOIN account a
                 ON a.account_id = p.account_id
                               WHERE p.payment_id = {$row->payment_id}";
                    $resultAccount2 = $myDatabase->query($sqlAccount2, MYSQLI_STORE_RESULT);

                    if ($resultAccount2 !== false && $resultAccount2->num_rows > 0) {
                        while ($rowAccount2 = $resultAccount2->fetch_object()) {

                            /*	if($row->b_currency_id == $row->currency_id && $row->currency_id != 1) {
				                // USD - USD - IDR
				                $totalAll2 = ceil($rowAccount2->amount * $row->exchange_rate);
				            } elseif($row->b_currency_id != $row->currency_id && $row->currency_id != 1) {
				                // IDR - USD - IDR
				                $totalAll2 = $rowAccount2->amount;
				            } elseif($row->b_currency_id != $row->currency_id && $row->currency_id == 1) {
				                // USD - IDR - IDR
				                $totalAll2 = ceil($rowAccount2->amount * $row->exchange_rate);
				            } else {
				                $totalAll2 = $rowAccount2->amount;
				            }
                    */
                            // $totalAmount2 = ceil($totalAll2 - $row->ppn_journal);
                            //$totalDP = $totalAmount - $row->amount_journal;

                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount2->account_no == 210101) {
                                if ($paymentStatus == 1) {
                                    if ($rowAccount2->total_amount < 0) {
                                        $tAmount = $rowAccount2->total_amount * -1;
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount2->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount2->account_id}, NULL, {$tAmount})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount2->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount2->account_id}, NULL, {$rowAccount2->total_amount})";
                                    }
                                } else {
                                    if ($rowAccount2->total_amount < 0) {
                                        $tAmount = $rowAccount2->total_amount * -1;
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount2->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount2->account_id}, NULL, {$tAmount})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount2->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount2->account_id}, NULL, {$rowAccount2->total_amount})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                if ($row->payment_method == 1) {
                    $totalAmount = 0;
                    $totaDP = 0;

                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1, 7) AND account_no in (150410, 130002, {$row->account_no}, 230204)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($row->b_currency_id == $row->currency_id && $row->currency_id != 1) {
                        // USD - USD - IDR
                        $totalAll = ceil($row->amount * $row->exchange_rate);
                    } elseif ($row->b_currency_id != $row->currency_id && $row->currency_id != 1) {
                        // IDR - USD - IDR
                        $totalAll = $row->amount;
                    } elseif ($row->b_currency_id != $row->currency_id && $row->currency_id == 1) {
                        // USD - IDR - IDR
                        $totalAll = ceil($row->amount * $row->exchange_rate);
                    } else {
                        $totalAll = $row->amount;
                    }

                    $totalAmount = ceil($totalAll - $row->ppn_journal);
                    $totalDP = $totalAmount - $row->amount_journal;

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            } elseif ($rowAccount->account_no == 130002) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 230204) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1, 7) AND account_no in (130002, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 130002) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {{$row->amount_journal}})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 2, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {{$row->amount_journal}})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->freight_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment FC">
                if ($row->payment_method == 1) {

                    $sqlAccount1 = "SELECT t.transaction_id, a.account_id, a.account_no, fc.freight_id,
ROUND((CASE WHEN f.freight_rule = 1 THEN t.freight_price * t.send_weight
ELSE t.freight_price * t.freight_quantity END) - COALESCE(ts.amt_claim,0),10) AS total_amount
                FROM `transaction` t
                LEFT JOIN payment p
                    ON t.fc_payment_id = p.payment_id
                 LEFT JOIN account a
                 ON a.account_id = p.account_id
                 LEFT JOIN freight_cost fc
                 ON t.`freight_cost_id` = fc.`freight_cost_id`
		LEFT JOIN freight f
		ON fc.freight_id = f.freight_id
		LEFT JOIN transaction_shrink_weight ts
		ON t.transaction_id = ts.transaction_id	
                               WHERE p.payment_id = {$row->payment_id}";
                    $resultAccount1 = $myDatabase->query($sqlAccount1, MYSQLI_STORE_RESULT);


                    if ($resultAccount1 !== false && $resultAccount1->num_rows > 0) {
                        while ($rowAccount1 = $resultAccount1->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount1->account_no == 210103) {
                                if ($paymentStatus == 1) {
                                    if ($rowAccount1->total_amount < 0) {
                                        $tAmount = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$tAmount})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                } else {
                                    if ($rowAccount1->total_amount < 0) {
                                        $tAmount = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$tAmount})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }

                if ($row->payment_method == 1) {
                    $totaDP = 0;
                    $totalPPhDP = 0;
                    if ($row->f_account_no == '' || $row->f_account_no == 'NULL') {
                        $f_account_no = 0;
                    } else {
                        $f_account_no = $row->f_account_no;
                    }
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (2, 7) AND account_no in (150410, 130003, {$row->account_no},{$f_account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($row->original_amount <= 0) {
                        $totalDP = ($row->amount - $row->ppn_journal) + $row->pph_journal;
                        $totalPPhDP = $row->pph_journal;
                        $totalPPN_dp = $row->ppn_journal;
                    } else {
                        $totalDP = (($row->amount - $row->original_amount) / ((100 - $row->f_pph_tax) / 100));
                        $totalPPhDP = $totalDP - ($row->amount - $row->original_amount);
                        $totalPPN_dp = $totalDP * ($row->f_ppn_tax / 100);
                    }

                    if ($row->fc_tax_category == 0 && $row->ppn_journal > 0 && $totalDP > 0) {
                        $amountJournal = (($row->amount - $row->ppn_journal) + $row->pph_journal) - $totalDP;
                    } elseif ($row->fc_tax_category == 0 && $totalDP > 0) {
                        $amountJournal = ($row->original_amount + $row->pph_journal) - $totalPPN_dp;
                    } elseif ($totalDP > 0) {
                        $amountJournal = $row->original_amount - $totalPPN_dp;
                    } elseif ($row->fc_tax_category == 0) {
                        $amountJournal = ($row->original_amount + $row->pph_journal);
                    } else {
                        $amountJournal = $row->original_amount;
                    }


                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }
                            if ($rowAccount->account_no == 150410) {
                                $ppnTotal = $row->ppn_journal - $totalPPN_dp;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppnTotal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppnTotal})";
                                }
                            } elseif ($rowAccount->account_no == 130003 && $totalDP !== 0) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', 'DP', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', 'DP', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                }
                            } elseif ($rowAccount->account_no == $f_account_no && $totalPPhDP !== 0) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'DP', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalPPhDP})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'DP', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalPPhDP})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no && $amountJournal < 0) {
                                $amountJournalIN = $amountJournal * -1;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalIN})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalIN})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $f_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (2, 7) AND account_no in (130003, {$row->account_no} , {$row->f_account_no}, 150410)";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 130003) {
                                $f_amountJournal = ($row->amount_journal + $row->ppn_journal) - $row->pph_journal;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$f_amountJournal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$f_amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $row->f_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 3, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->vendor_handling_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment HC">
                if ($row->payment_method == 1) {

                    $sqlAccount1 = "SELECT t.transaction_id, a.account_id, a.account_no, vhc.vendor_handling_id,
vhc.price_converted * t.handling_quantity AS total_amount

                FROM `transaction` t 
                LEFT JOIN payment p
                    ON t.hc_payment_id = p.payment_id
                 LEFT JOIN account a
                 ON a.account_id = p.account_id
                 LEFT JOIN vendor_handling_cost vhc
                 ON t.`handling_cost_id` = vhc.`handling_cost_id`
				 LEFT JOIN vendor_handling vh 
				 ON vhc.vendor_handling_id = vh.vendor_handling_id
                               WHERE p.payment_id = {$row->payment_id}";
                    $resultAccount1 = $myDatabase->query($sqlAccount1, MYSQLI_STORE_RESULT);


                    if ($resultAccount1 !== false && $resultAccount1->num_rows > 0) {
                        while ($rowAccount1 = $resultAccount1->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount1->account_no == 210106) {
                                if ($paymentStatus == 1) {
                                    if ($rowAccount1->total_amount < 0) {
                                        $totAmount = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$totAmount})";
                                    } else {
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                } else {
                                    if ($rowAccount1->total_amount < 0) {
                                        $totAmount = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$totAmount})";
                                    } else {
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }

                if ($row->payment_method == 1) {
                    $totaDP = 0;
                    $totalPPhDP = 0;
                    if ($row->vh_account_no == '' || $row->vh_account_no == 'NULL') {
                        $vh_account_no = 0;
                    } else {
                        $vh_account_no = $row->vh_account_no;
                    }
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (9, 7) AND account_no in (150410, 130006, {$row->account_no},{$vh_account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($row->amount_journal < 0) {
                        $totalDP = $row->amount - $row->pph_journal - $row->amount_journal;
                        $totalPPhDP = ($totalDP / (100 - $row->vh_pph_tax / 100)) - $totalDP;
                    } else {
                        $totalDP = (($row->amount - $row->amount_journal) / ((100 - $row->vh_pph_tax) / 100));
                        $totalPPhDP = $totalDP - ($row->amount - $row->amount_journal);
                    }

                    if ($row->vhc_tax_category == 0) {
                        $amountJournal = $row->amount_journal + $row->pph_journal;
                    } else {
                        $amountJournal = $row->amount_journal;
                    }


                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }
                            if ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            } elseif ($rowAccount->account_no == 130006 && $totalDP !== 0) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', 'DP', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', 'DP', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                }
                            } elseif ($rowAccount->account_no == $vh_account_no && $totalPPhDP !== 0) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'DP', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalPPhDP})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'DP', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalPPhDP})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no && $amountJournal < 0) {
                                $amountJournalIN = $amountJournal * -1;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalIN})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalIN})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $vh_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (9, 7) AND account_no in (130006, {$row->account_no} , {$row->vh_account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 130006) {
                                $vh_amountJournal = $row->amount_journal - $row->pph_journal;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                $vh_amountJournal = $row->amount_journal - $row->pph_journal;
                                if ($vh_amountJournal == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$f_amountJournal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$vh_amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $row->vh_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 13, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->labor_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment UC">
                if ($row->payment_method == 1) {

                    $sqlAccount1 = "SELECT t.transaction_id, a.account_id, a.account_no, t.unloading_price AS total_amount
                FROM `transaction` t 
                LEFT JOIN payment p
                    ON t.uc_payment_id = p.payment_id
                 LEFT JOIN account a
                 ON a.account_id = p.account_id
                               WHERE p.payment_id = {$row->payment_id}";
                    $resultAccount1 = $myDatabase->query($sqlAccount1, MYSQLI_STORE_RESULT);


                    if ($resultAccount1 !== false && $resultAccount1->num_rows > 0) {
                        while ($rowAccount1 = $resultAccount1->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount1->account_no == 210104) {

                                if ($paymentStatus == 1) {
                                    if ($rowAccount1->total_amount < 0) {
                                        $totalAmt = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$totalAmt})";
                                    } else {
                                        $totAmount = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                } else {
                                    if ($rowAccount1->total_amount < 0) {
                                        $totalAmt = $rowAccount1->total_amount * -1;
                                        $insertValues .= "(2, '{$source}', 'Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$totalAmt})";
                                    } else {

                                        $insertValues .= "(1, '{$source}', 'Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, {$rowAccount1->transaction_id}, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount1->account_id}, NULL, {$rowAccount1->total_amount})";
                                    }
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                if ($row->payment_method == 1) {
                    // $totalAmount = 0;
                    $totaDP = 0;
                    if ($row->u_account_no == '' || $row->u_account_no == 'NULL') {
                        $u_account_no = 0;
                    } else {
                        $u_account_no = $row->u_account_no;
                    }
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (3, 7) AND account_no in ( 150410, 130004, {$row->account_no}, {$u_account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    /* if($row->b_currency_id == $row->currency_id && $row->currency_id != 1) {
				                // USD - USD - IDR
				                $totalAll = ceil($row->amount * $row->exchange_rate);
				            } elseif($row->b_currency_id != $row->currency_id && $row->currency_id != 1) {
				                // IDR - USD - IDR
				                $totalAll = $row->amount;
				            } elseif($row->b_currency_id != $row->currency_id && $row->currency_id == 1) {
				                // USD - IDR - IDR
				                $totalAll = ceil($row->amount * $row->exchange_rate);
				            } else {
				                $totalAll = $row->amount;
				            }
                    */
                    //$totalAmount = ceil($totalAll - $row->ppn_journal);
                    $totalDP = $row->amount - $row->amount_journal;

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            } elseif ($rowAccount->account_no == 130004) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $u_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (3, 7) AND account_no in (130004, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 130004) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 4, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->sales_id != '') {
                // <editor-fold defaultstate="collapsed" desc="payment Sales">
                if ($row->payment_method == 1) {
                    $totalSalesAmount = 0;
                    $totalSalesDP = 0;

                    $sqlContract = "SELECT sh.quantity, sl.price_converted, 
                                        (
                                            SELECT COALESCE(SUM(amount_journal), 0) FROM payment_detail
                                            WHERE shipment_id = pd.shipment_id
                                            AND payment_id <> {$paymentId}
                                        ) AS total_dp
                                    FROM payment_detail pd
                                    INNER JOIN shipment sh
                                        ON sh.shipment_id = pd.shipment_id
                                    INNER JOIN sales sl
                                        ON sl.sales_id = sh.sales_id
                                    WHERE pd.payment_id = {$paymentId}";
                    $resultContract = $myDatabase->query($sqlContract, MYSQLI_STORE_RESULT);

                    if ($resultContract !== false && $resultContract->num_rows == 1) {
                        $rowContract = $resultContract->fetch_object();


                        if ($row->ppn_journal !== 0) {
                            $totalSalesDP = $rowContract->total_dp / 1.1;
                        } else {
                            $totalSalesDP = $rowContract->total_dp;
                        }
                    }


                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1, 7) AND account_no in (210200, 230100,{$row->ap_account_no},  {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($row->ppn_journal != 0) {
                                $totalSalesAmount = $row->amount_journal / 1.1;
                            } else {
                                $totalSalesAmount = $row->amount_journal;
                            }
                            if ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Payment', 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Payment', 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 210200) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalSalesDP})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalSalesDP})";
                                }
                            } elseif ($rowAccount->account_no == $row->ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalSalesAmount})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalSalesAmount})";
                                }
                            } elseif ($rowAccount->account_no == 230100) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN (1, 7) AND account_no in (230100, 210200, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);
                    $amountJournalDP = $row->amount_journal - $row->ppn_journal;
                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 210200) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournalDP})";
                                }
                            } elseif ($rowAccount->account_no == 230100) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 1, 9, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } elseif ($row->ap_account_type == 7 && $row->stockpile_contract_id == '' && $row->vendor_id == '' && $row->freight_id == '' && $row->labor_id == '' && $row->general_vendor_id == '' && $row->sales_id == '') {
                // <editor-fold defaultstate="collapsed" desc="internal transfer">
                echo $row->ap_account_type;
                $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 7 AND account_no in ({$row->ap_account_no}, {$row->account_no})";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        if ($rowAccount->account_no == $row->ap_account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(2, '{$source}', 'Payment', 2, 6, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                            } else {
                                $insertValues .= "(1, '{$source}', 'Payment', 2, 6, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                            }
                        }
                        if ($rowAccount->account_no == $row->account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(1, '{$source}', NULL, 2, 6, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, 2, 6, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                            }
                        }
                    }
                } else {
                    $boolContinue = false;
                }
                //</editor-fold>
            } elseif ($row->payment_type == 1 && $row->ap_account_type != 7 && $row->sales_id == '') {
                // <editor-fold defaultstate="collapsed" desc="in umum ho">
                echo 'test2';
                if ($row->gv_account_no == '' || $row->gv_account_no == 'NULL') {
                    $gv_account_no = 0;
                } elseif ($row->gv_account_no != '' || $row->gv_account_no != 'NULL') {
                    $gv_account_no = $row->gv_account_no;
                }
                $sqlAccount = "SELECT a.* FROM 
				(SELECT account_id, account_no FROM account WHERE account_type in ({$row->ap_account_type}, 7) AND account_no in ({$row->ap_account_no}, {$row->account_no}) UNION ALL SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}) AND account_no in ({$gv_account_no}, 150410))
				 a GROUP BY a.account_no";
                $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                    while ($rowAccount = $resultAccount->fetch_object()) {
                        if ($insertValues != "") {
                            $insertValues .= ", ";
                        }

                        $totalAmount = $row->amount_journal - $row->ppn_journal;

                        if ($row->ppn_journal != 0 && $row->pph_journal == 0) {
                            $amountJ = $row->amount_journal - $row->ppn_journal;
                        } else {
                            $amountJ = $row->amount_journal;
                        }

                        if ($rowAccount->account_no == $row->account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(2, '{$source}', 'Payment', 1, 8, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                            } else {
                                $insertValues .= "(1, '{$source}', 'Payment', 1, 8, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                            }
                        } elseif ($rowAccount->account_no == $row->ap_account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(1, '{$source}', NULL, 1, 8, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJ})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, 1, 8, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJ})";
                            }
                        } elseif ($rowAccount->account_no == 150410) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                            } else {
                                $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                            }
                        } elseif ($rowAccount->account_no == $gv_account_no && $row->ap_account_no != $gv_account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                            } else {
                                $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                            }
                        } elseif ($rowAccount->account_no == $gv_account_no && $row->ap_account_no == $gv_account_no) {
                            if ($paymentStatus == 1) {
                                $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, 0)";
                            } else {
                                $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, 0)";
                            }
                        }
                    }
                } else {
                    $boolContinue = false;
                }
                // </editor-fold>
            } elseif ($row->payment_type == 2 && $row->stockpile_contract_id == '' && $row->vendor_id == '' && $row->freight_id == '' && $row->labor_id == '' && $row->general_vendor_id != '') {
                // <editor-fold defaultstate="collapsed" desc="out other with general vendor">

                if ($row->payment_method == 1) {

                    if ($row->gv_account_no == '' || $row->gv_account_no == 'NULL') {
                        $gv_account_no = 0;
                    } elseif ($row->gv_account_no != '' || $row->gv_account_no != 'NULL') {
                        $gv_account_no = $row->gv_account_no;
                    }

                    $account_no = $row->account_no;
                    $ap_account_no = $row->ap_account_no;
                    $ppn_journal = $row->ppn_journal;
                    $pph_journal = $row->pph_journal;
                    $amount_journal = $row->amount_journal;
                    $ap_account_type = $row->ap_account_type;
                    $totalAmount = 0;
                    $totaDP = 0;
                    //$totalAll = $row->amount;

                    $sqlAccount = "SELECT a.* FROM (SELECT account_id, account_no FROM account WHERE account_type IN ({$ap_account_type}, 7) AND account_no IN (130005, {$account_no}, {$ap_account_no})
					UNION ALL SELECT account_id, account_no FROM account WHERE account_type IN ({$ap_account_type}) AND account_no IN ({$gv_account_no}, 150410))a GROUP BY a.account_no";

                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($row->b_currency_id == $row->currency_id && $row->currency_id != 1) {
                        // USD - USD - IDR
                        $totalAll = $row->amount * $row->exchange_rate;
                    } elseif ($row->b_currency_id != $row->currency_id && $row->currency_id != 1) {
                        // IDR - USD - IDR
                        $totalAll = $row->amount;
                    } elseif ($row->b_currency_id != $row->currency_id && $row->currency_id == 1) {
                        // USD - IDR - IDR
                        $totalAll = $row->amount * $row->exchange_rate;
                    } else {
                        $totalAll = $row->amount;
                    }

                    $totalAmount = $totalAll - $ppn_journal;
                    $totalDP = 0;

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                                }
                            } elseif ($rowAccount->account_no == 130005) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalDP})";
                                }
                            } elseif ($rowAccount->account_no == $account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId},{$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId},{$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$ppn_journal})";
                                }
                            } elseif ($rowAccount->account_no == $gv_account_no && $gv_account_no != $ap_account_no && $rowAccount->gv_pph_tax_id == 21) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph_journal})";
                                }
                            } elseif ($rowAccount->account_no == $gv_account_no && $gv_account_no != $ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$pph_journal})";
                                }
                            } elseif ($rowAccount->account_no == $gv_account_no && $gv_account_no == $ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, 0)";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, 0)";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                        echo 'FALSE 2';
                    }
                } elseif ($row->payment_method == 2) {
                    if ($row->gv_account_no == '' || $row->gv_account_no == 'NULL') {
                        $gv_account_no = 0;
                    } elseif ($row->gv_account_no != '' || $row->gv_account_no != 'NULL') {
                        $gv_account_no = $row->gv_account_no;
                    }
                    $sqlAccount = "SELECT a.* FROM (SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}, 7) AND account_no IN ({$row->account_no}, {$row->ap_account_no})
					UNION ALL SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}) AND account_no IN ({$gv_account_no}, 150410)) a GROUP BY a.account_no";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->ap_account_no) {
                                $amountJournal = $row->amount_journal - $row->ppn_journal;
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$amountJournal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == 150410) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->ppn_journal})";
                                }
                            } elseif ($rowAccount->account_no == $gv_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->pph_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;


                    }
                }
                // </editor-fold>
            } elseif ($row->payment_type == 2 && $row->stockpile_contract_id == '' && $row->vendor_id == '' && $row->freight_id == '' &&
                $row->labor_id == '' && $row->general_vendor_id == '') {
                // <editor-fold defaultstate="collapsed" desc="out other without general vendor">
                if ($row->payment_method == 1) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}, 7) AND account_no in ({$row->account_no}, {$row->ap_account_no}) GROUP BY account_no";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    $totalAmount = ceil($row->amount_journal - $row->ppn_journal);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == $row->ap_account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$totalAmount})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                } elseif ($row->payment_method == 2) {
                    $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type IN ({$row->ap_account_type}, 7) AND account_no in (130005, {$row->account_no})";
                    $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

                    if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                        while ($rowAccount = $resultAccount->fetch_object()) {
                            if ($insertValues != "") {
                                $insertValues .= ", ";
                            }

                            if ($rowAccount->account_no == 130005) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(2, '{$source}', 'Down Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(1, '{$source}', 'Down Payment', 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            } elseif ($rowAccount->account_no == $row->account_no) {
                                if ($paymentStatus == 1) {
                                    $insertValues .= "(1, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                } else {
                                    $insertValues .= "(2, '{$source}', NULL, 2, 5, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$paymentId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                                }
                            }
                        }
                    } else {
                        $boolContinue = false;
                    }
                }
                // </editor-fold>
            } else {
                $boolContinue = false;
            }
        } else {
            $boolContinue = false;
        }


        // </editor-fold>
    } elseif ($returnId != 'NULL') {
        // <editor-fold defaultstate="collapsed" desc="return payment">

        $sql = "SELECT p.*, a.account_no, ap.account_type AS ap_account_type, ap.account_no AS ap_account_no,
                    b.currency_id AS b_currency_id
                FROM payment p 
                INNER JOIN bank b
                    ON b.bank_id = p.bank_id
                INNER JOIN account a
                    ON a.account_id = b.account_id
                INNER JOIN account ap
                    ON ap.account_id = p.account_id 		   
                WHERE p.payment_id = {$returnId} AND payment_status = 0";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

        if ($result !== false && $result->num_rows == 1) {
            $row = $result->fetch_object();

            // <editor-fold defaultstate="collapsed" desc="in return">
            $sqlAccount = "SELECT account_id, account_no FROM account WHERE account_type = 7 AND account_no in ({$row->account_no})";
            $resultAccount = $myDatabase->query($sqlAccount, MYSQLI_STORE_RESULT);

            if ($resultAccount !== false && $resultAccount->num_rows > 0) {
                while ($rowAccount = $resultAccount->fetch_object()) {
                    if ($insertValues != "") {
                        $insertValues .= ", ";
                    }

                    if ($rowAccount->account_no == $row->account_no) {
                        $insertValues .= "(1, '{$source}', 'Payment', 1, 7, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$returnId}, {$jurnalId}, 0, 0, {$rowAccount->account_id}, NULL, {$row->amount_journal})";
                    }
                }

                if ($insertValues != "") {
                    $insertValues .= ", ";
                }

                $insertValues .= "(2, '{$source}', NULL, 1, 7, {$contractId}, {$invoiceId}, {$transactionId}, 0, {$returnId}, {$jurnalId}, 0, 0, {$row->account_id}, NULL, {$row->amount_journal})";
            } else {
                $boolContinue = false;
            }
            // </editor-fold>
        } else {
            $boolContinue = false;
        }

        // </editor-fold>
    }

    if ($boolContinue) {
        // INSERT
        $sql = "INSERT INTO general_ledger (general_ledger_type, general_ledger_module, general_ledger_method, general_ledger_transaction_type, general_ledger_for, contract_id, invoice_id, transaction_id, t_id, payment_id, jurnal_id, i_id, cash_id, account_id, description, amount) VALUES {$insertValues}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        if ($result === false) {
            echo 'FALSE';
            echo '</br>';
            echo $insertValues;
            echo '</br>';

        } else {
            echo 'TRUE';
            echo $sql;
            //$general_ledger_id = $myDatabase->insert_id;

        }
    }
}
