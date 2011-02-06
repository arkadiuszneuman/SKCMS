<?php
if (session_id() == null)
{
	session_start();
}

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
{
		$content="<form method=\"POST\" action=\"user.php?task=check\">
					<label for=\"login\">Login: </label>
					<input type=\"text\" name=\"login\"><br />
					<label for=\"pass\">Hasło: </label>
					<input type=\"password\" name=\"pass\"><br />
					<input type=\"submit\" name=\"log\" value=\"Wyślij\" onclick=\"Login('check'); return false\">
					</form>";

	if (@$_GET['module'] == "login")
	{
		switch(@$_GET['action'])
		{
			case "login":
				$data = array("title"=>"Użytkownik", "content"=>"<a href=\"#\" onclick=\"Login('login');\">ZalogujLOGIN</a>", "item_id"=>"login");
				break;
			case "register":
				$data = array("title"=>"Użytkownik", "content"=>"<a href=\"#\" onclick=\"Login('login');\">ZalogujREG</a>", "item_id"=>"login");
				break;
		}
	}
}
else
{
	$content="<a href=\"./panel/\">Panel administracyjny</a><br /><a href=\"#\" onclick=\"Login('logoff')\">Wyloguj</a>";
}   				

if (@$_GET['from'] == 'outer')
{
	echo "<h3>Użytkownik</h3>";
	echo $content;
}
else
	return $content;
?>
