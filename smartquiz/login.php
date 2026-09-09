<?php

session_start();
include("database.php");

if (isset($_SESSION['userID'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {

            $_SESSION['userID'] = $row['userID'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Invalid email or password.";

        }

    } else {

        $error = "Invalid email or password.";

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | SmartQuiz</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="login-container">

    <img src="images/logo.png" alt="SmartQuiz Logo">

    <h2>Welcome Back</h2>

    <p class="login-subtitle">
        Sign in to continue to SmartQuiz.
    </p>

    <?php if ($error != "") { ?>

        <div class="error">
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            autocomplete="email"
            autofocus
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
        >

        <button
            type="submit"
            name="login"
            class="btn"
        >
            Login
        </button>

    </form>

    <p class="back-home">

        <a href="index.php">
            ← Back to Home
        </a>

    </p>

    <p>

        Don't have an account?

        <a href="register.php">
            Register Here
        </a>

    </p>

</div>

</body>
</html>