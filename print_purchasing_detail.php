<?php

// PATH
require_once './assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';
	$namaFile = "purchasing.xls";


// Function penanda awal file (Begin Of File) Excel

function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}

// Function penanda akhir file (End Of File) Excel

function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}

// Function untuk menulis data (angka) ke cell excel

function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}

// Function untuk menulis data (text) ke cell excel

function xlsWriteLabel($Row, $Col, $Value ) {
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}

// header file excel

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");

// header untuk nama file
header("Content-Disposition: attachment;filename=".$namaFile."");

header("Content-Transfer-Encoding: binary ");

// memanggil function penanda awal file excel
xlsBOF();

// ------ membuat kolom pada excel --- //
$y=0;
$x=0;
// mengisi pada cell A1 (baris ke-0, kolom ke-0)

// -------- menampilkan data --------- //

$tempstockpile='';
$temppo='';
$tempcontract='';
$tempvendor='';
$tempqty='';
$tempprice='';
$temphandling='';
$stockpileId = '';
$stockpileIds='';

if(isset($_POST['stockpileId']) && $_POST['stockpileId'] != '') {

    $stockpileId = $_POST['stockpileId'];

	for ($i = 0; $i < sizeof($stockpileId); $i++) {
                        if($stockpileIds == '') {
                            $stockpileIds .= "'". $stockpileId[$i] ."'";
                        } else {
                            $stockpileIds .= ','. "'". $stockpileId[$i] ."'";
                        }
                    }
		$whereproperty = "and sc.stockpile_id in  ({$stockpileIds}) ";
		}
		else {
			$whereproperty = "";
		}

