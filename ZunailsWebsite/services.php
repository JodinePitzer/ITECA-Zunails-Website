<?php
session_start();

require 'db_connection.php';

function fetchServices($conn) {
    $categories = [
        'Acrylic Overlay' => [],
        'Gel Overlay' => [],
        'Acrylic Sculpture' => [],
        'Art' => [],
        'Removal' => []
    ];

    $sql = "SELECT * FROM services";
    $result = $conn->query($sql);

    if (!$result) {
        echo "Error: " . $conn->error;
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        if (isset($categories[$row['category']])) {
            $categories[$row['category']][] = $row;
        }
    }

    return $categories;
}

$categories = fetchServices($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Services</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&family=Poppins&display=swap" rel="stylesheet">
</head>
<body>
    <header><?php include 'nav.php'; ?></header>

    <section class="intro-section">
        <div class="intro-container">
            <div class="intro-heading">
                <h1>Book Online</h1>
            </div>
            <div class="intro-text">
                <p>At Zunails, booking your next nail appointment is a breeze with our convenient online booking system. Simply browse through our range of services, select your desired treatments, and with just a few clicks, secure your spot with us. It's that easy! Take the hassle out of scheduling and book your appointment online today.</p>
            </div>
        </div>
    </section>

    <div class="parent-container">
        <div class="services-container">
            <h2>Services and Pricing</h2>
            <form class="services-form" id="serviceForm">
                <h3>Step 1 - Decide the Length and Product</h3>
                <div class="step1">
                    <div class="services-grid">
                        <?php foreach (['Acrylic Overlay', 'Gel Overlay', 'Acrylic Sculpture'] as $category): ?>
                            <div class="service-category">
                                <h4><?php echo $category; ?></h4>
                                <?php foreach ($categories[$category] as $service): ?>
                                    <div class="service-item">
                                        <label>
                                            <input type="radio" name="main_category" class="service main-category-radio" value="<?php echo $service['id']; ?>" data-description="<?php echo $service['description']; ?>" data-category="<?php echo $category; ?>" data-price="<?php echo intval($service['price']); ?>">
                                            <span class="description"><?php echo $service['description']; ?></span>
                                            <span class="price">R<?php echo intval($service['price']); ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <h3>Step 2 - Determine Any Additions</h3>
                <div class="step2">
                    <div class="service-category">
                        <h4>Art & Design</h4>
                        <div class="art-grid">
                            <div>
                                <?php foreach ($categories['Art'] as $index => $service): ?>
                                    <?php if (strpos($service['description'], 'p/nail') === false): ?>
                                        <div class="service-item service-art-item">
                                            <label>
                                                <input type="radio" name="art_<?php echo $index; ?>" class="service-art-radio" value="<?php echo $service['id']; ?>" data-description="<?php echo $service['description']; ?>" data-category="Art" data-price="<?php echo intval($service['price']); ?>">
                                                <span class="description"><?php echo $service['description']; ?></span>
                                                <span class="price">R<?php echo intval($service['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div>
                                <?php foreach ($categories['Art'] as $index => $service): ?>
                                    <?php if (strpos($service['description'], 'p/nail') !== false): ?>
                                        <div class="service-item service-art-item">
                                            <label>
                                                <select class="service-art" data-price="<?php echo intval($service['price']); ?>" data-description="<?php echo $service['description']; ?>" data-category="Art">
                                                    <option value="0">Qty</option>
                                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <span class="description"><?php echo $service['description']; ?></span>
                                                <span class="price">R<?php echo intval($service['price']); ?></span>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Step 3 - Remove Current Artificial Nails</h3>
                <div class="step3">
                    <div class="services-grid">
                        <div class="service-category">
                            <h4>Removal</h4>
                            <?php foreach ($categories['Removal'] as $service): ?>
                                <div class="service-item service-removal-item">
                                    <label>
                                        <input type="radio" name="removal" class="service-removal" value="<?php echo $service['id']; ?>" data-description="<?php echo $service['description']; ?>" data-category="Removal" data-price="<?php echo intval($service['price']); ?>">
                                        <span class="description"><?php echo $service['description']; ?></span>
                                        <span class="price">R<?php echo intval($service['price']); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </form>

            <div class="service-summary-container">
                <div class="service-total" id="serviceTotal">
                    <h3>Total Cost: R<span id="totalCost">0.00</span></h3>
                </div>
                <div>
                    <button id="bookBtn" class="book-btn">Book This Service</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalCostSpan = document.getElementById('totalCost');
            const serviceForm = document.getElementById('serviceForm');
            const bookBtn = document.getElementById('bookBtn');
            let lastSelected = null;

            function calculateTotalCost() {
                let totalCost = 0;
                let selectedServices = [];
                const selectedRadios = serviceForm.querySelectorAll('input[type="radio"]:checked');
                const artQtyInputs = serviceForm.querySelectorAll('.service-art');
                
                selectedRadios.forEach(function(radio) {
                    const price = parseFloat(radio.getAttribute('data-price'));
                    totalCost += price;
                    selectedServices.push({
                        id: radio.value,
                        description: radio.getAttribute('data-description'),
                        price: price,
                        category: radio.getAttribute('data-category')
                    });
                });

                artQtyInputs.forEach(function(input) {
                    const qty = parseInt(input.value) || 0;
                    const price = parseFloat(input.getAttribute('data-price'));
                    if (qty > 0) {
                        totalCost += price * qty;
                        selectedServices.push({
                            id: null,
                            description: input.getAttribute('data-description') + ' x' + qty,
                            price: price * qty,
                            category: 'Art'
                        });
                    }
                });

                totalCostSpan.textContent = totalCost.toFixed(0); 
                localStorage.setItem('selectedServices', JSON.stringify(selectedServices));
                localStorage.setItem('totalCost', totalCost.toFixed(2));
            }

            serviceForm.addEventListener('change', calculateTotalCost);

            serviceForm.addEventListener('click', function(event) {
                if (event.target.type === 'radio') {
                    if (lastSelected && lastSelected === event.target) {
                        event.target.checked = false;
                        lastSelected = null;
                    } else {
                        lastSelected = event.target;
                    }
                    calculateTotalCost();
                }
            });

            bookBtn.addEventListener('click', function(event) {
                event.preventDefault();
                const selectedServices = JSON.parse(localStorage.getItem('selectedServices'));
                const totalCost = localStorage.getItem('totalCost');
                
                sessionStorage.setItem('selectedServices', JSON.stringify(selectedServices));
                sessionStorage.setItem('totalCost', totalCost);

                fetch('set_redirect.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ redirectUrl: 'book.php' })
                }).then(() => {
                    window.location.href = 'login.php';
                });
            });
        });
    </script>
</body>
</html>