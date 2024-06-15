<?php
require_once './database.php';
session_start();
try {
    $isUpdate = false;
    if (isset($_SESSION['adminId'])) {
        $isUpdate = true;
        $adminId = $_SESSION['adminId'];
        $query = "SELECT * FROM user WHERE id='$adminId'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
        $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        if (!$isUpdate) {
            $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
            $cpwd = mysqli_real_escape_string($conn, $_POST['cpwd']);
            // Check if passwords match
            if ($pwd !== $cpwd) {
                echo "Error: Passwords do not match.<br>";
                exit();
            }
            // Hash the password
            $hashed_password = password_hash($pwd, PASSWORD_DEFAULT);
        }
        if ($isUpdate) {
            // Retrieve the admin ID from the session
            $adminId = $_SESSION['adminId'];
            // Check if email already exists (excluding current admin)
            $check_email_query = "SELECT * FROM user WHERE email = '$email' AND id != '$adminId'";
            $result_email = mysqli_query($conn, $check_email_query);
            if (mysqli_num_rows($result_email) > 0) {
                echo "Error: Email already registered.<br>";
                exit();
            }
            // Check if phone already exists (excluding current admin)
            $check_phone_query = "SELECT * FROM user WHERE phone = '$phone' AND id != '$adminId'";
            $result_phone = mysqli_query($conn, $check_phone_query);
            if (mysqli_num_rows($result_phone) > 0) {
                echo "Error: Phone number already registered.<br>";
                exit();
            }
            // Update existing user
            $update_sql = "UPDATE user SET firstname='$firstname', lastname='$lastname', email='$email', phone='$phone', address='$address', dob='$dob' WHERE id='$adminId'";
            if (mysqli_query($conn, $update_sql)) {
                echo "Record updated successfully";
                $_SESSION['id']=$adminId;
                header("location: adminpage.php");
                exit();
            } else {
                echo "Error updating record: " . mysqli_error($conn);
                exit();
            }
        } else {
            // Check if email already exists for new registration
            $check_email_query = "SELECT * FROM user WHERE email = '$email'";
            $result_email = mysqli_query($conn, $check_email_query);
            if (mysqli_num_rows($result_email) > 0) {
                echo '<script>alert("Email already registered");</script>';
            } else {
                // Check if phone already exists for new registration
                $check_phone_query = "SELECT * FROM user WHERE phone = '$phone'";
                $result_phone = mysqli_query($conn, $check_phone_query);
                if (mysqli_num_rows($result_phone) > 0) {
                    echo '<script>alert("Phone number already registered");</script>';
                } else {
                    // Insert new user
                    $sql = "INSERT INTO user (firstname, lastname, email, phone, address, dob, password) 
                    VALUES ('$firstname', '$lastname', '$email', '$phone', '$address', '$dob', '$hashed_password')";
                    if (mysqli_query($conn, $sql)) {
                        echo "New record created successfully";
                        header("location: login.php");
                        exit();
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                        exit();
                    }
                }
            }
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
        <title>Register</title>
        <style>
            .form-group {
                position: relative;
                margin-bottom: 1.5rem;
            }
            .form-group label {
                position: absolute;
                top: 0;
                left: 0;
                padding: 0.75rem 0.75rem;
                transition: all 0.2s ease-in-out;
                pointer-events: none;
                color: #aaa;
            }
            .form-group input:focus + label,
            .form-group input:not(:placeholder-shown) + label {
                top: -1rem;
                left: 0.75rem;
                font-size: 0.75rem;
                color: #333;
            }
            .form-group input {
                padding: 1.5rem 0.75rem 0.75rem;
                width: 100%;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .error {
                border-color: red;
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
                        <img src="Assets/images/register.jpg" alt="Image" class="img-fluid">
                    </div>
                    <div class="col-md-6 contents">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h3>Registration</h3>
                                    <p class="mb-4">Please fill out the details below.</p>
                                </div>
                                <form id="registerForm" action="#" method="post">
                                    <div class="form-group first mt-2">
                                        <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Firstname" value="<?php echo isset($user['firstname']) ? $user['firstname'] : ''; ?>">
                                        <span class="error-message" id="firstname-error">First name is required.</span>
                                    </div>
                                    <div class="form-group first mt-2">
                                        <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Lastname" value="<?php echo isset($user['lastname']) ? $user['lastname'] : ''; ?>">
                                        <span class="error-message" id="lastname-error">last name is required.</span>
                                    </div>
                                    <div class="form-group first mt-2">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>">
                                        <span class="error-message" id="email-error">Email already exists.</span>
                                        <span class="error-message" id="email-required-error">Email is required.</span>
                                    </div>
                                    <div class="form-group first mt-2">
                                        <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone no" value="<?php echo isset($user['phone']) ? $user['phone'] : ''; ?>">
                                        <span class="error-message" id="phone-error">Invalid phone number.</span>
                                        <span class="error-message" id="phone-required-error">Phone no is required.</span>
                                    </div>
                                    <div class="form-group first mt-2 mb-2">
                                        <input type="text" name="address" id="address" class="form-control" placeholder="Address" value="<?php echo isset($user['address']) ? $user['address'] : ''; ?>">
                                        <span class="error-message" id="address-error">Address is required.</span>
                                    </div>
                                    <label for="dob">Date of Birth</label>
                                    <div class="form-group first ">
                                        <input type="date" name="dob" id="dob" class="form-control" placeholder="DOB" value="<?php echo isset($user['dob']) ? $user['dob'] : ''; ?>">
                                        <span class="error-message" id="dob-error">Date of Birth is required.</span>
                                    </div>
                                    <?php if (!$isUpdate): ?>
                                        <div class="form-group last mt-2" id="password-field">
                                            <input type="password" name="pwd" id="pwd" class="form-control" placeholder="Password">
                                            <span class="error-message" id="pwd-required-error">Password is required.</span>
                                            <span class="error-message" id="pwd-pattern-error">Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character.</span>
                                        </div>
                                        <div class="form-group first mt-2" id="confirm-password-field">
                                            <input type="password" name="cpwd" id="cpwd" class="form-control" placeholder="Confirm password">
                                            <span class="error-message" id="password-error">Passwords do not match.</span>
                                            <span class="error-message" id="cpwd-required-error">Confirm Password is required.</span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="submit" value="Proceed" class="btn btn-block btn-primary mb-4 mt-2">
                                </form>
                                <span class="ml-auto"><a href="index.php" class="forgot-pass">Home Page</a></span> <br> 
                                <span class="ml-auto"><a href="login.php" class="forgot-pass">Go to Login</a></span> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="Assets/js/jquery-3.3.1.min.js"></script>
        <script src="Assets/js/popper.min.js"></script>
        <script src="Assets/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#registerForm').on('submit', function (e) {
                    e.preventDefault();
                    $('.form-control').removeClass('error');
                    $('.error-message').hide();
                    var valid = true;
                    // Validate required fields
                    $('input[type="text"], input[type="email"], input[type="number"], input[type="date"]').each(function () {
                        if ($(this).val() === '') {
                            $(this).addClass('error');
                            $('#' + $(this).attr('id') + '-error').show();
                            valid = false;
                        }
                    });
                    // Validate email
                    var email = $('#email').val();
                    $.ajax({
                        url: 'check_email.php', // Server-side script to check email existence
                        type: 'POST',
                        data: {email: email},
                        async: false, // Make the request synchronous
                        success: function (response) {
                            if (response === 'exists') {
                                $('#email').addClass('error');
                                $('#email-error').show();
                                valid = false;
                            }
                        }
                    });
                    // Validate phone number (simple example)
                    var phone = $('#phone').val();
                    if (!phone.match(/^\d{10}$/)) {
                        $('#phone').addClass('error');
                        $('#phone-error').show();
                        valid = false;
                    }
<?php if (!$isUpdate): ?>
                        // Validate password match
                        var pwd = $('#pwd').val();
                        var cpwd = $('#cpwd').val();
                        if (pwd !== cpwd) {
                            $('#pwd').addClass('error');
                            $('#cpwd').addClass('error');
                            $('#password-error').show();
                            valid = false;
                        }
                        // Check if passwords are empty
                        if (pwd === '') {
                            $('#pwd').addClass('error');
                            $('#pwd-required-error').show();
                            valid = false;
                        }
                        if (cpwd === '') {
                            $('#cpwd').addClass('error');
                            $('#cpwd-required-error').show();
                            valid = false;
                        }
                        // Check password pattern
                        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                        if (!pwd.match(passwordPattern)) {
                            $('#pwd').addClass('error');
                            $('#pwd-pattern-error').show();
                            valid = false;
                        }
<?php endif; ?>
                    // If valid, submit the form
                    if (valid) {
                        this.submit();
                    }
                });
                // Floating label logic
                $('.form-control').on('focus', function () {
                    $(this).next('label').addClass('active');
                }).on('blur', function () {
                    if ($(this).val() === '') {
                        $(this).next('label').removeClass('active');
                    }
                });
                // Pre-fill logic for already filled fields
                $('.form-control').each(function () {
                    if ($(this).val() !== '') {
                        $(this).next('label').addClass('active');
                    }
                });
            });
        </script>
    </body>
</html>