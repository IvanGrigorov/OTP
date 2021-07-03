function validateNumber(number) {
    let correctNumber = number;
    correctNumber = trimNumber(correctNumber);
    correctNumber = appendCountryCodeIfNeeded(correctNumber);
    return correctNumber;
}

function trimNumber(number) {
    const trimmed = number.replace(/\D/ig, '');
    return trimmed;
}

function appendCountryCodeIfNeeded(number) {
    const prefixRegex = /^0/;
    const correctCountryCodeRegex = /^359/;
    let correctNumber = number;
    if (prefixRegex.test(correctNumber)) {
        correctNumber = correctNumber.replace(prefixRegex, '');
    }
    if (!correctCountryCodeRegex.test(correctNumber)) {
        correctNumber = '359' + correctNumber;
    }
    return correctNumber;
}

function generateOTP(data) {
    let formData = new FormData();
    formData.append('email', data.email);
    formData.append('telephone', data.telephone);

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && xhr.status == 200)
        {
            showPassword(JSON.parse(xhr.responseText)); // Another callback here
        }
    }; 
    xhr.open("POST", './server/generateOTP.php', true);
    xhr.send(formData);
}

function showPassword(json) {
    alert(json.password);
}

(function() {
    const form = document.getElementById('otpform');
    form.addEventListener('submit', (event) => {
        let email = form.elements[0].value;
        let telephone = form.elements[1].value;
        telephone = validateNumber(telephone);
        generateOTP({
            email: email,
            telephone: telephone
        });
        event.preventDefault();
    });
 })();