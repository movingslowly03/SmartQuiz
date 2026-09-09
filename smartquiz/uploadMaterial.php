<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Lecturer" && $_SESSION['role'] != "Student")
{
    header("Location: dashboard.php");
    exit();
}

$message = "";

if(isset($_POST['upload']))
{
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $subjectID = (int)$_POST['subjectID'];
    $file = $_FILES['material'];

    if($file['error'] == 0)
    {
        $allowed = ['pdf','docx','txt'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if(in_array($extension,$allowed))
        {
            $newFileName = time()."_".basename($file['name']);
            $destination = "images/uploads/".$newFileName;

            if(move_uploaded_file($file['tmp_name'],$destination))
            {
                mysqli_query($conn,
                "INSERT INTO materials
                (title,fileName,fileType,ownerID,ownerRole,subjectID)
                VALUES
                (
                '$title',
                '$newFileName',
                '".strtoupper($extension)."',
                '{$_SESSION['userID']}',
                '{$_SESSION['role']}',
                '$subjectID'
                )");

                if(mysqli_errno($conn))
                    $message = mysqli_error($conn);
                else
                    $message = "Material uploaded successfully.";
            }
            else
            {
                $message = "Failed to upload file.";
            }
        }
        else
        {
            $message = "Only PDF, DOCX and TXT files are allowed.";
        }
    }
}

?>

<h1>Upload Study Material</h1>

<?php
if($message!="")
{
    echo "<p class='message'>$message</p>";
}
?>

<form method="POST" enctype="multipart/form-data">

<label>Title</label>
<input type="text" name="title" required>

<label>Subject</label>

<select name="subjectID" required>

<option value="">-- Select Subject --</option>

<?php
$subjects = mysqli_query($conn,"SELECT * FROM subjects ORDER BY subjectCode");
while($subject=mysqli_fetch_assoc($subjects))
{
?>
<option value="<?php echo $subject['subjectID']; ?>">
<?php echo $subject['subjectCode']." - ".$subject['subjectName']; ?>
</option>
<?php } ?>

</select>

<label>Study Material</label>

<input type="file" name="material" accept=".pdf,.docx,.txt" required>

<br><br>

<div class="button-group">
<button type="submit" name="upload">Upload Material</button>
<a href="materialList.php" class="btn">Cancel</a>
</div>

</form>

<?php include("footer.php"); ?>
