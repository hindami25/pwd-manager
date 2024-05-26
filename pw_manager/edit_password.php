<?php
require 'db.php';
require 'functions.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['master_key'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$password = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$password) {
    echo "Password not found.";
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $website = $_POST['website'];
    $username = $_POST['username'];
    $password_encrypted = encrypt_password($_POST['password'], $_SESSION['master_key']);
    
    $stmt = $pdo->prepare("UPDATE passwords SET website = ?, username = ?, password_encrypted = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$website, $username, $password_encrypted, $id, $user_id])) {
        header("Location: dashboard.php?edit_success=1");
        exit();
    } else {
        $error = "Failed to update password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mt-5">Edit Password</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="post" class="mt-3">
                <div class="form-group">
                    <label for="website">Website/App</label>
                    <input type="text" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($password['website']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($password['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="text" class="form-control" id="password" name="password" value="<?php echo decrypt_password($password['password_encrypted'], $_SESSION['master_key']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update</button>
            </form>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
