<?php
session_start(); // iniciando sessão

// autenticação de login e sessão privada
if(isset($_SESSION['logged']) && $_SESSION['logged'] != false){

    // incluindo conexão do banco de dados
    include('../database/connectDB.php');

    // aplicando id em sessão para variável para fazer query select
    $id_user = $_SESSION['id_user'];
    $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`upload_infos` WHERE id_user = '$id_user' ORDER BY date_upload DESC");

    // impondo retorno da query select em variável
    $data = mysqli_fetch_array($select);

    // verificando a existência de erro e limpando a variável global ao recarregar a página
    if(isset($_SESSION['temp_id_file']) && isset($_SESSION['error' . $_SESSION['temp_id_file']])){
        unset($_SESSION['error' . $_SESSION['temp_id_file']]);
    }

    // gatilho de existência de pesquisa
    if(isset($_POST['search-input'])){

        // atribuindo valor de input de pesquisa em variável
        $search = strip_tags($mysqli->real_escape_string($_POST['search-input']));

        // select query substituindo valores default(todas as midias do usuário) para os da pesquisa
        $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`upload_infos` WHERE id_user = '$id_user' AND description LIKE CONCAT('%', '$search', '%') ORDER BY date_upload DESC");

        //(recarregamento da página feito com javascript para melhor experiência do usuário) -> myfiles.js
    }

    // gatilho de confirmação de delete
    if(isset($_POST['submit']) && $_POST['submit'] == 'yes-btn'){

        // adicionando valor do campo oculto com descrição específica da imagem ao qual o formulário foi enviado
        $original_desc = $_POST['original-description'];

        // select query para adicionar possível erro em imagem específica e pegar caminho da imagem 
        $selectIdFile = mysqli_query($mysqli,"SELECT path, id_file FROM `files_upload`.`upload_infos` WHERE description = '$original_desc'");

        // adicionando resultado da select query em array
        $result = mysqli_fetch_array($selectIdFile);

        // requisitando id_file do array e impondo em variável em sessão para mensagem de erro futura
        $_SESSION['temp_id_file'] = $result['id_file'];

        if($original_desc){

            // delete query para apagar a imagem determinada do banco de dados
            $deleteQuery = mysqli_query($mysqli, "DELETE FROM `files_upload`.`upload_infos` WHERE description = '$original_desc'");

            if($deleteQuery){

                // apaga arquivo de dentro do diretório local
                unlink($result['path']);

                // recarrega a página para remover exibição de imagem
                header('Location: myfiles.php');

                // saindo da página e encerrando processamentos
                exit();

            } else{
                $_SESSION['error' . $_SESSION['temp_id_file']] = 'Erro ao apagar mídia';
            }
        }
    }

    // gatilho para formulário de alteração de mídia e informações gerais
    if(isset($_POST['submit']) && $_POST['submit'] == 'save' && isset($_FILES['alter-image'])){

        // adicionando valor do campo oculto com descrição específica da imagem ao qual o formulário foi enviado
        $original_desc = $_POST['original-description'];

        // atribuindo possível nova descrição em variável
        $desc = strip_tags($mysqli->real_escape_string($_POST['description']));

        // removendo caracteres especiais
        $desc = preg_replace('/[^\w\s.,…\p{L}\p{N}\p{P}\p{S}"\']/', '', $desc);
        // removendo espaços ao começo e final
        $desc = trim($desc);
        // setando todas as letras como minúsculas
        $desc = strtolower($desc);
        // setando a primeira letra como maiúscula
        $desc = ucfirst($desc);

        // adicionando valor do campo oculto com data original para verificação de update
        $original_date = $_POST['original-date'];
        $file_date = $_POST['date'];

        // select query para adicionar possível erro em imagem específica e pegar caminho e descrição da imagem 
        $selectIdPath = mysqli_query($mysqli, "SELECT path, id_file, description FROM `files_upload`.`upload_infos` WHERE description = '$original_desc' ");
        $result = mysqli_fetch_array($selectIdPath);

        // requisitando id_file do array e impondo em variável para futuro update
        $id_file = $result['id_file'];

        // requisitando id_file do array e impondo em variável em sessão para mensagem de erro futura
        $_SESSION['temp_id_file'] = $result['id_file'];

        // select para validação de descrição já existente
        $selectTestDesc = mysqli_query($mysqli,"SELECT description FROM `files_upload`.`upload_infos` WHERE description = '$desc' ");

        if(mysqli_num_rows($selectTestDesc) == 1 && $desc != $original_desc){
            $_SESSION['error' . $_SESSION['temp_id_file']] = 'Título já existente';

        } else{

            // validação para necessidade de atualização de alguma informação geral da mídia
            if($desc != $original_desc || $file_date != $original_date){

                // update query das informações após validação
                $updateDesc = mysqli_query($mysqli, "UPDATE `files_upload`.`upload_infos` SET description = '$desc', file_date = '$file_date' WHERE id_file = '$id_file'");

                // redirecionamento para a mesma página para que seja feita a exibição das alterações
                header('Location: ' . $_SERVER['PHP_SELF']);

                // saindo da página e encerrando processamentos
                exit();
            }
    
            // adicionando arquivos enviado em variável
            $uploaded = $_FILES['alter-image'];

            // caso haja ao menos um arquivo enviado, pega o índice 0
            if(!empty($uploaded['name'][0])){

                // atribuindo valores da imagem para o índice
                $fileName = $uploaded['name'][0];
                $tempName = $uploaded['tmp_name'][0];
                $errorImage = $uploaded['error'][0];
                $size = $uploaded['size'][0];

                // buscando extensão do arquivo por meio da função pathinfo para fazer verificação de upload
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // definindo diretório de save dos arquivos
                $directory = '../files/gallery/';

                // atribuindo novo nome específico ao arquivo enviado
                $newFileName = uniqid();

                // validação de existência erros ao enviar
                if($errorImage){
                    $_SESSION['error' . $_SESSION['temp_id_file']] = 'Erro no upload do arquivo: ' . $errorImage;

                } else{
                    // validação de tamanho de arq  uivo enviado (máx: 50mb)
                    if($size > 52428800){
                        $_SESSION['error' . $_SESSION['temp_id_file']] = 'Arquivo muito grande(Máx: 50mb)';

                    } else{

                        // validação de tipo de arquivo enviado
                        if($extension != 'jpeg' && $extension != 'jpg' && $extension != 'png' && $extension != 'mp4'){
                            $_SESSION['error' . $_SESSION['temp_id_file']] = 'Tipo de arquivo não aceito';

                        } else{
                            // movendo arquivo para diretório
                            $moving = move_uploaded_file($tempName, $directory . $newFileName . '.' . $extension);

                            // validação para sucesso de envio para diretório
                            if(!$moving){
                                $_SESSION['error' . $_SESSION['temp_id_file']] = 'Erro ao exibir arquivo';

                            } else{

                                // variável concatenando novo caminho do arquivo e update query para atualização no banco de dados
                                $newPath = $directory . $newFileName . '.' . $extension;
                                $updateFile = mysqli_query($mysqli, "UPDATE `files_upload`.`upload_infos` SET path = '$newPath', original_name = '$fileName', new_name = '$newFileName', extension = '$extension' WHERE id_file = '$id_file'");

                                if(!$updateFile){
                                    $_SESSION['error' . $_SESSION['temp_id_file']] = "Erro ao alterar mídia";

                                } else{

                                    // redirecionamento para a mesma página para que seja feita a exibição das alterações
                                    header('Location: ' . $_SERVER['PHP_SELF']);

                                    // saindo da página e encerrando processamentos
                                    exit();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    // caso usuário não esteja logado, redireciona-o para página de login
    header('Location: login.php');

    // saindo da página e encerrando processamentos
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/favicon-16x16.png">  
    <script src="https://kit.fontawesome.com/bbdbe3941a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/styles/myfiles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus arquivos</title>
</head>

<body>

    <?php include('../components/headerMenu.php') ?>
    <?php include('../components/imageModal.php') ?>

    <main class="container">

        <section class="profile-card">

            <div class="profile-card-photo">
                <img src="<?php echo $dataPicture['path_picture']?>" alt="profile-pic">
                <p class="username"><?php echo $dataPicture['name']?></p>
            </div>

            <div class="search-title-box">
                <p class="myfiles">Meus arquivos</p>
                <section class="search-container" id="search-container">

                    <div class="search-icon" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass" id="fa-magnifying-glass"></i>
                    </div>

                    <form class="search-input-form" method="POST" action="" id="search-input-form">
                        <input required type="text" maxlength="35" class="search-input" name="search-input" id="search-input" placeholder="Pesquise uma mídia aqui...">
                        <button type="submit" id="search-form" value="search-form" style="display:none"></button>
                    </form>

                    <div class="wipe-search-icon" id="wipe-search-icon">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </div>

                </section>
            </div>

        </section>

        <div class="hide" id="fade"></div>
            <section class="hide" id="modal">
            
                <div class="modal-container">
                    
                    <div id="modal-header">
                        <h2>Atualizar mídia</h2>
                        <p id="modal-subtitle">Escolha a mídia clicando no botão abaixo.</p>
                    </div>

                    <div id="preview-container"></div>

                    <div class="modal-input-box">
                        <br>
                        <label for="modal-input" class='modal-label' style="display:flex;">
                            <span class="uploaded-name" id="uploaded-name">Alterar mídia</span>
                            <span class="search-file"><i class="fa-solid fa-upload"></i> Selecionar...</span>
                        </label>

                        <input type="file" name="alter-image-modal" id="modal-input">

                        <div class="buttons-box">
                            <button class="button-able modal-submit-btn" id='modal-submit-btn' type="button"><i class="fa-regular fa-paper-plane"></i>Enviar</button>
                            <button class="button-able modal-cancel-btn" id='close-modal-btn' type="button"><i class="fa-solid fa-x"></i>Cancelar</button>
                        </div>
                    </div>

                </div>
            </section>

        <?php echo (isset($_POST['search-input']) ? "<p>Exibindo resultados para '<span style='color: var(--search-font-color)'>" . $_POST['search-input'] . "</span>' :</p>" : '') ?>  

        <form class="container-image" method="POST" enctype="multipart/form-data" action="">
            <?php echo ((mysqli_num_rows($select) < 1) ? "<p style='color: var(--disabled-font-color)'>Nenhuma mídia encontrada :( </p>" : "")?>
        
            <!-- para cada linha presente na select query, exibe as informações respectivas a partir do array $file-->
            <?php foreach ($select as $index => $file) { ?>
            
            <div class="image">
                <!-- Caso o arquivo seja um vídeo, renderiza o bloco html para exibição de vídeos, do contrário, exibe o bloco para imagens-->
                <?php if ($file['extension'] == 'mp4') { ?>

                    <video src="<?php echo $file['path'] ?>" controls class="gallery-media" id="gallery-media" alt="<?php echo $file['description'] ?>"></video>
                    <div class="input-box">
                        <input disabled type="hidden" name="original-description" value="<?php echo $file['description'] ?>">
                        <input disabled type="hidden" name="original-date" value="<?php echo $file['file_date'] ?>">
                        <input disabled required type="text" maxlength="35" class="input-disabled" name="description" id="description" value="<?php echo $file['description'] ?>">
                        <input disabled type="date" class="input-disabled" name="date" id="date" value="<?php echo $file['file_date'] ?>">
                    </div>

                <?php } else { ?>

                    <img src="<?php echo $file['path'] ?>" class="gallery-media" id="gallery-image" alt="<?php echo $file['description'] ?>">
                    <div class="input-box">
                        <input disabled type="hidden" name="original-description" value="<?php echo $file['description'] ?>">
                        <input disabled type="hidden" name="original-date" value="<?php echo $file['file_date'] ?>">
                        <input type="file" name="alter-image[]" style="display:none" id='alter-image'>
                        <input disabled required type="text" maxlength="35" class="input-disabled" name="description" id="description" value="<?php echo $file['description'] ?>">
                        <input disabled type="date" class="input-disabled" name="date" id="date" value="<?php echo $file['file_date'] ?>">
                    </div>

                <?php } ?>

                <div class="update-picture-btn">
                    <p class="subtitle" style="color: #e60000;">Deseja mesmo apagar?</p>
                    <span class="error"><?php echo (isset($_SESSION['error' . $file['id_file']])) ? $_SESSION['error' . $file['id_file']] : '' ?></span>
                    <button type="button" class="label-active label-change-image visible-btn" id="label-change-image"><i class="fa-solid fa-camera"></i>Alterar mídia</button>
                    <button type="button" style="display:none" class="label-active label-change-image disabled-label" id="label-change-image"><i class="fa-solid fa-arrow-rotate-left"></i>Escolher outro</button>
                </div>

                <div class="buttons-box">
                    <button type="button" class="delete-btn button-able" id="delete-btn" value='delete'><i class="fa-solid fa-trash" style="color: #e60000;"></i> Apagar</button>
                    <button type="button" class="update-btn button-able" id="update-btn" value='update'><i class="fa-solid fa-pen" style="color: #004fd6;"></i> Editar</button>

                    <button type="button" class="cancel-btn" id="cancel-btn" value='cancel'><i class="fa-solid fa-xmark" style="color: #e60000;"></i> Cancelar</button>
                    <button type="submit" class="save-btn" name="submit" id="save-btn" value='save'><i class="fa-solid fa-check" style="color: #44db1a;"></i> Salvar</button>

                    <button type="submit" class="yes-btn" name="submit" id="yes-btn" value='yes-btn'><i class="fa-solid fa-trash" style="color: #e60000;"></i> Sim</button>
                    <button type="button" class="not-btn" id="not-btn" value='not-btn'><i class="fa-solid fa-rotate-right" style="color: #44db1a;"></i> Não</button>
                </div>

            </div>

            <?php } ?>
        </form>
    </main>

    <script src='../public/scripts/myfiles.js' defer></script>

</body>
</html>
