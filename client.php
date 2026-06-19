<?php
declare(strict_types=1);

const APP_NAME = 'Parcheggiamo';

$dataDir = __DIR__ . '/data';
$uploadDir = __DIR__ . '/uploads';

foreach ([$dataDir, $dataDir . '/sessions', $uploadDir] as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
}

session_save_path($dataDir . '/sessions');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//Escapes output before rendering it in HTML.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

//Redirects to another page and stops the request.
function go(string $path): never
{
    header('Location: ' . $path);
    exit;
}

// Saves a one-time message in the session.
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = compact('type', 'message');
}

// Reads and clears the one-time message.   
function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Creates or returns the CSRF token used by forms.
function csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

//Validates form submissions before actions run.
function check_csrf(): void
{
    if (!hash_equals(csrf(), $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

function require_client(): void
{
    if (($_SESSION['user']['role'] ?? '') !== 'client') {
        go('login.php');
    }
}

// Creates empty starter data once per browser session.
function ensure_session_data(): void
{
    if (!empty($_SESSION['session_data_ready'])) {
        return;
    }

    $_SESSION['user'] ??= [
        'id' => 1,
        'name' => 'Client',
        'role' => 'client',
    ];

    $_SESSION['account'] = [
        'id' => 1,
        'full_name' => $_SESSION['user']['name'] ?? 'Client',
        'email' => '',
        'phone' => '',
        'address' => '',
        'license_image' => null,
        'profile_image' => null,
    ];

    $_SESSION['account_attachments'] = [];
    $_SESSION['vehicles'] = [];
    $_SESSION['vehicle_attachments'] = [];
    $_SESSION['history'] = [];
    $_SESSION['session_data_ready'] = true;
}

// Returns the next integer id for a session-backed list.
function next_session_id(array $items): int
{
    $ids = array_map(fn($item) => (int) ($item['id'] ?? 0), $items);
    return $ids ? max($ids) + 1 : 1;
}

// Normalizes text input from forms.
function input_text(array $source, string $key): string
{
    $value = isset($source[$key]) ? (string) $source[$key] : '';
    return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
}

// Accepts only known choices and falls back to a default.
function input_choice(array $source, string $key, array $allowed, string $default): string
{
    $value = input_text($source, $key);
    return in_array($value, $allowed, true) ? $value : $default;
}

// Stops an action when required inputs are missing.
function require_input_fields(array $source, array $fields, string $message): void
{
    foreach ($fields as $field) {
        if (input_text($source, $field) === '') {
            throw new RuntimeException($message);
        }
    }
}

// Cleans plate numbers for display and validation.
function clean_plate_number(string $plateNumber): string
{
    $plateNumber = strtoupper(input_text(['plate' => $plateNumber], 'plate'));
    $plateNumber = preg_replace('/[^A-Z0-9 -]/', '', $plateNumber) ?? '';
    return trim($plateNumber);
}

function parking_charge(string $entryTime, ?string $exitTime = null): float
{
    $start = strtotime($entryTime);
    $end = $exitTime ? strtotime($exitTime) : time();
    $minutes = max(1, (int) ceil(($end - $start) / 60));
    $fullDays = intdiv($minutes, 1440);
    $remaining = $minutes % 1440;
    $charge = $fullDays * 1500;

    if ($remaining > 0) {
        $charge += $remaining <= 720 ? 500 : 1500;
    }

    return (float) $charge;
}

// Builds the parking rate cards.
function parking_rates(): array
{
    return [
        ['label' => 'Up to 12 Hours', 'amount' => 500],
        ['label' => 'Up to 24 Hours', 'amount' => 1500],
    ];
}

// Builds all slots and marks occupied slots from session history.
function parking_slots(): array
{
    $occupiedBySlot = [];
    foreach ($_SESSION['history'] ?? [] as $row) {
        if (empty($row['exit_time']) && empty($row['checkout_requested_at'])) {
            $occupiedBySlot[(int) $row['slot_id']] = $row;
        }
    }

    $slots = [];
    $id = 1;
    for ($levelNumber = 1; $levelNumber <= 3; $levelNumber++) {
        foreach (['A', 'B', 'C'] as $slotCategory) {
            for ($slotNumber = 1; $slotNumber <= 10; $slotNumber++) {
                $activeRow = $occupiedBySlot[$id] ?? null;
                $slots[] = [
                    'id' => $id,
                    'slot_code' => 'L' . $levelNumber . '-' . $slotCategory . $slotNumber,
                    'floor' => 'Level ' . $levelNumber,
                    'vehicle_type' => 'Car',
                    'hourly_rate' => 50 + (($levelNumber - 1) * 10),
                    'status' => $activeRow ? 'occupied' : 'available',
                    'occupied_owner_id' => $activeRow ? 1 : null,
                ];
                $id++;
            }
        }
    }

    return $slots;
}

// Saves uploaded files and returns attachment metadata.
function upload_documents(string $field, int $existingCount = 0, ?int $maxFiles = null): array
{
    if (empty($_FILES[$field]) || !is_array($_FILES[$field]['name'])) {
        return [];
    }

    $uploadDir = __DIR__ . '/uploads';
    $files = $_FILES[$field];
    $indexes = array_keys(array_filter($files['name'], fn($name) => $name !== ''));

    if ($maxFiles !== null && $existingCount + count($indexes) > $maxFiles) {
        throw new RuntimeException('You can upload a maximum of ' . $maxFiles . ' file(s) here.');
    }

    $saved = [];
    foreach ($indexes as $index) {
        if ($files['error'][$index] !== UPLOAD_ERR_OK || $files['size'][$index] > 5 * 1024 * 1024) {
            throw new RuntimeException('Each attachment must be 5 MB or smaller.');
        }

        $original = basename((string) $files['name'][$index]);
        $stored = uniqid('upload-', true) . '-' . preg_replace('/[^A-Za-z0-9._-]/', '_', $original);

        if (!move_uploaded_file($files['tmp_name'][$index], $uploadDir . '/' . $stored)) {
            throw new RuntimeException('Unable to save an attachment.');
        }

        $saved[] = [
            'id' => random_int(3000, 999999),
            'stored_name' => $stored,
            'original_name' => $original,
        ];
    }

    return $saved;
}

// Saves one profile image and returns its stored filename.
function upload_profile_image(string $field): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Profile picture must be 5 MB or smaller.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException('Profile picture must be JPG, PNG, or WEBP.');
    }

    $stored = 'profile-' . uniqid('', true) . '.' . $allowedTypes[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $stored)) {
        throw new RuntimeException('Unable to save profile picture.');
    }

    return $stored;
}

