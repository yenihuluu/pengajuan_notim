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
$requestDate = '';
$whereP2 = ''; 
$whereRequest = '';
$whereR2 = '';
$whereOA = '';
$whereGeneral = '';
$wherePKS = '';

if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && isset($_POST['requestDate']) && $_POST['requestDate'] != '') {
	$periodFrom = DateTime::createFromFormat('d/m/Y',  $_POST['periodFrom'])->format('Y/m/d');
    $periodTo = DateTime::createFromFormat('d/m/Y', $_POST['periodTo'])->format('Y/m/d');
	$requestDate = DateTime::createFromFormat('d/m/Y', $_POST['requestDate'])->format('Y/m/d');
	
    //$periodFrom = $_POST['periodFrom'];
    //$periodTo = $_POST['periodTo'];
	//$requestDate = $_POST['requestDate'];
	
    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereRequest .= " AND (DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pks.plan_payment_date, '%Y/%m/%d') = '{$requestDate}')";
    $whereP2 .= " DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereR2 .= " AND ((pgeneral_1.requestDate2 = '{$requestDate}') OR (others_1.requestDate1 = '{$requestDate}') OR (purchasing.requestDate3 = '{$requestDate}'))";
	$whereOA = " pp.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}' AND pp.requestDate1 = '{$requestDate}'";
	$whereGeneral = "pg.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}' AND pg.requestDate2 = '{$requestDate}'";
	$wherePKS = "pur.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}' AND pur.requestDate3 = '{$requestDate}'";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereBatch = " DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
} else if(isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodTo']) && $_POST['periodTo'] != '' && $_POST['requestDate'] == '') {
	//$periodFrom = $_POST['periodFrom'];
    //$periodTo = $_POST['periodTo'];
	$periodFrom = DateTime::createFromFormat('d/m/Y',  $_POST['periodFrom'])->format('Y/m/d');
    $periodTo = DateTime::createFromFormat('d/m/Y', $_POST['periodTo'])->format('Y/m/d');
	//echo $periodFrom . " | " . $periodTo;

    $whereProperty .= "DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereP2 .= " DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereOA = " pp.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$whereGeneral = "pg.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}'";
	$wherePKS = "pur.date2 BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
    $whereBatch = " DATE_FORMAT(l.entry_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";

}else if($_POST['periodFrom'] == '' && $_POST['periodTo'] == '' && isset($_POST['requestDate']) && $_POST['requestDate'] != ''){
	//$requestDate = $_POST['requestDate'];
	$requestDate = DateTime::createFromFormat('d/m/Y', $_POST['requestDate'])->format('Y/m/d');

	$whereProperty .= "(DATE_FORMAT(d.request_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') = '{$requestDate}') OR (DATE_FORMAT(pks.plan_payment_date, '%Y/%m/%d') = '{$requestDate}')";
	$whereOA = "  pp.requestDate1 = '{$requestDate}'";
	$whereGeneral = "pg.requestDate2 = '{$requestDate}'";
	$wherePKS = "pur.requestDate3 = '{$requestDate}'";
	$whereR2 .= "((pgeneral_1.requestDate2 = '{$requestDate}') OR (others_1.requestDate1 = '{$requestDate}') OR (purchasing.requestDate3 = '{$requestDate}'))";
    $wherePayment = " DATE_FORMAT(pay.entry_date, '%Y/%m/%d') = '{$requestDate}'";
    $whereBatch = " DATE_FORMAT(l.entry_date, '%Y/%m/%d') = '{$requestDate}'";

}

