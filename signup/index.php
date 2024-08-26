<?php
include '../db.php';

$nextExtension = ''; // Variable to hold the next available extension
$selectedTilde = ''; // Variable to hold the selected Tilde
$formSubmitted = false; // Variable to track if the form was submitted successfully

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tilde'])) {
    $selectedTilde = htmlspecialchars($_POST['tilde']);

    // Fetch the highest extension for the selected Tilde
    $sql = "SELECT MAX(extension) as max_extension FROM phonebook WHERE tilde_name='$selectedTilde'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Increment the extension by 1
        $nextExtension = strval(intval($row['max_extension']) + 1);
    } else {
        // Default extension if no records found
        $nextExtension = '0000001';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['extension'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = htmlspecialchars($_POST['username']);
    $tilde = htmlspecialchars($_POST['tilde']);
    $extension = htmlspecialchars($_POST['extension']);
    
    // Email content
    $subject = "tilde.tel account request";
    $message = "Email: $email\nUsername: $username\nTilde: $tilde\nExtension: $extension";
    $headers = "From: signup@tilde.tel\r\nReply-To: $email";

    // Send the email
    if (mail("signup@tilde.tel", $subject, $message, $headers)) {
        $formSubmitted = true;

        // Insert the new entry into the phonebook
        $stmt = $conn->prepare("INSERT INTO phonebook (tilde_name, extension, username) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $tilde, $extension, $username);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<p>There was an error sending your request. Please try again later.</p>";
    }
}

$sql = "SELECT DISTINCT tilde_name FROM phonebook ORDER BY tilde_name";
$result = $conn->query($sql);
?>
<html>
<head>
   <link rel="icon" href="https://tilde.tel/logo-300.png">
   <style>
      @font-face {
         font-family: 'DejaVu Sans';
         src: url('/https://tilde.tel/fonts/dejavu-sans-webfont.eot');
         src: url('/https://tilde.tel/fonts/dejavu-sans-webfont.eot#iefix') format('embedded-opentype'),
            url('/https://tilde.tel/fonts/dejavu-sans-webfont.woff2') format('woff2'),
            url('/https://tilde.tel/fonts/dejavu-sans-webfont.woff') format('woff'),
            url('/https://tilde.tel/fonts/dejavu-sans-webfont.ttf') format('truetype'),
            url('/https://tilde.tel/fonts/dejavu-sans-webfont.svg#dejavu_sansregular') format('svg');
      }
      html {
         width: 100%;
         height: 100%;
         background-color: #f0f0f0;
      }
      .splash {
         position: absolute;
         width: 400px;
         height: 400px;
         top: 38%;
         left: 50%;
         margin: -150px 0 0 -200px;
         font-family: 'DejaVu Sans', sans-serif;
         text-align: center;
      }
      .slogan {
         font-weight: bold;
         font-size: 30px;
         color: #ffa633;
         font-family: 'DejaVu Sans', sans-serif;
         text-align: center;
      }
      .banner {
         position: absolute;
         bottom: 10px;
         width: 468px;
         left: 50%;
         right: 50%;
         margin: 0px 0px 0px -234px;
         border: 4px solid #cacaca;
         background-color: #cacaca;
         font-family: 'DejaVu Sans', sans-serif;
         font-size: 9px;
         color: #f0f0f0;
      }
      .info {
         font-weight: bold;
         font-size: 17px;
         color: #FFA633;
      }
      .column {
         float: left;
      }
      .left {
         width: 111px;
         text-align: center;
      }
      .right {
         width: 639px;
         text-align: left;
         position: relative;
      }
      .form-container {
         margin: 20px auto;
         width: 70%;
         background-color: #ffffff;
         border: 2px solid #ffa633;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         font-family: 'DejaVu Sans', sans-serif;
         color: #333333;
      }
      .form-container label {
         font-weight: bold;
         font-size: 16px;
         color: #333333;
         display: block;
         margin-bottom: 5px;
      }
      .form-container input[type="text"],
      .form-container input[type="email"],
      .form-container select {
         width: 100%;
         padding: 10px;
         margin-bottom: 15px;
         border: 1px solid #cccccc;
         border-radius: 5px;
         font-size: 14px;
         font-family: 'DejaVu Sans', sans-serif;
      }
      .form-container input[type="submit"] {
         background-color: #ffa633;
         color: #ffffff;
         padding: 10px 20px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         font-size: 16px;
         font-family: 'DejaVu Sans', sans-serif;
      }
      .form-container input[type="submit"]:hover {
         background-color: #e69530;
      }
      .success-message {
         margin: 20px auto;
         width: 70%;
         background-color: #dff0d8;
         border: 2px solid #d6e9c6;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         font-family: 'DejaVu Sans', sans-serif;
         color: #3c763d;
         font-size: 18px;
         text-align: center;
      }
       .note {
         font-size: 14px;
         color: #777;
         margin-top: 10px;
         text-align: center;
      }
   </style>
   <title>~tel | Sign Up</title>
</head>
<body>

   <div class="row">
      <div class="column left">
         <a href="https://tilde.tel/"><img src="https://tilde.tel/logo-300.png" height="66px" width="66px" alt="tilde.tel logo"></a><br/>
         <br/>
         <br/>
         <a href="https://tilde.tel/faq.html"><img src="https://tilde.tel/faq-off.png" height="44px" width="44px" alt="FAQ"></a><br/>
         <br/>
         <br/>
         <a href="https://tilde.tel/phonebook.php"><img src="https://tilde.tel/phonebook-off.png" height="44px" width="44px" alt="Phonebook"></a><br/>
         <br/>
         <a href="https://connect.tilde.tel/ucp" target="_blank"><img src="https://tilde.tel/settings-off.png" height="44px" width="44px" alt="User Control Panel"></a>
         <br/>
         <br/>
         <a href="https://tilde.tel/signup"><img src="https://tilde.tel/signup-on.png" height="44px" width="44px" alt="Sign up"></a><br/>
      </div>
      <div class="column right">
         <div class="slogan">
            Sign up
         </div>
         <pre>
                Welcome to tilde.tel, excited to have you as a member! 
                
                   **See FAQ if you're unsure if you qualify to have an account**
                   
                   **If you were a previous tilde.tel user you can let us know if you would like your old extension.** 
                   
         </pre>
         
         <?php if ($formSubmitted): ?>
            <!-- Show success message -->
            <div class="success-message">
               <p>Thank you for signing up! We will get back to you shortly.</p>
            </div>
         <?php else: ?>
            <!-- Signup form -->
            <div class="form-container">
               <form action="" method="POST">
                  <label for="tilde">Tilde you're a member of:</label>
                  <select id="tilde" name="tilde" onchange="this.form.submit()" required>
                     <option value="" disabled selected>Select your Tilde</option>
                     <?php
                     if ($result->num_rows > 0) {
                         while($row = $result->fetch_assoc()) {
                             $selected = ($row['tilde_name'] == $selectedTilde) ? 'selected' : '';
                             echo "<option value='{$row['tilde_name']}' $selected>{$row['tilde_name']}</option>";
                         }
                     }
                     ?>
                  </select>
                  
                  <?php if (!empty($nextExtension)) : ?>
                  <p>Next available extension: <strong><?= $nextExtension ?></strong></p>
                  <input type="hidden" name="extension" value="<?= $nextExtension ?>">
                  <?php endif; ?>
                  
                  <label for="email">Email:</label>
                  <input type="email" id="email" name="email" required>
                  
                  <label for="username">Tilde Username:</label>
                  <input type="text" id="username" name="username" required>
                  
                  <?php if (!isset($_POST['tilde'])): ?>
                  <input type="submit" value="Continue">
                  <?php else: ?>
                  <input type="submit" value="Sign Up">
                  <?php endif; ?>
               </form>
              <p class="note">If your Tilde is not listed, please email the admin at <a href="mailto:deepend@tilde.tel">deepend@tilde.tel</a>.</p>
            </div>
         <?php endif; ?>
      </div>
   </div>
</body>
</html>

<?php $conn->close(); ?>
