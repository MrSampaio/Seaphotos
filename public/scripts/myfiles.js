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


// atribuições de botões e icons em variáveis
const updateButtons = document.querySelectorAll('.update-btn');
const cancelButtons = document.querySelectorAll('#cancel-btn');
const deleteButtons = document.querySelectorAll('#delete-btn');
const icons = document.querySelectorAll('.fa-pen');

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

// ********* configurações do botão de update:*********

// para cada botão retornado pela query selector updateButtons, recebe o nome de updateBtn
updateButtons.forEach(updateBtn =>{
    
    // ouvidor de evento de clique em clique no updateBtn
    updateBtn.addEventListener('click', ()=>{

        // bloqueio do clique na tecla "enter" para evitar possíveis conflitos com os gatilhos do formulário
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
              event.preventDefault();
            }
        })

        // atribuição de elementos em variáveis para tratamento de front-end
        let form = updateBtn.closest('div[class="image"]');
        let inputs = form.querySelectorAll('input');
        let deleteBtn = form.querySelector('.delete-btn');
        let saveBtn = form.querySelector('.save-btn');
        let cancelBtn = form.querySelector('.cancel-btn');
        let labelPic = form.querySelector('.label-active');

        // desabilitar todos os outros botões de atualização e ícones
        updateButtons.forEach(btn => {
            if (btn !== updateBtn) {
                btn.disabled = true;
                btn.style.color = 'gray';
                btn.style.border = 'solid gray 1px';
                btn.style.cursor = 'default';

                icons.forEach(icon =>{
                   icon.style.color = 'gray';
                   icon.style.cursor = 'default';
                   
                })
            }
        })

        // habilitando inputs gerais
        inputs.forEach(input =>{
            input.disabled = false;
            input.classList.remove('input-disabled');
            input.classList.add('input-able');
        })

        // permitindo e removendo visualização de determinados botões
        deleteBtn.style.display = 'none';
        updateBtn.style.display = 'none';
        saveBtn.style.display = 'flex';
        cancelBtn.style.display = 'flex';
        labelPic.style.display = 'flex';


        // para cada botão retornado pela query selector cancelButtons, recebe o nome de cancelBtn
        cancelButtons.forEach(cancelBtn =>{

            // ouvidor de evento de clique em clique no cancelBtn
            cancelBtn.addEventListener('click', ()=>{
                location.reload();
            })
        })

        // atribuindo elementos da caixa de upload de arquivos para tratamento
        const openModalBtns = document.querySelectorAll('#label-change-image');
        const closeModalBtns = document.querySelectorAll('#close-modal-btn');
        const submitModalBtns = document.querySelectorAll('#modal-submit-btn');
        const fade = document.querySelector('#fade');
        const modal = document.querySelector('#modal');
        const principalInputFile = document.querySelector('#alter-image'); 

        const fileInput = document.querySelector('#modal-input');
        const spanImage = document.querySelector('#uploaded-name');
        const img = document.createElement('img');
        const video = document.createElement('video');

        // ouvidor de eventos para arquivo recebido em input file, passando evento como parâmetro
        fileInput.addEventListener('change', (e) => {
            const mediaInput = e.target; // input file
            const preview = document.getElementById('preview-container'); // container de prévia para exibição
            preview.innerHTML = ''; // limpando possível prévia anterior

            // atribuindo arquivo enviado em variável
            const file = mediaInput.files[0];

            // caso exista o arquivo...
            if(file){
                // atribuindo o tipo de arquivo em varíavel para validação
                const fileType = file.type.split('/')[0];

                // caso o tipo de arquivo seja uma imagem, gera uma tag img com a respectiva classe para a renderização
                if(fileType == 'image'){
                    img.src = URL.createObjectURL(file);
                    img.className = 'preview-image';
                    spanImage.textContent = file.name;
                    preview.appendChild(img);

                    // atribuindo arquivos enviados em input do modal em input principal(dentro do form .container-image)
                    principalInputFile.files = mediaInput.files;

                // caso o tipo de arquivo seja um vídeo, gera uma tag video com a respectiva classe para a renderização
                } else if(fileType == 'video'){
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    video.className = 'preview-video';
                    preview.appendChild(video);

                    // atribuindo arquivos enviados em input do modal em input principal(dentro do form .container-image)
                    principalInputFile.files = mediaInput.files;

                // caso não seja nenhum dos dois, o arquivo não é aceito
                } else{
                    preview.innerHTML = 'Tipo de arquivo não suportado.';
                    preview.style.color = 'red';
                    preview.style.fontSize = '10pt';
                }
            }
        })

        // para cada botão retornado pela query selector openModalBtns, recebe o nome de openModalBtn
        openModalBtns.forEach(openModalBtn =>{

            // ouvidor de evento de clique em clique no openModalBtn
            openModalBtn.addEventListener('click', ()=>{

                // caso haja click no botão, libera a visualização do fade e do modal
                fade.classList.remove('hide');
                modal.classList.remove('hide');

                // para cada botão retornado pela query selector closeModalBtns, recebe o nome de closeModalBtn
                closeModalBtns.forEach(closeModalBtn =>{

                    // ouvidor de evento de clique em clique no closeModalBtn
                    closeModalBtn.addEventListener('click', ()=>{
                        // caso haja clique, remove a visualização do fade e do modal
                        fade.classList.add('hide');
                        modal.classList.add('hide');

                        // limpando valor do arquivo e removendo elementos criados
                        fileInput.value = '';
                        img.remove();
                        video.remove();
                        spanImage.textContent = 'Alterar mídia';

                        // removendo e permitindo visualização de botões
                        form.querySelector('.visible-btn').style.display = 'flex';
                        form.querySelector('.disabled-label').style.display = 'none';
                        
                    })
                })

                // para cada botão retornado pela query selector submitModalBtns, recebe o nome de submitModalBtn
                submitModalBtns.forEach(submitModalBtn =>{

                    // ouvidor de evento de clique
                    submitModalBtn.addEventListener('click', ()=>{
                        // caso haja clique, remove visualização do fade e modal
                        fade.classList.add('hide');
                        modal.classList.add('hide');

                        // removendo e permitindo visualização de botões
                        form.querySelector('.visible-btn').style.display = 'none';
                        form.querySelector('.disabled-label').style.display = 'flex';

                    })
                })
            })
        })
    }) 
})

