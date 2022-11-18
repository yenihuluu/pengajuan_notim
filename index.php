<?php

// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Global
require_once PATH_INCLUDE . DS . 'global_variable.php';

require_once PATH_INCLUDE . DS . 'check_session.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

//$_SESSION['userId'] = 1;

$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowNotimMenu = false;
$allowPayment = false;
$allowCashPayment = false;
$allowContract = false;
$allowSales = false;
$allowAccountingReport = false;
$allowTaxReport = false;
$allowFinanceReport = false;
$allowFeeReport = false;
$allowConfiguration = false;
$allowPKSReport = false;
$allowBSReport = false;
$allowPO = false;
$allowDataVendor = false;
$allowInvoice = false;
$allowAdminReport = false;
$allowPrivilegeConfiguration = false;
$allowAccConfiguration = false;
$allowTaxConfiguration = false;
$allowNewNotim = false;
$allowShipmentCost = false;
$allowSalesAgreement = false;
$allowSaldoAwal = false;
$allowPosting = false;
$allowReportPadang = false;
$allowRevisiTimbangan = false;
$allowAdjustmentPayment = false;
$allowMenuUpdate = false;
$allowClosingDate = false;
$allowJurnalMemorial = false;
$allowApproveTicket = false;
$allowDoTheTicket = false;
$allowNewNotimPhoto = false;
$allowSustainable = false;
$allowPerdinHRD = false;



