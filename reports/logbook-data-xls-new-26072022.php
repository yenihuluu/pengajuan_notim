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
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
    )
);

$styleArray9 = array(
    'font' => array(
        'bold' => true
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'D3D3D3')
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
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FF0000')
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);



$periodFrom = $myDatabase->real_escape_string($_POST['periodFrom']);
$periodTo = $myDatabase->real_escape_string($_POST['periodTo']);
$requestDate = $myDatabase->real_escape_string($_POST['requestDate']);

$whereProperty = '';
$whereP2 = '';
$whereRequest = '';
$whereR2 = '';
$whereOA = '';
$whereGeneral = '';
$wherePKS = '';
$requestDate = '';
$currentDate = '';
$date = '';


date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y');


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && isset($_POST['requestDate']) && $_POST['requestDate'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
	$requestDate = $_POST['requestDate'];

    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereRequest .= " AND ((DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') = '{$requestDate}'))";
    $whereP2 .= " DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereR2 .= " AND ((pgeneral.requestDateGeneral = '{$requestDate}') OR (pengajuanOA.requestDateOA = '{$requestDate}') OR (purchasing.requestDatePKS = '{$requestDate}'))";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereBatch = " DATE_FORMAT(buh.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";

} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && $_POST['requestDate'] == '') {
	$periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];

    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereP2 .= " DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereBatch = " DATE_FORMAT(buh.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";


}else if($_POST['periodFrom'] == '' && $_POST['periodTo'] == '' && isset($_POST['requestDate']) && $_POST['requestDate'] != ''){
	$requestDate = $_POST['requestDate'];
	
	$whereProperty .= "(DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') = '{$requestDate}')";
	$whereR2 .= "((pgeneral.requestDateGeneral  = '{$requestDate}') OR (pengajuanOA.requestDateOA = '{$requestDate}') OR (purchasing.requestDatePKS = '{$requestDate}'))";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') = '{$requestDate}'";
    $whereBatch = " DATE_FORMAT(buh.entry_date, '%Y/%m/%d') = '{$requestDate}'";

}

