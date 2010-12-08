<?php
session_start();
?>

<script type="text/javascript" src="./javascript/panel.js"></script>
<link rel="stylesheet" type="text/css" href="./css/panel.css" />

<?php

    include('structure/up.html');
    include('sql.php');

    function CreateTable($newses)
    {
        if ($newses == null)
            return;

        //akcja formy ustawiana w javascripcie
        ?>
            <form name="binFrm" method="POST" action="">
                <table id="tabPanel">
                    <tr id="upper">
                        <td>Lp</td>
                        <td>Zazn.</td>
                        <td class="topic">Temat</td>
                    </tr>
        <?php
        $parzysty = true;
        $i = 1;
        foreach ($newses as $news)
        {
            if ($parzysty)
            {
                ?><tr class="even"><?php
            }
            else
            {
                ?><tr class="odd"><?php
            }

            ?><td><?php echo $i ?></td>
            <td><input type="checkbox" name="check[]" value="<?php echo $news['id'] ?>" /></td>
            <td class="topic">
                <a href="./panel.php?task=editNote&id=<?php echo $news['id'] ?>"  title="<?php echo $news['note'] ?>"><?php echo $news['title'] ?></a></td>
            </tr><?php

            ++$i;
            $parzysty = !$parzysty;
        }
        ?></table><?php

        ?></form><?php
    }

    

    $sql = new Sql();

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

    function DrawHeader()
    {
        ?>

            <div id="header">
                PANEL ADMINISTRACYJNY
            </div>

        <?php
    }

    function DrawMenu()
    {
        ?>
            <div id="menu">
                <a href="./panel.php?task=addNote">Dodaj notkę</a><br />
                <a href="./panel.php?task=editNote">Edytuj notkę</a><br />
        <?php
            @$task = $_GET['task'];
            if ($task === "editNote" || empty($task) || $task === "moveToBin") //sprawdzenie czy jest w edycji notki (lub czy nie zostalo wykonane move to bin) i jesli wyswietla sie tabelka to dodanie przycisku do kosza
            {
                @$id = $_GET['id'];
                if ($id == 0)
                {
                    ?><a href="http://Kosz" id="toBinBtn" class="submenu">Do kosza</a><br /> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                }
            }
        ?>
                <a href="./panel.php?task=showBin">Kosz</a><br />
                <?php
                    if ($task === "showBin" || $task === "binToNews" || $task === "binRemove")
                    {
                        ?><a href="http://Przywroc" id="binToNews" class="submenu">Przywróć</a><br /> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                        ?><a href="http://Usun" id="binRemove" class="submenu">Usuń bezpowrotnie</a><br /> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                    }
                ?>
            </div>
        <?php
    }

    function addNote($sql)
    {
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
    }

    function moveToBin($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RemoveNewsToBin($checkboxes)) //
            SendInfo("News/Newsy zostały usunięte");
        else
            SendInfo("Nie można usunąć newsów");
    }

    function editNote($sql)
    {
        if(isset($_POST['edit'])) //po kliknieciu wyslij przy edycji notki
        {
            $title = $_POST['title'];
            $note = $_POST['note'];
            @$id = $_GET['id'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                SendInfo("News nie zaktualizowany z powodu braku tytułu lub treści");
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

            ?>
            <form method="POST" action="panel.php?task=editNote&id=<?php echo $id ?>">
                    <b>Tytuł:</b> <input type="text" size="65" name="title" value=" <?php echo $news['title'] ?>" /><br />
                    <b>Treść:</b> <textarea name="note" rows="10" cols="50">"<?php echo $news['note']?> </textarea><br />
                    <input type="submit" value="Wyślij" name="edit" />
            </form>

            <?php
        }
        else //... to wyswietli sie lista notek do wybrania
        {
            DrawInfo();
            $news = $sql->ReadNews(true, 0, 100);
            CreateTable($news);
        }
    }

    function binToNews($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RecoverNewsFromBin($checkboxes)) 
            SendInfo("News/Newsy zostały przywrócone");
        else
            SendInfo("Nie można przywrócić newsów");
    }

    function binRemove($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RemoveNews($checkboxes))
            SendInfo("News/Newsy zostały usunięte");
        else
            SendInfo("Nie można usunąć newsów");
    }

    function showBin($sql)
    {
            DrawInfo();
            $news = $sql->ReadNewsFromBin(true, 0, 100);
            CreateTable($news);
    }

    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
    {
        DrawHeader();
        DrawMenu();
        ?>
            <div id="srodek">
        <?php
        $_SESSION['info'] = "";

        @$task = $_GET['task'];
        if (empty($task)) //domyslnie ma byc edycja notki
            $task = "editNote";
        switch ($task)
        {
            case "addNote":
                addNote($sql);
                break;

            case "moveToBin": //usuwanie notki
                moveToBin($sql);
                editNote($sql); //po wywaleniu newsa do kosza wyswietlamy tez tabelke z newsami

            case "editNote":
                editNote($sql);
                break;

            case "binToNews":
                binToNews($sql);
                showBin($sql);
                break;

            case "binRemove":
                binRemove($sql);
                showBin($sql);
                break;

            case "showBin":
                showBin($sql);
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
    
    //zamkniecie diva srodek
    ?>
            </div> 
    <?php

    include('structure/down.html');
?>
