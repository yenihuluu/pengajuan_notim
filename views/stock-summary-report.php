<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
SELECT p.product_name, COALESCE(a.quantity1, 0), COALESCE(a.inventory_amount1, 0), COALESCE(a.freight_cost1, 0), COALESCE(a.unloading_cost1, 0), COALESCE(b.quantity2, 0), COALESCE(b.inventory_amount2, 0), COALESCE(b.freight_cost2, 0), COALESCE(b.unloading_cost2, 0),
COALESCE(c.quantity3, 0), COALESCE(c.inventory_amount3, 0), COALESCE(c.freight_cost3, 0), COALESCE(c.unloading_cost3, 0)
FROM product p
LEFT JOIN 
(
SELECT z.product_id1, COALESCE(SUM(z.quantity1), 0) AS quantity1, COALESCE(SUM(z.inventory_amount1), 0) AS inventory_amount1, 
COALESCE(SUM(z.freight_cost1), 0) AS freight_cost1, COALESCE(SUM(z.unloading_cost1), 0) AS unloading_cost1
FROM (
select t.product_id AS product_id1, t.stockpile_contract_id AS stockpile_contract_id1, t.shipment_id AS shipment_id1, t.transaction_date AS transaction_date1, 
case when t.transaction_type = 1 THEN t.unloading_date ELSE t.transaction_date END AS unloading_date1, 
case when t.transaction_type = 1 THEN t.quantity ELSE -1 * t.quantity END AS quantity1,
case when t.transaction_type = 1 THEN t.quantity * t.unit_price ELSE (SELECT -1 * COALESCE(SUM(inventory_value), 0) FROM delivery d WHERE d.shipment_id = t.shipment_id) END AS inventory_amount1,
case when t.transaction_type = 1 THEN t.quantity * t.freight_price ELSE 0 END AS freight_cost1,
case when t.transaction_type = 1 THEN t.unloading_price ELSE 0 END AS unloading_cost1
FROM `transaction` t
LEFT JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
LEFT JOIN shipment sh ON sh.shipment_id = t.shipment_id
LEFT JOIN sales sl ON sl.sales_id = sh.sales_id
WHERE (sc.stockpile_id = 2 OR sl.stockpile_id = 2)
) z
WHERE MONTH(z.unloading_date1) < 9
GROUP BY z.product_id1
) a 
	ON a.product_id1 = p.product_id
LEFT JOIN 
(
SELECT x.product_id2, COALESCE(SUM(x.quantity2), 0) AS quantity2, COALESCE(SUM(x.inventory_amount2), 0) AS inventory_amount2, 
COALESCE(SUM(x.freight_cost2), 0) AS freight_cost2, COALESCE(SUM(x.unloading_cost2), 0) AS unloading_cost2
FROM (
select t.product_id AS product_id2, t.stockpile_contract_id AS stockpile_contract_id2, t.transaction_date AS transaction_date2, 
t.unloading_date AS unloading_date2, t.quantity AS quantity2,
t.quantity * t.unit_price AS inventory_amount2,
t.quantity * t.freight_price AS freight_cost2,
t.unloading_price AS unloading_cost2
FROM `transaction` t 
INNER JOIN stockpile_contract sc ON sc.stockpile_contract_id = t.stockpile_contract_id
WHERE t.transaction_type = 1 AND sc.stockpile_id = 2
) x
WHERE MONTH(x.unloading_date2) = 9
GROUP BY x.product_id2
) b
	ON b.product_id2 = p.product_id
LEFT JOIN 
(
SELECT y.product_id3, COALESCE(SUM(y.quantity3), 0) AS quantity3, COALESCE(SUM(y.inventory_amount3), 0) AS inventory_amount3, 
COALESCE(SUM(y.freight_cost3), 0) AS freight_cost3, COALESCE(SUM(y.unloading_cost3), 0) AS unloading_cost3
FROM (
select t.product_id AS product_id3, t.shipment_id AS shipment_id3, t.transaction_date AS transaction_date3, 
t.unloading_date AS unloading_date3, t.quantity AS quantity3,
t.quantity * t.unit_price AS inventory_amount3,
t.quantity * t.freight_price AS freight_cost3,
t.unloading_price AS unloading_cost3
FROM `transaction` t 
LEFT JOIN shipment sh ON sh.shipment_id = t.shipment_id
LEFT JOIN sales sl ON sl.sales_id = sh.sales_id
WHERE t.transaction_type = 2 AND sl.stockpile_id = 2
) y
WHERE MONTH(y.transaction_date3) = 9
GROUP BY y.product_id3
) c
	ON c.product_id3 = p.product_id
WHERE 1=1