<?php

    function showPaging($page, $howMany, $count)
    {
        if ($page > 0) //wyswietlenie poprzednia strona
        {
            echo "<a href=\"#\" onclick=\"sendGet('news', '0')\">Pierwsza</a>  ";
            echo "<a href=\"#\" onclick=\"sendGet('news', '".($page-1)."')\">Poprzednia strona</a>    ";
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
                    echo "<a href=\"#\" onclick=\"sendGet('news', '".($i-1)."')\">".$i."</a> ";
                else
                    echo $i." ";
            }
        }
//TODO cos zle wyswietla ostatnia strone (za duzo ich)
        if (($page+1)*$howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
        {
            echo "<a href=\"#\" onclick=\"sendGet('news', '".($page+1)."')\">Następna strona</a>   ";
            echo "<a href=\"#\" onclick=\"sendGet('news', '".(($count-1)/$howMany)."')\">Ostatnia</a>";
        }
        else
        {
            echo "Następna strona   ";
            echo "Ostatnia";
        }
    }

    include('..\sql.php');
    $sql = new Sql();
    $count = $sql->NumberOfNews();

    $howMany = 3; //ilosc newsow na strone
    @$page = $_GET['page'];

    $newses = $sql->ReadNews(false, $page*$howMany,  $howMany); //3 newsy na strone
    $sql->Close();

    
    foreach ($newses as $news)
    {
        echo "<h3>".$news['title']."</h3>\n<h6>";
        echo $news['date']."</h6>\n<hr /><p>";
        echo nl2br($news['note'])."</p>\n<br /><br />\n\n";
    }

    showPaging($page, $howMany, $count);

?>
