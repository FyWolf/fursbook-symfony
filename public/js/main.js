const sidebarjs = new SidebarJS();
const langButton = document.getElementById("langButton");
let fontSize;

if(!localStorage.getItem("fontSize")) {
  localStorage.setItem("fontSize", "15");
  fontSize = 15;
}
else {
  fontSize = parseInt(localStorage.getItem("fontSize"));
  document.documentElement.style.setProperty('font-size', fontSize + "px");
}

if(localStorage.getItem("isDyslexiaFont") == null) {
  localStorage.setItem("isDyslexiaFont", false);
}
else if(localStorage.getItem("isDyslexiaFont") == "true"){
  document.body.style.fontFamily = "dyslexia";
}

if(localStorage.getItem("darkMode") == null) {
  localStorage.setItem("darkMode", false);
}
else if(localStorage.getItem("darkMode") == "true"){
  document.documentElement.style.setProperty('--mainBgColor', "#000");
  document.documentElement.style.setProperty('--altBgColor', "#000");
  document.documentElement.style.setProperty('--scndAltBgColor', "#000");
}

function sendAlert(message, type) {
    const alertDiv = document.getElementById('siteInfoDiv');
    let p = document.querySelector('#siteInfoDiv p');
    p.innerHTML = message;
    alertDiv.classList.add(type);
    alertDiv.classList.remove("infoDivHidden");
    setTimeout(() => {
        alertDiv.classList.add("infoDivHidden");
        alertDiv.classList.remove(type);
    }, 3000);
}

function setLocale(lang) {
    $.post(
        '/lang',
        {
          'lang': lang,
        },
        function (response) {
            location.reload();
        },
      );
}

langButton.addEventListener('click', function(e){
      let contextMenu = document.getElementById('langDiv');
      contextMenu.classList.remove("hidden");
});

document.addEventListener('click', function(e){
    let inside = (e.target.closest('#langDiv'));
    let ignoreButton = langButton.contains(e.target);
    if(!inside && !ignoreButton){
      let contextMenu = document.getElementById('langDiv');
      contextMenu.classList.add("hidden");
    }
});

function closeModal() {
  const hidder = document.getElementById("modalBackground");
  const modal = document.getElementById("modalDiv");
  const modalContent = document.getElementById("modalContent");
  modalContent.innerHTML = "";
  hidder.classList.add("hidden");
  modal.classList.add("hidden");
}



function swapFont() {
  if (localStorage.getItem("isDyslexiaFont") == "false") {
      document.body.style.fontFamily = "dyslexia";
      localStorage.setItem("isDyslexiaFont", "true");
  } else {
      document.body.style.fontFamily = "Inter";
      localStorage.setItem("isDyslexiaFont", "false");
  }
}

function changeFontSize(size) {
  if(size == "0") {
    fontSize = 15;
    localStorage.setItem("fontSize", "15");
    document.documentElement.style.setProperty('font-size', fontSize + "px");
  }
  else {
    if(fontSize < 22 && size == 1) {
      fontSize += 1;
      localStorage.setItem("fontSize", fontSize);
      document.documentElement.style.setProperty('font-size', fontSize + "px");
    }
    else if(fontSize > 10 && size == -1) {
      fontSize -= 1;
      localStorage.setItem("fontSize", fontSize);
      document.documentElement.style.setProperty('font-size', fontSize + "px");
    }
  }
}

function changeContrast() {
  let posts = document.querySelectorAll(".posts");
  const profile = document.getElementById("userProfile");
  const searchForm = document.getElementById("searchForm");
  const searchUserDiv = document.getElementById("searchUserDiv");

  if(localStorage.getItem("darkMode") == "false") {
    document.documentElement.style.setProperty('--mainBgColor', "#000");
    document.documentElement.style.setProperty('--altBgColor', "#000");
    document.documentElement.style.setProperty('--scndAltBgColor', "#000");
    localStorage.setItem("darkMode", true);
    setCookie("darkMode", true);

    if(searchUserDiv) {
      searchUserDiv.classList.add("darkSearchUser");
    }
    if(searchForm) {
      searchForm.classList.add("darkSearchForm");
    }
    if(profile) {
      profile.classList.add("darkProfile");
    }
    posts.forEach(function(post){
      post.classList.add("darkPost");
    });
  }
  else {
    document.documentElement.style.setProperty('--mainBgColor', "#261d2f");
    document.documentElement.style.setProperty('--altBgColor', "#311f3f");
    document.documentElement.style.setProperty('--scndAltBgColor', "#3f2652");
    localStorage.setItem("darkMode", false);
    setCookie("darkMode", false);

    if(searchUserDiv) {
      searchUserDiv.classList.remove("darkSearchUser");
    }
    if(searchForm) {
      searchForm.classList.remove("darkSearchForm");
    }
    if(profile) {
      profile.classList.remove("darkProfile");
    }
    posts.forEach(function(post){
      post.classList.remove("darkPost");
    });
  }
}

function setCookie(cname, cvalue) {
  const d = new Date();
  d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
  let expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}