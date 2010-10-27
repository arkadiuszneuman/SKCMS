<?php
    include('sql.php');
    $sql = new Sql("127.0.0.1", "root", "", "database");
    //$sql->AddNews("Test", "testestestes");
    $sql->ReadNews();
    $sql->Close();

?>
