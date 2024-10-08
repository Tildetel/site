<?php
include 'db.php';

// Fetch all entries from the phonebook table where username is not empty
$sql = "SELECT tilde_name, extension, username FROM phonebook WHERE username IS NOT NULL AND username != '' ORDER BY tilde_name, extension";
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
      }
      .row:after {
         content: "";
         display: table;
         clear: both;
      }
   </style>
   <title>~tel | the Orange Pages</title>
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
         <a href="https://tilde.tel/phonebook.php"><img src="https://tilde.tel/phonebook-on.png" height="44px" width="44px" alt="Phonebook"></a><br/>
                           <br/>
         <a href="https://connect.tilde.tel/ucp" target="_blank"><img src="https://tilde.tel/settings-off.png" height="44px" width="44px" alt="User Control Panel"></a>
         <br/>
         <br/>
         <a href="https://tilde.tel/signup"><img src="https://tilde.tel/signup-off.png" height="44px" width="44px" alt="Sign up"></a>
         <br/>
      </div>
      <div class="column right">
         <div class="slogan">
            the Orange Pages
         </div>
         <pre>
                                Service Numbers

[Extension]         [Service]
1101                Echo Test
1102                Music on Hold, courtesy of tilderadio.org
1104                Voicemail
1105                Conference bridge


                                  The 1900s (Currently unavailable)
                                 
                                           Coming soon!
                                           

[Extension]         [Contributor]       [Description]

1900s Hopefully returning soon!

                                ~Tel Community

[Tilde]             [Extension]         [User]
<br>
<?php
if ($result->num_rows > 0) {
    $currentTilde = '';
    while($row = $result->fetch_assoc()) {
        if ($currentTilde != $row['tilde_name']) {
            if ($currentTilde != '') {
                echo "<br>\n"; // Insert a line break between different Tildes
            }
            $currentTilde = $row['tilde_name'];
            echo $currentTilde . "\n";
        }
        echo str_pad('', 20) . str_pad($row['extension'], 20) . $row['username'] . "\n";
    }
} else {
    echo "No entries found.";
}
?>
<br>
         </pre>
      </div>
   </div>
</body>
</html>
<?php
$conn->close();
?>
