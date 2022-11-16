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
$whereProperty2 = '';
$currentDate = '';
$date = '';
$whereRequest = '';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');


if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && isset($_POST['requestDate']) && $_POST['requestDate'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $requestDate = $_POST['requestDate'];

    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereRequest .= " OR ((DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                       ." OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                       ." OR (DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') = '{$requestDate}'))";

    // $whereProperty2 .= "DATE_FORMAT(a.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && $_POST['requestDate'] == '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";

}else if($_POST['periodFrom'] == '' && $_POST['periodTo'] == '' && isset($_POST['requestDate']) && $_POST['requestDate'] != ''){
	$requestDate = $_POST['requestDate'];
    $whereRequest .= " (DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                        . "OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                        . "OR (DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') = '{$requestDate}') ";


    //Get auto Entry-Date untuk logbook Summary nya
    $sqlFirst = "SELECT DATE_FORMAT(entry_date, '%Y/%m/%d')  AS first_row FROM logbook_new l WHERE DATE_FORMAT(l.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}' ORDER BY l.entry_date ASC LIMIT 1";
    $resultFirst = $myDatabase->query($sqlFirst, MYSQLI_STORE_RESULT);
    if ($resultFirst !== false && $resultFirst->num_rows == 1) {
        $rowFirst = $resultFirst->fetch_object();
        $periodFrom = $rowFirst->first_row;
    }

    $sqlLast = "SELECT DATE_FORMAT(entry_date, '%Y/%m/%d')  AS last_row FROM logbook_new l WHERE DATE_FORMAT(l.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}' ORDER BY l.entry_date DESC LIMIT 1";
    $resultLast = $myDatabase->query($sqlLast, MYSQLI_STORE_RESULT);
    if ($resultLast !== false && $resultLast->num_rows == 1) {
        $rowLast = $resultLast->fetch_object();
        $periodTo = $rowLast->last_row;
    }

}

// else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
//     $periodFrom = $_POST['periodFrom'];
//     $whereProperty .= " DATE_FORMAT(l.entry_date, '%Y/%m/%d') <= '{$periodFrom}'";
//     $whereProperty2 .= " DATE_FORMAT(a.entry_date, '%Y/%m/%d') <= '{$periodFrom}'";
// } else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
//     $periodTo = $_POST['periodTo'];
//     $whereProperty .= " DATE_FORMAT(l.entry_date, '%Y/%m/%d') <= '{$periodTo}'";
//     $whereProperty2 .= " DATE_FORMAT(a.entry_date, '%Y/%m/%d') <= '{$periodTo}'";
// }

