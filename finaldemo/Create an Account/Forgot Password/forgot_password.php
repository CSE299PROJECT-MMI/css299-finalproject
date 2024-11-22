<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "hospital_management");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $errorMessage = '';
    $successMessage = '';

    // Check if email exists in any table
    $tables = ['users', 'doctors', 'admins'];
    $userFound = false;
    foreach ($tables as $table) {
        $sql = "SELECT * FROM $table WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userFound = true;
            $row = $result->fetch_assoc();

            // Generate reset token and expiry
            $resetToken = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $updateSql = "UPDATE $table SET reset_token=?, token_expiry=? WHERE email=?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("sss", $resetToken, $expiry, $email);
            $updateStmt->execute();

            // Send reset link to email
            $resetLink = "http://yourwebsite.com/reset_password.php?token=$resetToken";
            mail($email, "Password Reset", "Click the link to reset your password: $resetLink");

            $successMessage = "A password reset link has been sent to your email.";
            break;
        }
    }

    if (!$userFound) {
        $errorMessage = "Email not found!";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <form method="post" action="">
        <h2>Forgot Password</h2>
        <label for="email">Enter your registered email:</label>
        <input type="email" id="email" name="email" required>
        <button type="submit">Send Reset Link</button>
        <button type="submit"><a href="../login.php">signup</a></button>

    </form>
    <?php if (!empty($errorMessage)) echo "<p style='color:red;'>$errorMessage</p>"; ?>
    <?php if (!empty($successMessage)) echo "<p style='color:green;'>$successMessage</p>"; ?>
</body>
</html>
