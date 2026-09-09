<?php

$role = $_SESSION['role'];

?>

<div id="sidebarOverlay" class="sidebar-overlay"></div>

<aside class="sidebar" id="sidebar">

    <ul>

        <li><a href="dashboard.php">Dashboard</a></li>

        <li><a href="profile.php">Profile</a></li>

        <?php if($role == "Admin") { ?>

            <li><a href="userList.php">User Management</a></li>
            <li><a href="subjectList.php">Subject Management</a></li>
            <li><a href="materialList.php">Study Materials</a></li>
            
            <li><a href="resultList.php">Quiz Results</a></li>
            <li><a href="analytics.php">Analytics</a></li>

        <?php } ?>

        <?php if($role == "Lecturer") { ?>

            <li><a href="uploadMaterial.php">Upload Material</a></li>
            <li><a href="materialList.php">My Materials</a></li>
            <li><a href="quizList.php">My Quizzes</a></li>
            <li><a href="resultList.php">Student Results</a></li>
            <li><a href="analytics.php">Student Performance</a></li>

        <?php } ?>

        <?php if($role == "Student") { ?>

            <li><a href="subjectList.php">Subjects</a></li>
            <li><a href="uploadMaterial.php">Upload Material</a></li>
            <li><a href="materialList.php">My Materials</a></li>
            <li><a href="quizList.php">Official & Practice Quizzes</a></li>
            <li><a href="resultList.php">My Results</a></li>
            <li><a href="analytics.php">My Performance</a></li>

        <?php } ?>

    </ul>

</aside>

<main class="content">
