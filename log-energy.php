<!-- An energy log script, may be useful to save listing data -->
<?php
require_once "head.php";
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /content/carbon-calculator.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['input_error'] = 'You must be logged in to log energy usage.';
    header('Location: /content/carbon-calculator.php');
    exit;
}

$log_date = filter_input(INPUT_POST, 'log_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$generation_kw = filter_input(INPUT_POST, 'generation_kw', FILTER_VALIDATE_FLOAT);
$consumption_kw = filter_input(INPUT_POST, 'consumption_kw', FILTER_VALIDATE_FLOAT);

if (!$log_date || $generation_kw === false || $consumption_kw === false) {
    $_SESSION['input_error'] = 'Invalid input values. Please check your entries.';
    header('Location: /content/carbon-calculator.php');
    exit;
}

$d = DateTime::createFromFormat('Y-m-d', $log_date);
if (!$d || $d->format('Y-m-d') !== $log_date) {
    $_SESSION['input_error'] = 'Invalid date format.';
    header('Location: /content/carbon-calculator.php');
    exit;
}

$grid_import_export = $generation_kw - $consumption_kw;

$log_timestamp = $log_date . ' 00:00:00';

$stmt = $conn->prepare(
    "INSERT INTO rolsa_energy_logs (fk_user_id, log_timestamp, generation_kw, consumption_kw, grid_import_export)
     VALUES (?, ?, ?, ?, ?)"
);

if (!$stmt) {
    $_SESSION['input_error'] = 'DB error: ' . $conn->error;
    header('Location: /content/carbon-calculator.php');
    exit;
}

$stmt->bind_param('isddd', $user_id, $log_timestamp, $generation_kw, $consumption_kw, $grid_import_export);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Energy usage logged successfully! (Grid: ' . number_format($grid_import_export, 2) . ' kW)';
} else {
    $_SESSION['input_error'] = 'Could not log energy usage: ' . $stmt->error;
}

$stmt->close();

header('Location: /content/carbon-calculator.php');
exit;