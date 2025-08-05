<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .default-avatar {
            width: 50px;
            height: 50px;
            background: #555;
            border-radius: 5px;
            display: inline-block;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .user-info {
            font-size: 16px;
        }
        .table-container {
            margin: 0 15px;
        }
        .button {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 0 5px;
        }
        .button:hover {
            background: #0069d9;
        }
        .add-new {
            background: #28a745;
        }
        .add-new:hover {
            background: #218838;
        }
        .actions {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h2>👑 Admin Dashboard</h2>
        <div class="user-info">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
            <?php if ($_SESSION['is_super_admin']): ?>
                (Super Admin)
            <?php endif; ?>
            | <a href="logout.php" class="button">Logout</a>
        </div>
    </div>
    
    <h2>📋 User List</h2>    
    <div class="table-container">
        <table id="usersTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <div style="text-align: center;">
        <a class="button add-new" href="create.php">+ Add New User</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            serverSide: true,
            ajax: {
                url: 'server_processing.php',
                type: 'POST'
            },
            columns: [
                { data: 'id' },
                { 
                    data: 'image',
                    render: function(data) {
                        return data ? 
                            `<img src="uploads/${data}" width="50" style="border-radius:5px">` : 
                            '<div class="default-avatar"></div>';
                    },
                    orderable: false
                },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { 
                    data: 'id',
                    render: function(data) {
                        return `<div class="actions">
                            <a class="button" href="edit.php?id=${data}">Edit</a>
                            <a class="button" href="delete.php?id=${data}" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>`;
                    },
                    orderable: false
                }
            ]
        });
    });
    </script>
</body>
</html>