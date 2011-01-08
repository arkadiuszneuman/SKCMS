function CheckPass() //funkcja sprawdza czy 2 hasla sa takie same
{
    var password1 = document.getElementById("pass1");
    var password2 = document.getElementById("pass2");

    if (password1.value != password2.value || password1.value.length < 3)
    {
        //password1.style.border = "2px solid red";
        //password2.style.border = "2px solid red";
        document.getElementById('txtPass').style.display = 'inline';
    }
    else
    {
        //password1.style.border = "2px solid lightgreen";
        //password2.style.border = "2px solid lightgreen";
        document.getElementById('txtPass').style.display = 'none';
    }
}

function EmailValidate()
{
    var regex = /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
    var mail = document.getElementById("mail1");
    if (regex.test(mail.value))
    {
      document.getElementById('txtMail').style.display = 'none';
    }
    else
    {
      document.getElementById('txtMail').style.display = 'inline';
    }
}


oldTime = new Date(); //zapamietanie daty od ostatniego wyszukiwania w bazie
isPending = false; //funkcja CheckAjaxUser juz czeka na wywolanie, nie ma sensu uruchamiac nastepnej
//funkcja sprawdza czy uzytkownik wpisany do textboksa uzytkownik juz istnieje w bazie
function CheckAjaxUser()
{
    var r = getXMLHttpRequest();
    login = document.getElementsByName("login")[0].value;
    r.open('GET', './ajax/ajaxregisteruser.php?login='+login, true);

    r.onreadystatechange = function()
    {
        if (r.readyState == (1 || 0))
        {
            document.getElementById('txtUser').innerHTML = "Ładowanie...";
        }
        else if (r.readyState == 4)
        {
            document.getElementById('txtUser').innerHTML = r.responseText;
            oldTime = new Date();
            isPending = false;
        }
        else
        {
            document.getElementById('txtUser').innerHTML = "Błąd";
        }
    }

    r.send(null);
}

//spawdzanie podczas rejestracji czy podany uzytkownik juz istnieje po kazdym wpisaniu znaku, oszczednosc bazy
function CheckUser()
{
    wait = 2000; //czas jaki musi uplynac pomiedzy kolejnymi zapytaniami w bazie (3 sekundy)
    time = ((new Date()).getTime() - oldTime.getTime()); //roznica dat
    if (time > wait)
        time = wait;

    if (!isPending)
    {
        setTimeout("CheckAjaxUser()", wait-time); //wywolanie funkcji CheckAjaxUser po podanym czasie (max 3 sekundy - tylko wtedy jesli wpisze sie jeden znak po nastepnym)
        isPending = true;
    }
}

function CheckForm()
{
    if (document.getElementById('txtMail').style.display != 'none')
    {
        alert("Nieprawidłowy mail");
        return false;
    }
    if (document.getElementById('txtPass').style.display != 'none')
    {
        alert("Nieprawidłowe hasła");
        return false;
    }
    if (document.getElementsByName("login")[0].value == "")
    {
        alert("Nieprawidłowy login");
        return false;
    }

    return true;
}

function Validate()
{
    /*var passInputs = document.getElementsByName("pass"); //szuka inputow o nazwie pass

    for (var i = 0; i < passInputs.length; ++i)
    {
        passInputs[i].onkeyup = CheckPass; //przypisuje im funkcje checkPass przy puszczeniu klawisza
    }*/
}


