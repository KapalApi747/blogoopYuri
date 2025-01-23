<?php

class Validation
{
    // Methods
    public function check_username($username) {
        return strlen(htmlspecialchars($username)) >= 3 && ctype_alnum($username);
    }

    public function check_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function check_password($password) {
        return strlen(htmlspecialchars($password)) >= 8 && ctype_alnum($password) == false;
    }
}
