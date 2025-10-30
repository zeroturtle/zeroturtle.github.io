<?php
/**
* Register a user
*
* @param string $email
* @param string $username
* @param string $password
* @param bool $is_admin
* @return bool
*/

  // Automatically Logout Inactive User
  // Set the inactivity time of 15 minutes (900 seconds)
define("MAX_INACTIVITY_TIME", 12 * 60);


// Check if the last_timestamp is set
// and last_timestamp is greater then 15 minutes or 9000 seconds
// then unset $_SESSION variable & destroy session data
function inactivity_time(): void
{
    if (isset($_SESSION['last_timestamp']) && (time() - $_SESSION['last_timestamp']) > MAX_INACTIVITY_TIME) {
        session_unset();
        session_destroy();
    } else {
        // Regenerate new session id and delete old one to prevent session fixation attack
        session_regenerate_id(true);
        // Update the last timestamp
        $_SESSION['last_timestamp'] = time();
    }
}

function register_user(string $email, string $username, string $password, string $activation_code, bool $newsletter = false, string $lifetime = '+1 day', bool $is_admin = false): bool
{
    $sql = 'INSERT INTO accounts(username, email, password, newsletter, activation_code, activation_expiry)
            VALUES(:username, :email, :password, :newsletter, :activation_code,:activation_expiry)';

    $statement = db()->prepare($sql);

    $statement->bindValue(':username', $username);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
    $statement->bindValue(':newsletter', (bool)$newsletter, PDO::PARAM_INT);
    $statement->bindValue(':activation_code', password_hash($activation_code, PASSWORD_DEFAULT));
    $statement->bindValue(':activation_expiry', (new DateTime($lifetime))->format('Y-m-d H:i:s'));

    return $statement->execute();
}


function find_user_by_username(string $username)
{
    $sql = 'SELECT id, username, password, active, email
            FROM accounts
            WHERE active=true and username=:username';

    $statement = db()->prepare($sql);
    $statement->bindValue(':username', $username, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function find_user_by_email(string $email)
{
    $sql = 'SELECT id, username, password, active, email
            FROM accounts
            WHERE active=true and email=:email';

    $statement = db()->prepare($sql);
    $statement->bindValue(':email', $email, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

function login(string $username, string $password): bool
{
    $user = find_user_by_username($username);

    // if user found, check the password
    if ($user && is_user_active($user) && password_verify($password, $user['password'])) {

        // prevent session fixation attack
        session_regenerate_id();

        // set username in the session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        return true;
    }

    return false;
}

function is_user_active($user)
{
    return (int)$user['active'] === 1;
}

function is_user_logged_in(): bool
{
    return isset($_SESSION['username']);
}


function logout(): void
{
    if (is_user_logged_in()) {
        unset($_SESSION['username'], $_SESSION['user_id'], $_SESSION['target_link']);
        session_destroy(); // Destroy the active session, which logs the user out 
        redirect_to("/about.html");
    }
}

function current_user()
{
    if (is_user_logged_in()) {
        return $_SESSION['username'];
    }
    return null;
}

function require_login(): void
{
    if (!is_user_logged_in()) {
        $_SESSION['target_link'] = $_SERVER['REQUEST_URI'];   //save target url
        redirect_to('../auth/login.php');
    }
    else {
        unset($_SESSION['target_link']);
    }

}

function generate_activation_code(): string
{
    return bin2hex(random_bytes(16));
}

function activate_user(int $user_id): bool
{
    $sql = 'UPDATE accounts
            SET active = 1,
                activated_at = CURRENT_TIMESTAMP
            WHERE id=:id';

    $statement = db()->prepare($sql);
    $statement->bindValue(':id', $user_id, PDO::PARAM_INT);

    return $statement->execute();
}

function delete_user_by_id(int $id, int $active = 0)
{
    $sql = 'DELETE FROM accounts
            WHERE id =:id and active=:active';

    $statement = db()->prepare($sql);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->bindValue(':active', $active, PDO::PARAM_INT);

    return $statement->execute();
}

function find_unverified_user(string $activation_code, string $email)
{

    $sql = 'SELECT id, activation_code, activation_expiry < now() as expired
            FROM accounts
            WHERE active = 0 AND email=:email';

    $statement = db()->prepare($sql);
    $statement->bindValue(':email', $email);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // already expired, delete the in active user with expired activation code
        if ((int)$user['expired'] === 1) {
            delete_user_by_id($user['id']);
            return null;
        }
        // verify the password
        if (password_verify($activation_code, $user['activation_code'])) {
            return $user;
        }
    }

    return null;
}

function recover_request(string $email, string $validation_code, string $lifetime = '+1 day'): bool
{
    $sql = 'UPDATE accounts SET activation_code = :activation_code, activation_expiry = :activation_expiry WHERE email = :email';
    $statement = db()->prepare($sql);

    $statement->bindValue(':email', $email);
    $statement->bindValue(':activation_code', $validation_code);
    $statement->bindValue(':activation_expiry', (new DateTime($lifetime))->format('Y-m-d H:i:s'));

    return $statement->execute();
}

function reset_password(int $user_id, string $password): bool
{
    $sql = "UPDATE accounts SET password = :password WHERE id = :id";

    $statement = db()->prepare($sql);
    $statement->bindValue(':password', $password);
    $statement->bindValue(':id', $user_id);

    return $statement->execute();
}

function find_user(string $activation_code)
{
    $sql = 'SELECT id, activation_code, activation_expiry < now() as expired
            FROM accounts
            WHERE active = 1 AND activation_code = :activation_code';

    $statement = db()->prepare($sql);
    $statement->bindValue(':email', $email);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);

    return ($user) ? $user : null;
}


?>