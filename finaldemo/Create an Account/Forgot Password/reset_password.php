<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "hospital_management");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $tables = ['users', 'doctors', 'admins'];
    $passwordUpdated = false;

    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE reset_token=? AND token_expiry > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update password and clear token
            $updateSql = "UPDATE $table SET password=?, reset_token=NULL, token_expiry=NULL WHERE reset_token=?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $hashedPassword, $token);
            $updateStmt->execute();

            $passwordUpdated = true;
            break;
        }
    }

    if ($passwordUpdated) {
        echo "Password updated successfully. <a href='login.php'>Login</a>";
    } else {
        echo "Invalid or expired token.";
    }

    $conn->close();
} else {
    $token = $_GET['token'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <form method="post" action="">
        <h2>Reset Password</h2>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
