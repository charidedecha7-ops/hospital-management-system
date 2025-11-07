<?php
session_start();
include("../connection.php");

// Chapa Secret Key
$chapa_secret = 'CHASECK_TEST-sajQyHSrAnlzkubdiYFathXsVDojpLiH';

if(!isset($_GET['schedule_id']) || !isset($_GET['tx_ref'])){
    die("Invalid payment response");
}

$schedule_id = $_GET['schedule_id'];
$tx_ref = $_GET['tx_ref'];

// Verify payment with Chapa
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.chapa.co/v1/transaction/verify/$tx_ref");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $chapa_secret"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if($responseData['status'] == 'success' && $responseData['data']['status'] == 'success'){
    // Payment successful
    $useremail = $_SESSION['user'];
    $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];

    // Insert booking record into database with status 'Paid'
    $stmt = $database->prepare("INSERT INTO bookings (scheduleid, pid, payment_status) VALUES (?, ?, ?)");
    $status = "Paid";
    $stmt->bind_param("iis", $schedule_id, $userid, $status);
    $stmt->execute();
    $stmt->close();

    // Redirect to booking page
    header("Location: booking.php?id=$schedule_id&paid=1");
    exit();
}else{
    echo "Payment verification failed!";
    print_r($responseData);
}
?>
