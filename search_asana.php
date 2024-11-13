<?php
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yoga_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['asana_name']) && !empty(trim($_GET['asana_name']))) {
    $asana_name = trim($_GET['asana_name']);
    $stmt = $conn->prepare("SELECT name, benefits, restrictions, conditions_to_avoid, tutorial_link, image FROM asanas WHERE name = ?");
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $asana_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='search-result'>";

        while ($row = $result->fetch_assoc()) {
            if (!empty($row['image'])) {
                $imageData = base64_encode($row['image']);
                echo "<div class='image-container'><img src='data:image/jpeg;base64,{$imageData}' alt='Asana Image' style='max-width:300px; height:auto; border-radius:8px;'></div>";
            } else {
                echo "<p>No image available for this asana.</p>";
            }

            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<div class='section-title'>Benefits of " . htmlspecialchars($row['name']) . "</div>";
            echo "<p>" . htmlspecialchars($row['benefits']) . "</p>";
            echo "<div class='section-title'>Restrictions</div>";
            echo "<p><strong>Restrictions:</strong> " . htmlspecialchars($row['restrictions']) . "</p>";

            if (!empty($row['conditions_to_avoid'])) {
                echo "<div class='section-title'>Conditions to Avoid</div>";
                echo "<p>" . htmlspecialchars($row['conditions_to_avoid']) . "</p>";
            }

            if (!empty($row['tutorial_link'])) {
                echo "<div class='section-title'>Learn How to Do " . htmlspecialchars($row['name']) . "</div>";
                echo "<p>For a detailed tutorial, visit the link below:</p>";
                echo "<a href='" . htmlspecialchars($row['tutorial_link']) . "' class='tutorial-link' target='_blank'>Click here for the tutorial</a>";
            }
        }

        echo "</div>";
    } else {
        echo "<p>Asana not found.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Please enter an asana name to search.</p>";
}

$conn->close();
?>
