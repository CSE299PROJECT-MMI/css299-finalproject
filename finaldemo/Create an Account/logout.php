<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <link rel="stylesheet" href="style3.css">
</head>

<body>
<div class="container">
    <h1>You have been logged out</h1>
    <p>Choose an option below:</p>
    <button onclick="window.location.href='login.php'">Login Again</button>
    <button onclick="window.location.href='../index.html';">Go to Home Page</button>
</div>

</body>
</html>
