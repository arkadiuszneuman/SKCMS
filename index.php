<?php
	session_start();

?>

<script type="text/javascript" src="./javascript/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="./css/windowLogin.css" />

<?php
    function showPaging($page, $howMany, $count)
    {
        $link = $_GET['link'];
        if ($page > 0) //wyswietlenie poprzednia strona
        {
            ?><a href="./index.php?link=<?php echo $link ?>&page=0">Pierwsza</a>   <?php
            ?><a href="./index.php?link=<?php echo $link ?>&page=<?php echo ($page-1) ?>">Poprzednia strona</a>   <?php
        }
        else
        {
            echo "Pierwsza  ";
            echo "Poprzednia strona    ";
        }

        for ($i = $page-2; $i < $page+5; ++$i) //wyswietlenie numerow stron
        {
            if ($i > 0 && ($i-1)*$howMany < $count) //numery stron tylko w zakresie min max
            {
                if ($i != $page + 1) //link nie moze byc aktualna strona
                {
                    ?><a href="./index.php?link=<?php echo $link ?>&page=<?php echo ($i-1) ?>"><?php echo $i ?></a>   <?php
                }
                else
                {
                    echo $i." ";
                }
            }
        }

        if (($page+1)*$howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
        {
            ?><a href="./index.php?link=<?php echo $link ?>&page=<?php echo ($page+1) ?>">Następna strona</a>   <?php
            if ($count%$howMany != 0) //jesli ilosc newsow przez ilosc newsow na strone jest nierowna
            {
                ?><a href="./index.php?link=<?php echo $link ?>&page=<?php echo ((int)($count/$howMany)) ?>">Ostatnia</a>   <?php
            }
            else
            {
                ?><a href="./index.php?link=<?php echo $link ?>&page=<?php echo (($count/$howMany) - 1) ?>">Ostatnia</a>   <?php
            }
        }
        else
        {
            echo "Następna strona   ";
            echo "Ostatnia";
        }
    }

    include('structure/up.html');
    include('sql.php');

    ?><div id="all"><?php
    if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] == false)
    {
        ?>
<!--        <a href="./user.php?task=login">Panel administracyjny</a>-->
        <a href="#" onclick="Login('login');">Zaloguj</a>
        <?php
    }
    else
    {
        ?>
<!--        <a href="./user.php?task=login">Panel administracyjny</a>-->
        <a href="./panel/panel.php">Panel administracyjny</a> &nbsp; &nbsp;
        <a href="#" onclick="Login('logoff')">Wyloguj</a>
        <?php
        //echo '<a href="./panel/panel.php">Panel administracyjny</a> &nbsp; &nbsp;';
        //echo '<a href="./user.php?task=logoff">Wyloguj</a>';
    }
    
    //okienko z logowaniem
    ?>
        <div id="windowLogin"></div>
        <br />
    <?php

    //wyswietlenie linkow
    $sql = new Sql();
    $links = $sql->ReadLinks();

    foreach ($links as $link)
    {
        $txt = $link['link'];
        $txt =  str_replace(' ','_',$txt);
        ?>
            <a href="./index.php?link=<?php echo $txt ?>" class="link"><?php echo $link['link'] ?></a>
        <?php
    }

    @$page = $_GET['page'];
    $howMany = 3;
   

    foreach ($links as $link)
    {
        if (@$_GET['link'] == null)
            $_GET['link'] = str_replace(' ','_',$link['link']);

        if (str_replace(' ','_',$link['link']) == $_GET['link'])
        {
            $news = $sql->ReadNews(false, $page*$howMany, $howMany, $link['id']);

            if ($news != null)
            {
                foreach($news as $n)
                {
                    echo "<h3>".$n['title']."</h3>\n<h6>";
                    echo $n['date']."</h6>\n<hr /><p>";
                    echo nl2br($n['note'])."</p>\n<br /><br />\n\n";
                }

                $count = $sql->NumberOfNews($link['id']);
                if ($count > $howMany) //wyswietlenie pagingu tylko w przypadku wiekszej ilosci newsow niz strona
                    showPaging($page, $howMany, $count);
            }
            else
                echo "Brak arytkułów w podanym linku";

            break;
        }
    }

    ?>
<!--    <div id="news">Ładowanie</div></div>--> </div>
    <?php

    $sql->Close();
    include('structure/down.html');
?>
