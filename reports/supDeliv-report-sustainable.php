<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
$date = new DateTime();
$currentDate = $date->format('d/m/Y H:i:s');
$currentMonthYear = $date->format('Y/m');
$todayDate = $date->format('d/m/Y');
$currentYear = $date->format('Y');


$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowFilter = false;


if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 26) {
            $allowFilter = true;
        } 
    }
}

if(isset($_POST['periodFrom1']) && $_POST['periodFrom1'] != '' && isset($_POST['periodTo1']) && $_POST['periodTo1'] != '') {
    $periodFrom3 = $_POST['periodFrom1'];
    $periodTo3 = $_POST['periodTo1'];
    $periodFrom = DateTime::createFromFormat('d/m/Y',  $_POST['periodFrom1'])->format('Y/m/d');
    $periodTo = DateTime::createFromFormat('d/m/Y', $_POST['periodTo1'])->format('Y/m/d');
    $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m/%d') BETWEEN '{$periodFrom}' AND '{$periodTo}'";
}else{
   $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m') = '{$currentMonthYear}'";
//    $whereProperty = "DATE_FORMAT(tr.transaction_date, '%Y/%m') = '2020/10'";
}

/*$sql = "SELECT tr.transaction_id, ven.vendor_name AS vendorName,
        ven.vendor_code AS vendorCode,
        tr.transaction_date AS tanggal,
            lab.`labor_rules` AS labor_rules,
            con.contract_type AS contractType,
            st.stockpile_name AS stockpileName,
            SUM(tr.send_weight) AS sendweight,
            SUM(tr.shrink) AS susut,
            SUM(tr.quantity) AS inventory,
            SUM(tr.netto_weight) AS netto,
            SUM(tr.send_weight * tr.`unit_price`) AS AmountSendW,
            SUM(tr.shrink * tr.`unit_price`) AS AmountSusut,
            SUM(tr.quantity * tr.`unit_price`) AS AmountInventory,
            SUM(tr.freight_price * tr.freight_quantity) AS amountFreight,
            SUM(tr.handling_price * tr.quantity) AS amountHandling,
            CASE WHEN (lab.labor_rules = '1') THEN tr.unloading_price
                    WHEN (lab.labor_rules = '2') THEN 
                        CASE WHEN (tr.send_weight < tr.netto_weight)
                            THEN (tr.send_weight * tr.unloading_price) 
                        ELSE (tr.netto_weight * tr.unloading_price) 
                        END
                    WHEN (lab.labor_rules = '3') THEN (tr.netto_weight * tr.unloading_price)
                    WHEN (lab.labor_rules = '4') THEN (tr.send_weight * tr.unloading_price)
            ELSE NULL 
            END AS Unloading,
            tr.quantity AS qty
    FROM TRANSACTION tr
    LEFT JOIN stockpile_contract stc ON stc.stockpile_contract_id = tr.stockpile_contract_id
    LEFT JOIN contract con ON con.contract_id = stc.contract_id
    LEFT JOIN vendor ven ON ven.vendor_id = con.vendor_id
    LEFT JOIN stockpile st ON st.stockpile_id = stc.stockpile_id
    LEFT JOIN labor lab ON lab.labor_id = tr.labor_id
    WHERE  {$whereProperty}
    GROUP BY con.contract_type, ven.vendor_name, stockpileName
    ORDER BY tr.transaction_id DESC, ven.vendor_name asc"; */

    $sql = "SELECT  SUM(COALESCE(a.sendweight1,0)) AS sendweight,
            SUM(COALESCE(a.susut1,0)) AS susut,
            SUM(COALESCE(a.inventory1,0)) AS inventory,
            SUM(COALESCE(a.netto1,0)) AS netto,
            SUM(COALESCE(a.AmountSendW1,0)) AS AmountSendW,
            SUM(COALESCE(a.AmountSusut1,0)) AS AmountSusut,
            SUM(COALESCE(a.AmountInventory1,0)) AS AmountInventory,
            SUM(COALESCE(a.amountFreight1,0)) AS amountFreight,
            SUM(COALESCE(a.amountHandling1,0)) AS amountHandling,
            -- SUM(COALESCE(a.Unloading_retur,0)) + SUM(COALESCE(a.Unloading,0)) AS newUnloading, 
            SUM(COALESCE(a.Unloading,0)) AS newUnloading,
            SUM(COALESCE(a.fc_shrink1,0)) AS fc_shrink,
            a.* FROM 
                (
                SELECT tr.transaction_id, ven.vendor_name AS vendorName,
                ven.vendor_code AS vendorCode,
                tr.transaction_date AS tanggal,
                lab.`laborRules` AS laborRules,
                con.contract_type AS contractType,
                st.stockpile_name AS stockpileName,
                COALESCE(tr.send_weight,0) AS sendweight1,
                CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
                    THEN 0 
                ELSE COALESCE(tr.shrink,0) 
                END AS susut1,
                COALESCE(tr.quantity,0) AS inventory1,
                COALESCE(tr.netto_weight,0) AS netto1,
                CASE WHEN ven.rsb = 1 AND ven.ggl = 0 THEN 'ISCC'
            WHEN ven.rsb = 0 AND ven.ggl = 1 THEN 'GGL'
            WHEN ven.rsb = 0 AND ven.ggl = 0 THEN 'UNCERTIFIED'
        ELSE NULL END AS productClaim,

                CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                        THEN (COALESCE(tr.send_weight,0) * COALESCE(tr.`unit_cost`,0))
                ELSE (COALESCE(tr.send_weight,0) * COALESCE(tr.`unit_price`,0))
                END AS AmountSendW1,

                CASE WHEN con.contract_type = 'C' AND tr.transaction_type = 1
                    THEN 0 
                    ELSE
                        CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                            THEN (COALESCE(tr.shrink,0) * COALESCE(tr.`unit_cost`,0)) 
                            ELSE (COALESCE(tr.shrink,0) * COALESCE(tr.`unit_price`,0)) 
                        END
                END AS  AmountSusut1,

                CASE WHEN tr.mutasi_id > 0 AND con.langsir = 0
                        THEN (COALESCE(tr.quantity,0) * COALESCE(tr.`unit_cost`,0)) 
                            ELSE (COALESCE(tr.quantity,0) * COALESCE(tr.`unit_price`,0))   
                END AS AmountInventory1,

                (COALESCE(tr.freight_price,0) * COALESCE(tr.freight_quantity,0)) AS amountFreight1,
                (COALESCE(tr.handling_price,0) * COALESCE(tr.quantity,0)) AS amountHandling1,
                COALESCE(tr.unloading_price,0) AS Unloading,
                CASE WHEN fc.freight_cost_id != 0 AND ftx.tax_category = 1 AND ftx.tax_id != 0 
                    THEN (COALESCE(ts.amt_claim,0) / ((100-COALESCE(ftx.tax_value,0))/100))
                WHEN fc.freight_cost_id != 0 
                    THEN COALESCE(ts.amt_claim,0)
                ELSE 0 END AS fc_shrink1,
                COALESCE(tr.quantity,0) AS qty
                FROM TRANSACTION tr
            LEFT JOIN stockpile_contract stc ON stc.stockpile_contract_id = tr.stockpile_contract_id
            LEFT JOIN contract con ON con.contract_id = stc.contract_id
            LEFT JOIN vendor ven ON ven.vendor_id = con.vendor_id
            LEFT JOIN stockpile st ON st.stockpile_id = stc.stockpile_id
            LEFT JOIN labor lab ON lab.labor_id = tr.labor_id
            LEFT JOIN freight_cost fc ON fc.freight_cost_id = tr.freight_cost_id
            LEFT JOIN transaction_shrink_weight ts ON tr.transaction_id = ts.transaction_id
            LEFT JOIN tax ftx ON ftx.tax_id = tr.fc_tax_id
            WHERE  {$whereProperty} AND con.langsir = 0 and tr.transaction_type = 1
            GROUP BY tr.slip_no
            ) AS a
            GROUP BY a.contractType, a.vendorName, a.stockpileName
            ORDER BY a.transaction_id DESC, a.vendorName ASC";
     
    $resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

    // echo $sql;


