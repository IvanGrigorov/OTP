(function() {
    const form = document.getElementById('validate');
    const emlForm = document.getElementById('otpform');

    form.addEventListener('submit', (event) => {
        let password = form.elements[0].value;
        let email = emlForm.elements[0].value;
        let telephone = emlForm.elements[1].value;
        telephone = validateNumber(telephone);
        validatePassword({
            password: password,
            email: email,
            telephone: telephone
        });
        event.preventDefault();
    });
 })();

 function validatePassword(data) {
    let formData = new FormData();
    formData.append('password', data.password);
    formData.append('email', data.email);
    formData.append('telephone', data.telephone);
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            showMessage(JSON.parse(xhr.responseText)); // Another callback here
        }
    };
    xhr.open("POST", './server/validateOTP.php', true);
    xhr.send(formData);
}

function showMessage(json) {
    alert(json.data.message);
    setCoolDownIfNeeded(json.data.attempts)
}

function setCoolDownIfNeeded(attempts) {
    if (attempts >= 3) {
        const validateBtn = document.getElementById('validateBtn');
        const generateBtn = document.getElementById('generateBtn');
        validateBtn.setAttribute('disabled', true);
        generateBtn.setAttribute('disabled', true);
        setTimeout(function(){
            validateBtn.removeAttribute('disabled');
            generateBtn.removeAttribute('disabled');
          },60000);
    }
}