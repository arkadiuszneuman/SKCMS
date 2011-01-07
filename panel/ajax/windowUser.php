<?php

include_once("\..\..\CForm.php");
include_once("\..\..\sql.php");

@$login = $_GET['login'];
@$id = $_GET['id'];

?>Uprawnienia użytkownika <b><?php echo $login ?></b>:<br /><?php
$form = new CForm(CForm::POST, "?task=users&id=$id&login=$login");
$privileges = Privileges::WhatPrivileges($_GET['privileges']); //uprawnienia uzytkownika
foreach (Privileges::WhatPrivileges(Privileges::ALL) as $p) //przelecenie przez wszystkie uprawnienia i zaznaczenie tylko tych, ktore posiada uzytkownik
{
    $isNoPriv = false; //jesli ma byc wszystko disabled oprocz uzytkownika
    if ($_GET['privileges'] == 0) //sprawdzenie czy to w ogole ma jakiekolwiek uprawnienia
    {
        if ($p == Privileges::USER)
            $form->AddItem(new CCheckBox(Privileges::PrivilegeToString($p), "priv[]", false, false, $p, "user"));
        else
            $form->AddItem(new CCheckBox(Privileges::PrivilegeToString($p), "priv[]", true, true, $p));
    }
    else
    {
        if (in_array($p, $privileges)) //sprawdzenie jakie uprawnienia ma uzytkownik
        {
            if ($p == Privileges::USER)
                $form->AddItem(new CCheckBox(Privileges::PrivilegeToString($p), "priv[]", true, false, $p, "user")); //+ustawienie pierwszego checkboksa jako id=user, dla javascriptu potrzebne
            else
                $form->AddItem(new CCheckBox(Privileges::PrivilegeToString($p), "priv[]", true, false, $p));  //i checkniecie ich
        }
        else
            $form->AddItem(new CCheckBox(Privileges::PrivilegeToString($p), "priv[]", false, false, $p));
    }
}
//"Save('$id', '$login'); return false;"
$form->AddItem(new CButton("Zapisz zmiany", "save"));
$form->AddItem(new CButton("Anuluj", "save", null, "Close(); return false;"));
$form->Draw();

?>