require_client();
ensure_session_data();

//Completes one parking history row and releases it from active parking.
function complete_checkout_record(array &$row): void
{
    $exitTime = ($row['checkout_requested_at'] ?? null) ?: date('Y-m-d H:i:s');
    $entryTime = strtotime($row['entry_time']);
    $durationMinutes = max(1, (int) ceil((strtotime($exitTime) - $entryTime) / 60));
    $fee = parking_charge($row['entry_time'], $exitTime);

    $row['exit_time'] = $exitTime;
    $row['duration_minutes'] = $durationMinutes;
    $row['fee'] = $fee;
    $row['paid_amount'] = $fee;
    $row['payment_status'] = 'paid';
    $row['checkout_requested_at'] = $exitTime;
    $row['receipt_seen_at'] = $row['receipt_seen_at'] ?? null;
}

// Fixes older session records that were marked checkout-requested but not exited yet.
function complete_pending_checkouts(): void
{
    foreach ($_SESSION['history'] ?? [] as &$row) {
        if (empty($row['exit_time']) && !empty($row['checkout_requested_at'])) {
            complete_checkout_record($row);
        }
    }
    unset($row);
}

complete_pending_checkouts();

// Checks if the account already has a license on file.
function client_has_license(): bool
{
    return !empty($_SESSION['account']['license_image']) || count($_SESSION['account_attachments'] ?? []) > 0;
}

