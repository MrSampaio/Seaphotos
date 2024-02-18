const openMenuBtn = document.getElementById('open-menu');
const menuIcon = document.getElementById('menu-icon');
const nav = document.getElementById('nav');
const li = document.querySelectorAll('.li-menu');

// ao clicar na barra de menu, adiciona classe que permite a visualização do menu inteiro e libera ícone de fechamento
openMenuBtn.addEventListener('click', ()=>{
    nav.classList.toggle('nav-active');

    if(menuIcon.classList.contains('fa-bars')){

        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-x');

    } else{

        menuIcon.classList.remove('fa-x');
        menuIcon.classList.add('fa-bars');
    }
})