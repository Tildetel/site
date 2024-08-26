<?php

require_once __DIR__ . '/vendor/autoload.php'; // Load Composer packages

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Check if the user's IP matches the allowed IP
$allowed_ip = $_ENV['ALLOWED_IP'];
$user_ip = $_SERVER['REMOTE_ADDR'];

if ($user_ip !== $allowed_ip) {
    // If IP address doesn't match, show an error message and exit
    header('HTTP/1.0 403 Forbidden');
    echo "Access denied.";
    exit;
}

include 'db.php';

// Handle deletion of an entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    
    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM phonebook WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Entry deleted successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Handle adding a new phonebook entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tilde_name']) && isset($_POST['extension']) && isset($_POST['username'])) {
    $tilde_name = htmlspecialchars($_POST['tilde_name']);
    $extension = htmlspecialchars($_POST['extension']);
    $username = htmlspecialchars($_POST['username']);
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO phonebook (tilde_name, extension, username) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $tilde_name, $extension, $username);
    
    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "New entry added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Handle sending the confirmation email
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_email'])) {
    $extension = htmlspecialchars($_POST['extension']);
    $pbxPassword = htmlspecialchars($_POST['pbx_password']);
    $ucpPassword = htmlspecialchars($_POST['ucp_password']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Prepare the email content
    $subject = "Your tilde.tel Account Details";
    $message = "Here are your account details:\n\n" .
               "For VOIP Application:\n\n" .
               "Extension: $extension\n" .
               "Password: $pbxPassword\n" .
               "Server name: connect.tilde.tel\n" .
               "Port: 5060 (UDP)\n" .
               "Voicemail Password: Your extension number is your temporary password. Please dial the voicemail to change this.\n\n" .
               "For User Control Panel:\n\n" .
               "Username: $extension\n" .
               "Password: $ucpPassword\n" .
               "Access control panel at:\n\n" .
               "https://connect.tilde.tel/ucp\n\n" .
               "Thanks,\n\n~deepend";

    $headers = "From: no-reply@tilde.tel\r\n";
    $headers .= "Cc: signup@tilde.tel\r\n";

    // Send the email
    if (mail($email, $subject, $message, $headers)) {
        $success_message = "Email sent successfully to $email!";
    } else {
        $error_message = "Error sending email to $email.";
    }
}

// Fetch all entries with a username
$sql = "SELECT id, tilde_name, extension, username FROM phonebook WHERE username IS NOT NULL AND username != '' ORDER BY tilde_name, extension";
$users_result = $conn->query($sql);

// Fetch all pending entries (without a username)
$sql = "SELECT id, tilde_name, extension FROM phonebook WHERE username IS NULL OR username = '' ORDER BY tilde_name, extension";
$pending_result = $conn->query($sql);

// Determine which section to show based on the query parameter
$section = isset($_GET['section']) ? $_GET['section'] : 'users';
?>

