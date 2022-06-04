const acc = document.getElementsByClassName("accordion");

for (let i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
      this.classList.toggle("active");

      let panel = this.nextElementSibling;
      if (panel.style.display === "none") {
        panel.style.display = "block";
      } else {
        panel.style.display = "none";
      }
    });
}

function selectPannel(name) {
  $.post(
    window.location.pathname,
    {
      'action': 'switch',
      'pageName': name,
    },
    function (response) {
      if(response.page){
        document.getElementById("mainContent").innerHTML = response.page;
      }
    },
  );
}