//COUNT LOGBOOK
$sql1 = "SELECT DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') AS tgl,
	    #OA/OB/HANDLING
	    pengajuanOA.requestDateOA,
            pengajuanOA.qty_pengajuanOA,
            pengajuanOA.nilai_pengajuanOA,
            pengajuanOA.pending_OA,
            pengajuanOA.nilai_pending_OA,
            pengajuanOA.RejectOA,
            pengajuanOA.Reject_nilaiOA,
           
            
            #PENGAJUAN GENERAL
            pgeneral.requestDateGeneral,
            pgeneral.qty_pGeneral,
            pgeneral.nilai_pGeneral,
            pgeneral.pending_General,
            pgeneral.nilai_pending_General,
            reject_general.RejectGeneral,
            reject_general.Reject_nilaiGeneral,
            
            #INVOICE (GENERAL)
            invoice_general.qty_processGeneral,
            invoice_general.nilai_processGeneral,

            #KONTRAK PKS
            purchasing.requestDatePKS,
            purchasing.pengajuan_PKS,
            purchasing.nilai_PKS,
            purchasing.pending_PKS,
            purchasing.nilai_pending_PKS,

            #PAYMENT
            PV.countPV,
            PV.PVValue,
            
            #BATCH UPLOAD
            batch.qty_batch_upload,
            batch.nilai_batch_upload
            FROM logbook_new logbook
            #PENGAJUAN (OA/OB/HANDLING)
            LEFT JOIN (	
                SELECT DATE_FORMAT(entry_date, '%Y/%m/%d') AS tgl,
                DATE_FORMAT(urgent_payment_date, '%Y/%m/%d') AS requestDateOA,
                COUNT(entry_date) AS qty_pengajuanOA,
                IFNULL(SUM(amount),0) AS nilai_pengajuanOA,
                COUNT(CASE WHEN payment_id IS NULL AND (dp_status = 0 OR dp_status = 1 )THEN 1 ELSE NULL END) AS pending_OA,
                IFNULL(SUM(CASE WHEN payment_id IS NULL AND (dp_status = 0 OR dp_status = 1) THEN amount ELSE NULL END),0) AS nilai_pending_OA,                         
                COUNT(DISTINCT(CASE WHEN dp_status = '2' THEN 1 ELSE NULL END )) AS RejectOA, 
                IFNULL(SUM(CASE WHEN dp_status = '2' THEN amount ELSE NULL END),0) AS Reject_nilaiOA
                FROM pengajuan_payment pp
                GROUP BY tgl
            ) AS pengajuanOA ON pengajuanOA.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #PENGAJUAN  (GENERAL)
            LEFT JOIN (
                SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                DATE_FORMAT(pg.request_payment_date, '%Y/%m/%d') AS requestDateGeneral,
                COUNT(DISTINCT(pg.entry_date)) AS qty_pGeneral, 
                IFNULL(SUM(pgd.tamount_converted - (pgd.tamount_converted * (pgd.`termin`/100))),0) AS nilai_pGeneral,
                COUNT(CASE WHEN pg.invoice_status = 0 THEN 1 ELSE NULL END) AS pending_General,
                IFNULL(SUM(CASE WHEN pg.invoice_status = 0 THEN pgd.tamount_converted ELSE NULL END),0) AS nilai_pending_General                         
                FROM pengajuan_general pg 
                LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id	
                GROUP BY tgl
            ) AS pgeneral ON pgeneral.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #REJECT GENERAL
            LEFT JOIN (
                SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                    COUNT(DISTINCT(pg.`pengajuan_general_id`)) AS RejectGeneral, 
                    IFNULL(SUM(pgd.tamount_converted - (pgd.tamount_converted * (pgd.`termin`/100))),0) AS Reject_nilaiGeneral
                    FROM pengajuan_general pg 
                    LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
                    WHERE (pg.status_pengajuan = '4' OR pg.status_pengajuan = '2' OR pg.status_pengajuan = '5')
                GROUP BY tgl
            )AS reject_general ON reject_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #APPROVE INVOICE (GENERAL)
            LEFT JOIN (	
                SELECT DATE_FORMAT(inv.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN 1 ELSE NULL END)) AS qty_processGeneral,
                IFNULL(SUM(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN (invd.tamount_converted - (invd.tamount_converted * (invd.`termin`/100))) ELSE NULL END),0) AS nilai_processGeneral
                                                                                              
                FROM invoice inv  
                LEFT JOIN pengajuan_general pg ON pg.invoice_id = inv.invoice_id
                INNER JOIN invoice_detail invd ON invd.invoice_id = inv.invoice_id
                LEFT JOIN payment p ON p.invoice_id = inv.invoice_id
            GROUP BY tgl
            ) AS invoice_general ON invoice_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #PENGAJUAN-PURCHASING (KONTRAK PKS)
            LEFT JOIN (
                SELECT DATE_FORMAT(pur.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(pur.entry_date)) AS pengajuan_PKS,
                IFNULL(SUM(CASE WHEN pur.ppn = 1 THEN (pur.price * pur.`quantity`)
                WHEN pur.ppn = 2 THEN (pur.`price` * pur.`quantity`)
                ELSE 1 END),0) AS nilai_PKS,
                COUNT(CASE WHEN pur.status = 0 AND pur.payment_id IS NULL THEN 1 ELSE NULL END) AS pending_PKS,
                IFNULL(SUM(CASE WHEN pur.status = 0 AND pur.payment_id IS NULL AND pur.ppn = 1 THEN (pur.price * pur.`quantity`)
                        WHEN pur.`status` = 0 AND pur.`payment_id`IS NULL AND pur.ppn = 2 THEN (pur.price * pur.quantity)
                ELSE NULL END),0) AS nilai_pending_PKS,
                DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') AS requestDatePKS
                FROM purchasing pur 
                INNER JOIN logbook_new l ON l.`purchasing_Id` = pur.`purchasing_id`
                WHERE pur.company = 1
                GROUP BY tgl
            ) AS purchasing ON purchasing.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #PAYMENT
            LEFT JOIN(
                SELECT DATE_FORMAT(l.`entry_date`, '%Y/%m/%d') AS logbookDate, 
                DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS paymentDate, 
                COUNT(DISTINCT(pay.`entry_date`)) AS countPV,
                IFNULL(SUM(pay.amount),0) AS PVValue
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                WHERE {$wherePayment}
                GROUP BY DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d')
            ) AS PV ON PV.paymentDate = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
            #BATCH UPLOAD
            LEFT JOIN(
                SELECT DATE_FORMAT(buh.entry_date, '%Y/%m/%d') AS tgl, 
                COUNT(bud.batch_code) AS qty_batch_upload,
                IFNULL(SUM(bud.grand_total),0) AS nilai_batch_upload
                FROM batch_upload_detail bud
                LEFT JOIN batch_upload_header buh ON buh.batch_code = bud.batch_code
                INNER JOIN payment pay ON pay.payment_id = bud.payment_id
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
               WHERE {$whereBatch}
                GROUP BY tgl
            ) AS batch ON batch.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            WHERE {$whereP2} {$whereR2}
            GROUP BY DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            ORDER BY logbook.`entry_date` ASC"; 
