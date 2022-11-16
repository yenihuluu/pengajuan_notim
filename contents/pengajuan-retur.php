<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connectiongen
require_once PATH_INCLUDE . DS . 'db_init.php';

$allowApproveNotim = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 31) {
            $allowApproveNotim = true;
        }
    }
}

$sql = "SELECT pr.*,s.stockpile_name,u.user_name,pr.entry_date as tanggal_input, slipLama.slip_no as slip_lama, slipBaru.slip_no as slip_baru FROM pengajuan_retur pr
        LEFT JOIN USER u ON u.user_id = pr.entry_by
        LEFT JOIN stockpile s ON s.stockpile_id = pr.stockpile_id
        LEFT JOIN transaction slipLama ON slipLama.transaction_id = pr.slip_lama
        LEFT JOIN transaction slipBaru ON slipBaru.transaction_id = pr.slip_baru
        ORDER BY pr.entry_date DESC
        ";

$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowImport = true;

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
                    4: false
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

        $('#importContract').click(function (e) {
            e.preventDefault();

            $('#importModal').modal('show');
            $('#importModalForm').load('forms/contract-import.php');
        });

        $('#addNew').click(function (e) {

            e.preventDefault();
//            alert($('#addNew').attr('href'));
            loadContent($('#addNew').attr('href'));
        });
        $('#exportExcel').click(function (e) {
            $("#pengajuanGeneralForm").submit(); // Submit the for
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

                $('#dataContent').load('forms/pengajuan-retur.php', {
                    prId: menu[2],
                    _method: "UPDATE"
                }, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'approve') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/pengajuan-retur.php', {
                    prId: menu[2],
                    _method: "APPROVE"
                }, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'finish') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/pengajuan-retur.php', {
                    prId: menu[2],
                    _method: "FINISH"
                }, iAmACallbackFunction2);

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
                            url: './irvan.php',
                            method: 'POST',
                            data: {
                                action: 'pengajuan_return_data',
                                _method: 'DELETE',
                                prId: menu[2]
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
                                        $('#dataContent').load('contents/pengajuan-retur.php', {}, iAmACallbackFunction2);
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

<a href="#addNew|pengajuan-retur" id="addNew" role="button">
    <img src="assets/ico/add.png" width="18px" height="18px"
         style="margin-bottom: 5px;"/> Add Pengajuan Retur
</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>Status</th>
        <th>Slip Lama</th>
        <th>Stockpile</th>
        <th>Tanggal Notim</th>
        <th>Tanggal Return</th>
        <th>Slip Baru</th>
        <th>Alasan</th>
        <th>Status Notim</th>
        <th>Requester</th>
        <th>Request Date</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($resultData !== false && $resultData->num_rows > 0) {
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td>
                    <div style="text-align: center;">
                        <?php if ($allowApproveNotim) { ?>
                            <?php if ($rowData->status == 0) { ?>

                                <a href="#" id="approve|pr|<?php echo $rowData->id_p_retur; ?>" role="button"
                                   title="Approve"><img
                                            src="assets/ico/gnome-print.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>

                            <?php } elseif ($rowData->status == 1) { ?>

                                <a href="#" id="finish|pr|<?php echo $rowData->id_p_retur; ?>" role="button"
                                   title="Finish"><img
                                            src="assets/ico/gnome-print.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>
                            <?php } ?>

                        <?php } else { ?>
                            <?php if ($rowData->status == 0) { ?>

                                <a href="#" id="edit|pr|<?php echo $rowData->id_p_retur; ?>" role="button"
                                   title="Edit"><img
                                            src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>

                                <a href="#" id="delete|pr|<?php echo $rowData->id_p_retur; ?>"
                                   role="button"
                                   title="Delete"><img
                                            src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>

                            <?php } elseif ($rowData->status == 1) { ?>

                                <a href="#" id="finish|pr|<?php echo $rowData->id_p_retur; ?>" role="button"
                                   title="Finish"><img
                                            src="assets/ico/gnome-print.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </td>
                <?php
                if ($rowData->status == 1) {
                    echo "<td style='font_weight: bold; color: yellowgreen;'>";
                    echo "APPROVED";
                    echo "</td>";
                } elseif ($rowData->status == 2) {
                    echo "<td style='font_weight: bold; color: green;'>";
                    echo "FINISH";
                    echo "</td>";
                } else {
                    echo "<td style='font_weight: bold; color: blue;'>";
                    echo "PENGAJUAN";
                    echo "</td>";
                }
                ?>
                <td><?php echo $rowData->slip_lama; ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo $rowData->tanggal_notim; ?></td>
                <td><?php echo $rowData->tanggal_return; ?></td>
                <td><?php echo $rowData->slip_baru; ?></td>
                <td><?php echo $rowData->remarks; ?></td>
                <td><?php echo "LEVEL " . $rowData->status_notim; ?></td>
                <td><?php echo $rowData->user_name; ?></td>
                <td><?php echo $rowData->tanggal_input; ?></td>
            </tr>
        <?php }
    } else { ?>
        <tr>
            <td colspan="15">
                No data to be shown.
            </td>
        </tr>
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
<div class="row-fluid">

    LEVEL 1 = notim sudah dibayar(curah/oa/ob/handling), sudah dipakai untuk loading, dan ada payment di loading <br>
    LEVEL 2 = notim belum dibayar(curah/oa/ob/handling), sudah dipakai untuk loading, dan ada payment di loading<br>
    LEVEL 3 = notim sudah dibayar, sudah dipakai untuk loading, blm ada payment<br>
    LEVEL 4 = notim sudah dibayar, belum terpakai loading<br>
    LEVEL 5 = notim belum dibayar.

</div>