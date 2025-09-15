<?php
require_once '../config/db.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate reset token and expiry
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?")
                ->execute([$token, $expires, $user['id']]);

            // Build reset link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=$token";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'kylejzarahan@gmail.com'; // Use your Gmail address here
                $mail->Password = 'bhjg emio uzcd yqrc';    // Use your app password here
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('kylejzarahan@gmail.com', 'Teyvat Archives Support');
                $mail->addAddress($email, $user['username']);

                $mail->Subject = 'Password Reset Instructions';
                $mail->Body = "Hello {$user['username']},\n\n"
                    . "We received a request to reset your password.\n"
                    . "To reset your password, click the link below (valid for 1 hour):\n$reset_link\n\n"
                    . "If you did not request this, you can ignore this email.\n\n"
                    . "Best regards,\nTeyvat Archives";

                $mail->send();
                $msg = "If we found your email, instructions were sent.";
            } catch (Exception $e) {
                $msg = "Sorry, could not send email. Mailer error: " . htmlspecialchars($mail->ErrorInfo);
            }
        } else {
            // Always show generic message for security
            $msg = "If we found your email, instructions were sent.";
        }
    } else {
        $msg = "Please enter your email address.";
    }
}
?>
<link rel="stylesheet" href="css/forgot.css">
<div class="forgot-container">
    <h2>Forgot Username or Password?</h2>
    <?php if ($msg): ?><div class="success"><?= $msg ?></div><?php endif; ?>
    <form class="forgot-form" method="post">
        <label><strong>Email:</strong>
            <input type="email" name="email" required>
        </label>
        <button type="submit" class="forgot-btn">Send Instructions</button>
    </form>
    <p><a href="user_login.php">&larr; Back to login</a></p>
</div>
<?php require_once '../includes/footer.php'; ?>