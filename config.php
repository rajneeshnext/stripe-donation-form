<?php

$config = array();
// Form 1
$slug = "HelpPetsNow";
$config[$slug]['main_url'] = 'https://werescued.org/help/remote_form.php';
$config[$slug]['STRIPE_CLIENT_ID'] = 'xxxx';
$config[$slug]['STRIPE_SECRET_KEY'] = 'xxxx';

$config[$slug]['main_title'] = 'Help Where It\'s Needed Most.';
$config[$slug]['logo_url'] = 'https://www.werescued.org/help/images/download-img.png';
$config[$slug]['main_banner'] = 'https://werescued.org/help/images/download.png';
$config[$slug]['main_description'] = '<p class="text-line-clamp-6">Help us Amplify the Good! Your purchase today will help people, pets and the planet through Greater Good Charities\' lifesaving programs. We are deeply grateful for your kindness...</p>';
$config[$slug]['price']['options'] = "Feeds 1 Dog, Feeds 2 Dogs, Feeds 3 Dogs, Feeds 10 Dogs";
$config[$slug]['price']['value'] = "10, 20, 30, 40";
$config[$slug]['thank_you_url'] = 'https://werescued.org/help/thank-you.php';
$config[$slug]['terms_policy_url'] = 'https://werescued.org/help/terms_policy.php';


// Form 2
$slug = "HelpCatsNow";
$config[$slug]['main_url'] = 'https://werescued.org/help/remote_form.php';
$config[$slug]['STRIPE_CLIENT_ID'] = 'xxx';
$config[$slug]['STRIPE_SECRET_KEY'] = 'xxx';

$config[$slug]['main_title'] = 'Help Cats Where It\'s Needed Most.';
$config[$slug]['logo_url'] = 'https://www.werescued.org/help/images/download-img.png';
$config[$slug]['main_banner'] = 'https://werescued.org/help/images/download.png';
$config[$slug]['main_description'] = '<p class="text-line-clamp-6">Help Cat Amplify the Good! Your purchase today will help people, pets and the planet through Greater Good Charities\' lifesaving programs. We are deeply grateful for your kindness</p>';
$config[$slug]['price']['options'] = "Feeds 1 Cat, Feeds 2 Cat, Feeds 3 Cat, Feeds 10 Cat";
$config[$slug]['price']['value'] = "10, 20, 30, 40";
$config[$slug]['thank_you_url'] = 'https://werescued.org/help/thank-you.php?v=0';
$config[$slug]['terms_policy_url'] = 'https://werescued.org/help/terms_policy.php';


function send_stripe_email($customer_name=""){
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
echo $main_url = $config[$slug]['main_url']."?v=0.01&form=".$slug;
$logo_url = $config[$slug]['logo_url'];

echo $customer_name;

echo $html = '<!DOCTYPE html>
                            <html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                <title>Patron</title>
                                
                                <meta name="viewport" content="width=device-width, initial-scale=1">
                                </head>
                                <body style="padding: 0; margin: 0; background: transparent; font-family: Arial">
                                    <center>
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="border-radius: 15px;border:12px solid darkgrey; padding:20px;text-align: center;">
                                        <tbody>
                                            <tr>
                                            <td align="center" valign="top">
                                                <table width="650px" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="padding:5px 25px;text-align:center;box-shadow: 0px 4px 13px rgba(0, 0, 0, 0.37);border: border-radius: 15px;">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="12">
                                                                <p style="text-align:center;margin-bottom: 0px;"><a href="'.$main_url.'" style="font-style: italic;color: #747474;text-decoration: none;font-size: 16px;"><img src="'.$logo_url.'" alt="GRPVYN" style="width:75px"></a></p>
                                                                <p style="font-family: Source Sans Pro,sans-serif;
                                                                                        font-style: italic;
                                                                                        font-weight: normal;
                                                                                        font-size: 11.5px;
                                                                                        line-height: 14px;
                                                                                        text-align: center;
                                                                                        letter-spacing: 0.05em;
                                                                                    color: #7F7F7F;margin-top: 0px;"></p>
                                                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding-top:34px;text-align: center;">
                                                                       <tbody>
                                                                           <tr>
                                                                              <td>
                                                                                    <h3 style="font-family: Source Sans Pro,sans-serif;font-style: normal;font-weight: normal;font-size: 40px;line-height: 50px;text-align: center;letter-spacing: 0.05em;margin-top: 0px;margin-bottom: 10px;color: #EE943A;">Thanks for donation</h3>
                                                                                     <h6 style="font-weight: normal;font-size: 15px;line-height: 15px;letter-spacing: 0.05em;margin: 2px 0 20px 0;padding: 8px;font-family: Source Sans Pro,sans-serif;text-align: center;min-height: 80px;color: #000;font-style: italic;border: 2px solid #4B80C9;box-sizing: border-box;box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);border-radius: 6px;">
                                                                                        Hello '.$customer_name.',
                                                                                        <br/><br/>Your donation means a lot.
                                                                                     </h6>
                                                                                     <p>
                                                                                        &copy; 2024 <a href="'.$main_url.'">werescued.org</a></p>
                                                                                     </p> 
                                                                                </td>
                                                                            </tr>
                                                                    </tbody>
                                                                </table>  
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </center>
                                </body>
                            </html>';

    $to = 'boldertechno@gmail.com';
    $subject = 'Donations Received!';

    $headers  = "From: info@werescued.org\r\n";
    //$headers .= "Reply-To: " . strip_tags($_POST['req-email']) . "\r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    mail($to, $subject, $html, $headers);                        
}
?>
