originalTitle = document.title

focused = function () {
    document.title = originalTitle;
};

unfocused = function () {
    document.title = "Come Back!";
};

window.addEventListener("blur", unfocused);
window.addEventListener("focus", focused);
