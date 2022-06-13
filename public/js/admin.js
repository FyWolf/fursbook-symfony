const acc = document.getElementsByClassName("accordion");
let offset = 0;
let page = 1;
let userCount = 0;
let oppenedDD = "";
let queryRunning = false;
let usernameTaken = false;
let mailTaken = false;

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
        if(name == "userList") {
          offset = 0;
          page = 1;
          userCount = response.userCount;
          document.getElementById("usrlistPgCount").innerText = page;
        }
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

function deleteUserPrompt(id){
  const div = document.getElementById('modif');
  div.innerHTML = `
    <button onClick="deleteUser(${id})">confirm</button>
    <button onClick="cancel()">cancel</button>
  `
  div.classList.remove('hidden')
}

function deleteUser(id) {
  $.post(
    window.location.pathname,
    {
      'action': 'deleteUser',
      'id': id,
    },
    function (response) {
      selectPannel('userList');
      cancel();
    },
  );
}

function cancel() {
  const div = document.getElementById('modif');
  div.innerHTML = "";
  div.classList.add('hidden');
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
    <input placeholder="Email" name="email" type="email" oninput="mailCheck()" id="emailInput" maxlength="180">
    <button type="submit">Save</button>
    <button onClick="cancel()">cancel</button>
  </form>`
  div.classList.remove('hidden')
}

function openUsernamePrompt(id) {
  const div = document.getElementById('modif');
  div.innerHTML = `
  <form onSubmit="return sendUsername(${id})">
    <input placeholder="Usename" name="text" type="text" oninput="usernameCheck()" id="usernameInput" maxlength="25">
    <button type="submit">Save</button>
    <button onClick="cancel()">cancel</button>
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
      editUser(id);
      cancel();
    },
  );
  return false;
}

function sendUsername(id) {
  let username = document.getElementById("usernameInput").value
  $.post(
    window.location.pathname,
    {
      'action': 'setUsername',
      'id': id,
      'username': username,
    },
    function (response) {
      editUser(id);
      cancel();
    },
  );
  return false;
}

function createUser() {
  if(!usernameTaken && !mailTaken) {
    const mail = document.getElementById("emailInput");
    const username = document.getElementById("usernameInput");
    const password = document.getElementById("passwordInput");
    const banner = document.getElementById("banner");
    const pfp = document.getElementById("pfp");
    const bio = document.getElementById("bio");
    let bannerValue = "";
    let pfpValue = "/ressources/images/default/profilePicture.png";
    let bioValue = "";
    if(banner.value) {
      bannerValue = banner.value;
    }
    if(pfp.value) {
      pfpValue = pfp.value;
    }
    if(bio.value) {
      bioValue = bio.value;
    }
    $.post(
      window.location.pathname,
      {
        'action': 'createUser',
        'username': username.value,
        'email': mail.value,
        'password': password.value,
        'banner': bannerValue,
        'pfp': pfpValue,
        'bio': bioValue,
      },
      function (response) {
        if(response){
          sendAlert("User has successfully been created", 'success');
          mail.value = "";
          username.value = "";
          password.value = "";
          bio.value = "";
        }
      },
    );
    return false;
  }
}

function usernameCheck() {
  let timer = "";
  clearTimeout(timer);
  timer = setTimeout(() => {
    const username = document.getElementById("usernameInput");
    if(!queryRunning) {
      queryRunning = true;
      $.post(
        window.location.pathname,
        {
          'action': 'checkUsername',
          'username': username.value,
        },
        function (response) {
          if(response.match) {
            username.classList.add("error");
            usernameTaken = true;
            sendAlert("this username has already been taken", "error");
          }
          else {
            username.classList.remove("error");
            usernameTaken = false;
          }
          queryRunning = false;
        },
      );
    }
  }, 1000);
}

function mailCheck() {
  let timer = "";
  clearTimeout(timer);
  timer = setTimeout(() => {
    const mail = document.getElementById("emailInput");
    if(!queryRunning) {
      queryRunning = true;
      $.post(
        window.location.pathname,
        {
          'action': 'checkEmail',
          'mail': mail.value,
        },
        function (response) {
          if(response.match) {
            mail.classList.add("error");
            mailTaken = true;
            sendAlert("this mail has already been taken", "error");
          }
          else {
            mail.classList.remove("error");
            mailTaken = false;
          }
          queryRunning = false;
        },
      );
    }
  }, 1000);
}