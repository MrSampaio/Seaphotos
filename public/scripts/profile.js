let ableBtn = document.getElementById('able-button');
let submitBtn = document.getElementById('infos-submit');
let cancelBtn = document.getElementById('cancel-btn'); // mais configurações do botão ao final do arquivo profile.php
let labelPicture = document.getElementById('id-pic');
let removePic = document.getElementById('profile-pic-remove');
let inputs = document.querySelectorAll('.input');

ableBtn.addEventListener('click', ()=>{

    inputs.forEach(input =>{

        input.disabled = false;
        input.classList.remove('input-disabled');
        input.classList.add('input-able');
        submitBtn.style.display = 'block';
        cancelBtn.style.display = 'block';
        ableBtn.style.display = 'none';
        labelPicture.classList.remove('disabled');
        labelPicture.classList.add('able');
        removePic.classList.remove('disabled');
        removePic.classList.add('able');
        removePic.disabled = false;

        let icon = input.parentElement.querySelector('#icon');

        input.addEventListener('focus', ()=>{
            icon.style.color = '#8c36c6'; // muda a cor do ícone quando o input
            icon.style.transition = 'all 0.3s ease'; // adiciona animação durante a troca de cor
        });

        input.addEventListener('blur', ()=>{
            icon.style.color = '';
            icon.style.transition = 'all 0.3s ease'; // adiciona animação durante a troca de cor
        });

    });
      
});



let eyes = document.querySelectorAll('#eye-icon');

eyes.forEach(eye =>{ // para cada elemento encontrado, definirá o nome como "eye"
    eye.addEventListener("click", ()=>{ // ouvinte de eventos para clique no ícone relacionado

        let pswdInput = eye.previousElementSibling; // retorna o input relacionado a cada olho

        if(!eye.classList.contains("fa-eye-slash")){ // testa qual o ícone pelo texto que contem a tag
            // remove classe de ícone e adiciona outro
            eye.classList.remove('fa-eye'); 
            eye.classList.add("fa-eye-slash"); 
            pswdInput.type = "password" // modifica o tipo de input

        } else{

            eye.classList.remove("fa-eye-slash");
            eye.classList.add('fa-eye');
            pswdInput.type = "text";

        }
    })
})


let pictureInput = document.getElementById('profile-pic');
let pictureBtn = document.getElementById('profile-pic-btn');

pictureInput.addEventListener("change", ()=>{

    if(pictureInput.files.length > 0){
        pictureBtn.click();
    }
        
})
