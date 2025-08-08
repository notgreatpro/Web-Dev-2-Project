<?php
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Handle feedback submission
$feedback_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        // For demo: send email or store feedback in DB (implement as needed)
        $feedback_msg = "Thank you for your feedback, $name!";
        // mail('info@genshinexplorer.com', 'Feedback from '.$name, $message, "From: $email");
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
    <form method="post" class="feedback-form" style="display:flex; flex-direction:column; gap:1.1em;">
        <label>
            Name:
            <input type="text" name="name" required style="padding:10px; border-radius:8px; border:1.5px solid #e4eaf1;">
        </label>
        <label>
            Email:
            <input type="email" name="email" required style="padding:10px; border-radius:8px; border:1.5px solid #e4eaf1;">
        </label>
        <label>
            Message:
            <textarea name="message" rows="6" required style="padding:10px; border-radius:8px; border:1.5px solid #e4eaf1;"></textarea>
        </label>
        <button type="submit" style="background:#23233b; color:#ffe066; border:none; border-radius:8px; padding:12px 30px; font-weight:bold; font-size:1.07em; cursor:pointer;">Send Feedback</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>