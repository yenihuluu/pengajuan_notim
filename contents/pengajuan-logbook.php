<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$sql = "SELECT  pl.*,s.stockpile_name,b.bank_name,user.user_name FROM pengajuan_logbook pl
LEFT JOIN stockpile s ON s.stockpile_id = pl.stockpile_id
LEFT JOIN master_bank b on b.master_bank_id = pl.master_bank_id
LEFT JOIN user on user.user_id = pl.entry_by
WHERE pl.entry_by = {$_SESSION['userId']}
ORDER BY pl.id DESC";
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
            }
        }).tablesorterPager({
            container: $(".pager"),
            cssGoto: ".pagenum",
            removeRows: false,
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });

        $('#addNew').click(function (e) {
            e.preventDefault();
            loadContent($('#addNew').attr('href'));
        });

        $('#contentTable a').click(function (e) {
            e.preventDefault();
            $("#successMsgAll").hide();
            $("#errorMsgAll").hide();

            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/pengajuan-logbook.php', {pLogbookId: menu[2]}, iAmACallbackFunction2);

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
                                action: 'pengajuan_logbook_data',
                                actionType: 'DELETE',
                                pLogbookId: menu[2]
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
                                        $('#dataContent').load('contents/pengajuan-logbook.php', {}, iAmACallbackFunction2);
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

<a href="#addNew|pengajuan-logbook" id="addNew" role="button">
    <img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;"/> Add Pengajuan Logbook
</a>
<a href="print_p_logbook.php"><img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;" /> Download</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>ID</th>
        <th>Entry By</th>
        <th>Entry Date</th>
        <th>Status</th>
        <th>Incomplete Remarks</th>
        <th>Vendor</th>
        <th>Stockpile</th>
        <th>No Rek</th>
        <th>Bank Name</th>
        <th>Bank Branch</th>
        <th>Bank Account Name</th>
        <th>Qty</th>
        <th>Qty Price</th>
        <th>DPP</th>
        <th>PPN</th>
        <th>PPH</th>
        <th>Total</th>
        <th>Tax Remarks</th>
        <th>Remarks</th>
        <th>File</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($resultData !== false && $resultData->num_rows > 0) {
        while ($rowData = $resultData->fetch_object()) {
            ?>
            <tr>
                <td>
                    <?php if ($rowData->status != 1) { ?>
                        <div style="text-align: center;">
                            <a href="#" id="edit|logbook|<?php echo $rowData->id; ?>" role="button"
                               title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                                 style="margin-bottom: 5px;"/></a>
                            <a href="#" id="delete|logbook|<?php echo $rowData->id; ?>" role="button"
                               title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                                   style="margin-bottom: 5px;"/></a>
                        </div>
                    <?php } ?>
                </td>
                <td><?php echo $rowData->id; ?></td>
                <td><?php echo $rowData->user_name; ?></td>
                <td><?php echo $rowData->entry_date; ?></td>
                <td>
                    <?php
                    if ($rowData->status == 0) {
                        $status = 'Oncheck';
                    } elseif ($rowData->status == 1) {
                        $status = 'Complete';
                    } elseif ($rowData->status == 2) {
                        $status = 'Incomplete';
                    }
                    echo $status;
                    ?>
                </td>
                <td><?php echo $rowData->incomplete_remark; ?></td>
                <td><?php echo $rowData->vendor; ?></td>
                <td><?php echo $rowData->stockpile_name; ?></td>
                <td><?php echo $rowData->no_rek; ?></td>
                <td><?php echo $rowData->bank_name; ?></td>
                <td><?php echo $rowData->cabang_bank; ?></td>
                <td><?php echo $rowData->nama_akun_bank; ?></td>
                <td><?php echo number_format($rowData->qty, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->harga_qty, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->dpp, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->ppn, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->pph, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->total, 0, ".", ","); ?></td>
                <td><?php echo $rowData->tax_remark; ?></td>
                <td><?php echo $rowData->remark; ?></td>
                <td><?php echo "<a href='" . $rowData->file . "' target='_blank'>File</a>"; ?></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="16" style="text-align: center">
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
