<?php

include("database.php");
include("header.php");
include("sidebar.php");

$userID = $_SESSION['userID'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE userID = '$userID'");
$user = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['save']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $check = mysqli_query($conn,
        "SELECT * FROM users
        WHERE email='$email'
        AND userID != '$userID'");

    if(mysqli_num_rows($check) > 0)
    {
        $message = "Email already exists.";
    }
    else
    {
        if(!empty($password))
        {
            if($password != $confirmPassword)
            {
                $message = "Passwords do not match.";
            }
            else
            {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                mysqli_query($conn,
                    "UPDATE users
                    SET
                    name='$name',
                    email='$email',
                    password='$hashedPassword'
                    WHERE userID='$userID'");

                $_SESSION['name'] = $name;

                $message = "Profile updated successfully.";
            }
        }
        else
        {
            mysqli_query($conn,
                "UPDATE users
                SET
                name='$name',
                email='$email'
                WHERE userID='$userID'");

            $_SESSION['name'] = $name;

            $message = "Profile updated successfully.";
        }

        $query = mysqli_query($conn, "SELECT * FROM users WHERE userID='$userID'");
        $user = mysqli_fetch_assoc($query);
    }
}

?>

<h1>Edit Profile</h1>

<?php

if($message != "")
{
    echo "<p class='message'>$message</p>";
}

?>

<form method="POST">

    <label>Name</label>

    <input
        type="text"
        name="name"
        value="<?php echo $user['name']; ?>"
        required
    >

    <label>Email</label>

    <input
        type="email"
        name="email"
        value="<?php echo $user['email']; ?>"
        required
    >

    <label>Role</label>

    <input
        type="text"
        value="<?php echo $user['role']; ?>"
        readonly
    >

    <label>New Password</label>

    <input
        type="password"
        name="password"
        placeholder="Leave blank to keep current password"
    >

    <label>Confirm Password</label>

    <input
        type="password"
        name="confirmPassword"
    >

    <br><br>

    <button
        type="submit"
        name="save"
    >
        Save Changes
    </button>

    <a href="profile.php" class="btn">
        Cancel
    </a>

</form>

<?php

include("footer.php");

?>