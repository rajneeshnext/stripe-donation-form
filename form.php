<?php
include_once(dirname(__FILE__) ."/config.php");
if(isset($_GET['form'])){
    if($_GET['form'] == "DONATE"){
        $slug = "HelpPetsNow";
    }else{
        $slug = $_GET['form'];
    }
}else{
    $slug = "HelpPetsNow";
}
if(isset($config[$slug]['main_title'])){
}else{
    echo "<div style='text-align: center;font-weight: bold;margin-top: 50px;'>Form with name is not configured</div>";
    exit();
}
$stripe_clientID = $config[$slug]['STRIPE_CLIENT_ID'];
$logo_url = $config[$slug]['logo_url'];
$main_title = $config[$slug]['main_title'];
$main_banner = $config[$slug]['main_banner'];
$main_description = $config[$slug]['main_description'];

$terms_policy_url = $config[$slug]['terms_policy_url'];
$thank_you_url = $config[$slug]['thank_you_url'];
?>
<html lang="en">
<head>
<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Donate Form</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-creditcardvalidator/1.0.0/jquery.creditCardValidator.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script>
    Stripe.setPublishableKey("<?php echo $stripe_clientID; ?>");
    function stripePay(event) {
        $('.loader_gif').show();
        //$('#makePayment').attr('disabled', 'disabled');
        event.preventDefault(); 
        if(validateForm() == true) {
             $('#payNow').attr('disabled', 'disabled');
             $('#payNow').val('Payment Processing....');
             Stripe.createToken({
              number:$('#cardNumber').val(),
              cvc:$('#cardCVC').val(),
              exp_month : $('#cardExpMonth').val(),
              exp_year : $('#cardExpYear').val()
             }, stripeResponseHandler);
             return false;
        }else{
            error_value = validateForm();
            if(parseInt(error_value) == 9 || parseInt(error_value) == 8 || parseInt(error_value) == 7 || parseInt(error_value) == 6){
                slideStepsToCard(4);
            }
            if(parseInt(error_value) < 6){
                slideStepsToInformation(5);
            }
            $('.loader_gif').hide();
            //alert("Form incomplete");
        }
    }

function stripeResponseHandler(status, response) {
     if(response.error) {
          $('.loader_gif').hide();
          //$('#makePayment').removeAttr('disabled');
          alert("Error: "+response.error.message);
          slideStepsToCard(4);
          $(".card_error_message div div.ui-tooltip-body").text(response.error.message);            
          $(".card_error_message").show();  
     } else {
          $(".card_error_message").hide();  
          var stripeToken = response['id'];
          $('#paymentForm').append("<input type='hidden' name='stripeToken' value='" + stripeToken + "' />");
          var formData = $('#paymentForm').serialize();
          $.ajax({
                type: "POST",
                cache: false,
                dataType: "json",
                url: "functions.php",
                data: {myData: formData,action: 'donation'},
                success: function (msg) {
                    console.log(msg);
                    if(msg.error){
                        step = 4;
                        $(".form_steps").hide();

                        $(".step"+step).show("slide", { direction: "right" }, 500);
                        $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
                        $(".step"+step).css('display', 'flex');    
                        setTimeout(function(){ 
                           $(".step"+step).css('display', 'flex');    
                        }, 507);

                        $(".card_error_message").show();  
                        $(".card_error_message div div.ui-tooltip-body").text(msg.error.message);      

                    }else{
                        $(".card_error_message").hide();
                        customerName = $('#customerName').val();
                        alert(customerName);
                        window.parent.location.href = "<?php echo $thank_you_url;?>?form=<?php echo $slug;?>&name="+customerName;
                    }
                }
           });
     }
}

