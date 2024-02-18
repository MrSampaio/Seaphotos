<?php
session_start(); // iniciando sessão

// incluindo conexão do banco de dados
include('../database/connectDB.php'); 

if(isset($_SESSION['logged']) && $_SESSION['logged'] != false){
    
    $errors = '';

    if(isset($_POST['submit']) && $_POST['submit'] == 'files-submit') {

        // pegando email de sessão para query select de insert e atribuindo em array
        $user_email = $_SESSION['user_email'];
        $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`users_infos` WHERE email = '$user_email'");
        $data = mysqli_fetch_array($select);

        // atribuindo id de usuário em variavel para validação futura
        $id_user = $data['id_user'];

        // adicionando arquivos(s) enviado(s) em variável
        $uploaded = $_FILES['files'];

        // para cada arquivo enviado, recebe o nome file e sua respectiva index
        foreach($uploaded['name'] as $index => $file){

            // contador de arquivos enviados para prevenção de envio duplicado
            $countFiles = count($uploaded['name']);

            // definindo variáveis para receber informações de acordo com sua index
            $fileName = $uploaded['name'][$index];
            $tempName = $uploaded['tmp_name'][$index];
            $error = $uploaded['error'][$index];
            $size = $uploaded['size'][$index];

            // buscando extensão do arquivo por meio da função pathinfo para fazer verificação de upload
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
            // definindo diretório de save dos arquivos
            $directory = '../files/gallery/';
        
            // atribuindo novo nome específico ao arquivo enviado
            $newFileName = uniqid();
        
            // atribuindo em uma variável a respectiva descrição da mídia enviada e limpando possíveis comandos sql
            $description = strip_tags($mysqli->real_escape_string($_POST['description'][$index]));

            // removendo carateres especiais
            $description = preg_replace('/[^\w\s.,…\p{L}\p{N}\p{P}\p{S}"\']/', '', $description);

            // removendo espaços ao começo e final
            $description = trim($description);

            // setando todas as letras como minúsculas
            $description = strtolower($description);

            // tornando a primeira letra da primeira palavra maiúscula
            $description = ucfirst($description);

            // atribuindo em uma variável a respectiva data da mídia enviada de acordo com o usuário
            $date = $_POST['date'][$index];

            // select para validação de descrição já existente
            $selectTestDesc = mysqli_query($mysqli,"SELECT description FROM `files_upload`.`upload_infos` WHERE description = '$description' ");

            if(mysqli_num_rows($selectTestDesc) != 0){
                $errors = 'Mídia ' . ($index + 1) . ': ' . 'Título já existente';

            } else{

                // validação de existência erros ao enviar
                if($error){
                    $errors = 'Mídia ' . ($index + 1) . ': ' . 'Verifique o que está enviando e tente novamente!';
            
                } else{
                    // validação de tamanho de arquivo enviado (máx: 50mb)
                    if($size > 52428800){
                        $errors = 'Mídia ' . ($index + 1) . ': ' . 'Tamanho de arquivo não aceito! Máximo: 50mb';

                    } else{
                        // validação de tipo de arquivo enviado
                        if($extension != 'jpeg' && $extension != 'jpg' && $extension != 'png' && $extension != 'mp4'){
                            $errors = 'Mídia ' . ($index + 1) . ': ' . 'Tipo de arquivo não aceito! Envie uma imagem ou vídeo.';
                    
                        } else{
                            // movendo arquivo para diretório
                            $moving = move_uploaded_file($tempName, $directory . $newFileName . '.' . $extension);
                    
                            // validação para sucesso de envio para diretório
                            if(!$moving){
                                $errors = 'Mídia ' . ($index + 1) . ':' .'Erro ao mover arquivo. Por favor, tente novamente!';
                
                            } else{
                                // definindo caminho para o arquivo   
                                $path = $directory . $newFileName . '.' . $extension;
            
                                // inserindo informações do upload e do arquivo na database   
                                $insert = mysqli_query($mysqli, "INSERT INTO `files_upload`.`upload_infos`(path, original_name, new_name, extension, description, file_date, id_user, user_email) VALUES('$path', '$fileName', '$newFileName', '$extension', '$description', '$date', '$id_user', '$user_email')");
                                
                                // verificação de sucesso da query insert
                                if(!$insert){
                                    $errors = 'Mídia ' . ($index + 1) . ': ' . "Erro ao inserir mídia(s) no banco de dados." . $mysqli->error;

                                } else{

                                    // variável em sessão que permite a visualização do modal, apagada logo após sua exibição
                                    $_SESSION['sucess'] = true;

                                    // caso o index atinja o número de arquivos enviados, para a execução e recarrega a página
                                    if(($index + 1) >= $countFiles){

                                        header('Location: ' . $_SERVER['REQUEST_URI']);
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
    }
} else{
    // caso usuário não esteja logado, redireciona-o para página de login
    header('Location: login.php');
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
    <link rel="stylesheet" href="../public/styles/addfiles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>

<body>

    <?php include('../components/headerMenu.php') ?>

    <?php if(isset($_SESSION['sucess']) && $_SESSION['sucess']){ ?>

        <div class="fade" id="fade"></div>
        <section class="modal-container" id="modal-container">

            <div class="modal-box">

                <div class="success-message-box">
                    <p class="message">Conteúdo adicionado com sucesso!<i class="fa-solid fa-check"></i></p>
                    <span class="error"><?php echo (isset($errors)) ? $errors : ''?></span>
                </div>

                <form class="sucess-btns-box" method="POST" action="">
                    <a href="myfiles.php" class="sucess-btn" value='myfiles'>Visualizar minhas mídias</a>
                    <button type="submit" class="sucess-btn" name='add-more' value='add-more' id='add-more'>Adicionar mais mídias</button>
                </form>

            </div>

        </section>
        
    <?php unset($_SESSION['sucess']);} ?>

    <aside class="aside-form">
        <form action="" method="POST" class="how-many-form" id="how-many-form">
            <div class="how-many-box">
                <h2>Adicionar mídias</h2>
                <div class="subtitle">
                    <label for="howMany">Quantas mídias gostaria de adicionar?<i style="color: red;">*</i></label>
                </div>
                <input required type="number" name="howMany" id="howMany" placeholder="Máx.: 10 mídias por vez...">
                <span class="error"><?php echo $errors ?></span>
                <button type="submit" disabled id='submitHowMany' name='submitHowMany'>Enviar</button>
            </div>
        </form>

        <form action="" method="POST" enctype="multipart/form-data" class="input-container" id="files-form">
        <?php

        // validação de envio de form de quantidade de inputs desejados    
        if(isset($_POST['submitHowMany'])){
            
            // atribuição de valor de inputs em variável para uso em laço de repetição
            $howMany = $_POST['howMany'];

            // enquanto o valor da variável i for menor ou igual ao valor do input, gera campos para envio de arquivos
            for($i = 1; $i <= $howMany; $i++){
        ?>
            <div class="input-box" id="input-box-<?php echo $i ?>">
                <p>Adicione a <?php echo $i ?>ª mídia<i style="color:red;">*</i></p>
                <label for="files_<?php echo $i ?>" class='modal-label' id='modal-label_<?php echo $i ?>'>
                    <span class="uploaded-name uploaded-name-<?php echo $i ?>" id="uploaded-name_<?php echo $i ?>">Inserir mídia</span>
                    <span class="search-file" id='search-file_<?php echo $i ?>'><i class="fa-solid fa-upload"></i> Selecionar...</span>
                </label>
                <input required style="display: none" type="file" name="files[]" id="files_<?php echo $i ?>" class="files files_<?php echo $i ?>">

                <label for="description">Digite a descrição da mídia<i style="color:red;">*</i></label>
                <input required type="text" id="description" name="description[]" maxlength="35" placeholder="Fale um pouco sobre essa mídia..."></textarea>

                <label for="data">Digite a data da mídia</label>
                <input type="date" name="date[]" id="date">
            </div>

            <script>
                const inputBox_<?php echo $i ?> = document.getElementById('input-box-<?php echo $i ?>');
                const searchFile_<?php echo $i ?> = inputBox_<?php echo $i ?>.querySelector('.search-file');
                const fileInput_<?php echo $i ?> = inputBox_<?php echo $i ?>.querySelector('.files');

                fileInput_<?php echo $i ?>.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    inputBox_<?php echo $i ?>.querySelector('.uploaded-name').textContent = file.name;
                    searchFile_<?php echo $i ?>.innerHTML = '<i class="fa-solid fa-arrow-rotate-left"></i>Substituir';
                });
            </script>

        <?php } ?>
            <div class="submit-btn-box">
                <button class="submit button-able" type="submit" id="submit" name="submit" value='files-submit'>Enviar</button>
            </div>
        </form>
        
        <?php } ?>
    </aside>

    <script src="../public/scripts/addfiles.js"></script>
</body>
</html>