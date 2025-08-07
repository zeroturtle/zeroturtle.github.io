<?php

if (is_user_logged_in()) {
    redirect_to('/about.html');
}

$inputs = [];
$errors = [];

if (is_post_request()) {

    // sanitize & validate user inputs
    [$inputs, $errors] = filter($_POST, [
        'password' => 'string | required | secure',
        'password2' => 'string | required | same: password'
    ]);

    // if validation error
    if ($errors) {
        redirect_with('resetpwd.php', [
            'errors' => $errors,
            'inputs' => $inputs
        ]);
    }

    // if login fails
    if (true) {

        //$errors['login'] = 'Invalid username or password';

        redirect_with('resetpwd.php', [
            'errors' => $errors,
            'inputs' => $inputs
        ]);
    }


} else if (is_get_request()) {
    [$errors, $inputs] = session_flash('errors', 'inputs');
}

?>