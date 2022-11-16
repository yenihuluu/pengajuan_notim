<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

require_once PATH_EXTENSION . DS . 'PHPExcel.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/IOFactory.php';
require_once PATH_EXTENSION . DS . 'PHPExcel/Cell/AdvancedValueBinder.php';


// <editor-fold defaultstate="collapsed" desc="Define Style for excel">
$styleArray = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startcolor' => array(
            'argb' => 'FFA0A0A0'
        ),
        'endcolor' => array(
            'argb' => 'FFFFFFFF'
        )
    )
);

$styleArray1 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);

$styleArray2 = array(
    'font' => array(
        'bold' => true,
        'size' => 14
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);

$styleArray3 = array(
    'font' => array(
        'bold' => true
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFF00')
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )

);

$styleArray4 = array(
    'font' => array(
        'bold' => true
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
        )

);

$styleArray5 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray6 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray7 = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

$styleArray8 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
    )
);

$styleArray9 = array(
    'font' => array(
        'bold' => true
    )
);

$styleArray10 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )

);
$styleArray11 = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
    )
);

$styleArray12 = array(
    'font' => array(
        'bold' => true
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FF0000')
    )
);

$shipmentCode = $myDatabase->real_escape_string($_POST['shipmentCode']);
$stockpileID = $myDatabase->real_escape_string($_POST['stockpileID']);
$return_value = '';

if ($shipmentCode != '') {
    $shipmentCode = $_POST['shipmentCode'];
    $sql = "SELECT st.stockpile_name AS stockpile, ship.shipment_no AS shipmentNo, ship.quantity AS qty FROM invoice_detail invD
            LEFT JOIN shipment ship ON ship.shipment_id = invD.shipment_id
            LEFT JOIN sales sl ON sl.sales_id = ship.sales_id
            LEFT JOIN stockpile st ON st.stockpile_id = sl.stockpile_id
            WHERE invD.shipment_id = {$shipmentCode}";
        $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
        $row = $result->fetch_object();
        $shipmentNo = $row->shipmentNo . " ";
        $stockpileName = $row->stockpile . " ";
        $qty1 = $row->qty;
        if ($qty1 == 0 || $qty1==''){
            $qty1=0;
        }else{
            $qty1 = $row->qty;
        }
}

$sqlKiri = "SELECT ac.account_name AS Biaya, mst.tipe AS type1, 
                mst.vendorName AS vendor, 
                mst.exchangeRate AS currConvert,
                CASE WHEN mst.currencyID = '1' THEN 'Rp. ' 
                WHEN mst.currencyID = '2' THEN '$ ' 
                ELSE NULL END AS currency,
                mst.price_MT AS priceMT, 
                mst.flat_Price AS flatPrice,
                ac.account_id as accountID
            FROM master_shipmentcost mst
            LEFT JOIN stockpile st ON st.stockpile_id = mst.stockpile_id
            LEFT JOIN account ac ON ac.account_id = mst.account_id
            WHERE mst.stockpile_id = {$stockpileID}";
$resultKiri = $myDatabase->query($sqlKiri, MYSQLI_STORE_RESULT);


$fileName = "Shipment Code " . $shipmentNo  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn1 = "I";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn1}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

if ($shipmentNo != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stockpile = {$stockpileName}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "shipment No = {$shipmentNo}");

    $rowActive++;
    $rowActive++;
}

$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn1}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(18);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "DATA SHIPMENT COST MASTER");

// $rowActive++;
// $objPHPExcel->getActiveSheet()->getStyle("F{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray8); 
// $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "qty :");
// $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", (int)$qty1);

$rowActive++;
$headerRow1 = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Biaya");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Type");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Kurs");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Price/MT");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Quantity");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Additional/Flat");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Total Amount");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray4);

