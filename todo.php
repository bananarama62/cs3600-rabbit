<?php

function write_to_console($data) {
 $console = $data;
 if (is_array($console))
 $console = implode(',', $console);

 echo "<script>console.log('Console: " . $console . "' );</script>";
}
session_start();
// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
  header("Location: database/login.php");
  exit();
}

$tab = $_GET["type"];

include './database/data_connection.php';


$daily = [];
$weekly = [];
$monthly = [];
$yearly = [];
$onetime = [];
$stmt = $conn->prepare("SELECT type,id,text,last_completed FROM userdata WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
write_to_console("Tasks retrieved:");
foreach($data as $row){
  write_to_console($row);
  if($row['type'] === 'daily'){
    $daily[] = $row;
  } else if($row['type'] === 'weekly'){
    $weekly[] = $row;
  } else if($row['type'] === 'monthly'){
    $monthly[] = $row;
  } else if($row['type'] === 'yearly'){
    $yearly[] = $row;
  } else if($row['type'] === 'onetime'){
    $onetime[] = $row;
  }
}

$stmt->close();
$conn->close();

include './database/data_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_changes'])){
  date_default_timezone_set("UTC");
  $current_time = date("Y-m-d H:i:s");
  $stmt = $conn->prepare("UPDATE userdata SET last_completed=? where username=? and id=? and type=?");
  $fetchstmt = $conn->prepare("SELECT last_completed FROM userdata WHERE username=? AND id=? AND type=?");

  foreach($_POST as $name => $value){
    if($name != "submit_changes"){
      $split = explode("-",$name);
      $type = $split[0];
      $id = $split[1];
      $fetchstmt->bind_param("sis",$_SESSION['username'],$id,$type);
      if($fetchstmt->execute()){
        $result = $fetchstmt->get_result();
        $data = $result->fetch_assoc();
        $last_completed = $data['last_completed'];
        if($last_completed < $current_time){
          $stmt->bind_param("ssis",$current_time,$_SESSION['username'],$id,$type);
          $message = "";
          if($stmt->execute()){
            $message = "modified task successfully";
          } else {
            $message = "error: ".$stmt->error;
          }
          write_to_console($message);
        }
      } else {
        write_to_console("Failed to fetch...");
      }
      $stmt->close();
      $conn->close();
      header("Refresh:0; url=todo.php?type=".$type);
    }
  }
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
    <title>Task Tracker</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./CSS/style.css">
    <script src="./JS/title.js"></script>
    <script src="./JS/tabs.js"></script>
  </head>
  <body>
    <!--[if lt IE 7]>
      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div>
      <div class="navigation-head">
        <div class="site-logo">
          <a href="./index.php">
            <?php
              echo "<h1>";
              echo "Tasks ~ ".ucfirst(strtolower($_SESSION['username']));
              echo "</h1>";
            ?>
          </a>
        </div>
        <ul class="navigation-menu">
          <a href="./todo.php" class="menu-item active">
            <li class="underline-hover-effect">Tasks</li>
          </a>
          <a href="./database/logout.php" class="menu-item">
            <li class="underline-hover-effect">Logout</li>
          </a>
          <a href="./database/delete_account.php" class="menu-item">
            <li class="underline-hover-effect error">Delete Account</li>
          </a>
        </ul>
      </div>
      <hr id="head-rule">
    </div>
    <div class="breadcrumbs">
      <a href="./index.php">home</a>
      <p>></p>
      <p>tasks</p>
    </div>
    <div class="content">
      <div class="tab">
        <div class="tab-buttons">
          <?php
          $names = array(
            array("Daily","Daily","daily",$daily,$today),
            array("Weekly","Weekly","weekly",$weekly,$this_week),
            array("Monthly","Monthly","monthly",$monthly,$this_month),
            array("Yearly","Yearly","yearly",$yearly,$this_year),
            array("One-time","One Time","onetime",$onetime,0)
          ); 
              if(!isset($tab)){
                $tab="daily";
              }

              foreach($names as $item){
                $prefix = '<button class="tablinks"';
                $middle = '';
                $suffix = ' onclick="openTab(event,\''.$item[0].'\')">'.$item[1].'</button>';
                if($tab == $item[2]){
                  $middle = ' id="defaultOpen"';
                }
                echo $prefix.$middle.$suffix;
          }
        echo '</div>';
        echo '<!-- Tab content -->';
        

        date_default_timezone_set("UTC");
        $current_time = date("Y-m-d H:i:s");
        $today = date("Y-m-d H:i:s",strtotime('today,midnight'));
        if (date("w") == 0){
          $this_week = $today;
        } else {
          $this_week = date("Y-m-d H:i:s",strtotime('last Sunday'));
        }
        $this_month = date("Y-m-d H:i:s",strtotime('first day of this month, midnight'));
        $this_year = date("Y-m-d H:i:s",strtotime('first day of january this year'));

        foreach($names as $item){
          echo '<div id="'.$item[0].'" class="tabcontent">';
          echo '<div class="tab-title split-items">';
          echo '<h3>'.$item[1].'</h3>';
          echo '<p class="reset-timer-holder"></p>';
          echo '</div>';
          echo '<div class="task-content" id="'.$item[2].'-content">';
          echo '<form method="POST">';
          $completed_tasks = [];
          if ($item[3]){
            foreach($item[3] as $row){
              if($row['last_completed'] > $item[4]){
                $completed_tasks[] = $row;
              } else {
                echo '<div class="flex">';
                echo '<label class="task">';
                echo '<input type="checkbox" class="toggle-task" id="'.$item[2].'-'.$row['id'].'" name="'.$item[2].'-'.$row['id'].'">';
                  echo $row['text'];
                echo '</label>';
                echo '<div class="task-modification-buttons flex">';
                echo '<a href="database/modify_task.php?type='.$item[2].'&id='.$row['id'].'">Modify</a>';
                echo '</div>';
                echo '</div>';
              }
            }
          }
          if(sizeof($completed_tasks) >= sizeof($item[3]) && sizeof($item[3]) > 0){
            echo '<p>Nothing to see here!</p>';
            echo '<a href="./database/add_task.php?selected='.$item[2].'">Add Task</a>';
          } else {
            echo '<a href="./database/add_task.php?selected='.$item[2].'">Add Task</a>';
            if(sizeof($item[3]) > 0){
              echo '<button type="submit" name="submit_changes" class="submit-button">Submit</button>';
            }
          }
          echo '</form>';
          if(sizeof($completed_tasks) > 0){
            echo '<h3>Completed</h3>';
            foreach($completed_tasks as $item){
              echo '<p><del>'.$item['text'].'</del></p>';
            }
          }
          echo '</div>';
          echo '</div>';
        }

        ?>
        <script>
          // Get the element with id="defaultOpen" and click on it
          document.getElementById("defaultOpen").click();
        </script>
      </div>
    <div><p id="demo"></p></div>
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