// </editor-fold>

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

        
     /*   $("#contentTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets: ['zebra', 'filter', 'uitheme'],

            headers: {0: {sorter: false, filter: false},
             6: {sorter: false, filter: false},
            7: {sorter: false, filter: false}, 8: {sorter: false, filter: false},
            9: {sorter: false, filter: false}, 10: {sorter: false, filter: false}, 11: {sorter: false, filter: false}},

            widgetOptions: {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra: ["even", "odd"],
            }
        })
        .tablesorterPager({
            container: $(".pager"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
           
        }); */
        
    
       var $rows = $('#contentTable tr:not(.header)');
  //  var rows = Array.prototype.slice.call(table.querySelectorAll("tr:not(.header)")); 
        $('#supCode, #stockpile, #supName, #contractType').on('input', function() {
            var supname = $.trim($('#supName').val()).replace(/ +/g, ' ').toLowerCase();
            var supcode = $.trim($('#supCode').val()).replace(/ +/g, ' ').toLowerCase();
            var conType = $.trim($('#contractType').val()).replace(/ +/g, ' ').toLowerCase();
            var stockpile = $.trim($('#stockpile').val()).replace(/ +/g, ' ').toLowerCase();
           
            $rows.show().filter(function() {
                var supname1 = $(this).find('td:nth-child(2)').text().replace(/\s+/g, ' ').toLowerCase();
                var supcode1 = $(this).find('td:nth-child(3)').text().replace(/\s+/g, ' ').toLowerCase();
                var conType1 = $(this).find('td:nth-child(4)').text().replace(/\s+/g, ' ').toLowerCase();
                var stockpile1 = $(this).find('td:nth-child(5)').text().replace(/\s+/g, ' ').toLowerCase();
                return !~supname1.indexOf(supname) || !~supcode1.indexOf(supcode) || !~conType1.indexOf(conType) || !~stockpile1.indexOf(stockpile);
            }).hide();
        }); 

        var wto;
        $('#downloadxls').submit(function (e) {
            clearTimeout(wto);
            wto = setTimeout(function () {
                $.blockUI({ message: '<h4>Please wait...</h4>' }); 
                $('#dataContent').load('reports/supDeliv-report-sustainable.php', {
                    periodFrom1:document.getElementById('periodFrom3').value,
                    periodTo1:document.getElementById('periodTo3').value
                }, iAmACallbackFunction2);
            }, 1000);
        });

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
       alert(n);
        
    }

    
