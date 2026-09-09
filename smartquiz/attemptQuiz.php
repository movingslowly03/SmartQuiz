<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Student")
{
    header("Location: dashboard.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: quizList.php");
    exit();
}

$quizID = (int)$_GET['id'];

$query = mysqli_query($conn,

"SELECT quizzes.*, subjects.subjectCode

FROM quizzes

JOIN subjects
ON quizzes.subjectID = subjects.subjectID

WHERE quizzes.quizID='$quizID'

AND
(
(quizzes.quizType='Official' AND quizzes.status='Published')
OR
(quizzes.quizType='Practice'
AND quizzes.ownerID='{$_SESSION['userID']}'
AND quizzes.ownerRole='Student')
)");

if(mysqli_num_rows($query) == 0)
{
    header("Location: quizList.php");
    exit();
}

$quiz = mysqli_fetch_assoc($query);

/* ---------- Prevent Multiple Attempts ---------- */

$check = mysqli_query($conn,

"SELECT *

FROM quizAttempts

WHERE studentID='{$_SESSION['userID']}'

AND quizID='$quizID'");

if(mysqli_num_rows($check) > 0)
{
    header("Location: quizResult.php?id=".$quizID);
    exit();
}

/* ---------- Submit Quiz ---------- */

if(isset($_POST['submitQuiz']))
{
    $correct = 0;

    $totalQuestions = mysqli_num_rows(mysqli_query($conn,

    "SELECT *

    FROM questions

    WHERE quizID='$quizID'"));

    mysqli_query($conn,

    "INSERT INTO quizAttempts
    (
        studentID,
        quizID,
        score,
        totalQuestions,
        timeTaken
    )

    VALUES

    (
        '{$_SESSION['userID']}',
        '$quizID',
        0,
        '$totalQuestions',
        '".(int)$_POST['timeTaken']."'
    )");

    $attemptID = mysqli_insert_id($conn);

    $questions = mysqli_query($conn,

    "SELECT *

    FROM questions

    WHERE quizID='$quizID'

    ORDER BY questionID");

    while($question = mysqli_fetch_assoc($questions))
    {
        $studentAnswer = "";

        if(isset($_POST["question".$question['questionID']]))
        {
            $studentAnswer = trim($_POST["question".$question['questionID']]);
        }

        $isCorrect = 0;

        if(strcasecmp($studentAnswer,$question['correctAnswer']) == 0)
        {
            $isCorrect = 1;
            $correct++;
        }

        $studentAnswer = mysqli_real_escape_string($conn,$studentAnswer);

        mysqli_query($conn,

        "INSERT INTO attemptAnswers
        (
            attemptID,
            questionID,
            studentAnswer,
            isCorrect
        )

        VALUES

        (
            '$attemptID',
            '".$question['questionID']."',
            '$studentAnswer',
            '$isCorrect'
        )");

    }

    $score = round(($correct / $totalQuestions) * 100,2);

    mysqli_query($conn,

    "UPDATE quizAttempts

    SET score='$score'

    WHERE attemptID='$attemptID'");

    header("Location: quizResult.php?attempt=".$attemptID);
    exit();
}

?>

<h1><?php echo $quiz['title']; ?></h1>

<p>

Subject :
<?php echo $quiz['subjectCode']; ?>

</p>

<form method="POST" id="quizForm">

<input
type="hidden"
name="timeTaken"
id="timeTaken"
value="0">

<div id="timer">

Time :
<span id="clock">

00:00

</span>

</div>

<hr>

<?php

$questions = mysqli_query($conn,

"SELECT *

FROM questions

WHERE quizID='$quizID'

ORDER BY questionID");

$number = 1;

while($question = mysqli_fetch_assoc($questions))
{

?>

<h3>

<?php echo $number++; ?>.

<?php echo $question['questionText']; ?>

</h3>

<?php

if($question['questionType']=="MCQ")
{

?>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="A">

<?php echo $question['optionA']; ?>

</label>

<br>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="B">

<?php echo $question['optionB']; ?>

</label>

<br>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="C">

<?php echo $question['optionC']; ?>

</label>

<br>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="D">

<?php echo $question['optionD']; ?>

</label>

<?php

}
else if($question['questionType']=="TRUEFALSE")
{

?>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="True">

True

</label>

<br>

<label>

<input
type="radio"
name="question<?php echo $question['questionID']; ?>"
value="False">

False

</label>

<?php

}
else
{

?>

<input

type="text"

name="question<?php echo $question['questionID']; ?>"

placeholder="Your Answer"

>

<?php

}

?>

<hr>

<?php

}

?>

<button
type="submit"
name="submitQuiz">

Submit Quiz

</button>

</form>

<script>

let seconds = 0;

setInterval(function(){

    seconds++;

    document.getElementById("timeTaken").value = seconds;

    let min = Math.floor(seconds/60);

    let sec = seconds%60;

    document.getElementById("clock").innerHTML =
    String(min).padStart(2,'0') +
    ":" +
    String(sec).padStart(2,'0');

},1000);

</script>

<?php

include("footer.php");

?>