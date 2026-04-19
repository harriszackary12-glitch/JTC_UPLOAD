<!-- Cancel booking, may be useful -->
<?php
require_once "head.php";
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$booking_id = filter_input(INPUT_POST, 'pk_booking_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['error'] = 'You must be logged in to cancel bookings.';
    header('Location: /index.php');
    exit;
}

if (!$booking_id) {
    $_SESSION['error'] = 'Invalid booking id.';
    header('Location: /index.php');
    exit;
}

$chk = $conn->prepare("SELECT booking_status FROM rolsa_bookings WHERE pk_booking_id = ? AND fk_user_id = ?");
if (!$chk) {
    $_SESSION['error'] = 'DB error: ' . $conn->error;
    header('Location: /index.php');
    exit;
}
$chk->bind_param('ii', $booking_id, $user_id);
$chk->execute();
$res = $chk->get_result();
$row = $res->fetch_assoc();
$chk->close();

if (!$row) {
    $_SESSION['error'] = 'Booking not found or not authorised.';
    header('Location: /index.php');
    exit;
}

if ($row['booking_status'] === 'Cancelled') {
    $_SESSION['error'] = 'Booking is already cancelled.';
    header('Location: /index.php');
    exit;
}

$upd = $conn->prepare("UPDATE rolsa_bookings SET booking_status = 'Cancelled' WHERE pk_booking_id = ? AND fk_user_id = ?");
if (!$upd) {
    $_SESSION['error'] = 'DB error: ' . $conn->error;
    header('Location: /index.php');
    exit;
}
$upd->bind_param('ii', $booking_id, $user_id);
if ($upd->execute()) {
    $_SESSION['success'] = 'Booking cancelled.';
} else {
    $_SESSION['error'] = 'Could not cancel booking: ' . $upd->error;
}
$upd->close();

header('Location: /index.php');
exit;