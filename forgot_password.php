<?php
require_once 'config.php';
require_once __DIR__ . '/vendor/autoload.php'; // PHPMailer autoload
$message = '';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (empty($email)) {
        $message = 'Please enter your email.';
    } else {
        $connection = getDBConnection();
        $stmt = $connection->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Generar token seguro y guardar en password_resets
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt = $connection->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$user['id'], $token, $expires]);

            // Configurar PHPMailer para Outlook
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'dframirez4706@outlook.com'; // <-- Cambia esto
                $mail->Password = '100ABC345GHD++'; // <-- Cambia esto
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('dframirez4706@outlook.com', 'Simple Todo App');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/reset_password.php?token=' . $token;
                $mail->Body = 'Click the following link to reset your password: <a href="' . $resetLink . '">Reset Password</a><br>This link will expire in 1 hour.';

                $mail->send();
                $message = 'A password reset link has been sent to your email.';
            } catch (Exception $e) {
                $message = 'Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $message = 'No user found with that email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        .message { margin-bottom: 15px; color: #333; }
    </style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required style="width:100%;padding:8px;margin:10px 0;">
        <button type="submit">Send Reset Link</button>
    </form>
    <div style="margin-top:10px;"><a href="login.php">Back to Login</a></div>
</div>
</body>
</html>
