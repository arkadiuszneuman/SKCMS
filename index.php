<?php
    include('structure/up.html');

    session_start();
    if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] == false)
        echo '<a href="./user.php?task=login">Panel administracyjny</a>';
    else
    {
        echo '<a href="./panel.php">Panel administracyjny</a> &nbsp; &nbsp;';
        echo '<a href="./user.php?task=logoff">Wyloguj</a>';
    }

    include('sql.php');

    $sql = new Sql("127.0.0.1", "root", "", "database");
    //$sql->AddNews("Test", "testestestes\n\n asdasd\n asdasdasdawdaa");
    $newses = $sql->ReadNews();

    foreach ($newses as $news)
    {
        echo "<h3>".$news['title']."</h3>\n<h6>";
        echo $news['date']."</h6>\n<hr /><p>";
        echo nl2br($news['note'])."</p>\n<br /><br />\n\n";
    }

    $sql->Close();

    include('structure/down.html');
?>
