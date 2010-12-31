<?php
session_start();

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
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
    include('../structure/up.html');
    ?>Nie masz dostępu do tej strony<br /><br /><a href="../index.php">Powrót do strony głownej</a><?php
    include('../structure/down.html');
}

?>
