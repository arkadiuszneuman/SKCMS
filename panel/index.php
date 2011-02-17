<?php
session_start();
include('../includes/sql.php');
$sql = new Sql();

if(@$_GET['action'] == 'admin')
{
	if($sql->CheckUser($_POST['login'], md5($_POST['pass'])) && ($sql->CheckPrivileges($_POST['login']) >= Privileges::ARTICLES))
	{
		$_SESSION['loggedIn'] = true;
		$_SESSION['name'] = $_POST['login'];
	}
}

if (isset($_SESSION['name']))
    $priv = $sql->CheckPrivileges($_SESSION['name']);
else
    $priv = 0;

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $priv >= Privileges::ARTICLES) //wszystkie uprawnienia od mozliwosci dodawania artykulow maja dostep do panelu
{  
	//dynamiczne dodanie wszystkich klas w znajdujacych sie w folderze includes
	$dir = opendir('./includes/');
	while(false !== ($file = readdir($dir)))
	{
	  	if($file != '.' && $file != '..')
	  	{
	  		include_once('./includes/'.$file);
	  	}
	}

    @$task = $_GET['task'];
    if (empty($task)) //domyslnie ma byc edycja artykulow
        $task = "articles";

    switch ($task)
    {
        case "addNote":
            $c = new CAddArticle();
            $c->AddArticle();
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
            
        case "blocks":
            $c = new CBlocks();
            $c->Blocks();
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
else if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $priv <= Privileges::ARTICLES) //wszystkie uprawnienia od mozliwosci dodawania artykulow maja dostep do panelu
{
    ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
          <head>
            <title>SKCMS - Panel Administracyjny</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
          </head>
          <body>
		  	<p>Nie masz dostępu do tej strony. <a href="../index.php">Powrót do strony głównej</a></p>
          </body>
        </html>
		<?php
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
		 	<form method="POST" action="index.php?action=admin">
			<label for="login">Login: </label>
			<input type="text" name="login"><br />
			<label for="pass">Hasło: </label>
			<input type="password" name="pass"><br />
			<input type="submit" name="log" value="Wyślij">
			</form>
          </body>
        </html>
   <?php

}

?>
