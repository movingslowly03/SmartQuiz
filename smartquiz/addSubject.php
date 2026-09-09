<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Admin")
{
    header("Location: dashboard.php");
    exit();
}

$message = "";

if(isset($_POST['save']))
{
    $subjectCode = mysqli_real_escape_string($conn, trim($_POST['subjectCode']));
    $subjectName = mysqli_real_escape_string($conn, trim($_POST['subjectName']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    $check = mysqli_query($conn,

    "SELECT *

    FROM subjects

    WHERE subjectCode='$subjectCode'");

    if(mysqli_num_rows($check) > 0)
    {
        $message = "<div class='error'>Subject code already exists.</div>";
    }
    else
    {
        mysqli_query($conn,

        "INSERT INTO subjects
        (
            subjectCode,
            subjectName,
            description
        )

        VALUES
        (
            '$subjectCode',
            '$subjectName',
            '$description'
        )");

        header("Location: subjectList.php");
        exit();
    }
}

?>

<div class="page">

    <div class="page-title">

        <h1>Add Subject</h1>

    </div>

    <?php

    if($message != "")
    {
        echo $message;
    }

    ?>

    <div class="card">

        <form method="POST">

            <label>Subject Code</label>

            <input
            type="text"
            name="subjectCode"
            placeholder="Example: CSC264"
            required>

            <label>Subject Name</label>

            <input
            type="text"
            name="subjectName"
            placeholder="Example: Web Application Development"
            required>

            <label>Description</label>

            <textarea
            name="description"
            rows="5"
            placeholder="Enter subject description..."></textarea>

            <br><br>

            <button
            type="submit"
            name="save">

                Save Subject

            </button>

            <a
            href="subjectList.php"
            class="btn">

                Cancel

            </a>

        </form>

    </div>

</div>

<?php

include("footer.php");

?>