// ********* configurações do botão de delete:*********

// para cada botão retornado pela query selector deleteButtons, recebe o nome de deleteBtn
deleteButtons.forEach(deleteBtn =>{
    // ouvidor de evento de clique
    deleteBtn.addEventListener('click', ()=>{

        // atribuindo elementos da caixa de upload de arquivos para tratamento de front-end
        let form = deleteBtn.closest('div[class="image"]');
        let inputs = form.querySelectorAll('input');
        let subtitle = form.querySelector('.subtitle');
        let yesBtn = form.querySelector('.yes-btn');
        let notBtn = form.querySelector('.not-btn');
        let updateBtn = form.querySelector('.update-btn');

        // removendo e permitindo visualização de elementos
        subtitle.style.display = 'flex';
        deleteBtn.style.display = 'none';
        yesBtn.style.display = 'flex';
        notBtn.style.display = 'flex';
        updateBtn.style.display = 'none';

        // ouvidor de evento de clique pra remover visualização de elementos
        notBtn.addEventListener('click', ()=>{
            subtitle.style.display = 'none';
            deleteBtn.style.display = 'flex';
            yesBtn.style.display = 'none';
            notBtn.style.display = 'none';
            updateBtn.style.display = 'flex';

            // desabilitando inputs gerais
            inputs.forEach(input =>{
                input.disabled = true;
                input.classList.add('input-disabled');
                input.classList.remove('input-able');
            })
        })

        // habilitando inputs gerais
        inputs.forEach(input =>{
            input.disabled = false;
            input.classList.remove('input-disabled');
            input.classList.add('input-able');
        })
    })
})
