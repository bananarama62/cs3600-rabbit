<?php
include 'db_connection.php';

$message = "";
$error = 1;

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $password = password_hash($password, PASSWORD_DEFAULT);

  // Check if username already exists
  $checkEmailStmt = $conn->prepare("SELECT username FROM login where username = ?");
  $checkEmailStmt->bind_param("s",$username);
  $checkEmailStmt->execute();
  $checkEmailStmt->store_result();

  if ($checkEmailStmt->num_rows > 0){
    $message = "Username is not available";
  } else {
    $stmt = $conn->prepare("INSERT INTO login (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss",$username,$password);

    if($stmt->execute()){
      $message = "Account created successfully. You will be redirected soon.";
      $error = 0;
      header("refresh:5;url=../todo.php");
      session_start();
      $_SESSION['user'] = $id;
      $_SESSION['username'] = $user;
    } else {
      $message = "Error: ".$stmt->error;
    }
    $stmt->close();
  }
  $checkEmailStmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cookies</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../JS/title.js"></script>
    <script src="../JS/message.js"></script>
  </head>
  <body>
    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div>
      <div class="navigation-head">
        <div class="site-logo">
          <a href="../index.php">
            <h1>Tasks</h1>
          </a>
        </div>
        <ul class="navigation-menu">
          <?php
          session_start();
          // Check if user is logged in, if not redirect to login page
          if (!isset($_SESSION['username'])) {
            echo '<a href="./login.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Login</li>';
            echo '</a>';
            echo '<a href="./register.php" class="menu-item active">';
              echo '<li class="underline-hover-effect">Register</li>';
            echo '</a>';
          } else {
            echo '<a href="../todo.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Tasks</li>';
            echo '</a>';
            echo '<a href="./logout.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Logout</li>';
            echo '</a>';
            echo '<a href="./delete_account.php" class="menu-item">';
              echo '<li class="underline-hover-effect error">Delete Account</li>';
            echo '</a>';
          }
          ?>
        </ul>
      </div>
      <hr id="head-rule">
    </div>
    <div class="breadcrumbs">
      <a href="../index.php">home</a>
      <p>></p>
      <p>register</p>
    </div>
    <div class="content">
      <h1>Register</h1>
      <div id="submission-message-holder"><p></p></div>
      <?php
      session_start();
      // Lets user know they are already logged in

      if (!isset($_SESSION['username'])) {
        echo '<form method="post" class="form-control">';
          echo '<div>';
            echo '<label for="username">User Name: </label>';
            echo '<input type="text" name="username" id="username" class="text-input-small" required>';
          echo '</div>';
          echo '<div>';
            echo '<label for="password">Password: </label>';
            echo '<input type="password" name="password" id="password" class="text-input-small" required>';
          echo '</div>';
          echo '<div>';
            echo '<button type="submit" name="register" class="submit-button">Submit</button>';
          echo '</div>';
        echo '</form>';
      } else {
        echo "<p>";
          echo "You are already logged in. Would you like to ";
          echo "<a href='./logout.php'>";
            echo "logout";
          echo "</a>";
          echo "?";
        echo "</p>";
        echo "<p>";
          echo "Go to ";
          echo "<a href='../todo.php'>";
            echo "Task dashboard";
          echo "</a>";
        echo "</p>";
      }
      if(isset($message) && !empty($message)){
        echo '<script>submissionMessage("'.$message.'",'.$error.');</script>';
      }
      ?>
    </div>
    <script src="" async defer></script>
    <hr id="foot-rule">
  </body>
  <footer>
    <div class="split-items">
      <p>Last updated: <span>21 October 2025</span></p>
      <p>Author: Josh Gillum</p>
    </div>
    <div class="split-items">
      <a href="./cookies.html">cookies</a>
      <a href="./privacy.html">privacy policy</a>
      <a href="./terms.html">terms and conditions</a>
    </div>
  </footer>
</html>
