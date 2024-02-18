let inputs = document.querySelectorAll(".input");

inputs.forEach(input =>{ // para cada elemento encontrado, terá o nome de "input"
    let inputIcon = input.parentElement.querySelector("#icon"); // retorna o ícone relacionado a cada input

    input.addEventListener('focus', ()=>{ // ouvinte de evento para quando o input está em foco
        inputIcon.style.color = '#8c36c6'; // muda a cor do ícone quando o input
        inputIcon.style.transition = 'all 0.3s ease'; // adiciona animação durante a troca de cor
    })

    input.addEventListener('blur', ()=>{
        inputIcon.style.color = ''; // remove a cor do input ao sair de foco
    })    
})

// *******************************************************************************************

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