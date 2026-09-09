<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Lecturer" && $_SESSION['role'] != "Student")
{
    header("Location: dashboard.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: materialList.php");
    exit();
}

$materialID = $_GET['id'];

$query = mysqli_query($conn,
    "SELECT * FROM materials
    WHERE materialID='$materialID'
    AND ownerID='{$_SESSION['userID']}'
    AND ownerRole='{$_SESSION['role']}'");

if(mysqli_num_rows($query) == 0)
{
    header("Location: materialList.php");
    exit();
}

$material = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['update']))
{
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subjectID = $_POST['subjectID'];

    if($_FILES['material']['error'] == 0)
    {
        $allowed = ['pdf','docx','txt'];

        $extension = strtolower(pathinfo($_FILES['material']['name'], PATHINFO_EXTENSION));

        if(in_array($extension, $allowed))
        {
            if(file_exists("images/uploads/".$material['fileName']))
            {
                unlink("images/uploads/".$material['fileName']);
            }

            $newFileName = time() . "_" . basename($_FILES['material']['name']);

            move_uploaded_file(
                $_FILES['material']['tmp_name'],
                "images/uploads/".$newFileName
            );

            mysqli_query($conn,
                "UPDATE materials
                SET
                    title='$title',
                    subjectID='$subjectID',
                    fileName='$newFileName',
                    fileType='".strtoupper($extension)."',
                    aiStatus='Pending'
                WHERE materialID='$materialID'");
        }
        else
        {
            $message = "Invalid file type.";
        }
    }
    else
    {
        mysqli_query($conn,
            "UPDATE materials
            SET
                title='$title',
                subjectID='$subjectID'
            WHERE materialID='$materialID'");
    }

    if($message == "")
    {
        $message = "Material updated successfully.";

        $query = mysqli_query($conn,
            "SELECT * FROM materials
            WHERE materialID='$materialID'
            AND ownerID='{$_SESSION['userID']}'
            AND ownerRole='{$_SESSION['role']}'");

        $material = mysqli_fetch_assoc($query);
    }
}

?>

<h1>Edit Material</h1>

<?php

if($message != "")
{
    echo "<p class='message'>$message</p>";
}

?>

<form method="POST" enctype="multipart/form-data">

    <label>Title</label>

    <input
        type="text"
        name="title"
        value="<?php echo $material['title']; ?>"
        required
    >

    <label>Subject</label>

    <select
        name="subjectID"
        required
    >

<?php

$subjects = mysqli_query($conn,
    "SELECT * FROM subjects
    ORDER BY subjectCode");

while($subject = mysqli_fetch_assoc($subjects))
{

?>

<option
value="<?php echo $subject['subjectID']; ?>"

<?php

if($subject['subjectID'] == $material['subjectID'])
{
    echo "selected";
}

?>

>

<?php

echo $subject['subjectCode']." - ".$subject['subjectName'];

?>

</option>

<?php

}

?>

    </select>

    <label>Current File</label>

    <input
        type="text"
        value="<?php echo $material['fileName']; ?>"
        readonly
    >

    <label>Replace File (Optional)</label>

    <input
        type="file"
        name="material"
        accept=".pdf,.docx,.txt"
    >

    <br><br>

    <button
        type="submit"
        name="update"
    >
        Update Material
    </button>

    <a href="materialList.php" class="btn">

        Cancel

    </a>

</form>

<?php

include("footer.php");

?>