// Adds a new vehicle to session-backed data.
function handle_add_vehicle(): void
{
    if (!client_has_license()) {
        throw new RuntimeException('Please upload your license first.');
    }

    $vehicles = $_SESSION['vehicles'] ?? [];
    if (count($vehicles) >= 3) {
        throw new RuntimeException('You can register a maximum of three vehicles only.');
    }

    require_input_fields(
        $_POST,
        ['plate_number','vehicle_type'],
        'Complete all vehicle information.'
    );

    $plateNumber = clean_plate_number(input_text($_POST, 'plate_number'));
    if ($plateNumber === '' || !preg_match('/^[A-Z0-9 -]{2,20}$/', $plateNumber)) {
        throw new RuntimeException('Enter a valid plate number.');
    }

    $vehicleId = next_session_id($vehicles);
    $vehicles[] = [
        'id' => $vehicleId,
        'plate_number' => $plateNumber,
        'vehicle_type' => input_choice($_POST, 'vehicle_type', ['Car','Motorcycle','Van','Service'], 'Car'),
    ];

    $_SESSION['vehicles'] = $vehicles;
    $_SESSION['vehicle_attachments'][$vehicleId] = upload_documents('registration_documents');
    set_flash('success', 'Vehicle added.');
}

// Creates an active parking record for the selected vehicle and slot.
function handle_park_in(): void
{
    if (!client_has_license()) {
        throw new RuntimeException('Please upload your license first.');
    }

    $vehicleId = isset($_POST['vehicle_id']) ? (int) $_POST['vehicle_id'] : 0;
    $slotId = isset($_POST['slot_id']) ? (int) $_POST['slot_id'] : 0;
    $vehicles = $_SESSION['vehicles'] ?? [];
    $vehicle = null;

    foreach ($vehicles as $item) {
        if ((int) $item['id'] === $vehicleId) {
            $vehicle = $item;
            break;
        }
    }

    if (!$vehicle) {
        throw new RuntimeException('Select one of your registered vehicles.');
    }

    complete_pending_checkouts();

    foreach ($_SESSION['history'] ?? [] as $row) {
        if (
            (int) $row['vehicle_id'] === $vehicleId
            && empty($row['exit_time'])
            && empty($row['checkout_requested_at'])
        ) {
            throw new RuntimeException('That vehicle is already parked.');
        }
    }

    $selectedSlot = null;
    foreach (parking_slots() as $slot) {
        if ((int) $slot['id'] === $slotId) {
            $selectedSlot = $slot;
            break;
        }
    }

    if (!$selectedSlot || $selectedSlot['status'] !== 'available') {
        throw new RuntimeException('That parking slot is no longer available.');
    }

    $_SESSION['history'][] = [
        'id' => next_session_id($_SESSION['history'] ?? []),
        'vehicle_id' => $vehicleId,
        'plate_number' => $vehicle['plate_number'],
        'slot_id' => $slotId,
        'slot_code' => $selectedSlot['slot_code'],
        'hourly_rate' => $selectedSlot['hourly_rate'],
        'entry_time' => date('Y-m-d H:i:s'),
        'exit_time' => null,
        'duration_minutes' => null,
        'fee' => null,
        'paid_amount' => null,
        'payment_status' => 'pending',
        'checkout_requested_at' => null,
        'receipt_seen_at' => null,
    ];

    set_flash('success', 'Park in recorded.');
}

// Completes checkout for an active parking record.
function handle_request_checkout(): void
{
    $historyId = isset($_POST['history_id']) ? (int) $_POST['history_id'] : 0;

    foreach ($_SESSION['history'] as &$row) {
        if ((int) $row['id'] !== $historyId || !empty($row['exit_time'])) {
            continue;
        }

        $row['checkout_requested_at'] = ($row['checkout_requested_at'] ?? null) ?: date('Y-m-d H:i:s');
        complete_checkout_record($row);
        set_flash('success', 'Checkout complete. The vehicle can park again.');
        return;
    }

    throw new RuntimeException('Active parking record not found.');
}

// Closes the receipt modal for paid records.
function handle_receipt_seen(): void
{
    $historyId = isset($_POST['history_id']) ? (int) $_POST['history_id'] : 0;

    foreach ($_SESSION['history'] as &$row) {
        if ((int) $row['id'] === $historyId) {
            $row['receipt_seen_at'] = date('Y-m-d H:i:s');
            set_flash('success', 'Receipt closed.');
            return;
        }
    }
}

