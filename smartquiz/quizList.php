<?php

include("database.php");
include("header.php");
include("sidebar.php");

$role = $_SESSION['role'];

/* ---------------- DELETE QUIZ ---------------- */

if(isset($_GET['delete']) && $role=="Lecturer")
{
    $quizID=(int)$_GET['delete'];

    // Verify ownership
    $check=mysqli_query($conn,"
    SELECT * FROM quizzes
    WHERE quizID='$quizID'
    AND ownerID='{$_SESSION['userID']}'
    AND ownerRole='Lecturer'");

    if(mysqli_num_rows($check))
    {
        // Delete answers first
        mysqli_query($conn,"
        DELETE attemptAnswers
        FROM attemptAnswers
        INNER JOIN quizAttempts
        ON attemptAnswers.attemptID=quizAttempts.attemptID
        WHERE quizAttempts.quizID='$quizID'");

        // Delete attempts
        mysqli_query($conn,"
        DELETE FROM quizAttempts
        WHERE quizID='$quizID'");

        // Delete questions
        mysqli_query($conn,"
        DELETE FROM questions
        WHERE quizID='$quizID'");

        // Delete quiz
        mysqli_query($conn,"
        DELETE FROM quizzes
        WHERE quizID='$quizID'
        AND ownerID='{$_SESSION['userID']}'
    AND ownerRole='Lecturer'");
    }

    header("Location: quizList.php");
    exit();
}

/* ---------------- PUBLISH ---------------- */

if(isset($_GET['publish']) && $role=="Lecturer")
{
    $quizID=(int)$_GET['publish'];

    $check=mysqli_query($conn,"SELECT 1 FROM questions WHERE quizID='$quizID'");

    if(mysqli_num_rows($check))
    {
        mysqli_query($conn,"
        UPDATE quizzes
        SET status='Published'
        WHERE quizID='$quizID'
        AND ownerID='{$_SESSION['userID']}'
    AND ownerRole='Lecturer'");
    }

    header("Location: quizList.php");
    exit();
}

/* ---------------- DRAFT ---------------- */

if(isset($_GET['draft']) && $role=="Lecturer")
{
    $quizID=(int)$_GET['draft'];

    mysqli_query($conn,"
    UPDATE quizzes
    SET status='Draft'
    WHERE quizID='$quizID'
    AND ownerID='{$_SESSION['userID']}'
    AND ownerRole='Lecturer'");

    header("Location: quizList.php");
    exit();
}

?>

<div class="page-title">
    <div>
        <h1>Quiz List</h1>
        <p>Manage quizzes and publication status.</p>
    </div>
</div>

<table>

<tr>
<th>ID</th>
<th>Title</th>
<th>Subject</th>
<th>Difficulty</th>
<th>Status</th>
<th>Questions</th>
<th>Created</th>
<th>Action</th>
</tr>

<?php

if($role=="Admin")
{
    $query=mysqli_query($conn,"
    SELECT quizzes.*,subjects.subjectCode
    FROM quizzes
    JOIN subjects ON quizzes.subjectID=subjects.subjectID
    ORDER BY createdDate DESC");
}
elseif($role=="Lecturer")
{
    $query=mysqli_query($conn,"
    SELECT quizzes.*,subjects.subjectCode
    FROM quizzes
    JOIN subjects ON quizzes.subjectID=subjects.subjectID
    WHERE quizzes.ownerID='{$_SESSION['userID']}'
    AND quizzes.ownerRole='Lecturer'
    ORDER BY createdDate DESC");
}
else
{
    $query=mysqli_query($conn,"
    SELECT quizzes.*,subjects.subjectCode
    FROM quizzes
    JOIN subjects ON quizzes.subjectID=subjects.subjectID
    WHERE
    (
        quizType='Official'
        AND status='Published'
    )
    OR
    (
        quizType='Practice'
        AND ownerID='{$_SESSION['userID']}'
        AND ownerRole='Student'
    )
    ORDER BY createdDate DESC");
}

while($quiz=mysqli_fetch_assoc($query))
{
$qc=mysqli_num_rows(mysqli_query($conn,"SELECT 1 FROM questions WHERE quizID='".$quiz['quizID']."'"));
?>

<tr>
<td><?=$quiz['quizID']?></td>
<td><?=htmlspecialchars($quiz['title'])?></td>
<td><?=$quiz['subjectCode']?></td>
<td><?=$quiz['difficulty']?></td>
<td><?=$quiz['status']?></td>
<td><?=$qc?></td>
<td><?=date("d M Y",strtotime($quiz['createdDate']))?></td>
<td>

<?php if($role=="Admin"){ ?>

<a href="editQuiz.php?id=<?=$quiz['quizID']?>">View</a>

<?php } elseif($role=="Lecturer"){ ?>

<a href="editQuiz.php?id=<?=$quiz['quizID']?>">Edit</a> |

<?php if($quiz['status']=="Draft"){ ?>

<?php if($qc>0){ ?>
<a href="quizList.php?publish=<?=$quiz['quizID']?>">Publish</a>
<?php } else { echo "Publish"; } ?>

<?php } else { ?>

<a href="quizList.php?draft=<?=$quiz['quizID']?>">Unpublish</a>

<?php } ?>

|

<a href="quizList.php?delete=<?=$quiz['quizID']?>"
onclick="return confirm('Delete this quiz and all related attempts?')">Delete</a>

<?php } else { ?>

<a href="attemptQuiz.php?id=<?=$quiz['quizID']?>">
<?=($quiz['quizType']=="Practice")?"Practice":"Attempt"?>
</a>

<?php } ?>

</td>
</tr>

<?php } ?>

</table>

<?php include("footer.php"); ?>
