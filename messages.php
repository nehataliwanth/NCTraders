<?php
require_once 'config/session.php';
require_once 'includes/helpers.php';

requireLogin();
$currentUser = getCurrentUser();
$conversations = getConversations($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - NC Traders</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="messages.php">Messages</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <h1 class="fw-bold mb-4"><i class="fas fa-comments"></i> Inbox</h1>

        <?php if (empty($conversations)): ?>
            <div class="alert alert-info">
                <p class="mb-0">No conversations yet. Start a chat from a product page or seller profile.</p>
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($conversations as $conversation): ?>
                    <a href="chat.php?user_id=<?php echo $conversation['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($conversation['username'] ?? $conversation['email'] ?? 'User'); ?></h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars(truncateText($conversation['last_message'], 80)); ?></p>
                        </div>
                        <small class="text-muted"><?php echo date('M d, Y', strtotime($conversation['last_message_time'])); ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2026 NC Traders. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
