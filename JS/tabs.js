var timer_interval;

function timer(interval) {
  var current_time = new Date();
  var reset_time = new Date();
  const offset = current_time.getTimezoneOffset();
  current_time.setMinutes(current_time.getMinutes()+offset);
  reset_time.setMinutes(reset_time.getMinutes()+offset);
  if (interval == "Daily"){
    reset_time.setDate(current_time.getDate() + 1);
  } else if(interval == "Weekly"){
    console.log(current_time.getDay());
    reset_time.setDate(current_time.getDate() + (7-current_time.getDay()));
  } else if(interval == "Monthly"){
    reset_time.setMonth(current_time.getMonth()+1,1);
  } else if(interval == "Yearly"){
    reset_time.setFullYear(current_time.getFullYear()+1);
    reset_time.setMonth(0,1);
  } else if(interval == "One-time"){
    document.getElementById("reset-timer").innerHTML = "N/A";
    return;
  }
  else {
    document.getElementById("reset-timer").innerHTML = "Error!";
    return;
  }
  reset_time.setHours(0);
  reset_time.setMinutes(0);
  reset_time.setSeconds(0);

  const zeroPad = (num, places) => String(num).padStart(places, '0');
  var time_difference = Math.floor((reset_time.getTime() - current_time.getTime())/1000); //Convert ms to seconds
  const days = zeroPad(Math.floor(time_difference / 86400),3);
  time_difference -= days*86400;
  const hours = zeroPad(Math.floor(time_difference / 3600),2);
  time_difference -= hours*3600;
  const minutes = zeroPad(Math.floor(time_difference / 60),2);
  time_difference -= minutes*60;
  time_difference = zeroPad(time_difference,2);
  document.getElementById("reset-timer").innerHTML = `Reset ~ ${days}:${hours}:${minutes}:${time_difference}`;
}


function openTab(evt, tabName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  tab = document.getElementById(tabName);
  tab.style.display = "block";
  // Removes old reset-timer id and sets new one
  var old = document.getElementById("reset-timer");
  if(old){
    old.id = "";
    old.innerHTML = "";
  }
  var holder = tab.querySelector(".reset-timer-holder");
  console.log(holder);
  holder.id = "reset-timer";
  evt.currentTarget.className += " active";
  if (timer_interval){
    clearInterval(timer_interval);
  }
  timer(tabName);
  timer_interval = setInterval(timer, 1000, tabName);
} 

