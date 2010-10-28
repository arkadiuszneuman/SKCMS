<?php

session_start();
if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
{
    $_SESSION['zalogowany'] = false;
    $page = "javascript: history.go(-1)";
    header("Refresh: 3; url=$page");

    echo "Wylogowano<br>Za 3 sekundy zostaniesz przeniesiony do poprzedniej lokalizacji...";
}

?>
