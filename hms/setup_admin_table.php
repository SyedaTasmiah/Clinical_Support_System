<?php
// Simple admin table setup script
// Run this once to create the admin table and default admin user

include('include/config.php');

echo "<h2>Setting up Admin Table...</h2>";

// Test database connection
if(mysqli_connect_errno()) {
    echo "<p style='color: red;'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    exit();
} else {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
}

// Test if we can query the database
$test_query = mysqli_query($con, "SELECT 1");
if($test_query === false) {
    echo "<p style='color: red;'>✗ Database query test failed: " . mysqli_error($con) . "</p>";
    exit();
} else {
    echo "<p style='color: green;'>✓ Database query test successful!</p>";
}

// Create admin table
$create_table_sql = "
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

$create_result = mysqli_query($con, $create_table_sql);
if($create_result === false) {
    echo "<p style='color: red;'>✗ Error creating admin table: " . mysqli_error($con) . "</p>";
    echo "<p style='color: orange;'>SQL Error Code: " . mysqli_errno($con) . "</p>";
} else {
    echo "<p style='color: green;'>✓ Admin table created successfully!</p>";
}

// Check if admin table exists
$check_table = mysqli_query($con, "SHOW TABLES LIKE 'admin'");
if($check_table === false) {
    echo "<p style='color: red;'>✗ Error checking admin table: " . mysqli_error($con) . "</p>";
} else if(mysqli_num_rows($check_table) > 0) {
    echo "<p style='color: green;'>✓ Admin table exists!</p>";
    
    // Check if admin user exists
    $check_admin = mysqli_query($con, "SELECT * FROM admin WHERE username='admin'");
    if($check_admin === false) {
        echo "<p style='color: red;'>✗ Error checking admin user: " . mysqli_error($con) . "</p>";
    } else if(mysqli_num_rows($check_admin) == 0) {
        // Insert default admin user
        $insert_admin_sql = "
        INSERT INTO `admin` (`username`, `password`) VALUES
        ('admin', 'admin123')
        ";
        
        $insert_result = mysqli_query($con, $insert_admin_sql);
        if($insert_result === false) {
            echo "<p style='color: red;'>✗ Error creating admin user: " . mysqli_error($con) . "</p>";
            echo "<p style='color: orange;'>SQL Error Code: " . mysqli_errno($con) . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Default admin user created!</p>";
            echo "<p><strong>Default credentials:</strong></p>";
            echo "<p>Username: <strong>admin</strong></p>";
            echo "<p>Password: <strong>admin123</strong></p>";
            echo "<p style='color: orange;'>⚠️ IMPORTANT: Change these credentials after first login!</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Admin user already exists!</p>";
    }
    
    // Show current admin users
    $admin_users = mysqli_query($con, "SELECT id, username FROM admin");
    if($admin_users === false) {
        echo "<p style='color: red;'>✗ Error querying admin users: " . mysqli_error($con) . "</p>";
    } else if(mysqli_num_rows($admin_users) > 0) {
        echo "<h3>Current Admin Users:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th></tr>";
        while($admin = mysqli_fetch_array($admin_users)) {
            echo "<tr>";
            echo "<td>" . $admin['id'] . "</td>";
            echo "<td>" . $admin['username'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Admin table does not exist!</p>";
}

echo "<hr>";
echo "<p><a href='admin/index.php'>Go to Admin Login</a></p>";
echo "<p><a href='admin/manage-users.php'>Go to Manage Users</a></p>";
echo "<p><strong>Note:</strong> After setting up the admin table, you can log in with the default credentials and test the export functionality.</p>";

// If table creation failed, show manual SQL option
if(isset($create_result) && $create_result === false) {
    echo "<hr>";
    echo "<h3>Manual Setup Option</h3>";
    echo "<p>If the automatic setup failed, you can manually run this SQL in phpMyAdmin:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars($create_table_sql);
    echo "</pre>";
    echo "<p>Then run this to create the admin user:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo "INSERT INTO `admin` (`username`, `password`) VALUES ('admin', 'admin123');";
    echo "</pre>";
}
?>
