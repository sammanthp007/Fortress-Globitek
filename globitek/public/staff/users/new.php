<?php
require_once('../../../private/initialize.php');
require_login();

// Set default values for all variables the page needs.
$errors = array();
$user = array(
    'id' => null,
    'first_name' => '',
    'last_name' => '',
    'username' => '',
    'email' => '',
    'password_confirmation' => '',
    'password' => ''
);

if(is_post_request() && request_is_same_domain()) {
    ensure_csrf_token_valid();

    // Confirm that values are present before accessing them.
    if(isset($_POST['first_name'])) {
        $user['first_name'] = urlencode($_POST['first_name']);
    }
    if(isset($_POST['last_name'])) {
        $user['last_name'] = urlencode($_POST['last_name']);
    }
    if(isset($_POST['username'])) {
        $user['username'] = urlencode($_POST['username']);
    }
    if(isset($_POST['email'])) {
        $user['email'] = ($_POST['email']);
    }
    if(isset($_POST['password'])) {
        $user['password'] = ($_POST['password']);
    }
    if(isset($_POST['password_confirmation'])){
        $user['password_confirmation'] = ($_POST['password_confirmation']);
    }

    // TODO- Validation required here
    // Returns an error if password or confirm_password are blank.
    if (is_blank($user['password'])) {
        $errors[] = "Password cannot be blank.";
    }

    if (is_blank($user['password_confirmation'])) {
        $errors[] = "Confirm Password cannot be blank.";
    }

    // Returns an error if password and confirm_password do not match.
    if (!is_same($user['password'], $user['password_confirmation'])) {
        $errors[] = "Password and Confirm Password do not match.";
    }

    // Returns an error if password is not at least 12 characters long
    if (!has_length($user['password'], ['min' => 12])) {
        $errors[] = "Password should be more than 12 characters.";
    }

    // Returns an error if password does not contain at least one of each: uppercase letter, lowercase letter, letter, symbol.
    if (!has_valid_password($user['password'])) {
        $errors[] = "Password should contain at least one of each: uppercase letter, lowercase letter, letter, symbol";
    }

    // Validation ENDS HERE

    if (empty($errors)) {
        $result = insert_user($user);
        if($result === true) {
            $new_id = db_insert_id($db);
            redirect_to('show.php?id=' . $new_id);
        } else {
            $errors = $result;
        }
    }
}
?>
<?php $page_title = 'Staff: New User'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="main-content">
  <a href="index.php">Back to Users List</a><br />

  <h1>New User</h1>

  <?php echo display_errors($errors); ?>

  <form action="new.php" method="post">
    <?php echo csrf_token_tag(); ?>
    First name:<br />
    <input type="text" name="first_name" value="<?php echo h($user['first_name']); ?>" /><br />
    Last name:<br />
    <input type="text" name="last_name" value="<?php echo h($user['last_name']); ?>" /><br />
    Username:<br />
    <input type="text" name="username" value="<?php echo h($user['username']); ?>" /><br />
    Email:<br />
    <input type="text" name="email" value="<?php echo h($user['email']); ?>" /><br />
    Password:<br />
    <input type="password" name="password" /><br />
    Confirm Password:<br />
    <input type="password" name="password_confirmation" /><br />
    <br />
<p>
   Passwords should be at least 12 characters and include at least one uppercase letter, lowercase letter, number, and symbol.
</p>

    <input type="submit" name="submit" value="Create"  />
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
