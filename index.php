<?php

require_once 'includes/db_connect.php';

$pageTitle = "Home";
$basePath = "";

// Fetch distinct specializations for the filter dropdown
$specStmt = $pdo->query(
    "SELECT DISTINCT specialization
     FROM doctors
     ORDER BY specialization"
);

$specializations = $specStmt->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';

?>

<!-- =========================
     HERO SECTION
========================= -->

<section class="hero">

    <h1>Find & Book the Right Doctor, Instantly</h1>

    <p>
        Browse our doctor panel, check availability
        and channel your appointment online.
    </p>

</section>


<!-- =========================
     FILTER SECTION
========================= -->

<section class="filter-bar">

    <input
        type="text"
        id="searchInput"
        placeholder="Search doctor by name..."
    >


    <select id="specializationFilter">

        <option value="">
            All Specializations
        </option>

        <?php foreach ($specializations as $spec): ?>

            <option
                value="<?php echo htmlspecialchars($spec); ?>"
            >
                <?php echo htmlspecialchars($spec); ?>
            </option>

        <?php endforeach; ?>

    </select>


    <button id="resetFilters">
        Reset
    </button>

</section>


<!-- =========================
     DOCTOR GRID
========================= -->

<section
    class="doctor-grid"
    id="doctorGrid"
>

    <p class="loading-text">
        Loading doctors...
    </p>

</section>


<!-- =========================
     DOCTOR CARD TEMPLATE
========================= -->

<template id="doctorCardTemplate">

    <div class="doctor-card">

        <!-- Doctor Image -->

        <img
            class="doctor-img"
            src=""
            alt="Doctor photo"
        >


        <div class="doctor-info">

            <!-- Doctor Name -->

            <h3 class="doctor-name"></h3>


            <!-- Specialization -->

            <p class="doctor-spec"></p>


            <!-- Available Days -->

            <p class="doctor-days">

                <strong>Days:</strong>

                <span></span>

            </p>


            <!-- Available Time -->

            <p class="doctor-times">

                <strong>Time:</strong>

                <span></span>

            </p>


            <!-- Consultation Fee -->

            <p class="doctor-fee">

                Rs.

                <span></span>

            </p>


            <!-- Book Appointment -->

            <a
                class="book-btn"
                href="#"
            >
                Book Appointment
            </a>

        </div>

    </div>

</template>


<?php include 'includes/footer.php'; ?>


<!-- =========================
     JAVASCRIPT
========================= -->

<script src="js/script.js"></script>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        /*
         * Doctor images
         *
         * Make sure these files are inside:
         *
         * images/doctors/
         *
         * doctor-1.png
         * doctor-2.png
         * doctor-3.png
         */

        const doctorImages = [

            "images/doctors/doctor-1.png",

            "images/doctors/doctor-2.png",

            "images/doctors/doctor-3.png"

        ];


        /*
         * We wait until the existing
         * JavaScript creates the doctor cards.
         */

        const doctorGrid =
            document.getElementById("doctorGrid");


        const observer =
            new MutationObserver(function () {

                const doctorCards =
                    doctorGrid.querySelectorAll(
                        ".doctor-card"
                    );


                /*
                 * Add the 3 images to
                 * the first 3 doctor cards.
                 */

                doctorCards.forEach(
                    function (card, index) {

                        if (
                            index < doctorImages.length
                        ) {

                            const doctorImage =
                                card.querySelector(
                                    ".doctor-img"
                                );


                            if (doctorImage) {

                                doctorImage.src =
                                    doctorImages[index];


                                doctorImage.alt =
                                    "Doctor " +
                                    (index + 1);

                            }

                        }

                    }
                );

            });


        /*
         * Watch for cards being added
         * by AJAX / script.js
         */

        observer.observe(
            doctorGrid,
            {
                childList: true,
                subtree: true
            }
        );

    }
);

</script>