$result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT); 
// echo "COUNT ". $sql1;

//DATA LOGBOOK
$sql = "SELECT CASE WHEN l.type_pengajuan = 1 THEN 'Curah/OA/OB/Handling' 
                    WHEN l.type_pengajuan = 2 THEN 'General' ELSE 'PKS KONTRAK' END AS typePengajuan,
        CASE WHEN l.type_pengajuan = '1' THEN 
                CASE WHEN pp.payment_method = '1' OR l.type_pengajuan = '3' THEN 'Payment'
                     WHEN pp.payment_method = '2' THEN 'Down Payment'
                ELSE NULL END
            WHEN l.type_pengajuan = '2' OR l.type_pengajuan = '3' THEN 'payment'
        ELSE NULL END AS method,   
        CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.entry_date, '%d/%m/%Y')
             WHEN l.type_pengajuan = '2' THEN DATE_FORMAT(d.tgl, '%d/%m/%Y')  ELSE DATE_FORMAT(pur.entry_date, '%d/%m/%Y')  END AS tanggal_pengajuan,
        CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.email_date, '%d/%m/%Y') 
            WHEN l.type_pengajuan = '2' THEN DATE_FORMAT(d.pengajuan_email_date, '%d/%m/%Y')  ELSE NULL END AS tglEmail,     
        sp.stockpile_name, 
        us.user_name as PICPengajuan,
        CASE WHEN l.type_pengajuan = '1' THEN 
				CASE WHEN pp.urgent_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END 
			WHEN l.type_pengajuan = '2' THEN 
				CASE WHEN d.payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END
		ELSE 'NORMAL' END AS urgentType,

        CASE WHEN l.type_pengajuan = '1' 
                THEN CASE WHEN pp.vendor_id IS NOT NULL THEN v.vendor_name 
                          WHEN pp.freight_id IS NOT NULL THEN f.freight_supplier 
                          WHEN pp.labor_id IS NOT NULL THEN lb.labor_name 
                          WHEN pp.vendor_handling_id IS NOT NULL THEN vh.vendor_handling_name ELSE NULL END
             WHEN l.type_pengajuan = '2' THEN d.general_vendor_name 
             WHEN l.type_pengajuan = '3' THEN v.vendor_name 
        ELSE NULL END AS vendorName,
        CASE WHEN l.type_pengajuan = '1' THEN pp.remarks
             WHEN l.type_pengajuan = '2' THEN d.remarks ELSE NULL END AS remarks,
        CASE WHEN l.type_pengajuan = '1' THEN pp.amount
             WHEN l.type_pengajuan = '2' THEN ((d.amount + d.ppn1) - d.pph1 )
             WHEN l.type_pengajuan = '3'
                    THEN CASE WHEN pur.ppn = 1 THEN (pur.price * pur.quantity)
                              WHEN pur.ppn = 2 THEN (pur.price * pur.quantity)
                    ELSE NULL END
        ELSE NULL END AS amount, 
        CASE WHEN l.type_pengajuan = '1' THEN  ino.inv_notim_no
             WHEN l.type_pengajuan = '2' THEN inv.invoice_no ELSE NULL END AS invoiceNo2,
        p.payment_no, 
        CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.urgent_payment_date, '%d-%m-%Y') 
			WHEN l.type_pengajuan = '2' AND d.request_payment_date IS NOT NULL THEN DATE_FORMAT(d.request_payment_date, '%d-%m-%Y') 
			WHEN l.type_pengajuan = '2' AND d.request_payment_date IS NULL THEN DATE_FORMAT(d.request_date, '%d-%m-%Y') 
            WHEN l.type_pengajuan = '3' THEN DATE_FORMAT(pur.plan_payment_date, '%d-%m-%Y') 
			ELSE '-' END as urgentDate, 
        (SELECT user_name FROM `user` WHERE user_id = p.entry_by) AS PICFinance, 
        bud.kode3, 
        DATE_FORMAT(buh.batch_date, '%d/%m/%Y') as batchDate,
        (SELECT user_name FROM USER WHERE user_id = buh.entry_by) AS PICPaid, 
        bud.grand_total, 
        bud.batch_code,
        CASE WHEN buh.entry_date IS NULL THEN 
        CASE WHEN l.type_pengajuan = '1' THEN (SELECT TIMESTAMPDIFF(DAY,pp.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y'))) 
             WHEN l.type_pengajuan = '2' THEN (SELECT TIMESTAMPDIFF(DAY,d.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y')))
             WHEN l.type_pengajuan = '3' THEN (SELECT TIMESTAMPDIFF(DAY,pur.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y')))
             ELSE 0 END 
        ELSE NULL END AS AgingPending,
        CASE WHEN (pp.inv_notim_id IS NULL AND bud.batch_code IS NULL AND l.status1 !=2 AND pp.entry_date IS NOT NULL) 
            OR (d.status_pengajuan = 0 && d.status_pengajuan != 5) OR (l.status1 = 0) THEN 'Pengajuan' 
             WHEN ((l.payment_id IS NOT NULL OR l.inv_notim_id IS NOT NULL OR l.invoice_id IS NOT NULL)AND bud.batch_code IS NULL AND l.status1 !=2) THEN 'On Process' 
             WHEN bud.batch_code IS NOT NULL AND l.payment_id IS NOT NULL AND l.status1 !=2 THEN 'Paid' 
             WHEN l.status1 = 2  THEN 'Cancel' 
			 WHEN l.status1 = 4  THEN 'Reject' ELSE NULL END AS statusLogbook  #and d.status_pengajuan = 5
        FROM logbook_new l
        LEFT JOIN pengajuan_payment pp ON pp.idPP = l.ppayment_id 
        LEFT JOIN purchasing pur ON pur.purchasing_id = l.purchasing_Id and pur.company = 1
        #PENGAJUAN  (GENERAL)
        LEFT JOIN (
            SELECT pg.entry_date AS tgl,
                IFNULL(SUM(pgd.amount - (pgd.amount * (pgd.termin/100))),0) AS amount,
				IFNULL(SUM(pgd.pph - (pgd.pph * (pgd.termin/100))),0) AS pph1,
				IFNULL(SUM(pgd.ppn - (pgd.ppn * (pgd.termin/100))),0) AS ppn1,
                pg.`pengajuan_general_id`,
                pg.pengajuan_email_date,
                pg.stockpileId,
                pg.entry_by,
                pg.entry_date,
                gv.general_vendor_name,
                pg.remarks,
                pg.status_pengajuan,
				pg.request_payment_date,
				pg.payment_type,
				pg.request_date
            FROM pengajuan_general pg 
            LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id	
            LEFT JOIN general_vendor gv ON gv.general_vendor_id = pgd.general_vendor_id
            GROUP BY pgd.pg_id 
        ) AS d ON d.pengajuan_general_id = l.`pinvoice_id`
        #END
        LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id OR sp.stockpile_id = pur.stockpile_id OR d.stockpileId = sp.stockpile_id
        LEFT JOIN USER us ON us.user_id = pp.user OR us.user_id = pur.entry_by OR us.user_id = d.entry_by
        LEFT JOIN vendor v ON v.vendor_id = pp.vendor_id OR v.vendor_id = pur.vendor_id
        LEFT JOIN freight f ON f.freight_id = pp.freight_id 
        LEFT JOIN labor lb ON lb.labor_id = pp.labor_id 
        LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id
        LEFT JOIN invoice inv ON inv.invoice_id = l.invoice_id
        LEFT JOIN invoice_notim ino ON ino.inv_notim_id = l.inv_notim_id
        LEFT JOIN payment p ON p.payment_id = l.payment_id 
        LEFT JOIN batch_upload_detail bud ON bud.payment_id = l.payment_id 
        LEFT JOIN batch_upload_header buh ON buh.batch_code = bud.batch_code 
        WHERE {$whereProperty} {$whereRequest}
        ORDER BY logbook_id DESC"; 
  //echo " |-----| DATA " .$sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


$fileName = "Logbook Data "  . str_replace(" ", "-", $_SESSION['userName']) . " " . date("Ymd-His") . ".xls";
$onSheet = 0;
$lastColumn = "M";

// <editor-fold defaultstate="collapsed" desc="Create Excel and Define Header">
$objPHPExcel = new PHPExcel();
PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

$objPHPExcel->setActiveSheetIndex($onSheet);
$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(75);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$rowActive = 1; //row pertama -> selanjutnya akan increment ke bawah->
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray1);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "Print Date: " . date("d F Y"));

// if ($spName != "") {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Periode Awal = {$periodFrom}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Periode Akhir = {$periodTo}");
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Request Date = {$requestDate}");
    $rowActive++;
	$rowActive++;
// }
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("A{$rowActive}:{$lastColumn}{$rowActive}");
$objPHPExcel->getActiveSheet()->getStyle("A{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray2);
$objPHPExcel->getActiveSheet()->getRowDimension("{$rowActive}")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A{$rowActive}", "LOGBOOK DATA");

//LOGBOOK
$rowActive++;
$rowMerge = $rowActive+1;
$headerRow = $rowActive;
$objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:B{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No.");
$objPHPExcel->getActiveSheet()->mergeCells("C{$rowActive}:C{$rowMerge}");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Tanggal");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$rowActive++;
$objPHPExcel->getActiveSheet()->mergeCells("D{$rowActive}:E{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Pengajuan submit");
$objPHPExcel->getActiveSheet()->mergeCells("F{$rowActive}:G{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Pengajuan Reject");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "On Process");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Pending");
$objPHPExcel->getActiveSheet()->mergeCells("L{$rowActive}:M{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Paid");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);
$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Qty");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Amount");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray9);

$jumlalhKolom = 0;
$jumlahKolom = $result1->num_rows; 
if($jumlahKolom == 0){
    $rowActive++;
    $objPHPExcel->getActiveSheet()->mergeCells("B{$rowActive}:M{$rowActive}");
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Data Kosong");
    $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray12);
}

$no = 1;
$tempqty = 0;
$tempNilai = 0;
$tempQty1 = 0;
$tempNilai1 = 0;
$tempQty2 = 0;
$tempNilai2 = 0;
$tempQty3 = 0;
$tempNilai3 = 0;
$totalQtyPending = 0;
$TotalNilaiPending = 0;


$pengajuan = 0;
$nilaiPengajuan = 0;
$onProcess = 0;
$nilaiProcess = 0;
$tempReject = 0;
$tempNilaiReject = 0;
while($row1 = $result1->fetch_object()) {
    $tempQtyPending = 0;
    $tempNilaiPending = 0;


          //PENDING PKS + GENERAL + PURCHASING
        $tempQtyPending = $row1->pending_OA + $row1->pending_General + $row1->pending_PKS;
        $tempNilaiPending = $row1->nilai_pending_OA + $row1->nilai_pending_General + $row1->nilai_pending_PKS;
       

        //PENGAJUAN PKS + GENERAL + PURCHASING
        $pengajuan = $row1->qty_pengajuanOA + $row1->qty_pGeneral + $row1->pengajuan_PKS;
        $nilaiPengajuan = $row1->nilai_pengajuanOA + $row1->nilai_pGeneral + $row1->nilai_PKS;
        $onProcess = $row1->countPV + $row1->qty_processGeneral;
        $nilaiProcess = $row1->PVValue + $row1->nilai_processGeneral;

        //PENGAJUAN REJECT PKS + GENERAL
        $tempReject = $row1->RejectOA + $row1->RejectGeneral;
        $tempNilaiReject = $row1->Reject_nilaiOA + $row1->Reject_nilaiGeneral;
   
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row1->tgl);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $nilaiPengajuan); 
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $tempReject);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $tempNilaiReject);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $onProcess); 
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $nilaiProcess);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $tempQtyPending); 
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $tempNilaiPending);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row1->qty_batch_upload); 
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row1->nilai_batch_upload); 
    
    $tempqty = $tempqty + $pengajuan;
    $tempNilai = $tempNilai + $nilaiPengajuan;
    $tempQty1 = $tempQty1 + $onProcess;
    $tempNilai1 = $tempNilai1 + $nilaiProcess;
    $tempQty2 = $tempQty2 + $row1->qty_batch_upload;
    $tempNilai2 = $tempNilai2 + $row1->nilai_batch_upload;
    $tempQty3 = $tempQty3 + $tempReject;
    $tempNilai3 = $tempNilai3 + $tempNilaiReject;
    $totalQtyPending = $totalQtyPending + $tempQtyPending;
    $TotalNilaiPending = $TotalNilaiPending + $tempNilaiPending;
    $no++;
}

