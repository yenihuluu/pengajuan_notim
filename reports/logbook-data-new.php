<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

date_default_timezone_set('Asia/Jakarta');
$date = new DateTime();
$currentDate = $date->format('d/m/Y');

$whereProperty = '';
$periodFrom = '';
$periodTo = '';
$whereProperty2 = ''; 
$requestDate = '';
$whereRequest = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) 
    && $_POST['periodTo'] != '' && isset($_POST['requestDate']) && $_POST['requestDate'] == '') {
    $periodFrom = DateTime::createFromFormat('d/m/Y',  $_POST['periodFrom'])->format('Y/m/d');
    $periodTo = DateTime::createFromFormat('d/m/Y', $_POST['periodTo'])->format('Y/m/d');
    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
}else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && isset($_POST['requestDate']) && $_POST['requestDate'] != '') {
    $periodFrom = DateTime::createFromFormat('d/m/Y',  $_POST['periodFrom'])->format('Y/m/d');
    $periodTo = DateTime::createFromFormat('d/m/Y', $_POST['periodTo'])->format('Y/m/d');
    $requestDate = DateTime::createFromFormat('d/m/Y', $_POST['requestDate'])->format('Y/m/d');
    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereRequest .= " OR (DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                    . "OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') "
                    . "OR (DATE_FORMAT(pur.plan_payment_date, '%Y/%m/%d') = '{$requestDate}') ";
}else if($_POST['periodFrom'] == '' && $_POST['periodTo'] == '' && isset($_POST['requestDate']) && $_POST['requestDate'] != ''){
    $requestDate = DateTime::createFromFormat('d/m/Y', $_POST['requestDate'])->format('Y/m/d');
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
// echo $periodFrom .'|| ' . $periodTo;
//  else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] == '') {
//     $periodFrom = $_POST['periodFrom'];
//     $paymentFrom1 = DateTime::createFromFormat('d/m/Y', $periodFrom)->format('Y/m/d');
//     $whereProperty .= " DATE_FORMAT(l.entry_date, '%Y/%m/%d') <= '{$paymentFrom1}'";
//     $whereProperty2 .= " DATE_FORMAT(a.entry_date, '%Y/%m/%d') <= '{$paymentFrom1}'";
// } else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] == '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '') {
//     $periodTo = $_POST['periodTo'];
//     $periodTo1 = DateTime::createFromFormat('d/m/Y', $periodTo)->format('Y/m/d');
//     $whereProperty .= " DATE_FORMAT(l.entry_date, '%Y/%m/%d') <= '{$periodTo1}'";
//     $whereProperty2 .= " DATE_FORMAT(a.entry_date, '%Y/%m/%d') <= '{$periodTo1}'";
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
            CASE WHEN pp.payment_method = '1' OR pp.payment_method = '3' THEN 'Payment'
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
        ) a  where a.amount > 0"; 
  //echo $sql;
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


    //COUNT LOGBOOK
    // $sql1 = "CALL SP_logbookNew('{$periodFrom}', '{$periodTo}')";
    // $result1 = $myDatabase->query($sql1, MYSQLI_STORE_RESULT); 

?>

