<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$sql = "SELECT ttr.* , v.vendor_name, c.contract_no, CONCAT(s.stockpile_code, ' - ', s.stockpile_name) as stockpile
        FROM transaction_timbangan_rev ttr
        LEFT JOIN vendor v on ttr.vendor_id = v.vendor_id
        LEFT JOIN stockpile_contract sc on ttr.stockpile_contract_id = sc.stockpile_contract_id
        LEFT JOIN contract c on sc.contract_id = c.contract_id
LEFT JOIN stockpile s on s.stockpile_id = (SUBSTRING(ttr.slip,1,2))
LEFT JOIN user_stockpile us on us.stockpile_id = s.stockpile_id
where us.user_id = '{$_SESSION['userId']}' and ttr.vendor_id != 0 and ttr.stockpile_contract_id != 0
        ORDER BY ttr.entry_date DESC";
		//echo $sql;
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
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

                filter_functions: {
                    4: true
                },
                // reset filters button
//                filter_reset : ".reset"

                // set the uitheme widget to use the bootstrap theme class names
                // this is no longer required, if theme is set
                // ,uitheme : "bootstrap"

            }
        })
            .tablesorterPager({

                // target the pager markup - see the HTML block below
                container: $(".pager"),

                // target the pager page select dropdown - choose a page
                cssGoto: ".pagenum",

                // remove rows from the table to speed up the sort of large tables.
                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                removeRows: false,
                // output string - default is '{page}/{totalPages}';
                // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

            });

        $('#addNew').click(function (e) {

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });

        $('#contentTable a').click(function (e) {
            e.preventDefault();
            //alert(this.id);
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/revisi-timbangan.php', {revisiTimbanganId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'delete') {
                alertify.set({
                    labels: {
                        ok: "Yes",
                        cancel: "No"
                    }
                });
                alertify.confirm("Are you sure want to delete this record?", function (e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: {
                                action: 'delete_revisi_timbangan',
                                revisiTimbanganId: menu[2]
                            },
                            success: function (data) {
                                var returnVal = data.split('|');
                                if (parseInt(returnVal[3]) != 0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({
                                        labels: {
                                            ok: "OK"
                                        }
                                    });
                                    alertify.alert(returnVal[2]);
                                    if (returnVal[1] == 'OK') {
                                        $('#dataContent').load('contents/revisi-timbangan.php', {}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }
        });

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

</script>

<!--<a href="#addNew|revisi-timbangan" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px"-->
<!--                                                                  style="margin-bottom: 5px;"/> Add Vendor Curah</a>-->

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>Slip</th>
		<th>Stockpile</th>
        <th>Vendor Name</th>
        <th>Contract No</th>
		<th>Vehicle No</th>
        <th>Driver</th>
        <th>Status</th>
        <th>Send Weight</th>
        <th>Bruto Weight</th>
        <th>Tarra Weight</th>
        <th>Netto Weight</th>
        <th>Note</th>

    </tr>
    </thead>
    <tbody>
    <?php
    if($resultData !== false && $resultData->num_rows > 0) {
    while ($rowData = $resultData->fetch_object()) {
    ?>
    <tr>
        <td>
            <div style="text-align: center;">
                <?php if ($rowData->status == 0) { ?>
                    <a href="#" id="edit|revisi-timbangan|<?php echo $rowData->transaction_rev_id; ?>" role="button"
                       title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                         style="margin-bottom: 5px;"/></a>
<!--                    <a href="#" id="delete|revisi-timbangan|--><?php //echo $rowData->transaction_rev_id; ?><!--" role="button"-->
<!--                       title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"-->
<!--                                           style="margin-bottom: 5px;"/></a>-->
                <?php } else { ?>
<!--                    <a href="#" id="delete|revisi-timbangan|--><?php //echo $rowData->transaction_rev_id; ?><!--" role="button"-->
<!--                       title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"-->
<!--                                           style="margin-bottom: 5px;"/></a>-->
                <?php } ?>
            </div>
        </td>
        <td><?php echo $rowData->slip; ?></td>
		<td><?php echo $rowData->stockpile; ?></td>
        <td><?php echo $rowData->vendor_name; ?></td>
        <td><?php echo $rowData->contract_no; ?></td>
		<td><?php echo $rowData->vehicle_no; ?></td>
        <td><?php echo $rowData->driver; ?></td>
        <?php if ($rowData->status == 1){
            $status = 'CONFIRM' ?>
            <td style="color: lime"><?php echo $status; ?></td>
        <?php } else {
            $status = 'NOT CONFIRM' ?>
            <td style="color: red"><?php echo $status; ?></td>
        <?php } ?>
        <td><?php echo $rowData->send_weight; ?></td>
        <td><?php echo $rowData->bruto_weight; ?></td>
        <td><?php echo $rowData->tara_weight; ?></td>
        <td><?php echo $rowData->netto_weight; ?></td>
        <td><?php echo $rowData->note; ?></td>
    </tr>
    <?php
    }
    } else {
    ?>
    <tr>
        <td colspan="4">
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
