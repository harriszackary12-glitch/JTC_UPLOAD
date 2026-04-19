<!--Booking & Consultation, may be useful -->
<?php
require_once "head.php";
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "book") {
    $product_id = filter_input(INPUT_POST, "pk_product_id", FILTER_VALIDATE_INT);
    $scheduled_date = filter_input(INPUT_POST, "scheduled_date", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = $_SESSION["user_id"] ?? null;

    if (!$user_id) {
        $_SESSION["error"] = "You must be logged in to book.";
        header("Location: /index.php");
        exit;
    }

    $d = DateTime::createFromFormat("Y-m-d", $scheduled_date);
    if (!$d || $d->format("Y-m-d") !== $scheduled_date) {
        $_SESSION["error"] = "Invalid date format.";
        header("Location: /index.php");
        exit;
    }

    $today = new DateTime("today");
    if ($d <= $today) {
        $_SESSION["error"] = "Please choose a date after today.";
        header("Location: /index.php");
        exit;
    }

    if ($product_id && $scheduled_date) {
        $chk = $conn->prepare(
            "SELECT COUNT(*) AS count
             FROM rolsa_bookings
             WHERE fk_user_id = ? AND fk_product_id = ? 
             AND booking_type = 'Consultation'
             AND booking_status != 'Cancelled'"
        );
        if ($chk) {
            $chk->bind_param("ii", $user_id, $product_id);
            $chk->execute();
            $res = $chk->get_result();
            $row = $res->fetch_assoc();
            $chk->close();

            if (!empty($row["count"]) && (int)$row["count"] > 0) {
                $_SESSION["error"] = "You already have a consultation booking for this product.";
                header("Location: /index.php");
                exit;
            }
        } else {
            $_SESSION["error"] = "DB error (duplicate check): " . $conn->error;
            header("Location: /index.php");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO rolsa_bookings (fk_user_id, fk_product_id, booking_type, scheduled_date) 
                                VALUES (?, ?, 'Consultation', ?)");
        if ($stmt) {
            $stmt->bind_param("iis", $user_id, $product_id, $scheduled_date);
            if ($stmt->execute()) {
                $_SESSION["success"] = "Booking confirmed!";
            } else {
                $_SESSION["error"] = "DB error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION["error"] = "DB prepare failed: " . $conn->error;
        }
    } else {
        $_SESSION["error"] = "Missing required fields";
    }

    header("Location: /index.php");
    exit;
}