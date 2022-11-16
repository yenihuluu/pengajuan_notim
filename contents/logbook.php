<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$sql = "SELECT l.*, mr.requester, lc.name as category, lic.name as inv_category, c.company_name as company,
        sp.stockpile_name as stockpile, mpf.pic_name as pic_finance
        FROM logbook l
        LEFT JOIN company c ON c.company_id = l.company_id
        LEFT JOIN stockpile sp ON sp.stockpile_id = l.stockpile_id
        LEFT JOIN master_requester mr ON mr.id = l.master_requester_id
        LEFT JOIN master_pic_finance mpf ON mpf.id = l.master_pic_finance_id
        LEFT JOIN logbook_category lc ON lc.id = l.logbook_category_id
        LEFT JOIN logbook_inv_category lic ON lic.id = l.logbook_inv_category_id
        ORDER BY l.logbook_id DESC
        ";
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

                $('#dataContent').load('forms/add-logbook.php', {logbookId: menu[2]}, iAmACallbackFunction2);

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
                                action: 'logbook_data',
                                delete: 'true',
                                logbookId: menu[2]
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
                                        $('#dataContent').load('contents/logbook.php', {}, iAmACallbackFunction2);
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

<a href="#addNew|add-logbook" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px"
                                                             style="margin-bottom: 5px;"/> Add Logbook</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>Vendor</th>
        <th>Request Date HO</th>
        <th>Request Date</th>
        <th>Request Month</th>
        <th>Request Week</th>
        <th>Email Time</th>
        <th>Inv Receive</th>
        <th>Payment Schedule</th>
        <th>Company</th>
        <th>Stockpile</th>
        <th>Pic Finance</th>
        <th>Requester</th>
        <th>Category</th>
        <th>Inv Category</th>
        <th>MV Name</th>
        <th>QTY PKS</th>
        <th>Invoice Value</th>
        <th>Payment Date</th>
        <th>Paid Time</th>
        <th>Status</th>
        <th>Status Time</th>
        <th>Status Day</th>
        <th>PV Number</th>
        <th>Outstanding</th>
        <th>To Be Paid</th>
        <th>Paid</th>
        <th>Shipment Code</th>
        <th>Paid Remarks</th>
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
                        <a href="#" id="edit|logbook|<?php echo $rowData->logbook_id; ?>" role="button"
                           title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                             style="margin-bottom: 5px;"/></a>
                        <a href="#" id="delete|logbook|<?php echo $rowData->logbook_id; ?>" role="button"
                           title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                               style="margin-bottom: 5px;"/></a>
                    </div>
                </td>
                <td><?php echo $rowData->vendor_name; ?></td>
                <td><?php echo $rowData->request_date_ho; ?></td>
                <td><?php echo $rowData->request_date; ?></td>
                <td><?php echo $rowData->request_month; ?></td>
                <td><?php echo $rowData->request_week; ?></td>
                <td><?php echo $rowData->email_time; ?></td>
                <td><?php echo $rowData->inv_receive; ?></td>
                <td><?php echo $rowData->payment_schedule; ?></td>
                <td><?php echo $rowData->company; ?></td>
                <td><?php echo $rowData->stockpile; ?></td>
                <td><?php echo $rowData->pic_finance; ?></td>
                <td><?php echo $rowData->requester; ?></td>
                <td><?php echo $rowData->category; ?></td>
                <td><?php echo $rowData->inv_category; ?></td>
                <td><?php echo $rowData->mv_name; ?></td>
                <td><?php echo number_format($rowData->qty_pks, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->invoice_value, 0, ".", ","); ?></td>
                <td><?php echo $rowData->payment_date; ?></td>
                <td><?php echo $rowData->paid_time; ?></td>
                <td><?php echo $rowData->status; ?></td>
                <td><?php echo $rowData->status_time; ?></td>
                <td><?php echo $rowData->status_day; ?></td>
                <td><?php echo $rowData->pv_number; ?></td>

                <td><?php echo number_format($rowData->outstanding, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->to_be_paid, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->paid, 0, ".", ","); ?></td>


                <td><?php echo $rowData->shipment_code; ?></td>
                <td><?php echo $rowData->paid_remarks; ?></td>
            </tr>
            <?php
        }
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
