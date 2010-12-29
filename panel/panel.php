<?php

include('./CPanel.php');

$panel = new CPanel();

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
{
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
    }

    ?><br /><br /><a href="../index.php">Powrót do strony głownej</a><?php
}

?>
