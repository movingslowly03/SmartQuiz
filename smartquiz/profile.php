<?php

include("database.php");
include("header.php");
include("sidebar.php");

$userID = $_SESSION['userID'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE userID = '$userID'");
$user = mysqli_fetch_assoc($query);

?>

<h1>My Profile</h1>

<div class="profile-container">

    <table>

        <tr>

            <th>Name</th>

            <td><?php echo $user['name']; ?></td>

        </tr>

        <tr>

            <th>Email</th>

            <td><?php echo $user['email']; ?></td>

        </tr>

        <tr>

            <th>Role</th>

            <td><?php echo $user['role']; ?></td>

        </tr>

        <tr>

            <th>Member Since</th>

            <td><?php echo date("d M Y", strtotime($user['createdAt'])); ?></td>

        </tr>

    </table>

    <br>

    <a href="editProfile.php" class="btn">
        Edit Profile
    </a>

</div>

<?php

include("footer.php");

?>