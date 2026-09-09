<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Admin")
{
    header("Location: dashboard.php");
    exit();
}

/* ---------- DELETE USER ---------- */

if(isset($_GET['delete']))
{
    $userID = (int)$_GET['delete'];

    if($userID != $_SESSION['userID'])
    {
        mysqli_query($conn,
        "DELETE FROM users
        WHERE userID='$userID'
        AND role!='Admin'");
    }

    header("Location:userList.php");
    exit();
}

?>

<h1>User Management</h1>

<a href="register.php" class="btn">

Add New User

</a>

<br><br>

<table>

<tr>

    <th>ID</th>

    <th>Name</th>

    <th>Email</th>

    <th>Role</th>

    <th>Created</th>

    <th>Action</th>

</tr>

<?php

$query = mysqli_query($conn,

"SELECT *

FROM users

ORDER BY role,name");

while($user = mysqli_fetch_assoc($query))
{

?>

<tr>

<td>

<?php echo $user['userID']; ?>

</td>

<td>

<?php echo $user['name']; ?>

</td>

<td>

<?php echo $user['email']; ?>

</td>

<td>

<?php echo $user['role']; ?>

</td>

<td>

<?php

echo date("d M Y",strtotime($user['createdAt']));

?>

</td>

<td>

<a
href="userList.php?delete=<?php echo $user['userID']; ?>"
onclick="return confirm('Delete this user?')">

Delete

</a>

<?php

}

?>

</td>

</tr>

<?php



?>

</table>

<?php

include("footer.php");

?>