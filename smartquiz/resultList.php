<?php

include("database.php");
include("header.php");
include("sidebar.php");

$role = $_SESSION['role'];

?>

<h1>Quiz Results</h1>

<table>

<tr>

    <th>Student</th>

    <th>Quiz</th>

    <th>Score</th>

    <th>Questions</th>

    <th>Time</th>

    <th>Date</th>

    <th>Action</th>

</tr>

<?php

if($role == "Admin")
{

    $query = mysqli_query($conn,

    "SELECT

    quizAttempts.*,

    users.name,

    quizzes.title

    FROM quizAttempts

    JOIN users
    ON quizAttempts.studentID = users.userID

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    ORDER BY quizAttempts.attemptDate DESC");

}

else if($role == "Lecturer")
{

    $query = mysqli_query($conn,

    "SELECT

    quizAttempts.*,

    users.name,

    quizzes.title

    FROM quizAttempts

    JOIN users
    ON quizAttempts.studentID = users.userID

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    WHERE quizzes.ownerID='{$_SESSION['userID']}'
    AND quizzes.ownerRole='Lecturer'
    AND quizzes.quizType='Official'

    ORDER BY quizAttempts.attemptDate DESC");

}

else
{

    $query = mysqli_query($conn,

    "SELECT

    quizAttempts.*,

    quizzes.title

    FROM quizAttempts

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    WHERE quizAttempts.studentID='{$_SESSION['userID']}'

    ORDER BY quizAttempts.attemptDate DESC");

}

while($row = mysqli_fetch_assoc($query))
{

?>

<tr>

<?php

if($role == "Student")
{

?>

<td>

You

</td>

<?php

}
else
{

?>

<td>

<?php echo $row['name']; ?>

</td>

<?php

}

?>

<td>

<?php echo $row['title']; ?>

</td>

<td>

<?php echo $row['score']; ?>%

</td>

<td>

<?php echo $row['totalQuestions']; ?>

</td>

<td>

<?php

$minutes = floor($row['timeTaken']/60);

$seconds = $row['timeTaken']%60;

echo sprintf("%02d:%02d",$minutes,$seconds);

?>

</td>

<td>

<?php echo date("d M Y H:i",strtotime($row['attemptDate'])); ?>

</td>

<td>

<a href="quizResult.php?attempt=<?php echo $row['attemptID']; ?>">

View

</a>

</td>

</tr>

<?php

}

?>

</table>

<?php

include("footer.php");

?>