<title>Reset Credentials</title>

<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Connect to the database
    $conn = mysqli_connect('localhost', 'root', '', 'rename') or die('connection failed');

    // Get the user's details
    $user_id = $_SESSION['user_id'];
    $select = mysqli_query($conn, "SELECT * FROM `t_users` WHERE id = '$user_id'") or die('query failed');
    $row = mysqli_fetch_assoc($select);

    // Redirect based on the user's role
    if ($row['role'] == 'admin') {
        header('Location: admin');
    } else {
        header('Location: account');
    }
    exit();
}

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/config.php';

?>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">

    <main class="form-signin w-100 m-auto">
        <form action="" method="post">

            <a href="index.php"><img width="50" src="app/img/logo.png" alt="drauig logo" class="mb-3"></a>
            <h1 style="margin-top: 20px;" class="h5 mb-3 fw-normal">Hey there,<br><span
                    style="font-size:30px;"><strong>Forgot Credentials?</strong></span></h1>
            <?php
            if (isset($message)) {
                foreach ($message as $message) {
                    echo '<div style="border-radius:5px;font-size:14px;background:red;color:#fff;" class="message mb-2"><center>' . $message . '</center></div>';
                }
            }
            ?>
            <div class="form-floating mb-2">
                <input type="text" typeof="number" maxlength="11" class="form-control" name="login"
                    placeholder="Credentials">
                <label for="floatingInput">Mobile number</label>
            </div>
            <div class="form-floating my-3" style="font-size:13px;">
                A link with new credentials to log into your account will be sent to the registered email
                address
            </div>

            <button class="btn btn-primary w-100 py-2" value="Login" type="submit" name="submit">Reset
                Credentials</button>

            <div class="form-floating my-3" style="font-size:13px;">
                Remember credentials <a href="wws.php">Sign in</a>
                </label>
            </div>
        </form>
    </main>