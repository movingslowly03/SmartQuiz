<?php

session_start();
include("database.php");

if (isset($_SESSION['userID'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if (isset($_POST['register'])) {

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        $message = "An account with this email already exists.";

    } elseif ($password != $confirmPassword) {

        $message = "Passwords do not match.";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters.";

    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users(name,email,password,role)
                  VALUES('$name','$email','$hashedPassword','$role')";

        if (mysqli_query($conn, $query)) {

            header("Location: login.php");
            exit();

        } else {

            $message = "Registration failed. Please try again.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | SmartQuiz</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="register-container">

    <img src="images/logo.png" alt="SmartQuiz Logo">

    <h2>Create Account</h2>

    <p class="login-subtitle">
        Join SmartQuiz and start generating AI-powered quizzes.
    </p>

    <?php if ($message != "") { ?>

        <div class="error">
            <?php echo $message; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <label>Full Name</label>

        <input
            type="text"
            name="name"
            placeholder="Enter your full name"
            autocomplete="name"
            required
        >

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            autocomplete="email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Minimum 8 characters"
            autocomplete="new-password"
            required
        >

        <label>Confirm Password</label>

        <input
            type="password"
            name="confirmPassword"
            placeholder="Re-enter your password"
            autocomplete="new-password"
            required
        >

        <label>Role</label>

        <select name="role" required>

            <option value="">Select your role</option>

            <option value="Student">Student</option>

            <option value="Lecturer">Lecturer</option>

        </select>

        <button
            type="submit"
            name="register"
            class="btn"
        >
            Create Account
        </button>

    </form>

    <p class="back-home">

        <a href="index.php">
            ← Back to Home
        </a>

    </p>

    <p>

        Already have an account?

        <a href="login.php">
            Login Here
        </a>

    </p>

</div>

</body>
</html>