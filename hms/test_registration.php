<?php
include_once('include/config.php');

echo "<h2>Patient Registration Debug</h2>";

// Check database connection
if ($con) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit();
}

// Check if users table exists
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($table_check) > 0) {
    echo "<p style='color: green;'>✅ Users table exists</p>";
    
    // Check table structure
    $structure = mysqli_query($con, "DESCRIBE users");
    if ($structure) {
        echo "<h3>Users Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ Users table does not exist</p>";
}

// Test a simple insert
echo "<h3>Test Registration:</h3>";
if (isset($_POST['test_submit'])) {
    $fname = mysqli_real_escape_string($con, $_POST['test_name']);
    $address = mysqli_real_escape_string($con, $_POST['test_address']);
    $city = mysqli_real_escape_string($con, $_POST['test_city']);
    $gender = mysqli_real_escape_string($con, $_POST['test_gender']);
    $email = mysqli_real_escape_string($con, $_POST['test_email']);
    $password = md5($_POST['test_password']);
    
    echo "<p><strong>Test Data:</strong></p>";
    echo "<ul>";
    echo "<li>Name: " . htmlspecialchars($fname) . "</li>";
    echo "<li>Address: " . htmlspecialchars($address) . "</li>";
    echo "<li>City: " . htmlspecialchars($city) . "</li>";
    echo "<li>Gender: " . htmlspecialchars($gender) . "</li>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Password: " . substr($password, 0, 10) . "...</li>";
    echo "</ul>";
    
    $test_query = "INSERT INTO users(fullname,address,city,gender,email,password) VALUES('$fname','$address','$city','$gender','$email','$password')";
    
    echo "<p><strong>SQL Query:</strong></p>";
    echo "<pre>" . htmlspecialchars($test_query) . "</pre>";
    
    $result = mysqli_query($con, $test_query);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Test registration successful!</p>";
        echo "<p>New user ID: " . mysqli_insert_id($con) . "</p>";
        
        // Clean up test data
        $delete_query = "DELETE FROM users WHERE email = '$email'";
        mysqli_query($con, $delete_query);
        echo "<p style='color: blue;'>ℹ️ Test data cleaned up</p>";
    } else {
        echo "<p style='color: red;'>❌ Test registration failed!</p>";
        echo "<p>Error: " . mysqli_error($con) . "</p>";
        echo "<p>Error Number: " . mysqli_errno($con) . "</p>";
    }
}

// Check for any existing users
$user_count = mysqli_query($con, "SELECT COUNT(*) as count FROM users");
if ($user_count) {
    $count_data = mysqli_fetch_assoc($user_count);
    echo "<p><strong>Total users in database:</strong> " . $count_data['count'] . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 120px; font-weight: bold; }
        input, select { width: 250px; padding: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <form method="post" action="">
        <h3>Test Registration Form</h3>
        
        <div class="form-group">
            <label for="test_name">Name:</label>
            <input type="text" id="test_name" name="test_name" value="Test User" required>
        </div>
        
        <div class="form-group">
            <label for="test_address">Address:</label>
            <input type="text" id="test_address" name="test_address" value="123 Test St" required>
        </div>
        
        <div class="form-group">
            <label for="test_city">City:</label>
            <input type="text" id="test_city" name="test_city" value="Test City" required>
        </div>
        
        <div class="form-group">
            <label for="test_gender">Gender:</label>
            <select id="test_gender" name="test_gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="test_email">Email:</label>
            <input type="email" id="test_email" name="test_email" value="test@example.com" required>
        </div>
        
        <div class="form-group">
            <label for="test_password">Password:</label>
            <input type="password" id="test_password" name="test_password" value="testpass123" required>
        </div>
        
        <button type="submit" name="test_submit">Test Registration</button>
    </form>
    
    <hr>
    <p><a href="registration.php">Go to Patient Registration Page</a></p>
    <p><a href="user-login.php">Go to Patient Login Page</a></p>
    <p><a href="../index.php">Go to Home Page</a></p>
</body>
</html>
