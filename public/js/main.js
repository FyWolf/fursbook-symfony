const sidebarjs = new SidebarJS();

function sendAlert(message, type) {
    const alertDiv = document.getElementById('siteInfoDiv');
    let p = document.querySelector('#siteInfoDiv p');
    p.innerHTML = message;
    alertDiv.classList.add(type);
    alertDiv.classList.remove("infoDivHidden");
    setTimeout(() => {
        alertDiv.classList.add("infoDivHidden");
        alertDiv.classList.remove(type);
    }, 1500);
}