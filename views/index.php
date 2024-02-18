<?php 
session_start(); // iniciando sessão

// incluindo conexão do banco de dados
include('../database/connectDB.php');

// query select para exibição de mídias, ordenando da mais recente para a mais antiga
$select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`upload_infos` ORDER BY date_upload DESC");
// impondo retorno da query select em variável
$data = mysqli_fetch_array($select);

// gatilho de existência de pesquisa
if(isset($_POST['search-input'])){

    // atribuindo valor de input de pesquisa em variável
    $search = strip_tags($mysqli->real_escape_string($_POST['search-input']));

    // select query substituindo valores default(todas as midias do usuário) para os da pesquisa
    $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`upload_infos` WHERE description LIKE CONCAT('%', '$search', '%') ORDER BY date_upload DESC");

    //(recarregamento da página feito com javascript para melhor experiência do usuário) -> myfiles.js
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/favicon-16x16.png">  
    <script src="https://kit.fontawesome.com/bbdbe3941a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/styles/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEAPHOTOS</title>
</head>
<body>

    <?php include('../components/headerMenu.php') ?>
    <?php include('../components/imageModal.php') ?>

    <section class="profile-card">

        <div class="search-title-box">
            <p class="myfiles">Pesquisar arquivos</p>
            <div class="line-myfiles"></div>

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

    <main class="container">

        <?php echo (isset($_POST['search-input']) ? "<p>Exibindo resultados para '<span style='color: var(--search-font-color)'>" . $_POST['search-input'] . "</span>' :</p>" : '') ?>

        <form class="container-image" method="POST" enctype="multipart/form-data" action="">
            <?php echo ((mysqli_num_rows($select) < 1) ? "<p style='color: var(--disabled-font-color)'>Nenhuma mídia encontrada :( </p>" : "")?>
        
            <!-- para cada linha presente na select query, exibe as informações respectivas a partir do array $file-->
            <?php foreach ($select as $index => $file) { 

                // atribuindo id de usuário em variável para query select
                $id_user = $file['id_user'];
                //query select com id de usuário para requisitar o nome
                $selectUsers = mysqli_query($mysqli,"SELECT name, nickname FROM `files_upload`.`users_infos` WHERE id_user = '$id_user'");
                // adicionando nome de usuário em array
                $username = mysqli_fetch_array($selectUsers);
            ?>

            <div class="image">
                <!-- Caso o arquivo seja um vídeo, renderiza o bloco html para exibição de vídeos, do contrário, exibe o bloco para imagens-->
                <?php if ($file['extension'] == 'mp4') { ?>

                    <video src="<?php echo $file['path'] ?>" controls class="gallery-media" id="gallery-media" alt="<?php echo $file['description'] ?>"></video>
                    <div class="infos-box">
                        <p class="input-disabled" name="description" id="description"><?php echo $file['description'] ?></p>
                        <p class="input-disabled" name="date" id="date"><?php echo (!empty($file['file_date']) ? $file['file_date'] : 'Sem data') ?></p>

                        <div class="content"></div>

                        <div class="user-name">
                            <p><?php echo (!empty($username['nickname']) ? $username['nickname'] : $username['name'])?></p>
                            <div class="content-username"></div>
                        </div>
                    </div>

                    

                <?php } else { ?>

                    <img src="<?php echo $file['path'] ?>" class="gallery-media" id="gallery-image" alt="<?php echo $file['description'] ?>">

                    <div class="infos-box">
                        <p class="input-disabled" name="description" id="description"><?php echo $file['description'] ?></p>
                        <p class="input-disabled" name="date" id="date"><?php echo (!empty($file['file_date']) ? $file['file_date'] : 'Sem data') ?></p>

                        <div class="content"></div>

                        <div class="user-name">
                            <p><?php echo (!empty($username['nickname']) ? $username['nickname'] : $username['name'])?></p>
                            <div class="content-username"></div>
                        </div>
                    </div>

                <?php } ?>

            </div>

            <?php } ?>
        </form>
    </main>

    <script src="../public/scripts/index.js" defer></script>
</body>
</html>