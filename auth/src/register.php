<?php

$errors = [];
$inputs = [];

if (is_post_request()) {

    $fields = [
        'username' => 'string | required | alphanumeric | between: 3, 25 | unique: accounts, username',
        'email' => 'email | required | email | unique: accounts, email',
        'password' => 'string | required | secure',
        'password2' => 'string | required | same: password',
        'agree' => 'string | required',
        'newsletter' => 'string'
    ];

    // custom messages
    $messages = [
        'password2' => [
            'required' => 'Please enter the password again',
            'same' => 'The password does not match'
        ],
        'agree' => [
            'required' => 'You need to agree to the term of services to register'
        ]
    ];

    [$inputs, $errors] = filter($_POST, $fields, $messages);

    if ($errors) {
        redirect_with('register.php', [
            'inputs' => $inputs,
            'errors' => $errors
        ]);
    }

    $activation_code = generate_activation_code();

    if (register_user($inputs['email'], $inputs['username'], $inputs['password'], $activation_code, (bool)$inputs['newsletter'])) {

        // send the activation email
        send_activation_email($inputs, $activation_code);

        redirect_with_message(
            'login.php',
            'Your account has been created successfully. <br>Please check your email, follow a link in email to complete registration.'
        );

    }

} else if (is_get_request()) {
    [$inputs, $errors] = session_flash('inputs', 'errors');
}