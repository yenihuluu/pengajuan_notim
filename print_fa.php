<?php

// PATH
require_once './assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';
	$namaFile = "Fixed_Asset.xls";


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


	

$sql = "SELECT a.`fixed_asset_id`,a.`StockpileName`, a.`AssetCode`, a.`DateofAcquisition`
		, a.`AssetType`, a.`AssetName`,a.Unit, a.`AcquisitionCost`, IFNULL(AmtDepre,0) AS AmtDepre
		, (a.`AcquisitionCost`- IFNULL(AmtDepre,0)) AS balance FROM 
		`fixed_asset` AS a LEFT JOIN
		 (SELECT `AssetCode`, SUM(`AmountDepre`) AS AmtDepre FROM `trx_fixedasset`
		GROUP BY `AssetCode`) AS b
		ON
			a.`AssetCode` = b.`AssetCode`";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

xlsWriteLabel($y,$x,'Stockpile');
xlsWriteLabel($y,$x + 1,'Code');
xlsWriteLabel($y,$x + 2,'Acquisition');
xlsWriteLabel($y,$x + 3,'Type');
xlsWriteLabel($y,$x + 4,'Name');
xlsWriteLabel($y,$x + 5,'Unit');
xlsWriteLabel($y,$x + 6,'Cost');
xlsWriteLabel($y,$x + 7,'Depre');
xlsWriteLabel($y,$x + 8,'Balance');
$y =$y + 1;
while ($rowData = $resultData->fetch_object()) {	
		
			xlsWriteLabel($y, $x ,$rowData->StockpileName);
			xlsWriteLabel($y, $x + 1,$rowData->AssetCode);
			xlsWriteLabel($y, $x + 2,$rowData->DateofAcquisition);
			xlsWriteLabel($y, $x + 3,$rowData->AssetType);
			xlsWriteLabel($y, $x + 4,$rowData->AssetName);
			xlsWriteNumber($y, $x + 5,$rowData->Unit);
			xlsWriteNumber($y, $x + 6,$rowData->AcquisitionCost);	
			xlsWriteNumber($y, $x + 7,$rowData->AmtDepre);
			xlsWriteNumber($y, $x + 8,$rowData->balance);		
			$y =$y + 1;
	}
	


// memanggil function penanda akhir file excel
xlsEOF();
exit();

?>