// Updates the account profile and license attachments.
function handle_settings(): void
{
    $name = input_text($_POST, 'full_name');
    $email = strtolower(input_text($_POST, 'email'));
    $phone = input_text($_POST, 'phone');
    $address = input_text($_POST, 'address');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || $address === '') {
        throw new RuntimeException('Complete all profile fields.');
    }

    $_SESSION['account']['full_name'] = $name;
    $_SESSION['account']['email'] = $email;
    $_SESSION['account']['phone'] = $phone;
    $_SESSION['account']['address'] = $address;
    $_SESSION['user']['name'] = $name;

    $licenseFiles = upload_documents(
        'license_images',
        count($_SESSION['account_attachments'] ?? []),
        2
    );

    if ($licenseFiles) {
        $_SESSION['account']['license_image'] = $licenseFiles[0]['stored_name'];
        $_SESSION['account_attachments'] = array_merge(
            $_SESSION['account_attachments'] ?? [],
            $licenseFiles
        );
    }

    $profileImage = upload_profile_image('profile_image');
    if ($profileImage !== null) {
        $_SESSION['account']['profile_image'] = $profileImage;
    }

    set_flash('success', 'Account settings updated.');
}

function handle_delete_account(): void
{
    session_destroy();
    go('login.php');
}

$pages = ['dashboard','vehicles','slots','park','history','settings'];
$page = input_choice($_GET, 'page', $pages, 'dashboard');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        check_csrf();
        $action = input_text($_POST, 'action');

        if ($action === 'add_vehicle') {
            handle_add_vehicle();
        } elseif ($action === 'park_in') {
            handle_park_in();
        } elseif ($action === 'request_checkout') {
            handle_request_checkout();
        } elseif ($action === 'receipt_seen') {
            handle_receipt_seen();
        } elseif ($action === 'settings') {
            handle_settings();
        } elseif ($action === 'delete_account') {
            handle_delete_account();
        } else {
            throw new RuntimeException('Invalid action.');
        }
    } catch (Throwable $exception) {
        set_flash('error', $exception->getMessage());
    }

    go('client.php?page=' . urlencode($page));
}

$account = $_SESSION['account'];
$userId = 1;
$vehicles = $_SESSION['vehicles'] ?? [];
$accountAttachments = $_SESSION['account_attachments'] ?? [];
$vehicleAttachments = $_SESSION['vehicle_attachments'] ?? [];
$hasLicense = client_has_license();
$history = $_SESSION['history'] ?? [];
$active = array_values(array_filter($history, fn($row) => empty($row['exit_time'])));

foreach ($history as &$historyRow) {
    $end = $historyRow['exit_time'] ? strtotime($historyRow['exit_time']) : time();
    $minutes = max(1, (int) ceil(($end - strtotime($historyRow['entry_time'])) / 60));
    $hours = max(1, (int) ceil($minutes / 60));
    $historyRow['duration_minutes'] = $historyRow['duration_minutes'] ?? $minutes;
    $historyRow['hourly_rate'] = parking_charge($historyRow['entry_time'], $historyRow['exit_time']) / $hours;
}
unset($historyRow);

$receipt = null;
foreach ($history as $row) {
    if (!empty($row['exit_time']) && $row['payment_status'] === 'paid' && empty($row['receipt_seen_at'])) {
        $receipt = $row;
        break;
    }
}

$level = input_choice($_GET, 'level', ['1','2','3'], '1');
$category = input_choice($_GET, 'category', ['A','B','C'], 'A');
$parkStep = input_choice($_GET, 'step', ['start','slots'], 'start');
$filteredSlots = array_values(
    array_filter(
        parking_slots(),
        fn($slot) => str_starts_with($slot['slot_code'], 'L' . $level . '-' . $category)
    )
);
$parkingRates = parking_rates();
$flash = take_flash();
$titles = [
    'dashboard' => 'Dashboard',
    'vehicles' => 'Add Vehicle',
    'slots' => 'Parking Slots',
    'park' => 'Park In',
    'history' => 'Parking History',
    'settings' => 'Settings',
];
?>


<html>
<head>
    <title><?= $titles[$page] ?> | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="site.css?v=<?= filemtime(__DIR__ . '/site.css') ?>">
    <link rel="stylesheet" href="client.css?v=<?= filemtime(__DIR__ . '/client.css') ?>">
    <link rel="stylesheet" href="park.css?v=<?= filemtime(__DIR__ . '/park.css') ?>">
    <link rel="stylesheet" href="settings.css?v=<?= filemtime(__DIR__ . '/settings.css') ?>">
