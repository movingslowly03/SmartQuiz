<?php

include("database.php");
include("header.php");
include("sidebar.php");

$role = $_SESSION['role'];

?>

<div class="content-wrapper">

<div class="page-title">

    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
    </div>

</div>

<?php

if($role=="Admin")
{
    $userCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
    $subjectCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM subjects"));
    $materialCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM materials"));
    $quizCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizzes"));
?>

<div class="dashboard-grid">
    <div class="card"><h3>Total Users</h3><h1><?php echo $userCount; ?></h1></div>
    <div class="card"><h3>Subjects</h3><h1><?php echo $subjectCount; ?></h1></div>
    <div class="card"><h3>Materials</h3><h1><?php echo $materialCount; ?></h1></div>
    <div class="card"><h3>Quizzes</h3><h1><?php echo $quizCount; ?></h1></div>
</div>

<div class="section"></div>

<h2>Quick Actions</h2>

<div class="dashboard-grid">
    <a class="card" href="userList.php">Manage Users</a>
    <a class="card" href="subjectList.php">Manage Subjects</a>
    <a class="card" href="materialList.php">Study Materials</a>
    <a class="card" href="quizList.php">Quiz Management</a>
</div>

<?php
}
elseif($role=="Lecturer")
{
    $materialCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM materials WHERE ownerID='{$_SESSION['userID']}' AND ownerRole='Lecturer'"));
    $quizCount=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizzes WHERE ownerID='{$_SESSION['userID']}' AND ownerRole='Lecturer' AND quizType='Official'"));
?>

<div class="dashboard-grid">
    <div class="card"><h3>My Materials</h3><h1><?php echo $materialCount; ?></h1></div>
    <div class="card"><h3>My Quizzes</h3><h1><?php echo $quizCount; ?></h1></div>
</div>

<div class="section"></div>

<h2>Quick Actions</h2>

<div class="dashboard-grid">
    <a class="card" href="materialList.php">Study Materials</a>
    <a class="card" href="uploadMaterial.php">Upload Material</a>
    <a class="card" href="quizList.php">My Quizzes</a>
    <a class="card" href="resultList.php">Student Results</a>
</div>

<?php
}
else
{
    $availableQuiz=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizzes WHERE status='Published' AND quizType='Official'"));
    $attemptedQuiz=mysqli_num_rows(mysqli_query($conn,"SELECT * FROM quizAttempts WHERE studentID='{$_SESSION['userID']}'"));
?>

<div class="dashboard-grid">
    <div class="card"><h3>Available Quizzes</h3><h1><?php echo $availableQuiz; ?></h1></div>
    <div class="card"><h3>Completed Quizzes</h3><h1><?php echo $attemptedQuiz; ?></h1></div>
</div>

<div class="section"></div>

<h2>Quick Actions</h2>

<div class="dashboard-grid">
    <a class="card" href="materialList.php">My Materials</a>
    <a class="card" href="quizList.php">Official Quizzes</a>
    <a class="card" href="uploadMaterial.php">Upload Material</a>
    <a class="card" href="resultList.php">View Results</a>
    <a class="card" href="analytics.php">My Performance</a>
</div>

<?php } ?>

</div>

</main>

<?php include("footer.php"); ?>
