<?php

include("database.php");
include("header.php");
include("sidebar.php");

if($_SESSION['role'] != "Lecturer")
{
    header("Location: dashboard.php");
    exit();
}

if(!isset($_GET['quizID']))
{
    header("Location: quizList.php");
    exit();
}

$quizID = (int)$_GET['quizID'];

$query = mysqli_query($conn,
"SELECT *
FROM quizzes
WHERE quizID='$quizID'
AND lecturerID='{$_SESSION['userID']}'");

if(mysqli_num_rows($query) == 0)
{
    header("Location: quizList.php");
    exit();
}

$message = "";

if(isset($_POST['save']))
{
    $type = mysqli_real_escape_string($conn,$_POST['questionType']);
    $question = mysqli_real_escape_string($conn,$_POST['questionText']);

    $optionA = mysqli_real_escape_string($conn,$_POST['optionA']);
    $optionB = mysqli_real_escape_string($conn,$_POST['optionB']);
    $optionC = mysqli_real_escape_string($conn,$_POST['optionC']);
    $optionD = mysqli_real_escape_string($conn,$_POST['optionD']);

    $answer = mysqli_real_escape_string($conn,$_POST['correctAnswer']);
    $explanation = mysqli_real_escape_string($conn,$_POST['explanation']);

    mysqli_query($conn,

    "INSERT INTO questions
    (
        quizID,
        questionType,
        questionText,
        optionA,
        optionB,
        optionC,
        optionD,
        correctAnswer,
        explanation
    )

    VALUES

    (
        '$quizID',
        '$type',
        '$question',
        '$optionA',
        '$optionB',
        '$optionC',
        '$optionD',
        '$answer',
        '$explanation'
    )");

    header("Location: editQuiz.php?id=".$quizID);
    exit();
}

?>

<h1>Add Question</h1>

<form method="POST">

<label>Question Type</label>

<select
name="questionType"
id="questionType"
onchange="changeType()">

<option value="MCQ">Multiple Choice</option>

<option value="TRUEFALSE">True / False</option>

<option value="SHORTANSWER">Short Answer</option>

</select>

<label>Question</label>

<textarea
name="questionText"
rows="4"
required></textarea>

<div id="mcq">

<label>Option A</label>
<input type="text" name="optionA">

<label>Option B</label>
<input type="text" name="optionB">

<label>Option C</label>
<input type="text" name="optionC">

<label>Option D</label>
<input type="text" name="optionD">

<label>Correct Answer</label>

<select name="correctAnswer">

<option>A</option>
<option>B</option>
<option>C</option>
<option>D</option>

</select>

</div>

<div id="tf" style="display:none;">

<label>Correct Answer</label>

<select name="correctAnswerTF">

<option>True</option>

<option>False</option>

</select>

</div>

<div id="sa" style="display:none;">

<label>Correct Answer</label>

<input
type="text"
name="correctAnswerSA">

</div>

<label>Explanation</label>

<textarea
name="explanation"
rows="4"></textarea>

<br><br>

<button
type="submit"
name="save">

Save Question

</button>

<a href="editQuiz.php?id=<?php echo $quizID; ?>">

Cancel

</a>

</form>

<script>

function changeType()
{
    let type = document.getElementById("questionType").value;

    document.getElementById("mcq").style.display = "none";
    document.getElementById("tf").style.display = "none";
    document.getElementById("sa").style.display = "none";

    if(type == "MCQ")
    {
        document.getElementById("mcq").style.display = "block";
    }

    else if(type == "TRUEFALSE")
    {
        document.getElementById("tf").style.display = "block";
    }

    else
    {
        document.getElementById("sa").style.display = "block";
    }
}

document.querySelector("form").addEventListener("submit", function(){

    let type = document.getElementById("questionType").value;

    if(type == "TRUEFALSE")
    {
        document.querySelector("select[name='correctAnswer']").value =
        document.querySelector("select[name='correctAnswerTF']").value;
    }

    if(type == "SHORTANSWER")
    {
        document.querySelector("select[name='correctAnswer']").value =
        document.querySelector("input[name='correctAnswerSA']").value;
    }

});

</script>

<?php

include("footer.php");

?>