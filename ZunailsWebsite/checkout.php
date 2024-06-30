<?php
session_start();
require 'db_connection.php'; 

$payment_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['card_holder_name'], $_POST['card_number'], $_POST['expiry_date'], $_POST['cvv'], $_POST['total_price'], $_POST['booking_id'])) {
      
        $card_holder_name = $_POST['card_holder_name'];
        $card_number = $_POST['card_number'];
        $expiry_date = $_POST['expiry_date'];
        $cvv = $_POST['cvv'];
        $total_price = $_POST['total_price'];
        $booking_id = $_POST['booking_id'];

        $payment_successful = true;

        if ($payment_successful) {
           
            $query = "UPDATE bookings SET status='paid' WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $booking_id);

            if ($stmt->execute()) {
                $payment_message = "Payment successful! Booking status updated to 'paid'.";

                unset($_SESSION['booking_id']);
                unset($_SESSION['booking_date']);
                unset($_SESSION['booking_time']);
                unset($_SESSION['selectedServices']);

                header('Location: user.php');
                exit();
            } else {
                $payment_message = "Error updating booking status: " . $conn->error;
            }

            $stmt->close();
        } else {
            $payment_message = "Payment failed. Please try again.";
        }
    }
} else {

    if (isset($_POST['total_price'])) {
        $total_price = $_POST['total_price'];
    } else {
        $total_price = isset($_SESSION['total_price']) ? $_SESSION['total_price'] : 0.00; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700&display=swap" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <script>
        function showPaymentMethod(method) {
            var cardForm = document.getElementById('card-form');
            var eftDetails = document.getElementById('eft-details');

            if (method === 'card') {
                cardForm.style.display = 'block';
                eftDetails.style.display = 'none';
            } else {
                cardForm.style.display = 'none';
                eftDetails.style.display = 'block';
            }
        }
    </script>
</head>
<body class="checkout-page">
    <?php include 'nav.php'; ?>
    <div class="checkout-container">
        <h2>Checkout</h2>
        <div class="payment-methods">
            <button type="button" onclick="showPaymentMethod('card')">Pay with Card</button>
            <button type="button" onclick="showPaymentMethod('eft')">Pay with EFT</button>
        </div>
        <?php if ($payment_message): ?>
            <p><?php echo $payment_message; ?></p>
        <?php endif; ?>
        <div class="checkout-items">
            <form id="card-form" method="post" action="checkout.php" style="display:none;">
                <div class="checkout-form-group">
                    <label for="card-holder-name">Name on Card</label>
                    <input type="text" id="card-holder-name" name="card_holder_name" placeholder="e.g Ms J Doe" required>
                </div>
                <div class="checkout-form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" name="card_number" placeholder="0000 0000 0000 0000" required>
                </div>
                <div class="checkout-form-group">
                    <label for="expiry-date">Expiry Date</label>
                    <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" required>
                </div>
                <div class="checkout-form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="CVV" required>
                </div>
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <input type="hidden" name="booking_id" value="<?php echo $_SESSION['booking_id']; ?>">
                <div class="checkout-button-container">
                    <button type="submit" class="checkout-pay-now-btn" id="payNowBtn">Submit Payment</button>
                    <button type="button" class="checkout-cancel-payment-btn" onclick="window.location.href='cart.php'">Cancel Payment</button>
                </div>
            </form>
            <div id="eft-details" class="checkout-eft-details">
                <p><strong>Bank Name:</strong> Capitec</p>
                <p><strong>Account Number:</strong> 1780049771</p>
                <p><strong>Reference:</strong> Your Username</p>
                <p> After completing an EFT payment, kindly send your proof of payment to 071-786-7015. Please allow up to 24 hours for the payment to be processed. You will see the payment reflected in your profile once it has been updated.</p>
                <div class="checkout-button-container">
                    <button type="button" class="checkout-done-btn" onclick="window.location.href='index.php'">Done</button>
                    <button type="button" class="checkout-cancel-payment-btn" onclick="window.location.href='cart.php'">Cancel Payment</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        showPaymentMethod('card');
    </script>
</body>
</html>
