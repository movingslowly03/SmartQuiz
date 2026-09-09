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

$questionID = (int)$_GET['id'];

$query = mysqli_query($conn,

"SELECT questions.*, quizzes.quizID

FROM questions

JOIN quizzes
ON questions.quizID = quizzes.quizID

WHERE questionID='$questionID'

AND quizzes.lecturerID='{$_SESSION['userID']}'");

if(mysqli_num_rows($query) == 0)
{
    header("Location: quizList.php");
    exit();
}

$question = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['update']))
{
    $type = mysqli_real_escape_string($conn,$_POST['questionType']);
    $questionText = mysqli_real_escape_string($conn,$_POST['questionText']);

    $optionA = mysqli_real_escape_string($conn,$_POST['optionA']);
    $optionB = mysqli_real_escape_string($conn,$_POST['optionB']);
    $optionC = mysqli_real_escape_string($conn,$_POST['optionC']);
    $optionD = mysqli_real_escape_string($conn,$_POST['optionD']);

    if($type == "MCQ")
    {
        $answer = mysqli_real_escape_string($conn,$_POST['correctAnswer']);
    }
    else if($type == "TRUEFALSE")
    {
        $answer = mysqli_real_escape_string($conn,$_POST['correctAnswerTF']);
    }
    else
    {
        $answer = mysqli_real_escape_string($conn,$_POST['correctAnswerSA']);
    }

    $explanation = mysqli_real_escape_string($conn,$_POST['explanation']);

    mysqli_query($conn,

    "UPDATE questions

    SET

        questionType='$type',

        questionText='$questionText',

        optionA='$optionA',

        optionB='$optionB',

        optionC='$optionC',

        optionD='$optionD',

        correctAnswer='$answer',

        explanation='$explanation'

    WHERE questionID='$questionID'");

    header("Location: editQuiz.php?id=".$question['quizID']);
    exit();
}

?>

<h1>Edit Question</h1>

<form method="POST">

<label>Question Type</label>

<select
name="questionType"
id="questionType"
onchange="changeType()">

<option
value="MCQ"
<?php if($question['questionType']=="MCQ") echo "selected"; ?>>

Multiple Choice

</option>

<option
value="TRUEFALSE"
<?php if($question['questionType']=="TRUEFALSE") echo "selected"; ?>>

True / False

</option>

<option
value="SHORTANSWER"
<?php if($question['questionType']=="SHORTANSWER") echo "selected"; ?>>

Short Answer

</option>

</select>

<label>Question</label>

<textarea
name="questionText"
rows="4"
required><?php echo $question['questionText']; ?></textarea>

<div id="mcq">

<label>Option A</label>

<input
type="text"
name="optionA"
value="<?php echo $question['optionA']; ?>">

<label>Option B</label>

<input
type="text"
name="optionB"
value="<?php echo $question['optionB']; ?>">

<label>Option C</label>

<input
type="text"
name="optionC"
value="<?php echo $question['optionC']; ?>">

<label>Option D</label>

<input
type="text"
name="optionD"
value="<?php echo $question['optionD']; ?>">

<label>Correct Answer</label>

<select name="correctAnswer">

<option value="A" <?php if($question['correctAnswer']=="A") echo "selected"; ?>>A</option>

<option value="B" <?php if($question['correctAnswer']=="B") echo "selected"; ?>>B</option>

<option value="C" <?php if($question['correctAnswer']=="C") echo "selected"; ?>>C</option>

<option value="D" <?php if($question['correctAnswer']=="D") echo "selected"; ?>>D</option>

</select>

</div>

<div
id="tf"
style="display:none;">

<label>Correct Answer</label>

<select name="correctAnswerTF">

<option
value="True"
<?php if($question['correctAnswer']=="True") echo "selected"; ?>>

True

</option>

<option
value="False"
<?php if($question['correctAnswer']=="False") echo "selected"; ?>>

False

</option>

</select>

</div>

<div
id="sa"
style="display:none;">

<label>Correct Answer</label>

<input
type="text"
name="correctAnswerSA"
value="<?php

if($question['questionType']=="SHORTANSWER")
{
    echo $question['correctAnswer'];
}

?>">

</div>

<label>Explanation</label>

<textarea
name="explanation"
rows="4"><?php echo $question['explanation']; ?></textarea>

<br><br>

<button
type="submit"
name="update">

Update Question

</button>

<a href="editQuiz.php?id=<?php echo $question['quizID']; ?>">

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

changeType();

</script>

<?php

include("footer.php");

?>