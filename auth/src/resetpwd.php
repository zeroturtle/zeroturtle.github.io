<?php

if (is_user_logged_in()) {
    redirect_to('/about.html');
}

$inputs = [];
$errors = [];

if (is_post_request()) {

    // sanitize & validate user inputs
    [$inputs, $errors] = filter($_POST, [
        'validation_code' => 'string | required',
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

    $user = find_user($inputs['validation_code']);

    if ($user && reset_password($user['id'], $inputs['password'])) {

        redirect_with_message(
            'login.php', 
            'Your password has been updated successfully!'
        );
    }


} else if (is_get_request()) {
    [$errors, $inputs] = session_flash('errors', 'inputs');
}

?>