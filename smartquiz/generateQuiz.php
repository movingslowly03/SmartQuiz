<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors',1);

include("database.php");
include("gemini.php");
include("extractText.php");

if(!isset($_SESSION['userID']) || ($_SESSION['role']!="Lecturer" && $_SESSION['role']!="Student")){
    header("Location: dashboard.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: materialList.php");
    exit();
}

$materialID=(int)$_GET['id'];

$sql="SELECT materials.*,subjects.subjectCode,subjects.subjectName
      FROM materials
      JOIN subjects ON materials.subjectID=subjects.subjectID
      WHERE materials.materialID='$materialID'
      AND materials.ownerID='{$_SESSION['userID']}'
      AND materials.ownerRole='{$_SESSION['role']}'";

$query=mysqli_query($conn,$sql);

if(!$query || mysqli_num_rows($query)==0){
    header("Location: materialList.php");
    exit();
}

$material=mysqli_fetch_assoc($query);
$message="";

if(isset($_POST['generate'])){

    $questionCount=(int)$_POST['questionCount'];
    $difficulty=mysqli_real_escape_string($conn,$_POST['difficulty']);

    $filePath="images/uploads/".$material['fileName'];

    if(!file_exists($filePath)){
        $message="Uploaded file could not be found.";
    }else{

        $documentText=extractText($filePath,$material['fileType']);

        if(empty(trim($documentText))){
            $message="Unable to extract text from the document.";
        }else{

            $result=generateQuiz($documentText,$questionCount,$difficulty);

            if(!$result["success"]){
                $message=$result["error"];
            }else{

                $questions=$result["questions"];

                mysqli_query($conn,"
                INSERT INTO quizzes
                (title,difficulty,generatedBy,ownerID,ownerRole,quizType,status,materialID,subjectID)
                VALUES
                (
                    '".mysqli_real_escape_string($conn,$material['title'])." Quiz',
                    '$difficulty',
                    'AI',
                    '{$_SESSION['userID']}',
                    '{$_SESSION['role']}',
                    '".($_SESSION['role']=="Lecturer" ? "Official" : "Practice")."',
                    '".($_SESSION['role']=="Lecturer" ? "Draft" : "Published")."',
                    '$materialID',
                    '".$material['subjectID']."'
                )");

                if(mysqli_errno($conn)){
                    $message=mysqli_error($conn);
                }else{

                    $quizID=mysqli_insert_id($conn);

                    foreach($questions as $q){

                        mysqli_query($conn,"
                        INSERT INTO questions
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
                            '".mysqli_real_escape_string($conn,$q['type'] ?? 'MCQ')."',
                            '".mysqli_real_escape_string($conn,$q['question'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['optionA'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['optionB'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['optionC'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['optionD'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['answer'] ?? '')."',
                            '".mysqli_real_escape_string($conn,$q['explanation'] ?? '')."'
                        )");
                    }

                    mysqli_query($conn,"
                    UPDATE materials
                    SET aiStatus='Generated'
                    WHERE materialID='$materialID'");

                    if($_SESSION['role']=="Student"){
                        header("Location: attemptQuiz.php?id=".$quizID);
                    }else{
                        header("Location: editQuiz.php?id=".$quizID);
                    }
                    exit();
                }
            }
        }
    }
}

include("header.php");
include("sidebar.php");
?>

<div class="page">
<div class="page-title">
<h1>Generate AI Quiz</h1>
<p>Generate quiz questions from the selected study material.</p>
</div>

<?php if($message!=""){ ?>
<div class="error"><?php echo $message; ?></div>
<?php } ?>

<div class="card">
<table>
<tr><th>Title</th><td><?php echo htmlspecialchars($material['title']); ?></td></tr>
<tr><th>Subject</th><td><?php echo htmlspecialchars($material['subjectCode']." - ".$material['subjectName']); ?></td></tr>
<tr><th>File</th><td><?php echo htmlspecialchars($material['fileName']); ?></td></tr>
<tr><th>Type</th><td><?php echo htmlspecialchars($material['fileType']); ?></td></tr>
</table>
</div>

<div class="card">
<form method="POST">
<label>Questions</label>
<select name="questionCount">
<option value="5">5</option>
<option value="10" selected>10</option>
<option value="15">15</option>
<option value="20">20</option>
</select>

<label>Difficulty</label>
<select name="difficulty">
<option>Easy</option>
<option selected>Medium</option>
<option>Hard</option>
<option>Mixed</option>
</select>

<br><br>

<button type="submit" name="generate">Generate with Gemini AI</button>
<a href="materialList.php" class="btn">Cancel</a>

</form>
</div>

<?php include("footer.php"); ?>
