<?php 
session_start();
include 'db.php';

$form_values = $_SESSION['form_values'] ?? [
    'name' => '',
    'email' => '',
    'phone' => ''
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>➕ Add New User</h2>
    <div class="table-container">
        <form method="POST" action="store.php" enctype="multipart/form-data">
            <label>Profile Image:</label><br>
            <input type="file" name="image" accept="image/*"><br><br>
            <label>Name:</label><br>
            <input type="text" name="name" value="<?= $form_values['name'] ?>" required><br><br>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?= $form_values['email'] ?>" required><br><br>
            <label>Phone:</label><br>
            <input type="text" name="phone" value="<?= $form_values['phone'] ?>"><br><br>
            <div class="form-actions">
                <button onclick="window.location.href='admin_dashboard.php'" class="button" style="background: linear-gradient(135deg, #00c6ff, #0072ff)">Go Back</button>
                <button class="button" type="submit">Save</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php 
// Clear form values after displaying them
if (isset($_SESSION['form_values'])) {
    unset($_SESSION['form_values']);
}
?>