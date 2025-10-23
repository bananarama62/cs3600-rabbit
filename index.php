<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Task Tracker</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./CSS/style.css">
    <script src="./JS/title.js"></script>
  </head>
  <body>
    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div>
      <div class="navigation-head">
        <div class="site-logo">
          <a href="./index.php">
            <h1>Tasks</h1>
          </a>
        </div>
        <ul class="navigation-menu">
          <?php
          session_start();
          // Check if user is logged in, if not redirect to login page
          if (!isset($_SESSION['username'])) {
            echo '<a href="./database/login.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Login</li>';
            echo '</a>';
            echo '<a href="./database/register.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Register</li>';
            echo '</a>';
          } else {
            echo '<a href="./todo.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Tasks</li>';
            echo '</a>';
            echo '<a href="./database/logout.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Logout</li>';
            echo '</a>';
            echo '<a href="./database/delete_account.php" class="menu-item">';
              echo '<li class="underline-hover-effect error">Delete Account</li>';
            echo '</a>';
          }
          ?>
        </ul>
      </div>
      <hr id="head-rule">
      <div class="content">
        <h1>Task tracker</h1>
        <p>This tool will serve as a reminder to complete certain tasks at set intervals. You will tell it which tasks you would like to complete and how often, and it will help you do it.</p>
      </div>
    </div>
    <script src="" async defer></script>
    <hr id="foot-rule">
  </body>
  <footer>
    <div class="split-items">
      <p>Last updated: <span>20 October 2025</span></p>
      <p>Author: Josh Gillum</p>
    </div>
    <div class="split-items">
      <a href="./cookies.html">cookies</a>
      <a href="./privacy.html">privacy policy</a>
      <a href="./terms.html">terms and conditions</a>
    </div>
  </footer>
</html>
