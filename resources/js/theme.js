let currentTheme = localStorage.getItem("theme") ?? "light";
const toggle_button_icon = document.querySelector(".change-theme i") ?? document.querySelector(".toggle-mode i");
const button_text = document.querySelector(".toggle-mode p") ?? null;

if (currentTheme === "dark") {
    if (toggle_button_icon) {
        toggle_button_icon.classList.replace("fa-moon", "fa-sun");
    }
    if (button_text) {
        button_text.innerHTML = "Tryb Jasny";
    }
}

window.toggleMode = function (){
    if (currentTheme === 'light') {
        currentTheme = 'dark'
        if (button_text){
             button_text.innerHTML = "Tryb Jasny";
        }
        toggle_button_icon.classList.replace("fa-moon", "fa-sun");
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        currentTheme = 'light'
        if (button_text){
            button_text.innerHTML = "Tryb Ciemny";
        }
        document.documentElement.setAttribute('data-theme', 'light');
        toggle_button_icon.classList.replace("fa-sun", "fa-moon");
    }
    localStorage.setItem("theme", currentTheme);
}
