<?php
require_once 'includes/db_connect.php';
$pageTitle = "Home";
$basePath = "";

// Fetch distinct specializations for the filter dropdown
$specStmt = $pdo->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization");
$specializations = $specStmt->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>

<section class="hero">
    <h1>Find & Book the Right Doctor, Instantly</h1>
    <p>Browse our doctor panel, check availability and channel your appointment online.</p>
</section>

<section class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search doctor by name...">

    <select id="specializationFilter">
        <option value="">All Specializations</option>
        <?php foreach ($specializations as $spec): ?>
            <option value="<?php echo htmlspecialchars($spec); ?>"><?php echo htmlspecialchars($spec); ?></option>
        <?php endforeach; ?>
    </select>

    <button id="resetFilters">Reset</button>
</section>

<section class="doctor-grid" id="doctorGrid">
    <!-- Doctor cards are injected here by js/script.js via AJAX call to search.php -->
    <p class="loading-text">Loading doctors...</p>
</section>

<!-- Template used by JS to build each doctor card (hidden, cloned via JS) -->
<template id="doctorCardTemplate">
    <div class="doctor-card">
        <img class="doctor-img" src="" alt="Doctor photo">
        <div class="doctor-info">
            <h3 class="doctor-name"></h3>
            <p class="doctor-spec"></p>
            <p class="doctor-days"><strong>Days:</strong> <span></span></p>
            <p class="doctor-times"><strong>Time:</strong> <span></span></p>
            <p class="doctor-fee">Rs. <span></span></p>
            <a class="book-btn" href="#">Book Appointment</a>
        </div>
    </div>
</template>

<?php include 'includes/footer.php'; ?>

<script src="js/script.js"></script>
