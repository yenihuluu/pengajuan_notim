<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));


// PATH

require_once '../assets/include/path_variable.php';


// Session

require_once PATH_INCLUDE . DS . 'session_variable.php';


// Initiate DB connection

require_once PATH_INCLUDE . DS . 'db_init.php';


$whereProperty = '';

$period = '';

if (isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['periodFrom']) && $_POST['periodFrom'] != '' && isset($_POST['paymentSchedule']) && $_POST['paymentSchedule'] != '') {
    $periodFrom = $_POST['periodFrom'];
    $periodTo = $_POST['periodTo'];
    $paymentSchedule = $_POST['paymentSchedule'];

}

$sql = "select *, c.company_name, s.stockpile_name, mr.requester, mpf.pic_name, lc.name as logbook_category, lic.name as inv_category, mb.bank_name from logbook l left join company c on l.company_id = c.company_id left join stockpile s on l.stockpile_id = s.stockpile_id 
left join master_requester mr on l.master_requester_id = mr.id left join master_pic_finance mpf on l.master_pic_finance_id = mpf.id
left join logbook_category lc on l.logbook_category_id = lc.id left join logbook_inv_category lic on lic.logbook_category_id = lc.id
left join master_bank mb on l.master_bank_id = mb.master_bank_id
where l.payment_schedule = '{$paymentSchedule}' and l.request_date_ho BETWEEN '{$periodFrom}' AND '{$periodTo}'";

$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

?>

<form method="post" action="reports/logbook-report-xls.php">

    <input type="hidden" id="periodFrom" name="periodFrom" value="<?php echo $periodFrom; ?>"/>
    <input type="hidden" id="periodTo" name="periodTo" value="<?php echo $periodTo; ?>"/>
	<input type="hidden" id="paymentSchedule" name="paymentSchedule" value="<?php echo $paymentSchedule; ?>"/>

    <button class="btn btn-success">Download XLS</button>

</form>


<table class="table table-bordered table-striped" style="font-size: 8pt;">

    <thead>

    <tr>

        <th>Request Date HO</th>

        <th>Request Date</th>

        <th>Month</th>

        <th>Week</th>

        <th>Email Time(Document Received HO)</th>

        <th>Email Time(APP)</th>

        <th>Validate Invoice Receive Date</th>

        <th>Payment Schedule</th>

        <th>Company</th>

        <th>Stockpile</th>

        <th>Requester</th>

        <th>PIC Finance</th>

        <th>Category</th>

        <th>Advance Number</th>

        <th>INV Category</th>

        <th>Vendor Name</th>

        <th>Remarks</th>

        <th>MV Name</th>

        <th>QTY PKS</th>

        <th>Invoice Value</th>

        <th>Payment Date</th>

        <th>Status</th>

        <th>Paid Time</th>

        <th>12:00</th>

        <th>1 Day</th>

        <th>Bank</th>

        <th>PV Number</th>

        <th>Outstanding</th>

        <th>To Be Paid</th>

        <th>Paid</th>

        <th>Paid Remarks</th>

        <th>Shipment Code</th>
    </tr>

    </thead>

    <tbody>

    <?php

    if ($result === false) {

        echo 'wrong query';
        echo $sql;
    } else {

        while ($row = $result->fetch_object()) {


            ?>


            <tr>

                <td style="text-align: left;"><?php echo $row->request_date_ho; ?></td>

                <td style="text-align: left;"><?php echo $row->request_date; ?></td>

                <td style="text-align: left;"><?php echo $row->request_month; ?></td>

                <td style="text-align: left;"><?php echo $row->request_week; ?></td>

                <td style="text-align: left;"><?php echo $row->email_time; ?></td>

                <td style="text-align: left;"><?php echo $row->email_time_app; ?></td>

                <td style="text-align: left;"><?php echo $row->inv_receive; ?></td>

                <td style="text-align: left;"><?php echo $row->payment_schedule; ?></td>

                <td style="text-align: left;"><?php echo $row->company_name; ?></td>

                <td style="text-align: left;"><?php echo $row->stockpile_name; ?></td>

                <td style="text-align: left;"><?php echo $row->requester; ?></td>

                <td style="text-align: left;"><?php echo $row->pic_name; ?></td>

                <td style="text-align: left;"><?php echo $row->logbook_category; ?></td>

                <td style="text-align: left;"><?php echo $row->advance_number; ?></td>

                <td style="text-align: left;"><?php echo $row->inv_category; ?></td>

                <td style="text-align: left;"><?php echo $row->vendor_name; ?></td>

                <td style="text-align: left;"><?php echo $row->remarks; ?></td>

                <td style="text-align: left;"><?php echo $row->mv_name; ?></td>

                <td style="text-align: left;"><?php echo $row->qty_pks; ?></td>

                <td style="text-align: right;"><?php echo number_format($row->invoice_value, 0, ".", ",") ?></td>

                <td style="text-align: left;"><?php echo $row->payment_date; ?></td>

                <td style="text-align: left;"><?php echo $row->status; ?></td>

                <td style="text-align: left;"><?php echo $row->paid_time; ?></td>

                <td style="text-align: left;"><?php echo $row->status_time; ?></td>

                <td style="text-align: left;"><?php echo $row->status_day; ?></td>

                <td style="text-align: left;"><?php echo $row->bank_name; ?></td>

                <td style="text-align: left;"><?php echo $row->pv_number; ?></td>

                <td style="text-align: right;"><?php echo number_format($row->outstanding, 0, ".", ",") ?></td>

                <td style="text-align: right;"><?php echo number_format($row->to_be_paid, 0, ".", ",") ?></td>

                <td style="text-align: right;"><?php echo number_format($row->paid, 0, ".", ",") ?></td>

                <td style="text-align: left;"><?php echo $row->paid_remarks; ?></td>

                <td style="text-align: left;"><?php echo $row->shipment_code; ?></td>
            </tr>

            <?php

        }

    }

    ?>

    </tbody>

</table>