//COUNT LOGBOOK
$sql1 = "SELECT DATE_FORMAT(logbook.entry_date, '%d/%m/%Y') AS tgl,
        -- INTERNAL TF
        internalTf.qty_internalTf,
        internalTf.nilai_internalTf,

        -- OA/OB/HANDLING
        ppayment_notim.qty_pNotim,
        ppayment_notim.nilai_pNotim,

        -- INVOICE (OB/OA/HANDLING)
        approve_invNotim.qty_invNotim,
        approve_invNotim.nilai_invNotim,

        -- PENGAJUAN GENERAL
        pgeneral.qty_pGeneral,
        pgeneral.nilai_pGeneral,

        -- INVOICE (GENERAL)
        invoice_general.qty_inv_general,
        invoice_general.nilai_inv_general,

        -- KONTRAK PKS
        purchasing_1.pengajuan_purchasing,
        purchasing_1.nilai_purchasing,

        -- ALL PENDING
        allPending.pending,
        allPending.nilai_pending,

        -- ALL REJECT
        reject.qtyReject,
        reject.nilaiReject,
            
        -- PAYMENT
        payment.countPayment,
        payment.PaymentValue,

        -- BATCH UPLOAD
        batch.qty_paid,
        batch.nilai_paid
        --	paidBatchUpload.qty_batchUpload,
        --	paidBatchUpload.amountBU
        FROM logbook_new logbook

        -- INTERNAL TRANSFER
        LEFT JOIN (	
            SELECT DATE_FORMAT(itf.entry_date, '%Y/%m/%d') AS tgl,
            COUNT(DISTINCT(pengajuan_interalTF_id)) AS qty_internalTf,
            IFNULL(SUM(amount),0) AS nilai_internalTf
            FROM pengajuan_internaltf itf
            INNER JOIN logbook_new l ON l.internalTf_id = itf.pengajuan_interalTF_id
            GROUP BY tgl
        ) AS internalTf ON internalTf.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- PENGAJUAN (OA/OB/HANDLING)
        LEFT JOIN (	
            SELECT DATE_FORMAT(pp.entry_date, '%Y/%m/%d') AS tgl,
            COUNT(DISTINCT(idpp)) AS qty_pNotim,
            IFNULL(SUM(grand_total),0) AS nilai_pNotim
            -- COUNT(CASE WHEN STATUS = '2' OR STATUS = '4' THEN 1 ELSE NULL END ) AS ppReject, 
            -- IFNULL(SUM(CASE WHEN STATUS = '2' OR STATUS = '4' THEN amount ELSE NULL END),0) AS ppReject_nilai
            FROM pengajuan_payment pp
            INNER JOIN logbook_new l ON l.ppayment_id = pp.idpp
            WHERE grand_total > 0
            GROUP BY tgl
        ) AS ppayment_notim ON ppayment_notim.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- APPROVE INVOICE (OA/OB/HANDLING)
        LEFT JOIN(
        SELECT a.tgl, COUNT(DISTINCT(a.invId)) AS qty_invNotim, SUM(a.amount) AS nilai_invNotim
        FROM(
            SELECT 2 AS inc, 'Invoice' AS kategori, DATE_FORMAT(ino.entry_date, '%Y/%m/%d') AS tgl, 
            ino.inv_notim_id AS invId, SUM(ino.amount) AS amount 
                FROM invoice_notim ino
                LEFT JOIN pengajuan_payment pp ON pp.idpp = ino.idpp
                INNER JOIN logbook_new logbook ON logbook.`inv_notim_id` = ino.inv_notim_id 
                WHERE logbook.`status1` != 2 
                    AND NOT EXISTS 
                    (SELECT l.`ppayment_id` FROM payment pay
                        INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                        WHERE l.type_pengajuan = 1 AND l.status1 != 2 AND l.`ppayment_id` = pp.`idPP` 
                        AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(ino.entry_date, '%d/%m/%Y' AND ino.amount > 0) 
                        ) AND ino.amount > 0
                GROUP BY DATE_FORMAT(ino.entry_date, '%d/%m/%Y'), invId
            )AS a WHERE tgl BETWEEN  '{$periodFrom}' AND '{$periodTo}' 
            GROUP BY tgl
            ORDER BY tgl
        ) AS approve_invNotim ON approve_invNotim.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- PENGAJUAN  (GENERAL)
        LEFT JOIN (
            SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
            COUNT(DISTINCT(pg.pengajuan_general_id)) AS qty_pGeneral, #distinct dihilangin sementara
            IFNULL(SUM(pg.total_amount),0) AS nilai_pGeneral
            FROM pengajuan_general pg 
            INNER JOIN logbook_new l ON l.pgeneral_id = pg.pengajuan_general_id 
            -- LEFT JOIN payment pay1 ON pay1.invoice_id = pg.invoice_id
            GROUP BY tgl
        ) AS pgeneral ON pgeneral.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- REJECT GENERAL
        LEFT JOIN (
            SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(pg.`pengajuan_general_id`)) AS Reject1, 
                IFNULL(SUM(pg.total_amount),0) AS Reject_nilai1
                FROM pengajuan_general pg 
                WHERE pg.status_pengajuan = 2
            -- LEFT JOIN payment pay1 ON pay1.invoice_id = pg.invoice_id
            GROUP BY tgl
        )AS reject_general ON reject_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            

        -- APPROVE INVOICE (GENERAL)
        LEFT JOIN(
        SELECT b.tgl, COUNT(DISTINCT(b.invId)) AS qty_inv_general, SUM(b.amount) AS nilai_inv_general
        FROM(
            SELECT 3 AS inc, 'InvoiceG' AS kategori, DATE_FORMAT(inv.entry_date, '%Y/%m/%d') AS tgl, 
            inv.invoice_id AS invId, SUM(inv.total_amount) AS amount FROM invoice inv
            LEFT JOIN pengajuan_general pg ON pg.invoice_id = inv.invoice_id
                INNER JOIN logbook_new logbook ON logbook.`inv_general_id` = inv.invoice_id 
                WHERE logbook.`status1` != 2 AND NOT EXISTS (SELECT l.`pgeneral_id` FROM payment pay
                        INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                        WHERE l.type_pengajuan = 2 AND l.status1 != 2 AND l.`pgeneral_id` = pg.`pengajuan_general_id` 
                        AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(inv.entry_date, '%d/%m/%Y') 
                        )
                GROUP BY DATE_FORMAT(inv.entry_date, '%d/%m/%Y'), invId
            )AS b WHERE tgl BETWEEN  '{$periodFrom}' AND '{$periodTo}' 
            GROUP BY tgl
            ORDER BY tgl
        ) AS invoice_general ON invoice_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- PENGAJUAN-PURCHASING (KONTRAK PKS)
        LEFT JOIN (
            SELECT DATE_FORMAT(pur.entry_date, '%Y/%m/%d') AS tgl,
            COUNT(DISTINCT(pur.entry_date)) AS pengajuan_purchasing,
            IFNULL(SUM(CASE WHEN pur.ppn = 1 THEN (pur.price * pur.`quantity`)
                    WHEN pur.ppn = 2 THEN (pur.`price` * pur.`quantity`) + ((pur.`price` * pur.`quantity`)*(11/100))
            ELSE 1 END),0) AS nilai_purchasing
            FROM purchasing pur 
            INNER JOIN logbook_new l ON l.`purchasing_Id` = pur.`purchasing_id`
            WHERE pur.company = 1
            GROUP BY tgl
        ) AS purchasing_1 ON purchasing_1.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- ALL PENDING
        LEFT JOIN (
            SELECT pending_date, COUNT(DISTINCT(idpp)) AS pending, SUM(amount) AS nilai_pending
            FROM (
                -- PENGAJUAN NOTIM
                SELECT 1 AS inc ,'pengajuan' AS kategori, DATE_FORMAT(pp1.entry_date, '%Y/%m/%d') AS pending_date, pp1.idpp, SUM(pp1.grand_total) AS amount FROM pengajuan_payment AS pp1
                INNER JOIN logbook_new logbook ON logbook.`ppayment_id` = pp1.idpp AND logbook.status1 != 2
                WHERE pp1.grand_total > 0 AND
                    NOT EXISTS (SELECT ino.idpp FROM invoice_notim ino WHERE pp1.`idPP` = ino.`idPP` AND DATE_FORMAT(pp1.entry_date, '%d/%m/%Y')  = DATE_FORMAT(ino.entry_date, '%d/%m/%Y') AND ino.invoice_status != 2)
                    AND NOT EXISTS (SELECT l.`ppayment_id` FROM payment pay
                                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                                WHERE pp1.grand_total > 0 AND l.type_pengajuan = 1 AND l.status1 != 2 AND l.`ppayment_id` = pp1.`idPP` AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(pp1.entry_date, '%d/%m/%Y'))

                GROUP BY pending_date, idpp
                UNION ALL 
                SELECT 2 AS inc, 'Invoice' AS kategori, DATE_FORMAT(ino.entry_date, '%Y/%m/%d') AS pending_date, ino.idpp, SUM(pp.grand_total) AS amount FROM invoice_notim ino
                LEFT JOIN pengajuan_payment pp ON pp.idpp = ino.idpp
                INNER JOIN logbook_new logbook ON logbook.`inv_notim_id` = ino.inv_notim_id 
                WHERE ino.amount > 0 AND logbook.`status1` != 2 AND NOT EXISTS (SELECT l.`ppayment_id` FROM payment pay
                        INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                        WHERE ino.amount > 0 AND l.type_pengajuan = 1 AND l.status1 != 2 AND l.`ppayment_id` = pp.`idPP` 
                        AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(ino.entry_date, '%d/%m/%Y') 
                        )
                GROUP BY DATE_FORMAT(ino.entry_date, '%d/%m/%Y'), idpp
                UNION ALL 
                SELECT 3 AS inc, 'payment' AS kategori, DATE_FORMAT(pay.entry_date, '%Y/%m/%d') AS pending_date, l.ppayment_id AS idpp, SUM(pay.amount2) AS amount
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                LEFT JOIN pengajuan_payment pp ON pp.idpp = l.ppayment_id
                WHERE l.type_pengajuan = 1 AND l.status1 != 2 AND pay.amount2 > 0
                GROUP BY DATE_FORMAT(pay.entry_date, '%d/%m/%Y'), idpp
                
                -- PENGAJUAN GENERAL
                UNION ALL
                SELECT 4 AS inc ,'pengajuan_Gen' AS kategori, DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS pending_date, pg.pengajuan_general_id AS idpp, SUM(pg.total_amount) AS amount 
                FROM pengajuan_general AS pg
                INNER JOIN logbook_new logbook ON logbook.pgeneral_id = pg.pengajuan_general_id
                WHERE NOT EXISTS (SELECT pg1.pengajuan_general_id FROM invoice inv 
                        LEFT JOIN pengajuan_general pg1 ON pg1.invoice_id = inv.`invoice_id`
                        INNER JOIN logbook_new l ON l.inv_general_id = inv.invoice_id 
                        WHERE pg.`invoice_id` = inv.`invoice_id` AND l.status1 != 2 AND l.type_pengajuan = 2
                        AND DATE_FORMAT(pg.entry_date, '%d/%m/%Y')  = DATE_FORMAT(inv.entry_date, '%d/%m/%Y')
                        )
                AND NOT EXISTS (SELECT l.`pgeneral_id` FROM payment pay
                        INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                        WHERE  l.`pgeneral_id` = pg.`pengajuan_general_id` AND l.type_pengajuan = 2 AND l.status1 != 2
                        AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(pg.entry_date, '%d/%m/%Y')
                        )
                AND logbook.`status1` != 2 AND logbook.`type_pengajuan` = 2
                GROUP BY pending_date, idpp
                    
                -- INV GENERAL
                UNION ALL 
                SELECT 5 AS inc, 'Invoice_Gen' AS kategori, DATE_FORMAT(inv.entry_date, '%Y/%m/%d') AS pending_date, pg.pengajuan_general_id AS idpp, SUM(inv.total_amount) AS amount 
                FROM invoice inv
                LEFT JOIN pengajuan_general pg ON pg.`invoice_id` = inv.`invoice_id`
                INNER JOIN logbook_new logbook ON logbook.inv_general_id = inv.invoice_id
                WHERE NOT EXISTS (SELECT l.`pgeneral_id` FROM payment pay
                        INNER JOIN logbook_new l ON l.payment_id = pay.payment_id 
                        WHERE l.`pgeneral_id` = pg.`pengajuan_general_id` AND l.`type_pengajuan` = 2
                        AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(inv.entry_date, '%d/%m/%Y')
                        )
                AND logbook.`status1` != 2 AND logbook.`type_pengajuan` = 2
                GROUP BY DATE_FORMAT(inv.entry_date, '%d/%m/%Y'), idpp
            
                -- PAYMENT GENERAL
                UNION ALL 
                SELECT 3 AS inc, 'payment_Gen' AS kategori, DATE_FORMAT(pay.entry_date, '%Y/%m/%d') AS pending_date, l.pgeneral_id AS idpp, SUM(pay.amount2) AS amount
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                WHERE l.type_pengajuan = 2 AND l.status1 != 4
                GROUP BY DATE_FORMAT(pay.entry_date, '%d/%m/%Y'), idpp
                
                -- PURCHASING
                UNION ALL
                SELECT 7 AS inc,
                    'pengajuan_pur' AS kategori, 
                    DATE_FORMAT(pur.entry_date, '%Y/%m/%d') AS pending_date, 
                    pur.purchasing_id AS idpp, 
                    CASE WHEN pur.ppn = 1 THEN (pur.price * pur.quantity)
                        WHEN pur.ppn = 2 THEN  (pur.price * pur.quantity) + ((pur.`price` * pur.`quantity`)*(11/100))
                    ELSE 0 END AS amount 
                FROM purchasing AS pur
                INNER JOIN logbook_new lb ON lb.`purchasing_Id` = pur.purchasing_Id
                WHERE  NOT EXISTS (SELECT l.`purchasing_id` FROM payment pay
                            INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                            WHERE l.type_pengajuan = 3  AND l.`purchasing_id`= pur.`purchasing_id` 
                            AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(pur.entry_date, '%d/%m/%Y')
                        )
                AND lb.status1 NOT IN (2,4) AND lb.type_pengajuan = 3
                UNION ALL
                SELECT 8 AS inc, 'payment_pur' AS kategori, DATE_FORMAT(pay.entry_date, '%Y/%m/%d') AS pending_date, l.purchasing_id AS idpp, SUM(pay.amount2) AS amount
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                WHERE l.type_pengajuan = 3 AND l.status1 NOT IN (2,4)
                GROUP BY DATE_FORMAT(pay.entry_date, '%d/%m/%Y'), idpp
                
                -- INTERNAL TRANSFER
                UNION ALL
                SELECT 9 AS inc,
                    'internal_transfer' AS kategori, 
                    DATE_FORMAT(itf.entry_date, '%Y/%m/%d') AS pending_date, 
                    itf.pengajuan_interalTF_id AS idpp, 
                    SUM(itf.amount) AS amount
                FROM pengajuan_internaltf AS itf
                INNER JOIN logbook_new lb ON lb.`internalTf_id` = itf.pengajuan_interalTF_id
                WHERE  NOT EXISTS (SELECT l.`internalTf_id` FROM payment pay
                            INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                            WHERE l.type_pengajuan = 6  AND l.`internalTf_id`= itf.`pengajuan_interalTF_id` 
                            AND DATE_FORMAT(pay.entry_date, '%d/%m/%Y') = DATE_FORMAT(itf.entry_date, '%d/%m/%Y')
                        )
                AND lb.status1 != 2 AND lb.type_pengajuan = 6
                UNION ALL
                SELECT 10 AS inc, 'payment_InternalTF' AS kategori, DATE_FORMAT(pay.entry_date, '%Y/%m/%d') AS pending_date, l.internalTf_id AS idpp, SUM(pay.amount2) AS amount
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                WHERE l.type_pengajuan = 6 AND l.status1 != 2
                GROUP BY DATE_FORMAT(pay.entry_date, '%d/%m/%Y'), idpp
            ) AS a WHERE pending_date BETWEEN  '{$periodFrom}' AND '{$periodTo}' 
            GROUP BY pending_date
            ORDER BY pending_date
        )AS allPending ON allPending.pending_date = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- ALL REJECT
        LEFT JOIN (
            SELECT SUM(a.qty_reject) AS qtyReject, SUM(a.nilai_reject) AS nilaiReject, a.tgl AS tgl1 FROM 
                (
                -- R_PGENERAL
                SELECT DATE_FORMAT(l.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(l.`pgeneral_id`)) AS qty_reject, 
                IFNULL(SUM(pg.total_amount),0) AS nilai_reject
                FROM logbook_new l
                LEFT JOIN pengajuan_general pg ON pg.pengajuan_general_id = l.pgeneral_id
                WHERE l.status1 = 2 AND l.`type_pengajuan` = 2  
                GROUP BY tgl 
                -- R_PNOTIM
                UNION ALL
                SELECT DATE_FORMAT(l.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(l.`ppayment_id`)) AS qty_reject, 
                IFNULL(SUM(pp.grand_total),0) AS nilai_reject
                FROM logbook_new l
                LEFT JOIN pengajuan_payment pp ON pp.idpp = l.ppayment_id
                WHERE l.status1 = 2 AND l.`type_pengajuan` = 1  AND pp.grand_total > 0
                GROUP BY tgl 
                -- R_PURCHASING
                UNION ALL
                SELECT DATE_FORMAT(l.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(l.`purchasing_Id`)) AS qty_reject, 
                IFNULL(SUM(CASE WHEN pur.ppn = 1 THEN (pur.price * pur.`quantity`)
                            WHEN pur.ppn = 2 THEN (pur.`price` * pur.`quantity`) + ((pur.`price` * pur.`quantity`)*(11/100))
                ELSE 1 END),0) AS nilai_reject
                FROM logbook_new l
                LEFT JOIN purchasing pur ON pur.purchasing_Id = l.purchasing_Id
                WHERE l.status1 = 2 AND l.`type_pengajuan` = 3 
                GROUP BY tgl 
                -- R_INTERNAL_TRANSFER
                UNION ALL
                SELECT DATE_FORMAT(l.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(l.`internalTf_id`)) AS qty_reject, 
                IFNULL(SUM(itf.amount),0) AS nilai_reject
                FROM logbook_new l
                LEFT JOIN pengajuan_internaltf itf ON itf.pengajuan_interalTF_id = l.internalTf_id
                WHERE l.status1 = 2 AND l.`type_pengajuan` = 6
                GROUP BY tgl 
            ) AS a WHERE a.tgl BETWEEN '{$periodFrom}' AND '{$periodTo}' 
            GROUP BY a.tgl
        ) AS reject ON reject.tgl1 = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')

        -- PAYMENT
        LEFT JOIN(
            SELECT DATE_FORMAT(l.`entry_date`, '%Y/%m/%d') AS logbookDate, 
            DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS paymentDate, 
            COUNT(DISTINCT(pay.`payment_id`)) AS countPayment,
            IFNULL(SUM(pay.amount2),0) AS PaymentValue
            FROM payment pay
            -- INNER JOIN purchasing pur ON pur.`payment_id` = pay.`payment_id`
            INNER JOIN logbook_new l ON l.payment_id = pay.payment_id AND l.status1 != 2
            LEFT JOIN pengajuan_payment pp ON pp.idpp = l.ppayment_id 
            WHERE  pay.amount2 > 0 AND DATE_FORMAT(pay.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}' AND pay.payment_status = 0
            GROUP BY DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d')
        ) AS payment ON payment.paymentDate = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            
        -- BATCH UPLOAD
        LEFT JOIN(
            SELECT DATE_FORMAT(buh.entry_date, '%Y/%m/%d') AS tgl,
            COUNT(CASE WHEN bud.status <> 2 THEN 1 ELSE NULL END ) AS qty_paid,
            IFNULL(SUM(CASE WHEN bud.status <> 2 THEN bud.grand_total ELSE 0 END),0) AS nilai_paid
            FROM batch_upload_detail bud
            LEFT JOIN batch_upload_header buh ON buh.batch_code = bud.batch_code
            LEFT JOIN payment pay ON pay.payment_id = bud.payment_id
            INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
            WHERE pay.amount2 > 0 AND DATE_FORMAT(buh.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}' 
            GROUP BY tgl
        ) AS batch ON batch.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
        WHERE DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}' 
        -- DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN DATE_FORMAT('{$periodFrom}', '%Y/%m/%d') AND '{$periodTo}'
        GROUP BY DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
        ORDER BY logbook.`entry_date` ASC ";
    $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT); 
 
    // echo $sql1 ;
    // die();
