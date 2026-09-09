<?php
session_start();

if (isset($_SESSION['userID'])) {
    header("Location:dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmartQuiz</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="manifest" href="/smartquiz/manifest.json">

    <meta name="theme-color" content="#2563eb">

    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <meta name="apple-mobile-web-app-title" content="SmartQuiz">

</head>

<body>

<div class="landing-container">

    <div class="landing-content">

        <img src="images/logo.png" alt="SmartQuiz Logo">

        <span class="badge badge-success">
            Powered by Gemini AI
        </span>

        <h1>SmartQuiz</h1>

        <p class="landing-text">
            Generate quizzes instantly from documents using AI, assess learners automatically, and manage results from one modern platform.
        </p>

        

        <div class="button-group">

            <a href="login.php" class="btn">
                Login
            </a>

            <a href="register.php" class="btn btn-secondary">
                Register
            </a>

        </div>

        <div class="landing-footer">

            SmartQuiz © <?php echo date('Y'); ?>

        </div>

    </div>

</div>

<script>

if ("serviceWorker" in navigator)
{
    window.addEventListener("load", () =>
    {
        navigator.serviceWorker
            .register("/smartquiz/service-worker.js")
            .then(reg =>
            {
                console.log("Service Worker Registered:", reg.scope);
            })
            .catch(err =>
            {
                console.error(err);
            });
    });
}

</script>

</body>
</html>