function submissionMessage(text,error = true) {
  const wrapper = document.getElementById("submission-message-holder");
  holder = wrapper.querySelector("p");
  console.log(error);
  holder.textContent = text;
  if (error){
    holder.className += "error";
  } else {
    holder.className += "success";
  }
}
