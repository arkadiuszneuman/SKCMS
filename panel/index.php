<?php
session_start();
include('../sql.php');

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] != Privileges::USER)
{
    include('./CPanel.php');
    $panel = new CPanel();

    @$task = $_GET['task'];
    if (empty($task)) //domyslnie ma byc edycja artykulow
        $task = "editArticles";

    switch ($task)
    {
        case "addNote":
            $panel->AddNote();
            break;

        case "editArticles":
            $panel->EditArticles();
            break;

        case "bin":
            $panel->Bin();
            break;

        case "editLinks":
            $panel->EditLinks();
            break;
        case "preferences":
            $panel->Preferences();
            break;
    }

    ?><br /><br /><a href="../index.php">Powrót do strony głownej</a><?php
}
else
{
    ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
          <head>
            <title>SKCMS - Panel Administracyjny</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
          </head>
          <body>
        <?php
    ?>Nie masz dostępu do tej strony<br /><br /><a href="../index.php">Powrót do strony głownej</a><?php
    ?>
          </body>
        </html>
   <?php

}

?>
