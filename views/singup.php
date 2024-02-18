<?php 
session_start(); // iniciando sessão para atribuição de valores em super globais

// incluindo conexão do banco de dados
include('../database/connectDB.php'); 

// definindo as super globais como vazias
$_SESSION['name'] = '';
$_SESSION['surname'] = '';
$_SESSION['email'] = '';
$_SESSION['pswd'] = '';
$_SESSION['confirm_pswd'] = '';

// caso a página seja recarregada, torna as super globais vazias novamente
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    unset($_SESSION['name']);
    unset($_SESSION['surname']);
    unset($_SESSION['email']);
    unset($_SESSION['pswd']);
    unset($_SESSION['confirm_pswd']);
}

// array de erros para futuras validações
$errors = [
    'name' => '',
    'surname' => '',
    'email' => '',
    'pswd' => '',
    'confirm-pswd' => '',
    'insert' => '',
];

// gatilho para envio de formulário
if(isset($_POST['submit'])){

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

    $email = $mysqli->real_escape_string($_POST['email']);
    $pswd = strip_tags($mysqli->real_escape_string($_POST['pswd']));
    $confirm_pswd = strip_tags($mysqli->real_escape_string($_POST['confirm-pswd']));

    // atribuindo valores limpos e formatados dos inputs em super globais para serem exibidos
    // caso haja erros a serem corrigidos
    $_SESSION['name'] = $name;
    $_SESSION['surname'] = $surname;
    $_SESSION['email'] = $email;
    $_SESSION['pswd'] = $pswd;
    $_SESSION['confirm_pswd'] = $confirm_pswd;

    // definindo variável como true para fazer o controle das condicionais e inserção no banco
    $validate = true;

    // validação de nome
    if(strlen($name) < 3){
        $errors['name'] = 'Insira um nome válido!';
        $validate = false;

    } else if(preg_match('/[0-9]/', $name)){
        $errors['name'] = 'O nome não deve conter números';
        $validate = false;

    } else if(preg_match('/[^a-zA-Z0-9À-ÿ]/', $name)){
        $errors['name'] = 'O nome não deve conter caracteres especiais';
        $validate = false;

    } else if(preg_match('/[À-ÿ]/u', $name) && strlen($name) < 4){
        $errors['name'] = 'Insira um nome válido!';
        $validate = false;
    }
    
    // validação de sobrenome
    if(strlen($surname) <= 2){
        $errors['surname'] = 'Insira um sobrenome válido!';
        $validate = false;

    } else if(preg_match('/[0-9]/', $surname)){
        $errors['surname'] = 'O sobrenome não deve conter números';
        $validate = false;

    } else if(preg_match('/[^a-zA-Z0-9À-ÿ ]/', $surname)){
        $errors['surname'] = 'Não insira caracteres especiais';
        $validate = false;

    } else if(preg_match('/[À-ÿ]/u', $surname) && strlen($surname) < 4){
        $errors['surname'] = 'Insira um sobrenome válido!';
        $validate = false;
    }

    //validação de email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Insira um email válido!';
        $validate = false;
    } 
    
    // validação de senha(mínimo de oito caracteres, contendo entre eles ao menos um número, uma letra maiúscula,
    // uma letra minúscula e um caractere especial)
    if(strlen($pswd) <= 7){
        $errors['pswd'] = 'Senha muito curta! Mínimo 8 caracteres';
        $validate = false;

    } else if(strpos($pswd, ' ')){
        $errors['pswd'] = 'A senha não deve conter espaços';
        $validate = false;

    } else if(!preg_match('/[A-Z]/', $pswd)){
        $errors['pswd'] = 'A senha deve conter ao menos uma letra maiúscula';
        $validate = false;

    } else if(!preg_match('/[a-z]/', $pswd)){
        $errors['pswd'] = 'A senha deve conter ao menos uma letra minúscula';
        $validate = false;

    } else if(!preg_match('/[0-9]/', $pswd)){
        $errors['pswd'] = 'A senha deve conter ao menos um caractere numérico';
        $validate = false;

    } else if(!preg_match('/[^a-zA-Z0-9]/', $pswd)){
        $errors['pswd'] = 'A senha deve conter ao menos um caractere especial';
        $validate = false;
    }

    // validação de confirmação de senha
    if($confirm_pswd != $pswd){
        $errors['confirm-pswd'] = 'As senhas não coincidem!';
        $validate = false;
    }
    
    if($validate){

        // select para verificação de email existente em banco de dados
        $select = mysqli_query($mysqli, "SELECT * FROM `files_upload`.`users_infos` WHERE email = '$email'");

        // verificação de quantidade de linhas retornadas da consulta
        if(mysqli_num_rows($select) != 0){
            $errors['email'] = 'Email já cadastrado!';

        } else{

            // encriptografando a senha para inserção segura no banco de dados
            $pswd = password_hash($pswd, PASSWORD_DEFAULT);

            // query de inserção de informações no banco de dados
            $insertInfos = mysqli_query($mysqli, "INSERT INTO `files_upload`.`users_infos`(name, surnames, email, password) VALUES('$name', '$surname', '$email', '$pswd')");

            // validação de sucesso da query insert
            if(!$insertInfos){
                $errors['insert'] = 'Erro ao cadastrar usuário. Por favor, tente novamente.';

            } else{
                
                //limpando variáveis em sessão
                unset($_SESSION['name']);
                unset($_SESSION['surname']);
                unset($_SESSION['confirm_pswd']);
                
                //criando variável em sessão para vaidação de sessão privada
                $_SESSION['logged'] = true;

                // atribuindo email em variável em sessão para validação de login
                $_SESSION['user_email'] = $email;

                // redirecionando o usuário para sua página privada
                header('Location: profile.php');

                // fechando conexão de banco de dados
                mysqli_close($mysqli);

                // saindo da página
                exit();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <script src="https://kit.fontawesome.com/bbdbe3941a.js" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/favicon-16x16.png">  
    <link rel="stylesheet" href="../public/styles/styleSingUp.css">
    <title>SEAPHOTOS cadastro</title>
</head>
<body>

    <?php include('../components/logo.php') ?>

    <section class="boxLeft">
        <img id="image" src="../public/assets/singupsea.gif" alt="">
    </section>

    <section class="boxRight">

        <form action="" method="post">
            <div class="inputs-container">
                <div class="title">
                    <h2>Bem-vindo(a)!</h2>
                    <p class="sub">Para se cadastrar, basta preencher os campos.</p>
                </div>

                <div class="input-box">
                    <label for="first-name">Primeiro Nome<i style="color: red;">*</i></label>
                    <i id="icon" class="fa-regular fa-user material-icons"></i>
                    <input maxlength="100" required class="input" type="text" name="first-name" id="first-name" placeholder="Digite seu primeiro nome" value="<?php echo $_SESSION['name']?>">
                    <span class="error"><?php echo $errors['name']?></span>
                </div>

                <div class="input-box">
                    <label for="surnames">Sobrenome<i style="color: red;">*</i></label>
                    <i id="icon" class="fa-solid fa-user material-icons"></i>
                    <input maxlength="225" required class="input" type="text" name="surnames" id="surnames" placeholder="Digite seu sobrenome" value="<?php echo $_SESSION['surname']?>">
                    <span class="error"><?php echo $errors['surname']?></span>
                </div>
                
                <div class="input-box">
                    <label for="email">Email<i style="color: red;">*</i></label>
                    <i id="icon" class="fa-regular fa-envelope material-icons"></i>
                    <input maxlength="220" required class="input" type="email" name="email" id="email" placeholder="Digite seu email" value="<?php echo $_SESSION['email']?>">
                    <span class="error"><?php echo $errors['email']?></span>
                </div>
            
                <div class="input-box">
                    <label for="pswd">Senha<i style="color: red;">*</i></label>
                    <i id="icon" class="fa-solid fa-lock material-icons"></i>
                    <input maxlength="220" required class="input" type="password" name="pswd" id="pswd" placeholder="Digite sua senha (Ex.:Abc1234@)" value="<?php echo $_SESSION['pswd']?>">
                    <i id='eye-icon' class="fa-regular fa-eye-slash eye material-icons"></i>
                    <!-- <span id="error-js" class="error-js"></span> -->
                    <span class="error" style="color: rgb(255, 255, 97)"><?php echo $errors['pswd']?></span>
                </div>
            
                <div class="input-box">
                    <label for="confirm-pswd">Confirme sua senha<i style="color: red;">*</i></label>
                    <i id="icon" class="fa-solid fa-user-lock material-icons"></i>
                    <input required type="password" class="input" name="confirm-pswd" id="confirm-pswd" placeholder="Digite sua senha" value="<?php echo $_SESSION['confirm_pswd']?>">
                    <i id='eye-icon' class="fa-regular fa-eye-slash eye material-icons"></i>
                    <span class="error"><?php echo $errors['confirm-pswd']?></span>
                </div>

                <div class="button-box">
                    <span class="error"><?php echo $errors['insert']?></span>
                    <button type="submit" name="submit" id="submit" class="button">Enviar</button>
                    <a href="login.php"><p>Já possui cadastro? <span>Faça seu login!</span></p></a>
                </div>
            </div>
        </form>
    </section>

    <script src="../public/scripts/singup.js"></script>


</body>
</html>