</script>

<style>
    input[type=search]{
        border-radius:10px;
        outline:0;
       
    }

    input[type=search]:focus{
        box-shadow:0 0 2px blue;
    }

    input[type=text]{
        border-radius:7px;
        outline:0;
       
    }

    input[type=search]:focus{
        box-shadow:0 0 2px blue;
    }
</style>

<form class="form-horizontal" method="post" id="downloadxls" action="reports/supDeliv_report_sustainable_xls.php">
<input type="hidden" name="periodFrom2" id="periodFrom2" value = "<?php echo $periodFrom ?>"/>
    <input type="hidden" name="periodTo2" id="periodTo2" value = "<?php echo $periodTo ?>"/>

    <input type="hidden" name="periodFrom3" id="periodFrom3" value = "<?php echo $periodFrom3 ?>"/>
    <input type="hidden" name="periodTo3" id="periodTo3" value = "<?php echo $periodTo3 ?>"/>
    <button class="btn btn-success">Download XLS</button>
    <br><br>
        
    <table class="table table-bordered table-striped"  id="contentTable" style="font-size: 9pt;">
        <thead>
            <tr class="header">
                <th >No</th>
                <!-- <th rowspan="2" width="50px">
                        <div style="text-align: center">
                        <label><b>Checklist</b></label>
                            <input type="checkbox" onClick="toggle(this)" />
                        </div>
                    </th> -->
                <th >Supplier</th>
                <th>Kode Suplier</th>
                <th>Contract Type</th>
                <th>Stockpile</th>
                <th>Send Weight</th>
                <th>Susut</th>
                <th>Inventory Weight</th>
                <th>Product Claim</th>
            </tr>

            <!-- <tr class="header">
                <th>Qty</th>
                <th>Amount</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr> -->

            <tr class="header">
                <th></th>
                <th><input type="search"  style = "width: 93%;" id="supName"  name="supName" placeholder="Type to search"></th>
                <th><input type="search" style = "width: 85%;" id="supCode"  name="supCode" placeholder="Type to search"> </th>
                <th><input type="search" style = "width: 85%;" id="contractType"  name="contractType"  placeholder="Type to search"> </th>
                <th><input type="search" style = "width: 85%;"  id="stockpile"  name="stockpile"  placeholder="Type to search"></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
               
            </tr>
        </thead>
        <tbody>
    <?php

        if ($resultData !== false && $resultData->num_rows > 0) {
            $no = 1;
            $totalSWQty = 0;            $totalSWAmount = 0;
            $totalSusutQty = 0;         $totalSusutAmount = 0;
            $totalInvQty = 0;          $totalInvAmount = 0;
            while ($row = $resultData->fetch_object()) {
                $AmountSW = 0;              $tempAmountSw = 0;
                $AmountSusut = 0;           $tempAmountSusut = 0;
                $AmountInventory = 0;       $tempAmountInventory = 0;
                        
                // $AmountSW = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountSendW);
                // $AmountSusut = ($row->fc_shrink) + ($row->AmountSusut);
                // $AmountInventory = ($row->amountFreight) + ($row->amountHandling) + ($row->newUnloading) + ($row->AmountInventory);

                $AmountSW = ($row->AmountSendW);
                $AmountSusut = ($row->AmountSusut);
                $AmountInventory = ($row->AmountInventory);
                ?>

                <tr>
                    <td><?php echo $no; ?></td>
                    <!-- <td> 
                        <div style="text-align: center">
                            <input type="checkbox" name="checks[]" value="<?php echo $row->transaction_id; ?>" />
                        </div>
                    </td> -->
                    <td><?php echo $row->vendorName; ?></td>
                    <td><?php echo $row->vendorCode; ?></td>
                    <td><?php echo $row->contractType; ?></td>
                    <td><?php echo $row->stockpileName; ?></td>
                    <td><?php echo number_format($row->sendweight, 0, ".", ","); ?></td>
                    <td><?php echo number_format($row->susut, 0, ".", ","); ?></td>
                    <td><?php echo number_format($row->inventory, 0, ".", ","); ?></td>
                    <td><?php echo $row->productClaim; ?></td>
                </tr>
                <?php
                    $totalSWAmount = $totalSWAmount + $AmountSW;
                    $totalSusutAmount = $totalSusutAmount + $AmountSusut;
                    $totalInvAmount = $totalInvAmount + $AmountInventory;
                
                    $totalSWQty = $totalSWQty + ($row->sendweight);
                    $totalSusutQty = $totalSusutQty + ($row->susut);
                    $totalInvQty = $totalInvQty + ($row->inventory);
                $no++;
            }
            ?>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total</b></td>
            <td><?php echo number_format($totalSWQty, 0, ".", ","); ?></td>
            <td><?php echo number_format($totalSusutQty, 0, ".", ","); ?></td>
            <td><?php echo number_format($totalInvQty, 0, ".", ","); ?></td>
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
</form>

<!-- <div class="pager">
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
</div> -->