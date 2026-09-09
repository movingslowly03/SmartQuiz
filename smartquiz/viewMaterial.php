<?php

include("database.php");

if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}

if(!isset($_SESSION['userID']))
{
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: dashboard.php");
    exit();
}

$materialID = (int)$_GET['id'];

if($_SESSION['role'] == "Admin")
{
    $query = mysqli_query($conn,

    "SELECT materials.*, subjects.subjectCode, subjects.subjectName, users.name

    FROM materials

    JOIN subjects
    ON materials.subjectID = subjects.subjectID

    JOIN users
    ON materials.ownerID = users.userID

    WHERE materialID='$materialID'");
}
else if($_SESSION['role'] == "Lecturer")
{
    $query = mysqli_query($conn,

    "SELECT materials.*, subjects.subjectCode, subjects.subjectName

    FROM materials

    JOIN subjects
    ON materials.subjectID = subjects.subjectID

    WHERE materialID='$materialID'

    AND ownerID='{$_SESSION['userID']}'

    AND ownerRole='{$_SESSION['role']}'");
}
else if($_SESSION['role'] == "Student")
{
    $query = mysqli_query($conn,

    "SELECT materials.*, subjects.subjectCode, subjects.subjectName

    FROM materials

    JOIN subjects
    ON materials.subjectID = subjects.subjectID

    WHERE materialID='$materialID'

    AND ownerID='{$_SESSION['userID']}'

    AND ownerRole='Student'");
}
else
{
    header("Location: dashboard.php");
    exit();
}

if(mysqli_num_rows($query) == 0)
{
    header("Location: materialList.php");
    exit();
}

$material = mysqli_fetch_assoc($query);

$filePath = "images/uploads/" . $material['fileName'];

if(!file_exists($filePath))
{
    die("File not found.");
}

$fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

?>

<?php include("header.php"); ?>
<?php include("sidebar.php"); ?>

<h1>Study Material</h1>

<table>

<tr>

<th>Title</th>

<td><?php echo $material['title']; ?></td>

</tr>

<tr>

<th>Subject</th>

<td>

<?php

echo $material['subjectCode'];

echo " - ";

echo $material['subjectName'];

?>

</td>

</tr>

<?php

if($_SESSION['role'] == "Admin")
{

?>

<tr>

<th>Lecturer</th>

<td><?php echo $material['name']; ?></td>

</tr>

<?php

}

?>

<tr>

<th>Uploaded</th>

<td>

<?php

echo date("d M Y H:i", strtotime($material['uploadDate']));

?>

</td>

</tr>

<tr>

<th>File Type</th>

<td><?php echo $material['fileType']; ?></td>

</tr>

</table>

<br>

<?php

if($fileExtension == "pdf")
{

?>

<iframe

src="<?php echo $filePath; ?>"

width="100%"

height="700"

style="border:1px solid #ccc;">

</iframe>

<?php

}
else
{

?>

<a
href="<?php echo $filePath; ?>"
target="_blank"
class="btn">

Download / Open File

</a>

<?php

}

?>

<br><br>

<a href="materialList.php" class="btn">

Back

</a>

<?php include("footer.php"); ?>