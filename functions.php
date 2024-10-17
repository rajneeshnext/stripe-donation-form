<?php
    include_once(dirname(__FILE__) ."/config.php");
    if(isset($_GET['form'])){
        $slug = $_GET['form'];
    }else{
        $slug = "HelpPetsNow";
    }
    $stripe_clientID = $config[$slug]['STRIPE_CLIENT_ID'];
    $stripe_secretID = $config[$slug]['STRIPE_SECRET_KEY'];
    $paymentMessage="";
    global $wpdb; // this is how you get access to the database
    $params = array();
    $action = $_POST['action'];
    $totalAmount = $_POST['final_amount'];
    parse_str($_POST['myData'], $_POST);

    //include Stripe PHP library
    require_once('stripe-php/init.php'); 
    //set stripe secret key and publishable key  
    $stripe_IDs = array(
      "secret_key"      => "$stripe_secretID",
      "publishable_key" => "$stripe_clientID"
    );
    \Stripe\Stripe::setApiKey($stripe_IDs['secret_key']);                
    $stripe = new \Stripe\StripeClient("$stripe_secretID");

    
    if(!empty($_POST['stripeToken'] && $action == "donation")){
        // get token and user details
        $comment ="";
        $purchase_dedication ="";
        //print_r($_POST);
        $stripeToken  = $_POST['stripeToken'];
        $customerName = $_POST['custName']." ".$_POST['customerLastName'];
        $customerEmail = $_POST['custEmail'];

        $cardNumber = $_POST['cardNumber'];
        $cardCVC = $_POST['cardCVC'];
        $cardExpMonth = $_POST['cardExpMonth'];
        $cardExpYear = $_POST['cardExpYear']; 
        
        // item details for which payment made
        //$itemName = "Donation";
        $totalAmount = $_POST['final_amount'];
        $currency = $_POST['currency_code'];
        $select_payment_type = $_POST['select_payment_type'];
        
        $comment = $_POST['comment'];
        if($comment != ""){
        }else{
            $comment = "Donation";
        }
        $specialCustomerName = $_POST['specialCustomerName'];
        //add customer to stripe
        try {
            $customer = \Stripe\Customer::create(array(
                'name' => $customerName,
                'email' => $customerEmail,
                'source'  => $stripeToken
            ));  
        }catch (Exception $e) {
              $body = $e->getJsonBody();
              json_response($body);
              exit();
        } 
        
        if($select_payment_type == "monthly"){
            try { 
                // Create price with subscription info and interval 
                $price = \Stripe\Price::create([ 
                    'unit_amount' => $totalAmount, 
                    'currency' => 'USD', 
                    'recurring' => ['interval' => 'month'],
                    'product_data' => ['name' => 'Donation'], 
                ]); 
            } catch (Exception $e) {  
                $api_error = $e->getMessage(); 
                $body = $e->getJsonBody();
                $body = $e->getJsonBody();
                json_response($body);
                exit();
            } 
             
            // details for which monthly payment performed
            try { 
                $subscription = \Stripe\Subscription::create([ 
                    'customer' => $customer->id, 
                    'items' => [[ 
                        'price' => $price->id, 
                    ]], 
                    'description' => "$comment",
                    'metadata' => array("purchase_dedication" => $specialCustomerName),
                    'payment_settings' => ['save_default_payment_method' => 'on_subscription'], 
                ]); 
                $paymentResponse = $subscription->jsonSerialize();
                json_response($paymentResponse);
            }catch(Exception $e) { 
                  $api_error = $e->getMessage(); 
                  $body = $e->getJsonBody();
                  $body = $e->getJsonBody();
                  json_response($body);
                  exit();
            } 
            if(empty($api_error) && $subscription){ 
                $_POST['subscriptionId'] = $subscription->id;
                send_stripe_email($customerName, $customerEmail, $totalAmount);
                //echo json_encode($output); 
            }else{ 
                //echo json_encode(['error' => $api_error]); 
            } 
        }else{
            // details for which onetime payment performed
            try {
            $payDetails = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $totalAmount,
                'currency' => $currency,             
                'metadata' => array("purchase_dedication" => $specialCustomerName),                
                'description' => "$comment"
            ));   
            $_POST['chargeId'] = $payDetails->id;
            if($payDetails->id!=""){
                $paymentResponse = $payDetails->jsonSerialize();
                json_response($paymentResponse);
                send_stripe_email($customerName, $customerEmail, $totalAmount);
            }
            }catch (Exception $e) {
                  $body = $e->getJsonBody();
                  json_response($body);
                  exit();
            } 
            // get payment details
        }
        //wp_create_donation_user($_POST);
        $paymentMessage = "The payment was successful.";
    }else if(!empty($action) && $action == "payment_intent"){
            if($select_payment_type == "monthly"){
                try { 
                    // Create price with subscription info and interval 
                    $price = \Stripe\Price::create([ 
                        'unit_amount' => $totalAmount, 
                        'currency' => 'USD', 
                        'recurring' => ['interval' => 'month'],
                        'product_data' => ['name' => 'Donation'], 
                    ]); 
                } catch (Exception $e) {  
                    $api_error = $e->getMessage(); 
                    $body = $e->getJsonBody();
                    $body = $e->getJsonBody();
                    json_response($body);
                    exit();
                } 
                 
                // details for which monthly payment performed
                try { 
                    $subscription = \Stripe\Subscription::create([ 
                        'customer' => $customer->id, 
                        'items' => [[ 
                            'price' => $price->id, 
                        ]], 
                        'description' => "$comment",
                        'metadata' => array("purchase_dedication" => $specialCustomerName),
                        'payment_behavior' => 'default_incomplete',
                        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                        'expand' => ['latest_invoice.payment_intent'],
                    ]); 
                    echo $subscription->client_secret;   
                    exit();
                }catch(Exception $e) { 
                      $api_error = $e->getMessage(); 
                      $body = $e->getJsonBody();
                      $body = $e->getJsonBody();
                      json_response($body);
                      exit();
                } 
                if(empty($api_error) && $subscription){ 
                    $_POST['subscriptionId'] = $subscription->id;
                    //send_stripe_email($customerName, $customerEmail, $totalAmount);
                    //echo json_encode($output); 
                }else{ 
                    //echo json_encode(['error' => $api_error]); 
                } 
            }else{
                $comment = $_POST['comment'];
                if($comment != ""){
                }else{
                    $comment = "Donation";
                }
                $specialCustomerName = $_POST['specialCustomerName']; 
                
                $intent = $stripe->paymentIntents->create([
                  'amount' => $totalAmount,
                  'currency' => 'usd',
                  'description' => "$comment",
                   'metadata' => array("purchase_dedication" => $specialCustomerName),
                  'automatic_payment_methods' => ['enabled' => true],
                ]);
                echo $intent->client_secret;                
            }
            exit(); 
    }
    //echo $_SESSION["message"] = $paymentMessage;
    exit(); // this is required to return a proper result
   
function json_response($data=null, $httpStatus=200)
{
    header_remove();

    header("Content-Type: application/json");

    http_response_code($httpStatus);

    echo json_encode($data);

    exit();
} 