</head>
<body>
<div class="client-shell">
    <?php
    $menuItems = [
        'dashboard' => ['&#9632;', 'Dashboard'],
        'vehicles' => ['+', 'Add Vehicle'],
        'slots' => ['&#9638;', 'Parking Slots'],
        'park' => ['P', 'Park In'],
        'history' => ['H', 'History'],
        'settings' => ['&#9881;', 'Settings'],
    ];
    $clientName = $_SESSION['user']['name'] ?? 'Client';
    $clientFirstName = explode(' ', trim($clientName))[0] ?: 'Client';
    $profileImage = $_SESSION['account']['profile_image'] ?? null;
    $profileImagePath = $profileImage ? 'uploads/' . basename((string) $profileImage) : '';
    ?>
    <aside class="client-sidebar" id="clientSidebar">
        <div class="sidebar-heading">
            <a class="brand" href="client.php">
                <img src="assets/logo.png" alt="">
                <span><?= APP_NAME ?></span>
            </a>
        </div>

        <div class="client-profile">
            <span class="profile-initial">
                <?php if ($profileImagePath && is_file(__DIR__ . '/' . $profileImagePath)) { ?>
                    <img src="<?= e($profileImagePath) ?>" alt="">
                <?php } else { ?>
                    <?= e(strtoupper(substr($clientName, 0, 1))) ?>
                <?php } ?>
            </span>
            <div>
                <strong><?= e($clientName) ?></strong>
            </div>
        </div>

        <nav>
            <?php foreach ($menuItems as $key => $item) { ?>
                <a class="<?= $page === $key ? 'active' : '' ?>" href="?page=<?= $key ?>">
                    <span><?= $item[0] ?></span><?= $item[1] ?>
                </a>
            <?php } ?>
        </nav>

        <a class="client-logout" href="logout.php">Log Out</a>
    </aside>

    <main class="client-main">
        <header class="client-top">
            <div class="client-title-row">
                <div>
                    <?php if ($page === 'dashboard') { ?>
                        <p class="welcome-text">Welcome, <?= e($clientFirstName) ?></p>
                    <?php } ?>
                    <h1><?= e($titles[$page]) ?></h1>
                </div>
            </div>
        </header>

        <?php if ($flash) { ?>
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php } ?>

        <?php include __DIR__ . '/sidebar_client/' . $page . '.php'; ?>

        <?php if ($receipt) { ?>
            <div class="receipt-backdrop">
                <section class="receipt-modal">
                    <p class="eyebrow">PAYMENT RECEIPT</p>
                    <h2>Checkout Complete</h2>
                    <dl>
                        <div>
                            <dt>Vehicle</dt>
                            <dd><?= e($receipt['plate_number']) ?></dd>
                        </div>
                        <div>
                            <dt>Slot</dt>
                            <dd><?= e($receipt['slot_code']) ?></dd>
                        </div>
                        <div>
                            <dt>Time In</dt>
                            <dd><?= e(date('M d, Y h:i A', strtotime($receipt['entry_time']))) ?></dd>
                        </div>
                        <div>
                            <dt>Time Out</dt>
                            <dd><?= e(date('M d, Y h:i A', strtotime($receipt['exit_time']))) ?></dd>
                        </div>
                        <div>
                            <dt>Duration</dt>
                            <dd><?= (int) $receipt['duration_minutes'] ?> min</dd>
                        </div>
                        <div>
                            <dt>Total</dt>
                            <dd>PHP <?= number_format((float) $receipt['fee'], 2) ?></dd>
                        </div>
                        <div>
                            <dt>Paid</dt>
                            <dd>PHP <?= number_format((float) $receipt['paid_amount'], 2) ?></dd>
                        </div>
                    </dl>
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrf() ?>">
                        <input type="hidden" name="action" value="receipt_seen">
                        <input type="hidden" name="history_id" value="<?= (int) $receipt['id'] ?>">
                        <button class="btn btn-primary">Close Receipt</button>
                    </form>
                </section>
            </div>
        <?php } ?>
    </main>
</div>
</body>
</html>
