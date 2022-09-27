const menuImg = document.querySelector("#menuImg");

function hiddenImg() {
    menuImg.innerHTML = '';
}

window.addEventListener('load', hiddenImg);

//nie wiem jak ukryć obraz w menu głównym przy zmniejszeniu szerokości do 560 px