//COUNT LOGBOOK
$sql1 = "SELECT DATE_FORMAT(logbook.entry_date, '%Y/%m/%d') AS tgl,
            #OA/OB/HANDLING
            others.qty_pengajuan,
            others.nilai_pengajuan,
            others.pending_pengajuan,
            others.nilai_pending_pengajuan,
            others.Reject,
            others.Reject_nilai,
            others_1.pending AS ppayment_pending,
            others_1.nilai_pending AS ppayment_pending_value,
            others_1.requestDate1,

            #INVOICE (OB/OA/HANDLING)
            invoice.qty_inv_pks,
            invoice.nilai_inv_pks,
            invoice.pending_inv_pks,
            invoice.nilai_pending_inv_pks,
            #PENGAJUAN GENERAL
            pgeneral.qty_pinvoice,
            pgeneral.nilai_pinvoice,
            ppGeneral.pending_pInvoice,
            ppGeneral.nilai_pending_pInvoice,
            reject_general.Reject1,
            reject_general.Reject_nilai1,
            pgeneral_1.pending AS general_pending,
            pgeneral_1.nilai_pending AS general_pending_value,
            pgeneral_1.requestDate2,

            #INVOICE (GENERAL)
            invoice_general.qty_process_invoice,
            invoice_general.nilai_process_invoice,
            invoice_general.inv_pending_process1,
            invoice_general.inv_pending_process2,

            #KONTRAK PKS
            purchasing_1.pengajuan_purchasing,
            purchasing_1.nilai_purchasing,
            purchasing_1.pending_ppurchasing,
            purchasing_1.nilai_pending_ppurchasing,
            purchasing.pending AS purchasing_pending,
            purchasing.nilai_pending AS purchasing_pending_value,
            purchasing.requestDate3,

            #PAYMENT
            payment.countPayment,
            payment.PaymentValue,
            #BATCH UPLOAD
            batch.qty_paid,
            batch.nilai_paid
            FROM logbook_new logbook
            #PENGAJUAN (OA/OB/HANDLING)
            LEFT JOIN (	
                SELECT DATE_FORMAT(entry_date, '%Y/%m/%d') AS tgl,
                COUNT(entry_date) AS qty_pengajuan,
                IFNULL(SUM(amount),0) AS nilai_pengajuan,
                COUNT(CASE WHEN inv_notim_id IS NULL AND (dp_status = 0 OR dp_status = 1 )THEN 1 ELSE NULL END) AS pending_pengajuan,
                IFNULL(SUM(CASE WHEN inv_notim_id IS NULL AND (dp_status = 0 OR dp_status = 1) THEN amount ELSE NULL END),0) AS nilai_pending_pengajuan,                         
                COUNT(DISTINCT(CASE WHEN dp_status = '2' THEN 1 ELSE NULL END )) AS Reject, 
                IFNULL(SUM(CASE WHEN dp_status = '2' THEN amount ELSE NULL END),0) AS Reject_nilai
                FROM pengajuan_payment pp
                GROUP BY tgl
            ) AS others ON others.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #APPROVE INVOICE (OA/OB/HANDLING)
            LEFT JOIN(
                SELECT DATE_FORMAT(ino.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(CASE WHEN ino.inv_notim_no IS NOT NULL AND ino.status_payment IS NULL THEN 1 ELSE NULL END) AS qty_inv_pks,
                IFNULL(SUM( CASE WHEN ino.inv_notim_no IS NOT NULL AND ino.`status_payment` IS NULL THEN ino.amount_converted ELSE NULL END),0) AS nilai_inv_pks,      
                COUNT(CASE WHEN ino.status_payment IS NULL THEN 1 ELSE NULL END) AS pending_inv_pks,
                IFNULL(SUM(CASE WHEN ino.status_payment IS NULL THEN ino.amount_converted ELSE NULL END),0) AS nilai_pending_inv_pks
                FROM invoice_notim ino
                GROUP BY tgl
            ) AS invoice ON invoice.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PENDING (OA/OB/HANDLING)
            LEFT JOIN (
                SELECT pp.date1, 
                pp.pay_date, 
                pp.requestDate1,
                COUNT(CASE WHEN payment = 0 THEN 1 ELSE NULL END) AS pending,
                IFNULL(SUM(CASE WHEN payment = 0 THEN paymentValue ELSE 0 END),0) AS nilai_pending	
                FROM
                (
                    SELECT DATE_FORMAT(pp.`entry_date`, '%Y/%m/%d') AS DATE1,
                    DATE_FORMAT(pp.entry_date, '%Y/%m/%d') AS date2,
                    DATE_FORMAT(pp.urgent_payment_date, '%Y/%m/%d') AS requestDate1,
                    DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS PAY_DATE,
                    CASE WHEN DATE_FORMAT(pp.`entry_date`, '%Y/%m/%d') = DATE_FORMAT(pp.`entry_date`, '%Y/%m/%d') 
                        THEN pp.`payment_id` ELSE 0 END AS payment,
                    CASE WHEN DATE_FORMAT(pp.`entry_date`, '%Y/%m/%d') = DATE_FORMAT(pp.`entry_date`, '%Y/%m/%d') 
                        THEN pp.amount ELSE 0 END AS paymentValue
                FROM pengajuan_payment pp
                INNER JOIN payment pay ON pay.payment_id = pp.payment_id
                ) pp
            WHERE {$whereOA}
            GROUP BY pp.date1
            ) AS others_1 ON others_1.date1 = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PENGAJUAN  (GENERAL)
            LEFT JOIN (
                SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(pg.entry_date)) AS qty_pinvoice, #distinct dihilangin sementara
                IFNULL(SUM(pgd.amount),0) AS nilai_pinvoice
                FROM pengajuan_general pg 
                LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id	
                GROUP BY tgl
            ) AS pgeneral ON pgeneral.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            LEFT JOIN (
                SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                    COUNT(DISTINCT(pg.`pengajuan_general_id`)) AS Reject1, 
                    IFNULL(SUM((pgd.amount + pgd.ppn) - pgd.pph),0) AS Reject_nilai1
                    FROM pengajuan_general pg 
                    LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
                    WHERE (pg.status_pengajuan = '4'	OR pg.status_pengajuan = '2' OR pg.status_pengajuan = '5')
                GROUP BY tgl
            )AS reject_general ON reject_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            LEFT JOIN (
                SELECT DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS tgl,
                    COUNT(DISTINCT(pg.`pengajuan_general_id`)) AS pending_pInvoice, 
                    IFNULL(SUM((pgd.amount + pgd.ppn) - pgd.pph),0) AS nilai_pending_pInvoice
                    FROM pengajuan_general pg 
                    LEFT JOIN pengajuan_general_detail pgd ON pgd.pg_id = pg.pengajuan_general_id
                    WHERE pg.invoice_id IS NULL AND (pg.status_pengajuan = 0 OR pg.status_pengajuan = 1)
                GROUP BY tgl
            )AS ppGeneral ON ppGeneral.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #APPROVE INVOICE (GENERAL)
            LEFT JOIN (	
                SELECT DATE_FORMAT(inv.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN 1 ELSE NULL END)) AS qty_process_invoice,
                IFNULL(SUM(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN invd.amount ELSE NULL END),0) AS nilai_process_invoice,
                                                        
                COUNT(DISTINCT(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN 1 ELSE NULL END)) AS inv_pending_process1,
                IFNULL(SUM(CASE WHEN pg.invoice_id IS NOT NULL AND p.payment_no IS NULL THEN invd.amount ELSE NULL END),0) AS inv_pending_process2
                                                        
                FROM invoice inv  
                LEFT JOIN pengajuan_general pg ON pg.invoice_id = inv.invoice_id
                INNER JOIN invoice_detail invd ON invd.invoice_id = inv.invoice_id
                LEFT JOIN payment p ON p.invoice_id = inv.invoice_id
            GROUP BY tgl
            ) AS invoice_general ON invoice_general.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PENDING_GENERAL
            LEFT JOIN(
                SELECT 	
                pg.date1, 
                pg.pay_date, 
                pg.requestDate2,
                COUNT(CASE WHEN payment = 0 THEN 1 ELSE NULL END) AS pending,
                IFNULL(SUM(paymentValue),0) AS nilai_pending
                FROM
                (
                    SELECT DATE_FORMAT(pg.`entry_date`, '%Y/%m/%d') AS DATE1,
                    DATE_FORMAT(pg.entry_date, '%Y/%m/%d') AS date2,
                    DATE_FORMAT(pg.request_payment_date, '%Y/%m/%d') AS requestDate2,
                    DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS PAY_DATE,
                    CASE WHEN DATE_FORMAT(pg.`entry_date`, '%Y/%m/%d') = DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') 
                        THEN pay.`payment_id` ELSE 0 END AS payment,
                    CASE WHEN DATE_FORMAT(pg.`entry_date`, '%Y/%m/%d') = DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') 
                        THEN pay.amount ELSE 0 END AS paymentValue
                    FROM pengajuan_general pg  
                    INNER JOIN payment pay ON pay.invoice_id = pg.invoice_id
                ) pg
                WHERE {$whereGeneral}
                GROUP BY pg.date1
            ) AS pgeneral_1 ON pgeneral_1.date1 = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PENGAJUAN-PURCHASING (KONTRAK PKS)
            LEFT JOIN (
                SELECT DATE_FORMAT(pur.entry_date, '%Y/%m/%d') AS tgl,
                COUNT(DISTINCT(pur.entry_date)) AS pengajuan_purchasing,
                IFNULL(SUM(CASE WHEN pur.ppn = 1 THEN (pur.price * pur.`quantity`/1.1)
                WHEN pur.ppn = 2 THEN (pur.`price` * pur.`quantity`)
                ELSE 1 END),0) AS nilai_purchasing,
                COUNT(CASE WHEN pur.status = 0 AND pur.payment_id IS NULL THEN 1 ELSE NULL END) AS pending_ppurchasing,
                IFNULL(SUM(CASE WHEN pur.status = 0 AND pur.payment_id IS NULL AND pur.ppn = 1 THEN (pur.price * pur.`quantity`/1.1)
                        WHEN pur.`status` = 0 AND pur.`payment_id`IS NULL AND pur.ppn = 2 THEN (pur.price * pur.quantity)
                ELSE NULL END),0) AS nilai_pending_ppurchasing
                FROM purchasing pur 
                INNER JOIN logbook_new l ON l.`purchasing_Id` = pur.`purchasing_id`
                WHERE pur.company = 1
                GROUP BY tgl
            ) AS purchasing_1 ON purchasing_1.tgl = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PENDING PURCHASING
            LEFT JOIN(
                SELECT 	
                pur.date1, 
                pur.pay_date, 
                pur.requestDate3,
                COUNT(CASE WHEN payment = 0 THEN 1 ELSE NULL END) AS pending,
                IFNULL(SUM(CASE WHEN payment = 0 AND pur.ppn = 1 THEN (pur.price * pur.`quantity`/1.1)
                        WHEN payment = 0 AND pur.ppn = 2 THEN (pur.price * pur.quantity) ELSE 0 END),0) AS nilai_pending
                FROM
                (
                    SELECT DATE_FORMAT(pur.`entry_date`, '%Y/%m/%d') AS DATE1,
                    DATE_FORMAT(pur.entry_date, '%Y/%m/%d') AS date2,
                    DATE_FORMAT(pks.plan_payment_date, '%Y/%m/%d') AS requestDate3,
                    DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS PAY_DATE,
                    CASE WHEN DATE_FORMAT(pur.`entry_date`, '%Y/%m/%d') = DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') 
                        THEN pur.`payment_id` ELSE 0 END AS payment,
                pur.ppn,
                pur.price,
                pur.quantity
                FROM purchasing pur
                INNER JOIN payment pay ON pay.payment_id = pur.payment_id
                LEFT JOIN po_pks pks ON pks.purchasing_id = pur.purchasing_id
                ) pur
                WHERE {$wherePKS}
                GROUP BY pur.date1
            ) AS purchasing ON purchasing.date1 = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #PAYMENT
            LEFT JOIN(
                SELECT DATE_FORMAT(l.`entry_date`, '%Y/%m/%d') AS logbookDate, 
                DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d') AS paymentDate, 
                COUNT(DISTINCT(pay.`entry_date`)) AS countPayment,
                IFNULL(SUM(pay.amount),0) AS PaymentValue
                FROM payment pay
                INNER JOIN logbook_new l ON l.payment_id = pay.payment_id
                WHERE {$wherePayment}
                GROUP BY DATE_FORMAT(pay.`entry_date`, '%Y/%m/%d')
            ) AS payment ON payment.paymentDate = DATE_FORMAT(logbook.entry_date, '%Y/%m/%d')
            #BATCH UPLOAD
            LEFT JOIN(
                SELECT DATE_FORMAT(l.entry_date, '%Y/%m/%d') AS tgl, -- seharusnya pake entry date punya batch upload
                COUNT(bud.batch_code) AS qty_paid,
                IFNULL(SUM(bud.grand_total),0) AS nilai_paid
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
                    THEN CASE WHEN pur.ppn = 1 THEN (pur.price * pur.quantity)/1.1
                              WHEN pur.ppn = 2 THEN (pur.price * pur.quantity)
                    ELSE NULL END
        ELSE NULL END AS amount, 
        CASE WHEN l.type_pengajuan = '1' THEN  ino.inv_notim_no
             WHEN l.type_pengajuan = '2' THEN inv.invoice_no ELSE NULL END AS invoiceNo2,
        p.payment_no, 
        CASE WHEN l.type_pengajuan = '1' THEN DATE_FORMAT(pp.urgent_payment_date, '%d-%m-%Y') 
			WHEN l.type_pengajuan = '2' THEN DATE_FORMAT(d.request_payment_date, '%d-%m-%Y') 
            WHEN l.type_pengajuan = '3' THEN DATE_FORMAT(pks.plan_payment_date, '%d-%m-%Y') 
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
        LEFT JOIN po_pks pks ON pks.purchasing_id = l.purchasing_id
        #PENGAJUAN  (GENERAL)
        LEFT JOIN (
            SELECT pg.entry_date AS tgl,
                IFNULL(SUM(pgd.amount),0) AS amount,
				IFNULL(SUM(pgd.pph),0) AS pph1,
				IFNULL(SUM(pgd.ppn),0) AS ppn1,
                pg.`pengajuan_general_id`,
                pg.pengajuan_email_date,
                pg.stockpileId,
                pg.entry_by,
                pg.entry_date,
                gv.general_vendor_name,
                pg.remarks,
                pg.status_pengajuan,
				pg.request_payment_date,
				pg.payment_type
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

            widgets: ['zebra', 'filter', 'uitheme'],

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

<form method="post" action="reports/logbook-data-xls-prod.php">
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
            <th colspan="2">Pengajuan Reject</th>
            <th colspan="2">On Process</th>
            <th colspan="2">Pending</th>
            <th colspan="2">Paid</th>

            
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

		$tempPengajuanPending = 0;
		$tempPengajuanPendingVal = 0;
        $tempGeneralPending = 0;
        $tempGeneralPendingVal = 0;
		$tempKonstrakPending = 0;
		$tempKonstrakPendingVal = 0;
       

        //PENDING PKS + GENERAL + PURCHASING
		$tempPengajuanPending = $row1->pending_pengajuan + $row1->pending_inv_pks + $row1->ppayment_pending;
		$tempPengajuanPendingVal = $row1->nilai_pending_pengajuan + $row1->nilai_pending_inv_pks + $row1->ppayment_pending_value;
        $tempGeneralPending = $row1->pending_pInvoice + $row1->inv_pending_process1 + $row1->general_pending;
        $tempGeneralPendingVal = $row1->nilai_pending_pInvoice + $row1->inv_pending_process2 + $row1->general_pending_value;
		$tempKonstrakPending = $row1->pending_ppurchasing + $row1->purchasing_pending;
		$tempKonstrakPendingVal = $row1->nilai_pending_ppurchasing + $row1->purchasing_pending_value;
        $tempQtyPending = $tempPengajuanPending + $tempGeneralPending  + $tempKonstrakPending;
        $tempNilaiPending = $tempPengajuanPendingVal + $tempGeneralPendingVal + $tempKonstrakPendingVal;
       

        //PENGAJUAN PKS + GENERAL + PURCHASING
        $pengajuan = $row1->qty_pengajuan + $row1->qty_pinvoice + $row1->pengajuan_purchasing;
        $nilaiPengajuan = $row1->nilai_pengajuan + $row1->nilai_pinvoice + $row1->nilai_purchasing;
        $onProcess = $row1->countPayment + $row1->qty_process_invoice + $row1->qty_inv_pks;
        $nilaiProcess = $row1->PaymentValue + $row1->nilai_process_invoice + $row1->nilai_inv_pks;

        //PENGAJUAN REJECT PKS + GENERAL
        $tempReject = $row1->Reject + $row1->Reject1;
        $tempNilaiReject = $row1->Reject_nilai + $row1->Reject_nilai1;

       // echo $row1->nilai_process_invoice;
        ?>
        <tr>
            <td><?php echo $no; ?></td> 
            <td><?php echo $row1->tgl; ?></td>
            <td><?php echo number_format($pengajuan, 0, ".", ","); ?></td>
            <td><?php echo number_format($nilaiPengajuan, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempReject, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempNilaiReject, 0, ".", ","); ?></td>
            <td><?php echo number_format($onProcess, 0, ".", ","); ?></td>
            <td><?php echo number_format($nilaiProcess, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempQtyPending, 0, ".", ","); ?></td>
            <td><?php echo number_format($tempNilaiPending, 0, ".", ","); ?></td>
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
            <td><b><?php echo number_format($tempQty1, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($tempNilai1, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($totalQtyPending, 0, ".", ","); ?></b></td>
            <td><b><?php echo number_format($TotalNilaiPending, 0, ".", ","); ?></b></td>
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
		<th>Status</th>
        <th>Status Pengajuan</th>
        <th>Type Pengajuan</th>
        <th>Pengajuan System</th>
        <th>Pengajuan Email</th>
        <th>Request Payment Date</th>
        <th>Stockpile</th>
        <th>PIC Pengajuan</th>
        <th>Vendor</th>
        <th>Keterangan Pengajuan</th>
        <th>Nilai</th>
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
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td><?php echo $No; ?></td>
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
                    if($rowData->statusLogbook == 'Reject'){
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
                <td><?php echo $rowData->typePengajuan; ?></td>
                <td><?php echo $rowData->tanggal_pengajuan; ?></td>
                <td><?php echo $rowData->tglEmail; ?></td>
                <td><?php echo $rowData->urgentDate; ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo $rowData->PICPengajuan; ?></td>
                <td><?php echo $rowData->vendorName; ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                <td><?php echo number_format($rowData->amount, 0, ".", ","); ?></td>
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
            $totalNilai = $totalNilai + $rowData->amount;
            $tempGrandTotal = $tempGrandTotal + $rowData->grand_total;
            // $tempJmlSystem = $rowData->jmlPengajuan;
            // $tempJmlEmail = $rowData->jmlPengajuanEmail;
            $No++;
        }
        ?>
        <tr>
        <td><b>Total</b></td>
        <td></td>
        <td><?php //echo number_format($tempJmlSystem, 0, ".", ","); ?></td>
        <td><?php //echo number_format($tempJmlEmail, 0, ".", ","); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php //echo number_format($totalNilai, 0, ".", ","); ?></td>
        <td><?php echo number_format($totalNilai, 0, ".", ","); ?></td>
        <td><?php //echo number_format($tempjmlPV, 0, ".", ","); ?></td>
        <td></td>
        <td><?php //echo number_format($tempremaksEbangking, 0, ".", ","); ?></td>
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
