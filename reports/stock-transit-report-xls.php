<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
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
    )
);

$styleArray4 = array(
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
// </editor-fold>


$whereAvailableProperty = '';

$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$periodFull = '';


if ($periodTo != '') {
    $periodFull = "To " . $periodTo . " ";
}

// </editor-fold>

$fileName = "Stock Transit Report " . $periodFull . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "D";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);


$rowActive = 1;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

if ($periodFull != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Period {$periodFull}");
}


$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Stock Transit Report");

$rowActive++;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Kode Mutasi");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Amount PKS");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Amount GL");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Posting Value");

$sqlContent = "SELECT mh.`mutasi_header_id`, mh.`kode_mutasi`,
    c.pks, SUM(CASE WHEN gl.`general_ledger_type`=1 THEN gl.amount ELSE -gl.amount END) AS total_gl,COALESCE(f.`inventory_value`,0) AS posting_value
    FROM general_ledger gl
    LEFT JOIN invoice_detail id ON id.`invoice_detail_id`=gl.`invoice_id`
    LEFT JOIN invoice i ON i.`invoice_id`=id.`invoice_id`
    LEFT JOIN mutasi_detail md ON md.`mutasi_detail_id`=id.`mutasi_detail_id`
    LEFT JOIN mutasi_header mh ON mh.`mutasi_header_id`=md.`mutasi_header_id`
    LEFT JOIN
    (
    SELECT mutasi_id,transaction_date, SUM(send_weight),SUM(netto_weight), SUM(inventory_value) AS inventory_value FROM TRANSACTION
    WHERE (mutasi_id IS NOT NULL OR mutasi_id) <>0 AND notim_status <>1 AND slip_retur IS NULL
    GROUP BY mutasi_id
    )f ON f. mutasi_id=mh.mutasi_header_id
    LEFT JOIN(
    SELECT SUM(st.`send_weight`*c.`price_converted`) AS PKS, mh.`mutasi_header_id`
    FROM mutasi_header mh
    LEFT JOIN stock_transit st ON mh.`mutasi_header_id` = st.`mutasi_header_id`
    LEFT JOIN stockpile_contract sc ON st.`stockpile_contract_id`=sc.`stockpile_contract_id`
    LEFT JOIN contract c ON c.`contract_id`=sc.`contract_id`
    LEFT JOIN stockpile s ON mh.`stockpile_to` = s.stockpile_id
    WHERE  st.`loading_date`<= '{$periodTo}' 
    GROUP BY mh.`mutasi_header_id`
    )c ON c.mutasi_header_id=mh.`mutasi_header_id`
    WHERE gl.`account_id` IN (455,458)
    AND ((i.`payment_status` = 0 AND i.invoice_status = 0) OR (i.payment_status = 1 AND i.`invoice_status` = 0))
    AND i.`invoice_no`<>'INV/JPJ/2008/306'
    GROUP BY mh.`mutasi_header_id`";
$resultContent = $myDatabase->query($sqlContent, MYSQLI_STORE_RESULT);
//GrandTotal
$grandTotalAmountPks = 0;
$grandTotalAmountGl = 0;
$grandTotalPostingValue = 0;
while ($rowContent = $resultContent->fetch_object()) {

    $rowActive++;
    $objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}")->applyFromArray($styleArray2);
    $objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", $rowContent->kode_mutasi);
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $rowContent->pks);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $rowContent->total_gl);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $rowContent->posting_value);

    $grandTotalAmountPks += $rowContent->pks;
    $grandTotalAmountGl += $rowContent->total_gl;
    $grandTotalPostingValue += $rowContent->posting_value;
}

$rowActive++;
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:M{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "TOTAL");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $grandTotalAmountPks);
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $grandTotalAmountGl);
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $grandTotalPostingValue);

$bodyRowEnd = $rowActive;


// </editor-fold>


// <editor-fold defaultstate="collapsed" desc="Formating Excel">
// Set column width
for ($temp = ord("A"); $temp <= ord("D"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}

$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":B{$bodyRowEnd}")->getNumberFormat()->setFormatCode("DD-MMM-YYYY");

// Set number format for Amount
$objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow + 1) . ":D{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

// Set border for table
$objPHPExcel->getActiveSheet()->getStyle("A" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="Save Excel and return to browser">
ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>