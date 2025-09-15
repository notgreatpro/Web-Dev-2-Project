<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Feedback message
$feedback_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Simple validation
    if ($name && $email && $message) {
        $mail = new PHPMailer(true);
        try {
            // Gmail SMTP details:
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kylejzarahan@gmail.com';        
            $mail->Password = 'bhjg emio uzcd yqrc';                 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;       
            $mail->Port = 465;                                     

            // Set sender info
            $mail->setFrom('kylearahan19@gmail.com', 'Kyle Arahan'); 
            $mail->addAddress('genshinexplorerofficial@gmail.com');             

            $mail->Subject = 'Feedback from ' . $name;
            $mail->Body = "Name: $name\nEmail: $email\n\n$message";

            $mail->send();
            $feedback_msg = "Thank you for your feedback, $name! ";
        } catch (Exception $e) {
            $feedback_msg = "Sorry, feedback could not be sent. Mailer error: " . htmlspecialchars($mail->ErrorInfo);
        }
    } else {
        $feedback_msg = "Please fill out all fields.";
    }
}
?>

<div class="container" style="max-width:700px; margin-top:40px;">
    <h1>Feedback</h1>
    <p>
        We appreciate your suggestions, questions, and bug reports. Your feedback will help us improve our website without the hassle.
        Please input the form below and we reply to you as soon as possible!
    </p>
    <?php if ($feedback_msg): ?>
        <div style="color:<?= strpos($feedback_msg, 'Thank') === 0 ? 'green' : 'red' ?>; margin-bottom:1em;">
            <?= htmlspecialchars($feedback_msg) ?>
        </div>
    <?php endif; ?>
    <form method="post" class="feedback-form">
        <label>
            Name:
            <input type="text" name="name" required>
        </label>
        <label>
            Email:
            <input type="email" name="email" required>
        </label>
        <label>
            Message:
            <textarea name="message" rows="6" required></textarea>
        </label>
        <button type="submit">Send Feedback</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>