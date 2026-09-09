<?php

if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}

if(!isset($_SESSION['userID']))
{
    header("Location: login.php");
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

<header>

    <div class="logo">

        <button
            id="menuToggle"
            class="menu-toggle"
            type="button"
            aria-label="Open navigation"
        >
            ☰
        </button>

        <img src="images/logo.png" alt="SmartQuiz Logo">

        <h2>SmartQuiz</h2>

    </div>

    <div class="user-info">

        <span>
            Welcome,
            <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
            (<?php echo htmlspecialchars($_SESSION['role']); ?>)
        </span>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</header>

<div class="container">
