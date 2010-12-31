<?php
    session_start();
    include("CForm.php");
    @$task = $_GET['task'];

    switch($task)
    {
        case "login":
            $form = new CForm(CForm::POST, "./panel/panel.php");
            $form->AddItem(new CTextBox("Nazwa użytkownika: ", "login"));
            $form->AddItem(new CPassword("Hasło: ", "pass"));
            $form->AddItem(new CButton("Wyślij", "log", null, "Login('check'); return false;"));
            $form->Draw();
            ?>
            <br />
            <a href="#" onclick="Login('registration');">Rejestruj</a>
            <?php
            break;

        case "check":
            include('sql.php');

            $login = $_GET['login'];
            $pass = $_GET['pass'];

            $sql = new Sql();

            if ($sql->CheckAdmin($login, md5($pass))) //sprawdzenie loginu i przeniesienie do panelu w razie powodzenia
            {
                echo "Zalogowano";
                $_SESSION['zalogowany'] = true;
                
                //echo '<script type="text/javascript">RefreshSite();</script>';
            }
            else
            {
                echo "Błędny login lub hasło";
            }

            $sql->Close();
            break;

        case "logoff":
            if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
            {
                $_SESSION['zalogowany'] = false;
                echo "Wylogowano";
                //echo <br>Za 3 sekundy zostaniesz przeniesiony do poprzedniej lokalizacji...";
            }
            break;

        case "registration":
        default: //domyslnie jest rejestracja
            ?>
            <script type="text/javascript" src="./javascript/registerfrm.js"></script>
            <?php

            $form = new CForm(CForm::POST, "panel.php");
            $form->SetId("regForm");
            $form->AddItem(new CTextBox("Nazwa użytkownika: ", "login"));
            $form->AddItem(new CPassword("Hasło: ", "pass", "pass1"));
            $form->AddItem(new CPassword("Powtórz hasło: ", "pass", "pass2"));
            $form->AddItem(new CTextBox("Nazwa wyświetlana: ", "name"));
            $form->AddItem(new CTextBox("Adres e-mail: ", "mail"));
            $form->AddItem(new CButton("Wyślij", "send", null, "Login('register'); return false;"));
            $form->Draw();

            break;

        case "register":
            include('sql.php');

            $login = $_GET['login'];
            $pass = $_GET['pass'];
            $name = $_GET['name'];
            $mail = $_GET['mail'];

            $sql = new Sql();

            if ($sql->AddAdmin($login, md5($pass), $name, $mail)) //rejestracja i przeniesienie do panelu
            {
                ?>Dodano admina<?php
                $_SESSION['zalogowany'] = true;
            }

            break;
    }

    //zamkniecie okienka z logowaniem
    ?>
    <br /><br /><a href="#" onclick="RefreshSite(); Close();">Zamknij</a>
    <?php
?>
