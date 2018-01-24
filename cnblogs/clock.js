//时钟控制
var clock = document.getElementById("clock"), em = document.createElement("em"), b = document.createElement("b");
for (var i = 0; i < 60; i++) {
    em = em.cloneNode(true);
    if (i % 5) {
        em.className = ""
    } else {
        em.className = "em";
        b = b.cloneNode(false);
        b.innerHTML = i / 5 ? i / 5 : 12;
        b.style.left = Math.cos((i * 6 - 90) * Math.PI / 180).toFixed(4) * 40 + 50 + "%";
        b.style.top = Math.sin((i * 6 - 90) * Math.PI / 180).toFixed(4) * 40 + 50 + "%";
        clock.appendChild(b)
    }
    em.style.transform = em.style.WebkitTransform = "rotate(" + i * 6 + "deg)";
    clock.appendChild(em)
}
var pts = clock.getElementsByTagName("div"), d = new Date(), s = d.getSeconds() * 6, m = d.getMinutes() * 6 + s / 60,
    h = d.getHours() % 12 * 30 + m / 12;
pts[0].style.WebkitTransform = "rotate(" + h + "deg)";
pts[1].style.WebkitTransform = "rotate(" + m + "deg)";
pts[2].style.WebkitTransform = "rotate(" + s + "deg)";
document.styleSheets[0].insertRule("@-webkit-keyframes hours{to{-webkit-transform:rotate(" + (360 + h) + "deg);}}", 0);
document.styleSheets[0].insertRule("@-webkit-keyframes minutes{to{-webkit-transform:rotate(" + (360 + m) + "deg);}}", 0);
document.styleSheets[0].insertRule("@-webkit-keyframes seconds{to{-webkit-transform:rotate(" + (360 + s) + "deg);}}", 0);
pts[0].style.WebkitAnimationName = "hours";
pts[1].style.WebkitAnimationName = "minutes";
pts[2].style.WebkitAnimationName = "seconds";