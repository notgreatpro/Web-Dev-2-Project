<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Feedback message
$feedback_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Simple validation
    if ($name && $email && $message) {
        // Send feedback via email
        $to = 'info@genshinexplorer.com';
        $subject = 'Feedback from ' . $name;
        $body = "Name: $name\nEmail: $email\n\n$message";
        $headers = "From: $email\r\nReply-To: $email\r\n";
        if (mail($to, $subject, $body, $headers)) {
            $feedback_msg = "Thank you for your feedback, $name!";
        } else {
            $feedback_msg = "Sorry, we couldn't send your feedback. Please try again later.";
        }
    } else {
        $feedback_msg = "Please fill out all fields.";
    }
}
?>

<div class="container" style="max-width:700px; margin-top:40px;">
    <h1>Feedback</h1>
    <p>
        We appreciate your suggestions, questions, and bug reports. Fill out the form below, and we'll get back to you as soon as possible.
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