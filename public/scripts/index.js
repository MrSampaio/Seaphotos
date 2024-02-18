const imageModalContainer = document.getElementById('image-modal');
const images = document.querySelectorAll('#gallery-image');
const closeImageModalBtn = document.getElementById('close-image');
const modalImage = document.getElementById('image-modal-content');

// para cada imagem, adiociona um ouvidor de eventos que libera o modal com o src da imagem clicada
images.forEach(image => {

    image.addEventListener('click', ()=>{

        modalImage.src = image.src;
        imageModalContainer.style.display = 'flex';

        closeImageModalBtn.addEventListener('click', ()=>{
            imageModalContainer.style.display = 'none';

        })
    })
});


// atribuições de botões e icons de pesquisa em variáveis
const searchContainer = document.getElementById('search-container');
const searchBtn = document.getElementById('search-icon');
const wipeBtn = document.getElementById('wipe-search-icon');
const searchFormBtn = document.getElementById('search-form');
let searchInput = document.getElementById('search-input');

// ********* configurações do botão de pesquisa:*********

// ouvidor de evento de clique em clique no searchBtn
searchBtn.addEventListener('click', ()=>{

    // caso já contenha a classe search-active, envia o formulário
    if(searchContainer.classList.contains('search-active')){
        searchFormBtn.click();

    } else{
        // caso contrário, adiciona as classes para liberação do input para o usuário
        searchContainer.classList.add('search-active');
        searchBtn.classList.add('search-icon-clicked');
    }

    // ouvidor de evento de clique em clique no wipeBtn
    wipeBtn.addEventListener('click', ()=>{

        // caso o valor do input não seja vazio, limpa o conteúdo digitado
        if(searchInput.value != ''){
            searchInput.value = '';

        } else{
            // do contrário, desativa a caixa de pesquisa
            searchContainer.classList.remove('search-active');
            searchBtn.classList.remove('search-icon-clicked');
        }
    })
})