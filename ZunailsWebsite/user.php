<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$sql_bookings = "SELECT b.*, GROUP_CONCAT(s.description SEPARATOR ', ') AS services
                 FROM bookings b
                 JOIN booking_services bs ON b.id = bs.booking_id
                 JOIN services s ON bs.service_id = s.id
                 WHERE b.user_id = ?
                 GROUP BY b.id
                 ORDER BY b.booking_date, b.booking_time";
$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param('i', $user['id']);
$stmt_bookings->execute();
$bookings = $stmt_bookings->get_result();
$stmt_bookings->close();

$upcoming_bookings = [];
$past_bookings = [];
$currentDate = new DateTime();

while ($booking = $bookings->fetch_assoc()) {
    $bookingDate = new DateTime($booking['booking_date']);
    if ($bookingDate >= $currentDate) {
        $upcoming_bookings[] = $booking;
    } else {
        $past_bookings[] = $booking;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900&display=swap" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <script>
        function showBookings(type) {
            document.getElementById('upcoming-bookings').style.display = type === 'upcoming' ? 'block' : 'none';
            document.getElementById('past-bookings').style.display = type === 'past' ? 'block' : 'none';
        }
    </script>
</head>
<body class="profile-body">

    <?php include 'nav.php'; ?>

    <div class="profile-container">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?></h2>
        <div class="user-info">
            <p class="email">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p class="phone">Phone Number: <?php echo htmlspecialchars($user['phone_number']); ?></p>
        </div>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-button">Logout</button>
        </form>

        <div class="booking-buttons">
            <button type="button" onclick="showBookings('upcoming')">Upcoming Bookings</button>
            <button type="button" onclick="showBookings('past')">Past Bookings</button>
        </div>

        <div id="upcoming-bookings" class="booking-details">
            <h3>Upcoming Bookings</h3>
            <?php if (!empty($upcoming_bookings)) {
                foreach ($upcoming_bookings as $booking) {
                    echo "<div class='booking-card'>";
                    echo "<p><strong>Booking Date:</strong> " . htmlspecialchars($booking['booking_date']) . "</p>";
                    echo "<p><strong>Booking Time:</strong> " . htmlspecialchars($booking['booking_time']) . "</p>";
                    echo "<p><strong>Services:</strong> " . htmlspecialchars($booking['services']) . "</p>";
                    echo "<p><strong>Payment Status:</strong> " . ($booking['status'] === 'paid' ? 'Paid' : 'Unpaid') . "</p>";
                    echo "<hr>";
                    echo "</div>";
                }
            } else {
                echo "<p>No upcoming bookings found.</p>";
            }
            ?>
        </div>

        <div id="past-bookings" class="booking-details">
            <h3>Past Bookings</h3>
            <?php if (!empty($past_bookings)) {
                foreach ($past_bookings as $booking) {
                    echo "<div class='booking-card'>";
                    echo "<p><strong>Booking Date:</strong> " . htmlspecialchars($booking['booking_date']) . "</p>";
                    echo "<p><strong>Booking Time:</strong> " . htmlspecialchars($booking['booking_time']) . "</p>";
                    echo "<p><strong>Services:</strong> " . htmlspecialchars($booking['services']) . "</p>";
                    echo "<p><strong>Payment Status:</strong> " . ($booking['status'] === 'paid' ? 'Paid' : 'Unpaid') . "</p>";
                    echo "<hr>";
                    echo "</div>";
                }
            } else {
                echo "<p>No past bookings found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
