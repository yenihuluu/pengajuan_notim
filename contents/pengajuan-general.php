<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connectiongen
require_once PATH_INCLUDE . DS . 'db_init.php';

$allowDelete = false;
$allowApprovePG = false;
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        if ($row->module_id == 54) {
            $allowApprovePG = true;
        }
//         elseif ($row->module_id == 18) {
//            $allowDelete = true;
//        }
    }
}


$sql = "SELECT pg.*, pgd.*, DATE_FORMAT(pg.invoice_date, '%d %b %Y') AS invoice_date, DATE_FORMAT(pg.input_date, '%d %b %Y') AS input_date, 
        DATE_FORMAT(pg.request_date, '%d %b %Y') AS request_date, u.user_name, s.stockpile_name, gv.general_vendor_name,
		CASE WHEN pgd.type = 4 THEN 'LOADING'
			 WHEN pgd.type = 5 THEN 'UMUM'
			 WHEN pgd.type = 6 THEN 'HO'
		ELSE '' END AS invoiceType, ur.user_name AS user_name2,
			pg.total_amount as amount_total
        FROM pengajuan_general pg
        LEFT JOIN pengajuan_general_detail pgd
	    ON pgd.pg_id = pg.pengajuan_general_id
        LEFT JOIN currency cur
            ON cur.currency_id = pgd.currency_id
        LEFT JOIN general_vendor gv
            ON gv.general_vendor_id = pgd.general_vendor_id
		LEFT JOIN USER u
			ON u.user_id = pg.entry_by
		LEFT JOIN stockpile s
			ON pg.stockpileId = s.stockpile_id
		LEFT JOIN USER ur
			ON pg.sync_by = ur.user_id
        WHERE pg.company_id = {$_SESSION['companyId']} 
        AND pg.entry_by = {$_SESSION['userId']}
        GROUP BY pg.pengajuan_general_id ORDER BY pg.pengajuan_general_id DESC LIMIT 3000
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
         //   $.blockUI({ message: '<h4>Please wait...</h4>' }); 
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

                $('#dataContent').load('forms/pengajuan-general.php', {pgId: menu[2]}, iAmACallbackFunction2);

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } else if (menu[0] == 'approve') {
                $("#dataSearch").fadeOut();
                $("#dataContent").fadeOut();

                $.blockUI({message: '<h4>Please wait...</h4>'});

                $('#loading').css('visibility', 'visible');

                $('#dataContent').load('forms/approve-pengajuan-general.php', {pgId: menu[2]}, iAmACallbackFunction2);

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
                                action: 'pengajuan_general_data',
                                _method: 'DELETE',
                                pgId: menu[2]
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
                                        $('#dataContent').load('contents/pengajuan-general.php', {}, iAmACallbackFunction2);
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

    function toggle(source) {
        checkboxes = document.getElementsByName('checks[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
          checkboxes[i].checked = source.checked;
        }
    }

</script>

<a href="#addNew|pengajuan-general" id="addNew" role="button">
    <img src="assets/ico/add.png" width="18px" height="18px"
         style="margin-bottom: 5px;"/> Add Pengajuan General
</a>
<button id="exportExcel" class="btn btn-success">Download XLS</button>
<form method="POST"  action="print_p_general.php" id="pengajuanGeneralForm">
    <table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
        <thead>
        <tr>
          
            <th width="50px">
                    <div style="text-align: center">
                        <input type="checkbox" onClick="toggle(this)" />
                    </div>
                </th>
            <th style="width: 100px;">Action</th>
            <th>Status</th>
            <th>Payment Type</th>
            <th>Request Payment Date</th>
            <th>Pengajuan No.</th>
            <th>Invoice Date</th>
            <th>Original Invoice No.</th>
            <th>Vendor</th>
            <th>Request Date</th>
            <th>Stockpile</th>
            <th>Amount</th>
            <th>Remarks</th>
            <th>Remarks Reject/Cancel</th>
            <th>Input By</th>
            <th>Input Date</th>
            <th>Reject/Cancel Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($resultData !== false && $resultData->num_rows > 0) {
            while ($rowData = $resultData->fetch_object()) {
                ?>
                <tr>
                    <td>
                        <div style="text-align: center">
                            <input type="checkbox" name="checks[]"
                                   value="<?php echo $rowData->pengajuan_general_id ?>"/>
                        </div>
                    </td>
                    <td>
                        <div style="text-align: center;">
                            <?php if ($rowData->status_pengajuan == 0 || $rowData->status_pengajuan == 4) { ?>
                                <a href="#" id="edit|pg|<?php echo $rowData->pengajuan_general_id; ?>" role="button"
                                   title="Edit"><img
                                            src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a>
                                <!-- <a href="#" id="delete|invoice|<?php echo $rowData->pengajuan_general_id; ?>"
                                   role="button"
                                   title="Delete"><img
                                            src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                            style="margin-bottom: 5px;"/></a> -->
                            <?php }else if ($rowData->status_pengajuan == 3 || $rowData->status_pengajuan == 1){ ?> 
                                <a href="#" id="edit|pg|<?php echo $rowData->pengajuan_general_id; ?>" role="button" title="Preview"><img src="assets/ico/gnome-print.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a> 
                            <?php } ?>
                        </div>
                    </td>
                    <?php
                    if ($rowData->status_pengajuan == 1) {
                        echo "<td style='font_weight: bold; color: #0e90d2;'>";
                        echo "INVOICE";
                        echo "</td>";
                    } elseif ($rowData->status_pengajuan == 3) {
                        echo "<td style='font_weight: bold; color: yellowgreen;'>";
                        echo "PAYMENT";
                        echo "</td>";
                    } elseif ($rowData->status_pengajuan == 4) {
                        echo "<td style='font_weight: bold; color: red;'>";
                        echo "RETURN INVOICE";
                        echo "</td>";
                    } elseif ($rowData->status_pengajuan == 2) {
                        echo "<td style='font_weight: bold; color: red;'>";
                        echo "CANCEL PENGAJUAN";
                        echo "</td>";
                    } else if($rowData->status_pengajuan == 0) {
                        echo "<td style='font_weight: bold; color: blue;'>";
                        echo "PENGAJUAN";
                        echo "</td>";
                    }
                    ?>
                    <?php
                    if ($rowData->payment_type == 1) {
                        echo "<td style='font_weight: bold; color: red;'>";
                        echo "URGENT";
                        echo "</td>";
                    } else {
                        echo "<td style='font_weight: bold; color: black;'>";
                        echo "NORMAL";
                        echo "</td>";
                    }
                    ?>
                    <td><?php echo $rowData->request_payment_date; ?></td>
                    <td><?php echo $rowData->pengajuan_no; ?></td>
                    <td><?php echo $rowData->invoice_date; ?></td>
                    <td><?php echo $rowData->invoice_no2; ?></td>
                    <td><?php echo $rowData->general_vendor_name; ?></td>
                    <td><?php echo $rowData->request_date; ?></td>
                    <td><?php echo $rowData->stockpile_name; ?></td>
                    <td><?php echo number_format($rowData->amount_total, 2, ".", ","); ?></td>
                    <td><?php echo $rowData->remarks; ?></td>
                    <td><?php echo $rowData->reject_remarks; ?></td>
                  
                    <td><?php echo $rowData->user_name; ?></td>
                    <td><?php echo $rowData->input_date; ?></td>
                    <td><?php echo $rowData->reject_date; ?></td>
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

</form>

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
