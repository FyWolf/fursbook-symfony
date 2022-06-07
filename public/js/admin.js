const acc = document.getElementsByClassName("accordion");
let offset = 0;
let page = 1;
let userCount = 0;
let oppenedDD = "";

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
        offset = 0;
        page = 1;
        userCount = response.userCount;
        document.getElementById("mainContent").innerHTML = response.page;
        document.getElementById("usrlistPgCount").innerText = page;
      }
    },
  );
}

function usrListPgSwitch(sw) {
    if(sw) {
      offset += 15;
      if(offset >= userCount) {
        offset -= 15;
        return;
      }
      page += 1;
    }
    else if(offset != 0) {
      offset -= 15;
      page -= 1;
    }
    $.post(
      window.location.pathname,
      {
        'action': 'usrListSwitch',
        'offset': offset,
      },
      function (response) {
        if(response.template){
          document.getElementById("usrListDiv").innerHTML = response.template;
          document.getElementById("usrlistPgCount").innerText = page;
        }
      },
    );
}

function actionDropDown(id) {
  const DD = document.getElementById("drop" + id);
  if(oppenedDD && oppenedDD !== id) {
    const oppened = document.getElementById("drop" + oppenedDD);
    oppened.classList.add("hidden");
    DD.classList.remove("hidden");
    oppenedDD = id;
  }
  else {
    if(DD.classList.contains("hidden")) {
      DD.classList.remove("hidden");
      oppenedDD = id;
    }
    else {
      DD.classList.add("hidden");
      oppenedDD = null;
    }
  }
}

function editUser(id){
  $.post(
    window.location.pathname,
    {
      'action': 'switch',
      'pageName': 'editProfile',
      'id': id,
    },
    function (response) {
      if(response.page){
        document.getElementById("mainContent").innerHTML = response.page;
      }
    },
  );
}

document.addEventListener('click', function(e){
  if(oppenedDD){
    let ignore1 = document.getElementById("drop" + oppenedDD);
    let ignore2 = document.getElementById("dropBtn" + oppenedDD);
    if(e.target != ignore1 && e.target != ignore2) {
      ignore1.classList.add("hidden");
      oppenedDD = null;
    }
  }
});

function openEmailPrompt(id) {
  const div = document.getElementById('modif');
  div.innerHTML = `
  <form onSubmit="return sendEmail(${id})">
    <input name="email" type="email" id="emailInput">
    <button type="submit">Save</button>
  </form>`
  div.classList.remove('hidden')
}

function sendEmail(id) {
  let mail = document.getElementById("emailInput").value
  $.post(
    window.location.pathname,
    {
      'action': 'setEmail',
      'id': id,
      'email': mail,
    },
    function (response) {
      console.log(response.newPassword);
    },
  );
  return false;
}