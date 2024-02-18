const button = document.getElementById('submitHowMany');
const input = document.getElementById('howMany');

//ouvir de eventos com validação de valor atual do input
input.addEventListener('input', (e)=>{

    // caso seja maior que zero ou menor igual a dez, libera o botão e muda seu estilo
    if(e.target.value != 0 && e.target.value <= 10){
        button.classList.add('button-able');
        button.disabled = false;

    } else{
        button.classList.remove('button-able');
        button.disabled = true;
    }
})