$no1 = 1;
while($rowKiri = $resultKiri->fetch_object()) {
    $rowbaru = $rowKiri->Biaya;
    $TempQty = (int)$qty1;
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $no1);
    $objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($rowKiri->Biaya, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowKiri->type1);
    $objPHPExcel->getActiveSheet()->getCell("D{$rowActive}")->setValueExplicit($rowKiri->vendor, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $rowKiri->currency);
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowKiri->priceMT); 
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $qty1);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowKiri->flatPrice);
    $tempPriceMT = $rowKiri->priceMT;
    $tempFlatPrice = $rowKiri->flatPrice;
    $Currency = $rowKiri->currConvert;
    $hitung1 = (int)$tempPriceMT*(int)$Currency;
    $hitung2 = (int)$tempFlatPrice*(int)$Currency;
    $TotalAmount = ($hitung1*$TempQty) + $hitung2;
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $TotalAmount);
    $GrandTotal = $GrandTotal + $TotalAmount; //hitun Grand total amount utk Master
    $accountId = $rowKiri->accountID;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray11);
   
                        $sqlKanan = "SELECT  CASE WHEN inv.invoice_no2 IS NOT NULL THEN inv.invoice_no2
                                            ELSE 'None' END AS Invoice,
                                            SUBSTR(inv.invoice_date, 1, 10) AS invoiceDate,
                                            pay.payment_date AS paymentDate,
                                            ac.account_name AS accountName, mst.vendorName AS vendor,
                                            invd.tamount_converted AS AmountInvoice,
                                            invd.notes AS notes,
                                            ac.account_id AS biayaID,
                                            SUBSTR(rpt.gl_date, 1, 10) AS glDate
                                    FROM invoice_detail invd 
                                    LEFT JOIN master_shipmentcost mst ON mst.account_id = invd.account_id
                                    LEFT JOIN account ac ON ac.account_id = mst.account_id
                                    LEFT JOIN sales sl ON sl.stockpile_id = mst.stockpile_id AND sl.account_id = mst.account_id
                                    LEFT JOIN shipment sh ON sh.sales_id = sl.sales_id
                                    LEFT JOIN invoice inv ON inv.invoice_id = invd.invoice_id
                                    LEFT JOIN payment pay ON pay.invoice_id = invd.invoice_id AND pay.account_id = invd.account_id 
                                    LEFT JOIN rpt_upload rpt ON rpt.shipment_code = sh.shipment_code AND rpt.account_no = ac.account_no
                                    WHERE invd.shipment_id = {$shipmentCode} AND invd.account_id = {$accountId} ORDER BY mst.vendorName ASC";
                        $resultKanan = $myDatabase->query($sqlKanan, MYSQLI_STORE_RESULT);

                        $lastColumn = "I";
                        $rowActive++;
                        $rowActive++;
                        $headerRow = $rowActive;
                        $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Invoice");
                        $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Invoice Date");
                        $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Payment Date");
                        $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Biaya");
                        $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Vendor");
                        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Invoice Notes");
                        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Invoice Amount");
                        $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Accrue Date");
                        $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray10);
                        $jumlahKolom = $resultKanan->num_rows; //buat tau jumlah row
                        if($jumlahKolom == 0){
                            $lastColumn = "J";
                            $rowActive++;
                            $objPHPExcel->getActiveSheet()->getStyle("J{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray12);
                            $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Not Invoice");
                        }
                        
                        $no = 1;
                        $total = 0;
                        while($rowKanan = $resultKanan->fetch_object()){
                            $rowbaru1 = $rowKanan->accountName;
                            $tempInvoice = $rowKanan->Invoice;
                            if($rowbaru === $rowbaru1 && ($tempInvoice!="" || $tempInvoice != null || $tempInvoice != 0 )){
                                $rowActive++;
                                $objPHPExcel->getActiveSheet()->getCell("B{$rowActive}")->setValueExplicit($rowKanan->Invoice, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowKanan->invoiceDate);
                                $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowKanan->paymentDate);
                                $objPHPExcel->getActiveSheet()->getCell("E{$rowActive}")->setValueExplicit($rowKanan->accountName, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $rowKanan->vendor);
                                $objPHPExcel->getActiveSheet()->getCell("G{$rowActive}")->setValueExplicit($rowKanan->notes, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $rowKanan->AmountInvoice); 
                                $AmountVal = $rowKanan->AmountInvoice;
                                $total = $total + (int)$AmountVal;
                                $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $rowKanan->glDate);
                                $no++;  
                            }
                        }

                        $lastColumn = "H";
                        $rowActive++;
                        $objPHPExcel->getActiveSheet()->getStyle("H{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
                        $objPHPExcel->getActiveSheet()->getStyle("G{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray3); 
                        $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Total :");
                        $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $total);
                        $grandTotalInvoice = $grandTotalInvoice + $total; //Hitung Grand total amount untuk invoice
                        $bodyRowEnd = $rowActive;
                        for ($temp = ord("B"); $temp <= ord("J"); $temp++) {
                            $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
                            $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
                        }
                        $objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow + 1) . ":H{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":H{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $no1++;
            $lastColumn1 = "I";
            $rowActive++;
            $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn1}{$rowActive}");
}
$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("H{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray3);
$objPHPExcel->getActiveSheet()->getStyle("G{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray3); 
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Grand Total :");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $grandTotalInvoice);

//$objPHPExcel->getActiveSheet()->getStyle("H{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray8); 
$objPHPExcel->getActiveSheet()->getStyle("I{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray4);
//$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Grand Total Amount :");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $GrandTotal);

$bodyRowEnd1 = $rowActive;
for ($temp1 = ord("A"); $temp1 <= ord("I"); $temp1++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp1))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp1))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow1 + 1) . ":I{$bodyRowEnd1}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow1) . ":{$lastColumn1}{$bodyRowEnd1}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table

ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();