function userFormSubmit() {
    const passwdOld = document.getElementById('user_settings_oldPassword').value;
    const passwd1 = document.getElementById('user_settings_newPassword1').value;
    const passwd2 = document.getElementById('user_settings_newPassword2').value;
    if(passwd1 && passwd2) {
        if(passwd1 == passwd2) {
            setNewPassword(passwdOld)
        }
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
                sendAlert('the mail has been sent', 'informative');
            }
            else {
                sendAlert(trans.errorOccured, 'error');
            }
        },
      );
}


function setNewPassword(old) {
    $.post(
        window.location.pathname,
        {
          'action': 'setNewPassword',
          'oldPwd': old,
        },
        function (response) {
            if(response.done == 'saved') {
                sendAlert('The informations had been saved', 'success');
            }
            else {
                sendAlert(trans.pwdMissMatch, 'error');
            }
        },
      );
}