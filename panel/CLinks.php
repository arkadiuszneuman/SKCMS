<?php

class CLinks extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Links()
    {
		if (Privileges::CheckPrivilege(Privileges::MENU, $this->privileges))
		{
			@$id = $_GET['id'];
			if ($id == null)
				$id = 0;

			if (isset($_POST['remove']))
			{
				@$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
				if (count($checkboxes) > 0)
				{
					if ($this->sql->RemoveLink($checkboxes))
						$this->SendInfo("Link/linki zostały usunięte");
					else
						$this->SendInfo("Nie można usunąć linku/linków");
				}
				else
					$this->SendInfo("Nie zaznaczono żadnego linku");
			}
			else if(isset($_POST['newlink'])) //dodanie nowego linku
			{
				$link = $_POST['link'];
				$link = trim($link);
				$type = $_POST['type'];
				$value = $_POST['value'];

				if (empty($link))
				{
					$this->SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
				}
				else
				{
					if ($this->sql->AddLink($link, $type, $value))
						$this->SendInfo("Link został dodany");
					else
						$this->SendInfo("Link nie został dodany");
				}

				?><br /><?php
			}
			else if (isset($_POST['editlink'])) //lub edycja linku
			{
				$link = $_POST['link'];
				$link = trim($link);
				$type = $_POST['type'];
				$value = $_POST['value'];

				if (empty($link))
				{
					$this->SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
				}
				else
				{
					if ($this->sql->EditLink($id, $link, $type, $value))
						$this->SendInfo("Link został zmieniony");
					else
						$this->SendInfo("Link nie został zmieniony");
				}

				?><br /><?php
			}
			else if (isset($_GET['do']) && $_GET['do'] == "saveOrder")
			{
				if ($this->sql->SaveOrder())
					$this->SendInfo("Kolejność linków została zmieniona");
				else
					$this->SendInfo("Kolejność linków nie została zmieniona (czy zostały wprowadzone jakiekolwiek zmiany?)");
			}

			$this->DrawInfo();

			$przenies = "?task=links";
			if ($id != 0)
				$przenies = $przenies."&id=$id";
				/*
			$form = new CForm(CForm::POST, $przenies);
			$text = new CTextBox("Dodaj nowy link: ", "link");
			if ($id != 0)
			{
				$link = $this->sql->ReadLinks((int)$id);
				$text->SetValue($link[0]['link']);
			}
			$text->SetAddionalAttribs('size="65"');
			$form->AddItem($text);
			$text = new 
			$form->AddItem($text);
			$form->AddItem(new CButton((($id != 0) ? "Edytuj" : "Wyślij"), (($id != 0) ? "editlink" : "newlink")));
			$form->Draw();
*/
			echo "<form method=\"POST\" action=".$przenies.">";
			echo "<fieldset>";
			echo "<label for=\"link\">Dodaj nowy link: </label><br />";
			if ($id !=0)
			{
				$link = $this->sql->ReadLinks((int)$id);
				echo "<input type=\"text\" name=\"link\" value=\"".$link[0]['link']."\" size=\"65\" /><br />";

				if ($link[0]['type'] == 0)
				{
					echo "<input type=\"radio\" name=\"type\" id=\"cat\" value=\"0\" checked>";
					echo "<label for=\"cat\">Kategoria</label>";
					echo "<input type=\"radio\" name=\"type\" id=\"out\" value=\"1\">";
					echo "<label for=\"out\">Link zewnętrzny</label><br />";
				}
				else
				{
					echo "<input type=\"radio\" name=\"type\" id=\"cat\" value=\"0\">";
					echo "<label for=\"cat\">Kategoria</label>";
					echo "<input type=\"radio\" name=\"type\" id=\"out\" value=\"1\" checked>";
					echo "<label for=\"out\">Link zewnętrzny</label><br />";
				}

				echo "<label for=\"value\">Adres: </label><br />";
				echo "<input type=\"text\" name=\"value\" value=\"".$link[0]['value']."\" size=\"100\"><br />";
				echo "<input type=\"submit\" name=\"editlink\" value=\"Edytuj\" />";
			}
			else
			{
				echo "<input type=\"text\" name=\"link\" size=\"65\" /><br />";
				echo "<input type=\"radio\" name=\"type\" id=\"cat\" value=\"0\" checked>";
				echo "<label for=\"cat\">Kategoria</label>";
				echo "<input type=\"radio\" name=\"type\" id=\"out\" value=\"1\">";
				echo "<label for=\"out\">Link zewnętrzny</label><br />";
				echo "<label for=\"value\">Adres: </label><br />";
				echo "<input type=\"text\" name=\"value\" value=\"\" size=\"100\"><br />";
				echo "<input type=\"submit\" name=\"newlink\" value=\"Wyślij\" />";
			}
			echo "</fieldset></form>";
			$links = $this->sql->ReadLinks();
			$this->DrawTable($links, CPanel::LINKS);
		}
		else
		{
			echo "Nie możesz tu być!";
		}
    }
}

?>
