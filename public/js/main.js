const sidebarjs = new SidebarJS();
const langButton = document.getElementById("langButton");

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