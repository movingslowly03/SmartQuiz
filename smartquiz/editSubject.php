<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Admin")
{
    header("Location: dashboard.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: subjectList.php");
    exit();
}

$subjectID = $_GET['id'];

$query = mysqli_query($conn,
    "SELECT * FROM subjects
    WHERE subjectID = '$subjectID'");

if(mysqli_num_rows($query) == 0)
{
    header("Location: subjectList.php");
    exit();
}

$subject = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['update']))
{
    $subjectCode = mysqli_real_escape_string($conn, $_POST['subjectCode']);
    $subjectName = mysqli_real_escape_string($conn, $_POST['subjectName']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn,
        "UPDATE subjects
        SET
            subjectCode='$subjectCode',
            subjectName='$subjectName',
            description='$description'
        WHERE subjectID='$subjectID'");

    $message = "Subject updated successfully.";

    $query = mysqli_query($conn,
        "SELECT * FROM subjects
        WHERE subjectID='$subjectID'");

    $subject = mysqli_fetch_assoc($query);
}

?>

<h1>Edit Subject</h1>

<?php

if($message != "")
{
    echo "<p class='message'>$message</p>";
}

?>

<form method="POST">

    <label>Subject Code</label>

    <input
        type="text"
        name="subjectCode"
        value="<?php echo $subject['subjectCode']; ?>"
        required
    >

    <label>Subject Name</label>

    <input
        type="text"
        name="subjectName"
        value="<?php echo $subject['subjectName']; ?>"
        required
    >

    <label>Description</label>

    <textarea
        name="description"
        rows="4"
    ><?php echo $subject['description']; ?></textarea>

    <br><br>

    <button
        type="submit"
        name="update"
    >
        Update Subject
    </button>

    <a href="subjectList.php" class="btn">
        Cancel
    </a>

</form>

<?php

include("footer.php");

?>