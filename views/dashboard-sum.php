<?php
error_rePOrting(E_ALL ^ (E_NOTICE | E_WARNING));
// PATH
require_once '../assets/include/path_variable.php';

// Session
require_once PATH_INCLUDE.DS.'session_variable.php';

// Initiate DB connectionre
require_once PATH_INCLUDE.DS.'db_init.php';

$_SESSION['menu_name'] = 'Dashboard-sum';


$date = new DateTime();
$currentDate = $date->format('dd/mm/yyyy');
$currentDate2 = $date->format('Y-m-d');
$currentDate3 = $date->format('dd/mm/yyyy');
// <editor-fold defaultstate="collapsed" desc="Variable for Contract Data">

?>

<script type="text/javascript">
    
    function getDBsum(requestDate,intervalmonth) {
		
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { action: 'getDBsum',
					requestDate: requestDate,
					intervalmonth: intervalmonth
            },
            success: function(data){
                if(data != '') {
                    document.getElementById('getDBsumDet').innerHTML = data;
					document.body.style.cursor = 'default';
                }
            }
        });
    }
	
    $(document).ready(function(){
              
     $('#dashboardsum').submit(function(e){
            e.preventDefault();
			document.body.style.cursor = 'wait';
			$.blockUI({ message: '<h4>Please wait...</h4>' }); 
			getDBsum(document.getElementById('requestDate').value,document.getElementById('intervalmonth').value);
        });
	/*
	$('#requestDate').change(function() {            
				//getDBsum(document.getElementById('requestDate').value,document.getElementById('intervalmonth').value);
                //$('#getDBsumDet').show();         
				//alert (document.getElementById('intervalmonth').value+document.getElementById('requestDate').value);
		});
        */
        var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0!
		var yyyy = today.getFullYear();

		if (dd < 10) {
		  dd = '0' + dd;
		}

		if (mm < 10) {
		  mm = '0' + mm;
		}

		today = dd + '/' + mm + '/' + yyyy;
		getDBsum(today,document.getElementById('intervalmonth').value);
    
    });
	
	 
</script>


<script type="text/javascript">
$(function() {
        //https://github.com/eternicode/bootstrap-datepicker
        $('.datepicker').datepicker({
            minViewMode: 0,
            todayHighlight: true,
            autoclose: true,
            startView: 0
        });
		});
</script>
<form class="form-horizontal" id="dashboardsum" method="post" autocomplete="off">
 <div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
		 <label>Date</label>
		<input type="text"  placeholder="DD/MM/YYYY" tabindex="" id="requestDate" name="requestDate" data-date-format="dd/mm/yyyy" class="datepicker" >
        </div>
		<div class="span1 lightblue">
        </div>
        <div class="span4 lightblue">
       <label>Interval(month)</label><input type="text" tabindex="" id="intervalmonth" name="intervalmonth"  value="1" > Last Month
        </div>
		<div class="span1">
		 <button class="btn btn-primary" >Submit</button>
        </div>
		</div>
	
	<div class="row-fluid" id="getDBsumDet">
    </div>
	<div class="row-fluid" style="margin-bottom: 7px;">   
        <div class="span3 lightblue">
		 <label><strong>Quantity in KG</strong></label>		
        
    </div>
</form>
