<?php

include("database.php");
include("header.php");
include("sidebar.php");

$role = $_SESSION['role'];

?>

<div class="page-title">

    <div>

        <h1>Analytics Report</h1>

        <p>View and export SmartQuiz analytics.</p>

    </div>

    <div class="button-group">

        <button
            class="btn"
            onclick="printReport()"
            type="button"
        >
            🖨 Print / Save PDF
        </button>

    </div>

</div>

<?php

/* ===========================
   ADMIN ANALYTICS
=========================== */

if($role == "Admin")
{

    $totalUsers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
    $totalSubjects = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM subjects"));
    $totalMaterials = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM materials"));
    $totalQuizzes = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizzes"));
    $totalAttempts = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizAttempts"));

    $averageScore = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT AVG(score) AS averageScore FROM quizAttempts"));

?>

<div class="dashboard-grid">

    <div class="card"><h3>Total Users</h3><h1><?php echo $totalUsers; ?></h1></div>
    <div class="card"><h3>Total Subjects</h3><h1><?php echo $totalSubjects; ?></h1></div>
    <div class="card"><h3>Total Materials</h3><h1><?php echo $totalMaterials; ?></h1></div>
    <div class="card"><h3>Total Quizzes</h3><h1><?php echo $totalQuizzes; ?></h1></div>
    <div class="card"><h3>Total Attempts</h3><h1><?php echo $totalAttempts; ?></h1></div>
    <div class="card"><h3>Average Score</h3><h1><?php echo number_format($averageScore['averageScore'],2); ?>%</h1></div>

</div>

<?php
}
elseif($role=="Lecturer")
{

    $quizCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizzes WHERE ownerID='{$_SESSION['userID']}' AND ownerRole='Lecturer' AND quizType='Official'"));

    $attempts=mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) totalAttempts,
           AVG(score) averageScore,
           MAX(score) highestScore,
           MIN(score) lowestScore
    FROM quizAttempts
    JOIN quizzes ON quizAttempts.quizID=quizzes.quizID
    WHERE quizzes.ownerID='{$_SESSION['userID']}' AND quizzes.ownerRole='Lecturer' AND quizzes.quizType='Official'"));

    $popularQuiz=mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT quizzes.title,COUNT(*) total
    FROM quizAttempts
    JOIN quizzes ON quizAttempts.quizID=quizzes.quizID
    WHERE quizzes.ownerID='{$_SESSION['userID']}' AND quizzes.ownerRole='Lecturer' AND quizzes.quizType='Official'
    GROUP BY quizzes.quizID
    ORDER BY total DESC
    LIMIT 1"));
?>

<div class="dashboard-grid">

<div class="card"><h3>My Quizzes</h3><h1><?php echo $quizCount; ?></h1></div>
<div class="card"><h3>Total Attempts</h3><h1><?php echo $attempts['totalAttempts']; ?></h1></div>
<div class="card"><h3>Average Score</h3><h1><?php echo number_format($attempts['averageScore'],2); ?>%</h1></div>
<div class="card"><h3>Highest Score</h3><h1><?php echo $attempts['highestScore']; ?>%</h1></div>
<div class="card"><h3>Lowest Score</h3><h1><?php echo $attempts['lowestScore']; ?>%</h1></div>
<div class="card"><h3>Most Attempted Quiz</h3><p><?php echo $popularQuiz['title'] ?? "N/A"; ?></p></div>

</div>

<?php
}
else
{

$stats=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) totalAttempts,
AVG(score) averageScore,
MAX(score) highestScore,
MIN(score) lowestScore,
SUM(timeTaken) totalTime
FROM quizAttempts
WHERE studentID='{$_SESSION['userID']}'"));
?>

<div class="dashboard-grid">

<div class="card"><h3>Quizzes Attempted</h3><h1><?php echo $stats['totalAttempts']; ?></h1></div>
<div class="card"><h3>Average Score</h3><h1><?php echo number_format($stats['averageScore'],2); ?>%</h1></div>
<div class="card"><h3>Highest Score</h3><h1><?php echo $stats['highestScore']; ?>%</h1></div>
<div class="card"><h3>Lowest Score</h3><h1><?php echo $stats['lowestScore']; ?>%</h1></div>
<div class="card"><h3>Total Study Time</h3><h1><?php echo gmdate("H:i:s",$stats['totalTime']); ?></h1></div>

</div>

<?php } ?>

<script>
function printReport(){
    window.print();
}
</script>

<?php include("footer.php"); ?>
