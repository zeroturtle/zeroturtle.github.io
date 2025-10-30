<?php

require __DIR__ . '/../src/bootstrap.php';
//require __DIR__ . '/../src/resetpwd.php';


view('header', ['title' => 'Reset password']); 

if (is_get_request()) {

    // sanitize the email & activation code
    [$inputs, $errors] = filter($_GET, [
        'validation_code' => 'string | required'
    ]);

    if ($errors) {

       // redirect to the register page in other cases
        redirect_with_message(
            'recover.php',
            'You are trying to use the expired link which as valid only 24 hours after request or you have already used the key in which case it is deactivated.<br>
             Please try again.',
            FLASH_ERROR
        );
    }
}

?>

      <div class="d-flex justify-content-center m-3">
        <div class="shadow p-3 mb-5 bg-body-tertiary rounded" style="background-color: #fff; width: 400px;  max-width: 95%;">
		<form id="myForm" action="resetpwd.php" method="post">
		<h3>Reset account password</h3>
                    <fieldset class="d-grid gap-3">
                        <input type="hidden" name="validation_code" value="<?= $inputs['validation_code'] ?>">
                        <div class="form-group">
                          <label for="password">Please create a new password:</label>
                          <input type="password" name="password" id="password" value="<?= $inputs['password'] ?? '' ?>"
                                 class="form-control <?= error_class($errors, 'password') ?>">
                          <small class="text-danger"><?= $errors['password'] ?? '' ?></small>
                        </div>
                        <div class="form-group">
                          <label for="password2">Confirm New Password:</label>
                          <input type="password" name="password2" id="password2" value="<?= $inputs['password2'] ?? '' ?>"
                                 class="form-control <?= error_class($errors, 'password2') ?>">
                          <small class="text-danger"><?= $errors['password2'] ?? '' ?></small>
                        </div>
			<p class="text-warning"><?php echo (!empty($error) ? $error : ''); ?></p>
			<div class="text-left form-group">
				<button type="submit" class="btn btn-primary btn-md w-100">Change Account Password</button>
			</div>
                    </fieldset>
	        </form>
        </div>
      </div>


<?php view('footer') ?>