<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Connect to the database
    $conn = mysqli_connect('localhost', 'root', '', 'drauigpay') or die('connection failed');

    // Get the user's details
    $user_id = $_SESSION['user_id'];
    $select = mysqli_query($conn, "SELECT * FROM `t_users` WHERE id = '$user_id'") or die('query failed');
    $row = mysqli_fetch_assoc($select);

    // Check if the user needs to onboard
    if (empty($row['image'])) {
        header('Location: onboard.php');
        exit();
    }

    // Redirect based on the user's role
    switch ($row['role']) {
        case 'customer':
            header('Location: customer/');
            break;
        default:
            header('Location: wws.php');
            break;
    }
    exit();
}

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/config.php';

$conn = mysqli_connect('localhost', 'root', '', 'drauigpay') or die('connection failed');

if (isset($_POST['submit'])) {
    $login = mysqli_real_escape_string($conn, $_POST['login']); // Assuming the user enters either email or username
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    // Query to check if the login input matches either email or username
    $select = mysqli_query($conn, "SELECT * FROM `t_users` WHERE (email = '$login' OR username = '$login') AND password = '$pass'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);
        $_SESSION['user_id'] = $row['id'];

        // Check if the user needs to onboard
        if (empty($row['image'])) {
            header('Location: onboard.php');
            exit();
        }

        // Redirect based on the user's role
        switch ($row['role']) {
            case 'customer':
                header('Location: customer/');
                break;
            default:
                header('Location: wws.php');
                break;
        }
        exit();
    } else {
        $message[] = 'Incorrect email/username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drauig Account</title>
    <link rel="shortcut icon" href="app/img/logo.png" type="image/x-icon">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary bg-body-tertiar">
    <style>
        .bg-body-tertiar {
            background-color: #f6f4f9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23340086' fill-opacity='0.06'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    <main class="form-signin w-100 m-auto">
        <form action="" method="post">
            <a href="#"><img width="50" src="app/img/logo.png" alt="drauig logo" class="mb-3"></a>
            <h1 style="margin-top: 20px;" class="h5 mb-3 fw-normal">Sign in to your<br><span
                    style="font-size:30px;"><strong>Drauig LMS account</strong></span></h1>
            <?php
            if (isset($message)) {
                foreach ($message as $message) {
                    echo '<div style="border-radius:5px;font-size:14px;background:red;color:#fff;" class="message mb-2"><center>' . $message . '</center></div>';
                }
            }
            ?>
            <div class="form-floating mb-2">
                <input type="tel" class="form-control" name="login" placeholder="Credentials">
                <label for="floatingInput">Mobile number</label>
            </div>
            <div class="form-floating">
                <input style="border-radius:5px;" auto-complete="off" type="password" class="form-control"
                    name="password" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>

            <div style="font-size:14px;" class="form-floating my-3">
                Forgot credentials <a style="text-decoration:none;" href="reset-auth.php">
                    Reset password
                </a>
            </div>
            <button class="btn btn-primary w-100 py-2" value="Login" type="submit" name="submit">Sign in</button>

            <div class="form-floating my-3" style="font-size:14px;">
                Don't have an account?
                <a style="font-weight:500;text-decoration:none;font-size:13px;" href="wws-register.php">
                    Sign up
                </a>
            </div>

        </form>
    </main>