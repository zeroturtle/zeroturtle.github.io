<?php

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/recover.php';
?>

<?php view('header', ['title' => 'Recover']) ?>


      <div class="d-flex justify-content-center m-3">	
        <div class="shadow p-3 mb-5 bg-body-tertiary rounded" style="background-color: #fff; width: 400px;  max-width: 95%;">
		  <form id="myForm" action="recover.php" method="post">
			<h3>Forgot Password?</h3>
			<p class="grey-text">Please enter your email address below. You will receive link to create a new password.</p>

			<div class="form-group py-2">
                          <label for="email">Email:</label>
                          <input type="email" name="email" id="email" value="<?= $inputs['email'] ?? '' ?>"
                                 class="form-control <?= error_class($errors, 'email') ?>">
                          <small class="text-danger"><?= $errors['email'] ?? '' ?></small>
			</div>

			<div class="text-left form-group py-2">
				<button type="submit" class="btn btn-primary btn-md w-100">Reset password</button>
			</div>
	           </form>
        </div>
      </div>



<?php view('footer') ?>