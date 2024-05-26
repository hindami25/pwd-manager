<?php
require 'db.php';
require 'functions.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['master_key'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ?");
$stmt->execute([$user_id]);
$passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Password copied to clipboard');
            }, function(err) {
                alert('Could not copy text: ', err);
            });
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this password?")) {
                window.location.href = "delete_password.php?id=" + id;
            }
        }

        <?php if (isset($_GET['edit_success'])): ?>
            window.onload = function() {
                alert('Password successfully edited');
            }
        <?php endif; ?>

        <?php if (isset($_GET['delete_success'])): ?>
            window.onload = function() {
                alert('Password successfully deleted');
            }
        <?php endif; ?>
    </script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center mt-5">Password Manager</h2>
            <div class="text-right mb-3">
                <a href="add_password.php" class="btn btn-success">Add Password</a>
                <a href="generate_password.php" class="btn btn-secondary">Generate Password</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Website/App</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($passwords as $password) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($password['website']); ?></td>
                            <td><?php echo htmlspecialchars($password['username']); ?></td>
                            <td><button class="btn btn-primary" onclick="copyToClipboard('<?php echo decrypt_password($password['password_encrypted'], $_SESSION['master_key']); ?>')">Copy</button></td>
                            <td>
                                <a href="edit_password.php?id=<?php echo $password['id']; ?>" class="btn btn-warning">Edit</a>
                                <button class="btn btn-danger" onclick="confirmDelete(<?php echo $password['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
