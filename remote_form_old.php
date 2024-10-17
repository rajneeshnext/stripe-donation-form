<?php
	if(isset($_GET['form'])){
		if($_GET['form'] == "DONATE"){
	        $slug = "HelpPetsNow";
	        $v= $_GET['v'];
	    }else{
	    	$slug = $_GET['form'];
	    	$v= $_GET['v'];
	    }
    }else{
    	$v= $_GET['v'];
        $slug = "HelpPetsNow";
    }
?>
<html lang="en">
<head>
<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Donate Form</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>
<div class="donate-button-info inactive"> 
    <div class="button-ripple-circles">
        <div class="circle1"></div>
    </div>
    <button><svg class="zoom-in-out" width="16" height="16" viewBox="0 0 16 16"><path fill="#28C4FF" d="M8 14.3611C20.957 7.21501 12.8701 -0.965478 8 4.04274C3.121000 -0.965534 -4.957 7.21495 8 14.3611Z"></path></svg>Donate</button>
</div>   
<div class="stripe-donation-body-iframe" style="display: none;">
	<iframe src="form.php?v=<?php echo $v;?>&form=<?php echo $slug;?>" height="200" width="300" title="Remote Form"></iframe>
</div> 
<style>
	.donate-button-info {
		cursor: pointer;
	    position: absolute; 
	    width: 140px;
	    overflow: hidden;
	    z-index: 11;
	    margin: 0px auto;
	    right: 20px;
	    top: 20px;z-index: 0;}
	.donate-button-info button {border: none;color: #fff;background-color: #018fd7;border-radius: 8px;font-size: 16px;font-weight: 600;display: inline-flex;align-items: center;column-gap: 10px;padding: 12px 30px;}
	.donate-button-info:hover button{background-color: #018fd7;}	
	.stripe-donation-body-iframe{	    
	    position: fixed !important;
	    inset: 0px !important;
	    z-index: 3;	    
	} 
	.stripe-donation-body-iframe iframe{
		transform: translateZ(100px) !important;
	    min-height: 100% !important;
	    max-height: 100% !important;
	    min-width: 100% !important;
	    max-width: 100% !important;
	    margin: 0px !important;
	    padding: 0px !important;
	    border: 0px !important;
	    width: 100% !important;
	    height: 100% !important;
	}
	.ui-dialog-titlebar {
	    display: none;
	}
	.close-overlay:hover{opacity: .7;}   
	.close-overlay{
	    position: absolute;
	    right: 27px;
	    top: 15px;
	    z-index: 3;
	    cursor: pointer;
	}  
</style>
<script>
	var url_params = new URLSearchParams(window.location.search);
	if(url_params.has('form')){
		openPopupForm();
	}
	$(document).on("click", ".donate-button-info.inactive", function() {    
		openPopupForm();
    });
    function openPopupForm(){
    	screenWidth = window.innerWidth;
        if ( screenWidth < 600 ) {
            dialogWidth = screenWidth * .95;
        } else {
            dialogWidth = 850;
            isDesktop = true;
        }
        $( ".stripe-donation-body-iframe" ).dialog({
              width : dialogWidth
        });
        $(".donate-button-info").addClass("active");
        $(".donate-button-info").removeClass("inactive");
        $("body").append('<div class="close-overlay"><svg fill="none" height="32" viewBox="0 0 32 32" width="32" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect fill="#ced3d9" height="32" rx="16" width="32"></rect><path d="m21 11-10 10m0-10 10 10" stroke="#222832" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></svg></div>');
        return;
    }
    $(document).on("click", ".donate-button-info.active, .close-overlay", function() {
      $("body .close-overlay").remove();
      $(".stripe-donation-body-iframe").dialog("close");
      $(".donate-button-info").addClass("inactive");
      $(".donate-button-info").removeClass("active");
      return;
    });
</script>
</body>
</html>