<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE . DS . 'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE . DS . 'db_init.php';


//UBAH
$sql = "SELECT a.*, b.account_name AS accountName, d.stockpile_name AS stokpileName,
        CASE WHEN a.currencyID = '1' THEN 'Rp. ' 
        WHEN a.currencyID = '2' THEN '$ ' 
        ELSE NULL END AS currency
            FROM master_shipmentcost a
            LEFT JOIN account b ON b.account_id = a.account_id
            LEFT JOIN currency c ON c.currency_id = a.currencyID
            LEFT JOIN stockpile d ON d.stockpile_id = a.stockpile_id
            ORDER BY a.shipmentCost_id DESC";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

function createCombo($sql, $setvalue = "", $disabled = "", $id = "", $valuekey = "", $value = "", $uniq = "", $tabindex = "", $class = "", $empty = 1, $onchange = "", $boolAllow = false) {
    global $myDatabase;
    
    $result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);
    
    echo "<SELECT class='$class' tabindex='$tabindex' $disabled name='" . ($id . $uniq) . "' id='" . ($id . $uniq) . "' $onchange>";
    
    if($empty == 1) {
        echo "<option value=''>-- Please Select --</option>";
    } else if($empty == 2) {
        echo "<option value=''>-- Please Select Stockpile --</option>";
    } else if($empty == 3) {
        echo "<option value=''>-- Please Select Type --</option>";
    } else if($empty == 4) {
        echo "<option value=''>-- Please Select Payment For --</option>";
    } else if($empty == 5) {
        echo "<option value=''>-- Please Select Method --</option>";
    } else if($empty == 6) {
        echo "<option value=''>-- Please Select Buyer --</option>";
    } else if($empty == 7) {
        echo "<option value=''>-- All --</option>";
    }
    
    if($result !== false) {
        while ($combo_row = $result->fetch_object()) {
            if (strtoupper($combo_row->$valuekey) == strtoupper($setvalue))
                $prop = "selected";
            else
                $prop = "";

            echo "<OPTION value=\"" . $combo_row->$valuekey . "\" " . $prop . ">" . $combo_row->$value . "</OPTION>";
        }
    }
    
    if($boolAllow) {
        echo "<option value='INSERT'>-- Insert New --</option>";
    }
    
    echo "</SELECT>";
}
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
        })
            .tablesorterPager({
                container: $(".pager"),
                cssGoto: ".pagenum",
                removeRows: false,
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'
        });

        $(".select2combobox300").select2({
            width: "300%"
        });

        $(".select2combobox75").select2({
            width: "75%"
        });

        $(".select2combobox50").select2({
            width: "50%"
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

                $('#dataContent').load('forms/add-shipment-cost.php', {shipmentCostID: menu[2]}, iAmACallbackFunction2); //form inputan data saat TAMBAH

                $('#loading').css('visibility', 'hidden');	//and hide the rotating gif

            } 
        
            else if (menu[0] == 'delete') {
                alertify.set({
                    labels: {
                        ok: "Yes",
                        cancel: "No"
                    }
                });
                alertify.confirm("Are you sure want to delete this record?", function (e) {
                    if (e) {
                        $.ajax({
                            url: './request_processing.php', //UBAH file nya (diluar folder)
                            method: 'POST',
                            data: {
                                action: 'delete_shipment_cost', //call file utk Delete (UBAH) /tabs/request-data.php
                                delete: 'true',
                                shipmentCostID: menu[2]
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
                                        $('#dataContent').load('contents/shipment-cost.php', {}, iAmACallbackFunction2);
                                        //BALIK KE HALAMAN AWAL (GANTI JUGA)
                                    }
                                }
                            }
                        });
                    }
                    return false;
                });
            } //END
        });

    });

    function iAmACallbackFunction2() {
        $("#dataContent").fadeIn("slow");
    }

    function rupiah($nilai = 0){ //ini adalah fungsi yg akan dipakai berkali" yt Rupiah. jadi parameter $nilai = 0 akan di lempar ke .php yg membutuhkan Currency
        $string = "Rp." .number_format($nilai);
        return $string;
    }
</script>



