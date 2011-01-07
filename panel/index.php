<?php
session_start();
include('../sql.php');
$sql = new Sql();

if (isset($_SESSION['name']))
    $priv = $sql->CheckPrivileges($_SESSION['name']);
else
    $priv = 0;

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $priv >= Privileges::ARTICLES) //wszystkie uprawnienia od mozliwosci dodawania artykulow maja dostep do panelu
{
    include('./Includes.php');

    @$task = $_GET['task'];
    if (empty($task)) //domyslnie ma byc edycja artykulow
        $task = "articles";

    switch ($task)
    {
        case "addNote":
            $c = new CAddNote();
            $c->AddNote();
            break;

        case "articles":
            $c = new CArticles();
            $c->Articles();
            break;

        case "bin":
            $c = new CBin();
            $c->Bin();
            break;

        case "links":
            $c = new CLinks();
            $c->Links();
            break;

        case "users":
            $c = new CUsers();
            $c->Users();
            break;

        case "preferences":
            $c = new CPreferences();
            $c->Preferences();
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
