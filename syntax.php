<?php
$schoolName = "Kingforkbeard Academy of Chess";
$activeClasses = ["Beginner Tactics", "Advanced Openings", "Endgame Mastery"];

echo "<h2>Welcome to $schoolName</h2>";
echo "<p>Current Active Modules:</p>";
echo "<ul>";
foreach ($activeClasses as $class) {
    echo "<li>" . htmlspecialchars($class) . "</li>";
}
echo "</ul>";
?>