<?php

include("database.php");
include("header.php");
include("sidebar.php");

if(!isset($_GET['attempt']))
{
    header("Location: resultList.php");
    exit();
}

$attemptID = (int)$_GET['attempt'];

if($_SESSION['role'] == "Student")
{
    $query = mysqli_query($conn,

    "SELECT
        quizAttempts.*,
        quizzes.title

    FROM quizAttempts

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    WHERE quizAttempts.attemptID='$attemptID'

    AND quizAttempts.studentID='{$_SESSION['userID']}'");

}
else if($_SESSION['role'] == "Lecturer")
{
    $query = mysqli_query($conn,

    "SELECT
        quizAttempts.*,
        quizzes.title

    FROM quizAttempts

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    WHERE quizAttempts.attemptID='$attemptID'

    AND quizzes.ownerID='{$_SESSION['userID']}'
    AND quizzes.ownerRole='Lecturer'
    AND quizzes.quizType='Official'");

}
else if($_SESSION['role'] == "Admin")
{
    $query = mysqli_query($conn,

    "SELECT
        quizAttempts.*,
        quizzes.title

    FROM quizAttempts

    JOIN quizzes
    ON quizAttempts.quizID = quizzes.quizID

    WHERE quizAttempts.attemptID='$attemptID'");
}
else
{
    header("Location: dashboard.php");
    exit();
}

if(mysqli_num_rows($query) == 0)
{
    header("Location: resultList.php");
    exit();
}

$attempt = mysqli_fetch_assoc($query);

?>

<h1>Quiz Result</h1>

<h2><?php echo $attempt['title']; ?></h2>

<table>

<tr>

<th>Score</th>

<td>

<?php echo $attempt['score']; ?>%

</td>

</tr>

<tr>

<th>Total Questions</th>

<td>

<?php echo $attempt['totalQuestions']; ?>

</td>

</tr>

<tr>

<th>Time Taken</th>

<td>

<?php

$minutes = floor($attempt['timeTaken']/60);

$seconds = $attempt['timeTaken']%60;

echo sprintf("%02d:%02d",$minutes,$seconds);

?>

</td>

</tr>

<tr>

<th>Attempt Date</th>

<td>

<?php echo $attempt['attemptDate']; ?>

</td>

</tr>

</table>

<hr>

<?php

$questions = mysqli_query($conn,

"SELECT

questions.*,

attemptAnswers.studentAnswer,

attemptAnswers.isCorrect

FROM questions

JOIN attemptAnswers

ON questions.questionID = attemptAnswers.questionID

WHERE attemptAnswers.attemptID='$attemptID'

ORDER BY questions.questionID");

$number = 1;

while($question = mysqli_fetch_assoc($questions))
{

?>

<h3>

Question <?php echo $number++; ?>

</h3>

<p>

<strong>

<?php echo $question['questionText']; ?>

</strong>

</p>

<?php

if($question['questionType']=="MCQ")
{

?>

<p>

A. <?php echo $question['optionA']; ?>

</p>

<p>

B. <?php echo $question['optionB']; ?>

</p>

<p>

C. <?php echo $question['optionC']; ?>

</p>

<p>

D. <?php echo $question['optionD']; ?>

</p>

<?php

}

?>

<p>

<strong>Your Answer :</strong>

<?php echo $question['studentAnswer']; ?>

</p>

<p>

<strong>Correct Answer :</strong>

<?php echo $question['correctAnswer']; ?>

</p>

<p>

<strong>Explanation :</strong>

<?php echo $question['explanation']; ?>

</p>

<p>

<?php

if($question['isCorrect'])
{
    echo "<span style='color:green;'>Correct</span>";
}
else
{
    echo "<span style='color:red;'>Incorrect</span>";
}

?>

</p>

<hr>

<?php

}

?>

<?php

if($_SESSION['role']=="Student")
{
    echo '<a href="quizList.php" class="btn">Back to Quiz List</a>';
}
else
{
    echo '<a href="resultList.php" class="btn">Back to Results</a>';
}

?>

<?php

include("footer.php");

?>