<title>Account Registration</title>
<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/config.php';

// Make a connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('connection failed');

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));


    // Check for existing username, email, or phone
    $stmt = $conn->prepare("SELECT * FROM t_users WHERE username = ? OR email = ? OR phone = ?");
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $message[] = 'Username, Email, or Phone number already in use. Please try again with different details.';
    } else {
        // Function to generate random alphanumeric string
        function generateRandomString($length = 36)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

        // Generate random ID
        function generateUniqueID($conn)
        {
            $id = generateRandomString();
            $query = "SELECT drauig_id FROM t_users WHERE drauig_id = '$id'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                return generateUniqueID($conn); // If ID exists, regenerate
            } else {
                return $id;
            }
        }

        $drauig_id = generateUniqueID($conn);

        // Process the phone number to remove the leading zero
        $account_id = ltrim($phone, '0');

        // Insert user data into the database along with generated ID and processed phone number
        $insert = mysqli_query($conn, "INSERT INTO t_users(username, fname, lname, email, phone, password, drauig_id, account_id, is_verified) 
                                        VALUES('$username', '$fname', '$lname', '$email', '$phone', '$pass', '$drauig_id', '$account_id', 0)")
            or die('query failed');

        if ($insert) {

            // Generate verification code and insert into the t_verification_codes table
            $verification_code = generateRandomString(6); // You can adjust the length of the verification code
            $user_id = mysqli_insert_id($conn); // Get the last inserted user ID

            $status = 'not verified'; // Status for pending verification
            $created_at = date('Y-m-d H:i:s');
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Code expires in 1 hour

            $insert_verification = mysqli_query($conn, "INSERT INTO t_verification_codes(user_id, verification_code, status, created_at, expires_at) 
                                                        VALUES('$user_id', '$verification_code', '$status', '$created_at', '$expires_at')")
                or die('query failed');

            if ($insert_verification) {
                // Here you should send the verification code to the user's email or phone
                // ...

                $message[] = 'Account created successfully! Please check your email to verify your account.';
                header('Location: auth.php'); // Redirect to auth.php for verification
                exit();
            } else {
                $message[] = 'Oops! An error occurred while generating the verification code. Please try again later.';
            }
        } else {
            $message[] = 'Oops! An error occurred. Please try again later.';
        }
    }
    $stmt->close();
}
?>

<body class="bg-body-tertiar">
    <div class="container col-xl-10 col-xxl-8 px-4 py-5">
        <div class="row align-items-center g-lg-5 py-5">
            <div class="col-lg-7 text-center text-lg-start">
                <h1 class="display-4 fw-bold lh-1 text-body-emphasis mb-3">Create account</h1>
                <p class="col-lg-10 fs-4">Register now and learn from Drauig LMS bootcamp.</p>
            </div>
            <div class="col-md-10 mx-auto col-lg-5">
                <form action="" method="post" enctype="multipart/form-data"
                    class="p-4 p-md-5 border rounded-3 bg-body-tertiary">

                    <?php
                    if (isset($message)) {
                        foreach ($message as $msg) {
                            echo '<div style="border-radius:5px;font-size:14px;background:#0d6efd;color:#fff;" class="message mb-2"><center>' . $msg . '</center></div>';
                        }
                    }
                    ?>
                    <div class="form-floating mb-3">
                        <input required type="text" class="form-control" name="username">
                        <label for="floatingInput">Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="text" class="form-control" name="fname">
                        <label for="floatingInput">First name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="text" class="form-control" name="lname">
                        <label for="floatingInput">Last name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="email" class="form-control" name="email">
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="phone" class="form-control" name="phone">
                        <label for="floatingInput">Phone number</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="password" class="form-control" name="password">
                        <label for="floatingInput">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input required type="password" class="form-control" name="cpassword">
                        <label for="floatingPassword">Confirm password</label>
                    </div>
                    <button class="mb-3 w-100 btn btn-lg btn-primary" name="submit" value="submit" type="submit">Sign
                        up</button>
                    <div class="form-floating my-3" style="font-size:13px;">
                        Already have an account? <a style="font-weight:500;text-decoration:none;font-size:13px;"
                            href="wws.php">
                            Sign in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .bg-body-tertiar {
            background-color: #f6f4f9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23340086' fill-opacity='0.06'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>

    <style>
        @media (min-width: 992px) {
            .rounded-lg-3 {
                border-radius: .3rem;
            }
        }
    </style>