var $ = jQuery.noConflict();
const cookiePopup = document.getElementById('js-cookie-popup');
const popupButton = document.getElementById('js-cookie-popup-button');

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  let expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  let name = cname + "=";
  let ca = document.cookie.split(';');
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

	function openPopup() {
  $("body").addClass("showPopup");
}
function closePopup() {
  $("body").removeClass("showPopup removePopup");
}
function closePopupScreen() {
  $("body").addClass("removePopup");
  setTimeout(closePopup, 200);
}
