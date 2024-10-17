<?php
    include_once(dirname(__FILE__) ."/config.php");
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
<title>Help Form</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-creditcardvalidator/1.0.0/jquery.creditCardValidator.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
</head>
<body data-new-gr-c-s-check-loaded="14.1164.0" data-gr-ext-installed="">    
    <div class="stripe-donation-body">
        <div class="stripe-donation-info">
            <div class="stripe-left-content">
                <div class="logo_mobile">
                    <img src="https://www.werescued.org/help/images/download-img.png" width="41" height="50"> 
                </div>
                <div class="strip-top-left-img">
                    <img src="https://werescued.org/help/images/download.png">
                </div>
                <div class="stripe-bottom-content">
                    <div class="logo_desktop">
                        <img src="https://www.werescued.org/help/images/download-img.png" width="41" height="50"> <br/>
                    </div>
                    <h3>Thanks for help!</h3>
                    <p class="text-line-clamp-6"></p><p class="text-line-clamp-6">Your help means a lot.</p>
                    <p class="text-line-clamp-6 text-center text-underline"><a href="<?php echo $config[$slug]['main_url'];?>?form=<?php echo $slug;?>">Home</a></p>
                    <br/><br/>
                </div>
            </div>
        </div>
    </div>
    <div class="ui-widget-overlay ui-widget-overlay-missing" style="background: rgba(0, 0, 0, 0.5);"></div>
    </body>
<style>
.text-underline{text-decoration: underline;}    
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
.stripe-donation-body {margin: 0 auto;width: 400px;position: relative;flex-direction: column;z-index:2;}
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
    .select_amount label, .select_amount_monthly  label, .select_amount_type  label{background: #fff;}
    .step2 .mid_part p{margin-bottom: 50px;}.gift_icon{z-index:3;}
    .form_steps #onetime .col-xs-6, .form_steps #monthly .col-xs-6{width: 100%;}
    .stripe-donation-info{    border-radius:0;background: #fff;
    box-shadow: none;margin: 10px 0px 0px; display: block;}
    .cover_cost.tip_icon{right: 18px; top:19px}
    .panel-body{
            margin: 0 auto;
    padding: 10px;
    padding-top: 20px;
    padding-bottom: 20px;
    padding-left: 20px;
    padding-right: 20px;}
    .logo_mobile{    display: block;
        text-align: center;
        margin-top: 0px;
        margin-bottom: 10px;
    }
    form {margin: 0;}
    .header-desktop{border-bottom: none;}
    .stripe-bottom-content h3 {
        font-size: 20px;
        line-height: 34px;
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
</html>