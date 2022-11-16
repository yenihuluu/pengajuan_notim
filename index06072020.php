<?php

// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Global
require_once PATH_INCLUDE.DS.'global_variable.php';

require_once PATH_INCLUDE.DS.'check_session.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

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

if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 1) {
            $allowNotimMenu = true;
        } elseif($row->module_id == 2) {
            $allowPayment = true;
        } elseif($row->module_id == 3) {
            $allowContract = true;
        } elseif($row->module_id == 4) {
            $allowSales = true;
        } elseif($row->module_id == 35) {
            $allowAdminReport = true;
        } elseif($row->module_id == 6) {
            $allowConfiguration = true;
        } elseif($row->module_id == 22) {
            $allowPKSReport = true;
        } elseif($row->module_id == 23) {
            $allowCashPayment = true;
        } elseif($row->module_id == 24) {
            $allowBSReport = true;
        } elseif($row->module_id == 28) {
            $allowPO = true;
        } elseif($row->module_id == 33) {
            $allowAccountingReport = true;
        } elseif($row->module_id == 32) {
            $allowFinanceReport = true;
        } elseif($row->module_id == 34) {
            $allowTaxReport = true;
        } elseif($row->module_id == 36) {
            $allowFeeReport = true;
        } elseif($row->module_id == 38) {
            $allowDataVendor = true;
        } elseif($row->module_id == 37) {
            $allowInvoice = true;
        } elseif($row->module_id == 39) {
            $allowPrivilegeConfiguration = true;
        } elseif($row->module_id == 40) {
            $allowAccConfiguration = true;
        } elseif($row->module_id == 41) {
            $allowTaxConfiguration = true;
        } elseif($row->module_id == 43) {
            $allowNewNotim = true;
        } elseif($row->module_id == 44) {
            $allowShipmentCost = true;
        } elseif($row->module_id == 45) {
            $allowSalesAgreement = true;
        } elseif($row->module_id == 48) {
            $allowSaldoAwal = true;
        } elseif($row->module_id == 51) {
            $allowPosting = true;
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
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
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
        <link rel="stylesheet" href="assets/extensions/alertify.js-0.3.11/themes/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="assets/extensions/alertify.js-0.3.11/themes/alertify.default.css" />
        <script type="text/javascript" src="assets/extensions/alertify.js-0.3.11/lib/alertify.min.js"></script>

        <!-- Add select2.js -->
        <link rel="stylesheet" type="text/css" href="assets/extensions/select2-3.5.1/select2.css" />
        <script type="text/javascript" src="assets/extensions/select2-3.5.1/select2.js"></script>
        
        <!-- Add jquery-number.js https://github.com/customd/jquery-number -->
        <script type="text/javascript" src="assets/js/jquery.number.min.js"></script>
        <script type="text/javascript" src="assets/js/jquery.idle.js"></script>
		
		<script type="text/javascript">
				 $(document).idle({
				  onIdle: function(){
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
				  idle: 600000,
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
            
            #loading {	/* hiding the rotating gif graphic by default */
                visibility:hidden;
            }
            
            #pageContent {
                overflow: auto;
            }
            
            .error {
                color:red;
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
                        if($_SESSION['companyId'] == 2) {
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
                            <?php  if($allowNotimMenu || $allowNewNotim) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Nota Timbang <b class="caret"></b></a>
                                <ul class="dropdown-menu">
									<?php  if($allowNotimMenu) { ?>
                                    <li><a href="#transaction">Input Nota Timbang</a></li>
                                    <li><a href="#search-transaction">Search Nota Timbang</a></li>
									<?php } ?>
									<?php if($allowNewNotim) {?>
									<li class="divider"></li>
									<!--<li><a href="#transaction-new">Input Nota Timbang (NEW)</a></li>-->
									<li><a href="#notim-new">Input Nota Timbang (NEW)</a></li>
									<li><a href="#search-notim-new">Search Nota Timbang (NEW)</a></li>
									<!--<li><a href="#search-transaction-new">Search Input Nota Timbang (NEW)</a></li>-->
									<li><a href="#search-timbangan">Import Data Timbangan</a></li>
									
									<?php }?>	
                                </ul>
                            </li>
                            <?php }?>
							
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
											<li><a href="#posting-mutasi">Posting Mutasi</a></li>
											
										<?php } ?>

									</ul>
								</li>
							<?php } ?>

							<?php if($allowPayment || $allowCashPayment) {?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Payments <b class="caret"></b></a>
                                <ul class="dropdown-menu">
									<?php if($allowPayment) {?>
                                    <li><a href="#payment">Payment</a></li>
                                    <li><a href="#search-payment">Search</a></li>
									<li><a href="#paymentToTax">Approval Sheets</a></li>
									<?php }?>
									<!--<li><a href="#updatePayment">Update Data Payment</a></li>-->
									<?php if($allowCashPayment) {?>
									<li class="divider"></li>
                                    <li><a href="#pettyCash">Cash Payment</a></li>
                                    <li><a href="#search-pettyCash">Search</a></li>
									<?php }?>	
                                </ul>
                            </li>
							<?php }?>
                            <?php
                            if($allowInvoice) {
                            ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Invoice <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#invoice">Invoice</a></li>
                                    <!--<li><a href="#updateInvoice">Update Data Invoice</a></li>-->
									<li><a href="#invoiceReport">Invoice Report</a></li>
                                </ul>
                            </li>
                            <?php
                            }
                            if($allowContract) {
                            ?>
                             <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contracts <b class="caret"></b></a>
                                <ul class="dropdown-menu">
									<li><a href="#purchasing">Purchasing</a></li>
                                    <li><a href="#po-pks">Contracts</a></li>
                                    <li><a href="#contract">PO PKS</a></li>
									<li><a href="#adjustment">Adjustment</a></li>
                                </ul>
                            </li>
                            
                            <?php
                            }
                            
                            if($allowSales) {
                            ?>
							<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <b class="caret"></b></a>
                                <ul class="dropdown-menu">
									
									<li><a href="#sales-header">Input Sales Number</a></li>	
									<li><a href="#sales-detail">Sales Schedule</a></li>
									<?php if($allowSalesAgreement) {?>
                                    <li><a href="#sales">Sales Agreement</a></li>
									<li><a href="#adjustment-audit">Adjustment Audit</a></li>
									<?php }?>
                                </ul>
                            </li>
                            
                            <?php
                            }
                            
                           
                            ?>
							<?php if($allowAdminReport || $allowAccountingReport || $allowFinanceReport || $allowTaxReport || $allowFeeReport || $allowPKSReport) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports <b class="caret"></b></a>
                                <ul class="dropdown-menu">
								
								<?php if($allowAdminReport) { ?>

									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Admin Report</a>
									<ul class="dropdown-menu">
										<li><a href="#complete-report-admin">Nota Timbang (ADMIN)</a></li>
										<li><a href="#timbangan">Timbangan (ADMIN)</a></li>
										<li><a href="#po-summary-report-admin">PO Summary Report (ADMIN)</a></li>
										<li><a href="#oa-summary-report">OA Summary Report (ADMIN)</a></li>
										<li><a href="#daily-jurnal-report">Daily Jurnal Report (ADMIN)</a></li>
										<li><a href="#bank-book-report">Bank Book</a></li>
										
									</ul>
									</li>
									
								<?php } ?>		
								<?php if($allowAccountingReport) { ?>
									
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Accounting Report</a>
									<ul class="dropdown-menu">
										<li><a href="#complete-report">Nota Timbang</a></li>
										<li><a href="#bank-book-report">Bank Book</a></li>
										<li><a href="#management-report">End Stock Report</a></li>
										<li><a href="#general-ledger-report">General Ledger</a></li>
										<li><a href="#cogs-report">COGS Report Detail</a></li>
										<li><a href="#cogs-report-period">COGS Report Period</a></li>
										<li><a href="#fixed_asset">Fixed Asset</a></li>
										<li><a href="#inputJurnal">Input Jurnal Memorial</a></li>
										<li><a href="#bs-report">Balance Sheet Report</a></li>
										<li><a href="#pl-period-report">PL Period Report</a></li>
										<!--<li><a href="#pl-stockpile-report">PL Stockpile Report</a></li>
										<!--<li><a href="#stock-card-report">Stock Card</a></li>
										<li><a href="#sales-report">Sales Report</a></li>
										<li><a href="#sales-collection-report">Sales Collection Report</a></li>-->
									</ul>
									</li>
									
								<?php } ?>		
								<?php if($allowFinanceReport) { ?>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Finance Report</a>
									<ul class="dropdown-menu">
										<li><a href="#bank-book-report">Bank Book</a></li>
										<li><a href="#vendor-activity-report">Vendor Activity Report</a></li>
										<li><a href="#po-summary-report">PO Summary Report</a></li>
										<li><a href="#po-summary-report-curah">PO Summary Report (CURAH)</a></li>
										<li><a href="#po-detail-report">PO Detail Report</a></li>
										<li><a href="#contract-report">Contract Report</a></li>
										<li><a href="#unpaid-contract-report">Unpaid Contract</a></li>
										<li><a href="#outstanding-invoice-report">Outstanding Invoice</a></li>
										<!--<li><a href="#order-contract-report">Order Contract Report</a></li>-->
										<li><a href="#daily-summary-report">Daily Summary Report</a></li>
										<li><a href="#HutangDagangOA">HD OA</a></li>
										<li><a href="#HutangDagangOB">HD OB</a></li>
										<li><a href="#HutangDagangCurah">HD Curah</a></li>
										<li><a href="#logbook-report">Logbook Report</a></li>

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
								<?php if($allowTaxReport) { ?>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Tax Report</a>
									<ul class="dropdown-menu">
										<li><a href="#wht">WHT</a></li>
										<li><a href="#vat">VAT</a></li>
										<li><a href="#arusBarang">Arus Barang</a></li>
										<li><a href="#arusUang">Arus Uang</a></li>
									</ul>
									</li>
									
								<?php } ?>		
								<?php if($allowFeeReport) { ?>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Fee Report</a>
									<ul class="dropdown-menu">
										<li><a href="#FeeReport">Fee Report Detail</a></li>
										<li><a href="#FeeReportAll">Fee Report</a></li>
									</ul>
									</li>
									
								<?php } ?>		
								<?php if($allowPKSReport) { ?>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">PKS Report</a>
									<ul class="dropdown-menu">
										<li><a href="#dailyPks-report">Daily PKS Report</a></li>
										<li><a href="#input-dailyPks">Input Data</a></li>
									</ul>
									</li>
								
								<?php } ?>
								<?php if($allowShipmentCost) { ?>
                                   <li><a href="#shipment-cost-report">Shipment Cost</a></li>
								 <?php } ?>
								<?php if($allowSaldoAwal) { ?>
									<li><a href="#SaldoAkun">Saldo Awal Akun</a></li>
								<?php } ?>
								<?php if($allowSaldoAwal) { ?>
								
									<li class="dropdown-submenu">
									<a tabindex="-1" href="#">Purchasing Report</a>
									<ul class="dropdown-menu">
										<li><a href="#purchasing-summary">Summary</a></li>
										<li><a href="#purchasing-detail">Detail</a></li>
									</ul>
									</li>
								
								<?php } ?>
                                </ul>
                            </li>
                            <?php } ?>
                            
							
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Configuration <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    
                                    <li><a href="#user">Users</a></li>
									<?php if($allowPrivilegeConfiguration) { ?>
                                    <li><a href="#module">Modules</a></li>
									<?php
										}
									?>
									<?php if($allowAccConfiguration) { ?>
                                    <li><a href="#account">Accounts</a></li>
                                    <li><a href="#bank">Bank Accounts</a></li>
									<li><a href="#vehicle">Vehicles</a></li>
									<li><a href="#pic-finance">PIC Finance</a></li>
                                    <li><a href="#logbook-category">Logbook Category</a></li>
                                    <li><a href="#logbook-requester">Logbook Requester</a></li>
									<li><a href="#logbook">Logbook</a></li>
                                    <li><a href="#termin">Termin</a></li>
                                    <li><a href="#biaya-mutasi">Tipe Biaya Mutasi</a></li>
									<?php
										}
									?>
                                    <!--<li><a href="#category">Categories</a></li>-->
									<?php if($allowTaxConfiguration) { ?>
                                    <li><a href="#vendor-handling">Vendors Handling</a></li>
                                    <li><a href="#freight">Freights</a></li>
									 <li><a href="#freight-group">Freights Group</a></li>
                                    <li><a href="#vendor">Vendors</a></li>
                                    <li><a href="#customer">Customers</a></li>
                                    <li><a href="#labor">Labor Workers</a></li>
                                    <li><a href="#general-vendor">General Vendors</a></li>
									<li><a href="#tax">Tax</a></li>
									<li><a href="#stockpile">Stockpiles</a></li>
									<?php
										}
									?>
                                </ul>
                            </li>
							
                           
							
							<?php if($allowDataVendor) { ?>
							<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Data Vendor <b class="caret"></b></a>
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
							
							<?php if($allowPO) { ?>
							<li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">PO <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#po">Create PO</a></li>
                                    <li><a href="#poapprove">Approval PO</a></li>
									<li class="divider"></li>
									<li><a href="#uom">UOM</a></li>
									<li><a href="#groupitem">Group Item</a></li>
									<li><a href="#item">Item</a></li>
									<li><a href="#sign">Sign</a></li>
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
            
            <img id="loading" src="assets/img/ajax-loader.gif" alt="loading" />

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

        <script>
            function logout() {
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: { 
                        action: 'logout'
                    },
                    success: function(data){
                        window.open("<?php echo "{$urlProtocol}://{$urlHost}"; ?>", "_self");
                        
                    }
                });
            }
        </script>
    </body>
</html>
<?php
/*if(!empty($_SERVER['HTTP_CLIENT_IP'])){
$ip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else{
$ip=$_SERVER['REMOTE_ADDR'];
}*/
//$host= gethostname();
//$ip = gethostbyname($host);
//$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
?>
<?php //echo "Host Name Public=" .$hostname;?><br />
<?php //echo "IP Address=".$ip;?>

<?php

// Close DB connection
require_once PATH_INCLUDE.DS.'db_close.php';