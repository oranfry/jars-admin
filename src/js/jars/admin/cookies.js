window.getCookie = function(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');

    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];

        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }

        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }

    return "";
};

window.setCookie = function(cname, cvalue, exdays) {
    exdays > 0 && setCookie(cname, '', -1);
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; path=" + HOMEPATH + "; " + expires;
};

window.deleteCookie = function(cname) {
    setCookie(cname, '', -1)
};
