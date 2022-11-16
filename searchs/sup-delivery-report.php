<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connection
require_once PATH_INCLUDE.DS.'db_init.php';

// <editor-fold defaultstate="collapsed" desc="Functions">
$sql = "SELECT * FROM user_module WHERE user_id = {$_SESSION['userId']}";
$result = $myDatabase->query($sql, MYSQLI_STORE_RESULT);

$allowFilter = false;


if($result->num_rows > 0) {
    while($row = $result->fetch_object()) {
        if($row->module_id == 26) {
            $allowFilter = true;
        } 
    }
}

// </editor-fold>

?>

<script type="text/javascript">
    $(document).ready(function(){

            $('#dataContent').load('reports/supDeliv-report.php', {
                periodFrom1: $('input[id="periodFrom"]').val(),
                periodTo1: $('input[id="periodTo"]').val(),
            }, iAmACallbackFunction2);

            $('#searchForm').submit(function(e){
                e.preventDefault();
    //            alert('tes');
                if($('input[id="periodFrom"]').val() != '') {
                    $('#dataContent').load('reports/supDeliv-report.php', {
                        periodFrom1: $('input[id="periodFrom"]').val(),
                        periodTo1: $('input[id="periodTo"]').val(),
                    }, iAmACallbackFunction2);
                } else {
                    alertify.set({ labels: {
                        ok     : "OK"
                    } });
                    alertify.alert("Periode date is Empty.");
                }
            });
        

//         $('#downloadxls').submit(function(e){
//             e.preventDefault();
// //            alert('tes');
//                 $('#dataContent').load('reports/supDeliv_report_xls.php', {
//                     periodFrom: $('input[id="periodFrom2"]').val(),
//                     periodTo: $('input[id="periodTo2"]').val(),
//                 }, iAmACallbackFunction2);
//         });
    });
    
    $(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            //autoclose: true,
			orientation: "bottom auto",
            startView: 0
        });
    });

    function myFunction1() {
        var tempFrom = document.getElementById('periodFrom').value;
        $("#periodFrom2").val(tempFrom);  
    }

    function myFunction2() {
        var tempTo = document.getElementById('periodTo').value;
        $("#periodTo2").val(tempTo); 

    }
</script>


<div class="row-fluid">
    <div class="span6 lightblue">
        <!-- <form class="form-horizontal" method="post" id="downloadxls" action="reports/supDeliv_report_xls.php">
            <input type="hidden" name="periodFrom2" id="periodFrom2" />
            <input type="hidden" name="periodTo2" id="periodTo2"/>


            <button class="btn btn-success">Download XLS</button>
        </form> -->
    </div>

    <div class="span6 lightblue">
        <form class="form-horizontal" id ="searchForm" method="post">
            <div class="row-fluid">
                    <div class="span5 lightblue">
                        <input type="text" placeholder="periode from" tabindex="" id="periodFrom" name="periodFrom" onchange="myFunction1()"  data-date-format="dd/mm/yyyy" class="datepicker"  >
                     </div>
                    <div class="span5 lightblue">
                        <input type="text" placeholder="periode to" tabindex="" id="periodTo" name="periodTo" onchange="myFunction2()" data-date-format="dd/mm/yyyy" class="datepicker" >
                    </div>

                    <div  class="span1 lightblue">
                        <button type="submit" class="btn">Search</button>
                    </div>
            </div>
        </form>
    </div>
</div>

