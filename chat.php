<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireLogin();
$currentUser = getCurrentUser();
$otherUserId = intval($_GET['user_id'] ?? 0);
$otherUser = getUserById($otherUserId);
$error = '';

if (!$otherUser || $otherUser['id'] === $currentUser['id']) {
    header('Location: messages.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageText = sanitize($_POST['message_text'] ?? '');
    if (empty($messageText)) {
        $error = 'Please type a message before sending.';
    } else {
        sendMessage($currentUser['id'], $otherUser['id'], $messageText);
        header('Location: chat.php?user_id=' . $otherUser['id']);
        exit;
    }
}

$messages = getConversationMessages($currentUser['id'], $otherUser['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - NC Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-store"></i> NC Traders
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="messages.php">Back to Inbox</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-comments"></i> Chat with <?php echo htmlspecialchars($otherUser['username'] ?? $otherUser['email']); ?></h4>
                    </div>
                    <div class="card-body" style="min-height: 400px;">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($messages)): ?>
                            <div class="text-center text-muted py-5">
                                <p class="mb-0">No messages yet. Start the conversation below.</p>
                            </div>
                        <?php else: ?>
                            <div class="message-list">
                                <?php foreach ($messages as $message): ?>
                                    <div class="mb-3 <?php echo $message['sender_id'] === $currentUser['id'] ? 'text-end' : 'text-start'; ?>">
                                        <div class="d-inline-block p-3 rounded <?php echo $message['sender_id'] === $currentUser['id'] ? 'bg-primary text-white' : 'bg-light text-dark'; ?>">
                                            <?php echo nl2br(htmlspecialchars($message['message_text'])); ?>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <?php echo $message['sender_id'] === $currentUser['id'] ? 'You' : htmlspecialchars($otherUser['username'] ?? 'Seller'); ?> · <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <form method="POST">
                            <div class="input-group">
                                <textarea class="form-control" name="message_text" rows="2" placeholder="Write your message..." required></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