//DATA LOGBOOK
$sql = "SELECT * FROM 
        (
            SELECT 
            CASE WHEN l.type_pengajuan = 1 THEN l.ppayment_id
                WHEN l.type_pengajuan = 2 THEN l.pgeneral_id
                WHEN l.type_pengajuan = 3 THEN l.purchasing_Id
                WHEN l.type_pengajuan = 6 THEN l.internalTf_id
            ELSE '-' END AS IdPengajuan,

            CASE WHEN l.type_pengajuan = 1 THEN 'Curah/OA/OB/Handling' 
            WHEN l.type_pengajuan = 2 THEN 'General' 
            WHEN l.type_pengajuan = 6 THEN 'Internal Transfer' 
            ELSE 'PKS KONTRAK' END AS typePengajuan,
            
            CASE WHEN l.type_pengajuan = '1' THEN 
            CASE WHEN pp.payment_method = '1' OR l.type_pengajuan = '3' THEN 'Payment'
            WHEN pp.payment_method = '2' THEN 'Down Payment'
            ELSE NULL END
            WHEN l.type_pengajuan = '2' OR l.type_pengajuan = '3' OR l.type_pengajuan = 6 THEN 'payment'
            ELSE NULL END AS method, 
            
            CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.entry_date, '%d/%m/%Y')
            WHEN l.type_pengajuan = '2' THEN DATE_FORMAT(d.tgl, '%d/%m/%Y')  
            WHEN l.type_pengajuan = '6' THEN DATE_FORMAT(itf.entry_date, '%d/%m/%Y')
            ELSE DATE_FORMAT(pur.entry_date, '%d/%m/%Y')  END AS tanggal_pengajuan,
            CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.email_date, '%d/%m/%Y') 
            WHEN l.type_pengajuan = '2' THEN DATE_FORMAT(d.pengajuan_email_date, '%d/%m/%Y')  ELSE '-' END AS tglEmail,     
            sp.stockpile_name, 
            us.user_name AS PICPengajuan,

            CASE WHEN pp.urgent_payment_type = 1 OR d.payment_type = 1 OR itf.request_payment_type = 1 THEN 'URGENT' ELSE 'NORMAL' END AS urgentType,
            CASE WHEN l.type_pengajuan = '1' THEN pp.vendor_name
                WHEN l.type_pengajuan = '2' THEN d.general_vendor_name 
                WHEN l.type_pengajuan = '3' THEN v.vendor_name 
            ELSE '-' END AS vendorName,
            
            CASE WHEN l.type_pengajuan = '1' THEN pp.remarks
            WHEN l.type_pengajuan = '2' THEN d.remarks
            WHEN l.type_pengajuan = '6' THEN itf.remarks  ELSE '-' END AS remarks,
            
            CASE WHEN l.type_pengajuan = '1' THEN pp.total_dpp
        WHEN l.type_pengajuan = '2' THEN d.amount 
        WHEN l.type_pengajuan = '3' THEN (pur.price * pur.quantity)
        ELSE 0 END AS dpp, 


        CASE WHEN l.type_pengajuan = '1' THEN pp.total_ppn_amount
            WHEN l.type_pengajuan = '2' THEN d.ppn
            WHEN l.type_pengajuan = '3' THEN 
                    CASE WHEN pur.ppn = 1 THEN 0
                            WHEN pur.ppn = 2 THEN (pur.price * pur.quantity) * (11/100)
                    ELSE NULL END
        ELSE NULL END AS ppn, 

        CASE WHEN l.type_pengajuan = '1' THEN pp.total_pph_amount
            WHEN l.type_pengajuan = '2' THEN d.pph
            WHEN l.type_pengajuan = '3' THEN 0
        ELSE NULL END AS pph, 

        CASE WHEN l.type_pengajuan = '1' THEN pp_dp1.pp_downpayment
            WHEN l.type_pengajuan = '2' THEN d.downpayment
        ELSE 0 END AS downpayment, 

        CASE WHEN l.type_pengajuan = '1' THEN pp.grand_total
            WHEN l.type_pengajuan = '2' THEN ((d.amount + d.ppn) - d.pph - d.downpayment) 
            WHEN l.type_pengajuan = '3'
                    THEN CASE WHEN pur.ppn = 1 THEN (pur.price * pur.quantity)
                            WHEN pur.ppn = 2 THEN (pur.price * pur.quantity) + ((pur.price * pur.quantity) * (11/100))
                    ELSE NULL END
            WHEN l.type_pengajuan = '6' THEN (itf.amount)
        ELSE NULL END AS amount, 

            CASE WHEN l.type_pengajuan = '1' THEN  ino.inv_notim_no
            WHEN l.type_pengajuan = '2' THEN inv.invoice_no ELSE '-' END AS invoiceNo2,
            (SELECT payment_no FROM payment WHERE payment_id = l.payment_id AND payment_status = 0) AS payment_no, 
            -- CASE WHEN l.type_pengajuan = 1 THEN DATE_FORMAT(pp.urgent_payment_date, '%d-%m-%Y') 
            --         WHEN l.type_pengajuan = '2' AND d.request_payment_date IS NOT NULL THEN DATE_FORMAT(d.request_payment_date, '%d-%m-%Y') 
            --         WHEN l.type_pengajuan = '2' AND d.request_payment_date IS NULL THEN DATE_FORMAT(d.request_date, '%d-%m-%Y') 
            --     WHEN l.type_pengajuan = 6 THEN DATE_FORMAT(itf.request_payment_date, '%d-%m-%Y') 
            -- ELSE '-' END AS urgentDate,
            DATE_FORMAT(l.urgent_payment_date, '%d-%m-%Y') AS urgentDate,
            (SELECT u.user_name FROM `user` u, payment p WHERE user_id = p.entry_by AND p.payment_id = l.payment_id AND p.payment_status = 0) AS PICFinance, 
            bud.kode3, 
            DATE_FORMAT(buh.batch_date, '%d/%m/%Y') AS batchDate,
            (SELECT user_name FROM USER WHERE user_id = buh.entry_by) AS PICPaid, 
            bud.grand_total, 
            bud.batch_code,
            CASE WHEN buh.entry_date IS NULL THEN 
            CASE WHEN l.type_pengajuan = '1' THEN (SELECT TIMESTAMPDIFF(DAY,pp.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y'))) 
            WHEN l.type_pengajuan = '2' THEN (SELECT TIMESTAMPDIFF(DAY,d.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y')))
            WHEN l.type_pengajuan = '3' THEN (SELECT TIMESTAMPDIFF(DAY,pur.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y')))
            WHEN l.type_pengajuan = '6' THEN (SELECT TIMESTAMPDIFF(DAY,itf.entry_date, STR_TO_DATE('{$currentDate}', '%d/%m/%Y')))
            ELSE 0 END 
            ELSE NULL END AS AgingPending,
            CASE 
            -- WHEN (bud.batch_code IS NULL AND l.status1 !=2 AND pp.entry_date IS NOT NULL) OR (d.status_pengajuan = 0 && d.status_pengajuan != 5) OR (l.status1 = 0) THEN 'Pengajuan' 
            --  WHEN ((l.payment_id IS NOT NULL OR l.inv_notim_id IS NOT NULL OR l.invoice_id IS NOT NULL)AND bud.batch_code IS NULL AND l.status1 !=2) THEN 'On Process' 
            WHEN l.status1 = 0  THEN 'Pengajuan' 
            WHEN l.status1 IN (3,1,4) AND bud.batch_code IS NULL  THEN 'On Process' 
            WHEN bud.batch_code IS NOT NULL AND l.payment_id IS NOT NULL AND l.status1 !=2 THEN 'Paid' 
            WHEN l.status1 = 2  THEN 'Cancel/Return' ELSE NULL END AS statusLogbook,  #and d.status_pengajuan = 5
            l.ppayment_id AS idpp,
            pp.status AS statuspp,
            (SELECT DATE_FORMAT(payment_date, '%d/%m/%Y') FROM payment WHERE payment_id = l.payment_id AND payment_status = 0) AS payment_date,
            DATE_FORMAT(ino.entry_date, '%d/%m/%Y') AS invDate
            FROM logbook_new l
            LEFT JOIN pengajuan_payment pp ON pp.idPP = l.ppayment_id
            LEFT JOIN purchasing pur ON pur.purchasing_id = l.purchasing_Id
            -- PENGAJUAN  (GENERAL)
            LEFT JOIN (
                SELECT pg.entry_date AS tgl,
            /*  CASE WHEN pgd.termin < 100 THEN
                    IFNULL(SUM(pgd.amount - (pgd.amount * (pgd.termin/100))),0)
                ELSE IFNULL(SUM(pgd.amount),0) END AS amount,
                CASE WHEN pgd.termin < 100 THEN
                    IFNULL(SUM(pgd.pph - (pgd.pph * (pgd.termin/100))),0)
                ELSE IFNULL(SUM(pgd.pph),0)	END AS pph,
                CASE WHEN pgd.termin < 100 THEN
                    IFNULL(SUM(pgd.ppn - (pgd.ppn * (pgd.termin/100))),0) 
                ELSE IFNULL(SUM(pgd.ppn),0)  END AS ppn,*/
            IFNULL(pgd.dp_amount,0) AS downpayment,
            IFNULL(pg.total_dpp,0) AS amount,
            IFNULL(pg.total_pph,0) AS pph,
            IFNULL(pg.total_ppn,0) AS ppn,
                pg.`pengajuan_general_id`,
                pg.pengajuan_email_date,
                pg.stockpileId,
                pg.entry_by,
                pg.entry_date,
                gv.general_vendor_name,
                pg.remarks,
                pg.status_pengajuan,
                pg.payment_type,
                pg.request_payment_date,
                pg.request_date
                FROM pengajuan_general pg 
                LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id	
                LEFT JOIN general_vendor gv ON gv.general_vendor_id = pgd.general_vendor_id
                LEFT JOIN general_vendor_pph acc ON acc.general_vendor_id = gv.general_vendor_id
                LEFT JOIN tax tx ON tx.tax_id = acc.pph_tax_id
                GROUP BY pgd.pg_id 
            ) AS d ON d.pengajuan_general_id = l.`pgeneral_id`
            LEFT JOIN (
                SELECT sum(settle_amount) as pp_downpayment, pp_dp1.idpp
                FROM pengajuan_payment pp1 
                LEFT JOIN pengajuan_payment_dp pp_dp1 ON pp_dp1.idpp = pp1.idpp	
                GROUP BY pp_dp1.idpp 
            ) AS pp_dp1 ON pp_dp1.idpp = l.`ppayment_id`
            -- END
            LEFT JOIN pengajuan_internaltf itf ON itf.pengajuan_interalTF_id = l.internalTf_id
            LEFT JOIN stockpile sp ON sp.stockpile_id = pp.stockpile_id 
            OR sp.stockpile_id = pur.stockpile_id 
            OR d.stockpileId = sp.stockpile_id
            OR itf.stockpile = sp.stockpile_id
            LEFT JOIN USER us ON us.user_id = pp.user 
            OR us.user_id = pur.entry_by 
            OR us.user_id = d.entry_by
            OR us.user_id = itf.entry_by
            LEFT JOIN vendor v ON v.vendor_id = pp.vendor_id OR v.vendor_id = pur.vendor_id
            LEFT JOIN freight f ON f.freight_id = pp.freight_id 
            LEFT JOIN labor lb ON lb.labor_id = pp.labor_id 
            LEFT JOIN vendor_handling vh ON vh.vendor_handling_id = pp.vendor_handling_id
            LEFT JOIN invoice inv ON inv.invoice_id = l.inv_general_id
            LEFT JOIN invoice_notim ino ON ino.inv_notim_id = l.inv_notim_id
            -- LEFT JOIN payment p ON p.payment_id = l.payment_id 
            LEFT JOIN batch_upload_detail bud ON bud.payment_id = l.payment_id 
            LEFT JOIN batch_upload_header buh ON buh.batch_code = bud.batch_code
            WHERE {$whereProperty}{$whereRequest}
            ORDER BY logbook_id DESC
        ) a where a.amount > 0"; 
//   echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


// $sql1 = "CALL SP_logbookNew('{$periodFrom}', '{$periodTo}')";
// $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT);


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
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "Tanggal Request Pembayaran = {$requestDate}");
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
$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Pengajuan cancel");
$objPHPExcel->getActiveSheet()->mergeCells("H{$rowActive}:I{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Pending");
$objPHPExcel->getActiveSheet()->mergeCells("J{$rowActive}:K{$rowActive}");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "On Process");
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
    $tempQtyPending = $row1->pending - $row1->countPayment;
    $tempNilaiPending = $row1->nilai_pending - $row1->PaymentValue;

    //PENGAJUAN PKS + GENERAL + PURCHASING
    $pengajuan = $row1->qty_pNotim + $row1->qty_pGeneral + $row1->pengajuan_purchasing + $row1->qty_internalTf;
    $nilaiPengajuan = $row1->nilai_pNotim + $row1->nilai_pGeneral + $row1->nilai_purchasing + $row1->nilai_internalTf;

    //ON PROSES INVOICE
    // $onProcess = $row1->qty_invNotim + $row1->qty_inv_general + $row1->countPayment;
    // $nilaiProcess = $row1->nilai_invNotim + $row1->nilai_inv_general + $row1->PaymentValue;
    $onProcess = $row1->countPayment;
    $nilaiProcess =  $row1->PaymentValue;


    //PENGAJUAN REJECT PKS + GENERAL
    // $tempReject = $row1->ppReject + $row1->Reject1;
    // $tempNilaiReject = $row1->ppReject_nilai + $row1->Reject_nilai1;
    $tempReject = $row1->qtyReject;
    $tempNilaiReject = $row1->nilaiReject;

   
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row1->tgl);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $nilaiPengajuan); 
    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $tempReject);
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $tempNilaiReject);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $tempQtyPending); 
    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $tempNilaiPending);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $onProcess); 
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $nilaiProcess);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row1->qty_paid); 
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row1->nilai_paid); 
    
    $tempqty = $tempqty + $pengajuan;
    $tempNilai = $tempNilai + $nilaiPengajuan;
    $tempQty1 = $tempQty1 + $onProcess;
    $tempNilai1 = $tempNilai1 + $nilaiProcess;
    $tempQty2 = $tempQty2 + $row1->qty_paid;
    $tempNilai2 = $tempNilai2 + $row1->nilai_paid;
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
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $totalQtyPending);
$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $TotalNilaiPending);
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $tempQty1);
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $tempNilai1);
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $tempQty2);
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $tempNilai2);
$objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn}{$rowActive}")->applyFromArray($styleArray8);
$bodyRowEnd = $rowActive;
$objPHPExcel->getActiveSheet()->getStyle("C" . ($headerRow + 1) . ":C{$bodyRowEnd}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");

