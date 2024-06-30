<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require 'db_connection.php';

function fetchAvailableTimes($conn, $date) {
    $stmt = $conn->prepare("SELECT available_time FROM available_times WHERE available_date = ?");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $availableTimes = [];

    while ($row = $result->fetch_assoc()) {
        $availableTimes[] = $row['available_time'];
    }

    $stmt->close();
    return $availableTimes;
}

if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
    $availableTimes = fetchAvailableTimes($conn, $selectedDate);
    header('Content-Type: application/json');
    echo json_encode(['availableTimes' => $availableTimes]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now'])) {
    $username = $_SESSION['username'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $selectedServices = json_decode($_POST['selected_services'], true);

    if (empty($booking_date)) {
        $error = "Booking date is required.";
    } else {
        $booking_date = date('Y-m-d', strtotime($booking_date));
    }

    $stmt_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_user->bind_param('s', $username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user_row = $result_user->fetch_assoc();
        $user_id = $user_row['id'];

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, status) VALUES (?, ?, ?, 'booked')");
        $stmt->bind_param('iss', $user_id, $booking_date, $booking_time);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id;

            foreach ($selectedServices as $service) {
                $service_id = $service['id'];
                $stmt_service = $conn->prepare("INSERT INTO booking_services (booking_id, service_id) VALUES (?, ?)");
                $stmt_service->bind_param('ii', $booking_id, $service_id);
                $stmt_service->execute();
                $stmt_service->close();
            }

            $stmt_delete = $conn->prepare("DELETE FROM available_times WHERE available_date = ? AND available_time = ?");
            $stmt_delete->bind_param('ss', $booking_date, $booking_time);
            $stmt_delete->execute();
            $stmt_delete->close();

            $_SESSION['booking_id'] = $booking_id;
            $_SESSION['booking_date'] = $booking_date;
            $_SESSION['booking_time'] = $booking_time;
            $_SESSION['selectedServices'] = $selectedServices;
            header("Location: cart.php");
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error: User not found.";
    }
    $stmt_user->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="book-page-container">
        <div class="centered-container">
            <div class="booking-box">
                <h2>Select a Date and Time</h2>
                <form id="bookingForm" method="POST">
                    <div class="content-columns">
                        <div class="column">
                            <div class="calendar-container">
                                <div class="calendar">
                                    <div class="calendar-header">
                                        <button type="button" id="prevMonth">&lt;</button>
                                        <span id="currentMonth"></span>
                                        <button type="button" id="nextMonth">&gt;</button>
                                    </div>
                                    <div class="calendar-days-header">
                                        <div>Sun</div>
                                        <div>Mon</div>
                                        <div>Tue</div>
                                        <div>Wed</div>
                                        <div>Thu</div>
                                        <div>Fri</div>
                                        <div>Sat</div>
                                    </div>
                                    <div class="calendar-days" id="calendarDays"></div>
                                </div>
                            </div>
                        </div>
                        <div class="column middle">
                            <h3>Available Times</h3>
                            <div class="time-slots" id="timeSlots"></div>
                        </div>
                        <div class="column wide">
                            <div class="booking-information">
                                <h3>Booking Summary</h3>
                                <p>Date: <span id="bookingSummaryDate"></span></p>
                                <p>Time: <span id="bookingSummaryTime"></span></p>
                                <p>Services: <span id="serviceSummary"></span></p>
                                <p>Total Cost: R<span id="totalCostSummary"></span></p>
                                <?php if (isset($error)): ?>
                                    <p class="error"><?php echo $error; ?></p>
                                <?php endif; ?>
                                <button type="button" id="confirmBookingBtn" class="time-slot" disabled>Confirm Booking Details</button>
                            </div>
                        </div>
                    </div>
                    <div class="booknowbutton">
                        <button type="submit" name="book_now" id="bookNowBtn" disabled>Book Now</button>
                    </div>
                    <input type="hidden" name="selected_services" id="selectedServicesInput">
                    <input type="hidden" name="booking_date" id="bookingDateInput">
                    <input type="hidden" name="booking_time" id="selectedTimeInput">
                </form>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeSlotsDiv = document.getElementById('timeSlots');
        const bookNowBtn = document.getElementById('bookNowBtn');
        const confirmBookingBtn = document.getElementById('confirmBookingBtn');
        const selectedServicesInput = document.getElementById('selectedServicesInput');
        const selectedTimeInput = document.getElementById('selectedTimeInput');
        const bookingDateInput = document.getElementById('bookingDateInput');
        const bookingSummaryDateSpan = document.getElementById('bookingSummaryDate');
        const bookingSummaryTimeSpan = document.getElementById('bookingSummaryTime');
        const serviceSummarySpan = document.getElementById('serviceSummary');
        const totalCostSummarySpan = document.getElementById('totalCostSummary');
        const currentMonthSpan = document.getElementById('currentMonth');
        const calendarDaysDiv = document.getElementById('calendarDays');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');

        const selectedServices = JSON.parse(localStorage.getItem('selectedServices') || '[]');
        const totalCost = parseFloat(localStorage.getItem('totalCost') || '0');

        const serviceDescriptions = selectedServices.map(service => service.description);
        serviceSummarySpan.textContent = serviceDescriptions.join(', ');
        selectedServicesInput.value = JSON.stringify(selectedServices);
        totalCostSummarySpan.textContent = totalCost.toFixed(2).replace(/\.00$/, '');

        let currentMonth = new Date();
        const today = new Date();

        function renderCalendar(month) {
            const year = month.getFullYear();
            const monthIndex = month.getMonth();
            const firstDay = new Date(year, monthIndex, 1).getDay();
            const lastDate = new Date(year, monthIndex + 1, 0).getDate();

            currentMonthSpan.textContent = month.toLocaleDateString('en-us', { month: 'long', year: 'numeric' });

            calendarDaysDiv.innerHTML = '';

            const adjustedFirstDay = firstDay;

            for (let i = 0; i < adjustedFirstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('calendar-day');
                calendarDaysDiv.appendChild(emptyDay);
            }

            for (let i = 1; i <= lastDate; i++) {
                const day = document.createElement('div');
                day.classList.add('calendar-day');
                day.textContent = i;

                const dayDate = new Date(year, monthIndex, i);
                if (dayDate <= today) {
                    day.classList.add('past-date');
                    day.style.pointerEvents = 'none';
                } else {
                    day.addEventListener('click', function() {
                        const selectedDate = new Date(year, monthIndex, i);
                        const formattedDisplayDate = formatDisplayDate(selectedDate);
                        const formattedBackendDate = formatBackendDate(selectedDate);
                        bookingDateInput.value = formattedBackendDate;
                        bookingSummaryDateSpan.textContent = formattedDisplayDate;
                        document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                        day.classList.add('selected');
                        clearSelectedTime();
                        fetchAvailableTimes(formattedBackendDate);
                        updateBookingSummary();
                    });
                }

                calendarDaysDiv.appendChild(day);
            }
        }

        function formatDisplayDate(date) {
            const options = { day: '2-digit', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
        }

        function formatBackendDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatTime(time) {
            return time.substring(0, 5);
        }

        function fetchAvailableTimes(date) {
            fetch(`book.php?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    timeSlotsDiv.innerHTML = '';
                    if (data.availableTimes.length > 0) {
                        data.availableTimes.forEach(time => {
                            const formattedTime = formatTime(time);
                            const timeButton = document.createElement('button');
                            timeButton.textContent = formattedTime;
                            timeButton.type = 'button';
                            timeButton.classList.add('time-slot');
                            timeButton.addEventListener('click', function() {
                                selectedTimeInput.value = formattedTime;
                                bookingSummaryTimeSpan.textContent = formattedTime;
                                document.querySelectorAll('.time-slot').forEach(btn => btn.classList.remove('selected'));
                                this.classList.add('selected');
                                updateBookingSummary();
                            });
                            timeSlotsDiv.appendChild(timeButton);
                        });
                    } else {
                        const noTimesMessage = document.createElement('p');
                        noTimesMessage.textContent = 'No available times for the selected date.';
                        noTimesMessage.classList.add('no-available-times'); 
                        timeSlotsDiv.appendChild(noTimesMessage);
                    }
                });
        }

        function updateBookingSummary() {
            const selectedDate = bookingDateInput.value;
            const selectedTime = selectedTimeInput.value;
            if (selectedDate && selectedTime) {
                bookingSummaryDateSpan.textContent = formatDisplayDate(new Date(selectedDate));
                bookingSummaryTimeSpan.textContent = selectedTime;
                confirmBookingBtn.disabled = false;
            } else {
                confirmBookingBtn.disabled = true; 
            }
        }

        function clearSelectedTime() {
            selectedTimeInput.value = '';
            bookingSummaryTimeSpan.textContent = '';
            document.querySelectorAll('.time-slot').forEach(btn => btn.classList.remove('selected'));
            confirmBookingBtn.disabled = true; 
            bookNowBtn.disabled = true; 
        }

        prevMonthBtn.addEventListener('click', function() {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            renderCalendar(currentMonth);
        });

        nextMonthBtn.addEventListener('click', function() {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            renderCalendar(currentMonth);
        });

        confirmBookingBtn.addEventListener('click', function() {
            bookNowBtn.disabled = false; 
        });

        renderCalendar(currentMonth);
    });
    </script>
</body>
</html>
