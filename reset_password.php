<?php
require_once 'config.php';
$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    die('Invalid or missing reset token.');
}
$connection = getDBConnection();
$stmt = $connection->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = ?');
$stmt->execute([$token]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$reset) {
    die('Invalid or expired reset link.');
}
if (strtotime($reset['expires_at']) < time()) {
    die('Reset link has expired.');
}
$user_id = $reset['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    if (strlen($new_password) < 6) {
        $message = 'Password must be at least 6 characters.';
    } else {
        $passwordHash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $connection->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$passwordHash, $user_id]);
        // Eliminar el token usado
        $stmt = $connection->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->execute([$token]);
        $message = 'Password updated! <a href="login.php">Login now</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        .message { margin-bottom: 15px; color: #333; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required style="width:100%;padding:8px;margin:10px 0;">
        <button type="submit">Update Password</button>
    </form>
    <div style="margin-top:10px;"><a href="login.php">Back to Login</a></div>
</div>
</body>
</html>