function validateForm() {

 var validCard = 0;
 var valid = false;
 var cardCVC = $('#cardCVC').val();
 var cardExpMonth = $('#cardExpMonth').val();
 var cardExpYear = $('#cardExpYear').val();
 var cardNumber = $('#cardNumber').val();
 var emailAddress = $('#emailAddress').val();
 var customerName = $('#customerName').val();
 var customerAddress = $('#customerAddress').val();
 var customerCity = $('#customerCity').val();
 var customerZipcode = $('#customerZipcode').val();
 var customerCountry = $('#customerCountry').val();
 var validateName = /^[a-z ,.'-]+$/i;
 var validateEmail = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/;
 var validateMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
 var validateYear = /^2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
 var cvv_expression = /^[0-9]{3,4}$/;

 $('#cardNumber').validateCreditCard(function(result){
  if(result.valid) {
   $('#cardNumber').removeClass('require');
   $('#errorCardNumber').text('');
   validCard = 1;
  } else {
   $('#cardNumber').addClass('require');
   $('#errorCardNumber').text('Invalid Card Number');
   $("#cardNumber").css("border","1px solid red");
   validCard = 0;    
   return 9;
  }
 });

 if(validCard == 1) {
  if(!validateMonth.test(cardExpMonth)){
   $('#cardExpMonth').addClass('require');
   $("#cardExpMonth").css("border","1px solid red");
   $('#errorCardExpMonth').text('Invalid Data');
   valid = false;
   return 8;
  } else { 
   $('#cardExpMonth').removeClass('require');
   $('#errorCardExpMonth').text('');
   valid = true;
  }

  if(!validateYear.test(cardExpYear)){
   $("#cardExpYear").css("border","1px solid red");
   $('#cardExpYear').addClass('require');
   $('#errorCardExpYear').error('Invalid Data');
   valid = false;return 7;
  } else {
   $('#cardExpYear').removeClass('require');
   $('#errorCardExpYear').error('');
   valid = true;
  }

  if(!cvv_expression.test(cardCVC)) {
   $("#cardCVC").css("border","1px solid red");
   $('#cardCVC').addClass('require');
   $('#errorCardCvc').text('Invalid Data');
   valid = false;return 6;
  } else {
   $('#cardCVC').removeClass('require');
   $('#errorCardCvc').text('');
   valid = true;
  }
  
  if(!validateName.test(customerName)) {
   $("#customerName").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#customerName").css("border","1px solid #D4D4D4");
   valid = true;
  }

  if(!validateEmail.test(emailAddress)) {
   $("#emailAddress").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#emailAddress").css("border","1px solid #D4D4D4");
   valid = true;
  }

  if(customerAddress == '') {
   $("#customerAddress").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#customerAddress").css("border","1px solid #D4D4D4");
   valid = true;
  }

  if(customerCity == ''){
   $("#customerCity").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#customerCity").css("border","1px solid #D4D4D4");
   valid = true;
  }

  if(customerZipcode == ''){
   $("#customerZipcode").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#customerZipcode").css("border","1px solid #D4D4D4");
   valid = true;
  }

  if(customerCountry == '') {
   $("#customerCountry").css("border","1px solid red");
   valid = false;return valid;
  } else {
   $("#customerCountry").css("border","1px solid #D4D4D4");
   valid = true;
  } 

  if($("#terms_policy").is(':checked')) {
   $("#terms_policy").css("outline-style","none");
  } else {
   $("#terms_policy").css("outline-style","solid");
   $("#terms_policy").css("outline-color","red");
   $("#terms_policy").css("outline-width","thin");
   valid = false;return valid;
  }  
 }else{
    valid = 9;
 }
 return valid;
}
function slideStepsToInformation(step){
    $(".form_steps").hide();
    $(".step"+step).show("slide", { direction: "right" }, 500);
    $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
    $(".step"+step).css('display', 'flex');    
    setTimeout(function(){ 
       $(".step"+step).css('display', 'flex');    
    }, 507);
}
function slideStepsToCard(step){
    $(".form_steps").hide();
    $(".step"+step).show("slide", { direction: "right" }, 500);
    $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
    $(".step"+step).css('display', 'flex');    
    setTimeout(function(){ 
       $(".step"+step).css('display', 'flex');    
    }, 507);
}

function validateNumber(event) {
 var charCode = (event.which) ? event.which : event.keyCode;
 if (charCode != 32 && charCode > 31 && (charCode < 48 || charCode > 57)){
  return false;
 }
 return true;
}

$(document).ready(function() {

    $(".customer").on("keyup change", function(e) {         
         var emailAddress = $('#emailAddress').val();
         var customerName = $('#customerName').val();
         var validateName = /^[a-z ,.'-]+$/i;
         var validateEmail = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/;
         if(!validateName.test(customerName)) {
           $("#customerName").css("border","1px solid red");
           valid = false;return valid;
          } else {
           $("#customerName").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(!validateEmail.test(emailAddress)) {
           $("#emailAddress").css("border","1px solid red");
           valid = false;return valid;
          } else {
           $("#emailAddress").css("border","1px solid #D4D4D4");
           valid = true;
          }
    });

    $("#validateCard").click(function(e){ 
         var validCard = 0;
         var valid = false;
         var cardCVC = $('#cardCVC').val();
         var cardExpMonth = $('#cardExpMonth').val();
         var cardExpYear = $('#cardExpYear').val();
         var cardNumber = $('#cardNumber').val();
         var emailAddress = $('#emailAddress').val();
         var customerName = $('#customerName').val();
         var customerAddress = $('#customerAddress').val();
         var customerCity = $('#customerCity').val();
         var customerZipcode = $('#customerZipcode').val();
         var customerCountry = $('#customerCountry').val();
         var validateName = /^[a-z ,.'-]+$/i;
         var validateEmail = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/;
         var validateMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
         var validateYear = /^2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
         var cvv_expression = /^[0-9]{3,4}$/;

         $('#cardNumber').validateCreditCard(function(result){
              if(result.valid) {
                   $('#cardNumber').removeClass('require');
                   $('#errorCardNumber').text('');
                   $("#cardNumber").css("border","1px solid #aab1bc");
                   validCard = 1;
              } else {
                   $('#cardNumber').addClass('require');
                   $('#errorCardNumber').text('Invalid Card Number');
                   $("#cardNumber").css("border","1px solid red");
                   e.stopPropagation();
                   return;
              }
         });

        if(validCard == 1) {
          if(!validateMonth.test(cardExpMonth)){
               $('#cardExpMonth').addClass('require');
               $("#cardExpMonth").css("border","1px solid red");
               $('#errorCardExpMonth').text('Invalid Data');
               e.stopPropagation();
               return;
          } else { 
               $('#cardExpMonth').removeClass('require');
               $("#cardExpMonth").css("border","1px solid #aab1bc");
               $('#errorCardExpMonth').text('');
               valid = true;
          }
          if(!validateYear.test(cardExpYear)){
               $("#cardExpYear").css("border","1px solid red");
               $('#cardExpYear').addClass('require');
               $('#errorCardExpYear').error('Invalid Data');
               e.stopPropagation();
               return;
          } else {
               $('#cardExpYear').removeClass('require');
               $('#errorCardExpYear').error('');
               $("#cardExpYear").css("border","1px solid #aab1bc");
               valid = true;
          }
          if(!cvv_expression.test(cardCVC)) {
               $("#cardCVC").css("border","1px solid red");
               $('#cardCVC').addClass('require');
               $('#errorCardCvc').text('Invalid Data');
               e.stopPropagation();
               return;
          } else {
               $('#cardCVC').removeClass('require');
               $("#cardCVC").css("border","1px solid #aab1bc");
               $('#errorCardCvc').text('');
                step =parseInt($(this).attr('step'))+1;
                $(".form_steps").hide();
                //$(this).attr('step', step);

                $(".step"+step).show("slide", { direction: "right" }, 500);
                $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
                $(".step"+step).css('display', 'flex');    
                setTimeout(function(){ 
                   $(".step"+step).css('display', 'flex');    
                }, 507);
               valid = true;
          }

        }   
    });       
    
    // validate paypal  
    $(".makePayment_paypal").click(function(e){
         $('.loader_gif').show();
         $('#makePayment').attr('disabled', 'disabled');
         var valid = false;
         var emailAddress = $('#emailAddress').val();
         var customerName = $('#customerName').val();
         var customerAddress = $('#customerAddress').val();
         var customerCity = $('#customerCity').val();
         var customerZipcode = $('#customerZipcode').val();
         var customerCountry = $('#customerCountry').val();
         var validateName = /^[a-z ,.'-]+$/i;
         var validateEmail = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/;
         var validateMonth = /^01|02|03|04|05|06|07|08|09|10|11|12$/;
         var validateYear = /^2017|2018|2019|2020|2021|2022|2023|2024|2025|2026|2027|2028|2029|2030|2031$/;
        if(!validateName.test(customerName)) {
           $("#customerName").css("border","1px solid red");
            
           valid = false;return valid;
          } else {
           $("#customerName").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(!validateEmail.test(emailAddress)) {
           $("#emailAddress").css("border","1px solid red");
           
           valid = false;return valid;
          } else {
           $("#emailAddress").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(customerAddress == '') {
           $("#customerAddress").css("border","1px solid red");
              
           valid = false;return valid;
          } else {
           $("#customerAddress").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(customerCity == ''){
           $("#customerCity").css("border","1px solid red");
           valid = false;return valid;
          } else {
           $("#customerCity").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(customerZipcode == ''){
           $("#customerZipcode").css("border","1px solid red");
           valid = false;return valid;
          } else {
           $("#customerZipcode").css("border","1px solid #D4D4D4");
           valid = true;
          }

          if(customerCountry == '') {
           $("#customerCountry").css("border","1px solid red");
           valid = false;return valid;
          } else {
           $("#customerCountry").css("border","1px solid #D4D4D4");
           valid = true;
          }  
        var formData = $('#paymentForm').serialize();
        $.ajax({
            type: "POST",
            cache: false,
            url: "<?php echo '/wp-admin/admin-ajax.php'?>",
            data: {myData: formData,action: 'donation_paypal'},
            success: function (msg) {   
                 $('.loader_gif').hide();
                 $(this).submit();return;
            }
        }); 

    });
        
    $(".select_amount").click(function(){  
        amount = $(this).attr("amount");
        $("#other_amount").val('');
        $(".total_amount_text").text("$"+($(this).attr("amount")/100).toFixed(2));
        $(".make_it_monthly").attr("amount", amount);               
        var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
        $('.stripe_fees').text(stripeFee.toFixed(2));
        final_amount = amount/100 +stripeFee;
        $("#total_amount").val(final_amount.toFixed(2)*100); 
        $("#final_amount").val(final_amount.toFixed(2)*100); 
        $('.final_amount').text(final_amount.toFixed(2));
        $("#dntplgn_once_amount").val($(this).attr("amount")/100);
        $(".select_amount").removeClass("active");
        $(this).addClass("active");
    });
    $("#other_amount").on("keyup change", function(e) {
        $(".select_amount").removeClass("active");
        amount = parseFloat($(this).val())*100;
        if(isNaN(amount)) {
            $("#total_amount").val('');
            $(".total_amount_text").text("$"+'0');
            $("#dntplgn_once_amount").val(0);
        }else{
            $("#total_amount").val(parseFloat($(this).val())*100);
            $(".total_amount_text").text("$"+parseFloat($(this).val()));
            $("#dntplgn_once_amount").val(parseFloat($(this).val()));
        }    
    });
    $(".select_payment_mode").click(function(){
        $(".select_payment_mode").removeClass("active");
        $(this).addClass("active");
        if($(this).hasClass("stripe")){        
           $("#donate_by_stripe").show();
            $("#makePayment").show();
           $("#donate_by_paypal").hide();
        }else{
            $("#donate_by_stripe").hide();
            $("#donate_by_paypal").show();
            $("#makePayment").hide();
        }
    });
    $(".select_amount_type").click(function(){
        if($(this).attr("type") == "onetime"){  
            $(".per_onetime_month").hide();  
            $(".payment_type_selected").attr("step","1");
            $(".payment_type_selected_skip").attr("step","2");
            $("#onetime").show();
            $("#onetime_form_paypal").show();
            $("#monthly_form_paypal").hide();
            $("#monthly").hide();
            amount = parseFloat($(".select_amount.active").attr("amount"));
            $(".make_it_monthly").attr("amount", amount);
            if(isNaN(amount)) {
                amount = parseFloat($("#other_amount").val())*100;
                $(".total_amount_text").text("$"+(amount/100).toFixed(2));
                $("#total_amount").val(amount);
                var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
                $('.stripe_fees').text(stripeFee.toFixed(2));
                final_amount = amount/100 +stripeFee;
                $('.final_amount').text(final_amount.toFixed(2));
                $("#total_amount").val(final_amount.toFixed(2)*100); 
                $("#final_amount").val(final_amount.toFixed(2)*100); 
            }else{
                $(".total_amount_text").text("$"+(amount/100).toFixed(2));
                var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
                $('.stripe_fees').text(stripeFee.toFixed(2));
                final_amount = amount/100 +stripeFee;
                $('.final_amount').text(final_amount.toFixed(2));
                $("#total_amount").val(final_amount.toFixed(2)*100); 
                $("#final_amount").val(final_amount.toFixed(2)*100); 
            }
        }else{  
            $(".per_onetime_month").show();
            $(".payment_type_selected").attr("step","2");
            $(".payment_type_selected_skip").attr("step","1");
            $("#onetime").hide();
            $("#monthly").show();
            $("#onetime_form_paypal").hide();
            $("#monthly_form_paypal").show();
            amount = parseFloat($(".select_amount_monthly.active").attr("amount"));
            $(".make_it_monthly").attr("amount", amount);
            if(isNaN(amount)) {
                amount = parseFloat($("#other_amount_monthly").val())*100;
                $(".total_amount_text").text("$"+(amount/100).toFixed(2));
                $("#amount_paypal").val((amount/100).toFixed(2));
                var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
                $('.stripe_fees').text(stripeFee.toFixed(2));
                final_amount = amount/100 +stripeFee;
                $('.final_amount').text(final_amount.toFixed(2));
                $("#total_amount").val(final_amount.toFixed(2)*100); 
                $("#final_amount").val(final_amount.toFixed(2)*100); 
            }else{
                $(".total_amount_text").text("$"+(amount/100).toFixed(2));
                $("#amount_paypal").val((amount/100).toFixed(2));
                var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
                $('.stripe_fees').text(stripeFee.toFixed(2));
                final_amount = amount/100 +stripeFee;
                $('.final_amount').text(final_amount.toFixed(2));
                $("#total_amount").val(final_amount.toFixed(2)*100); 
                $("#final_amount").val(final_amount.toFixed(2)*100); 
            }
        }
        $("#select_payment_type").val($(this).attr("type"));
        $( ".select_amount_type" ).removeClass( "active" );
        $( this ).addClass( "active" );
    });
    $(".select_amount_monthly").click(function(){
        $("#other_amount_monthly").val('');
        $(".total_amount_text").text("$"+($(this).attr("amount")/100).toFixed(2));
        amount = $(this).attr("amount");
        $(".make_it_monthly").attr("amount", amount);
        var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
        $('.stripe_fees').text(stripeFee.toFixed(2));
        final_amount = amount/100 +stripeFee;
        $('.final_amount').text(final_amount.toFixed(2));
        $("#total_amount").val(final_amount.toFixed(2)*100); 
        $("#final_amount").val(final_amount.toFixed(2)*100); 
        $("#amount_paypal").val($(this).attr("amount")/100);
        $(".select_amount_monthly").removeClass("active");
        $(this).addClass("active");
    });
    $(".make_it_monthly").click(function(){
        $(".payment_type_selected").attr("step","2");
        $(".payment_type_selected_skip").attr("step","1");
        $(".per_onetime_month").show();
        $(".total_amount_text").text("$"+($(this).attr("amount")/100).toFixed(2));
        amount = $(this).attr("amount");
        var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
        $('.stripe_fees').text(stripeFee.toFixed(2));
        final_amount = amount/100 +stripeFee;
        $('.final_amount').text(final_amount.toFixed(2));
        var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
        $('.stripe_fees').text(stripeFee.toFixed(2));
        final_amount = amount/100 +stripeFee;
        $("#total_amount").val(final_amount.toFixed(2)*100); 
        $("#final_amount").val(final_amount.toFixed(2)*100); 

        $("#amount_paypal").val($(this).attr("amount")/100);
        $("#select_payment_type").val("monthly");
        $( ".select_amount_type" ).removeClass( "active" );
        $( ".select_amount_type.btn-one-radius" ).addClass( "active" );
    });     
    $(".add_fees").click(function(){
        var checked = $(this).is(':checked');
        if (checked) { 
            $( ".star_icons_svg" ).removeClass('hide');
            $( ".transaction_cost" ).show();
            amount = parseFloat(parseFloat($(".final_amount").text())) + parseFloat($(".stripe_fees").text());
            $( ".final_amount" ).text(amount.toFixed(2));
            $("#total_amount").val(amount.toFixed(2)*100); 
            $("#final_amount").val(amount.toFixed(2)*100);    
            $( ".transaction_cost_alert" ).removeClass('showw');
            payButton(amount.toFixed(2)*100, "Donation", $("#comment").val());   
        } else {
            amount = parseFloat(parseFloat($(".final_amount").text())) - parseFloat($(".stripe_fees").text());
            $( ".final_amount" ).text(amount.toFixed(2));
            $("#total_amount").val(amount.toFixed(2)*100); 
            $("#final_amount").val(amount.toFixed(2)*100); 
            $( ".transaction_cost" ).hide();
            $( ".star_icons_svg" ).addClass('hide');
            $( ".transaction_cost_alert" ).addClass('showw');
            payButton(amount.toFixed(2)*100, "Donation", $("#comment").val());
        }
    });
    $("#other_amount_monthly").on("keyup change", function(e) {
        $(".select_amount_monthly").removeClass("active");
        amount = parseFloat($(this).val())*100;
        if(isNaN(amount)) {
            $("#total_amount").val('');
            $(".total_amount_text").text("$"+'0');
        }else{
            $("#total_amount").val(parseFloat($(this).val())*100);
            $("#amount_paypal").val(amount/100);
            $(".total_amount_text").text("$"+parseFloat($(this).val()));
        }    
    });

    $(".open_comment_step").click(function(){
        $(".form_steps").hide();

        $(".comment_step").show("slide", { direction: "right" }, 500);
        $(".comment_step").parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
        $(".comment_step").css('display', 'flex');    
        setTimeout(function(){ 
           $(".comment_step").css('display', 'flex');    
        }, 507);

    });
    $(".next").click(function(){

        if(parseInt($(this).attr('step')) == 2){
            amount = $('.make_it_monthly').attr("amount");
            var checked = $('.add_fees').is(':checked');
            if (checked) {                 
                var stripeFee = (amount/100 * 2.9 / 100) + 0.30;
                final_amount = amount/100 + stripeFee;
                final_amount = final_amount.toFixed(2)*100;
                payButton(final_amount, "Donation", $("#comment").val());
            }else {
                payButton(amount, "Donation", $("#comment").val());
            }
        }

        step =parseInt($(this).attr('step'))+1;
        $(".form_steps").hide();
        //$(this).attr('step', step);

        $(".step"+step).show("slide", { direction: "right" }, 500);
        $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
        $(".step"+step).css('display', 'flex');    
        setTimeout(function(){ 
           $(".step"+step).css('display', 'flex');    
        }, 507);

    });
    $(".header-back-screen").click(function(){
        step =parseInt($(this).attr('step'));
        $(".form_steps").hide();

        $(".step"+step).show("slide", { direction: "left" }, 500);
        $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "left" }, 500);
        $(".step"+step).css('display', 'flex');    
        setTimeout(function(){ 
           $(".step"+step).css('display', 'flex');    
        }, 502);
    });  

    $(".select_amount_type").click(function(){
        if($(this).attr("type") == "onetime"){            
            $(".red_heart").show();
            //$(".white_heart").hide();
        }else{  
            $(".heart-holder").removeClass("heart-y-move-enter-active");
            setTimeout(function(){ 
               $(".heart-holder").addClass("heart-y-move-enter-active");               
            }, 5); 
        }
    });
    
    $(document).on("click", ".cvc_info .tip_icon", function() {
      $(".cvc_info .tip_info").toggle();
      return;
    }); 
    $(document).on("click", ".cover_cost.tip_icon", function() {
      $(".cover_cost.tip_info").toggle();
      return;
    });
    $('body').click(function(event) {
       $(".cover_cost.tip_info").hide();
       $(".cvc_info .tip_info").hide();
       $(".footer_tip_info").hide();
    });
    $(document).on("click", ".payment_secure_hover", function() {
      $(".payment_secure_tips").toggle();
      return;
    }); 
    $(document).on("click", ".payment_tax_deductible_click", function() {
      $(".payment_tax_deductible_tips").toggle();
      return;
    });
    $(document).on("click", ".recurring_payment_click", function() {
      $(".recurring_payment_tips").toggle();
      return;
    });
});

</script>
<style>
*{
    font-family: 'IBM Plex Sans', sans-serif;
}
.stripe-bottom-content h3 {
    font-size: 20px;
    line-height: 34px;
    font-weight: 600;
    margin-top: 8px;
}
p {
    font-size: 16px;
    line-height: 20px;
    font-weight: 400;
    margin-top: 0;
    margin-bottom: 15px;
}
.btn.btn-success {
    font-size: 16px;
    line-height: 24px;
    font-weight: 500;
}
.select_amount_type label {
    font-weight: 500 !important;
}
.transaction_cost  strong, .final_transaction_cost strong{float: left;margin-left: 12px;}    
.transaction_cost  span, .final_transaction_cost span{float: right;margin-left: 4px;margin-top: 3px;}    

.close-overlay{
    position: absolute;
    right: 27px;
    top: 50px;
    z-index:2;
    cursor: pointer;
}    
.btn-success{background-color: #3371e6;}
.btn-success:active:hover, .btn-success:hover, .btn-success.focus, .btn-success:focus{background-color: #2e66cf;}
.ui-dialog-titlebar-close {
    visibility: hidden;
}    
.ui-widget-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}    
.heading .total_amount_text{
    font-size: 32px;
    line-height: 40px;
    font-weight: 400;
}
.heading span, .heading .per_onetime_month{
    font-size: 20px;
    line-height: 28px;
    font-weight: 400;
}    
.header-back-screen{
    background: none;
    border: none;
}
.section-header-container{
    font-size: 20px;
    line-height: 28px;
    font-weight: 600;
    margin: 0px;
    color: #222832;
}
.btn-danger {
    background: #ec414b;
    color: #fff;
}
.gift_icon{
    position: absolute;
    z-index: 2;
    top: -8px;
    left: -7px;
 }   
.btn {
    display: block;
    width: 100%;
    position: relative;
    text-align: center;
    text-decoration: none;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    margin: 0;
    border: 0;
    border-radius: 8px;
    padding: 12px 4px;
    box-shadow: 0 2px 3px rgb(12 33 65 / 30%), 0 -1px 0 rgb(0 0 0 / 20%) inset, 0 0 0 1px rgb(0 0 0 / 15%) inset;
    background-image: none;
    -webkit-user-select: none;
    user-select: none;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-tap-highlight-color: rgba(0,0,0,.1);
    transition: background-color .15s ease-in-out;
}

.add_heart{top: 4px;position: relative;}
.next{
    width: 100%;
    padding: 12px 12px;
    margin-bottom: 15px;
}
.part_collections {
    width: 100%;
    flex-grow: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
}   
.mid_part .cover_cost_div{
    margin-bottom: 140px;
} 
.mid_part{
    position: relative;
    flex-grow: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
}
.foot_part{
    position: relative;
    z-index: 3;
    min-height: 0;
    flex-shrink: 0;
}
.loader_gif{
    text-align: center;
    margin-top: 10px;
}
.loader_gif img{width: 20px;}
.step1.form_steps{
    display: block;
}
.form_steps{
    display: none;
}   
.form_steps .header {    
    display: flex;
    margin-left: -15px;
    margin-right: -15px;
    padding-left: 15px;margin-bottom: 10px;
    padding-right: 15px;
    padding-bottom: 10px;
}
.header-desktop {
    border-bottom: 1px solid rgba(34,40,50,.2);
}
.header-main {
    flex-grow: 1;
    text-align: center;
}
.title-1, .campaign-title {
    font-size: 20px;
    line-height: 34px;
    font-weight: 600;
    padding: 0;
    margin: -3px;
}
.select_amount_type.form-group{
    width: 50%;
    float: left;
}
.heading.title{
    margin-bottom: 20px;
    margin-top: 11px;
    padding: 0px 15px 0px 19px;
}    
#onetime .col-xs-6, #monthly .col-xs-6{
    width: 50%;
    padding: 0 5px;
} 
.cover_cost.tip_icon{right: 20px; top:17px}
.stripe_fees{font-weight: bold;} 
.ui-dialog-titlebar{display: none;} 
.select_amount_monthly  label span, .select_amount label span{color: #000;}
.text-green-80{     color: #00c07b;}
.stripe-donation-body {position: relative;flex-direction: column;z-index:2;}
.stripe-donation-body.active {display: flex;}
.ui-dialog{z-index: 1;}
.stripe-donation-info {position: relative;overflow: hidden;display: flex;flex-grow: 1;flex-shrink: 0;min-height: 0;column-gap: 20px;max-width: 850px;margin: 30px auto 0px;    border-radius: 16px;}
.stripe-left-content {background: #fff;display: flex;flex-direction: column;}
.strip-top-left-img img {width: 100%; border-top-left-radius: 16px;border-top-right-radius: 16px;}
.stripe-bottom-content {padding: 20px 20px 0px;}
.stripe-donation-body div#primary {margin: 0px;}
#paymentForm .select_amount_type:hover:not(.active) label, 
#paymentForm .select_amount:hover:not(.active) label, 
#paymentForm .select_amount_monthly:hover:not(.active) label,
#other_amount:hover, #other_amount_monthly:hover{background: #f5f7ff !important;}
.stripe-donation-body #primary {margin: 0px;}
.stripe-donation-body #primary .panel-body {box-shadow: unset;border: none;}
.stripe-donation-body #primary .panel {box-shadow: unset;border: none;border-radius: 16px;margin: 0px;}
.pl-0 {padding-left: 0px;}
.pr-0 {padding-right: 0px;}
.btn-one-radius label {border-top-right-radius: 0 !important;border-bottom-right-radius: 0 !important;}
.btn-two-radius label {border-top-left-radius: 0 !important;border-bottom-left-radius: 0 !important;}
#monthly, #onetime {padding: 0px 10px;}
#monthly .col-xs-4, #onetime .col-xs-4{padding: 0px 5px;}
.select_amount_monthly, .select_amount {margin-bottom: 10px;}

#makePayment, .makePayment_paypal {width: 100%;border-radius: 8px;margin-top: 15px;padding: 15px 10px;outline: none;border: none;}
.donate-button-info {cursor: pointer;
    position: absolute;
    width: 140px;
    overflow: hidden;
    z-index: 11;
    margin: 0px auto;
    right: 20px;
    top: 20px;z-index: 0;}
.donate-button-info button {border: none;color: #fff;background-color: #018fd7;border-radius: 8px;font-size: 16px;font-weight: 600;display: inline-flex;align-items: center;column-gap: 10px;padding: 12px 30px;}
.donate-button-info:hover button{background-color: #018fd7;}
.zoom-in-out {animation: zoom-in-zoom-out 2s ease infinite;}
.button-ripple-circles {height: 242px;width: 242px;position: absolute;left: 50%;top: 50%;transform: translate(-63%, -50%);}
.button-ripple-circles > div {animation: growAndFade 3s infinite ease-out;background-color: #fff;border-radius: 50%;height: 100%;opacity: 0;position: absolute;width: 100%;}
.button-ripple-circles  .circle1 {animation-delay: 1s;}
#donate_by_stripe .col-xs-4 {padding-right: 0px;}
#donate_by_stripe label {font-weight: bold;}
.faq-link-title{font-size: 12px;opacity: .8;}
.faq-link-title:hover{opacity: 1;}
.widget-footer-panel{text-align: center;}
.widget-footer li{position: relative;padding-right: 15px;display: inline;list-style: disc;color: #fff;}
.widget-footer{width: 850px;margin: 0 auto;}
.widget-footer button{background: none;border: none;color: #fff;}
body {
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
}
body::-webkit-scrollbar { 
    display: none;  /* Safari and Chrome */
}
@media (min-width: 950px){
    .stripe-donation-info {flex-direction: row;padding: 30px 5px;}
    .stripe-left-content, .stripe-donation-body #primary {border-radius: 16px;box-shadow: 0 0 10px rgba(0,0,0,.45);}
}
@media (max-width: 949px){
    .stripe-donation-body #primary .panel{border-radius: 0;}
  .stripe-donation-info{flex-direction: column;max-width: 600px;margin: 10px auto 10px;box-shadow: 0 0 10px rgba(0,0,0,.45);}
  .stripe-left-content {border-bottom-left-radius: 0;border-bottom-right-radius: 0;}
  .stripe-donation-body #primary {border-top-left-radius: 0;border-top-right-radius: 0;}
}
@media (max-width: 660px){
    p {
        font-size: 15px;
    }
    .mid_part .cover_cost_div {
        margin-bottom: 20px;
    }
    .strip-top-left-img img {
        height: 180px;
    }
    .form_steps .header{margin-bottom: 0px;padding-bottom: 0px;}
    .select_amount label, .select_amount_monthly  label, .select_amount_type  label{background: #fff;}
    .step2 .mid_part p{margin-bottom: 20px;}.gift_icon{z-index:3;}
    .form_steps #onetime .col-xs-6, .form_steps #monthly .col-xs-6{width: 100%;}
    .stripe-donation-info{    border-radius:0;background: #fff;
    box-shadow: none;margin: 5px 0px 0px; display: block;}
    .cover_cost.tip_icon{right: 18px; top:19px}
    .panel-body{
            margin: 0 auto;
        padding: 10px;
        padding-top: 20px;
        padding-bottom: 20px;
        padding-left: 20px;
        padding-right: 20px;
        }
    .logo_mobile{    display: block;
        text-align: center;
        margin-top: 0px;
        margin-bottom: 5px;
    }
    form {margin: 0;}
    .header-desktop{border-bottom: none;}
    .stripe-bottom-content h3 {
        font-size: 20px;
        line-height: 15px;
        font-weight: 600;
        margin-top: 0;
        text-align: center;
    }
    .widget-footer li{display: list-item; color: #000; list-style: disc;}
    .ui-widget-overlay{background: #fff !important;}
    .logo_desktop{display: none;}
    .widget-footer{width: 100%;margin-top: 12px;margin-bottom: 12px;}
    .widget-footer button{color: #000;}
    .widget-footer-panel{text-align: left;}
    .panel {
        background-color: rgb(206, 211, 217, .3);
        border-top: 1px solid rgba(34,40,50,.2) !important;
        border-bottom: 1px solid rgba(34,40,50,.2) !important;
    }
    ul.faq-links{width: 80%;margin: 0 auto;}
}

@media (min-width: 660px){
    .logo_mobile{display: none;}
    .logo_desktop{display: block;}
    .form_steps{
        min-width: 345px;
        height: 480px;
    }
}

@keyframes zoom-in-zoom-out {
  0% {transform: scale(1, 1);}
  50% {transform: scale(1.5, 1.5);}
  100% {transform: scale(1, 1);}
}
@keyframes growAndFade {
  0% {opacity: .25;transform: scale(0);}
  100% {opacity: 0;transform: scale(1);}
}
#customerCountry{width: 100%; height: 34px;}
.select_amount_type.active label, .select_amount.active label, .select_amount_monthly.active label{
    box-shadow: 0 2px 7px #0050a8 inset, 0 0 0 1px rgb(0 0 0 / 15%) inset;
    background: #3371e6;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 67.19%),#3371e6;
    color: #fff !important;;
}
.form-group.active span{color: #fff;}
#paymentForm .select_amount_type label, #paymentForm .select_amount label, #paymentForm .other_amount_class, #paymentForm .select_amount_monthly label, #other_amount, #other_amount_monthly{cursor: pointer;display: block; width: 100%; padding: 7px 8px;
    text-align: center;color: #6b6b6b;line-height: 26px;box-shadow: 0 2px 3px rgba(12,33,65,.2), 0 -1px 0 rgba(0,0,0,.2) inset, 0 0 0 1px #ced3d9 inset;outline: 0;border-radius: 8px;border: none;}
input {text-align: center;}                        
::-webkit-input-placeholder {text-align: center;}                        
:-moz-placeholder {text-align: center;}
.form-group label{color: #000;font-size: 16px;line-height: 24px;font-weight: 400;}

.heading{
    font-size: 15px;
    margin-top: 10px;
    margin-bottom: 20px;
}
#donate_by_paypal{display: none;}
#monthly_form_paypal, #onetime_form_paypal{text-align: center;}
.select_payment_mode{text-align: center;width: 100%;border-bottom: 3px solid grey;padding: 15px;cursor: pointer;}
.select_payment_mode.active{border-bottom: 3px solid red;padding: 15px;margin-bottom: 20px;}
.select_payment_mode.stripe{border-right: 3px solid grey; }
.select_payment_mode img{height: 38px;}
input#specialCustomerName{text-align: left;}
.form-control.customer{text-align: left;}
.form-group select {background-color: #fff !important;outline: 0;border-radius: 8px !important;}
.form-control {
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
    display: block;
    width: 100%;
    padding: 9px 14px;
    height: 44px;
    color: #222832;
    -webkit-text-fill-color: #222832;
    background-color: #fff;
    background-image: none;
    border: 1px solid #aab1bc;
    border-radius: 8px;
    background-clip: padding-box;
    box-shadow: 0 2px 3px rgb(12 33 65 / 20%) inset;
    -webkit-appearance: none;
    appearance: none;
}
#comment{
    outline: 0;
    outline-offset: 0;
    box-shadow: 0 0 0 3px rgb(51 113 230 / 65%) inset;
    border-color: #688fc9;
}
.form-control:focus {
    outline: 0;
    outline-offset: 0;
    box-shadow: 0 0 0 3px rgb(51 113 230 / 65%) inset;
    border-color: #688fc9;
}    
#keep_it_one_time{
    box-shadow: 0 2px 2px rgb(12 33 65 / 15%), 0 -1px 0 rgb(0 0 0 / 20%) inset, 0 0 0 1px #ced3d9 inset;
    background: #fff;
    color: #000;
    text-shadow: none;
}
#keep_it_one_time:hover {
    background: #f5f7ff;
    color: #222832;
}
#donate_by_stripe{margin-bottom: 50px;margin-top: 10px;}
.star_icons_svg {position: absolute;left: -15px;top: -30px; opacity: 1; transition: opacity 0.6s linear;}
.star_icons_svg.hide{opacity: 0;}    
.terms_checkbox div{display: inline-block;}
.card_error_message{
    display: none;
}    
.card_error_message .ui-tooltip{
    transition: opacity 0.6s linear;
    position: absolute;
    left: 18px;
    max-width: 312px;
    background: #fff;
    color: red;
    border-radius: 8px;
    border: none;
    z-index: 1020;
    box-shadow: 0 3px 12px rgb(0 0 0 / 40%);
    width: 100%;
    padding: 16px;
    top: -72px;    
    text-align: center;
    height: 72px;
}
.transaction_cost_alert.showw{display: block;}
.transaction_cost_alert{
    display: none;
}
.transaction_cost_alert.showw .ui-tooltip{opacity: 1;}
.transaction_cost_alert .ui-tooltip{
    opacity: 0;
    transition: opacity 0.6s linear;
    position: absolute;
    left: 18px;
    max-width: 266px;
    background: #fff;
    color: #222832;
    border-radius: 8px;
    border: none;
    z-index: 1020;
    box-shadow: 0 3px 12px rgb(0 0 0 / 40%);
    width: 100%;
    padding: 16px;
    top: 63px;    
    text-align: center;
}

.ui-tooltip-arrow {
    position: absolute;
    width: 12px;
    height: 12px;
    transform: rotate(45deg);
    background: #fff;
    top: -6px;
    left: 127px;
}
.add_fees, .add_fees:focus{    outline: 2px solid;}
.add_fees_group label{color: #fff; margin-bottom: 0; margin-left: 5px;}
.tip_icon{
    position: absolute;
    right: 23px;
    top: 15px;
}   
.final_transaction_cost {
    margin-top: 4px;
}
.cover_cost svg{color : #fff;}

.cover_cost.tip_info{right: 8px;
    z-index: 4;}
.tip_info{
    top: 40px;
    right: 0;
    position: absolute;

    width: 160px;
    overflow: visible;    
    font-size: 12px;
    box-shadow: 0 3px 12px rgb(0 0 0 / 40);
    background: #fff;
    color: #222832;
    border-radius: 8px;
    padding: 10px;
    text-align: left;
} 
.footer_tip_info {
    position: absolute;
    width: 300px;
    overflow: visible;
    box-shadow: 0 3px 12px rgb(0 0 0);
    background: #fff;
    color: #222832;
    border-radius: 8px;
    padding: 15px;
    text-align: left;
    left: 0;
    bottom: 25px;
    font-size: inherit;
    line-height: 21px;
    z-index: 3;
}
.p-abs.top-left {
    top: 0;
    left: 0;
}

.z-index-2 {
    z-index: 2;
}
.p-abs {
    position: absolute;
}
.header.header-desktop .p-abs {
    left: 18px;
}
.heart, .heart-list {
    display: block;
}.heart-y-move-enter-active {
    animation: heart-y-move 2.5s forwards linear;
    animation-delay: 50ms;
}
.heart-y-move-enter {
    will-change: transform,opacity;
}
.heart-holder {
    display: block;
    width: 13px;
    height: 11px;
    transform-origin: 50% 50%;
}
.heart-y-move-enter .heart-x-move, .heart-y-move-enter-active .heart-x-move {
    visibility: visible;
}
.heart-x-move {
    width: 13px;
    height: 11px;
    position: absolute;
    left: 0;
    top: 0;
    visibility: hidden;
}
@keyframes heart-y-move{0%{transform:translate(0, 0) rotate(0deg);opacity:1}5%{transform:translate(0px, 0px) scale(1.7)}60%{transform:translate(0px, -65px) rotate(25deg) scale(1.7)}80%{transform:translate(0px -76px) rotate(0deg) scale(1.7);opacity:1}100%{transform:translate(0, -95px) scale(2) rotate(0deg);opacity:0}}

@keyframes heart-x-move{0%{transform:translate(0, -50%) rotate(0)}33%{transform:translate(-7px, -50%) rotate(23deg)}67%{transform:translate(7px, -50%) rotate(-23deg)}100%{transform:translate(0, -50%) rotate(0)}}                

.terms_checkbox input[type=checkbox], input[type=radio]{
    margin-left: 5px;
    width: 24px;
    height: 20px;
    float: left;
    margin-top: 0px;
    margin-bottom: 10px;
}
input[type=checkbox]:focus{outline: none;}
.card_number_icon i{
    position: absolute;
    right: 10px;
    top: 15px;
    color: #357edd;
}
.card_number_icon {position: relative;}

</style>
</head>
<body>    
    <div class="stripe-donation-body">
        <div class="stripe-donation-info">
            <div class="stripe-left-content">
                <div class="logo_mobile">
                    <img src="<?php echo $logo_url;?>" width="41" height="50" /> 
                </div>
                <div class="strip-top-left-img">
                    <img src="<?php echo $main_banner;?>" />
                </div>
                <div class="stripe-bottom-content">
                    <div class="logo_desktop">
                        <img src="<?php echo $logo_url;?>" width="41" height="50" /> 
                    </div>
                    <h3><?php echo $main_title;?></h3>
                    <p class="text-line-clamp-6"><?php echo $main_description;?></p>
                </div>
            </div>
            <div id="primary">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">                
                                <span class="paymentErrors alert-danger"></span>
                                <form action="" method="POST" id="paymentForm"> 
                                    <div class="step1 form_steps">
                                        <div class="part_collections">
                                            <div class="row">
                                                <div class="row heading title">
                                                    <div class="col-xs-2 pr-0">
                                                        <svg fill="none" height="28" viewBox="0 0 28 28" width="28" xmlns="http://www.w3.org/2000/svg" class="text-green-80 icon-fill d-block"><g fill="currentColor"><path clip-rule="evenodd" d="m15.0393 2.441000c-.6707-.24772-1.4079-.24772-2.0786 0l-8.30715 3.06797c-.39274.14504-.65355.51939-.65355.93807v7.54411c0 3.6861 1.95965 6.6874 4.28911 8.8073 1.16017 1.0557 2.38789 1.8689 3.45309 2.4137 1.1081.5668 1.9145.779 2.2578.779s1.1497-.2122 2.2578-.779c1.0652-.5448 2.2929-1.358 3.4531-2.4137 2.3294-2.1199 4.2891-5.1212 4.2891-8.8073v-7.54411c0-.41868-.2608-.79303-.6536-.93807zm-2.7715-1.876139c1.1179-.412868 2.3465-.412868 3.4644 0l8.3071 3.067969c1.1783.43514 1.9607 1.55819 1.9607 2.81421v7.54411c0 4.4389-2.3618 7.9375-4.943 10.2865-1.2952 1.1786-2.6702 2.092-3.8885 2.7151-1.1754.6012-2.3332.9984-3.1685.9984s-1.9931-.3972-3.1685-.9984c-1.21831-.6231-2.59328-1.5365-3.88847-2.7151-2.58125-2.349-4.94303-5.8476-4.94303-10.2865v-7.54411c0-1.25602.78243-2.37907 1.96066-2.81421z" fill-rule="evenodd"></path><path d="m18.2906 11.75h-.2535v-1.1855c0-2.19278-1.7415-4.02451-3.9182-4.06361-.0595-.00107-.1783-.00107-.2378 0-2.1767.0391-3.91819 1.87083-3.91819 4.06361v1.1855h-.25354c-.39069 0-.70937.4028-.70937.9003v5.9463c0 .4969.31868.9035.7094.9035h8.5812c.3907 0 .7094-.4066.7094-.9035v-5.9463c0-.4974-.3187-.9003-.7094-.9003zm-3.4867 3.8674v1.7967c0 .2058-.1723.3799-.3784.3799h-.8509c-.2061 0-.3785-.1741-.3785-.3799v-1.7967c-.1999-.1966-.3162-.4684-.3162-.7691 0-.5698.4408-1.0594 1.0013-1.082.0594-.0024.1783-.0024.2377 0 .5605.0226 1.0013.5122 1.0013 1.082 0 .3007-.1164.5725-.3163.7691zm1.5623-3.8674h-4.7323v-1.1855c0-1.30621 1.0623-2.38623 2.3661-2.38623s2.3662 1.08002 2.3662 2.38623z"></path></g></svg>
                                                    </div>
                                                     <div class="col-xs-10 pr-0">
                                                        <h2 class="title-1 text-truncate">Secure transaction</h2>
                                                     </div>
                                                 </div>             
                                                
                                                <div class="col-xs-12">
                                                    <div class="form-group select_amount_type btn-one-radius active" type="onetime">
                                                        <label>Give Once</label>
                                                    </div>  
                                                    <div class="form-group select_amount_type btn-two-radius" type="monthly">
                                                        <label> <span class="flex-shrink-0 mr-1" style="position: relative;"><span class="d-block p-rel">

                                                                <span class="d-block p-rel z-index-1 add_heart">

                                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" class="red_heart d-block"><path d="M9 14.1538C20.106 8.03634 13.1743 1.03334 9 5.32069C4.82567 1.03329 -2.106 8.03629 9 14.1538Z" fill="url(#fh-4210030669929289299)"></path><defs><radialGradient id="fh-4210030669929289299" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(9 5.16275) rotate(90) scale(8.9911 10.6258)"><stop stop-color="#F45D5A"></stop><stop offset="0.465615" stop-color="#F55E5B"></stop><stop offset="0.808237" stop-color="#F21814"></stop><stop offset="1" stop-color="#F00905"></stop></radialGradient></defs></svg>

                                                                    <svg style="display:none;" cwidth="18" height="18" viewBox="0 0 18 18" fill="none" class="d-block white_heart"><path d="M9 14.1538C20.106 8.03634 13.1743 1.03334 9 5.32069C4.82567 1.03329 -2.106 8.03629 9 14.1538Z" fill="white"></path><defs><radialGradient id="fh-1088343141171617791" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(9 5.16275) rotate(90) scale(8.9911 10.6258)"><stop stop-color="#F45D5A"></stop><stop offset="0.465615" stop-color="#F55E5B"></stop><stop offset="0.808237" stop-color="#F21814"></stop><stop offset="1" stop-color="#F00905"></stop></radialGradient></defs></svg>
                                                                </span>

                                                                <span class="p-abs top-left z-index-2">     
                                                                <span class="heart-list"><span class="heart p-abs"><span class="heart-holder heart-y-move-enter"><span class="heart-x-move"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" class="d-block"><path d="M9 14.1538C20.106 8.03634 13.1743 1.03334 9 5.32069C4.82567 1.03329 -2.106 8.03629 9 14.1538Z" fill="url(#fh-4210030669929289299)"></path><defs><radialGradient id="fh-4210030669929289299" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(9 5.16275) rotate(90) scale(8.9911 10.6258)"><stop stop-color="#F45D5A"></stop><stop offset="0.465615" stop-color="#F55E5B"></stop><stop offset="0.808237" stop-color="#F21814"></stop><stop offset="1" stop-color="#F00905"></stop></radialGradient></defs></svg></span></span></span></span>                                 
                                                                </span> 
                   
                                                            </span></span>

                                                        Monthly</label>
                                                    </div>                                              
                                                </div>  
                                            </div>
                                            <div id="onetime">
                                                <div class="row">
                                                    <?php
                                                        $priceOptions = explode(',',$config[$slug]['price']['options']);
                                                        $pricevalue = explode(',',$config[$slug]['price']['value']);
                                                        $i=0;
                                                        foreach($priceOptions as $priceOption){
                                                            $priceCents = trim($pricevalue[$i])*100;
                                                            if($i==1){$active = "active";}else{$active = "";}
                                                            echo '<div class="col-xs-6">
                                                                <div class="form-group select_amount '.$active.'" amount="'.$priceCents.'">
                                                                    <label>$'.trim($pricevalue[$i]).' <span> - '.trim($priceOption).'</span></label>
                                                                </div>  
                                                            </div>  ';
                                                            $i++;
                                                        }
                                                    ?>
                                                </div>  
                                            </div>
                                            <div id="monthly" style="display: none;">
                                                <div class="row">
                                                    <?php
                                                        $priceOptions = explode(',',$config[$slug]['price']['options']);
                                                        $pricevalue = explode(',',$config[$slug]['price']['value']);
                                                        $i=0;
                                                        foreach($priceOptions as $priceOption){
                                                            $priceCents = trim($pricevalue[$i])*100;
                                                            if($i==1){$active = "active";}else{$active = "";}
                                                            echo '<div class="col-xs-6">
                                                                <div class="form-group select_amount_monthly '.$active.'" amount="'.$priceCents.'">
                                                                    <label>$'.trim($pricevalue[$i]).' <span> - '.trim($priceOption).'</span></label>
                                                                </div>  
                                                            </div>  ';
                                                            $i++;
                                                        }
                                                    ?>
                                                </div>  
                                            </div>                                                        
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <input type="checkbox" id="dedicate" name="dedicate" class="form-control1" checked>&nbsp;&nbsp;Dedicate this purchase
                                                        <input type="text" id="specialCustomerName" name="specialCustomerName" class="form-control" placeholder="Name of someone special to me">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <label class="open_comment_step" style="text-decoration: underline;">Add comment</label>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <span step="1" class="btn btn-success next payment_type_selected">
                                                            Help Now
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="comment_step form_steps">
                                        <div class="part_collections">
                                            <div class="header header-desktop">
                                                <div class="header-aside">
                                                    <div class="icon-slot icon-slot-32">
                                                        <div class="p-abs centered">
                                                            <button step="1" class="header-back-screen" type="button" aria-label="Back" data-qa="back-checkout" data-tracking-element-name="backButton"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block" aria-hidden="true" data-testid="back-icon"><path d="M16.5 9L11.5 14L16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="header-main header-main-desktop">
                                                    <h4 class="section-header-container">Comment</h4>
                                                </div>
                                                <div class="header-aside">
                                                    <div class="icon-slot icon-slot-32"></div>
                                                </div>
                                            </div>
                                            <div class="mid_part">
                                                <p><textarea name="comment" id="comment" class="form-control" placeholder="Enter your comment" data-qa="comment" data-testid="comment-input" maxlength="500" style="min-height: 300px;"></textarea></p>
                                            </div>    
                                            <div class="foot_part">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <span step="0" id="" class="btn btn-success next">
                                                            Save
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step2 form_steps">
                                        <div class="part_collections">
                                            <div class="header header-desktop">
                                                <div class="header-aside">
                                                    <div class="icon-slot icon-slot-32">
                                                        <div class="p-abs centered">
                                                            <button step="1" class="header-back-screen" type="button" aria-label="Back" data-qa="back-checkout" data-tracking-element-name="backButton"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block" aria-hidden="true" data-testid="back-icon"><path d="M16.5 9L11.5 14L16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="header-main header-main-desktop">
                                                    <h4 class="section-header-container">Become a monthly supporter</h4>
                                                </div>
                                                <div class="header-aside">
                                                    <div class="icon-slot icon-slot-32"></div>
                                                </div>
                                            </div>
                                            <div class="mid_part">
                                                <p class="text-center">Will you consider becoming one of <br/>our valued monthly supporters by<br/>converting your <strong><span class="total_amount_text">$20.00</span></strong> purchase<br/>into a monthly purchase?<br/><br/>Ongoing monthly purchases allow us<br/>to better focus on our mission</p>
                                            </div>    
                                            <div class="foot_part">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <svg class="gift_icon mt-1 ml-2" aria-hidden="true" fill="none" height="73" viewBox="0 0 72 73" width="72" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><filter id="a-5211837001430960921" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse" height="72.3115" width="71.1349" x="0" y="0"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" result="hardAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dx="6" dy="2"></feOffset><feGaussianBlur stdDeviation="4"></feGaussianBlur><feColorMatrix type="matrix" values="0 0 0 0 0.591667 0 0 0 0 0.175035 0 0 0 0 0.199399 0 0 0 0.25 0"></feColorMatrix><feBlend in2="BackgroundImageFix" mode="normal" result="effect1_dropShadow_8986_17614"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow_8986_17614" mode="normal" result="shape"></feBlend></filter><filter id="b-5211837001430960921" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse" height="61.6255" width="61.1349" x="2" y="6.54346"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feColorMatrix in="SourceAlpha" result="hardAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dx="3" dy="3"></feOffset><feGaussianBlur stdDeviation="1.5"></feGaussianBlur><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"></feColorMatrix><feBlend in2="BackgroundImageFix" mode="normal" result="effect1_dropShadow_8986_17614"></feBlend><feBlend in="SourceGraphic" in2="effect1_dropShadow_8986_17614" mode="normal" result="shape"></feBlend></filter><linearGradient id="c-5211837001430960921" gradientUnits="userSpaceOnUse" x1="33.358" x2="55.1048" y1="36.8415" y2="38.8628"><stop offset="0" stop-color="#ff6375"></stop><stop offset="1" stop-color="#f2394e"></stop></linearGradient><linearGradient id="d-5211837001430960921" gradientUnits="userSpaceOnUse" x1="57.652" x2="32.9253" y1="47.3215" y2="61.3441"><stop offset="0" stop-color="#db3448"></stop><stop offset="1" stop-color="#f2394e"></stop></linearGradient><linearGradient id="e-5211837001430960921"><stop offset="0" stop-color="#ccc"></stop><stop offset="1" stop-color="#fff" stop-opacity="0"></stop></linearGradient><linearGradient id="f-5211837001430960921" gradientUnits="userSpaceOnUse" x1="2.7417" x2="29.5784" xlink:href="#e-5211837001430960921" y1="5.62305" y2="16.674"></linearGradient><linearGradient id="g-5211837001430960921" gradientUnits="userSpaceOnUse" x1="57.6504" x2="30.8137" xlink:href="#e-5211837001430960921" y1="5.31152" y2="16.3625"></linearGradient><g filter="url(#a-5211837001430960921)"><g filter="url(#b-5211837001430960921)"><path d="m2 20.4733 27.5499-13.92.0169-.00984 27.5667 13.92894.0014.0009v6.8919l-2.4122 1.2362v20.6755l-25.1553 12.8922-25.15525-12.8922v-20.6755l-2.41215-1.2362z" fill="url(#c-5211837001430960921)"></path></g><path d="m4.41235 49.2779 25.15525 12.8921v-27.5674l-25.15525-12.8922z" fill="#ff6375"></path><path d="m54.723 49.2779-25.1553 12.8921v-27.5674l25.1553-12.8922z" fill="#fb4d62"></path><path d="m4.41235 30.3253 25.15525 12.8921v-8.6148l-25.15525-12.8922z" fill="#db3448" fill-opacity=".75"></path><path d="m54.723 30.3253-25.1553 12.8921v-8.6148l25.1553-12.8922z" fill="#db3448" fill-opacity=".5"></path><path d="m29.5499 6.5533-27.5499 13.92 27.5668 14.1274 27.5667-14.1283-27.5667-13.92894z" fill="#ff7787"></path><path d="m2 20.4731 27.5674 14.1284v6.8918l-27.5674-14.1283z" fill="#fb4d62"></path><path d="m57.1345 20.4731-27.5674 14.1284v6.8918l27.5674-14.1283z" fill="#ff6375"></path><path d="m12.3378 53.3394v-27.5674l7.2364 3.7086v27.5675z" fill="#dcdcdc"></path><path d="m47.1403 25.5949-7.2149 3.6978-10.1245-5.0269-10.2267 5.2146-7.223-3.7014 17.3508-8.8229z" fill="#f4f4f4"></path><path d="m47.142 25.5952-7.2365 3.7087v27.5674l7.2365-3.7087z" fill="#dcdcdc"></path><path d="m29.5676 62.3113-25.15525-12.8921v-.6892l25.15525 12.8921 25.1553-12.8921v.6892z" fill="url(#d-5211837001430960921)" fill-opacity=".2"></path><path d="m45.8044 17.4261c-3.6871-.6226-7.6011 1.431-10.2537 2.8227-.9582.5027-1.7518.9191-2.3105 1.0917-.3618-.1451-.8792-.236-1.4538-.236h-3.9611c-.5567 0-1.0598.0853-1.4196.2226-.5213-.2107-1.1855-.5592-1.9564-.9637-2.6526-1.3917-6.5666-3.4453-10.2537-2.8227-3.9608.6687-4.65037 4.4568-3.9609 4.9658.4446.4109 4.6365.4646 15.7653-.1146.3474.2122.5804.9196 2 .9196h3.7864c.8521 0 1.9341-.7195 2.2136-1 11.2947.5993 15.3177.4941 15.7653.0804.6894-.509-.0001-4.2971-3.9609-4.9658z" fill="#db3448" fill-opacity=".5"></path><path d="m10.469 20.0297-.004-.0098-.0047-.0094c-.0642-.1276-.1269-.3985-.1665-.8141-.0386-.4048-.0529-.9172-.0381-1.5046.0295-1.1746.1748-2.6343.4655-4.1076.291-1.4746.7256-2.9523 1.3292-4.16833.6061-1.22108 1.3661-2.14551 2.2897-2.56537 1.2648-.5749 2.5766-.25259 3.9015.62535 1.3277.87973 2.6251 2.29152 3.8183 3.80205.8432 1.0675 1.6238 2.1702 2.3226 3.1573.2902.4098.5662.7997.8267 1.1588.4409.6078.84 1.1311 1.1842 1.5038.1718.1861.3371.3425.4931.454.0795.0568.1653.1084.2555.1443v3.2766c-5.8992.8884-9.9813.9034-12.6427.5588-1.3471-.1744-2.3223-.4399-2.9824-.7258-.33-.1429-.5757-.2885-.7487-.426-.1748-.1391-.2631-.2601-.2992-.35z" fill="#f4f4f4" stroke="url(#f-5211837001430960921)" stroke-width=".5"></path><path d="m49.9231 19.7182.004-.0098.0047-.0094c.0642-.1276.1269-.3985.1665-.8141.0386-.4048.0528-.9172.0381-1.5046-.0295-1.1746-.1748-2.6343-.4655-4.1076-.291-1.4746-.7257-2.9523-1.3292-4.16835-.6061-1.22108-1.3661-2.14551-2.2897-2.56537-1.2648-.5749-2.5766-.25259-3.9015.62535-1.3277.87973-2.6251 2.29151-3.8183 3.80207-.8432 1.0674-1.6238 2.1702-2.3226 3.1573-.2902.4098-.5662.7996-.8267 1.1588-.4409.6078-.84 1.1311-1.1842 1.5038-.1719.1861-.3371.3424-.4931.454-.0795.0568-.1654.1083-.2555.1443v3.2766c5.8992.8884 9.9813.9033 12.6427.5588 1.3471-.1744 2.3223-.44 2.9824-.7258.33-.1429.5757-.2885.7486-.426.1749-.1391.2632-.2601.2993-.35z" fill="#f4f4f4" stroke="url(#g-5211837001430960921)" stroke-width=".5"></path><path d="m27.3922 17.498c-1.6553.0001-7.6553-13.37515-13.1553-10.87509-3.5 2.00014 9.4703 11.74249 13.1553 10.87509z" fill="#dcdcdc"></path><path d="m32.9999 17.1864c1.6553.0002 7.6553-13.37507 13.1553-10.87501 3.5 2.00013-9.4703 11.74251-13.1553 10.87501z" fill="#dcdcdc"></path><path d="m26.5256 18.0437c.2721-.9838 1.2851-1.3759 2.3057-1.3805.3098-.0014.6782-.0249 1.1217-.0812.5022-.0638.9385-.1108 1.309-.1455.9169-.0858 1.7218.5308 1.8747 1.4389.2481 1.4727.4432 3.5148-.3104 3.8119-1.1972.4719-3.5916-.2431-5.7466 0-1.3613.1535-1.0028-2.0209-.5541-3.6436z" fill="#f4f4f4"></path></g></svg>
                                                        <span step="2" id="" amount="2000" class="btn btn-success next btn btn-danger make_it_monthly">
                                                            Help Now <span class="total_amount_text">$20.00</span>/month
                                                        </span>
                                                        <span step="2" id="keep_it_one_time" class="btn btn-success next btn">
                                                            Keep my one-time <span class="total_amount_text">$20.00</span> purchase
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="step3 form_steps">
                                        <div class="part_collections">
                                            <div class="header header-desktop">
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32">
                                                            <div class="p-abs centered">
                                                                <button step="2" class="header-back-screen payment_type_selected_skip" type="button" aria-label="Back" data-qa="back-checkout" data-tracking-element-name="backButton"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block" aria-hidden="true" data-testid="back-icon"><path d="M16.5 9L11.5 14L16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="header-main header-main-desktop">
                                                        <h4 class="section-header-container">Payment option</h4>
                                                    </div>
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32"></div>
                                                    </div>
                                            </div>
                                            <div class="mid_part">
                                                <div align="center">
                                                    <div class="heading">
                                                        <span class="total_amount_text">$20.00</span> 
                                                        <span>USD</span><span class="per_onetime_month" style="display: none;">/month</span>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="form-group cover_cost_div">
                                                            <div class="star_icons_svg">
                                                            <svg class="star_icons" aria-hidden="true" fill="none" height="68" viewBox="0 0 56 68" width="56" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><filter id="a-7752449082606388807" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse" height="31.4131" width="30.8215" x="7.42749" y="10.3657"><feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood><feBlend in="SourceGraphic" in2="BackgroundImageFix" mode="normal" result="shape"></feBlend><feColorMatrix in="SourceAlpha" result="hardAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"></feColorMatrix><feOffset dy="-.5"></feOffset><feGaussianBlur stdDeviation="1.5"></feGaussianBlur><feComposite in2="hardAlpha" k2="-1" k3="1" operator="arithmetic"></feComposite><feColorMatrix type="matrix" values="0 0 0 0 0.979167 0 0 0 0 0.3525 0 0 0 0 0 0 0 0 0.5 0"></feColorMatrix><feBlend in2="shape" mode="normal" result="effect1_innerShadow_8986_21698"></feBlend></filter><linearGradient id="b-7752449082606388807" gradientUnits="userSpaceOnUse" x1="23.3018" x2="33.5" y1="25.1587" y2="35"><stop offset="0" stop-color="#ffba07"></stop><stop offset="1" stop-color="#ff8a00"></stop></linearGradient><radialGradient id="c-7752449082606388807" cx="0" cy="0" gradientTransform="matrix(3.64090919 -8.738167 7.18621663 2.99426209 18.9327 24.4305)" gradientUnits="userSpaceOnUse" r="1"><stop offset=".317708" stop-color="#fff491"></stop><stop offset="1" stop-color="#fff490" stop-opacity="0"></stop></radialGradient><linearGradient id="d-7752449082606388807" gradientUnits="userSpaceOnUse" x1="25.8578" x2="24.1154" y1="57.3837" y2="54.6061"><stop offset="0" stop-color="#ff522d"></stop><stop offset="1" stop-color="#ff7b5f"></stop></linearGradient><radialGradient id="e-7752449082606388807" cx="0" cy="0" gradientTransform="matrix(2.583374 -.29682138 .24410427 2.12455258 24.357 55.5499)" gradientUnits="userSpaceOnUse" r="1"><stop offset=".317708" stop-color="#ffd3ab"></stop><stop offset="1" stop-color="#ffd3ab" stop-opacity="0"></stop></radialGradient><clipPath id="f-7752449082606388807"><path d="m0 0h56v68h-56z"></path></clipPath><g clip-path="url(#f-7752449082606388807)"><g filter="url(#a-7752449082606388807)"><path d="m15.7885 11.8705c-.0023-.8288.9468-1.3005 1.6061-.7982l7.2797 5.5466c.2618.11000.605.2574.9177.1548l8.6962-2.8524c.7875-.2583 1.5294.4986 1.2554 1.2808l-3.0256 8.6374c-.1088.3106-.0578.6549.1364.9206l5.4001 7.3891c.489.6692-.0015 1.6087-.8302 1.5898l-9.1496-.2084c-.329-.0075-.6407.1474-.8334.4142l-5.3588 7.4191c-.4853.6719-1.5303.4957-1.7685-.2982l-2.6292-8.7662c-.0945-.3153-.3381-.5638-.6514-.6647l-8.71196-2.8038c-.78898-.2539-.94431-1.3023-.26285-1.7741l7.52471-5.2094c.2706-.1874.4317-.4959.4308-.825z" fill="url(#b-7752449082606388807)"></path><path d="m15.7885 11.8705c-.0023-.8288.9468-1.3005 1.6061-.7982l7.2797 5.5466c.2618.11000.605.2574.9177.1548l8.6962-2.8524c.7875-.2583 1.5294.4986 1.2554 1.2808l-3.0256 8.6374c-.1088.3106-.0578.6549.1364.9206l5.4001 7.3891c.489.6692-.0015 1.6087-.8302 1.5898l-9.1496-.2084c-.329-.0075-.6407.1474-.8334.4142l-5.3588 7.4191c-.4853.6719-1.5303.4957-1.7685-.2982l-2.6292-8.7662c-.0945-.3153-.3381-.5638-.6514-.6647l-8.71196-2.8038c-.78898-.2539-.94431-1.3023-.26285-1.7741l7.52471-5.2094c.2706-.1874.4317-.4959.4308-.825z" fill="url(#c-7752449082606388807)" fill-opacity=".4"></path></g><path d="m27.6127 53.2098c.4692-.1126.8099.4391.4991.8082l-1.1699 1.3895c-.1023.1215-.1408.2843-.1038.4387l.4238 1.7663c.1126.4692-.4392.8099-.8083.4991l-1.3894-1.17c-.1215-.1022-.2843-.1407-.4387-.1037l-1.7663.4238c-.4692.1125-.8099-.4392-.4991-.8083l1.1699-1.3894c.1023-.1215.1408-.2843.1038-.4387l-.4238-1.7663c-.1126-.4693.4392-.81.8083-.4991l1.3894 1.1699c.1215.1023.2843.1408.4387.1038z" fill="url(#d-7752449082606388807)"></path><path d="m27.6127 53.2098c.4692-.1126.8099.4391.4991.8082l-1.1699 1.3895c-.1023.1215-.1408.2843-.1038.4387l.4238 1.7663c.1126.4692-.4392.8099-.8083.4991l-1.3894-1.17c-.1215-.1022-.2843-.1407-.4387-.1037l-1.7663.4238c-.4692.1125-.8099-.4392-.4991-.8083l1.1699-1.3894c.1023-.1215.1408-.2843.1038-.4387l-.4238-1.7663c-.1126-.4693.4392-.81.8083-.4991l1.3894 1.1699c.1215.1023.2843.1408.4387.1038z" fill="url(#e-7752449082606388807)" fill-opacity=".4"></path><path d="m50.0525 13.2436c.3656-.2587.8608.0569.7807.4976l-.3906 2.1497c-.024.1322.0061.2685.0838.3782l1.262 1.7836c.2587.3656-.0569.8608-.4975.7807l-2.1498-.3906c-.1322-.024-.2685.0062-.3782.0838l-1.7836 1.262c-.3656.2588-.8608-.0568-.7807-.4975l.3906-2.1497c.024-.1322-.0062-.2685-.0838-.3782l-1.262-1.7836c-.2587-.3656.0568-.8608.4975-.7808l2.1498.3907c.1322.024.2685-.0062.3782-.0838z" fill="#f90"></path><path d="m9.40768 43.4483c.0297-.68.87682-.972 1.31922-.4549l.9325 1.0901c.1351.1579.3296.2527.5372.2618l1.4332.0626c.6799.0297.9719.8767.4548 1.3192l-1.0901.9325c-.1579.1351-.2527.3296-.2617.5372l-.0626 1.4331c-.0297.68-.8768.972-1.3192.4549l-.9326-1.0901c-.135-.1579-.3296-.2527-.53714-.2617l-1.43317-.0627c-.67992-.0297-.97197-.8767-.45482-1.3191l1.09007-.9326c.15789-.1351.25267-.3296.26174-.5372z" fill="#8ae5f1"></path><circle cx="28.5" cy="4.5" fill="#7dc6ff" r="2.5"></circle><circle cx="15.5" cy="66.5" fill="#ff7f24" r="1.5"></circle><circle cx="49.5" cy="1.5" fill="#8ae5f1" r="1.5"></circle></g></svg>
                                                            </div>
                                                            <span class="btn btn-success btn add_fees_group">
                                                                <input type="checkbox" class="add_fees" name="add_fees" checked/>
                                                                <label>Cover $<span class="stripe_fees" style="">0.88</span> transaction costs</label>                                                        
                                                            </span>
                                                            <div class="tip_icon cover_cost">
                                                                <span class="text-gray-45 font-size-18" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block"><path d="M8.00016 14.6668C11.6821 14.6668 14.6668 11.6821 14.6668 8.00016C14.6668 4.31826 11.6821 1.3335 8.00016 1.3335C4.31826 1.3335 1.3335 4.31826 1.3335 8.00016C1.3335 11.6821 4.31826 14.6668 8.00016 14.6668Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.06006 5.99989C6.21679 5.55434 6.52616 5.17863 6.93336 4.93931C7.34056 4.7 7.81932 4.61252 8.28484 4.69237C8.75036 4.77222 9.1726 5.01424 9.47678 5.37558C9.78095 5.73691 9.94743 6.19424 9.94672 6.66656C9.94672 7.99989 7.94673 8.66656 7.94673 8.66656" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8 11.2446H8.0075" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
                                                            </div>
                                                            <div class="tip_info cover_cost" style="display: none;">
                                                                <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip"> When you cover our transaction costs, you’re covering our Stripe payment processing platform costs</div><div class="ui-tooltip-arrow" style="top: -6px;left: 134px;"></div></div>
                                                            </div>
                                                            <div class="transaction_cost_alert">
                                                                    <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip">Would you like to cover the transaction costs so that we receive 100% of your purchase?</div><div class="ui-tooltip-arrow" style="top: -6px; left: 127px;"></div></div>
                                                            </div>
                                                            <div class="payment_summary" style="display: none">
                                                                <!-- Displayed none as not in use-->
                                                                <br/>
                                                                <div class="transaction_cost "><strong>Transaction costs</strong>
                                                                &nbsp;&nbsp;&nbsp;<span class="stripe_fees" style="margin-right: 20px;">0.88</span><span>$</span></div>
                                                                <div class="final_transaction_cost"><strong>Help Now</strong>&nbsp;&nbsp;&nbsp;<span class="final_amount" style="margin-right: 20px;">20.78</span><span>$</span></div>
                                                            </div>    

                                                        </div>
                                                    </div> 
                                                    <input type="hidden" id="total_amount" name="total_amount" value="2078">
                                                    <input type="hidden" id="final_amount" name="final_amount" value="2078">
                                                    <input type="hidden" id="select_payment_type" name="select_payment_type" value="onetime">
                                                </div>
                                            </div>
                                            <div class="foot_part">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <span step="3" id="" class="btn btn-success next">
                                                            Credit Card
                                                        </span>
                                                        <script src="https://js.stripe.com/v3/"></script>
                                                        <div id="payment-request-button">
                                                          <!-- A Stripe Element will be inserted here. -->
                                                        </div>
                                                        <script>
                                                            function payButton(amount, title="", description=""){
                                                                $(".card_error_message").hide();  
                                                                const stripe = Stripe('pk_test_51M2gbdJaLA4XRgtI9FH4cyXsYm37Mdce9bTLsXpmP2uR57YGCQIAeRU9LasFTnhVQm73oCvaaMFmxZAyWdN7m7d900dsCRwm3m', {
                                                                      apiVersion: "2022-08-01",
                                                                    });  
                                                                clientSecret = "";
                                                                const paymentRequest = stripe.paymentRequest({           
                                                                  country: 'US',
                                                                  currency: 'usd',
                                                                  total: {
                                                                    label: title + " "+ description,
                                                                    amount: parseInt(amount),
                                                                  },
                                                                  requestPayerName: true,
                                                                  requestPayerEmail: true,
                                                                });    
                                                                  
                                                                const elements = stripe.elements();
                                                                const prButton = elements.create('paymentRequestButton', {
                                                                  paymentRequest,
                                                                  style: {
                                                                    paymentRequestButton: {
                                                                      type: 'default',
                                                                      theme: 'dark',
                                                                      height: '48px',
                                                                      height: '48px',
                                                                    },
                                                                  },
                                                                });

                                                                (async () => {
                                                                  // Check the availability of the Payment Request API first.
                                                                  const result = await paymentRequest.canMakePayment();
                                                                  if (result) {          
                                                                  var formData = $('#paymentForm').serialize();                                     
                                                                    $.ajax({
                                                                        type: "POST",
                                                                        cache: false,
                                                                        async: false,
                                                                        url: "functions.php",
                                                                        data: {myData: formData, final_amount: amount, action: 'payment_intent'},
                                                                        success: function (msg) {
                                                                            prButton.mount('#payment-request-button');
                                                                            clientSecret = msg;
                                                                        }
                                                                   });
                                                                  } else {
                                                                    document.getElementById('payment-request-button').style.display = 'none';
                                                                  }
                                                                })();

                                                                paymentRequest.on('paymentmethod', async (ev) => {
                                                                  // Confirm the PaymentIntent without handling potential next actions (yet).

                                                                  const {paymentIntent, error: confirmError} = await stripe.confirmCardPayment(
                                                                    clientSecret,
                                                                    {payment_method: ev.paymentMethod.id},
                                                                    {handleActions: false}
                                                                  );

                                                                  var acc = [];
                                                                  confirmErrorMessage= '';

                                                                  if (confirmError) {
                                                                    $.each(confirmError, function(index, value) {
                                                                        if (index == "message") {
                                                                            confirmErrorMessage = value;
                                                                            return false;
                                                                        }
                                                                    });
                                                                    // Report to the browser that the payment failed, prompting it to
                                                                    // re-show the payment interface, or show an error message and close
                                                                    // the payment interface.
                                                                    step = 4;
                                                                    $(".form_steps").hide();

                                                                    $(".step"+step).show("slide", { direction: "right" }, 500);
                                                                    $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
                                                                    $(".step"+step).css('display', 'flex');    
                                                                    setTimeout(function(){ 
                                                                       $(".step"+step).css('display', 'flex');    
                                                                    }, 507);

                                                                    $(".card_error_message").show();  
                                                                    $(".card_error_message div div.ui-tooltip-body").text(confirmErrorMessage);
                                                                    ev.complete('fail');
                                                                  } else {
                                                                    // Report to the browser that the confirmation was successful, prompting
                                                                    // it to close the browser payment method collection interface.
                                                                    ev.complete('success');
                                                                    // Check if the PaymentIntent requires any actions and, if so, let Stripe.js
                                                                    // handle the flow. If using an API version older than "2019-02-11"
                                                                    // instead check for: `paymentIntent.status === "requires_source_action"`.
                                                                    if (paymentIntent.status === "requires_action") {
                                                                      // Let Stripe.js handle the rest of the payment flow.
                                                                      const {error} = await stripe.confirmCardPayment(clientSecret);
                                                                      if (error) {
                                                                        $.each(error, function(index, value) {
                                                                            if (index == "message") {
                                                                                confirmErrorMessage = value;
                                                                                return false;
                                                                            }
                                                                        });
                                                                        // The payment failed -- ask your customer for a new payment method.
                                                                        step = 4;
                                                                        $(".form_steps").hide();
                                                                        $(".step"+step).show("slide", { direction: "right" }, 500);
                                                                        $(".step"+step).parent().siblings(":visible").hide("slide", { direction: "right" }, 500);
                                                                        $(".step"+step).css('display', 'flex');    
                                                                        setTimeout(function(){ 
                                                                           $(".step"+step).css('display', 'flex');    
                                                                        }, 507);

                                                                        $(".card_error_message").show();  
                                                                        $(".card_error_message div div.ui-tooltip-body").text(confirmErrorMessage);  
                                                                      } else {
                                                                        // The payment has succeeded -- show a success message to your customer.
                                                                        //alert("Thanks for donation");
                                                                        window.parent.location.href = "<?php echo $thank_you_url;?>";
                                                                      }
                                                                    } else {
                                                                        //alert("Thanks for donation");
                                                                        window.parent.location.href = "<?php echo $thank_you_url;?>";
                                                                       // The payment has succeeded -- show a success message to your customer.
                                                                    }
                                                                  }
                                                                });
                                                            }
                                                        </script>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>                                
                                    </div>   
                                    <div class="step4 form_steps">
                                        <div class="part_collections">
                                            <div class="header header-desktop">
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32">
                                                            <div class="p-abs centered">
                                                                <button step="3" class="header-back-screen" type="button" aria-label="Back" data-qa="back-checkout" data-tracking-element-name="backButton"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block" aria-hidden="true" data-testid="back-icon"><path d="M16.5 9L11.5 14L16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="header-main header-main-desktop">
                                                        <h4 class="section-header-container">Credit card</h4>
                                                    </div>
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32"></div>
                                                    </div>
                                            </div>
                                            <div class="mid_part">
                                                <p class="text-center">Please provide your card details<br/>to continue with your donation.<br/>This card will be charged.</p>
                                                <div id="donate_by_stripe"> 
                                                    <div class="form-group fl card_number_icon">
                                                        <input placeholder="Card number" type="text" name="cardNumber" size="20" autocomplete="off" id="cardNumber" class="form-control" />
                                                    </div>  
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <div class="form-group">
                                                                <input type="text" name="cardExpMonth" placeholder="MM" size="2" id="cardExpMonth" class="form-control" /> 
                                                            </div>  
                                                        </div>  
                                                        <div class="col-xs-4">
                                                            <div class="form-group">
                                                                <input type="text" name="cardExpYear" placeholder="YYYY" size="4" id="cardExpYear" class="form-control" />
                                                            </div>  
                                                        </div>  
                                                        <div class="col-xs-4" style="padding-right: 15px;">
                                                            <div class="form-group cvc_info">
                                                                <input placeholder="CVC" type="password" name="cardCVC" size="4" autocomplete="off" id="cardCVC" class="form-control" />
                                                                <div class="tip_icon">
                                                                    <span class="text-gray-45 font-size-18" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block"><path d="M8.00016 14.6668C11.6821 14.6668 14.6668 11.6821 14.6668 8.00016C14.6668 4.31826 11.6821 1.3335 8.00016 1.3335C4.31826 1.3335 1.3335 4.31826 1.3335 8.00016C1.3335 11.6821 4.31826 14.6668 8.00016 14.6668Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.06006 5.99989C6.21679 5.55434 6.52616 5.17863 6.93336 4.93931C7.34056 4.7 7.81932 4.61252 8.28484 4.69237C8.75036 4.77222 9.1726 5.01424 9.47678 5.37558C9.78095 5.73691 9.94743 6.19424 9.94672 6.66656C9.94672 7.99989 7.94673 8.66656 7.94673 8.66656" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8 11.2446H8.0075" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></span>
                                                                </div>
                                                                <div class="tip_info" style="display: none;">
                                                                    <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip">For MasterCard or Visa it is the last three digits in the signature area on the back of your card. For American Express it is the four digits on the front of the card.</div><div class="ui-tooltip-arrow" style="top: -6px;left: 127px;"></div></div>
                                                                </div>
                                                            </div>  
                                                        </div>                                        
                                                    </div>                      
                                                </div>
                                            </div>
                                            <div class="foot_part">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <span step="4" id="validateCard" class="btn btn-success">
                                                            Continue
                                                        </span>
                                                        <span class="card_error_message">
                                                            <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip"></div><div class="ui-tooltip-arrow" style="top: 64px;left: 127px;"></div></div>
                                                        </span>
                                                    </div>
                                                </div> 
                                            </div>    
                                        </div> 
                                    </div>    
                                    <div class="step5 form_steps">
                                        <div class="part_collections">
                                            <div class="header header-desktop">
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32">
                                                            <div class="p-abs centered">
                                                                <button step="4" class="header-back-screen" type="button" aria-label="Back" data-qa="back-checkout" data-tracking-element-name="backButton"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-stroke d-block" aria-hidden="true" data-testid="back-icon"><path d="M16.5 9L11.5 14L16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="header-main header-main-desktop">
                                                        <h4 class="section-header-container">Personal information</h4>
                                                    </div>
                                                    <div class="header-aside">
                                                        <div class="icon-slot icon-slot-32"></div>
                                                    </div>
                                            </div>
                                            <div class="mid_part">
                                                <p class="text-center">Please provide your details so we can contact you about your donation.</p>
                                                <div class="form-group">
                                                    <input placeholder="First name" type="text" id="customerName" name="custName" class="customer form-control">
                                                </div>
                                                <div class="form-group">
                                                    <input placeholder="Last name" type="text" id="customerLastName" name="customerLastName" class="customer form-control">
                                                </div>
                                                <div class="form-group">
                                                    <input  placeholder="Email" type="email" id="emailAddress"  name="custEmail" class="customer form-control">
                                                </div>
                                                <div class="form-group terms_checkbox">
                                                    <div>
                                                         <input type="checkbox" id="good_news"  name="good_news" class="form-control1" checked>&nbsp;&nbsp;Add Paws Digest to my inbox<br/>
                                                    </div>
                                                    <div>
                                                        <input type="checkbox" id="terms_policy"  name="terms_policy" class="form-control1" checked>&nbsp;&nbsp;I agree to the <a href="<?php echo $terms_policy_url;?>" target="_blank">Terms and Privacy Policy</a>
                                                    </div>
                                                </div>
                                            </div>    
                                            <div class="foot_part">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <div align="center">
                                                            <input type="hidden" name="currency_code" value="USD">
                                                            <input type="hidden" name="item_details" value="Donation">
                                                            <input type="submit" id="makePayment" class="btn btn-success" onclick="stripePay(event)" value="Help Now">                                                    
                                                        </div>
                                                    </div>
                                                </div>  
                                            </div> 
                                        </div>    
                                    </div>                                                 
                                                                
                                </form> 
                                <div class='loader_gif' style="display: none;"><img src="images/loader.gif"/></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #primary -->
        </div>
        <div class="widget-footer widget-footer-desktop" data-qa="widget-footer-block">
            <div class="fixed-container">
                    <div class="widget-footer-panel widget-footer-panel-desktop">
                        <ul class="faq-links faq-links-desktop">
                            <li class="faq-link-item faq-link-item-desktop" data-qa="is-donation-secure">
                                <button type="button" class="faq-link faq-link-desktop">
                                    <span class="faq-link-title payment_secure_hover">Is my payment secure?</span></button>
                                <div class="footer_tip_info payment_secure_tips" style="display: none;">
                                    <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip"><strong>Is my donation secure?</strong><br/>Yes, we use industry-standard SSL technology to keep your information secure. We partner with Stripe, the industry's established payment processor trusted by some of the world's largest companies. Your sensitive financial information never touches our servers. We send all data directly to Stripe's PCI-compliant servers through SSL.</div><div class="ui-tooltip-arrow" style="bottom: -5px;left: 70px;top: unset;"></div></div>
                                </div>     
                            </li>
                            <li class="faq-link-item faq-link-item-desktop" data-qa="is-tax-deductible">
                                <button type="button" class="faq-link faq-link-desktop"><span class="faq-link-title payment_tax_deductible_click">Is this payment tax-deductible?</span></button>
                                <div class="footer_tip_info payment_tax_deductible_tips" style="display: none;">
                                    <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip"> <strong>Is this donation tax-deductible?</strong><br/>Your purchase is not tax-deductible, as we are not a tax-exempt organization. Your purchase allows for-profit companies to help underserved shelters who need it the most.<br/>We will email you a receipt. Please keep this as your official record of your purchase.</div><div class="ui-tooltip-arrow" style="bottom: -5px;left: 70px;top: unset;"></div></div>
                                </div>  
                            </li>
                            <li class="faq-link-item faq-link-item-desktop" data-qa="cancel-recurring-donation">
                                <button type="button" class="faq-link faq-link-desktop"><span class="faq-link-title recurring_payment_click">Can I cancel my recurring payment?</span></button>
                                <div class="footer_tip_info recurring_payment recurring_payment_tips" style="display: none;">
                                    <div class="ui-tooltip ui-tooltip-desktop ui-tooltip-bottom ui-tooltip-to-bottom-enter-done" role="status" aria-live="polite"><div class="ui-tooltip-body ui-tooltip-body-desktop" data-qa="cover-fee-tooltip"> <strong>Can I cancel my recurring payment?</strong><br/>Of course. You can cancel it any time by clicking on the link in your purchase receipt and following the instructions provided.</div><div class="ui-tooltip-arrow" style="bottom: -5px;left: 70px;top: unset;"></div></div>
                                </div>  
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </div>
    <div class="ui-widget-overlay ui-widget-overlay-missing" style="background: rgba(0, 0, 0, 0.5);"></div>
    <script src="https://formvalidation.io/vendors/@form-validation/umd/bundle/popular.min.js"></script>
    <script src="https://formvalidation.io/vendors/@form-validation/umd/plugin-tachyons/index.min.js"></script>
    <script>
            document.addEventListener('DOMContentLoaded', function (e) {
                FormValidation.formValidation(document.getElementById('paymentForm'), {
                    fields: {
                        cardNumber: {
                            validators: {
                                notEmpty: {
                                    message: 'The credit card number is required',
                                },
                                creditCard: {
                                    message: 'The credit card number is not valid',
                                },
                            },
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        tachyons: new FormValidation.plugins.Tachyons(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'fa fa-check',
                            invalid: 'fa fa-times',
                            validating: 'fa fa-refresh',
                        }),
                    },
                })
                    .on('core.validator.validated', function (e) {
                        if (e.field === 'cardNumber' && e.validator === 'creditCard' && e.result.valid) {
                            let icon = '';
                            // e.result.meta.type can be one of
                            // AMERICAN_EXPRESS, DINERS_CLUB, DINERS_CLUB_US, DISCOVER, JCB, LASER,
                            // MAESTRO, MASTERCARD, SOLO, UNIONPAY, VISA
                            switch (e.result.meta.type) {
                                case 'AMERICAN_EXPRESS':
                                    icon = 'fa fa-cc-amex';
                                    break;

                                case 'DISCOVER':
                                    icon = 'fa-cc-discover';
                                    break;

                                case 'MASTERCARD':
                                case 'DINERS_CLUB_US':
                                    icon = 'fa-cc-mastercard';
                                    break;

                                case 'VISA':
                                    icon = 'fa-cc-visa';
                                    break;

                                default:
                                    icon = 'fa-credit-card';
                                    break;
                            }

                            // Query the icon element
                            const iconEle = e.element.nextSibling;
                            iconEle.setAttribute('class', 'fv-plugins-icon ' + icon);
                        }
                    })
                    .on('core.element.validated', function (e) {
                        if (e.field === 'cardNumber' && !e.valid) {
                            const iconEle = e.element.nextSibling;
                            iconEle.setAttribute('class', 'fv-plugins-icon fa fa-times');
                        }
                    });
            });
        </script>
</body>
</html>    
