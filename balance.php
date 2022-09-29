<?php
    session_start();

    if(!isset($_SESSION['idUser'])){
        header('Location: index.php');
        exit();
    }

    // if(isset($_POST['currentMonth'])){
    //     $actualDate = date('Y-m-d');
    //     echo strtotime($actualDate);

    // }

    // if(isset($_POST['previousMonth'])){
    //     echo "wcisnieto previousMonth";
    // }

    // if(isset($_POST['currentYear'])){
    //     echo "wcisnieto  currenyear";
    // }
    $validation = true;

    if(isset($_POST['submit']) && isset($_POST['firstDate']) && isset($_POST['secondDate'])){
        
        $_SESSION['firstBalanceDate'] = $_POST['firstDate'];
        $_SESSION['secondBalanceDate'] = $_POST['secondDate'];

        $first_balance_date = new DateTime($_SESSION['firstBalanceDate']);
        $second_balance_date = new DateTime($_SESSION['secondBalanceDate']);

        if($second_balance_date < $first_balance_date){
            $validation = false;
            $_SESSION['errDateBalance'] = "Źle określono przediał czasu.";
        }

        if($validation == true)
        {
            require_once "database.php";
        
            //Przychody
            $queryIncomes = $connection -> prepare('SELECT name, SUM(amount) AS sumIncome FROM incomes_category_assigned_to_users, incomes WHERE date_of_income >= :firstDate AND date_of_income <= :secondDate AND incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id AND incomes.user_id = :idUser GROUP BY name ORDER BY sumIncome DESC');
            $queryIncomes -> bindValue(':firstDate', $_SESSION['firstBalanceDate'], PDO::PARAM_STR);
            $queryIncomes -> bindValue(':secondDate', $_SESSION['secondBalanceDate'], PDO::PARAM_STR);
            $queryIncomes -> bindValue(':idUser', $_SESSION['idUser'], PDO::PARAM_STR);
            $queryIncomes -> execute();
            $incomesBudget = $queryIncomes -> fetchAll();

            $_SESSION['incomeBalance'] = "incomes";

            //Wydatki 
            $queryExpense= $connection -> prepare('SELECT name, SUM(amount) AS sumExpense FROM expenses_category_assigned_to_users, expenses WHERE date_of_expense >= :firstDate AND date_of_expense <= :secondDate AND expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id AND expenses.user_id = :idUser GROUP BY name ORDER BY sumExpense DESC');
            $queryExpense -> bindValue(':firstDate', $_SESSION['firstBalanceDate'], PDO::PARAM_STR);
            $queryExpense -> bindValue(':secondDate', $_SESSION['secondBalanceDate'], PDO::PARAM_STR);
            $queryExpense -> bindValue(':idUser', $_SESSION['idUser'], PDO::PARAM_STR);
            $queryExpense -> execute();
            $expensesBudget = $queryExpense -> fetchAll();

            $_SESSION['expenseBalance'] = "expenses";
        }
        
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Balance Budget</title>
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
                    <h2 class="text-center p-5">Przeglądaj bilans z wybranego okresu:</h2>
                </blockquote>
                <div class="row">
                    <nav class="col-xs-12 col-sm-6 col-md-3 border-right pr-0">
                        <div class="nav flex-column">
                            <a class="btn btn-primary bt-sm m-3" href="menu.php" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-house-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                                    <path fill-rule="evenodd"
                                        d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
                                </svg> Strona Główna</a>
                            <a class="btn btn-primary bt-sm m-3" href="addincome.php" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-cash-coin" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" />
                                    <path
                                        d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z" />
                                    <path
                                        d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z" />
                                    <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z" />
                                </svg> Dodaj Przychód</a>
                            <a class="btn btn-primary bt-sm m-3" href="addexpense.php" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-cart4" viewBox="0 0 16 16">
                                    <path
                                        d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z" />
                                </svg> Dodaj Wydatek</a>
                            <a class="btn btn-primary bt-sm m-3 active" href="balance.php" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-list-check" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                                </svg> Przeglądaj Bilans</a>
                            <a class="btn btn-primary bt-sm m-3" href="#" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-wrench" viewBox="0 0 16 16">
                                    <path
                                        d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364L.102 2.223zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11l.471.242z" />
                                </svg> Ustawienia</a>
                            <a class="btn btn-primary bt-sm m-3" href="logout.php" role="button"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor"
                                    class="bi bi-door-open" viewBox="0 0 16 16">
                                    <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z" />
                                    <path
                                        d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117zM11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5zM4 1.934V15h6V1.077l-6 .857z" />
                                </svg>Wyloguj się</a>
                        </div>
                        <div>
                        <p id="logIn" class="text-success mt-5 p-3">Zalogowany: <?php if(isset($_SESSION['idUser'])){echo $_SESSION['userName'];} ?></p>
                        </div>
                    </nav>
                    <div class="col-xs-12 col-sm-6 col-md-9 pl-0">
                        <form method="post">
                            <div class="d-flex justify-content-around flex-sm-column flex-md-row bg-light py-2">
                                <!-- <div>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="submit" class="btn btn-secondary" name="currentMonth">Bieżący miesiąc</button>
                                        <button type="submit" class="btn btn-secondary" name="previousMonth">Poprzedni miesiąc</button>
                                        <button type="submit" class="btn btn-secondary" name="currentYear">Bieżący rok</button>
                                    </div>
                                </div> -->
                                <div>
                                    <div class="col-xs-12 col-sm-12 input-group m-1 pl-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">Od:</span>
                                        </div>
                                        <input id="d1" type="date" <?php if(isset($_SESSION['incomeBalance'])){echo 'value="'.$_SESSION['firstBalanceDate'].'" ';} ?> class="form-control" name="firstDate" min="2022-01-01">
                                    </div>
                                    <div class="col-xs-12 col-sm-12 input-group m-1 pl-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2">Do:</span>
                                        </div>
                                        <input id="d2" type="date" <?php if(isset($_SESSION['expenseBalance'])){echo 'value="'.$_SESSION['secondBalanceDate'].'" ';} ?> class="form-control" name="secondDate" min="2022-01-01">
                                    </div>
                                    <?php 
                                        if(isset($_SESSION['errDateBalance'])){
                                            echo '<div class="d-flex justify-content-center text-danger">'.$_SESSION['errDateBalance'].'</div>';
                                            unset($_SESSION['errDateBalance']);
                                        }
                                    ?>
                                    
                                </div>
                                <div><button type="submit" class="btn btn-success m-1" name="submit">Przeglądaj bilans</button></div>
                            </div>
                        </form>
                        <div class="row justify-content-center">
                            <div class="col-xs-12 col-sm-12 col-md-4 m-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Przychód:</th>
                                            <th scope="col">Kwota:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(isset($_SESSION['incomeBalance'])){
                                                foreach($incomesBudget as $incomes){
                                                    echo '<tr><th scope="row">'.$incomes[0].'</th><td>'.$incomes[1].'</td></tr>';
                                                    $_SESSION['sumIncomes'] += $incomes[1];
                                                }
                                                echo '<tr><th scope="row">Suma:</th><td>'.$_SESSION['sumIncomes'].'</td></tr>';
                                                unset($_SESSION['incomeBalance']);
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4 m-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">Wydatek:</th>
                                            <th scope="col">Kwota:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(isset($_SESSION['expenseBalance'])){
                                                foreach($expensesBudget as $expenses){
                                                    echo '<tr><th scope="row">'.$expenses[0].'</th><td>'.$expenses[1].'</td></tr>';
                                                    $_SESSION['sumExpenses'] += $expenses[1];
                                                }
                                                echo '<tr><th scope="row">Suma:</th><td>'.$_SESSION['sumExpenses'].'</td></tr>';
                                                unset($_SESSION['expenseBalance']);
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php
                                if(isset($_SESSION['sumIncomes']) && isset($_SESSION['sumExpenses'])){
                                    $balance = $_SESSION['sumIncomes'] - $_SESSION['sumExpenses'];
                                    if($_SESSION['sumIncomes'] <= $_SESSION['sumExpenses']){
                                        echo '<div class="alert alert-danger" role="alert">Uważaj, wpadasz w długi! Twój bilans wynosi: '.$balance.'</div>';
                                    }else{
                                        echo '<div class="alert alert-success" role="alert">Gratulacje! Twój bilans wynosi: '.$balance.'</div>';
                                    }
                                    unset($_SESSION['sumIncomes']);
                                    unset($_SESSION['sumExpenses']);
                                }
                            ?>
                    </div>
                </div>
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
</body>

</html>