<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db_connection.php';


$sql = "SELECT image_name, description, price, image_path FROM images";
$result = $conn->query($sql);

$images = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="gallery-container">
        <h2 class="gallery-heading">Gallery</h2>
        <div class="gallery">
            <?php foreach ($images as $index => $image): ?>
                <div class="gallery-item" <?php echo $index >= 12 ? 'style="display:none;"' : ''; ?>>
                    <img src="uploads/<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['image_name']); ?>">
                    <div class="description">
                        <h3><?php echo htmlspecialchars($image['image_name']); ?></h3>
                        <p><?php echo htmlspecialchars($image['description']); ?></p>
                        <p>R<?php echo htmlspecialchars($image['price']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="load-more-button" onclick="toggleGallery()">Load More</button>
    </div>

    <footer id="contact-section"><?php include 'footer.php'; ?></footer>

    <script>
        let currentVisibleCount = 12;
        const increment = 6;

        function toggleGallery() {
            const galleryItems = document.querySelectorAll('.gallery-item');
            const button = document.querySelector('.load-more-button');

            if (currentVisibleCount < galleryItems.length) {
                for (let i = currentVisibleCount; i < currentVisibleCount + increment && i < galleryItems.length; i++) {
                    galleryItems[i].style.display = 'block';
                }
                currentVisibleCount += increment;

                if (currentVisibleCount >= galleryItems.length) {
                    button.textContent = 'View Less';
                }
            } else {
                for (let i = 12; i < galleryItems.length; i++) {
                    galleryItems[i].style.display = 'none';
                }
                currentVisibleCount = 12;
                button.textContent = 'Load More';
            }
        }
    </script>
</body>
</html>
