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
$prediksiId = '';
$shipmentCode1 = '';
$prediksiIds = '';
$prediksiIds1 = '';


if(isset($_POST['prediksiId']) && $_POST['prediksiId'] != '') {
    $prediksiId = $_POST['prediksiId'];
    for ($i = 0; $i < sizeof($prediksiId); $i++) {
        if($prediksiIds == '') {
            $prediksiIds .= $prediksiId[$i];
            $prediksiIds1 .= $prediksiId[$i];
        } else {
            $prediksiIds .= ','. $prediksiId[$i];
            $prediksiIds1 .= ','. $prediksiId[$i];
        }
    }
}

//jika yg dipilih ALL
if($prediksiIds == ''){
    $sqlA = "SELECT prediction_id FROM accrue_prediction WHERE STATUS <> 1 ORDER BY prediction_id ASC";
    $resultA = $myDatabase->query($sqlA, MYSQLI_STORE_RESULT); 
    if($resultA !== false && $resultA->num_rows > 0) {
        while($rowA = $resultA->fetch_object()) {
            if($prediksiIds == '') {
                $prediksiIds .= $rowA->prediction_id;
            } else {
                $prediksiIds .= ','. $rowA->prediction_id;
            }
        }
    }
}

    $sql = "CALL sp_prediksi('{$prediksiIds}')"; 
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT); 
    // echo $sql;
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
        });

            $("#contentTable").tablesorter({
            theme: "bootstrap",

            widthFixed: true,

            headerTemplate: '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            widgets: ['zebra', '', 'uitheme'],

            headers: {0: {sorter: false, filter: false}},

            widgetOptions: {
                zebra: ["even", "odd"],
            }
        }).tablesorterPager({
            container: $(".pager"),
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

<form method="post" action="reports/prediksi-report-xls.php">
    <input type="hidden" id="prediksiIds" name="prediksiIds" value="<?php echo $prediksiIds; ?>" />
    <input type="hidden" id="temp" name="temp" value="<?php echo $prediksiIds1; ?>" />
    <button class="btn btn-success">Download XLS</button>
</form>

<!-- MENAMPILKAN DATA YG SUDAH DI BUAT JURNAL NYA DAN BELUM DIPAKAI DI INVOICE -->
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 8pt;">
<thead>
        <tr>
            <!-- HEADER -->
            <th  >No.</th>
            <th>Status</th>
            <?php if($prediksiIds1 == '' || $i > 1){ 
                echo "<th>"; 
                echo 'Prediction code';
                echo "</td>";
            }?>
            <th>Prediction detail code</th>
            <th>Cost</th>
            <th>General Vendor</th>
            <th>Account Name</th>
            <th>Max Charge</th>
            <th>Min Charge</th>
            <th>Qty Type</th>
            <th>Qty Value</th>
            <th>Price type</th>
            <th>UOM</th>
            <th>Price</th>
            <th>Total Amount</th>
            <th>Kurs</th>
            <th>Rupiah</th>
            <th>Stockpile Remarks</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if($result === false) {
        echo 'wrong query';
        die();
    } else {
        $no = 1;
        while($row = $result->fetch_object()) {
            
        // echo $row1->nilai_process_invoice;
            ?>
            <tr>
               <!-- HEADER --> 
                <td><?php echo $no; ?></td> 
                <?php 
                    if($row->journal_status == 1){
                        echo "<td style='font_weight: bold; color: Green;'>"; 
                        echo $row->journalText;
                        echo "</td>";
                    } else if($row->journal_status == 2){
                        echo "<td style='font_weight: bold; color: red;'>"; 
                        echo $row->journalText;
                        echo "</td>";
                    }
                ?>
                <?php if($prediksiIds1 == '' || $i > 1){ 
                    echo "<td >"; 
                    echo $row->prediction_code;
                    echo "</td>";
                  }
                ?>
                <td><?php echo $row->generate_code_detail; ?></td>
                <td><?php echo $row->tipe_biaya; ?></td>
                <td><?php echo $row->general_vendor_name; ?></td>
                <td><?php echo $row->accountName; ?></td>
                <td><?php echo number_format($row->maxused, 2, ".", ","); ?></td>
                <td><?php echo number_format($row->minused, 2, ".", ","); ?></td>
                <td><?php echo $row->coaType; ?></td>
                <td><?php echo number_format($row->qty, 3, ".", ","); ?></td>
                <td><?php echo $row->priceType; ?></td>
                <td><?php echo $row->coaType; ?></td>
                <td><?php echo $row->curr.''.number_format($row->priceMT, 2, ".", ","); ?></td>
                <td><?php echo $row->curr.''.number_format($row->total_amount, 2, ".", ","); ?></td>
                <td><?php echo 'Rp.'.number_format($row->exchange_rate, 0, ".", ","); ?></td>
                <td><?php echo 'Rp.'.number_format($row->in_rupiah, 2, ".", ","); ?></td>
                <td><?php echo $row->spRemarks; ?></td>
            </tr>
<?php $no++; } ?>
 <?php } ?>
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
