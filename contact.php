<?php
/**
 * Arlo Estimating — Contact Form Handler
 * ----------------------------------------
 * Upload this file to the root of your Hostinger website (same folder as index.html).
 *
 * CONFIGURATION:
 *   1. Set $to_email to the address where you want to receive enquiries.
 *   2. Set $from_email to a matching address on your Hostinger domain
 *      (e.g. noreply@arloestimating.com.au).  It must exist in your
 *      Hostinger Email panel or mail() may be rejected.
 */

header('Content-Type: application/json');

// ——— Only accept POST ———
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ——— Configuration — edit these two lines ———
$to_email   = 'info@arloestimating.com.au';    // where enquiries are sent TO
$from_email = 'noreply@arloestimating.com.au'; // the sending address (must exist on your domain)

// ——— Collect & sanitise inputs ———
$name    = htmlspecialchars(strip_tags(trim($_POST['name']    ?? '')));
$email   = trim($_POST['email']   ?? '');
$phone   = htmlspecialchars(strip_tags(trim($_POST['phone']   ?? '')));
$enquiry = htmlspecialchars(strip_tags(trim($_POST['enquiry'] ?? '')));

// ——— Validate ———
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (empty($enquiry)) {
    $errors[] = 'Please include your enquiry.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ——— Build plain-text email ———
$subject  = 'New Enquiry — ' . $name . ' via Arlo Estimating';

$body  = "You have received a new enquiry via arloestimating.com.au\n";
$body .= str_repeat('-', 48) . "\n\n";
$body .= "Name:    " . $name  . "\n";
$body .= "Email:   " . $email . "\n";
$body .= "Phone:   " . ($phone ?: 'Not provided') . "\n\n";
$body .= "Enquiry:\n" . $enquiry . "\n";

$headers  = "From: Arlo Estimating <" . $from_email . ">\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ——— Send ———
if (mail($to_email, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Sent.']);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, we could not send your message right now. Please email us directly at info@arloestimating.com.au.'
    ]);
}
