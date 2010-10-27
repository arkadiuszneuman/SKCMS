<?php

    include('sql.php');
    $sql = new Sql("127.0.0.1", "root", "", "database");
    //$sql->AddNews("Test", "testestestes\n\n asdasd\n asdasdasdawdaa");
    $newses = $sql->ReadNews();

    foreach ($newses as $news)
    {
        echo "<h3>".$news['title']."</h3><h6>";
        echo $news['date']."</h6><hr /><p>";
        echo nl2br($news['note'])."</p><br /><br />";
    }

    $sql->Close();

?>
