<?php
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// ⚙️ Your API credentials
$keyId = "rzp_test_abc123";
$keySecret = "your_secret_key";

$api = new Api($keyId, $keySecret);

// Get payment ID from frontend
$paymentId = $_POST['payment_id'] ?? '';

if ($paymentId) {
    try {
        // Fetch payment details from Razorpay
        $payment = $api->payment->fetch($paymentId);

        // Optional: verify amount / status
        if ($payment['status'] == 'captured') {
            echo "✅ Payment Successful! ID: " . $paymentId;
            // TODO: update order status in DB here
        } else {
            echo "⚠️ Payment not captured. Status: " . $payment['status'];
        }

    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
    }
} else {
    echo "No payment ID received.";
}
?>
