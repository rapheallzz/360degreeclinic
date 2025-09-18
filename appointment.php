<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Retrieve and sanitize form inputs
$surname = htmlspecialchars($_POST['surname']);
$firstname = htmlspecialchars($_POST['firstname']);
$dob = htmlspecialchars($_POST['dob']);
$address = htmlspecialchars($_POST['address']);
$phone = htmlspecialchars($_POST['phone']);
$email = htmlspecialchars($_POST['email']);
$bloodgroup = htmlspecialchars($_POST['bloodgroup']);
$genotype = htmlspecialchars($_POST['genotype']);
$maritalstatus = htmlspecialchars($_POST['maritalstatus']);
$nationality = htmlspecialchars($_POST['nationality']);
$stateorigin = htmlspecialchars($_POST['stateorigin']);
$lga = htmlspecialchars($_POST['lga']);
$nextofkin = htmlspecialchars($_POST['nextofkin']);
$service = htmlspecialchars($_POST['service']);

// Validation for required fields
if (empty($surname) || empty($firstname) || empty($dob) || empty($address) || empty($phone) || empty($email) || empty($service)) {
    echo "All fields are required!";
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Enter a valid email address!";
    exit;
}

// Email recipient and message details
$receiver = "appointment@360degreeclinic.com.ng"; 
$subject = "Appointment Request for Service: $service";
$body = 
    "Surname: $surname\n" .
    "First Name: $firstname\n" .
    "Date of Birth: $dob\n" .
    "Address: $address\n" .
    "Phone: $phone\n" .
    "Email: $email\n" .
    "Blood Group: $bloodgroup\n" .
    "Genotype: $genotype\n" .
    "Marital Status: $maritalstatus\n" .
    "Nationality: $nationality\n" .
    "State of Origin: $stateorigin\n" .
    "LGA: $lga\n" .
    "Next of Kin: $nextofkin\n" .
    "Selected Service: $service\n\n" .
    "Regards,\n$surname $firstname";

// Email headers
$headers = "From: noreply@360degreeclinic.com.ng\r\n";
$headers .= "Reply-To: $email\r\n";

// Send email and provide feedback
if (mail($receiver, $subject, $body, $headers)) {
    echo "
    <h2><center>Your appointment request has been sent successfully!</center></h2>
    <center><a href='index.html' style='text-decoration: none; color: #007BFF; font-size: 18px;'>Return to Home Page</a></center>
    ";
} else {
    echo "Sorry, failed to send your appointment request!";
}
?>
