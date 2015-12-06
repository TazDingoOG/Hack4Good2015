function formValidation() {
    var refugee_camp_name = document.registration.refugee_camp_name;
    var street_name = document.registration.street_name;
    var street_number = document.registration.street_number;
    var zip = document.registration.zip;
    var phone_number = document.registration.phone_number;
    var contact_person = document.registration.contact_person;
    var email = document.registration.email;

    if (allLetter(refugee_camp_name)) {
        if (allLetter(street_name)) {
            if (alphanumeric(street_number)) {
                if (alphanumeric(zip)) {
                    if (alphanumeric(phone_number)) {
                        if (allLetter(contact_person)) {
                            if (validate_email(email)) {
                                alert('Form Succesfully Submitted');

                                window.location.reload();
                                return true;
                            }
                        }
                    }
                }
            }
        }

    }
    return false;
}

function allLetter(name) {
    var letters = /^[A-Za-z]+$/;
    if (name.value.match(letters)) {
        return true;
    } else {
        alert(name + ' must have alphabet characters only');
        name.focus();
        return false;
    }
}

function alphanumeric(number) {
    var letters = /^[0-9a-zA-Z]+$/;
    if (number.value.match(letters)) {
        return true;
    } else {
        alert(number + 'User address must have alphanumeric characters only');
        number.focus();
        return false;
    }
}

function validate_email(uemail) {
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (uemail.value.match(mailformat)) {
        return true;
    } else {
        alert("You have entered an invalid email address!");
        uemail.focus();
        return false;
    }
}


function send_formular_to_server() {
    // sends variables to server
}