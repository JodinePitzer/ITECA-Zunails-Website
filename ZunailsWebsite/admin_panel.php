<?php
session_start();


$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_paid']) || isset($_POST['cancel_booking'])) {
        $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
        if ($booking_id !== false) {
            $status = isset($_POST['set_paid']) ? 'paid' : 'cancelled';
            $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $booking_id);
            if ($stmt->execute()) {
                $success = "Booking status updated successfully.";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Invalid booking ID.";
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        if ($user_id !== false) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            if ($stmt->execute()) {
                $success = "User deleted successfully.";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Invalid user ID.";
        }
    }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $image_name = $_POST['image_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
   
    if(isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
            
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    
        if(in_array($file_ext, $allowed_extensions)) {
            
            $new_file_name = uniqid('', true) . '.' . $file_ext;
    
            $upload_directory = 'uploads/';
    
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0755, true);
            }

            if(move_uploaded_file($file_tmp, $upload_directory . $new_file_name)) {
                
                $sql = "INSERT INTO images (image_name, description, price, image_path) VALUES (?, ?, ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param('ssds', $image_name, $description, $price, $new_file_name);
                    if ($stmt->execute()) {
                        $success = "Image uploaded successfully.";
                    } else {
                        $error = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Error: " . $conn->error;
                }
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type. Allowed types: jpg, jpeg, png, gif";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}
 
 if (isset($_POST['set_available_time'])) {
    $available_date = filter_input(INPUT_POST, 'available_date', FILTER_SANITIZE_STRING);
    $available_times = isset($_POST['available_times']) ? $_POST['available_times'] : [];

    if ($available_date && !empty($available_times)) {
        $stmt = $conn->prepare("INSERT INTO available_times (available_date, available_time) VALUES (?, ?)");
        
        $all_success = true;
        foreach ($available_times as $available_time) {
            $stmt->bind_param('ss', $available_date, $available_time);
            if (!$stmt->execute()) {
                $all_success = false;
                $error = "Error setting available time: " . $stmt->error;
                break;
            }
        }
        
        if ($all_success) {
            $success = "Available times set successfully.";
        }
        
        $stmt->close();
    } else {
        $error = "Invalid input data for setting available time.";
    }
}

if (isset($_POST['fetch_available_times'])) {
    $selected_date = filter_input(INPUT_POST, 'available_date', FILTER_SANITIZE_STRING);
    
    if ($selected_date) {
        $stmt = $conn->prepare("SELECT available_time FROM available_times WHERE available_date = ?");
        $stmt->bind_param('s', $selected_date);
        $stmt->execute();
        $stmt->bind_result($available_time);
        
        $available_times = [];
        while ($stmt->fetch()) {
            $available_times[] = $available_time;
        }
        
        $stmt->close();
        echo json_encode(['booked_times' => $available_times]);
    }
    exit;
}

if (isset($_POST['delete_available_times'])) {
    $delete_times = isset($_POST['delete_times']) ? $_POST['delete_times'] : [];

    if (!empty($delete_times)) {
        $delete_times_placeholders = implode(',', array_fill(0, count($delete_times), '?'));
        $types = str_repeat('i', count($delete_times)); 
        $stmt = $conn->prepare("DELETE FROM available_times WHERE id IN ($delete_times_placeholders)");
        $stmt->bind_param($types, ...$delete_times);

        if ($stmt->execute()) {
            $success = "Selected available times deleted successfully.";
        } else {
            echo "<p>Error deleting available times: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Please select at least one available time to delete.</p>";
    }
}

    if (isset($_POST['fetch_available_times_for_deletion'])) {
        $selected_date = filter_input(INPUT_POST, 'delete_date', FILTER_SANITIZE_STRING);
        
        if ($selected_date) {
            $stmt = $conn->prepare("SELECT id, available_time FROM available_times WHERE available_date = ?");
            $stmt->bind_param('s', $selected_date);
            $stmt->execute();
            $stmt->bind_result($id, $available_time);
            
            $available_times = [];
            while ($stmt->fetch()) {
                $available_times[] = ['id' => $id, 'time' => $available_time];
            }
            
            $stmt->close();
            echo json_encode(['available_times' => $available_times]);
        }
        exit;
    }

        if (isset($_POST['add_service'])) {
            $category = $_POST['category'];
            $description = $_POST['description'];
            $price = $_POST['price'];
    
            $sql = "INSERT INTO services (category, description, price) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssd', $category, $description, $price);
            
            if ($stmt->execute()) {
                $success = "Service added successfully.";
            } else {
                $error = "Error adding service: " . $stmt->error;
            }
            $stmt->close();
        }
    if (isset($_POST['edit_service'])) {
        $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        
        if ($service_id !== false && $category && $description && $price !== false) {
            $stmt = $conn->prepare("UPDATE services SET category = ?, description = ?, price = ? WHERE id = ?");
            $stmt->bind_param('ssdi', $category, $description, $price, $service_id);
            if ($stmt->execute()) {
                $success = "Service updated successfully.";
            } else {
                $error = "Error updating service: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Invalid input data for editing service.";
        }
    }
    if (isset($_POST['delete_service'])) {
        $service_id = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        if ($service_id !== false) {
            $check_bookings_sql = "SELECT COUNT(*) AS total_bookings FROM booking_services WHERE service_id = ?";
            $stmt_check = $conn->prepare($check_bookings_sql);
            $stmt_check->bind_param('i', $service_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $row_check = $result_check->fetch_assoc();
            $total_bookings = $row_check['total_bookings'];
            $stmt_check->close();

            if ($total_bookings > 0) {
                $error = "Cannot delete the service. There are bookings associated with it.";
            } else {

                $delete_service_sql = "DELETE FROM services WHERE id = ?";
                $stmt_delete = $conn->prepare($delete_service_sql);
                $stmt_delete->bind_param('i', $service_id);
                if ($stmt_delete->execute()) {
                    $success = "Service deleted successfully.";
                } else {
                    $error = "Error deleting service: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            }
        } else {
            $error = "Invalid service ID.";
        }
    }

}

if ($success) {
    $_SESSION['success_message'] = $success;
}

if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$sql = "SELECT bookings.*, users.username FROM bookings JOIN users ON bookings.user_id = users.id ORDER BY booking_date, booking_time";
$bookings = $conn->query($sql);

$paid_sql = "SELECT bookings.*, users.username FROM bookings JOIN users ON bookings.user_id = users.id WHERE bookings.status = 'paid' ORDER BY booking_date, booking_time";
$paid_bookings = $conn->query($paid_sql);

$sql = "SELECT * FROM users";
$users_result = $conn->query($sql);


$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to Admin Panel</h2>
        
        <div class="dashboard">

            <div class="section-box" onclick="showSection('upload-image')">
                <h3>Upload Image</h3>
            </div>
            <div class="section-box" onclick="showSection('view-bookings')">
                <h3>View Bookings</h3>
            </div>
            <div class="section-box" onclick="showSection('view-paid-bookings')">
                <h3>View Paid Bookings</h3>
            </div>
            <div class="section-box" onclick="showSection('set-available-times')">
                <h3>Set Available Times</h3>
            </div>
            <div class="section-box" onclick="showSection('view-users')">
                <h3>View Users</h3>
            </div>
            <div class="section-box" onclick="showSection('add-service')">
                <h3>Add New Service</h3>
            </div>
            <div class="section-box" onclick="showSection('manage-services')">
                <h3>Manage Services</h3>
            </div>
        </div>

        <div id="upload-image" class="content-section">
            <h3>Upload Image</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="text" name="image_name" placeholder="Image Name" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="number" name="price" placeholder="Price" required>
                <input type="file" name="image" required>
                <button type="submit" name="submit">Upload</button>
            </form>
        </div>

        <div id="view-bookings" class="content-section">
            <h3>All Bookings</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['booking_date']; ?></td>
                        <td><?php echo $row['booking_time']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                        <form method="POST" action="">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="cancel_booking">Cancel</button>
                                <?php if ($row['status'] !== 'paid'): ?>
                                    <button type="submit" name="set_paid">Mark as Paid</button>
                                <?php endif; ?>
                        </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
<div id="view-paid-bookings" class="content-section">
    <h3>Paid Bookings</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $paid_bookings->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['booking_date']; ?></td>
                <td><?php echo $row['booking_time']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                <button class="view-button" data-booking-id="<?php echo $row['id']; ?>">View Booked Services</button>
                <table class="booked-services-table" style="display: none;">
                        <tr>
                            <th>Booking ID</th>
                            <th>Service ID</th>
                            <th>Category</th>
                            <th>Description</th>
                        </tr>
                        <?php 
                        $sql = "SELECT bs.booking_id, bs.service_id, s.category, s.description
                                FROM booking_services bs
                                JOIN services s ON bs.service_id = s.id
                                WHERE bs.booking_id = " . $row['id'];
                        $booking_services_result = $conn->query($sql);
                        
                        while ($booking_row = $booking_services_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $booking_row['booking_id']; ?></td>
                                <td><?php echo $booking_row['service_id']; ?></td>
                                <td><?php echo $booking_row['category']; ?></td>
                                <td><?php echo $booking_row['description']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
        <div id="set-available-times" class="content-section">
    <h3>Set Available Times</h3>
    <form method="POST" action="">
        <div class="calendar-container">
            <label for="available_date">Select Date:</label>
            <input type="date" id="available_date" name="available_date" required onchange="fetchAvailableTimes()">
        </div>
        <div id="time-slots" class="time-slots">
            <label><input type="checkbox" name="available_times[]" value="08:00:00" id="time_08"> 08:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="09:00:00" id="time_09"> 09:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="10:00:00" id="time_10"> 10:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="11:00:00" id="time_11"> 11:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="12:00:00" id="time_12"> 12:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="13:00:00" id="time_13"> 13:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="14:00:00" id="time_14"> 14:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="15:00:00" id="time_15"> 15:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="16:00:00" id="time_16"> 16:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="17:00:00" id="time_17"> 17:00</label><br>
            <label><input type="checkbox" name="available_times[]" value="18:00:00" id="time_18"> 18:00</label><br>
        </div>
        <button type="submit" name="set_available_time">Set Available Time</Time></button>
    </form>
    <form method="POST" action="">
                <div class="calendar-container">
                    <label for="delete_date">Select Date:</label>
                    <input type="date" id="delete_date" name="delete_date" required onchange="fetchAvailableTimesForDeletion()">
                </div>
                <div id="delete-time-slots" class="time-slots">
              
                </div>
                <button type="submit" name="delete_available_times">Delete Selected Times</button>
            </form>
</div>
        <div id="view-users" class="content-section">
            <h3>View Users</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div id="add-service" class="content-section">
    <h3>Add New Service</h3>
    <form method="POST" action="">
        <select name="category" required class="styled-select">
            <option value="" disabled selected>Select Category</option>
            <option value="Acrylic Overlay">Acrylic Overlay</option>
            <option value="Acrylic Sculpture">Acrylic Sculpture</option>
            <option value="Gel Overlay">Gel Overlay</option>
            <option value="Art">Art</option>
            <option value="Removal">Removal</option>
        </select>
        <input type="text" name="description" placeholder="Description" required>
        <input type="number" name="price" placeholder="Price" required>
        <button type="submit" name="add_service">Add Service</button>
    </form>
</div>

        <div id="manage-services" class="content-section">
            <h3>Existing Services</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>R<?php echo $row['price']; ?></td>
                        <td>
                        <form id="editForm" method="POST" action="">
                        <input type="hidden" name="service_id" value="<?php echo $row['id']; ?>">
                        
                        <div class="edit-fields" style="display: none;">
                            <input type="text" name="category" value="<?php echo $row['category']; ?>" required>
                            <input type="text" name="description" value="<?php echo $row['description']; ?>" required>
                            <input type="number" name="price" value="<?php echo $row['price']; ?>" required>
                        </div>
                       
                        <button type="button" class="edit-button">Edit</button>
                        <button type="submit" name="delete_service" onclick="return confirm('Are you sure you want to delete this service?')">Delete</button>
                        <button type="submit" class="confirm-edit" name="edit_service" style="display: none;">Confirm Edit</button>
                    
                    </div>
                    </form>                  
                </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-button');

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const table = this.nextElementSibling; 
                table.style.display = table.style.display === 'none' ? 'table' : 'none';
            });
        });
    });
    document.addEventListener("DOMContentLoaded", function() {

        var editButtons = document.querySelectorAll(".edit-button");

        editButtons.forEach(function(button) {
            button.addEventListener("click", function() {
    
                var editFields = this.parentNode.querySelector(".edit-fields");
                var confirmEditButton = this.parentNode.querySelector(".confirm-edit");

                if (editFields.style.display === "none") {
            
                    editFields.style.display = "block";
                    confirmEditButton.style.display = "inline-block";
         
                    this.style.display = "none";
                } else {
            
                    editFields.style.display = "none";
                    confirmEditButton.style.display = "none";
      
                    this.style.display = "inline-block";
                }
            });
        });
    });
               function showSection(sectionId) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
        });

        function fetchAvailableTimes() {
            const date = document.getElementById('available_date').value;
            if (date) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'fetch_available_times': true,
                        'available_date': date
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const timeSlots = document.querySelectorAll('.time-slots input[type="checkbox"]');
                    timeSlots.forEach(checkbox => {
                        checkbox.checked = false;
                        checkbox.disabled = false;
                    });
                    data.booked_times.forEach(bookedTime => {
                        const checkbox = document.querySelector(`#time_${bookedTime.replace(':', '').slice(0, 2)}`);
                        if (checkbox) {
                            checkbox.checked = true;
                            checkbox.disabled = true;
                        }
                    });
                });
            }
        }
        function fetchAvailableTimesForDeletion() {
            const date = document.getElementById('delete_date').value;
            if (date) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'fetch_available_times_for_deletion': true,
                        'delete_date': date
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const deleteTimeSlots = document.getElementById('delete-time-slots');
                    deleteTimeSlots.innerHTML = '';

                    data.available_times.forEach(timeSlot => {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'delete_times[]';
                        checkbox.value = timeSlot.id;
                        checkbox.id = `delete_time_${timeSlot.id}`;

                        const label = document.createElement('label');
                        label.htmlFor = `delete_time_${timeSlot.id}`;
                        label.textContent = timeSlot.time;

                        const br = document.createElement('br');

                        deleteTimeSlots.appendChild(checkbox);
                        deleteTimeSlots.appendChild(label);
                        deleteTimeSlots.appendChild(br);
                    });
                });
            }
        }

    function showSection(sectionId) {
     
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => section.style.display = 'none');
      
        document.getElementById(sectionId).style.display = 'block';
    }
    document.addEventListener('DOMContentLoaded', function() {
  
    function displaySuccessMessage(message) {
        if (message) {
            const successMessageElement = document.createElement('div');
            successMessageElement.classList.add('success-message');
            successMessageElement.textContent = message;
            document.body.appendChild(successMessageElement);

            document.addEventListener('click', function hideSuccessMessage() {
                document.body.removeChild(successMessageElement);
                document.removeEventListener('click', hideSuccessMessage);
            });
        }
    }

    <?php if ($success): ?>
        displaySuccessMessage(<?php echo json_encode($success); ?>);
    <?php endif; ?>
});
   
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => section.style.display = 'none');

        const timeSlots = document.querySelectorAll('.time-slot');
        timeSlots.forEach(slot => {
            slot.addEventListener('click', () => {
                timeSlots.forEach(s => s.classList.remove('selected'));
                slot.classList.add('selected');
                document.getElementById('available_time').value = slot.getAttribute('data-time');
            });
        });
    });
    </script>
</body>
</html>
