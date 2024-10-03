<?php 
session_start(); // iniciando sessão

// incluindo conexão do banco de dados
include('../database/connectDB.php'); 

// autenticação de login e sessão privada
if(isset($_SESSION['logged']) && $_SESSION['logged'] != false){

    // aplicando email em sessão para variável para fazer query select
    $user_email = $_SESSION['user_email'];
    $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`users_infos` WHERE email = '$user_email'");

    // impondo retorno da query select em variável
    $data = mysqli_fetch_array($select);

    // atribuindo id de usuário em variável em sessão para uso futuro
    $_SESSION['id_user'] = $data['id_user'];

    // formatação para gerar nome completo(primeiro nome e último sobrenome):

    // pegando sobrenomes do banco de dados
    $lastName = $data['surnames'];

    // removendo espaços ao final da string
    $lastName = rtrim($lastName);

    // dividindo a string em palavras e criando array
    $lastName = explode(' ', $lastName);

    // obtendo última palavra do array gerado
    $lastName = end($lastName);

    // concatenando nome presente na database com o resultado da formatação acima
    $completeName = $data['name'] . ' ' . $lastName;

    // passando id de usuário para variável para uso futuro
    $user_id = $data['id_user'];

    // array de erros em sessão para futuras validações
    $_SESSION['errors'] = [
        'image' => '',
        'name' => '',
        'surname' => '',
        'email' => '',
        'pswd' => '',
        'confirm-pswd' => '',
        'nickname' => '',
    ];

    // criando e setando variáveis em sessão para armazenamento de conteúdo digitado pelo usuário
    $_SESSION['input-name'] = '';
    $_SESSION['input-surname'] = '';
    $_SESSION['input-nickname'] = '';
    $_SESSION['input-email'] = '';
    $_SESSION['input-pswd'] = '';
    $_SESSION['input-confirm-pswd'] = '';

    // verificação para caso a página seja recarregada, variáveis sejam limpas 
    // para que deixem de ser exibidas
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $_SESSION['input-name'] = '';
        $_SESSION['input-surname'] = '';
        $_SESSION['input-nickname'] = '';
        $_SESSION['input-email'] = '';
        $_SESSION['input-pswd'] = '';
        $_SESSION['input-confirm-pswd'] = '';

        $_SESSION['form-errors'] = false;

    }

    // validação de envio de arquivo da profile pic
    if(isset($_FILES['profile-pic'])){

        // atribuindo valor de botão clicado em variável
        $button = $_POST['profile-pic-btn'];

        if($button == 'remove' && $data['path_picture'] == '../files/users/default.jpg'){
            $_SESSION['errors']['image'] = 'Você não possui foto!';
            
        // caso o botão clicado seja o com valor igual a remove e a path não seja a default
        } else if($button == 'remove' && $data['path_picture'] != '../files/users/default.jpg'){

            // query uptade para setar path com imagem default
            $updateToDefault = mysqli_query($mysqli, "UPDATE `files_upload`.`users_infos` SET path_picture = '../files/users/default.jpg' WHERE id_user = '$user_id'");

            // caso haja erro, atribui erro na variável em sessão
            if(!$updateToDefault){
                $_SESSION['errors']['image'] = 'Falha ao apagar imagem!';

            } else{
                // apaga arquivo de dentro do diretório local
                unlink($data['path_picture']);

                // recarrega página para atualização de foto
                header('Location: ' . $_SERVER['PHP_SELF']);

                // função exit para evitar que possiveis processamentos causem erro
                exit();
            }
            
        } else{
            
            // adicionando arquivos enviado em variável
            $uploaded = $_FILES['profile-pic'];

            // adicionando informações do arquivo enviado em diferentes variáveis
            $fileName = $uploaded['name'];
            $tempName = $uploaded['tmp_name'];
            $error = $uploaded['error'];
            $size = $uploaded['size'];

            // buscando extensão do arquivo por meio da função pathinfo para fazer verificação de upload
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
            // definindo diretório de save dos arquivos
            $directory = '../files/users/';

            // atribuindo novo nome específico ao arquivo enviado
            $newFileName = uniqid();

            // validação de existência erros ao enviar
            if($error){
                $_SESSION['errors']['image'] = 'Erro ao fazer upload do arquivo';

            } else{

                // validação de tamanho de arquivo enviado (máx: 10mb)
                if($size > 10485760){
                    $_SESSION['errors']['image'] = 'Arquivo muito grande(Máx: 10mb)';

                } else{

                    // validação de tipo de arquivo enviado
                    if($extension != 'jpeg' && $extension != 'jpg' && $extension != 'png'){
                        $_SESSION['errors']['image'] = 'Tipo de arquivo não aceito';

                    } else{
                        // movendo arquivo para diretório
                        $moving = move_uploaded_file($tempName, $directory . $newFileName . '.' . $extension);
                
                        // validação para sucesso de envio para diretório
                        if(!$moving){
                            $_SESSION['errors']['image'] = 'Erro ao mover arquivo';
            
                        } else{
                            // caso seja diferente do caminho default, apaga a respectiva imagem do diretório local
                            if($data['path_picture'] != '../files/users/default.jpg'){
                                unlink($data['path_picture']);
                            }

                            // definindo caminho para o arquivo   
                            $path = $directory . $newFileName . '.' . $extension;
            
                            // inserindo informações do upload e do arquivo na database   
                            $updatePath = mysqli_query($mysqli, "UPDATE `files_upload`.`users_infos` SET path_picture = '$path' WHERE id_user = '$user_id'");
                                
                            // verificação de sucesso da query update
                            if(!$updatePath){
                                $_SESSION['errors']['image'] = "Erro ao atualizar imagem: " . $mysqli->error;

                            } else{
                                // recarrega a página para atualização da foto de perfil
                                header('Location: profile.php');
                            }
                        }
                    }
                }
            }
        }
    }

    // verificação de envio de formulário das informações
    if(isset($_POST['infos-submit'])){

        // limpando possíveis ataques XSS e comandos SQL recebidos
        $name = strip_tags($mysqli->real_escape_string($_POST['first-name']));

        // divide a frase em palavras e cria um array
        $name = explode(' ', $name);

        // retorna o primeiro índice do array resultante
        $name = $name[0];

        // setando todas as letras da palavra como minúscula
        $name = strtolower($name);

        // tornando a primeira letra da palavra maiúscula
        $name = ucfirst($name);

        // prevenindo ataques e setando todas as letras como minúsculas
        $surname = strip_tags($mysqli->real_escape_string($_POST['surnames']));
        $surname = strtolower($surname);

        // tornando a primeira letra de cada palavra maiúscula
        $surname = ucwords($surname);

        // repetindo o processo da variável name
        $nickname = strip_tags($mysqli->real_escape_string($_POST['nickname']));
        $nickname = explode(' ', $nickname);
        $nickname = $nickname[0];
        
        $email = $mysqli->real_escape_string($_POST['email']);
        $pswd = strip_tags($mysqli->real_escape_string($_POST['pswd']));
        $confirm_pswd = strip_tags($mysqli->real_escape_string($_POST['confirm-pswd']));

        // definindo variável como true para fazer o controle das condicionais e inserção no banco
        $validate = true;

        // adicionando valores nas variáveis de sessão para que sejam exibidas em erro de validação
        $_SESSION['input-name'] = $name;
        $_SESSION['input-surname'] = $surname;
        $_SESSION['input-nickname'] = $nickname;
        $_SESSION['input-email'] = $email;
        $_SESSION['input-pswd'] = $pswd;
        $_SESSION['input-confirm-pswd'] = $confirm_pswd;

        // validação de nome
        if(strlen($name) < 3){
            $_SESSION['errors']['name'] = 'Insira um nome válido';
            $validate = false;

        } else if(preg_match('/[0-9]/', $name)){
            $_SESSION['errors']['name'] = 'Não insira números';
            $validate = false;

        } else if(preg_match('/[^a-zA-Z0-9À-ÿ]/', $name)){
            $_SESSION['errors']['name'] = 'Não insira caracteres especiais';
            $validate = false;

        } else if(preg_match('/[À-ÿ]/u', $name) && strlen($name) < 4){
            $_SESSION['errors']['name'] = 'Insira um nome válido!';
            $validate = false;
        }

        // validação de sobrenome
        if(strlen($surname) <= 2){
            $_SESSION['errors']['surname'] = 'Insira um sobrenome válido!';
            $validate = false;

        } else if(preg_match('/[0-9]/', $surname)){
            $_SESSION['errors']['surname'] = 'Não insira números';
            $validate = false;

        } else if(preg_match('/[^a-zA-Z0-9À-ÿ ]/', $surname)){
            $_SESSION['errors']['surname'] = 'Não insira caracteres especiais';
            $validate = false;

        } else if(preg_match('/[À-ÿ]/u', $surname) && strlen($surname) < 4){
            $_SESSION['errors']['surname'] = 'Insira um sobrenome válido!';
            $validate = false;
        }

        if(strlen($nickname) == 1){
            $_SESSION['errors']['nickname'] = 'Insira um nickname válido!';
            $validate = false;
        }

        //validação de email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['errors']['email'] = 'Insira um email válido!';
            $validate = false;
        } 
        
        // validação de senha(mínimo de oito caracteres, contendo entre eles ao menos um número, uma letra maiúscula,
        // uma letra minúscula e um caractere especial)
        if(strlen($pswd) <= 7){
            $_SESSION['errors']['pswd'] = 'Senha muito curta! Mínimo 8 caracteres';
            $validate = false;

        } else if(strpos($pswd, ' ')){
            $_SESSION['errors']['pswd'] = 'A senha não deve conter espaços';
            $validate = false;

        } else if(!preg_match('/[A-Z]/', $pswd)){
            $_SESSION['errors']['pswd'] = 'Insira uma letra maiúscula';
            $validate = false;

        } else if(!preg_match('/[a-z]/', $pswd)){
            $_SESSION['errors']['pswd'] = 'Insira uma letra minúscula';
            $validate = false;

        } else if(!preg_match('/[0-9]/', $pswd)){
            $_SESSION['errors']['pswd'] = 'Insira um caractere numérico';
            $validate = false;

        } else if(!preg_match('/[^a-zA-Z0-9]/', $pswd)){
            $_SESSION['errors']['pswd'] = 'Insira um caractere especial';
            $validate = false;
        }

        // validação de confirmação de senha
        if($confirm_pswd != $pswd){
            $_SESSION['errors']['confirm-pswd'] = 'As senhas não coincidem!';
            $validate = false;
        }

        // verificação de sucesso nas validações
        if($validate){

            // query select para verificação de email existente na database
            $selectInfos = mysqli_query($mysqli, "SELECT email FROM `files_upload`.`users_infos` WHERE email = '$email'");
            $dataInfos = mysqli_fetch_array($selectInfos);

            // validação de email existente
            if(mysqli_num_rows($selectInfos) > 1){
                $_SESSION['errors']['email'] = 'Email já cadastrado!';
                $_SESSION['form-errors'] = true;

            } else{

                // setando valores em sessão para serem exibidos ao usuário
                $_SESSION['pswd'] = $pswd;
                $_SESSION['user_email'] = $email;
                $_SESSION['input-email'] = $_SESSION['user_email'];

                // encriptografando a senha para inserção segura no banco de dados
                $hashPswd = password_hash($pswd, PASSWORD_DEFAULT);
                $updateInfos = mysqli_query($mysqli, "UPDATE `files_upload`.`users_infos` SET name = '$name', surnames = '$surname', email = '$email', password = '$hashPswd', nickname = '$nickname' WHERE id_user = '$user_id'");
                
                // limpando variáveis em sessão após update da database
                unset($_SESSION['input-name']);
                unset($_SESSION['input-surname']);
                unset($_SESSION['input-nickname']);
                unset($_SESSION['input-email']);
                unset($_SESSION['input-pswd']);
                unset($_SESSION['input-confirm-pswd']);

                unset($_SESSION['errors']);  

                // recarregando página
                header('Location: profile.php');
                exit();
            }

        } else{
            // caso haja erro presente nas validações, seta variável de erro como true(js ao final do arquivo)
            $_SESSION['form-errors'] = true;
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
    <link rel="stylesheet" href="../public/styles/profile.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <?php include('../components/headerMenu.php')?>


    <main>
        <section class="profile-area">

            <form class="picture" action="" enctype="multipart/form-data" method="post" id="profile-form">

                <div class="error-box">
                    <!-- exibindo mensagem de erro retornada das validações php -->
                    <span class="error"><?php echo $_SESSION['errors']['image']?></span>
                </div>

                <img class="photo" src="<?php echo $data['path_picture'] ?>" alt="">

                <div class="buttons-profile-box">
                    
                    <label for="profile-pic" class="label-pic disabled" id="id-pic">Alterar foto de perfil</label>
                    <button disabled type="submit" name="profile-pic-btn" id="profile-pic-remove" class="disabled" value="remove">Remover foto de perfil</button>
                    <input disabled type="file" class="input" name='profile-pic' id='profile-pic'>

                </div>

                <button type="submit" id="profile-pic-btn" name="profile-pic-btn"></button>
            
            </form>

            <div class="content"></div>

            <div class="infos">
                
                <form action="" method="POST" class="infos-form">

                    <div class="title">
                        <h2>Bem-vindo(a), <br> <?php echo $completeName?>!</h2>
                        <p class="sub">Para alterar as informações, clique no botão abaixo.</p>
                    </div>

                    <div class="input-container">

                        <div class="input-box">
                            <label for="first-name">Primeiro Nome<i style="color: red;">*</i></label>
                            <i id="icon" class="fa-regular fa-user material-icons first"></i>
                            <input disabled maxlength="40" required class="input input-disabled" type="text" name="first-name" id="first-name" placeholder="Digite seu nome" 
                            value="<?php echo (strlen($_SESSION['input-name']) != 0) ? $_SESSION['input-name'] : $data['name']?>">
                            <!-- acima, uma validação ternária para verificação de conteúdo em variáveis para serem exibidas ao usuário -->
                            <div class="error-box">
                                <span class="error"><?php echo $_SESSION['errors']['name']?></span>
                            </div>
                            
                        </div>

                        <div class="input-box">
                            <label for="surnames">Sobrenome<i style="color: red;">*</i></label>
                            <i id="icon" class="fa-solid fa-user material-icons second"></i>
                            <input disabled maxlength="225" required class="input input-disabled" type="text" name="surnames" id="surnames" placeholder="Digite seu sobrenome" 
                            value="<?php echo (strlen($_SESSION['input-surname']) != 0) ? $_SESSION['input-surname'] : $data['surnames']?>">
                            
                            <div class="error-box">
                                <span class="error"><?php echo $_SESSION['errors']['surname']?></span>
                            </div>
                            
                        </div>
                        
                    </div>

                    <div class="input-container">

                        <div class="input-box">
                            <label for="nickname">Apelido</label>
                            <i id="icon" class="fa-solid fa-user-group material-icons first"></i>
                            <input disabled maxlength="40" class="input input-disabled" type="text" name="nickname" id="nickname" placeholder="Digite seu apelido" 
                            value="<?php echo (strlen($_SESSION['input-nickname']) != 0) ? $_SESSION['input-nickname'] : $data['nickname']?>">
                            
                            <div class="error-box">
                                <span class="error"><?php echo $_SESSION['errors']['nickname']?></span>
                            </div>
                        </div>

                        <div class="input-box">
                            <label for="email">Email<i style="color: red;">*</i></label>
                            <i id="icon" class="fa-regular fa-envelope material-icons second"></i>
                            <input disabled maxlength="220" required class="input input-disabled" type="email" name="email" id="email" placeholder="Digite seu email" 
                            value="<?php echo (strlen($_SESSION['input-email']) != 0) ? $_SESSION['input-email'] : $_SESSION['user_email']?>">
                            
                            <div class="error-box">
                                <span class="error"><?php echo $_SESSION['errors']['email']?></span>
                            </div>
                        </div>
                        
                    </div>

                    <div class="input-container">

                        <div class="input-box">
                            <label for="pswd">Senha<i style="color: red;">*</i></label>
                            <i id="icon" class="fa-solid fa-lock material-icons first"></i>
                            <input disabled maxlength="220" required class="input input-disabled" type="password" name="pswd" id="pswd" placeholder="Digite sua senha (Ex.:Abc1234@)" 
                            value="<?php echo (strlen($_SESSION['input-pswd']) != 0) ? $_SESSION['input-pswd'] : $_SESSION['pswd']?>">
                            <i id='eye-icon' class="fa-regular fa-eye-slash first-eye eye material-icons"></i>
                            <!-- <span id="error-js" class="error-js"></span> -->
                            <div class="error-box">
                                <span class="error" style="color: rgb(255, 255, 97)"><?php echo $_SESSION['errors']['pswd']?></span>
                            </div>
                        </div>

                        <div class="input-box">
                            <label for="confirm-pswd">Confirme sua senha<i style="color: red;">*</i></label>
                            <i id="icon" class="fa-solid fa-user-lock material-icons second"></i>
                            <input disabled required type="password" class="input input-disabled" name="confirm-pswd" id="confirm-pswd" placeholder="Digite sua senha" 
                            value="<?php echo (strlen($_SESSION['input-confirm-pswd']) != 0) ? $_SESSION['input-confirm-pswd'] : $_SESSION['pswd']?>">
                            <i id='eye-icon' class="fa-regular fa-eye-slash second-eye eye material-icons"></i>
                            <div class="error-box">
                                <span class="error"><?php echo $_SESSION['errors']['confirm-pswd']?></span>
                            </div>
                            
                        </div>

                    </div>

                    <div class="buttons-box">
                        <button type="button" id="able-button" class="able-button">Alterar informações</button>
                        <button type="submit" style="display: none" name="infos-submit" id="infos-submit" class="submit button-dsb">Salvar alterações</button>
                        <button type="button" style="display: none" name="disable-button" id="cancel-btn" class="button-dsb">Cancelar alterações</button>
                    </div>

                </form>
            </div>

        </section>
    </main>

    <script src="../public/scripts/profile.js"></script>

    <script>
        // ouvidor de evento para clique de botão cancelar
        cancelBtn.addEventListener('click', ()=>{

            // apontando inputs em variáveis para adicionar valores padrão
            let name = document.getElementById('first-name');
            let surname = document.getElementById('surnames');
            let nickname = document.getElementById('nickname');
            let email = document.getElementById('email');
            let password = document.getElementById('pswd');
            let confirmPassword = document.getElementById('confirm-pswd');

            // adicionando valores padrão nos inputs
            name.value = "<?php echo $data['name'] ?>";
            surname.value = "<?php echo $data['surnames'] ?>";
            nickname.value = "<?php echo $data['nickname'] ?>";
            email.value = "<?php echo $_SESSION['email'] ?>";
            password.value = "<?php echo $_SESSION['pswd'] ?>";
            confirmPassword.value = "<?php echo $_SESSION['pswd'] ?>";

            // para cada input encontrado, modifica sua visualização e a de botões do formulário
            inputs.forEach(input =>{

                input.disabled = true;
                input.classList.remove('input-able');
                input.classList.add('input-disabled');
                submitBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                ableBtn.style.display = 'block';
                labelPicture.classList.remove('able');
                labelPicture.classList.add('disabled');
                removePic.classList.remove('able');
                removePic.classList.add('disabled');
                removePic.disabled = true;
            });

        });

        // script php+js para verificação de erro presente no formulário, atualizando a página
        // sem perder o conteúdo digitado pelo usuário, cujo qual é retornado ao mesmo(mesmo que haja erro)
        <?php
        if (isset($_SESSION['form-errors']) && $_SESSION['form-errors']) {
            echo 'ableBtn.click();';
            unset($_SESSION['form-errors']); // Limpa a variável de sessão
        }
        ?>

    </script>
</body>
</html>
