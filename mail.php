<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$phone = htmlspecialchars($_POST['phone']);
$subject = htmlspecialchars($_POST['subject']);
$message = htmlspecialchars($_POST['message']);

if(empty($name) || empty($email) || empty($message)) {
    echo "Name, email, and message fields are required!";
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Enter a valid email address!";
    exit;
}

$receiver = "info@360degreeclinic.com.ng"; 
$mailSubject = "Contact Form Submission: $subject";
$body = "Name: $name\nEmail: $email\nPhone: $phone\nSubject: $subject\nMessage:\n$message\n\nRegards,\n$name";
$sender = "From: noreply@360degreeclinic.com.ng";
$replyTo = "Reply-To: $email";

if(mail($receiver, $mailSubject, $body, "$sender\r\n$replyTo")) {
    echo "
    <h2><center>Your message has been sent successfully!</center></h2>
    <center><a href='index.html' style='text-decoration: none; color: #007BFF; font-size: 18px;'>Return to Home Page</a></center>
    ";
} else {
    echo "Sorry, failed to send your message!";
}
?>
