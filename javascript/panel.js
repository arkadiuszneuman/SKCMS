function quRemoveNote(note, id) //funkcja upewnia sie, ze chcemy usunac newsa i przekierowywuje do odpowiedniej strony w razie odpowiedzi twierdzacej
{
    if (confirm("Czy chcesz usunąć newsa: "+note+"?"))
    {
        window.location.href = "./panel.php?task=removeNote&id="+id;
    }
}

function Event(action)
{
        var checkboxes = document.getElementsByName("check[]");
        var isOneChecked = false;
        for (var i = 0; i < checkboxes.length; ++i)
        {
            if (checkboxes[i].checked)
            {
                isOneChecked = true;
                break;
            }
        }
        if (!isOneChecked)
            alert("Aby przenieść newsa do kosza musisz jakiegoś zaznaczyć.");
        else
        {
            document.binFrm.action = action;
            document.binFrm.submit();
        }

        return false;
}

function ButtonsEvents() //eventy (klikniecia) na przyciski do kosza, przywroc z kosza i usun permamentnie
{
    var btn = document.getElementById("toBinBtn");
    if (btn != null)
        btn.onclick = function() { return Event("panel.php?task=moveToBin"); }

    btn = document.getElementById("binToNews");
    if (btn != null)
        btn.onclick = function() { return Event("panel.php?task=binToNews"); }
    
    btn = document.getElementById("binRemove");
    if (btn != null)
        btn.onclick = function() { return Event("panel.php?task=binRemove"); }
}


window.onload = function()
{

    ButtonsEvents();

    

    /*/obsluga zaznaczania sie na zolto tabeli
    var tab = document.getElementById("tabPanel"); //zlapanie tabeli
    var tr = tab.getElementsByTagName("tr"); //zlapanie tr'ow w tabeli
    var rememberColor; //zapamietanie koloru, ktory oryginalnie ma tabela

    for (var i = 0; i < tr.length; i++)
    {
        tr[i].onmouseover = function() //dla kadego tr przypisanie funkcji onmouseover i out
        {
            rememberColor = this.style.backgroundColor;
            this.style.backgroundColor = "#faffcf"; //zolty
        }
        tr[i].onmouseout = function()
        {
            this.style.backgroundColor = rememberColor;
        }
    }*/
}