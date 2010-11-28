<script type="text/javascript" src="./javascript/panel.js"></script>
<link rel="stylesheet" type="text/css" href="./css/panel.css" />

<?php

    include('structure/up.html');
    include('sql.php');

    function CreateTable($newses)
    {
        echo "<form name=\"removeFrm\" method=\"POST\" action=\"panel.php?task=removeNote\">";
        echo "<br /> <table id=\"tabPanel\">";
        $parzysty = true;
        $i = 1;
        foreach ($newses as $news)
        {
            if ($parzysty)
            {
                echo "<tr class=\"even\">";
            }
            else
            {
                echo "<tr class=\"odd\">";
            }

            echo "<td>".$i."</td>";
            echo "<td><input type=\"checkbox\" name=\"check[]\" value=\"".$news['id']."\" /></td>";
            echo "<td class=\"topic\"><a href=\"./panel.php?task=editNote&id=".$news['id']."\" title=\"".$news['note']."\">".$news['title']."</a></td>\n";
            echo "</tr>";

            ++$i;
            $parzysty = !$parzysty;
        }
        echo "</table>";

        echo "</form>";
    }

    

    $sql = new Sql();

    session_start();

    function SendInfo($info)
    {
        $_SESSION['info'] = $info;
    }

    function DrawInfo()
    {
        //wlaczenie zielonej info u gory
        if (isset($_SESSION['info']) && !empty($_SESSION['info']))
        {
            ?><div id="info"><?php echo $_SESSION['info'] ?></div><?php
        }
    }

    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
    {
        ?>

        <a href="./panel.php?task=addNote">Dodaj notkę</a>&nbsp;&nbsp;
        <a href="./panel.php?task=editNote">Edytuj notkę</a>&nbsp;&nbsp;
        <a href="http://Kosz" id="binBtn">Do kosza</a><br /> <!-- javascript lapie remove i anuluje link oraz wysyla formularz -->
        
        <?php 
            
        $_SESSION['info'] = "";

        @$task = $_GET['task'];
        switch ($task)
        {
            case "addNote":
                if(isset($_POST['newnote']))
                {
                    $title = $_POST['title'];
                    $note = $_POST['note'];

                    $title = trim($title);
                    $note = trim($note);

                    if (empty($title) || empty($note))
                    {
                        echo "Notka nie dodana z powodu braku tytułu lub treści";
                    }
                    else
                    {
                        if ($sql->AddNews($title, $note))
                            SendInfo("News został wysłany");
                        else
                            SendInfo("News nie został wysłany");

                        DrawInfo();
                    }

                    echo "<br />";
                }
                ?>
                <form method="POST" action="panel.php?task=addNote">
                    <b>Tytuł:</b> <input type="text" size="65" name="title" /><br />
                    <b>Treść:</b> <textarea name="note" rows="10" cols="50"></textarea><br />
                    <input type="submit" value="Wyślij" name="newnote" />
                </form>
                <?php
                break;

            case "removeNote": //usuwanie notki
                @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
                if ($sql->RemoveNewsToBin($checkboxes)) //
                    SendInfo("News/Newsy zostały usunięte");
                else
                    SendInfo("Nie można usunąć newsów");

                //nie dajemy break'a, zeby przeszedl do edit note i wyswietlil tabelke

            case "editNote": //troche syf jest tutaj
                if(isset($_POST['edit'])) //po kliknieciu wyslij przy edycji notki
                {
                    $title = $_POST['title'];
                    $note = $_POST['note'];
                    @$id = $_GET['id'];

                    $title = trim($title);
                    $note = trim($note);

                    if (empty($title) || empty($note))
                    {
                        SendInfo("Notka nie dodana z powodu braku tytułu lub treści");
                    }
                    else
                    {
                        if ($sql->EditNews($id, $title, $note))
                            SendInfo("News został zaktualizowany");
                        else
                            SendInfo("News nie został zaktualizowany");

                        $id = 0; //zeby przeszedl do malowania tabelki
                    }

                    echo "<br />";
                }
                else
                {
                    @$id = $_GET['id'];
                }
                
                if ($id != 0) //jesli wybrana zostala jakas notka to dawaj formularz, a jesli nie...
                {
                    $news = $sql->ReadSelectedNews($id);

                    echo "<form method=\"POST\" action=\"panel.php?task=editNote&id=".$id."\">";
                        echo "<b>Tytuł:</b> <input type=\"text\" size=\"65\" name=\"title\" value=\"".$news['title']."\" /><br />";
                        echo "<b>Treść:</b> <textarea name=\"note\" rows=\"10\" cols=\"50\">".$news['note']."</textarea><br />";
                        echo "<input type=\"submit\" value=\"Wyślij\" name=\"edit\" />";
                    echo "</form>";
                }
                else //... to wyswietli sie lista notek do wybrania
                {
                    DrawInfo();
                    $news = $sql->ReadNews(true, 0, 100);
                    CreateTable($news);
                }
                break;
        }

        echo '<br /><br /><a href="./index.php">Powrót do strony głownej</a>';
    }
    else //jesli nie zalogowany
    {
        if(isset($_POST['send'])) //sprawdzenie formularza rejestracji
        {
            $login = $_POST['login'];
            $pass = $_POST['pass1'];
            $name = $_POST['name'];
            $mail = $_POST['mail'];

            if ($sql->AddAdmin($login, md5($pass), $name, $mail)) //rejestracja i przeniesienie do panelu
            {
                echo "Dodano admina<br />Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
                $_SESSION['zalogowany'] = true;
            }
        }
        else if (isset($_POST['log'])) //sprawdzenie formularza logowania
        {
            $login = $_POST['login'];
            $pass = $_POST['pass'];

            if ($sql->CheckAdmin($login, md5($pass))) //sprawdzenie loginu i przeniesienie do panelu w razie powodzenia
            {
                echo "Zalogowano<br />Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $_SESSION['zalogowany'] = true;
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
            }
            else
            {
                echo "Błędny login lub hasło\n";
            }
        }
        else //jesli chce sie dostac do panelu to najpierw trzeba sie zalogowac
        {
            echo "Musisz się zalogować<br />Zostaniesz przeniesiony do strony logowania za 3 sekundy...";
            $_SESSION['zalogowany'] = true;
            $page = "./user.php?task=login";
            header("Refresh: 3; url=$page");
        }
    }

    $sql->Close();

    include('structure/down.html');
?>
