<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: wws.php');
    exit();
}

// Include configuration file
include 'config.php';
include 'header.php';

// Connect to the database using MySQLi with error handling
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    exit('Database connection failed');
}

// Get the user's details using a prepared statement to prevent SQL injection
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM `t_users` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Check if user is already verified or not a customer
if ($row['is_verified'] == 1 || $row['role'] != 'customer') {
    header('Location: wws.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $verification_code = mysqli_real_escape_string($conn, $_POST['verification_code']);

    // Get the verification code details
    $stmt = $conn->prepare("SELECT * FROM `t_verification_codes` WHERE user_id = ? AND verification_code = ? AND status = 0");
    $stmt->bind_param("is", $user_id, $verification_code);
    $stmt->execute();
    $verification_result = $stmt->get_result();
    $verification_row = $verification_result->fetch_assoc();
    $stmt->close();

    // Check if the verification code is valid and not expired
    if ($verification_row && strtotime($verification_row['expires_at']) > time()) {
        // Update the user's status to verified
        $stmt = $conn->prepare("UPDATE `t_users` SET is_verified = 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Update the verification code status to used
        $stmt = $conn->prepare("UPDATE `t_verification_codes` SET status = 1 WHERE id = ?");
        $stmt->bind_param("i", $verification_row['id']);
        $stmt->execute();
        $stmt->close();

        // Redirect to the onboard page
        header('Location: onboard.php');
        exit();
    } else {
        $message[] = 'Invalid verification code. Please try again!';
    }
}

// Handle resend verification code request
if (isset($_POST['resend'])) {
    // Generate a new verification code
    function generateVerificationCode($length = 6)
    {
        $characters = '0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    $new_verification_code = generateVerificationCode();

    // Insert the new verification code into the database
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $conn->prepare("INSERT INTO `t_verification_codes` (user_id, verification_code, status, created_at, expires_at) VALUES (?, ?, 0, NOW(), ?)");
    $stmt->bind_param("iss", $user_id, $new_verification_code, $expires_at);
    $stmt->execute();
    $stmt->close();

    // Here you should send the new verification code to the user's email or phone
    // ...

    $message[] = 'A code has been sent to your phone.';
}

$email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); // Escape output to prevent XSS
$username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); // Escape output to prevent XSS
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Verify account - ' . htmlspecialchars($email) . ' - Drauig'; ?>
    </title>
    <link rel="shortcut icon" href="app/img/logo.png" type="image/x-icon">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary bg-body-tertiar">
    <style>
        .bg-body-tertiar {
            background-color: #f6f4f9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23340086' fill-opacity='0.06'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    <main class="form-signin w-100 m-auto">
        <form action="" method="post">
            <div class="form-group mb-3 text-left">
                <span class="img-div">
                    <img style="border-radius: 100px;"
                        src="<?php echo !empty($row['image']) ? $row['image'] : 'app/img/accts.png'; ?>" width="110"
                        onClick="triggerClick()" id="profileDisplay">
                </span>
            </div>
            <h1 style="margin-top: 20px;" class="h5 mb-3 fw-normal">Hey, @<?php echo $username; ?><br><span
                    style="font-size:30px;"><strong>Confirm account</strong></span></h1>
            <?php
            if (isset($message)) {
                foreach ($message as $msg) {
                    echo '<div style="border-radius:5px;font-size:14px;background:red;color:#fff;" class="message mb-2"><center>' . $msg . '</center></div>';
                }
            }
            ?>
            <div class="form-floating mb-2">
                <input type="text" class="form-control" name="verification_code" placeholder="Verification Code"
                    required>
                <label for="verification_code">Verification code</label>
            </div>

            <div class="form-floating my-3">
                Could not verify account? <button
                    style="padding:0;text-decoration:underline;background-color:none;border:0;box-shadow:none;"
                    type="submit" name="resend">Resend code</button>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit" name="submit">Continue</button>

        </form>
    </main>
</body>