<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Lecturer")
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
AND quizzes.ownerID='{$_SESSION['userID']}'
AND quizzes.ownerRole='Lecturer'
AND quizzes.quizType='Official'");

if(mysqli_num_rows($query) == 0)
{
    header("Location: quizList.php");
    exit();
}

$quiz = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['update']))
{
    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $difficulty = mysqli_real_escape_string($conn,$_POST['difficulty']);

    mysqli_query($conn,

    "UPDATE quizzes

    SET

    title='$title',

    difficulty='$difficulty'

    WHERE quizID='$quizID'");

    $message = "Quiz updated successfully.";

    $query = mysqli_query($conn,

    "SELECT quizzes.*, subjects.subjectCode

    FROM quizzes

    JOIN subjects
    ON quizzes.subjectID = subjects.subjectID

    WHERE quizzes.quizID='$quizID'");

    $quiz = mysqli_fetch_assoc($query);
}

if(isset($_GET['deleteQuestion']))
{
    $questionID = (int)$_GET['deleteQuestion'];

    mysqli_query($conn,

    "DELETE FROM questions

    WHERE questionID='$questionID'

    AND quizID='$quizID'");

    header("Location: editQuiz.php?id=".$quizID);
    exit();
}

?>

<h1>Edit Quiz</h1>

<?php

if($message != "")
{
    echo "<p class='message'>$message</p>";
}

?>

<form method="POST">

<label>Quiz Title</label>

<input
type="text"
name="title"
value="<?php echo $quiz['title']; ?>"
required>

<label>Subject</label>

<input
type="text"
value="<?php echo $quiz['subjectCode']; ?>"
readonly>

<label>Difficulty</label>

<select name="difficulty">

<option
value="Easy"
<?php if($quiz['difficulty']=="Easy") echo "selected"; ?>
>

Easy

</option>

<option
value="Medium"
<?php if($quiz['difficulty']=="Medium") echo "selected"; ?>
>

Medium

</option>

<option
value="Hard"
<?php if($quiz['difficulty']=="Hard") echo "selected"; ?>
>

Hard

</option>

<option
value="Mixed"
<?php if($quiz['difficulty']=="Mixed") echo "selected"; ?>
>

Mixed

</option>

</select>

<br><br>

<button
type="submit"
name="update">

Save Changes

</button>

</form>

<hr>

<h2>Questions</h2>

<a href="addQuestion.php?quizID=<?php echo $quizID; ?>">

Add Question

</a>

<br><br>

<table>

<tr>

<th>#</th>

<th>Type</th>

<th>Question</th>

<th>Answer</th>

<th>Action</th>

</tr>

<?php

$questions = mysqli_query($conn,

"SELECT *

FROM questions

WHERE quizID='$quizID'

ORDER BY questionID ASC");

$number = 1;

while($question = mysqli_fetch_assoc($questions))
{

?>

<tr>

<td>

<?php echo $number++; ?>

</td>

<td>

<?php echo $question['questionType']; ?>

</td>

<td>

<?php echo $question['questionText']; ?>

</td>

<td>

<?php echo $question['correctAnswer']; ?>

</td>

<td>

<a href="editQuestion.php?id=<?php echo $question['questionID']; ?>">

Edit

</a>

|

<a

href="editQuiz.php?id=<?php echo $quizID; ?>&deleteQuestion=<?php echo $question['questionID']; ?>"

onclick="return confirm('Delete this question?')">

Delete

</a>

</td>

</tr>

<?php

}

?>

</table>

<br>

<a href="quizList.php" class="btn">

Back

</a>

<?php

include("footer.php");

?>