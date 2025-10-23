<?php
session_start();
// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
  header("Location: database/login.php");
  exit();
}

function write_to_console($data) {
 $console = $data;
 if (is_array($console))
 $console = implode(',', $console);

 echo "<script>console.log('Console: " . $console . "' );</script>";
}

$selected = $_GET['selected'];

include 'data_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
  if(isset($_POST['add_task'])){
    $type = $_POST['frequency'];
    $text = $_POST['text-content']; 
    $stmt = $conn->prepare("SELECT id FROM userdata where username = ? and type = ?");
    $stmt->bind_param("ss",$_SESSION['username'],$type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $ids = [];
    foreach($data as $row){
      $ids[] = $row['id'];
    }
    sort($ids);
    $previous = -1;
    $next = -1;
    foreach($ids as $current){
      if ($previous + 1 < $current){
        $next = $previous + 1;
        break;
      } else {
        $previous = $current;
      }
    }
    if ($next < 0){
      $next = $previous + 1;
    }
    $message = "";
    $stmt = $conn->prepare("INSERT INTO userdata (username,id,type,text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss",$_SESSION['username'],$next,$type,$text);
    if($stmt->execute()){
      $message = "Added task successfully";
    } else {
      $message = "Error: ".$stmt->error;
    }
    $stmt->close();
    write_to_console($message);
    header("Location: ../todo.php?type=".$type);
    exit();
  }
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
    <title>Add Task</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../JS/title.js"></script>
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
          // Check if user is logged in, if not redirect to login page
          if (!isset($_SESSION['username'])) {
            echo '<a href="./login.php" class="menu-item active">';
              echo '<li class="underline-hover-effect">Login</li>';
            echo '</a>';
            echo '<a href="./register.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Register</li>';
            echo '</a>';
          } else {
            echo '<a href="../todo.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Tasks</li>';
            echo '</a>';
            echo '<a href="./logout.php" class="menu-item">';
              echo '<li class="underline-hover-effect">Logout</li>';
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
      <a href="../todo.php">tasks</a>
      <p>></p>
      <p>add_task</p>
    </div>
    <div class="content">
        <h1>Add task</h1>
          <form method="post">
            <div>
              <label for="frequency">Frequency: </label>
              <select name="frequency" id="frequency">
                <?php
                $names = array(
                  array("daily","Daily"),
                  array("weekly","Weekly"),
                  array("monthly","Monthly"),
                  array("yearly","Yearly"),
                  array("onetime","One time")
                );
                  foreach($names as $item){
                    if(isset($selected) && $selected == $item[0]){
                      echo '<option value="'.$item[0].'" selected>'.$item[1].'</option>';
                    } else {
                      echo '<option value="'.$item[0].'">'.$item[1].'</option>';
                    }
                }
                ?>
              </select>
            </div>
            <div>
              <label for="text-content">Text: </label>
              <input type="text" name="text-content" id="text-content" class="text-input-wide" required placeholder="This task is to..." maxlength="255"/>
            </div>
            <div>
              <button type="submit" name="add_task" class="styled-button submit-button">Submit</button>
            </div>
          </form>
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
      <a href="../cookies.html">cookies</a>
      <a href="../privacy.html">privacy policy</a>
      <a href="../terms.html">terms and conditions</a>
    </div>
  </footer>
</html>
