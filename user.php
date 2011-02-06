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

            $login = $_POST['login'];
            $pass = $_POST['pass'];

            $sql = new Sql();

            if ($sql->CheckUser($login, md5($pass))) //sprawdzenie loginu i przeniesienie do panelu w razie powodzenia
            {
                echo "Zalogowano";
                $_SESSION['loggedIn'] = true;
                $_SESSION['name'] = $login;
                
                //echo '<script type="text/javascript">RefreshSite();</script>';
            }
            else
            {
                echo "Błędny login lub hasło";
            }

            $sql->Close();
            break;

        case "logoff":
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
            {
                $_SESSION['loggedIn'] = false;
                $_SESSION['name'] = "";
                echo "Wylogowano";
                //echo <br>Za 3 sekundy zostaniesz przeniesiony do poprzedniej lokalizacji...";
            }
            break;

        case "registration":
        default: //domyslnie jest rejestracja
            /*?>
            <script type="text/javascript" src="./javascript/registerfrm.js"></script>
            <?php*/

            $form = new CForm(CForm::POST);
            $form->SetId("regForm");
            $form->AddItem(new CTextBox("Nazwa użytkownika: ", "login", "login1", "CheckUser();"));
            $form->AddItem("<span id=\"txtUser\"><img alt=\"x\" src=\"javascript\\x.png\"/></span>");
            $form->AddItem("<br />");
            $form->AddItem(new CPassword("Hasło: ", "pass", "pass1", "CheckPass();"));
            $form->AddItem("<br />");
            $form->AddItem(new CPassword("Powtórz hasło: ", "pass", "pass2", "CheckPass();"));
            $form->AddItem("<span id=\"txtPass\">Hasła nie są takie same</span>");
            $form->AddItem("<br />");
            $form->AddItem(new CTextBox("Adres e-mail: ", "mail", "mail1", "EmailValidate()"));
            $form->AddItem("<span id=\"txtMail\">Adres e-mail jest nieprawidłowy</span>");
            $form->AddItem("<br />");
            $form->AddItem(new CButton("Wyślij", "send", null, "if (CheckForm()) { Login('registation'); } return false;"));

            $form->SetBrs(false);
            $form->Draw();

            break;

        case "registation":
            include('sql.php');

            $login = $_GET['login'];
            $pass = $_GET['pass'];
            $mail = $_GET['mail'];

            $sql = new Sql();

            if ($sql->AddUser($login, md5($pass), $mail)) //rejestracja i przeniesienie do panelu
            {
                ?>Zarejestrowano!<?php
                $_SESSION['loggedIn'] = true;
                $_SESSION['name'] = $login;
            }

            break;
    }

    //zamkniecie okienka z logowaniem
    ?>
    <br /><br /><a href="#" onclick="RefreshSite(); Close();">Zamknij</a>
    <?php
?>
