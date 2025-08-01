<?php
include 'db.php';
session_start();
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM userrs WHERE id = $id");
$row = $result->fetch_assoc();

// Get stored form values if they exist
$form_values = $_SESSION['form_values'] ?? [
    'name' => $row['name'],
    'email' => $row['email'],
    'phone' => $row['phone']
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>✏️ Edit User</h2>
    <div class="table-container">
        <form method="POST" action="update.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <label>Name:</label><br>
            <input type="text" name="name" value="<?= htmlspecialchars($form_values['name']) ?>" required><br><br>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?= htmlspecialchars($form_values['email']) ?>" required><br><br>
            <label>Phone:</label><br>
            <input type="text" name="phone" value="<?= htmlspecialchars($form_values['phone']) ?>"><br><br>
            
            <label>Profile Image:</label><br>
            <?php if ($row['image']): ?>
                <div style="margin: 10px 0;">
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="100"><br>
                    <label>
                        <input type="checkbox" name="remove_image"> Remove current image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif"><br><br>
            
            <div class="form-actions">
                <a href="read.php" class="button go-back">Go Back</a>
                <button class="button" type="submit">Update</button>
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