<script type="text/javascript">
$('#stockpileID').change(function () {
        resetShipmentCost('Stockpile');
        if (document.getElementById('stockpileID').value != '' && document.getElementById('stockpileID').value != 'INSERT') {
            resetShipmentCost(' Stockpile ');
            setShipmentCost(0, $('select[id="stockpileID"]').val(), 0);
        }
    });

    function resetShipmentCost() {
        document.getElementById('shipmentCode').options.length = 0;
        var x = document.createElement('option');
        x.value = '';
        x.text = '--  Select Stockpile --';
        document.getElementById('shipmentCode').options.add(x);

        $("#shipmentCode").select2({
            width: "300%",
            placeholder: "--  Select Stockpile --"
        });
    }

    function setShipmentCost(type, stockpileID, shipmentCode) {
        $.ajax({
            url: 'get_data_yeni.php',
            method: 'POST',
            data: {
                action: 'getShipmentCost', //Nama Function di dlm get_data_yeni.php
                stockpileID: stockpileID, // parameter yang akan di lempar ke get_data_yeni.php
                newShipmentCode: shipmentCode
            },
            success: function (data) {
                var returnVal = data.split('~');
                if (parseInt(returnVal[0]) != 0)	//if no errors
                {
                    //alert(returnVal[1].indexOf("{}"));
                    if (returnVal[1] == '') {
                        returnValLength = 0;
                    } else if (returnVal[1].indexOf("{}") == -1) {
                        isResult = returnVal[1].split('{}');
                        returnValLength = 1;
                    } else {
                        isResult = returnVal[1].split('{}');
                        returnValLength = isResult.length;
                    }

                    //alert(isResult);
                    if (returnValLength >= 0) {
                        document.getElementById('shipmentCode').options.length = 0;
                        var x = document.createElement('option');
                        x.value = '';
                        x.text = '-- Please Select --';
                        document.getElementById('shipmentCode').options.add(x);

                        $("#shipmentCode").select2({
                            width: "300%",
                            placeholder: "-- Please Select --"
                        });
                    }

                    for (i = 0; i < returnValLength; i++) {
                        var x = document.createElement('option');
                        resultOption = isResult[i].split('||');
                        x.value = resultOption[0];
                        x.text = resultOption[1];
                        document.getElementById('shipmentCode').options.add(x);
                    }

                    if (type == 1) {
                        $('#shipmentCode').find('option').each(function (i, e) {
                            if ($(e).val() == shipmentCode) {
                                $('#shipmentCode').prop('selectedIndex', i);

                                $("#shipmentCode").select2({
                                    width: "300%",
                                    placeholder: shipmentCode
                                });
                            }
                        });
                    }
                }
            }
        });
    }
</script>

<div class="row" style="background-color: #f5f5f5; 
            margin-bottom: 5px; padding-top: 15px; 
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;">
    <div class="offset3 span3">
        <form class="form-horizontal" action="reports/download_shipment-cost-xls.php" method="post" id="SearchData">
            <div class="control-group">
                <label class="control-label" for="stockpileID">Stockpile</span></label>
                <div class="controls">
                    <?php 
                    createCombo("SELECT stockpile_id, stockpile_name
                                FROM stockpile", "", "", "stockpileID", "stockpile_id", "stockpile_name", 
                                "", 1, "select2combobox300", 2);
                    ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="shipmentCode">Shipment Code</span></label>
                <div class="controls">
                    <?php 
                     createCombo("SELECT a.shipment_code as shipmentID, b.shipment_no as shipmentNo
                     FROM master_requester a, shipment b where a.shipment_code = b.shipment_id order by a.shipment_code desc", 
                     "", "", "shipmentCode", "shipment_id", "shipment_no", "", 1,"select2combobox300",  2);
                    ?>
                </div>
            </div>
                <div class="control-group">
                    <div class="controls">
                        <!--<button type="submit" class="btn" id="preview">Preview</button>-->
                        <button class="btn btn-success">Download XLS</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--forms/add-request.php [UBAH]--> 
<a href="#addNew|add-shipment-cost" id="addNew" role="button"><img src="assets/ico/add.png" width="18px" height="18px" 
                                                             style="margin-bottom: 5px;"/> Add Shipment Cost</a>

<table class="table table-bordered table-striped" id="contentTable" style="font-size: 9pt;">
    <thead>
    <tr>
        <th style="width: 100px;">Action</th> <!--Jangann lupa tambah kolom Entry Date -->
        <th>Stockpile</th>
        <th>Biaya</th>
        <th>Type</th>
        <th>Vendor</th>
        <th>Price/MT</th>
        <th>Flat Price/Additional</th>
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
                        <a href="#" id="edit|shipment-cost|<?php echo $rowData->shipmentCost_id; ?>" role="button"
                           title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px"
                                             style="margin-bottom: 5px;"/></a>
                        <a href="#" id="delete|shipment-cost|<?php echo $rowData->shipmentCost_id; ?>" role="button"
                           title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px"
                                               style="margin-bottom: 5px;"/></a>
                    </div>
                </td>
                <td><?php echo $rowData->stokpileName; ?></td>
                <td><?php echo $rowData->accountName; ?></td>
                <td><?php echo $rowData->tipe; ?></td>
                <td><?php echo $rowData->vendorName; ?></td>
                <td><?php echo $rowData->currency, " ", number_format ($rowData->price_MT, 2, ".", ","); ?></td>
                <td><?php echo $rowData->currency, " ",number_format ($rowData->flat_Price, 2, ".", ","); ?></td>
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