<html>
<head>
    <link rel="icon" href="https://tilde.tel/logo-300.png">
    <style>
        .container {
            margin: 20px auto;
            width: 80%;
            font-family: 'DejaVu Sans', sans-serif;
            color: #333333;
        }
        .container h2 {
            color: #ffa633;
            text-align: center;
        }
        .nav-menu {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-menu a {
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
            color: #ffa633;
        }
        .nav-menu a.active {
            font-weight: bold;
            text-decoration: underline;
        }
        .list-container {
            margin-bottom: 40px;
            background-color: #ffffff;
            border: 2px solid #ffa633;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .collapsible {
            background-color: #ffa633;
            color: white;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .collapsible:after {
            content: '\002B'; /* Plus symbol */
            font-size: 13px;
            color: white;
            float: right;
            margin-left: 5px;
        }
        .collapsible.active:after {
            content: "\2212"; /* Minus symbol */
        }
        .content {
            padding: 0 20px;
            display: none;
            overflow: hidden;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #cccccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ffa633;
            color: white;
        }
        .form-container {
            margin: 20px auto;
            width: 50%;
            background-color: #ffffff;
            border: 2px solid #ffa633;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            font-weight: bold;
            font-size: 16px;
            color: #333333;
            display: block;
            margin-bottom: 5px;
        }
        .form-container input[type="text"],
        .form-container input[type="password"],
        .form-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container input[type="submit"] {
            background-color: #ffa633;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container input[type="submit"]:hover {
            background-color: #e69530;
        }
        .message {
            font-size: 16px;
            color: green;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
        .error {
            font-size: 16px;
            color: red;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }
    </style>
    <title>Admin - Phonebook Management</title>
</head>
<body>
    <div class="container">
        <h2>Phonebook Management</h2>

        <?php
        if (isset($success_message)) {
            echo "<p class='message'>$success_message</p>";
        }
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>

        <div class="nav-menu">
            <a href="?section=users" class="<?= $section == 'users' ? 'active' : '' ?>">Existing Users</a>
            <a href="?section=pending" class="<?= $section == 'pending' ? 'active' : '' ?>">Pending Entries</a>
            <a href="?section=add" class="<?= $section == 'add' ? 'active' : '' ?>">Add New Entry</a>
            <a href="?section=send_email" class="<?= $section == 'send_email' ? 'active' : '' ?>">Send Confirmation Email</a>
        </div>

        <?php if ($section == 'users'): ?>
        <div class="list-container">
            <h3>Existing Users</h3>
            <?php
            if ($users_result->num_rows > 0) {
                $currentTilde = '';
                while($row = $users_result->fetch_assoc()) {
                    if ($currentTilde != $row['tilde_name']) {
                        if ($currentTilde != '') {
                            echo "</table></div>"; // Close previous content div and table
                        }
                        $currentTilde = $row['tilde_name'];
                        echo "<button class='collapsible'>$currentTilde</button>";
                        echo "<div class='content'>";
                        echo "<table>
                                <tr>
                                    <th>Extension</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                </tr>";
                    }
                    echo "<tr>";
                    echo "<td>{$row['extension']}</td>";
                    echo "<td>{$row['username']}</td>";
                    echo "<td>
                        <form action='' method='POST'>
                            <button type='submit' name='delete' value='{$row['id']}'>Delete</button>
                        </form>
                      </td>";
                    echo "</tr>";
                }
                echo "</table></div>"; // Close the last table and content div
            } else {
                echo "<p>No users found.</p>";
            }
            ?>
        </div>
        <?php elseif ($section == 'pending'): ?>
        <div class="list-container">
            <h3>Pending Entries (No Username)</h3>
            <table>
                <tr>
                    <th>Tilde Name</th>
                    <th>Extension</th>
                    <th>Action</th>
                </tr>
                <?php
                if ($pending_result->num_rows > 0) {
                    while($row = $pending_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['tilde_name']}</td>";
                        echo "<td>{$row['extension']}</td>";
                        echo "<td>
                            <form action='' method='POST'>
                                <button type='submit' name='delete' value='{$row['id']}'>Delete</button>
                            </form>
                          </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No pending entries.</td></tr>";
                }
                ?>
            </table>
        </div>
        <?php elseif ($section == 'add'): ?>
        <div class="form-container">
            <h3>Add a New Phonebook Entry</h3>
            <form action="" method="POST">
                <label for="tilde_name">Tilde Name:</label>
                <input type="text" id="tilde_name" name="tilde_name" required>
                
                <label for="extension">Extension:</label>
                <input type="text" id="extension" name="extension" required>
                
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                
                <input type="submit" value="Add Entry">
            </form>
        </div>
        <?php elseif ($section == 'send_email'): ?>
        <div class="form-container">
            <h3>Send Signup Confirmation Email</h3>
            <form action="" method="POST">
                <label for="extension">Extension:</label>
                <input type="text" id="extension" name="extension" required>
                
                <label for="pbx_password">PBX Password:</label>
                <input type="password" id="pbx_password" name="pbx_password" required>
                
                <label for="ucp_password">UCP Password:</label>
                <input type="password" id="ucp_password" name="ucp_password" required>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
                
                <input type="submit" name="send_email" value="Send Confirmation Email">
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Collapsible functionality
        var coll = document.getElementsByClassName("collapsible");
        for (var i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.display === "block") {
                    content.style.display = "none";
                } else {
                    content.style.display = "block";
                }
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
