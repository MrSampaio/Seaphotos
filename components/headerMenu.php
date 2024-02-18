<?php 

if(isset($_SESSION['logged']) && $_SESSION['logged']){
    // header('Location: ../views/profile.php');

    // incluindo conexão do banco de dados
    include('../database/connectDB.php'); 

    // aplicando email em sessão para variável para fazer query select
    $user_email = $_SESSION['user_email'];
    $selectPicture = mysqli_query($mysqli, "SELECT path_picture, name FROM `files_upload`.`users_infos` WHERE email = '$user_email'");

    // impondo retorno da query select em variável
    $dataPicture = mysqli_fetch_array($selectPicture);
}
?>

<header>
    <h1>
        <div class="open-menu" id="open-menu">
            <i class="fa-solid fa-bars" id="menu-icon"></i>
        </div>
        <a class="header-title" href="index.php">seaphotos</a>
    </h1>

    <nav id="nav">
        <ul>
            <li class="li-menu">
                <a href="index.php" class="logo">
                    <img src="../public/assets/logowhite.png">
                    <span class="nav-item">seaphotos</span>
                </a>
            </li>

            <li class="li-menu">
                <a href="profile.php">
                    <i class="fas fa-regular fa-user"></i>
                    <span class="nav-item">Minha conta</span>
                </a>
            </li>

            <li class="li-menu">
                <a href="myfiles.php">
                    <i class="fas fa-solid fa-photo-film"></i>
                    <span class="nav-item">Minhas mídias</span>
                </a>
            </li>

            <li class="li-menu">
                <a href="addfiles.php">
                    <i class="fas fa-solid fa-plus"></i>
                    <span class="nav-item">Adicionar mídias</span>
                </a>
            </li>

            <li class="li-menu">
                <a href="index.php">
                    <i class="fas fa-regular fa-image"></i>
                    <span class="nav-item">Galeria</span>
                </a>
            </li>

            <?php if(isset($_SESSION['logged']) && $_SESSION['logged']){?>
                <li class="li-menu">
                    <a href="../public/scripts/logout.php">
                        <i class="fas fa-solid fa-arrow-right-from-bracket"></i>
                        <span class="nav-item">Logout</span>
                    </a>
                </li>
            <?php } else {?>

            <li class="li-menu">
                <a href="login.php">
                    <i class="fas fa-solid fa-right-to-bracket"></i>
                    <span class="nav-item">Login</span>
                </a>
            </li>

            <li class="li-menu">
                <a href="singup.php">
                    <i class="fas fa-solid fa-user-plus"></i>
                    <span class="nav-item">Cadastre-se</span>
                </a>
            </li>

            <?php }?>
        </ul>
    </nav>

    <?php if(isset($_SESSION['logged']) && $_SESSION['logged']){ ?>

        <div class="user-photo">
            <a href="profile.php">
                <img src="<?php echo $dataPicture['path_picture']?>" alt="">
            </a>  
        </div>

    <?php } else{ ?>

        <div class="user-photo">
            <a href="login.php">
                <img src="../files/users/default.jpg" alt="">
            </a>  
        </div>
        
    <?php } ?>

    <script src="../public/scripts/menu.js" defer></script>
    
</header>
