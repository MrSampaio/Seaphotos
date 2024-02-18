<?php 
session_start(); // iniciando sessão para atribuição de valores em super globais

// incluindo conexão do banco de dados
include('../database/connectDB.php'); 

// definindo as super globais como vazias
$_SESSION['email'] = '';
$_SESSION['tmp_pswd'] = '';

// caso a página seja recarregada, torna as super globais vazias novamente
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    unset($_SESSION['email']);
    unset($_SESSION['pswd']);
}

// array de erros para futuras validações
$errors = [
    'email' => '',
    'pswd' => '',
];

// gatilho para envio de formulário
if(isset($_POST['submit'])){

    // limpando possíveis ataques XSS e comandos SQL recebidos
    $email = $mysqli->real_escape_string($_POST['email']);
    $pswd = strip_tags($mysqli->real_escape_string($_POST['pswd']));

    // atribuindo valores limpos e formatados dos inputs em super globais para serem exibidos
    // caso haja erros a serem corrigidos
    $_SESSION['email'] = $email;
    $_SESSION['tmp_pswd'] = $pswd;

    //validação de email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Insira um email válido!';

    } else{

        $select = mysqli_query($mysqli, "SELECT id_user, email, password FROM `files_upload`.`users_infos` WHERE email = '$email'");
        $data = mysqli_fetch_array($select);

        if(mysqli_num_rows($select) == 0){
            $errors['email'] = 'Email não cadastrado.';

        } else if(!password_verify($pswd, $data['password'])){
            $errors['pswd'] = 'Senha incorreta!';

        } else{

            //criando variável em sessão para vaidação de sessão privada
            $_SESSION['logged'] = true;

            // atribuindo email em variável em sessão para validação de login
            $_SESSION['user_email'] = $data['email'];

            // atribuindo senha em variável em sessão para ser exibida no perfil
            $_SESSION['pswd'] = $_SESSION['tmp_pswd'];

            // redirecionando o usuário para sua página privada
            header('Location: profile.php');

            // fechando conexão de banco de dados
            mysqli_close($mysqli);

            // saindo da página
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="apple-touch-icon" sizes="180x180" href="../public/assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/assets/favicon-16x16.png">  
    <script src="https://kit.fontawesome.com/bbdbe3941a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../public/styles/styleLogin.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEAPHOTOS login</title>
</head>
<body>

    <?php include('../components/logo.php') ?>

    <section class="boxLeft">
        <img src="../public/assets/login.gif" alt="">
    </section>

    <section class="boxRight">

        <form action="" method="post">
            <div class="inputs-container">

                <div class="title">
                    <h2>Bem-vindo(a)!</h2>
                    <p class="sub">Entre com suas informações de cadastro</p>
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
                    <input maxlength="220" required class="input" type="password" name="pswd" id="pswd" placeholder="Digite sua senha" value="<?php echo $_SESSION['tmp_pswd']?>">
                    <i id='eye-icon' class="fa-regular fa-eye-slash eye material-icons"></i>
                    <span class="error"><?php echo $errors['pswd']?></span>
                </div>

                <div class="button-box">
                    <button type="submit" name="submit" id="submit" class="button">Enviar</button>
                    <a href="singup.php"><p>Não possui cadastro? <span>Cadastre-se aqui</span></p></a>
                </div>
            </div>
        </form>
    </section>

    <script src="../public/scripts/login.js"></script>
</body>
</html>