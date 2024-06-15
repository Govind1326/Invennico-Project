<?php
error_reporting(0);
require_once './database.php';
session_start();
try {
// Check if user is logged in
    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    } elseif (isset($_SESSION['email'])) {
        if ($_SESSION['role'] == 'Admin') {
            header('Location: adminpage.php');
            exit();
        }
    }
    $emailuser = $_SESSION['email'];
    $query = "SELECT * FROM user WHERE email='$emailuser' or id='" . $_SESSION['id'] . "'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if (!$user) {
        echo "Error fetching user data";
        exit();
    }

// Logout Button
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    } elseif (isset($_POST['editUser'])) {
        $_SESSION['adminId'] = $user['id'];
        header("Location: Register.php");
        exit();
    }
} catch (Exception $ex) {
    echo $ex;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Dashboard</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h2 class="mb-4 mt-4" style="text-align: center">User Dashboard</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="?logout=true" class="btn btn-danger">Logout</a>
                </div>
                <div>
                    <form method="post">
                        <button class="btn btn-primary" name="editUser" type="submit">Edit Details</button>
                    </form>
                </div>
            </div>
            <table class="table table-bordered table-hover">
                <tr>
                    <th>First Name</th>
                    <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo htmlspecialchars($user['address']); ?></td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td><?php echo htmlspecialchars($user['dob']); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                </tr>
            </table>
        </div>
    </body>
</html>
