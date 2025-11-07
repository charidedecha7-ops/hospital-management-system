<?php
session_start();
include("../connection.php"); // your database connection

// Chapa Secret Key
$chapa_secret = 'CHASECK_TEST-sajQyHSrAnlzkubdiYFathXsVDojpLiH';

// Get logged in user
if(!isset($_SESSION['user']) || $_SESSION['usertype'] != 'p'){
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION['user'];
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];
$useremail = $userfetch["pemail"];

// Get schedule id
if(!isset($_GET['id'])){
    die("No schedule selected!");
}
$schedule_id = $_GET['id'];

// Get schedule details
$schedule_row = $database->query("SELECT * FROM schedule INNER JOIN doctor ON schedule.docid=doctor.docid WHERE schedule.scheduleid='$schedule_id'")->fetch_assoc();
$title = $schedule_row['title'];
$amount = 300; // Set appointment fee in ETB

// Generate unique transaction reference
$tx_ref = 'TX-'.uniqid();
$callback_url = "http://localhost/edoc-doctor-appointment-system-main_1/patient/payment_success.php?schedule_id=$schedule_id";

// Prepare data for Chapa
$data = [
    'amount' => $amount,
    'currency' => 'ETB',
    'email' => $useremail,
    'first_name' => $username,
    'last_name' => '',
    'tx_ref' => $tx_ref,
    'callback_url' => $callback_url
];

// Initialize payment with Chapa
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.chapa.co/v1/transaction/initialize");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $chapa_secret"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if($responseData['status'] == 'success'){
    // Redirect user to Chapa checkout page
    header("Location: " . $responseData['data']['checkout_url']);
    exit();
}else{
    echo "Payment initialization failed!";
    print_r($responseData);
}
?>
