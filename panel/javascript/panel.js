function quRemoveNote(note, id) //funkcja upewnia sie, ze chcemy usunac newsa i przekierowywuje do odpowiedniej strony w razie odpowiedzi twierdzacej
{
    if (confirm("Czy chcesz usunąć newsa: "+note+"?"))
    {
        window.location.href = "./panel.php?task=removeNote&id="+id;
    }
}

function ButtonsEvents() //eventy (klikniecia) na przyciski do kosza, przywroc z kosza i usun permamentnie
{
    /*btn = document.getElementById("binToNews");
    if (btn != null)
        btn.onclick = function() { return Event("panel.php?task=binToNews"); }
    
    btn = document.getElementById("binRemove");
    if (btn != null)
        btn.onclick = function() { return Event("panel.php?task=binRemove"); }*/

    btn = document.getElementById("saveOrder");
    if (btn != null)
    {
        btn.onclick = function()
        {
            document.binFrm.action = "?task=links&do=saveOrder";
            document.binFrm.submit();

            return false;
        }
    }
}


window.onload = function()
{
    ButtonsEvents();

}
