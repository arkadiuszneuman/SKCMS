function checkPass() //funkcja sprawdza czy 2 hasla sa takie same
{
    var password1 = document.getElementById("pass1");
    var password2 = document.getElementById("pass2");

    if (password1.value != password2.value || password1.value.length < 3)
    {
        password1.style.border = "2px solid red";
        password2.style.border = "2px solid red";
    }
    else
    {
        password1.style.border = "2px solid lightgreen";
        password2.style.border = "2px solid lightgreen";
    }
}

window.onload = function()
{
    var passInputs = document.getElementsByName("pass"); //szuka inputow o nazwie pass

    for (var i = 0; i < passInputs.length; ++i)
    {
        passInputs[i].onkeyup = checkPass; //przypisuje im funkcje checkPass przy puszczeniu klawisza
    }
}

