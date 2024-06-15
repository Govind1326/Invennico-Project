<?php
error_reporting(0);
require_once './database.php';
session_start();
try {
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin') {
        header('Location: login.php');
        exit();
    }
    // Fetch current admin user details
    $emailuser = $_SESSION['email'];
    $role = $_SESSION['role'];
    $query = "SELECT * FROM user WHERE email='$emailuser' or id='" . $_SESSION['id'] . "'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $adminId = $user['id'];
    // Role assignment and status change logic
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['assignRole'])) {
            $userId = $_POST['userId'];
            $newRole = $_POST['role'];

            $updateSql = "UPDATE user SET role='$newRole' WHERE id='$userId'";
            if ($conn->query($updateSql) === TRUE) {
                echo '<script>alert("Role updated successfully");</script>';
            } else {
                echo '<script>alert("Error updating role: ' . $conn->error . '");</script>';
            }
        } elseif (isset($_POST['changeStatus'])) {
            $userId = $_POST['userId'];
            $newStatus = $_POST['status'];

            $updateSql = "UPDATE user SET status='$newStatus' WHERE id='$userId'";
            if ($conn->query($updateSql) === TRUE) {
                echo '<script>alert("Status updated successfully");</script>';
            } else {
                echo '<script>alert("Error updating status: ' . $conn->error . '");</script>';
            }
        } elseif (isset($_POST['deleteUser'])) {
            $userId = $_POST['userId'];
            $deleteSql = "DELETE FROM user WHERE id='$userId'";
            if ($conn->query($deleteSql) === TRUE) {
                echo '<script>alert("User deleted successfully");</script>';
            } else {
                echo '<script>alert("Error deleting user: ' . $conn->error . '");</script>';
            }
        } elseif (isset($_POST['editUser'])) {
            $_SESSION['adminId'] = $adminId;
            header("Location: Register.php");
            exit();
        }
    }
    // Logout Button
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
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
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            body{
                margin: 19px;
            }
            .table td, .table th {
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <header style="text-align: center;color: black;padding: 5px;margin-bottom: 6px">
                <h2 class="mb-4">Admin Dashboard</h2>
                <div class="mb-3">
                    <a href="?logout=true" class="btn btn-danger">Logout</a>
                </div>
            </header>
            <!-- Filter form -->
            <div class="form-row mb-3">
                <div class="col">
                    <select id="roleFilter" class="form-control">
                        <option value="">All Roles</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                        <option value="Customer">Customer</option>
                    </select>
                </div>
                <div class="col">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <table id="userTable" class="table table-striped table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Sn No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Phone no</th>
                        <th>Role</th>
                        <th>Address</th>
                        <th>Date of Birth</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM user";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td>' . $row['firstname'] . '</td>';
                            echo '<td>' . $row['lastname'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['phone'] . '</td>';
                            echo '<td>' . $row['role'] . '</td>';
                            echo '<td>' . $row['address'] . '</td>';
                            echo '<td>' . $row['dob'] . '</td>';
                            echo '<td>' . $row['status'] . '</td>';
                            echo '<td>';
                            if ($role == "Admin") {
                                if ($adminId == $row['id']) {
                                    // Role change form
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<select name="role" class="form-control mb-2">';
                                    echo '<option value="Staff" ' . ($row['role'] == 'Staff' ? 'selected' : '') . '>Staff</option>';
                                    echo '<option value="Customer" ' . ($row['role'] == 'Customer' ? 'selected' : '') . '>Customer</option>';
                                    echo '<option value="Admin" ' . ($row['role'] == 'Admin' ? 'selected' : '') . '>Admin</option>';
                                    echo '</select>';
                                    echo '<button type="submit" name="assignRole" class="btn btn-sm btn-info">Assign Role</button>';
                                    echo '</form>';
                                    // Status change form
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<select name="status" class="form-control mb-2">';
                                    echo '<option value="Active" ' . ($row['status'] == 'Active' ? 'selected' : '') . '>Active</option>';
                                    echo '<option value="Inactive" ' . ($row['status'] == 'Inactive' ? 'selected' : '') . '>Inactive</option>';
                                    echo '</select>';
                                    echo '<button type="submit" name="changeStatus" class="btn btn-sm btn-warning">Change Status</button>';
                                    echo '</form>';
                                    // Edit button for Admin's own row
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<button type="submit" name="editUser" class="btn btn-sm btn-primary mr-1">Edit</button>';
                                    echo '</form>';
                                } else {
                                    // Role assignment form
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<select name="role" class="form-control mb-2">';
                                    echo '<option value="Staff" ' . ($row['role'] == 'Staff' ? 'selected' : '') . '>Staff</option>';
                                    echo '<option value="Customer" ' . ($row['role'] == 'Customer' ? 'selected' : '') . '>Customer</option>';
                                    echo '<option value="Admin" ' . ($row['role'] == 'Admin' ? 'selected' : '') . '>Admin</option>';
                                    echo '</select>';
                                    echo '<button type="submit" name="assignRole" class="btn btn-sm btn-info">Assign Role</button>';
                                    echo '</form>';
                                    // Status change form
                                    echo '<form method="post">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<select name="status" class="form-control mb-2">';
                                    echo '<option value="Active" ' . ($row['status'] == 'Active' ? 'selected' : '') . '>Active</option>';
                                    echo '<option value="Inactive" ' . ($row['status'] == 'Inactive' ? 'selected' : '') . '>Inactive</option>';
                                    echo '</select>';
                                    echo '<button type="submit" name="changeStatus" class="btn btn-sm btn-warning">Change Status</button>';
                                    echo '</form>';
                                    // Delete user button   
                                    echo '<form method="post" style="display: inline-block;">';
                                    echo '<input type="hidden" name="userId" value="' . $row['id'] . '">';
                                    echo '<button type="submit" name="deleteUser" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this user?\')">Delete</button>';
                                    echo '</form>';
                                }
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="9">No users found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                var table = $('#userTable').DataTable();
                $('#roleFilter, #statusFilter').on('change', function () {
                    var role = $('#roleFilter').val();
                    var status = $('#statusFilter').val();
                    table.column(5).search(role).column(8).search(status).draw();
                });
            });
        </script>
    </body>
</html>
