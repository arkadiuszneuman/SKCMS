<?php
    include('sql.php');
    $sql = new Sql("127.0.0.1", "root", "", "database");
    $count = $sql->NumberOfNewses();

    $howMany = 3; //ilosc newsow na strone
    @$page = $_GET['page'];

    if ($page*$howMany >= $count) //zabezpieczenie przed zbyt duza wartoscia, ale i tak chujnia, bo skrypt ajax.js zwieksza dalej zmienna! :/
    {
        $page = $count/$howMany - 1;
    }

    $newses = $sql->ReadNews(false, $page*$howMany,  $howMany); //3 newsy na strone
    $sql->Close();

    
    foreach ($newses as $news)
    {
        echo "<h3>".$news['title']."</h3>\n<h6>";
        echo $news['date']."</h6>\n<hr /><p>";
        echo nl2br($news['note'])."</p>\n<br /><br />\n\n";
    }
    
?>
