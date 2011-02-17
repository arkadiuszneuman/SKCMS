<?php

include_once("CPanel.php");

class CPreferences extends CPanel
{
	private $settings;

    public function __construct()
    {
        parent::__construct();
    }

    public function Preferences()
    {
		$this->settings = $this->sql->GetSettings();
        //TODO SPRAWDZAC PRZY KAZDYM WEJSCIU UPRAWNIENIA
        if (isset($_POST['save']))
        {
			foreach ($this->settings as $setting)
			{
				if (isset($_POST[$setting['name']]))
					$values[$setting['name']] = $_POST[$setting['name']];
				else
					$values[$setting['name']] = 0; //jesli wartosc nie istnieje to jest to checkbox i nie jest on zaznaczony
			}

            if ($this->sql->SaveSettings($values))
			{
                $this->SendInfo("Zapisano zmiany");
			}
            else
			{
                $this->SendInfo("Nie zapisano zmian");
			}
        }

        $this->DrawInfo();
		$this->settings = $this->sql->GetSettings();
		$this->GenerateForm();
    }

	private function GenerateForm()
	{
		?><div id="preferences"><?php
		$form = new CForm(CForm::POST, "?task=preferences");
		foreach ($this->settings as $setting)
		{
			$action = explode("\n", $setting['options']);
			echo $action[0].' ';
			switch($action[0])
			{
				case 'text':
					$item = new CTextBox($setting['title']."<br />".$setting['description']."<br />",
						$setting['name']);
					$item->SetValue($setting['value']);
					break;
				case 'check':
					$form->AddItem("<b>".$setting['title']."<br />".$setting['description']."</b><br />");
					$item = new CCheckBox(null, $setting['name'], $setting['value'], false, "1"); //tytul jest dodawany linijke wyzej
					break;
			}
			
			$form->AddItem($item);
		}
		$form->AddItem(new CButton("Zapisz zmiany", "save"));
		$form->Draw();
		?></div><?php
	}
}

?>