$sql = "SELECT s.`stockpile_name`,c.`po_no`,c.`contract_no`,v.`vendor_name`,c.`quantity`, SUM(COALESCE(t.`send_weight`,0)) AS qty_rcv,
				COALESCE(c.`price_converted`,0) AS price, f.`freight_supplier`,COALESCE(fc.`price_converted`,0) AS oa,
				vh.`vendor_handling_name`,vhc.`price_converted` AS handling,sc.stockpile_contract_id
				FROM contract c
				LEFT JOIN stockpile_contract sc ON sc.`contract_id`=c.`contract_id`
				LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id`=sc.`stockpile_contract_id`
				INNER JOIN stockpile s ON s.`stockpile_id`=sc.`stockpile_id`
				LEFT JOIN vendor v ON v.`vendor_id`=c.`vendor_id`
				LEFT JOIN freight_cost fc ON fc.`freight_cost_id`=t.`freight_cost_id`
				LEFT JOIN freight f ON f.`freight_id`=fc.`freight_id`
				LEFT JOIN `vendor_handling_cost` vhc ON vhc.`handling_cost_id`=t.`handling_cost_id`
				LEFT JOIN `vendor_handling` vh ON vh.`vendor_handling_id`=vhc.`vendor_handling_id`
				WHERE c.`contract_type`='P' ".$whereproperty."
				GROUP BY c.po_no,f.`freight_supplier`
				ORDER BY s.stockpile_name ASC,c.`entry_date`";

$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
//xlsWriteLabel($y,$x,$sql);

xlsWriteLabel($y,$x,'Stockpile');
xlsWriteLabel($y,$x + 1,'PO');
xlsWriteLabel($y,$x + 2,'Contract');
xlsWriteLabel($y,$x + 3,'Vendor');
xlsWriteLabel($y,$x + 4,'Price');
xlsWriteLabel($y,$x + 5,'Qty');
xlsWriteLabel($y,$x + 6,'Qty_receive');
xlsWriteLabel($y,$x + 7,'Freight');
xlsWriteLabel($y,$x + 8,'OA');
xlsWriteLabel($y,$x + 9,'Handling');
xlsWriteLabel($y,$x + 10,'OH');
xlsWriteLabel($y,$x + 11,'Vehicle');
xlsWriteLabel($y,$x + 12,'OB');
xlsWriteLabel($y,$x + 13,'Vendor fee');
xlsWriteLabel($y,$x + 14,'Fee');
$y =$y + 1;
$ystart=$y;
while ($rowData = $resultData->fetch_object()) {

			$y=$ystart;
			if ($tempstockpile <> $rowData->stockpile_name)
			xlsWriteLabel($y, $x ,$rowData->stockpile_name);
			else {
			xlsWriteLabel($y, $x ,'');
			}

			if ($temppo <> $rowData->po_no)
			xlsWriteLabel($y, $x+1 ,$rowData->po_no);
			else {
			xlsWriteLabel($y, $x+1 ,'');
			}

			if ($tempcontract <> $rowData->contract_no)
							xlsWriteLabel($y, $x+2 ,$rowData->contract_no);
							else {
							xlsWriteLabel($y, $x+2 ,'');
							}

				if ($tempvendor <> $rowData->vendor_name)
				xlsWriteLabel($y, $x+3 ,$rowData->vendor_name);
				else {
				xlsWriteLabel($y, $x+3 ,'');
				}

				if ($temppo <> $rowData->po_no)
					xlsWriteNumber($y, $x+4 ,$rowData->price);
					else {
					xlsWriteLabel($y, $x+4 ,'');
					}

				if ($temppo <> $rowData->po_no)
								xlsWriteNumber($y, $x+5 ,$rowData->quantity);
								else {
								xlsWriteLabel($y, $x+5 ,'');
								}

			xlsWriteNumber($y, $x+6 ,$rowData->qty_rcv);
			xlsWriteLabel($y, $x + 7,$rowData->freight_supplier);
			xlsWriteNumber($y, $x + 8,$rowData->oa);

			if ($temphandling <> $temphandling=$rowData->vendor_handling_name){
				xlsWriteLabel($y, $x + 9,$rowData->vendor_handling_name);
				xlsWriteNumber($y, $x + 10,$rowData->handling);
			}else {
					xlsWriteLabel($y, $x+9 ,'NONE');
					xlsWriteLabel($y, $x+10 ,'');
			}
			$sc_id=$rowData->stockpile_contract_id;

			if ($temppo <> $rowData->po_no){
				$y3=$ystart;
				$sql2 = "SELECT sc.`stockpile_contract_id`,v.`vehicle_name`,AVG(COALESCE(t.`unloading_price`,0)) AS ob FROM stockpile_contract sc
								LEFT JOIN TRANSACTION t ON t.`stockpile_contract_id`=sc.`stockpile_contract_id`
								LEFT JOIN `unloading_cost` uc ON uc.`unloading_cost_id`=t.`unloading_cost_id`
								LEFT JOIN vehicle v ON v.`vehicle_id`=uc.`vehicle_id`
								WHERE sc.`stockpile_contract_id`=$sc_id
								GROUP BY sc.`stockpile_contract_id`,v.`vehicle_name`
								";
				$resultData2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
				while ($rowData2 = $resultData2->fetch_object()) {

					xlsWriteLabel($y3, $x+11 ,$rowData2->vehicle_name);
					xlsWriteNumber($y3, $x + 12,$rowData2->ob);
					//xlsWriteLabel($y, $x+11 ,'test');
					//xlsWriteLabel($y, $x + 12,'test2');
					$y3=$y3+1;
				}
				$y2=$ystart;
				$sql3 = "SELECT gv.`general_vendor_name`,i.`price` FROM invoice_detail i
									LEFT JOIN contract c ON c.`contract_id`=i.`poid`
									LEFT JOIN account a ON a.`account_id`=i.`account_id`
									LEFT JOIN general_vendor gv ON gv.`general_vendor_id`=i.`general_vendor_id`
									LEFT JOIN `stockpile_contract` sc ON sc.`contract_id`=c.`contract_id`
									 WHERE (a.`account_no`=521000 OR a.`account_no`=520900) AND i.poid IS NOT NULL AND sc.`stockpile_contract_id`=$sc_id";
				$resultData3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
				while ($rowData3 = $resultData3->fetch_object()) {

					xlsWriteLabel($y2, $x+13 ,$rowData3->general_vendor_name);
					xlsWriteNumber($y2, $x + 14,$rowData3->price);
					//xlsWriteLabel($y, $x+11 ,'test');
					//xlsWriteLabel($y, $x + 12,'test2');
					$y2=$y2+1;
				}
				//xlsWriteLabel($y, $x+11 ,'test');
				//xlsWriteLabel($y, $x + 12,'test2');
			}




			$tempstockpile=$rowData->stockpile_name;
			$temppo=$rowData->po_no;
			$tempcontract=$rowData->contract_no;
			$tempvendor=$rowData->vendor_name;
			$tempqty=$rowData->quantity;
			$tempqtyrcv=$rowData->qty_rcv;
			$tempprice=$rowData->price;
			$temphandling=$rowData->vendor_handling_name;
			if($y3>$y2){
				$y=$y3;
			}
			else {
				$y=$y2;
			}

			$y =$y + 1;
			$ystart=$y;

	}



// memanggil function penanda akhir file excel
xlsEOF();
exit();

?>
