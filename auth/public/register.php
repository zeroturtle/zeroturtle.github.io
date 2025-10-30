<?php
require __DIR__ . '/../src/bootstrap.php';
require __DIR__ . '/../src/register.php';

view('header', ['title' => 'Register']) 
?>

      <div class="d-flex justify-content-center m-3">
        <div class="shadow p-3 mb-5 bg-body-tertiary rounded" style="background-color: #fff; width: 400px;  max-width: 95%;">
              <h3 class="mb-3 dark-grey-text">Sign Up</h3>
              <p>Already have an account? <a href="login.php">Sign in </a></p>
              <form action="register.php" method="post">
		<fieldset class="d-grid gap-3">

                  <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" id="username" value="<?= $inputs['username'] ?? '' ?>"
                           class="form-control <?= error_class($errors, 'username') ?>">
                    <small class="text-danger"><?= $errors['username'] ?? '' ?></small>
                  </div>

                  <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?= $inputs['email'] ?? '' ?>"
                           class="form-control <?= error_class($errors, 'email') ?>">
                    <small class="text-danger"><?= $errors['email'] ?? '' ?></small>
                  </div>

                  <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" value="<?= $inputs['password'] ?? '' ?>"
                           class="form-control <?= error_class($errors, 'password') ?>">
                    <small class="text-danger"><?= $errors['password'] ?? '' ?></small>
                  </div>

                  <div class="form-group">
                    <label for="password2">Confirm Password:</label>
                    <input type="password" name="password2" id="password2" value="<?= $inputs['password2'] ?? '' ?>"
                           class="form-control <?= error_class($errors, 'password2') ?>">
                    <small class="text-danger"><?= $errors['password2'] ?? '' ?></small>
		  </div>

                  <div class="form-group form-inline text-left">
                    <label for="agree">
                       <input type="checkbox" name="agree" id="agree" value="checked" <?= $inputs['agree'] ?? '' ?> /> 
                       &nbsp;I agree with the <a href="#" title="term of services">Term of services</a>
                    </label>
                    <br><small class="text-danger"><?= $errors['agree'] ?? '' ?></small>
                  </div>

                  <div class="form-group form-inline text-left">
                     <label>
                       <div><input type="checkbox" checked name="newsletter" style="outline: none;">&nbsp; Please send me an E-Mail when a new Optimus version is available</div>
                     </label>
                  </div>
		</fieldset>
                  <p class="text-warning"><?php echo (!empty($error) ? $error : ''); ?></p>
                  <div class="text-left form-group">
                      <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>
                  </div>
              </form>
              <p><small><strong>WARNING!</strong> Please use a valid email to receive the confidential information. We'll never share your email with anyone else.</small></p>
        </div>
      </div>


<?php view('footer') ?>
