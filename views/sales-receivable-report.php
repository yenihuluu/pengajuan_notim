<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
SELECT s.stockpile_name, sl.sales_no, DATE_FORMAT(sl.sales_date, '%d %b %Y') AS sales_date2, sl.sales_date,  
                sl.quantity AS sales_quantity, sl.price, sl.price * sl.quantity AS sales_amount, sl.destination,
                sh.shipment_date, sh.invoice_amount
        FROM shipment sh
        INNER JOIN sales sl
                ON sl.sales_id = sh.sales_id
        INNER JOIN stockpile s
                ON s.stockpile_id = sl.stockpile_id
        INNER JOIN customer cust
                ON cust.customer_id = sl.customer_id
        INNER JOIN payment p
        		ON p.sales_id = sh.sales_id
        INNER JOIN payment_detail pd
        		ON pd.shipment_id = sh.shipment_id
        INNER JOIN bank b
        		ON b.bank_id = p.bank_id
        INNER JOIN `transaction` t
        		ON t.shipment_id = sh.shipment_id
        WHERE 1=1 AND sh.shipment_date IS NOT NULL
        ORDER BY s.stockpile_name, sl.sales_no