$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Total : ");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $tempqty);
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $tempNilai); 
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $tempQty3);
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $tempNilai3);
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $tempQty1);
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $tempNilai1);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $totalQtyPending);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $TotalNilaiPending);
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $tempQty2);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $tempNilai2);
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
$bodyRowEnd = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");

//DATA LOGBOOK
$lastColumn1 = "W";
$rowActive++;
$rowActive++;
$headerRow1 = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Status Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Type Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Pengajuan System");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Pengajuan Email");

$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Request Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "PIC Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Keterangan Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Nilai");

$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "No. Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "No. PV");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "PIC Finance");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "Remaks E-Banking");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Tanggal Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Jumlah Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "PIC Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "Batch");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "Aging");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Catatan");
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray9);

$no1 = 1;
$totalNilai = 0;
$tempGrandTotal = 0;
$tempJmlSystem = 0;
$tempJmlEmail = 0;
$tempjmlInvoiceNo = 0;
$tempjmlPV = 0;
$tempremaksEbangking = 0;
$GrandTotalPengajuan = 0;
$GrandTotalPembayaran = 0;
while($row = $resultData->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no1);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->urgentType);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->statusLogbook);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->typePengajuan); 
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->tanggal_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->tglEmail); 

    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->urgentDate);
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->PICPengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->vendorName);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->remarks);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->amount);
    
    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->invoiceNo2);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->payment_no);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->PICFinance);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->kode3);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->batchDate);
    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->grand_total);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->PICPaid);
	$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->batch_code);
	$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->AgingPending);
	$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Remaks");
    $no1++;

    $GrandTotalPengajuan = $GrandTotalPengajuan + $row->amount;
    $GrandTotalPembayaran = $GrandTotalPembayaran + $row->grand_total;
}


$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Grand Total : ");
 $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $GrandTotalPengajuan);
 $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $GrandTotalPembayaran);
 $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray8);


$bodyRowEnd1 = $rowActive;
for ($temp = ord("B"); $temp <= ord("W"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table
	
	$objPHPExcel->getActiveSheet()->getStyle("E" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
	
	$objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 

	$objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 


    $objPHPExcel->getActiveSheet()->getStyle("M" . ($headerRow1 + 1) . ":{$lastColumn1}{$bodyRowEnd1}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow1) . ":{$lastColumn1}{$bodyRowEnd1}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table
    $objPHPExcel->getActiveSheet()->getStyle("F" . ($headerRow1 + 1) . ":F{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow1 + 1) . ":G{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow1 + 1) . ":H{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("R" . ($headerRow1 + 1) . ":R{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");



ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();