if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 1) {
            $allowNotimMenu = true;
        } elseif ($row->module_id == 2) {
            $allowPayment = true;
        } elseif ($row->module_id == 3) {
            $allowContract = true;
        } elseif ($row->module_id == 4) {
            $allowSales = true;
        } elseif ($row->module_id == 35) {
            $allowAdminReport = true;
        } elseif ($row->module_id == 6) {
            $allowConfiguration = true;
        } elseif ($row->module_id == 22) {
            $allowPKSReport = true;
        } elseif ($row->module_id == 23) {
            $allowCashPayment = true;
        } elseif ($row->module_id == 24) {
            $allowBSReport = true;
        } elseif ($row->module_id == 28) {
            $allowPO = true;
        } elseif ($row->module_id == 33) {
            $allowAccountingReport = true;
        } elseif ($row->module_id == 32) {
            $allowFinanceReport = true;
        } elseif ($row->module_id == 34) {
            $allowTaxReport = true;
        } elseif ($row->module_id == 36) {
            $allowFeeReport = true;
        } elseif ($row->module_id == 38) {
            $allowDataVendor = true;
        } elseif ($row->module_id == 37) {
            $allowInvoice = true;
        } elseif ($row->module_id == 39) {
            $allowPrivilegeConfiguration = true;
        } elseif ($row->module_id == 40) {
            $allowAccConfiguration = true;
        } elseif ($row->module_id == 25) {
            $allowAccConfiguration = true;
        } elseif ($row->module_id == 41) {
            $allowTaxConfiguration = true;
        } elseif ($row->module_id == 43) {
            $allowNewNotim = true;
        } elseif ($row->module_id == 44) {
            $allowShipmentCost = true;
        } elseif ($row->module_id == 45) {
            $allowSalesAgreement = true;
        } elseif ($row->module_id == 48) {
            $allowSaldoAwal = true;
        } elseif ($row->module_id == 51) {
            $allowPosting = true;
        } elseif ($row->module_id == 52) {
            $allowReportPadang = true;
        } elseif ($row->module_id == 53) {
            $allowRevisiTimbangan = true;
        } elseif ($row->module_id == 57) {
            $allowClosingDate = true;
        } elseif ($row->module_id == 55) {
            $allowAdjustmentPayment = true;
        } elseif ($row->module_id == 56) {
            $allowMenuUpdate = true;
        } elseif ($row->module_id == 58) {
            $allowJurnalMemorial = true;
        } elseif ($row->module_id == 59) {
            $allowApproveTicket = true;
        } elseif ($row->module_id == 60) {
            $allowDoTheTicket = true;
        } elseif ($row->module_id == 61) {
            $allowNewNotimPhoto = true;
        } elseif ($row->module_id == 64) {
            $allowSustainable = true;
        } elseif ($row->module_id == 67) {
            $allowPerdinHRD = true;
        }

    }
}
//$page = $_SERVER['PHP_SELF'];
//$sec = "600";
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>JPJ Inventory</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=9"/>
        <meta name="description" content="">
        <meta name="author" content="">
        <!--<meta http-equiv="refresh" content="<?php //echo $sec?>;URL='<?php //echo $page?>'">-->
        <script src="assets/js/jquery-1.10.2.min.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/jquery.form.js"></script>
        <script src="assets/js/jquery.validate.min.js"></script>
        <script src="assets/js/site_processing.js"></script>
        <script src="assets/js/bootstrap-paginator.min.js"></script>
        <script src="assets/js/jquery.tablesorter.js"></script>
        <script src="assets/js/jquery.tablesorter.pager.js"></script>
        <script src="assets/js/jquery.tablesorter.widgets.js"></script>
        <script src="assets/js/jquery.tablesorter.widgets-filter-formatter.js"></script>

        <!-- Add alertify.js -->
        <link rel="stylesheet" href="assets/extensions/alertify.js-0.3.11/themes/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="assets/extensions/alertify.js-0.3.11/themes/alertify.default.css"/>
        <script type="text/javascript" src="assets/extensions/alertify.js-0.3.11/lib/alertify.min.js"></script>

        <!-- Add select2.js -->
        <link rel="stylesheet" type="text/css" href="assets/extensions/select2-3.5.1/select2.css"/>
        <script type="text/javascript" src="assets/extensions/select2-3.5.1/select2.js"></script>

        <!-- Add jquery-number.js https://github.com/customd/jquery-number -->
        <script type="text/javascript" src="assets/js/jquery.number.min.js"></script>
        <script type="text/javascript" src="assets/js/jquery.idle.js"></script>

        <script type="text/javascript">
       
            $(":input").change(function(){
                    console.log("trigered");
                        // sessionStorage.setItem(idMenu+"."+this.id, this.value);
            });
            $(document).idle({
                onIdle: function () {
                    location.reload();
                },
                /*onActive: function(){
                   $('#status').toggleClass('idle').html('Active!');
                 },
                 onHide: function(){
                   $('#visibility').toggleClass('idle').html('Hidden!');
                 },
                 onShow: function(){
                   // Add a slight pause so you can see the change
                   setTimeout(function(){
                     $('#visibility').toggleClass('idle').html('Visible!');
                   }, 250);
                 },*/
                idle: 900000,
                keepTracking: true
            });
        </script>

        <!-- Add selectize.js -->
        <!--        <link rel="stylesheet" type="text/css" href="assets/extensions/selectize.js-master/dist/css/selectize.css" />
                <script type="text/javascript" src="assets/extensions/selectize.js-master/dist/js/standalone/selectize.js"></script>-->

        <!-- Le styles -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
        <link href="assets/css/bootstrap-combobox.css" rel="stylesheet">
        <link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet">
        <link href="assets/css/datepicker.css" rel="stylesheet">
        <link href="assets/css/tablesorter/theme.bootstrap.css" rel="stylesheet">
        <link href="assets/css/tablesorter/jquery.tablesorter.pager.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 150px;
                padding-bottom: 40px;
            }

            #loading { /* hiding the rotating gif graphic by default */
                visibility: hidden;
            }

            #pageContent {
                overflow: auto;
            }

            .error {
                color: red;
            }
        </style>
        <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="assets/ico/favicon.png">

        <script type="text/javascript">
            /*$(document).ajaxStop($.unblockUI);

            $(document).ready(function(){
                alertify.set({buttonReverse: true});


                $("#switch").click(function(e){
                    $.ajax({
                        url: './switch_processing.php',
                        method: 'POST',
                        data: 'action=switch&companyId=<?php echo $_SESSION['companyId']; ?>',
                        success: function(data) {
                            window.location = "/";
                        }
                    });
                });
            });*/
        </script>
    </head>

    <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="brand" href="#" id="switch">
                    <?php
                    if ($_SESSION['companyId'] == 2) {
                        echo "Jatim Pro";
                    } else {
                        echo "Inventory";
                    }
                    ?>
                </a>
                <p class="navbar-text pull-right">
                    <?php echo $_SESSION['userName']; ?> (<a href="login.php" onclick="logout()" class="navbar-link">logout</a>)
                </p>
                <div class="nav-collapse collapse">
                    <ul class="nav">
                        <li class="active"><a href="#dashboard">Dashboard</a></li>
                        <?php if ($allowNotimMenu || $allowNewNotim || $allowRevisiTimbangan || $allowNewNotimPhoto) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Nota Timbang <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">

                                    <?php if ($allowNotimMenu) { ?>
                                        <li><a href="#transaction">Input Nota Timbang</a></li>
										<li><a href="#transaction_preview">Preview Nota Timbang</a></li>
                                        <li><a href="#search-transaction">Search Nota Timbang</a></li>
                                    <?php } ?>
                                    <?php if ($allowNewNotim) { ?>
                                        <li class="divider"></li>
                                        <!--<li><a href="#transaction-new">Input Nota Timbang (NEW)</a></li>-->
                                        <li><a href="#notim-new">Input Nota Timbang (NEW)</a></li>
                                        <li><a href="#search-notim-new">Search Nota Timbang (NEW)</a></li>

                                        <!--<li><a href="#search-transaction-new">Search Input Nota Timbang (NEW)</a></li>-->
                                        <!--<li><a href="#search-timbangan">Import Data Timbangan</a></li>-->

                                    <?php } ?>
                                    <?php if ($allowRevisiTimbangan) { ?>
                                        <li><a href="#revisi-timbangan">Revisi Timbangan</a></li>
                                    <?php } ?>
									<?php if ($allowNewNotimPhoto) { ?> 
                                         <li class="divider"></li>
                                         <li><a href="#notim-new-photo">Input Nota Timbang Photo (NEW)</a></li>
                                         <li><a href="#search-notim-new-photo">Search Nota Timbang Photo (NEW)</a></li>
                                        <!-- <li><a><button class="btn btn-default" id="transfer" onclick= "transfer()">Transfer Data Timbangan</button></a></li> -->
                                    <?php } ?>
                                    <?php if ($allowPosting) { ?>
                                        <li class="divider"></li>
                                        <li><a href="#verification-notim">Verification</a></li>
                                        <li><a href="#posting-notim">Posting</a></li>
                                    <?php } ?>
                                    <li><a href="#pengajuan-retur">Pengajuan Retur Notim (TEST)</a></li>

                                </ul>
                            </li>
                        <?php } ?>

                        <!-- MUTASI############################################################################################## -->
                        <?php if ($allowNotimMenu || $allowNewNotim) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mutasi <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <?php if ($allowNotimMenu) { ?>
                                        <li><a href="#stock-transit">Stock Transit</a></li>
                                        <li><a href="#posting-transit">Posting Stock Transit</a></li>


                                    <?php } ?>
                                    <?php if ($allowPosting) { ?>
                                        <li class="divider"></li>
                                        <li><a href="#mutasi">Mutasi</a></li>
                                       <!-- <li><a href="#posting-mutasi">Posting Mutasi</a></li> -->
                                        <!--<li><a href="#transaction-new">Input Nota Timbang (NEW)</a></li>-->
                                        <!--<li><a href="#search-transaction-new">Search Input Nota Timbang (NEW)</a></li>-->
                                        <!--<li><a href="#search-timbangan">Import Data Timbangan</a></li>-->

                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } ?>
                        <!--######################################################################################################-->
                        <?php if ($allowPayment || $allowCashPayment) { 
									$jumlahTotal = '';
									$jumlah = '';
									$sql = "SELECT COUNT(*) AS jumlah FROM pengajuan_internalTF WHERE `status` = 0";
									$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
									if ($resultData->num_rows > 0) {
									while ($row = $resultData->fetch_object()) {
										if($row->jumlah > 0){
										$jumlah = $row->jumlah;
										}
										}
									}
									
								$jumlahTotal = $jumlah;
						?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-info"><?php echo $jumlahTotal; ?></span>Payments <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <?php if ($allowPayment) { ?>
                                        <li><a href="#payment">Payment</a></li>
										 <li><a href="#pengajuan-payment-view">Payment-New</a></li>
                                        <li><a href="#search-payment">Search</a></li>
                                        <li><a href="#paymentToTax">Approval Sheets</a></li>
										<li><a href="#payment-notim-salesOA">Payment Sales Freight</a></li>
										<li><a href="#approved-internal-transfer">Approved Internal Transfer (<?php echo $jumlah; ?>)</a></li>
                                    <?php } ?>
                                    <!--<li><a href="#updatePayment">Update Data Payment</a></li>-->
                                    <?php if ($allowCashPayment) {
									?>
									
                                        <li class="divider"></li>
                                        <li><a href="#pettyCash">Cash Payment</a></li>
                                        <li><a href="#search-pettyCash">Search</a></li>
                                        
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
						
						<?php if ($allowInvoice) { ?>
						<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Local Sales Invoice<b class="caret"></b></a>
						<ul class="dropdown-menu">
						<li><a href="#pengajuan-notim-sales">Create Invoice Receivable</a></li>
						<li><a href="#invoice-notim-sales">Register Invoice Receivable</a></li>
						<li><a href="#payment-notim-sales">Receivable Payment</a></li>
						</ul>
						</li>
						<?php } ?>

						<?php if ($allowInvoice) { 
						$jumlahTotal = '';
						$jumlah = '';
							$sql = "SELECT COUNT(*) AS jumlah FROM perdin_adv_settle a WHERE (CASE WHEN a.sa_method = 1 THEN a.approval_status ELSE 1 END) = 1 AND a.upload_status = 1 AND a.invoice_status = 0";
						$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
						if ($resultData->num_rows > 0) {
						while ($row = $resultData->fetch_object()) {
							if($row->jumlah > 0){
							$jumlah = $row->jumlah;
							}
							}
						}
						
						$jumlah2 = '';
							$sql2 = "SELECT COUNT(*) AS jumlah FROM pengajuan_general WHERE status_pengajuan = 0";
						$resultData2 = $myDatabase->query($sql2, MYSQLI_STORE_RESULT);
						if ($resultData2->num_rows > 0) {
						while ($row2 = $resultData2->fetch_object()) {
							if($row2->jumlah > 0){
							$jumlah2 = $row2->jumlah;
							}
							}
						}
						
						$jumlah3 = '';
							$sql3 = "SELECT COUNT(*) AS jumlah FROM pengajuan_payment WHERE dp_status = 0 AND email_date IS NOT NULL ";
						$resultData3 = $myDatabase->query($sql3, MYSQLI_STORE_RESULT);
						if ($resultData3->num_rows > 0) {
						while ($row3 = $resultData3->fetch_object()) {
							if($row3->jumlah > 0){
							$jumlah3 = $row3->jumlah;
							}
							}
						}
						
						$jumlah4 = '';
							$sql4 = "SELECT COUNT(*) AS jumlah FROM pengajuan_payment_sales_oa WHERE `status` = 0 AND email_date IS NOT NULL";
						$resultData4 = $myDatabase->query($sql4, MYSQLI_STORE_RESULT);
						if ($resultData4->num_rows > 0) {
						while ($row4 = $resultData4->fetch_object()) {
							if($row4->jumlah > 0){
							$jumlah4 = $row4->jumlah;
							}
							}
						}
						
						$jumlahTotal = $jumlah + $jumlah2 + $jumlah3 + $jumlah4;
						
						?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-info"><?php echo $jumlahTotal; ?></span> Invoice <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#pengajuan-logbook">Pengajuan Logbook</a></li>
                                <!-- <li><a href="#logbook">Logbook</a></li> -->
                                <li><a href="#logbook_new">Logbook-New</a></li>

                                    <li><a href="#register-invoice-general">Register Invoice General (<?php echo $jumlah2; ?>)</a></li>
                                    <li><a href="#invoice-notim-views">Register Invoice OA/OB/Hand (<?php echo $jumlah3; ?>)</a></li>
									<li><a href="#invoice-ls-oa">Register Invoice Local Sales Freight(<?php echo $jumlah4; ?>)</a></li>
                                    <li><a href="#invoice">Invoice</a></li>
                                    <!--<li><a href="#updateInvoice">Update Data Invoice</a></li>-->
									
									<li class="divider"></li>
									<li><a href="#register-invoice-perdin">Register Invoice Perdin (<?php echo $jumlah; ?>)</a></li>
									


                            </ul>
                        </li>
						<?php } ?>
                        <?php

                        if ($allowContract) {
							$jumlah = '';
							$sql = "SELECT COUNT(*) AS jumlah FROM purchasing WHERE `status` = 0 AND admin_input IS NULL";
						$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
						if ($resultData->num_rows > 0) {
						while ($row = $resultData->fetch_object()) {
							if($row->jumlah > 0){
							$jumlah = $row->jumlah;
							}
							}
						}
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-info"><?php echo $jumlah; ?></span>Contracts <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#purchasing">Purchasing (<?php echo $jumlah; ?>)</a></li>
                                    <li><a href="#po-pks">Contracts PKS</a></li>
									 <li><a href="#po-curah">Contracts Curah</a></li>
                                    <li><a href="#contract">PO PKS</a></li>
                                    <li><a href="#langsir">PO Langsir (SIT)</a></li>
                                    <li><a href="#adjustment">Adjustment</a></li>
                                </ul>
                            </li>

                            <?php
                        }

                        if ($allowSales) {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">

                                    <li><a href="#sales-header">Input Sales Number</a></li>
                                    <li><a href="#sales-detail">Sales Schedule< /a></li>
                                    <?php if ($allowSalesAgreement) { ?>
										<li><a href="#sales-local">Local Sales Agreement</a></li>
                                        <li><a href="#sales">Sales Agreement</a></li>
                                        <li><a href="#adjustment-audit">Adjustment Audit</a></li>
                                    <?php } ?>
                                </ul>
                            </li>

                            <?php
                        }


                        ?>
                        <?php if ($allowAdminReport || $allowAccountingReport || $allowFinanceReport || $allowTaxReport || $allowFeeReport || $allowPKSReport) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
                                <ul class="dropdown-menu">

                                    <?php if ($allowAdminReport) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Admin Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#complete-report-admin">Nota Timbang (ADMIN)</a></li>
                                                <!--<li><a href="#timbangan">Timbangan (ADMIN)</a></li>-->
                                                <?php if ($allowReportPadang) { ?>
                                                    <li><a href="#padang-report">OA Report (PADANG)</a></li>
                                                    <?php
                                                }
                                                ?>
                                                <!--<li><a href="#vehicle-report">Vehicle Report</a></li>
                                                <li><a href="#po-summary-report-admin">PO Summary Report (ADMIN)</a></li>
                                                <li><a href="#oa-summary-report">OA Summary Report (ADMIN)</a></li>
                                                <li><a href="#daily-jurnal-report">Daily Jurnal Report (ADMIN)</a></li>-->
                                                <li><a href="#bank-book-report">Bank Book</a></li>

                                            </ul>
                                        </li>

                                    <?php } ?>


                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#">Accounting Report</a>
                                        <ul class="dropdown-menu">
                                            <?php if ($allowAccountingReport) { ?>
                                                <li><a href="#complete-report">Nota Timbang</a></li>
                                                <li><a href="#bank-book-acc-report">List Payment Transaction Report</a>
                                                </li>
												<li><a href="#bank-book-accDetail-report">List Payment Transaction Detail Report</a>
                                                </li>
                                                <li><a href="#management-report">End Stock Report</a></li>
                                                <li><a href="#general-ledger-report">General Ledger</a></li>
												
												<li><a href="#general-ledger-report-saldo">General Ledger with balance</a></li>
                                                <li><a href="#cogs-report">COGS Report Detail</a></li>
                                                <li><a href="#langsir-report">Langsir Report Detail</a></li>
                                                <li><a href="#cogs-report-period">COGS Report Period</a></li>
                                                <li><a href="#fixed_asset">Fixed Asset</a></li>
                                                <li><a href="#inputJurnal">Input Jurnal Memorial</a></li>
                                                <li><a href="#bs-report">Balance Sheet Report</a></li>
                                                <li><a href="#pl-period-report">PL Period Report</a></li>
                                                <li><a href="#pks-traceability-report">PKS Traceability Report</a></li>
                                                <li><a href="#pks-traceability-with-code-report">Code PKS Traceability
                                                        Report</a></li>
                                                <li><a href="#report-posting-transit">Report Posting Stock Transit</a>
                                                </li>
                                                <li><a href="#odoo-report">Odoo Report</a></li>
                                                <li><a href="#odoo-summary-report">Odoo Summary Report </a></li>

                                                <li><a href="#stock-transit-report">Stock Transit Report</a></li>
                                            <?php } ?>
											
                                            <?php if ($_SESSION['userId'] == 19 || $_SESSION['userId'] == 47 || $_SESSION['userId'] == 5 || $_SESSION['userId'] == 200) { ?>

                                                <li><a href="#stock-mutation-report">Stock Mutation Report</a></li>
                                                <li><a href="#dwm-report">DWM Report</a></li>
                                                <li><a href="#sup-delivery-report">Suppliers Delivery</a></li>
                                                <li><a href="#summary-stock">Summary Stock</a></li>
                                                <li><a href="#pks-source-collection-report">PKS Source Collection
                                                        Report</a></li>

                                            <?php } ?>
                                            <li><a href="#pengajuan-retur-report">Notim Retur Report</a></li>

                                            <!--<li><a href="#pl-stockpile-report">PL Stockpile Report</a></li>
                                            <li><a href="#stock-card-report">Stock Card</a></li>
                                            <li><a href="#sales-report">Sales Report</a></li>
                                            <li><a href="#sales-collection-report">Sales Collection Report</a></li>-->
                                        </ul>
                                    </li>


                                    <?php if ($allowFinanceReport) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Finance Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#bank-book-report">Bank Book</a></li>
                                                <li><a href="#vendor-activity-report">Vendor Activity Report</a></li>
                                                <li><a href="#po-summary-report">PO Summary Report</a></li>
                                                <li><a href="#po-summary-report-curah">PO Summary Report (CURAH)</a>
                                                </li>
												<!--<li><a href="#po-curah-detail-report">PO Detail Report (CURAH)</a></li>-->
                                                <li><a href="#po-detail-report">PO Detail Report</a></li>
												<li><a href="#contract-detail-report">Contract Detail Report</a></li>
                                                <li><a href="#contract-report">Contract Report</a></li>
                                                <li><a href="#unpaid-contract-report">Unpaid Contract</a></li>
                                                <li><a href="#outstanding-invoice-report">Outstanding Invoice</a></li>
                                                <!--<li><a href="#order-contract-report">Order Contract Report</a></li>-->

                                                <li><a href="#daily-summary-report">Daily Summary Report</a></li>
                                                <li><a href="#HutangDagangOA">HD OA</a></li>
                                                <li><a href="#HutangDagangOB">HD OB</a></li>
                                                <li><a href="#HutangDagangCurah">HD Curah</a></li>
                                                <li><a href="#HutangDagangHandling">HD Handling</a></li>
                                                <li><a href="#logbook-report">Logbook Report</a></li>
                                                <li><a href="#vehicle-report">Vehicle Report</a></li>


                                            </ul>
                                        </li>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Down Payment Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#invoiceDP-report">invoice</a></li>
                                                <li><a href="#pettyCashDP-report">Petty Cash</a></li>
                                                <li><a href="#paymentDP-report">Payment</a></li>

                                            </ul>
                                        </li>

                                    <?php } ?>
                                    <?php if ($allowTaxReport) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Tax Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#wht">WHT</a></li>
												<li><a href="#wht-new">WHT (NEW)</a></li>
                                                <li><a href="#vat">VAT</a></li>
                                                <li><a href="#arusBarang">Arus Barang</a></li>
                                                <li><a href="#arusUang">Arus Uang</a></li>
                                            </ul>
                                        </li>

                                    <?php } ?>
                                    <?php if ($allowFeeReport) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Fee Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#FeeReport">Fee Report Detail</a></li>
                                                <li><a href="#FeeReportAll">Fee Report</a></li>
                                            </ul>
                                        </li>

                                    <?php } ?>
                                    <?php if ($allowPKSReport) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">PKS Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#dailyPks-report">Daily PKS Report</a></li>
                                                <li><a href="#input-dailyPks">Input Data</a></li>
                                            </ul>
                                        </li>

                                    <?php } ?>
                                    <?php if ($allowShipmentCost) { ?>
                                        <li><a href="#shipment-cost-report">Shipment Cost</a></li>
                                    <?php } ?>
                                    <?php if ($allowSaldoAwal) { ?>
                                        <li><a href="#SaldoAkun">Saldo Awal Akun</a></li>
                                    <?php } ?>
                                    <?php if ($allowSaldoAwal) { ?>

                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Purchasing Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#purchasing-summary">Summary</a></li>
                                                <li><a href="#purchasing-detail">Detail</a></li>
                                            </ul>
                                        </li>

                                    <?php } ?>
									<li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">IT Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#do-truck-photo-report">DO/Truck Photo Report</a></li>
                                            </ul>
                                        </li>
									 <?php if ($allowSustainable) { ?>
                                        <li class="dropdown-submenu">
                                            <a tabindex="-1" href="#">Sustainable Report</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#complete-report-sustain">NOTA TIMBANG (Sustain)</a></li>
                                                <li><a href="#summary-stock-sustainable">Summary Stock</a></li> 
                                                <li><a href="#dwm-report-sustainable">DWM Report</a></li> 
                                                <li><a href="#supDeliv-report-sustainable">Supplier Delivery Report</a></li>  
                                                <li><a href="#pks-traceability-sustainable">Code PKS traceability Report</a></li> 
                                                <li><a href="#sustain-report">Sustain Report</a></li>   
                                            </ul>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>


                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Configuration <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">

                                <li><a href="#user">Users</a></li>
								<li><a href="#modulesupport">Module Supports</a></li>

                                <?php if ($allowPrivilegeConfiguration) { ?>
                                    <li><a href="#module">Modules</a></li>
									<li><a href="#modulesupport">Module Supports</a></li>
                                    <?php
                                }
                                ?>
                                <?php if ($allowClosingDate) { ?>
                                    <li><a href="#closing-date">Closing Date</a></li>
                                <?php } ?>
                                <?php if ($allowAccConfiguration) { ?>
                                    <li><a href="#account">Accounts</a></li>
                                    <li><a href="#bank">Bank Accounts</a></li>
                                    <li><a href="#vehicle">Vehicles</a></li>
                                    <?php
                                }
                                ?>
                                <!--<li><a href="#category">Categories</a></li>-->
                                <?php if ($allowTaxConfiguration) { ?>
                                    <li><a href="#vendor-handling">Vendors Handling</a></li>
									<li><a href="#freight-local-sales">Freights Local Sales</a></li>
                                    <li><a href="#freight">Freights</a></li>
                                    <li><a href="#freight-group">Freights Group</a></li>
                                    <li><a href="#termin">Termin</a></li>
                                    <li><a href="#biaya-mutasi">Tipe Biaya Mutasi</a></li>
                                    <li><a href="#vendor">Vendors</a></li>
                                    <li><a href="#vendor-group">Vendor Groups</a></li>

                                    <li><a href="#customer">Customers</a></li>
                                    <li><a href="#labor">Labor Workers</a></li>
                                    <li><a href="#general-vendor">General Vendors</a></li>
                                    <li><a href="#tax">Tax</a></li>
                                    <li><a href="#stockpile">Stockpiles</a></li>
                                    <?php
                                }
                                ?>
                                <?php if ($allowFinanceReport) { ?>
                                    <li><a href="#revisi-timbangan">Revisi Timbangan</a></li>
									<li><a href="#no-email-account">No Email Account</a></li>
                                    <li><a href="#vendor-curah">Vendor Curah</a></li>
                                    <li><a href="#shipment-cost">Shipment Cost</a></li>
                                    <li><a href="#pic-finance">PIC Finance</a></li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#">Logbook</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#logbook-category">Logbook Category</a></li>
                                            <li><a href="#logbook-requester">Logbook Requester</a></li>
                                            <li><a href="#logbook">Logbook</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php if ($allowPO) { ?>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#">PO</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#uom">UOM</a></li>
                                            <li><a href="#groupitem">Group Item</a></li>
                                            <li><a href="#item">Item</a></li>
                                            <li><a href="#sign">Sign</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>

                            </ul>
                        </li>


                        <?php if ($allowDataVendor) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Data Vendor <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">

                                    <li><a href="#DataVendor">Data General Vendor</a></li>
                                    <li><a href="#DataVendorPKS">Data Vendor PKS</a></li>
                                    <li><a href="#DataVendorOA">Data Vendor OA</a></li>
                                    <li><a href="#DataVendorOB">Data Vendor OB</a></li>
                                    <li><a href="#DataVendorHC">Data Vendor HC</a></li>

                                    <li><a href="#vendor-branch">Update Vendor Branch</a></li>
                                </ul>
                            </li>
                            <?php
                        }
                        ?>


                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">PO <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#po">Create PO</a></li>
								<?php if ($allowFinanceReport) { ?>
                                <li><a href="#poapprove">Approval PO</a></li>
								 <?php
                        }
                        ?>
                            </ul>
                        </li>
			
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pengajuan <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#pengajuan-general">Pengajuan General</a></li>
                                <li><a href="#pengajuan-notim-views">Pengajuan OA/OB/Hand</a></li>
								<?php if ($allowTaxReport) { 
								$jumlah = '';
                                    $sql = "SELECT COUNT(*) AS jumlah FROM pengajuan_freight_cost WHERE `status` = 1";
                                $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
                                if ($resultData->num_rows > 0) {
                                while ($row = $resultData->fetch_object()) {
                                    if($row->jumlah > 0){
                                    $jumlah = $row->jumlah;
                                    } else {
									$jumlah = 0;	
									}
                                    }
                                } ?>
                               <li><a href="#pengajuan-freight-cost">Pengajuan Freight Cost (<?php echo $jumlah; ?>)</a></li>
							   <?php
							   }
							   ?>
                                <li><a href="#pengajuan-internal-transfer">Pengajuan Internal Transfer</a></li>
								<li><a href="#pengajuan-payment-sales">Pengajuan OA Penjualan Lokal</a></li>
								
								<li class="divider"></li>
								<li class="nav-header">PERJALANAN DINAS</li>
								<li><a href="#perdin-surat">Surat Perjalanan Dinas</a></li>
								<li><a href="#perdin-adv_settle">Pengajuan Advance/Settle/Reimburse</a></li>
								
								<li><a href="#perdin-item">Master Item</a></li>
								
                            </ul>
                        </li>
						<?php if ($allowAccountingReport) { ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Integrasi Odoo <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="#journal-odoo">Journals</a></li>
                            </ul>
                        </li>
						<?php } ?>
                        <?php if ($allowAdjustmentPayment) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Jurnal Memorial <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">
								<?php if ($allowJurnalMemorial) { ?>
                                                    <li><a href="#journal-memorial">Journals</a></li>
                                                    <?php
                                                }
                                                ?>
                                    <li><a href="#adjustmentOA">Adjustment HD OA</a></li>
                                    <li><a href="#adjustmentOB">Adjustment HD OB</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if ($allowMenuUpdate) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Update Data <b
                                            class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#updateInvoice">Update Invoice</a></li>
                                    <li><a href="#updatePayment">Update Payment</a></li>
                                    <li><a href="#updateCashPayment">Update Data Cash Payment</a></li>
                                </ul>
                            </li>
                        <?php } ?>
						<?php
                        if ($allowApproveTicket || $allowDoTheTicket) {
							$jumlah = '';
							$sql = "SELECT COUNT(*) AS jumlah FROM ticket_it_support WHERE `status` = 1 AND pic_id = '{$_SESSION['userId']}'";
						$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
						if ($resultData->num_rows > 0) {
						while ($row = $resultData->fetch_object()) {
							if($row->jumlah > 0){
							$jumlah = $row->jumlah;
							}
							}
						}
                        ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-info"><?php echo $jumlah; ?></span> IT Support <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#ticket">Add Ticket</a></li>
                        <?php
                            if ($allowApproveTicket) {
                        ?>
                                    <li><a href="#ticket-approve">Approve Ticket</a></li>
                        <?php
                            }
                            if ($allowDoTheTicket) {
                        ?>
                                    <li><a href="#ticket-assign">To Do Ticket (<?php echo $jumlah; ?>)</a></li>
                        <?php
                            }
                        ?>
                                </ul>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li><a href="#ticket">IT Support</a></li>
                        <?php
                        }
                        ?>
						<?php if ($allowPerdinHRD) { 
						$jumlah = '';
							$sql = "SELECT COUNT(*) AS jumlah FROM perdin_adv_settle a WHERE ((CASE WHEN a.sa_method = 1 THEN a.approval_status ELSE 0 END) = 0 OR (CASE WHEN a.sa_method = 1 THEN a.approval_status ELSE 3 END) = 3)  AND a.upload_status = 1 AND a.sa_method = 1";
						$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
						if ($resultData->num_rows > 0) {
						while ($row = $resultData->fetch_object()) {
							if($row->jumlah > 0){
							$jumlah = $row->jumlah;
							}
							}
						}
						?>
							<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="badge badge-info"><?php echo $jumlah; ?></span> Approval Perdin <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
							   <li><a href="#perdin-approval">Approval Perdin (<?php echo $jumlah; ?>)</a></li>
							   <li class="divider"></li>
                               <!--<li><a href="#perdin-user">Data Karyawan</a></li>-->
							   <li><a href="#perdin-benefit">Data Travel Expenses</a></li>
							   <li><a href="#general-vendor-employee">General Vendors Employee</a></li>
							   <li><a href="#perdin-level">Data Level/Jabatan</a></li>
							   
							   <li><a href="#perdin-divisi">Data Divisi</a></li>
							   <li><a href="#perdin-departement">Data Departemen</a></li>
							   
                            </ul>
							</li>
							<?php
                        }
                        ?>
                    </ul>
                    <!-- IF LOGGED IN -->


                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>

    <div class="container">

        <div id="pageContent"></div>

        <img id="loading" src="assets/img/ajax-loader.gif" alt="loading"/>

        <hr>

        <footer>
            <p>&copy; PT JATIM PROPERTINDO JAYA 2016</p>
        </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/bootstrap-transition.js"></script>
    <script src="assets/js/bootstrap-alert.js"></script>
    <script src="assets/js/bootstrap-modal.js"></script>
    <script src="assets/js/bootstrap-dropdown.js"></script>
    <script src="assets/js/bootstrap-scrollspy.js"></script>
    <script src="assets/js/bootstrap-tab.js"></script>
    <script src="assets/js/bootstrap-tooltip.js"></script>
    <script src="assets/js/bootstrap-popover.js"></script>
    <script src="assets/js/bootstrap-button.js"></script>
    <script src="assets/js/bootstrap-collapse.js"></script>
    <script src="assets/js/bootstrap-carousel.js"></script>
    <script src="assets/js/bootstrap-typeahead.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/bootstrap-datepicker.js"></script>
    <script src="assets/js/bootstrap-fileupload.min.js"></script>
    <script src="assets/js/printThis.js"></script>
    <script src="assets/js/bootstrap-combobox.js"></script>
    <script src="assets/js/bootstrap-affix.js"></script>

    <script type="text/javascript">
    // $(document).ready(function () {
    //     $(".dropdown-menu a").click(function(){
    //          var href = this.href.split('#');
    //          var idMenu = href[1];

    //          console.log(idMenu)
            
    //     });
    // });
      
        function logout() {
            $.ajax({
                url: './data_processing.php',
                method: 'POST',
                data: {
                    action: 'logout'
                },
                success: function (data) {
                    window.open("<?php echo "{$urlProtocol}://{$urlHost}"; ?>", "_self");

                }
            });
        }
    </script>
    </body>
    </html>


<?php


// Close DB connection
require_once PATH_INCLUDE . DS . 'db_close.php';?>