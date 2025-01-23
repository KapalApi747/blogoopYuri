<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");

$the_message = "";
$show_modal = false;

if (isset($_SESSION['the_message'])) {
    $the_message = $_SESSION['the_message'];
    unset($_SESSION['the_message']); // Verwijder de melding na ophalen
}
if (isset($_POST['submit'])) {
    $validation = new Validation();
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmpassword']);
    //check als de user bestaat in onze database
    $user_found = User::verify_user($username, $password);
    $email_found = User::verify_email($email);

    if ($user_found) {
        $show_modal = true;
        $the_message = "This user already exists!";

    } elseif ($email_found) {
        $show_modal = true;
        $the_message = "This email already exists, please use a different one!";
        $_SESSION['the_message'] = $the_message;

    } elseif ($validation->check_username($username) && $validation->check_email($email) && $validation->check_password($password) && $password === $confirmpassword) {
        $user = new User();
        $user->username = trim($_POST['username']);
        $user->first_name = trim($_POST['first_name']);
        $user->last_name = trim($_POST['last_name']);
        $user->email = trim($_POST['email']);
        $user->password = trim($_POST['password']);
        $user->status = 1;
        $user->create();

        $the_message = "New user: " . $user->username . " was added to the Database.";

        // Zet de boodschap in de sessie voor gebruik na redirect
        $_SESSION['the_message'] = $the_message;

        // Voer een redirect uit naar dezelfde pagina (zonder POST-data)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); // Stop verdere uitvoering van het script

    } elseif ($validation->check_username($username) === false && $validation->check_password($password) === false) {
        $show_modal = true;
        $the_message = "Both the username and password you've entered are not valid!";
        $_SESSION['the_message'] = $the_message;

    } elseif ($validation->check_username($username) === false) {
        $show_modal = true;
        $the_message = "The username you've entered is not valid, it has to be at least 3 characters long and can only contain alphanumeric characters!";
        $_SESSION['the_message'] = $the_message;

    } elseif ($validation->check_email($email) === false) {
        $show_modal = true;
        $the_message = "The e-mail address you've entered is not valid!";
        $_SESSION['the_message'] = $the_message;

    } elseif ($validation->check_password($password) === false) {
        $show_modal = true;
        $the_message = "The password you've entered is not valid, it has to be at least 8 characters long and contain at least one special character!";
        $_SESSION['the_message'] = $the_message;

    } else {
        $show_modal = true;
        $the_message = "The passwords you've entered are not alike!";
        $_SESSION['the_message'] = $the_message;
    }
}

if(isset($_GET['delete'])) {
    //$user_id = $_GET['delete'];
    $user = User::find_user_by_id($_GET['delete']);
    if($user) {
        $user->delete();
        header("Location: users_modals.php");
        exit;
    } else {
        return "<script>alert('User not found!')</script>";
    }
}
?>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">
                    Users
                </h5>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                    <tr>
                        <th>Customer Nr</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Actions</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $users = User::find_all_users(); ?>
                    <?php foreach($users as $user):?>
                        <tr>
                            <td><?= $user->id; ?></td>
                            <td><span><img height="40" width="40" class="avatar me-3" src="../admin/assets/static/images/faces/8.jpg" alt=""></span><?= $user->username; ?></td>
                            <td><?= $user->password;?></td>
                            <td><?= $user->first_name;?></td>
                            <td><?= $user->last_name; ?></td>
                            <td><?= $user->email; ?></td>
                            <td>
                                <div class="d-flex">
                                    <div class="mx-2">
                                        <a href="users.php?delete=<?php echo $user->id; ?>" onclick="return confirm("Weet je zeker dat je deze gebruiker wil verwijderen?")">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                    </div>
                                    <div class="mx-2">
                                        <a href="edit_user.php?id=<?php echo $user->id; ?>">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($user->status == 1): ?>
                                    <span class="text-success">Active</span>
                                <?php endif ?>
                                <?php if($user->status == 0): ?>
                                    <span class="text-danger">Inactive</span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section>
        <div id="addUserModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <?php if(!empty($the_message)):?>
                                        <div class="alert alert-danger alert-dismissible show fade">
                                            <?php echo $the_message; ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    <form action="" method="post">
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="text" class="form-control form-control-xl" placeholder="First Name" name="first_name" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="text" class="form-control form-control-xl" placeholder="Last Name" name="last_name" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="text" class="form-control form-control-xl" placeholder="Username" name="username" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="text" class="form-control form-control-xl" placeholder="E-mail Address" name="email" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-envelope"></i>
                                            </div>
                                        </div>
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="password" class="form-control form-control-xl" placeholder="Password" name="password" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-shield-lock"></i>
                                            </div>
                                        </div>
                                        <div class="form-group position-relative has-icon-left mb-4">
                                            <input type="password" class="form-control form-control-xl" placeholder="Confirm Password" name="confirmpassword" required>
                                            <div class="form-control-icon">
                                                <i class="bi bi-shield-lock"></i>
                                            </div>
                                        </div>
                                        <input type="submit" name="submit" value="Register" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
                                        <!--                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>-->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
if (isset($the_message) && !empty($the_message)) {
    echo '<script>
        var addUserModal = new bootstrap.Modal(document.getElementById("addUserModal"));
        addUserModal.show();
    </script>';
}
?>
<script src="assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="assets/static/js/pages/simple-datatables.js"></script>

<?php
require_once("includes/footer.php");
?>
