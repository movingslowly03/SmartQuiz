<?php

include("database.php");
include("header.php");
include("sidebar.php");

$role = $_SESSION['role'];

if(isset($_POST['addSubject']) && $role == "Admin")
{
    $subjectCode = mysqli_real_escape_string($conn, $_POST['subjectCode']);
    $subjectName = mysqli_real_escape_string($conn, $_POST['subjectName']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn,
        "INSERT INTO subjects(subjectCode, subjectName, description)
        VALUES('$subjectCode','$subjectName','$description')");
}


if(isset($_GET['delete'])&& $role == "Admin")
{
    $subjectID = (int)$_GET['delete'];

    $check = mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM materials
         WHERE subjectID='$subjectID'");

    $row = mysqli_fetch_assoc($check);

    if($row['total'] > 0)
    {
        $message = "Cannot delete this subject because study materials are still assigned to it.";
    }
    else
    {
        mysqli_query($conn,
            "DELETE FROM subjects
             WHERE subjectID='$subjectID'");

        header("Location: subjectList.php");
        exit();
    }
}

?>

<h1>Subjects</h1>

<?php if($role == "Admin") { ?>

<form method="POST">

    <label>Subject Code</label>

    <input
        type="text"
        name="subjectCode"
        required
    >

    <label>Subject Name</label>

    <input
        type="text"
        name="subjectName"
        required
    >

    <label>Description</label>

    <textarea
        name="description"
        rows="3"
    ></textarea>

    <br><br>

    <button
        type="submit"
        name="addSubject"
    >
        Add Subject
    </button>

</form>

<br>

<?php } ?>

<div class="page-title">

    

</div>

<table>

    <tr>

        <th>ID</th>

        <th>Code</th>

        <th>Subject Name</th>

        <th>Description</th>

        <?php
        if($role == "Admin")
        {
            echo "<th>Action</th>";
        }
        ?>

    </tr>

<?php

$query = mysqli_query($conn,
    "SELECT * FROM subjects
    ORDER BY subjectCode ASC");

while($row = mysqli_fetch_assoc($query))
{

?>

<tr>

    <td><?php echo $row['subjectID']; ?></td>

    <td><?php echo $row['subjectCode']; ?></td>

    <td><?php echo $row['subjectName']; ?></td>

    <td><?php echo $row['description']; ?></td>

    <?php if($role == "Admin") { ?>

    <td>

        <a href="editSubject.php?id=<?php echo $row['subjectID']; ?>">
            Edit
        </a>

        |

        <a
            href="subjectList.php?delete=<?php echo $row['subjectID']; ?>"
            onclick="return confirm('Delete this subject?')"
        >
            Delete
        </a>

    </td>

    <?php } ?>

</tr>

<?php

}

?>

</table>

<?php

include("footer.php");

?>