const passwdOld = document.getElementById('user_settings_oldPassword');
const passwd1 = document.getElementById('user_settings_newPassword1');
const passwd2 = document.getElementById('user_settings_newPassword2');
const email = document.getElementById('user_settings_email');

function userFormSubmit() {
    if(passwd1.value && passwd2.value) {
        if(passwd1.value !== passwdOld.value) {
            if(passwd1.value == passwd2.value) {
                if(passwd1.value.length < 6) {
                    sendAlert("password can't be unser 6 characters", 'warning');
                    passwd1.classList.add("error");
                    passwd2.classList.add("error");
                }
                else {
                    setNewPassword(passwdOld.value, passwd1.value)
                    return false
                }
            }
            else {
                sendAlert(trans.pwdMissMatch, 'error');
                passwd1.classList.add("error");
                passwd2.classList.add("error");
            }
        }
        else {
            sendAlert(trans.pwdSame, 'warning');
        }
        console.log('I did that...')
    }

    else if(email.value !== email.placeholder) {
        setNewEmail(passwdOld.value, email.value)
    }
    return false
}

function sendVerifMail() {
    $.post(
        window.location.pathname,
        {
          'action': 'mailVerify',
        },
        function (response) {
            if(response.done) {
                sendAlert(trans.mailSent, 'informative');
            }
            else {
                sendAlert(trans.errorOccured, 'error');
            }
        },
    );
}


function setNewPassword(oldPwd, newPwd) {
    $.post(
        window.location.pathname,
        {
          'action': 'setNewPassword',
          'oldPwd': oldPwd,
          'newPwd': newPwd,
        },
        function (response) {
            if(response.done) {
                sendAlert(trans.infoSaved, 'success');
            }
            else {
                sendAlert(trans.pwdWrong, 'error');
                passwdOld.classList.add("error");
            }
        },
    );
}

function setNewEmail(oldPwd, newMail) {
    $.post(
        window.location.pathname,
        {
          'action': 'setNewMail',
          'oldPwd': oldPwd,
          'newMail': newMail,
        },
        function (response) {
            if(response.done) {
                sendAlert(trans.infoSaved, 'success');
            }
            else {
                sendAlert(trans.pwdWrong, 'error');
                passwdOld.classList.add("error");
            }
        },
    );
}

$("#user_settings_newPassword1").on("input", function(){
    passwd1.classList.remove("error");
    passwd2.classList.remove("error");
});

$("#user_settings_newPassword2").on("input", function(){
    passwd1.classList.remove("error");
    passwd2.classList.remove("error");
});

$("#user_settings_oldPassword").on("input", function(){
    passwdOld.classList.remove("error");
});