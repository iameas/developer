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

// Check if the user already has an image set
if (!empty($row['image'])) {
    header('Location: customer/');
    exit();
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $uploadDir = 'customer/src/img/uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        // Check file size
        if ($_FILES['image']['size'] > 5000000) { // 5MB limit
            $message[] = "Sorry, your file is too large.";
        } else {
            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                $message[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                // Check if file already exists
                if (file_exists($uploadFile)) {
                    $message[] = "Sorry, file already exists.";
                } else {
                    // Attempt to move uploaded file
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        // Update the user's image column in the database
                        $stmt = $conn->prepare("UPDATE `t_users` SET `image` = ? WHERE `id` = ?");
                        $stmt->bind_param("si", $uploadFile, $user_id);
                        if ($stmt->execute()) {
                            $message[] = "Profile picture uploaded successfully.";

                            // Redirect to customer folder
                            header('Location: customer/');
                            exit();
                        } else {
                            $message[] = "Failed to update profile picture.";
                        }
                    } else {
                        $message[] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        }
    } else {
        $message[] = "File is not an image.";
    }
}

$email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); // Escape output to prevent XSS
$username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); // Escape output to prevent XSS
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Profile Picture - <?php echo htmlspecialchars($email); ?> - Drauig</title>
    <link rel="shortcut icon" href="app/img/logo.png" type="image/x-icon">
    <!-- Additional CSS or meta tags as needed -->
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary bg-body-tertiar">
    <style>
        .bg-body-tertiar {
            background-color: #f6f4f9;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23340086' fill-opacity='0.06'%3E%3Cpath opacity='.5' d='M96 95h4v1h-4v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4h-9v4h-1v-4H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15v-9H0v-1h15V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h9V0h1v15h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9h4v1h-4v9zm-1 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm9-10v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-10 0v-9h-9v9h9zm-9-10h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9zm10 0h9v-9h-9v9z'/%3E%3Cpath d='M6 5V0H5v5H0v1h5v94h1V6h94V5H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    <main class="form-signin w-100 m-auto">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            enctype="multipart/form-data">
            <div class="form-group mb-3 text-left">
                <span class="img-div">
                    <img style="border-radius: 100px;"
                        src="<?php echo !empty($row['image']) ? $row['image'] : 'app/img/accts.png'; ?>" width="110"
                        onClick="triggerClick()" id="profileDisplay">
                </span>
                <input type="file" required onChange="displayImage(this)" id="profileImage" name="image"
                    class="form-control" style="display: none;">
            </div>
            <h1 style="margin-top: 20px;" class="h5 mb-3 fw-normal">Hey @<?php echo $username; ?><br><span
                    style="font-size:30px;"><strong>Add a profile picture</strong></span></h1>
            <?php
            if (isset($message)) {
                foreach ($message as $msg) {
                    echo '<div style="border-radius:5px;font-size:14px;background:red;color:#fff;" class="message mb-2"><center>' . $msg . '</center></div>';
                }
            }
            ?>
            <div class="form-floating my-3">
                <a style="font-weight:400;text-decoration:none;font-size:13px;">
                    Click on the emoji above to add a profile picture.
                </a>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit" name="submit">Continue</button>

        </form>
    </main>
    <script>
        function triggerClick() {
            document.querySelector('#profileImage').click();
        }

        function displayImage(e) {
            if (e.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('#profileDisplay').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(e.files[0]);
            }
        }
    </script>
</body>