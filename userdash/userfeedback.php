<?php

// Include your database connection
include '../db_connection.php';
include 'header.php'; // Include the header file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

$loggedInUserId = $_SESSION['user_id'];
$loggedInUsername = $_SESSION['username'];

// Handle voting
if (isset($_POST['vote'])) {
    $feedbackId = $_POST['feedback_id'];
    $userId = $loggedInUserId; // Use logged-in user ID
    $voteType = $_POST['vote_type'];

    try {
        // Check if user has already voted
        $checkVote = $pdo->prepare("SELECT * FROM feedback_votes WHERE feedback_id = :feedback_id AND user_id = :user_id");
        $checkVote->execute(['feedback_id' => $feedbackId, 'user_id' => $userId]);

        if ($checkVote->rowCount() == 0) {
            // If no existing vote, insert the new vote
            $stmt = $pdo->prepare("INSERT INTO feedback_votes (feedback_id, user_id, vote_type) VALUES (:feedback_id, :user_id, :vote_type)");
            $stmt->execute(['feedback_id' => $feedbackId, 'user_id' => $userId, 'vote_type' => $voteType]);
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle replies
if (isset($_POST['reply'])) {
    $feedbackId = $_POST['feedback_id'];
    $userId = $loggedInUserId; // Use logged-in user ID
    $replyText = $_POST['reply_text'];

    try {
        // Insert reply into feedback_replies table
        $stmt = $pdo->prepare("INSERT INTO feedback_replies (feedback_id, user_id, reply_text) VALUES (:feedback_id, :user_id, :reply_text)");
        $stmt->execute(['feedback_id' => $feedbackId, 'user_id' => $userId, 'reply_text' => $replyText]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all feedbacks with votes and replies
try {
    $stmt = $pdo->prepare("
        SELECT 
            f.id, 
            f.feedback_text, 
            f.rating, 
            f.created_at, 
            u.username,
            (SELECT COUNT(*) FROM feedback_votes WHERE feedback_id = f.id AND vote_type = 'up') AS upvotes,
            (SELECT COUNT(*) FROM feedback_votes WHERE feedback_id = f.id AND vote_type = 'down') AS downvotes,
            (SELECT r.reply_text FROM feedback_replies r WHERE r.feedback_id = f.id LIMIT 1) AS reply_text, 
            (SELECT u2.username FROM feedback_replies r JOIN users u2 ON r.user_id = u2.user_id WHERE r.feedback_id = f.id LIMIT 1) AS reply_username
        FROM 
            feedback f
        JOIN 
            users u ON f.user_id = u.user_id
        ORDER BY 
            f.created_at DESC
    ");
    $stmt->execute();
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Feedback</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }

        /* Header Styling */
        .navbar {
            background-color: #000 !important;
        }

        .navbar-brand, .navbar-nav .nav-link, .dropdown-item {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover {
            background-color: #a4aba6 !important;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .dropdown-menu {
            background-color: #000;
            color: #fff;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #A4ABA6 !important;
        }

        .navbar, .navbar-nav, .dropdown-menu {
            opacity: 1 !important;
            filter: none !important;
            box-shadow: none !important;
        }

        /* Feedback Page Specific Styles */
        .feedback-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .feedback-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .profile-icon {
            font-size: 50px;
            color: #6c757d;
            margin-right: 15px;
        }

        .username {
            font-weight: bold;
            font-size: 1.2em;
        }

        .feedback-body {
            margin-top: 10px;
        }

        .rating-stars i {
            color: #FFD700; /* Gold color for stars */
            font-size: 1.5em;
            margin-right: 3px;
        }

        .feedback-text {
            margin-top: 10px;
        }

        .feedback-footer {
            margin-top: 15px;
            font-size: 0.9em;
            color: #555;
            display: flex;
            justify-content: space-between;
        }

        .vote-icons {
            display: flex;
            align-items: center;
        }

        .vote-icons form {
            display: inline; /* Ensure forms are inline for styling */
            margin-right: 15px; /* Space between the two forms */
        }

        .vote-icons button {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 1.2em;
            color: #6c757d; /* Default color */
            transition: color 0.3s, box-shadow 0.3s;
        }

        .vote-icons button:hover {
            color: #007bff;
            box-shadow: 0px 4px 12px rgba(0, 123, 255, 0.4); /* Shadow effect on hover */
        }

        .vote-icons i {
            margin-right: 5px; /* Spacing between icon and text */
        }

        .reply-section {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .reply-section textarea {
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .reply-section button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">User Feedback</h2>

        <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <i class="fas fa-user-circle profile-icon"></i>
                        <div>
                            <span class="username"><?php echo htmlspecialchars($feedback['username']); ?></span>
                            <div class="rating-stars">
                                <?php for ($i = 0; $i < $feedback['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="text-muted"><?php echo date('M j, Y', strtotime($feedback['created_at'])); ?></div>
                        </div>
                    </div>
                    <div class="feedback-body">
                        <p class="feedback-text"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                    </div>
                    <div class="feedback-footer">
                        <span>Was this helpful?</span>
                        <div class="vote-icons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $loggedInUserId; ?>">
                                <input type="hidden" name="vote_type" value="up">
                                <button type="submit" name="vote" class="btn btn-link"><i class="fas fa-thumbs-up"></i> <?php echo $feedback['upvotes']; ?></button>
                            </form>

                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $loggedInUserId; ?>">
                                <input type="hidden" name="vote_type" value="down">
                                <button type="submit" name="vote" class="btn btn-link"><i class="fas fa-thumbs-down"></i> <?php echo $feedback['downvotes']; ?></button>
                            </form>
                        </div>
                    </div>

                    <!-- Display reply if exists -->
                    <?php if (!empty($feedback['reply_text'])): ?>
                        <div class="feedback-reply">
                            <strong><?php echo htmlspecialchars($feedback['reply_username']); ?>'s Reply:</strong>
                            <p><?php echo htmlspecialchars($feedback['reply_text']); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Reply Section -->
                    <div class="reply-section">
                        <form method="POST">
                            <div class="form-group">
                                <textarea name="reply_text" class="form-control" rows="3" placeholder="Write your reply..."></textarea>
                            </div>
                            <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                            <button type="submit" name="reply" class="btn btn-primary">Submit Reply</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No feedback available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