//DATA LOGBOOK
$lastColumn1 = "AB";
$rowActive++;
$rowActive++;
$headerRow1 = $rowActive;
$objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", "No");
$objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", "Status");
$objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", "Status Pengajuan");

$objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", "Metode");

$objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", "Type Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", "Pengajuan System");
$objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", "Pengajuan Email");

$objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", "Request Payment Date");
$objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", "Stockpile");
$objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", "PIC Pengajuan");
$objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", "Vendor");
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Keterangan Pengajuan");

$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", "Dpp");
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", "PPn");
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", "PPh");
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", "DP");
$objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", "Total Nilai");

$objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", "Tanggal PV");

$objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", "No. Invoice");
$objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", "No. PV");
$objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", "PIC Finance");
$objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", "Remaks E-Bangking");
$objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", "Tanggal Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", "Jumlah Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", "PIC Pembayaran");
$objPHPExcel->getActiveSheet()->setCellValue("AA{$rowActive}", "Batch");
$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", "Aging");
$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Catatan");
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
$totalDpp = 0;
$totalPPh = 0;
$totalPPn = 0;
$totalDP = 0;
while($row = $resultData->fetch_object()) {
    $rowActive++;
    $objPHPExcel->getActiveSheet()->setCellValue("B{$rowActive}", $no1);
    $objPHPExcel->getActiveSheet()->setCellValue("C{$rowActive}", $row->urgentType);
    $objPHPExcel->getActiveSheet()->setCellValue("D{$rowActive}", $row->statusLogbook);

    $objPHPExcel->getActiveSheet()->setCellValue("E{$rowActive}", $row->method);

    $objPHPExcel->getActiveSheet()->setCellValue("F{$rowActive}", $row->typePengajuan); 
    $objPHPExcel->getActiveSheet()->setCellValue("G{$rowActive}", $row->tanggal_pengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("H{$rowActive}", $row->tglEmail); 

    $objPHPExcel->getActiveSheet()->setCellValue("I{$rowActive}", $row->urgentDate);
    $objPHPExcel->getActiveSheet()->setCellValue("J{$rowActive}", $row->stockpile_name);
    $objPHPExcel->getActiveSheet()->setCellValue("K{$rowActive}", $row->PICPengajuan);
    $objPHPExcel->getActiveSheet()->setCellValue("L{$rowActive}", $row->vendorName);
    $objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", $row->remarks);

    $objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $row->dpp);
    $objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $row->ppn);
    $objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $row->pph);
    $objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $row->downpayment);
    $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $row->amount);

    $objPHPExcel->getActiveSheet()->setCellValue("S{$rowActive}", $row->payment_date);
    $objPHPExcel->getActiveSheet()->setCellValue("T{$rowActive}", $row->invoiceNo2);
    $objPHPExcel->getActiveSheet()->setCellValue("U{$rowActive}", $row->payment_no);
    $objPHPExcel->getActiveSheet()->setCellValue("V{$rowActive}", $row->PICFinance);
    $objPHPExcel->getActiveSheet()->setCellValue("W{$rowActive}", $row->kode3);
    $objPHPExcel->getActiveSheet()->setCellValue("X{$rowActive}", $row->batchDate);
    $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $row->grand_total);
    $objPHPExcel->getActiveSheet()->setCellValue("Z{$rowActive}", $row->PICPaid);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit("AA{$rowActive}", $row->batch_code, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValue("AB{$rowActive}", $row->AgingPending);
	$objPHPExcel->getActiveSheet()->setCellValue("AC{$rowActive}", "Remaks");
    $no1++;

    $totalDpp = $totalDpp + $row->dpp;
    $totalPPh = $totalPPh + $row->pph;
    $totalPPn = $totalPPn + $row->ppn;
    $totalDP = $totalDP + $row->downpayment;
    $GrandTotalPengajuan = $GrandTotalPengajuan + $row->amount;
    $GrandTotalPembayaran = $GrandTotalPembayaran + $row->grand_total;
}