<script type="text/javascript">
    $(document).ready(function () {	//executed after the page has loaded

        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table: 'table table-bordered',
            header: 'bootstrap-header', // give the header a gradient background
            footerRow: '',
            footerCells: '',
            icons: '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone: 'bootstrap-icon-unsorted',
            sortAsc: 'icon-chevron-up',
            sortDesc: 'icon-chevron-down',
            active: '', // applied when column is sorted
            hover: '', // use custom css here - bootstrap class may not override it
            filterRow: '', // filter row class
            even: '', // odd row zebra striping
            odd: ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets: ['zebra', 'filter', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra: ["even", "odd"],
            }
        }).tablesorterPager({
            container: $(".pager"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });

        // $('#addNew').click(function (e) {
        //     e.preventDefault();
        //     loadContent($('#addNew').attr('href'));
        // });

        $('#contentTable a').click(function (e) {
            e.preventDefault();
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                // $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                // $('#dataContent').load('forms/add-logbook.php', {logbookId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } 
        });

        //TEST
            $("#contentTable1").tablesorter({
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            widgets: ['zebra', '', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                zebra: ["even", "odd"],
            }
        }).tablesorterPager({
            container: $(".pager1"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });
        
        //END TEST

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }
</script>

<form method="post" action="reports/logbook-data-xls-new.php">
    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>" />
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>" />
	<input type="hidden" id="requestDate" name="requestDate" value="<?php echo $requestDate; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<table class="table table-bordered table-striped" id="contentTable1" style="font-size: 8pt;">
<thead>
        <tr>
            <th rowspan="2" >No.</th>
            <th rowspan="2">Tanggal</th>
            <th colspan="2">Pengajuan submit</th>
            <th colspan="2">Pengajuan cancel</th>
            <th colspan="2">Pending</th>
            <th colspan="2">On Process (PV)</th>
            <th colspan="2">Paid (BU)</th>
        </tr>

        <tr>
            <th>Qty</th>
            <th>Nilai</th>
            <th>Qty</th>
            <th>Nilai</th>
            <th>Qty</th>
            <th>Nilai</th>
            <th>Qty</th>
            <th>Nilai</th>
            <th>Qty</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>
    <?php
         if($result1 === false) {
             echo 'wrong query';
         } else {
			
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
	
    $no = 1;
    while($row1 = $result1->fetch_object()) {
        $tempQtyPending = 0;
        $tempNilaiPending = 0;

        //PENDING PKS + GENERAL + PURCHASING
        // $tempQtyPending = $row1->pending - $row1->qty_batchUpload;
		// $tempNilaiPending = $row1->nilai_pending - $row1->amountBU;
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


       // echo $row1->nilai_process_invoice;
        ?>
        <tr>
            <td><?php echo $no; ?></td> 
            <td><?php echo $row1->tgl; ?></td>
            <td><?php echo number_format($pengajuan, 0, ".", ","); ?></td>
            <td><?php echo number_format($nilaiPengajuan, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempReject, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempNilaiReject, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempQtyPending, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempNilaiPending, 0, ".", ","); ?></td>
            <td><?php echo number_format($onProcess, 0, ".", ","); ?></td>
            <td><?php echo number_format($nilaiProcess, 0, ".", ","); ?></td>
            <td><?php echo number_format($row1->qty_paid, 0, ".", ","); ?></td>
            <td><?php echo number_format($row1->nilai_paid, 0, ".", ","); ?></td>
        </tr>
        <?php
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
        } ?>

        <tr>
            <td></td> 
            <td><b>TOTAL</b></td>
            <td><b><?php echo number_format($tempqty, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempNilai, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempQty3, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempNilai3, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($totalQtyPending, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($TotalNilaiPending, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempQty1, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempNilai1, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempQty2, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempNilai2, 0, ".", ","); ?></b></td>

            
        </tr>
        <?php }
        ?>
    </tbody>
</table>

<div class="pager1" text-align: center;>
    Page: <select class="pagenum input-mini"></select>
    <i class="first icon-step-backward" alt="First" title="First page"></i>
    <i class="prev icon-arrow-left" alt="Prev" title="Previous page"></i>
    <button type="button" class="btn first"><i class="icon-step-backward"></i></button>
    <button type="button" class="btn prev"><i class="icon-arrow-left"></i></button>
    <span class="pagedisplay"></span>
    <i class="next icon-arrow-right" alt="Next" title="Next page"></i>
    <i class="last icon-step-forward" alt="Last" title="Last page"></i>
    <button type="button" class="btn next"><i class="icon-arrow-right"></i></button>
    <button type="button" class="btn last"><i class="icon-step-forward"></i></button>
    <select class="pagesize input-mini">
        <option selected="selected" value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="40">40</option>
    </select>
</div>

</br>
</br>
<!-- 
<a href="#addNew|add-logbook" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px"
                                                       style="margin-bottom: 5px;"/> Add Logbook</a> -->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th>No</th>
        <th>No.Pengajuan</th>
		<th>Status</th>
        <th>Status Pengajuan</th>
        <th>Metode</th>
        <th>Type Pengajuan</th>
        <th>Pengajuan System</th>
        <th>Pengajuan Email</th>
        <th>Request Payment Date</th>
        <th>DPP</th>
        <th>PPn</th>
        <th>PPh</th>
        <th>DP</th>
        <th>Nilai</th>
        <th>Stockpile</th>
        <th>PIC Pengajuan</th>
        <th>Vendor</th>
        <th>Keterangan Pengajuan</th>
        <th>Tanggal PV </th>
        <th>No. Invoice</th>
        <th>No. PV</th>
        <th>PIC Finance</th>
        <th>Remaks E-Bangking</th>
        <th>Tanggal Pembayaran</th>
        <th>Jumlah Pembayaran</th>
        <th>PIC Pembayaran</th>
        <th>Batch</th>
        <th>Aging</th>
        <th>Catatan</th>
    </tr>
    </thead>
    <tbody>
    <?php

    if ($resultData !== false && $resultData->num_rows > 0) {
        $No = 1;
        $totalNilai = 0;
        $tempGrandTotal = 0;
        $tempJmlSystem = 0;
        $tempJmlEmail = 0;
        $tempjmlInvoiceNo = 0;
        $tempjmlPV = 0;
        $tempremaksEbangking = 0;
        $totalDpp = 0;
        $totalPPh = 0;
        $totalPPn = 0;
        $totalDP = 0;
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td><?php echo $No; ?></td>
                <td><?php echo $rowData->IdPengajuan; ?></td>
				<?php 
                    if($rowData->urgentType == 'URGENT'){
                        echo "<td style='font_weight: bold; color: red;'>"; 
						echo $rowData->urgentType;
                        echo "</td>";
                    }else{
                        echo "<td style='font_weight: bold; color: black;'>"; 
						echo $rowData->urgentType;
                        echo "</td>";
                    }

                ?>
                <?php 
                    if($rowData->statusLogbook == 'Reject' || $rowData->statusLogbook == 'Cancel/Return'){
                        echo "<td style='font_weight: bold; color: red;'>"; 
                        echo $rowData->statusLogbook;
                        echo "</td>";
                    }else if($rowData->statusLogbook == 'Paid'){
                        echo "<td style='font_weight: bold; color: green;'>"; 
                        echo $rowData->statusLogbook;
                        echo "</td>";
                    }
                    else{
                        echo "<td style='font_weight: bold; color: blue;'>"; 
                        echo $rowData->statusLogbook;
                        echo "</td>";
                    }
                ?>
                <td><?php echo $rowData->method; ?></td>
                <td><?php echo $rowData->typePengajuan; ?></td>
                <td><?php echo $rowData->tanggal_pengajuan; ?></td>
                <td><?php echo $rowData->tglEmail; ?></td>
                <td><?php echo $rowData->urgentDate; ?></td>
                <td><?php echo number_format($rowData->dpp, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->ppn, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->pph, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->downpayment, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->amount, 0, ".", ","); ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo $rowData->PICPengajuan; ?></td>
                <td><?php echo $rowData->vendorName; ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                <td><?php echo $rowData->payment_date; ?></td>
                <td><?php echo $rowData->invoiceNo2; ?></td>
                <td><?php echo $rowData->payment_no; ?></td>
                <td><?php echo $rowData->PICFinance; ?></td>
                <td><?php echo $rowData->kode3; ?></td>
                <td><?php echo $rowData->batchDate; ?></td>
                <td><?php echo number_format($rowData->grand_total, 0, ".", ","); ?></td>
                <td><?php echo $rowData->PICPaid; ?></td>
                <td><?php echo $rowData->batch_code; ?></td>
                <td><?php echo number_format($rowData->AgingPending, 0, ".", ","); ?> Hari</td>
                <td>Remarks</td>
            </tr>
            <?php
            $totalDpp = $totalDpp + $rowData->dpp;
            $totalPPh = $totalPPh + $rowData->pph;
            $totalPPn = $totalPPn + $rowData->ppn;
            $totalNilai = $totalNilai + $rowData->amount;
            $totalDP= $totalDP + $rowData->downpayment;
            $tempGrandTotal = $tempGrandTotal + $rowData->grand_total;
            $No++;
        }
        ?>
        <tr>
        <td colspan="8"><b>Total</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo number_format($totalDpp, 0, ".", ","); ?></td>
        <td><?php echo number_format($totalPPn, 0, ".", ","); ?></td>
        <td><?php echo number_format($totalPPh, 0, ".", ","); ?></td>
        <td><?php echo number_format($totalDP, 0, ".", ","); ?></td>
        <td><?php echo number_format($totalNilai, 0, ".", ","); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo number_format($tempGrandTotal, 0, ".", ","); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        </tr>
        <?php
    } else {
        ?>
        <tr>
            <td colspan="3">
                No data to be shown.
            </td>
        </tr>
        <?php
    }

    ?>
    </tbody>
</table>

<div class="pager">
    Page: <select class="pagenum input-mini"></select>
    <i class="first icon-step-backward" alt="First" title="First page"></i>
    <i class="prev icon-arrow-left" alt="Prev" title="Previous page"></i>
    <button type="button" class="btn first"><i class="icon-step-backward"></i></button>
    <button type="button" class="btn prev"><i class="icon-arrow-left"></i></button>
    <span class="pagedisplay"></span>
    <i class="next icon-arrow-right" alt="Next" title="Next page"></i>
    <i class="last icon-step-forward" alt="Last" title="Last page"></i>
    <button type="button" class="btn next"><i class="icon-arrow-right"></i></button>
    <button type="button" class="btn last"><i class="icon-step-forward"></i></button>
    <select class="pagesize input-mini">
        <option selected="selected" value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="40">40</option>
    </select>
</div>
