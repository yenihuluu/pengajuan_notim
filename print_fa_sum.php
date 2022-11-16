<?php

// PATH
require_once './assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';
	$namaFile = "Fixed_Asset_Sum.xls";


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


	

$sql = "SELECT distinct s.stockpile_name,ma.assettype,sum(distinct(fa.acquisitionCost)) as aqcost,
					SUM(tf.`AmountDepre`) AS depre,
		 SUM(tf.`BalanceDepre`)  AS bal
					FROM fixed_asset fa
					LEFT JOIN `master_assettype` ma ON ma.master_assettype_id = fa.master_assettype_id
					LEFT JOIN stockpile s ON s.stockpile_id = fa.stockpile_id
					LEFT JOIN `trx_fixedasset` tf ON tf.`AssetCode`=fa.`AssetCode`
					where tf.`DepreDate`=(select max(`DepreDate`) from trx_fixedasset)
					GROUP BY stockpile_name,ma.assettype
        ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

xlsWriteLabel($y,$x,'Stockpile');
xlsWriteLabel($y,$x + 1,'Type');
xlsWriteLabel($y,$x + 2,'AcquisitionCost');
xlsWriteLabel($y,$x + 3,'Depreciation');
xlsWriteLabel($y,$x + 4,'Balance');
$y =$y + 1;
while ($rowData = $resultData->fetch_object()) {	
			$stockpileName=$rowData->stockpile_name;
			$assetType=$rowData->assettype;
			xlsWriteLabel($y, $x ,$rowData->stockpile_name);
			xlsWriteLabel($y, $x + 1,$rowData->assettype);
			xlsWriteNumber($y, $x + 2,$rowData->aqcost);
			xlsWriteNumber($y, $x + 3,$rowData->depre);
			xlsWriteNumber($y, $x + 4,$rowData->bal);		
			$y =$y + 1;
			
			$sql2 = "SELECT DISTINCT s.stockpile_name,ma.assettype,fa.`AssetName`,fa.acquisitionCost,
					(tf.`AmountDepre`) AS depre,MIN(tf.`BalanceDepre`) AS bal
					FROM fixed_asset fa
					LEFT JOIN `master_assettype` ma ON ma.master_assettype_id = fa.master_assettype_id
					LEFT JOIN stockpile s ON s.stockpile_id = fa.stockpile_id
					LEFT JOIN `trx_fixedasset` tf ON tf.`AssetCode`=fa.`AssetCode`
					where s.stockpile_name = '$stockpileName' and ma.assettype='$assetType'
					and tf.`DepreDate`=(select max(`DepreDate`) from trx_fixedasset)
					GROUP BY stockpile_name,ma.assettype,fa.assetname ";
			$resultData2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
			
			xlsWriteLabel($y,$x,'Detail');
			xlsWriteLabel($y,$x + 1,'Name');
			xlsWriteLabel($y,$x + 2,'Acquisition');
			xlsWriteLabel($y,$x + 3,'Depre');
			xlsWriteLabel($y,$x + 4,'Bal');
			$y =$y + 1;
			while ($rowData2 = $resultData2->fetch_object()) {	
					
						xlsWriteLabel($y, $x + 1,$rowData2->AssetName);
						xlsWriteNumber($y, $x + 2,$rowData2->acquisitionCost);
						xlsWriteNumber($y, $x + 3,$rowData2->depre);
						xlsWriteNumber($y, $x + 4,$rowData2->bal);	
						$y =$y + 1;
				}
			$y =$y + 1;
	}
	


// memanggil function penanda akhir file excel
xlsEOF();
exit();

?>