<?php
    session_start();

    if(isset($_SESSION['idUser'])){
        header('Location: menu.php');
    }


    if(isset($_POST['email'])){   //dodać warunek dla niewpisanego hasla
        $email = $_POST['email'];
        $password = $_POST['password'];

        $email = htmlentities($email,ENT_QUOTES, "UTF-8");
        $password = htmlentities($password,ENT_QUOTES, "UTF-8");

        require_once "database.php";

        $result = $connection -> prepare('SELECT * FROM users WHERE email = :email');
        $result -> bindValue(':email',$email,PDO::PARAM_STR);
        $result -> execute();

        $numOfEmail = $result -> rowCount();
        if($numOfEmail > 0){
            $user = $result -> fetch();
            if(password_verify($password,$user['password']))
            {   
                $_SESSION['idUser'] = $user['id'];
                $_SESSION['userName'] = $user['username'];
                header('Location: menu.php');
            }else{
                $_SESSION['errLogPassword'] = "Podane hasło jest nieprawidłowe.";
            }

        }else{
            $_SESSION['errLogEmail'] = "Podany e-mail nie istnieje w bazie.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Personal Budget</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container bg-white p-0 modal-xl">

        <header>
            <div class="bg-primary">
                <h1 class="text-white text-center p-4"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                        fill="currentColor" class="bi bi-piggy-bank" viewBox="0 0 16 16">
                        <path
                            d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm1.138-1.496A6.613 6.613 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 0 0 .286-.958A7.602 7.602 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962z" />
                        <path fill-rule="evenodd"
                            d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069c0-.145-.007-.29-.02-.431.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a.95.95 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.735.735 0 0 0-.375.562c-.024.243.082.48.32.654a2.112 2.112 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595zM2.516 6.26c.455-2.066 2.667-3.733 5.448-3.733 3.146 0 5.536 2.114 5.536 4.542 0 1.254-.624 2.41-1.67 3.248a.5.5 0 0 0-.165.535l.66 2.175h-.985l-.59-1.487a.5.5 0 0 0-.629-.288c-.661.23-1.39.359-2.157.359a6.558 6.558 0 0 1-2.157-.359.5.5 0 0 0-.635.304l-.525 1.471h-.979l.633-2.15a.5.5 0 0 0-.17-.534 4.649 4.649 0 0 1-1.284-1.541.5.5 0 0 0-.446-.275h-.56a.5.5 0 0 1-.492-.414l-.254-1.46h.933a.5.5 0 0 0 .488-.393zm12.621-.857a.565.565 0 0 1-.098.21.704.704 0 0 1-.044-.025c-.146-.09-.157-.175-.152-.223a.236.236 0 0 1 .117-.173c.049-.027.08-.021.113.012a.202.202 0 0 1 .064.199z" />
                    </svg> personalbudget.pl</h1>
            </div>
        </header>

        <article>
            <div>
                <blockquote class="blockquote d-block w-100">
                    <?php
                        if(isset($_SESSION['registrationComplete']))
                        {
                            echo '<h2 class="text-center p-5">Rejestracja przebiegła pomyślnie, teraz możesz się zalogować:</h2>';
                            unset($_SESSION['registrationComplete']);
                        }else{
                            echo '<h2 class="text-center p-5">Wprowadź potrzebne dane i zaloguj:</h2>';
                        }
                    ?>
                    
                </blockquote>
            </div>
            <div>
                <form method="post">
                    <div class="d-flex align-items-center flex-column  ">
                        <div class="input-group col-md-4 col-7 m-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                        <path
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                                    </svg></span>
                            </div>
                            <input type="email" class="form-control" placeholder="e-mail" name="email">
                            <?php
                                if(isset($_SESSION['errLogEmail'])){
                                    echo '<div class="d-flex justify-content-center text-danger">'.$_SESSION['errLogEmail'].'</div>';
                                    unset($_SESSION['errLogEmail']);
                                }
                            ?>
                        </div>
                        <div class="input-group col-md-4 col-7 m-1">
                            <div class=" input-group-prepend">
                                <span id="showPassword" class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-key"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                                        <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                    </svg></span>
                            </div>
                            <input id="password" type="password" class="form-control" placeholder="hasło" name="password">
                            <?php
                                if(isset($_SESSION['errLogPassword'])){
                                    echo '<div class="d-flex justify-content-center text-danger">'.$_SESSION['errLogPassword'].'</div>';
                                    unset($_SESSION['errLogPassword']);
                                }
                            ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="d-flex justify-content-center col-8 m-5">
                            <a class="btn btn-secondary col-md-2 m-2 p-1" href="index.php" role="button">Cofnij</a>
                            <input class="btn btn-success col-md-4 m-2 p-1" type="submit" value="Zaloguj">
                        </div>
                    </div>
                </form>
            </div>

        </article>
        <blockquote class="blockquote bg-primary text-white text-right p-2 mt-5">
            <p class="mb-0">
                Po więcej informacji zapraszam do korespondencji <svg xmlns="http://www.w3.org/2000/svg" width="25"
                    height="25" fill="currentColor" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                    <path
                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z" />
                </svg>
            </p>
            <footer class="blockquote-footer text-white">krystian.surowiec.programista@gmail.com</footer>
        </blockquote>


    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
        integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s"
        crossorigin="anonymous"></script>
    <script src="js/appShow.js"></script>
</body>

</html>