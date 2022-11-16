
$(document).ready(function(){	//executed after the page has loaded
            
//    checkURL();
    checkURL($('.navbar .navbar-inner .container ul li.active a').attr('href'));	//check if the URL has a reference to a page and load it

    $('.navbar .navbar-inner .container ul li a').click(function (e){	//traverse through all our navigation links..

        e.preventDefault();
        checkURL(this.hash);	//.. and assign them a new onclick event, using their own hash as a parameter (#page1 for example)
//
        $('.navbar .navbar-inner .container ul li').siblings().removeClass('active');
        $(this).parent().addClass('active');
        
    });

//    setInterval("checkURL()",250);	//check for a change in the URL every 250 ms to detect if the history buttons have been used

});

var lasturl="";	//here we store the current URL hash

function checkURL(hash)
{
    if(!hash) hash=window.location.hash;	//if no parameter is provided, use the hash value from the current address

    if(hash != lasturl)	// if the hash value has changed
    {
        lasturl=hash;	//update the current hash
        loadPage(hash);	// and load the new page
        
    } 
}

function loadPage(url)	//the function that loads pages via AJAX
{
    url = url.replace('#','');	//strip the #page part of the hash and leave only the page number

//    var menu = url.split('|');

    if(url != '') {
        
        setForm();
    
        $("#pageContent").fadeOut();
        
        $.blockUI({ message: '<h4>Please wait...</h4>' }); 

        $('#loading').css('visibility','visible');	//show the rotating gif animation
        
        $('#pageContent').load('views/' + url + '.php', {}, iAmACallbackFunction);	//load the returned html into pageContet  
        
        $('#loading').css('visibility','hidden');	//and hide the rotating gif

    }
}

function loadContent(url) {
    url = url.replace('#','');

    var menu = url.split('|');

    if(menu[0] == 'addNew') {
        $("#dataSearch").fadeOut();
        $("#dataContent").fadeOut();
        $("#dataPage").fadeOut();
        $("#successMsgAll").hide();
        $("#errorMsgAll").hide();
        
        $('#loading').css('visibility','visible');
        
        $('#dataContent').load('forms/' + menu[1] + '.php', {}, iAmACallbackFunction2);
        
        $('#loading').css('visibility','hidden');
        
    } 
}

function iAmACallbackFunction() {
    $("#pageContent").fadeIn("slow");
}

function iAmACallbackFunction2() {
    $("#dataContent").fadeIn("slow");
}

function setForm() {
    $.ajax({
        url: 'session_processing.php',
        method: 'POST',
        data: { action: 'setFormSession'
        },
        success: function(data){
            var returnVal = data.split('|');
            if(parseInt(returnVal[0])!=0)	//if no errors
            {
//                alert(returnVal[2]);
                if(returnVal[2] != 0) {
                    saveForm(returnVal[2]);
                }
            }
        }
    });
}

function saveForm(id) {
    if(id == 1) {
        $.ajax({
            url: 'session_processing.php',
            method: 'POST',
            data: $("#transactionDataForm").serialize(),
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    
                }
            }
        });
    } else if(id == 2) {
        $.ajax({
            url: 'session_processing.php',
            method: 'POST',
            data: $("#paymentDataForm").serialize(),
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    
                }
            }
        });
    } else if(id == 3) {
        $.ajax({
            url: 'session_processing.php',
            method: 'POST',
            data: $("#contractDataForm").serialize(),
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    
                }
            }
        });
    } else if(id == 4) {
        $.ajax({
            url: 'session_processing.php',
            method: 'POST',
            data: $("#salesDataForm").serialize(),
            success: function(data){
                var returnVal = data.split('|');
                if(parseInt(returnVal[0])!=0)	//if no errors
                {
                    
                }
            }
        });
    }
}




