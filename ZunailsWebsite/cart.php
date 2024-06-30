<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$isCartEmpty = !isset($_SESSION['booking_id']) || !isset($_SESSION['booking_date']) || !isset($_SESSION['booking_time']) || !isset($_SESSION['selectedServices']) || empty($_SESSION['selectedServices']);

if (!$isCartEmpty) {
    $booking_id = $_SESSION['booking_id'];
    $booking_date = $_SESSION['booking_date'];
    $booking_time = $_SESSION['booking_time'];
    $selectedServices = $_SESSION['selectedServices'];

    $totalCost = 0;
    foreach ($selectedServices as $service) {
        $totalCost += $service['price'];
    }

    $groupedServices = [];
    foreach ($selectedServices as $service) {
        $groupedServices[$service['category']][] = $service;
    }
}

function canCancelBooking($booking_date, $booking_time) {
    $bookingDateTime = new DateTime($booking_date . ' ' . $booking_time);
    $currentDateTime = new DateTime();
    $interval = $currentDateTime->diff($bookingDateTime);
    return ($interval->days * 24 + $interval->h) >= 48;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700&display=swap" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="fullscreen-wrapper">
        <div class="cart-container">
            <?php if (!$isCartEmpty): ?>
                <h2>Your Booking</h2>
            <?php endif; ?>

            <?php if ($isCartEmpty): ?>
                <div class="center-text">
                    <p class="cart-empty-message">Browse our services and make a booking to get started.</p>
                    <div class="button-container">
                        <a href="services.php" class="cart-book-now-button">Book Now</a>
                    </div>
                </div>
            <?php else: ?>
                <h3 class="booking-info">Booking Information</h3>
                <div class="booking-summary">
                    <p class="service-details"><span class="label">Date:</span> <?php echo htmlspecialchars($booking_date); ?></p>
                    <p class="service-details"><span class="label">Time:</span> <?php echo htmlspecialchars($booking_time); ?></p>
                </div>

                <?php foreach ($groupedServices as $category => $services): ?>
                    <div class="category-section">
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                        <ul class="service-list">
                            <?php foreach ($services as $service): ?>
                                <li class="service-details">
                                    <span><?php echo htmlspecialchars($service['description']); ?></span>
                                    <span>R<?php echo floor($service['price']); ?></span>
                                    <a href="cart.php?delete_service_id=<?php echo $service['id']; ?>" class="delete-btn">Delete</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                    </div>
                <?php endforeach; ?>

                <div class="bottom-container">
                    <a href="#" id="editBtn" class="edit-link">Edit</a>
                    <div class="total-cost">
                        <h3>Total: R<?php echo floor($totalCost); ?></h3>
                    </div>
                </div>

                <div class="button-container">
                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
                        <input type="hidden" name="total_cost" value="<?php echo htmlspecialchars($totalCost); ?>">
                        <button type="submit" class="pay-now-button">Pay Now</button>
                    </form>

                    <?php if (canCancelBooking($booking_date, $booking_time)): ?>
                        <form action="cart.php" method="POST" class="cancel-form">
                            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">
                            <button type="submit" name="cancel_booking" class="edit-link">Cancel Booking</button>
                        </form>
                    <?php else: ?>
                        <p>You cannot cancel the booking within 48 hours of the scheduled time. Please contact Zunails if you need to cancel an appointment at the last minute. Phone: 071 786 7015</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('editBtn').addEventListener('click', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                if (button.style.display === 'none') {
                    button.style.display = 'inline';
                } else {
                    button.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
