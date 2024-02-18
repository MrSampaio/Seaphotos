<?php 

if(isset($_SESSION['logged']) && $_SESSION['logged']){

    // incluindo conexão do banco de dados
    include('../database/connectDB.php'); 

    // aplicando email em sessão para variável para fazer query select
    $user_email = $_SESSION['user_email'];
    $selectPicture = mysqli_query($mysqli, "SELECT path_picture, name FROM `files_upload`.`users_infos` WHERE email = '$user_email'");

    // impondo retorno da query select em variável
    $dataPicture = mysqli_fetch_array($selectPicture);
}

?>

<div class="image-modal" id="image-modal">
    <span class="close-image" id="close-image"><i class="fa-solid fa-x"></i></span>
    <img src="" alt="" class="image-modal-content" id="image-modal-content">
</div>