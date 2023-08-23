<?php
require_once('recaptchalib.php');

$env = parse_ini_file('.env');
$recaptchaCode = $_POST['g-recaptcha-response'];

//echo "<p>ENV_TEST: ".$env["ENV_TEST"]."</p><br/>";
//echo "<p>recaptcha: ".$recaptchaCode."</p><br/>";

$recaptcha = new ReCaptcha($env["RECAPTCHA_SECRET_KEY"]); 
$recaptcha = $recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $recaptchaCode); 

//print_r($recaptcha);

if(!$recaptcha->success){ 
    // Failed
    echo "recaptcha failed!";
    return;
}

$errorMSG = "";

function clean_string($string) {
    $bad = array("content-type", "bcc:", "to:", "cc:", "href");
    return str_replace($bad, "", $string);
}

if (empty($_POST["name"])) {
    $errorMSG = "Name is required ";
} else {
    $name = clean_string($_POST["name"]);
}

if (empty($_POST["email"])) {
    $errorMSG = "Email is required ";
} else {
    $email = clean_string($_POST["email"]);
}

if (empty($_POST["message"])) {
    $errorMSG = "Message is required ";
} else {
    $message = clean_string($_POST["message"]);
}

if (empty($_POST["terms"])) {
    $errorMSG = "Terms is required ";
} else {
    $terms = clean_string($_POST["terms"]);
}

$EmailTo = "hello@eventgum.com";
$Subject = "New message from EventGum landing page";

// prepare email body text
$Body = "";
$Body .= "Name: ";
$Body .= $name;
$Body .= "\n";
$Body .= "Email: ";
$Body .= $email;
$Body .= "\n";
$Body .= "Message: ";
$Body .= $message;
$Body .= "\n";
$Body .= "Terms: ";
$Body .= $terms;
$Body .= "\n";

// send email
$success = mail($EmailTo, $Subject, $Body, "From:".$email);

// redirect to success page
if ($success && $errorMSG == ""){
   echo "success";
}else{
    if($errorMSG == ""){
        echo "Something went wrong :(";
    } else {
        echo $errorMSG;
    }
}
?>