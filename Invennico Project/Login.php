<?php
require_once './database.php';
session_start();
try {
    if (isset($_SESSION['email'])) {
        if ($_SESSION['role'] == 'admin') {
            header('Location: adminpage.php');
            exit();
        } else {
            header('Location: userpage.php');
            exit();
        }
    }
    $error_message = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
        $user_exist = "SELECT * FROM user WHERE email = '$email'";
        $result_email = mysqli_query($conn, $user_exist);
        if (mysqli_num_rows($result_email) > 0) {
            $user = mysqli_fetch_array($result_email, MYSQLI_ASSOC);
            if (password_verify($pwd, $user['password'])) {
                $_SESSION['email'] = $email;
                if ($user['role'] == 'Admin') {
                    $_SESSION['role'] = $user['role'];
                    header('Location: adminpage.php');
                    exit();
                } else {
                    $_SESSION['role'] = "others";
                    header('Location: userpage.php');
                    exit();
                }
            } else {
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            $error_message = "User does not exist.";
        }
        mysqli_close($conn);
    }
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    die();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="Assets/fonts/icomoon/style.css">
        <link rel="stylesheet" href="Assets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="Assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="Assets/css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>Login</title>
        <style>
            .error-input {
                border-color: red !important;
            }
            .error-message {
                color: red;
                font-size: 0.875em;
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <img src="Assets/images/login.jpg" alt="Image" class="img-fluid">
                    </div>
                    <div class="col-md-6 contents">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h3>Log In</h3>
                                    <p class="mb-4">Please use your credentials.</p>
                                    <?php if ($error_message): ?>
                                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                    <?php endif; ?>
                                </div>
                                <form id="login-form" action="#" method="post">
                                    <div class="form-group first">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" name="email">
                                        <div class="error-message"></div>
                                    </div>
                                    <div class="form-group last mb-4">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" name="pwd">
                                        <div class="error-message"></div>
                                    </div>
                                    <input type="submit" value="Log In" class="btn btn-block btn-primary">
                                </form>
                                <span class="ml-auto"><a href="index.php" class="forgot-pass">Home Page</a></span> <br>
                                <span class="ml-auto"><a href="Register.php" class="forgot-pass">Go to Registration</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('#login-form').on('submit', function (e) {
                    // Remove previous error indications
                    $('.form-control').removeClass('error-input');
                    $('.error-message').hide();
                    // Check if email and password fields are empty
                    var email = $('input[name="email"]').val();
                    var password = $('input[name="pwd"]').val();
                    var isValid = true;
                    if (email.trim() === '') {
                        $('input[name="email"]').addClass('error-input');
                        $('input[name="email"]').siblings('.error-message').text('Email is required.').show();
                        isValid = false;
                    }
                    if (password.trim() === '') {
                        $('input[name="pwd"]').addClass('error-input');
                        $('input[name="pwd"]').siblings('.error-message').text('Password is required.').show();
                        isValid = false;
                    }
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
                // Floating label logic
                $('.form-control').on('focus', function () {
                    $(this).siblings('label').addClass('active');
                }).on('blur', function () {
                    if ($(this).val() === '') {
                        $(this).siblings('label').removeClass('active');
                    }
                });
            });
        </script>
        <script src="Assets/js/popper.min.js"></script>
        <script src="Assets/js/bootstrap.min.js"></script>
        <script src="Assets/js/main.js"></script>
    </body>
</html>
