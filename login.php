<?php

// PATH
require_once 'assets/include/path_variable.php';

// Session
require_once 'assets/include/session_variable.php';

// Initiate DB connection
require_once PATH_ASSETS.DS.'/include/db_init.php';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Sign in &middot; JPJ Inventory</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
          body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
          }

          .form-signin {
            max-width: 300px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
               -moz-border-radius: 5px;
                    border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
               -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                    box-shadow: 0 1px 2px rgba(0,0,0,.05);
          }
          .form-signin .form-signin-heading,
          .form-signin .checkbox {
            margin-bottom: 10px;
          }
          .form-signin input[type="text"],
          .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
          }

        </style>
        <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="../assets/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="assets/ico/favicon.png">
        
        <script src="assets/js/jquery-1.10.2.min.js"></script>
        
        <!-- Add alertify.js -->
        <link rel="stylesheet" href="assets/extensions/alertify.js-0.3.11/themes/alertify.core.css" />
        <link rel="stylesheet" type="text/css" href="assets/extensions/alertify.js-0.3.11/themes/alertify.default.css" />
        <script type="text/javascript" src="assets/extensions/alertify.js-0.3.11/lib/alertify.min.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function(){	
                
                $("#submitLogin").click(function () {
                    var userEmail = $("input#userEmail").val(); 
                    var userPassword = $("input#userPassword").val(); 
                    var dataString = 'action=login&userEmail='+ userEmail + '&userPassword=' + userPassword;
                    $.ajax({
                        type: "POST",
                        url: "data_processing.php",
                        data: dataString, // serializes the form's elements.
                        success: function(data)
                        {
//                                alert(data); // show response from the php script.
                            var returnVal = data.split('|');

                            if (parseInt(returnVal[3]) != 0)	//if no errors
                            {
                                alertify.set({ labels: {
                                    ok     : "OK"
                                } });
								if (returnVal[1] == 'PASS') {
                                    window.location = "user-pass.php";
                                }
                                else if (returnVal[1] == 'OK') {
                                    window.location = "index.php";
                                } else {
                                    alertify.alert(returnVal[2]);
                                }
                            }
                        }
                      });

                     return false; // avoid to execute the actual submit of the form.
                });
            });

            
        </script>
    </head>

    <body>

        <div class="container">

            <form class="form-signin" id="formLogin">
                <h2 class="form-signin-heading">Jatim Propertindo</h2>
                <input type="text" class="input-block-level" id="userEmail" name="userEmail" placeholder="Email">
                <input type="password" class="input-block-level" id="userPassword" name="userPassword" placeholder="Password">
<!--                <select id="appl_id" name="appl_id">
                    <option value="1">Aplikasi - Live</option>
                    <option value="2">Aplikasi - Demo</option>
                </select>-->
                <button class="btn btn-large btn-primary" id="submitLogin" type="submit">Sign in</button>
            </form>

        </div> <!-- /container -->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="assets/js/jquery.js"></script>
        <script src="assets/js/bootstrap-transition.js"></script>
        <script src="assets/js/bootstrap-alert.js"></script>
        <script src="assets/js/bootstrap-modal.js"></script>
        <script src="assets/js/bootstrap-dropdown.js"></script>
        <script src="assets/js/bootstrap-scrollspy.js"></script>
        <script src="assets/js/bootstrap-tab.js"></script>
        <script src="assets/js/bootstrap-tooltip.js"></script>
        <script src="assets/js/bootstrap-popover.js"></script>
        <script src="assets/js/bootstrap-button.js"></script>
        <script src="assets/js/bootstrap-collapse.js"></script>
        <script src="assets/js/bootstrap-carousel.js"></script>
        <script src="assets/js/bootstrap-typeahead.js"></script>

    </body>
</html>
<?php

// Close DB connection
require_once PATH_ASSETS.DS.'/include/db_close.php';

?>
