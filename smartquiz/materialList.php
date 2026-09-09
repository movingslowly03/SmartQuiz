<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Admin" &&
   $_SESSION['role'] != "Lecturer" &&
   $_SESSION['role'] != "Student")
{
    header("Location: dashboard.php");
    exit();
}

/* DELETE */

if(isset($_GET['delete']))
{
    $materialID = (int)$_GET['delete'];

    $query = mysqli_query($conn,
    "SELECT * FROM materials WHERE materialID='$materialID'");

    if(mysqli_num_rows($query)>0)
    {
        $material=mysqli_fetch_assoc($query);

        if(
            $_SESSION['role']=="Admin" ||
            (
                $material['ownerID']==$_SESSION['userID'] &&
                $material['ownerRole']==$_SESSION['role']
            )
        )
        {
            if(file_exists("images/uploads/".$material['fileName']))
            {
                unlink("images/uploads/".$material['fileName']);
            }

            mysqli_query($conn,
            "DELETE FROM materials WHERE materialID='$materialID'");
        }
    }

    header("Location: materialList.php");
    exit();
}
?>

<h1>Study Materials</h1>

<?php if($_SESSION['role']=="Lecturer" || $_SESSION['role']=="Student"){ ?>

<a href="uploadMaterial.php" class="btn">Upload New Material</a>

<br><br>

<?php } ?>

<table>

<tr>
<th>ID</th>
<th>Title</th>
<th>Subject</th>
<th>File Type</th>
<th>Upload Date</th>
<th>AI Status</th>
<th>Owner</th>
<th>Action</th>
</tr>

<?php

if($_SESSION['role']=="Admin")
{
    $query=mysqli_query($conn,
    "SELECT materials.*,subjects.subjectCode
    FROM materials
    JOIN subjects ON materials.subjectID=subjects.subjectID
    ORDER BY uploadDate DESC");
}
else
{
    $query=mysqli_query($conn,
    "SELECT materials.*,subjects.subjectCode
    FROM materials
    JOIN subjects ON materials.subjectID=subjects.subjectID
    WHERE ownerID='{$_SESSION['userID']}'
    AND ownerRole='{$_SESSION['role']}'
    ORDER BY uploadDate DESC");
}

while($row=mysqli_fetch_assoc($query))
{
?>

<tr>

<td><?php echo $row['materialID']; ?></td>
<td><?php echo $row['title']; ?></td>
<td><?php echo $row['subjectCode']; ?></td>
<td><?php echo $row['fileType']; ?></td>
<td><?php echo $row['uploadDate']; ?></td>
<td><?php echo $row['aiStatus']; ?></td>
<td><?php echo $row['ownerRole']; ?></td>

<td>

<a href="viewMaterial.php?id=<?php echo $row['materialID']; ?>">View</a>

<?php if($_SESSION['role']!="Admin"){ ?>

|

<a href="editMaterial.php?id=<?php echo $row['materialID']; ?>">Edit</a>

|

<a href="generateQuiz.php?id=<?php echo $row['materialID']; ?>">
<?php echo ($_SESSION['role']=="Student")?"Practice Quiz":"Generate Quiz"; ?>
</a>

|

<a href="materialList.php?delete=<?php echo $row['materialID']; ?>"
onclick="return confirm('Delete this material?')">
Delete
</a>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

<?php include("footer.php"); ?>
