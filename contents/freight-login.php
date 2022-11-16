<?php

// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

$readonlyProperty = '';
$disabledProperty = '';
$whereProperty = '';

$freightId = "";


if(isset($_POST['freightId']) && $_POST['freightId'] != '') {
    $freightId = $_POST['freightId'];
}

$sql = "SELECT CONCAT(f.freight_code, ' - ' , f.freight_supplier) as freight_name, fl.freight_login_id, fl.freight_username, DATE_FORMAT(fl.created_at, '%d %b %Y %H:%i:%s') AS entry_date2, mg.group_name AS group_name
        FROM freight_login fl LEFT JOIN freight f ON f.freight_id = fl.freight_id
        LEFT JOIN master_group mg on mg.master_group_id = fl.group_id
        WHERE f.freight_id = {$freightId}
        ORDER BY fl.created_at DESC 
        ";
$resultData = $myDatabase->query($sql, MYSQLI_STORE_RESULT);


?>
<script type="text/javascript">
    $(document).ready(function(){	//executed after the page has loaded
        
        $.extend($.tablesorter.themes.bootstrap, {
            // these classes are added to the table. To see other table classes available,
            // look here: http://twitter.github.com/bootstrap/base-css.html#tables
            table      : 'table table-bordered',
            header     : 'bootstrap-header', // give the header a gradient background
            footerRow  : '',
            footerCells: '',
            icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
            sortNone   : 'bootstrap-icon-unsorted',
            sortAsc    : 'icon-chevron-up',
            sortDesc   : 'icon-chevron-down',
            active     : '', // applied when column is sorted
            hover      : '', // use custom css here - bootstrap class may not override it
            filterRow  : '', // filter row class
            even       : '', // odd row zebra striping
            odd        : ''  // even row zebra striping
        });

        // call the tablesorter plugin and apply the uitheme widget
        $("#tabTable").tablesorter({
            // this will apply the bootstrap theme if "uitheme" widget is included
            // the widgetOptions.uitheme is no longer required to be set
            theme : "bootstrap",

            widthFixed: true,

            headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

            // widget code contained in the jquery.tablesorter.widgets.js file
            // use the zebra stripe widget if you plan on hiding any rows (filter widget)
            widgets : [ 'zebra', 'filter', 'uitheme' ],
                    
            headers: { 0: { sorter: false, filter: false } },

            widgetOptions : {
                // using the default zebra striping class name, so it actually isn't included in the theme variable above
                // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
                zebra : ["even", "odd"],
                        
            //    filter_functions : {
            //        2: true,
            //    },
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
            cssGoto  : ".pagenum",

            // remove rows from the table to speed up the sort of large tables.
            // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
            removeRows: false,
            // output string - default is '{page}/{totalPages}';
            // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
            output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

        });
        
        $('#tabTable a').click(function(e){
            e.preventDefault();
            //alert(this.id);
            $("#modalErrorMsg").hide();
            
            //alert(this.id);
            var linkId = this.id;
            var menu = linkId.split('|');
            if (menu[0] == 'edit') {
                $("#modalErrorMsg").hide();
                $('#EditFreightLoginModal').modal('show');
    //            alert($('#addNew').attr('href'));
                $('#EditFreightLoginModalForm').load('forms/edit-freight-login.php', {freightLoginId: menu[2],freightId: $('input[id="freightId"]').val()}, iAmACallbackFunction2());
                $('#loading').css('visibility','hidden');	//and hide the rotating gif
            } else if (menu[0] == 'delete') {
                alertify.set({ labels: {
                    ok     : "Yes",
                    cancel : "No"
                } });
                alertify.confirm("Are you sure want to delete this record?", function(e) {
                    if (e) {
                        $.ajax({
                            url: './data_processing.php',
                            method: 'POST',
                            data: { action: 'delete_freight_login',
                                    freightLoginId: menu[2]
                            },
                            success: function(data){
                                var returnVal = data.split('|');
                                if(parseInt(returnVal[3])!=0)	//if no errors
                                {
                                    //alert(msg);
                                    alertify.set({ labels: {
                                        ok     : "OK"
                                    } });
                                    alertify.alert(returnVal[2]);
                                    if(returnVal[1] == 'OK') {
                                        $('#freight-login-data').load('tabs/freight-login-data.php', {freightId: $('input[id="freightId"]').val()}, iAmACallbackFunction2());
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

    function iAmACallBackFunction2() {
        $("#freight-login-data").fadeIn("slow");
    }
</script>
<input type="hidden" id="freightId" name="freightId" value="<?php echo $freightId; ?>">
<div width="100%" style="overflow-x: auto; overflow-y: visible;">
    <table class="tablesorter" id="tabTable" style="font-size: 9pt;">
        <thead>
            <tr>
                <th>Action</th>
                <th>Freight Name</th>
                <th>Username</th>
                <th>Group Name</th>
                <th>Entry Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
            if($resultData !== false && $resultData->num_rows > 0) {
                while($rowData = $resultData->fetch_object()) {
                ?>
            <tr>
                <td>
                <div style="text-align: center">
                        <a href="#" id="edit|freight-login|<?php echo $rowData->freight_login_id; ?>" role="button" title="Edit"><img src="assets/ico/gnome-edit.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                        <a href="#" id="delete|freight-login|<?php echo $rowData->freight_login_id; ?>" title="Delete"><img src="assets/ico/gnome-trash.png" width="18px" height="18px" style="margin-bottom: 5px;" /></a>
                </div>
                </td>
                <td><?php echo $rowData->freight_name; ?></td>
                <td><?php echo $rowData->freight_username; ?></td>
                <td><?php echo $rowData->group_name; ?></td>
				<td><?php echo $rowData->entry_date2; ?></td>
            </tr>
                <?php
                }
            } else {
            ?>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

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
</div>