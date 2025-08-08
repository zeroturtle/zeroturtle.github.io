<?php

if (is_user_logged_in()) {
    redirect_to('/about.html');
}

$inputs = [];
$errors = [];

if (is_post_request()) {

    // sanitize & validate user inputs
    [$inputs, $errors] = filter($_POST, [
        'email' => 'string | required'
    ]);

    // if validation error
    if ($errors) {
        redirect_with('recover.php', [
            'errors' => $errors,
            'inputs' => $inputs
        ]);
    }

    $validation_code = generate_activation_code();

    if (recover_request($inputs['email'], $validation_code)) {

        send_validation_email($inputs, $validation_code);

        redirect_with_message(
            'recover.php',
            'Please check your email, follow a link in email to reset password.<br>Return to <a href="/about.html">home page</a>'
        );

    }


} else if (is_get_request()) {
    [$errors, $inputs] = session_flash('errors', 'inputs');
}