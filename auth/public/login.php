<?php

require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/login.php';
?>

<?php view('header', ['title' => 'Login']) ?>

<?php if (isset($errors['login'])) : ?>
    <div class="alert alert-error">
        <?= $errors['login'] ?>
    </div>
<?php endif ?>

      <div class="d-flex justify-content-center m-3">
        <div class="shadow p-3 mb-5 bg-body-tertiary rounded" style="background-color: #fff; width: 400px;  max-width: 95%;">
		<h3 class="mb-3 dark-grey-text">Sign in</h3>
		<p>Don't have an account yet?  <a href="register.php">Sign up now</a></p>
		<form id="myForm" action="login.php" method="post">
		<fieldset class="d-grid gap-3">
		<div class="form-group">
                  <label for="username">Username:</label>
                  <input type="text" name="username" id="username" value="<?= $inputs['username'] ?? '' ?>" class="form-control">
                  <small class="text-danger"><?= $errors['username'] ?? '' ?></small>
		</div>        
		<div class="form-group">
                  <label for="password">Password:</label>
                  <input type="password" name="password" id="password" class="form-control">
                  <small class="text-danger"><?= $errors['password'] ?? '' ?></small>
		</div>
		</fieldset>
		<p class="text-warning"><?php echo (!empty($error) ? $error : ''); ?></p>
		<div class="form-group form-inline text-left py-2">
			<button type="submit" name="submit"  class="btn btn-primary w-100">Login</button>
		</div>
		</form>
		<p><small>Forgot password? <a href="recover.php" id="wlRecover" style="">Recover your password</a></small></p>
	</div>	
      </div>


<?php view('footer') ?>