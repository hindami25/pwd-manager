<?php
require 'db.php';
require 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$generated_password = generate_password();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mt-5">Generated Password</h2>
            <p class="text-center"><?php echo $generated_password; ?></p>
            <div class="text-center">
                <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $generated_password; ?>')">Copy</button>
            </div>
            <script>
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        alert('Password copied to clipboard');
                    }, function(err) {
                        alert('Could not copy text: ', err);
                    });
                }
            </script>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
