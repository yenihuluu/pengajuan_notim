<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';

$mutasiId = $_POST['mutasiId'];
$sql = "SELECT md.*, mh.mutasi_header_id, mh.kode_mutasi, mh.total as total_header, td.percentage as termin_percentage, mbm.tipe_biaya, c.currency_id
        FROM mutasi_detail md
        LEFT JOIN mutasi_header mh ON mh.mutasi_header_id = md.mutasi_header_id
        LEFT JOIN termin_detail td ON md.termin_detail_id = td.id 
        LEFT JOIN master_termin mt ON mt.id = td.termin_id
        LEFT JOIN master_biaya_mutasi mbm on mbm.id = md.biaya_mutasi_id
        LEFT JOIN currency as c on md.currency_id = c.currency_id
        WHERE md.mutasi_header_id = {$mutasiId}
        ORDER BY md.mutasi_detail_id ASC 
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

                e.preventDefault();

                $("#modalErrorMsg").hide();
                $('#AddMutasiDetail').modal('show');
                $('#AddMutasiDetailForm').load('forms/add-mutasi-detail.php', {
                    mutasiDetailId: menu[2],
                    mutasiId: $('input[id="mutasiId"]').val()
                });

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
                                action: 'mutasi_detail',
                                actionType: 'DELETE',
                                mutasiDetailId: menu[2],
                                mutasiId: $('input[id="mutasiId"]').val(),
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
                                    alertify.alert(returnVal[1] + ' | '+ returnVal[2]);
                                    if (returnVal[1] == 'OK') {
                                        $('#mutasi-detail').load('tabs/mutasi-detail.php', {mutasiId: $('input[id="mutasiId"]').val()}, iAmACallbackFunction2);
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            }
        });

        $('#addMutasiDetail').click(function (e) {

            e.preventDefault();

            $("#modalErrorMsg").hide();
            $('#AddMutasiDetail').modal('show');
            $('#AddMutasiDetailForm').load('forms/add-mutasi-detail.php', {mutasiId: $('input[id="mutasiId"]').val()});
        });


        $("#mutasiDetailForm").validate({
            rules: {
                name: "required",
            },

            messages: {
                name: "Sub Category is a required field.",

            },

            submitHandler: function (form) {
                $.ajax({
                    url: './data_processing.php',
                    method: 'POST',
                    data: $("#mutasiDetailForm").serialize(),
                    success: function (data) {
                        var returnVal = data.split('|');
                        //                        alert(data);
                        if (parseInt(returnVal[3]) != 0)	//if no errors
                        {
                            //alert(msg);
                            if (returnVal[1] == 'OK') {
                                //show success message
                                $('#AddMutasiDetail').modal('hide');
                                $('#mutasi-detail').load('tabs/mutasi-detail.php', {mutasiId: $('input[id="mutasiId"]').val()});

                                alertify.set({
                                    labels: {
                                        ok: "OK"
                                    }
                                });
                                alertify.alert(returnVal[2]);
                            } else {
                                //show error message
                                document.getElementById('modalErrorMsg').innerHTML = returnVal[2];
                                $("#modalErrorMsg").show();
                            }
                        }
                    }
                });
            }
        });
    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

</script>

<a href="#addMutasiDetail" id="addMutasiDetail" role="button">
    <img src="assets/ico/add.png" width="18px" height="18px" style="margin-bottom: 5px;"/> Add Mutasi Detail
</a>
<input type="hidden" id="mutasiId" name="mutasiId" value="<?php echo $mutasiId ?>">
<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th>
        <th>Kode Mutasi</th>
        <th>Tipe Biaya</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Currency</th>
        <th>Total</th>
        <th>Termin Percentage</th>
        <th>Total per termin</th>
        <th>PPN</th>
        <th>PPN Converted</th>
        <th>PPH</th>
        <th>PPH Converted</th>
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
                        <a href="#" id="edit|mutasi|<?php echo $rowData->mutasi_detail_id; ?>" role="button"
                           title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                             style="margin-bottom: 5px;"/></a>
                        <a href="#" id="delete|mutasi|<?php echo $rowData->mutasi_detail_id;  ?>" role="button"
                           title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                               style="margin-bottom: 5px;"/></a>
                    </div>
                </td>
                <td><?php echo $rowData->kode_mutasi; ?></td>
                <td><?php echo $rowData->tipe_biaya; ?></td>
                <td><?php echo number_format($rowData->qty, 0, ".", ","); ?></td>
                <td><?php echo number_format($rowData->price, 2, ".", ","); ?></td>
                <?php if ($rowData->currency_id == '1'){?>
                    <td>IDR</td>
                    <?php
                } else { ?>
                    <td>USD</td>
                    <?php
                } ?>
                <td><?php echo number_format($rowData->total, 2, ".", ","); ?></td>
                <td><?php echo $rowData->termin_percentage; ?>%</td>
                <td><?php echo number_format($rowData->total_per_termin, 2, ".", ","); ?></td>
                <td><?php echo number_format($rowData->ppn, 0, ".", ","); ?>%</td>
                <td><?php echo number_format($rowData->ppn_converted, 2, ".", ","); ?></td>
				<td><?php echo floatval($rowData->pph); ?>%</td>
                <!-- <td><?php echo number_format($rowData->pph, 0, ".", ","); ?>%</td> -->
                <td><?php echo number_format($rowData->pph_converted, 2, ".", ","); ?></td>

            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="6" style="text-align: center">
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

<div id="AddMutasiDetail" class="modal hide fade" tabindex="-1" role="dialog"
     aria-labelledby="AddMutasiDetailLabel" aria-hidden="true">
    <form id="mutasiDetailForm" method="post" style="margin: 0px;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                    id="closeAddLogbookSubCategoryModal">×
            </button>
            <h3 id="AddMutasiDetailLabel">Add Mutasi Detail</h3>
        </div>
        <div class="alert fade in alert-error" id="modalErrorMsg" style="display:none;">
            Error Message
        </div>
        <div class="modal-body" id="AddMutasiDetailForm">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true" id="closeLogbookSubCategoryModal">Close</button>
            <button class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>