$rowActive++;
$objPHPExcel->getActiveSheet()->setCellValue("M{$rowActive}", "Grand Total : ");
$objPHPExcel->getActiveSheet()->setCellValue("N{$rowActive}", $totalDpp);
$objPHPExcel->getActiveSheet()->setCellValue("O{$rowActive}", $totalPPn);
$objPHPExcel->getActiveSheet()->setCellValue("P{$rowActive}", $totalPPh);
$objPHPExcel->getActiveSheet()->setCellValue("Q{$rowActive}", $totalDP);
 $objPHPExcel->getActiveSheet()->setCellValue("R{$rowActive}", $GrandTotalPengajuan);
 $objPHPExcel->getActiveSheet()->setCellValue("Y{$rowActive}", $GrandTotalPembayaran);
 $objPHPExcel->getActiveSheet()->getStyle("B{$rowActive}:{$lastColumn1}{$rowActive}")->applyFromArray($styleArray8);


$bodyRowEnd1 = $rowActive;
for ($temp = ord("B"); $temp <= ord("AB"); $temp++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension(chr($temp))->setAutoSize(true);
}
    $objPHPExcel->getActiveSheet()->getStyle("D" . ($headerRow + 1) . ":{$lastColumn}{$bodyRowEnd}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow) . ":{$lastColumn}{$bodyRowEnd}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table

    $objPHPExcel->getActiveSheet()->getStyle("N" . ($headerRow1 + 1) . ":R{$bodyRowEnd1}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 
    $objPHPExcel->getActiveSheet()->getStyle("Y" . ($headerRow1 + 1) . ":Y{$bodyRowEnd1}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Set number format for Amount 

    $objPHPExcel->getActiveSheet()->getStyle("B" . ($headerRow1) . ":{$lastColumn1}{$bodyRowEnd1}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); // Set border for table
    $objPHPExcel->getActiveSheet()->getStyle("I" . ($headerRow1 + 1) . ":I{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
    $objPHPExcel->getActiveSheet()->getStyle("G" . ($headerRow1 + 1) . ":G{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("H" . ($headerRow1 + 1) . ":H{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");
	$objPHPExcel->getActiveSheet()->getStyle("X" . ($headerRow1 + 1) . ":X{$bodyRowEnd1}")->getNumberFormat()->setFormatCode("dd-mm-YYYY");



ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
// </editor-fold>
exit();