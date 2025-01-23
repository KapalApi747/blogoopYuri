<?php
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/content-top2.php");

if(isset($_GET['delete'])) {
    //$user_id = $_GET['delete'];
    $user = User::find_user_by_id($_GET['delete']);
    if($user) {
        $user->delete();
        header("Location: users.php");
        exit;
    } else {
        return "<script>alert('Gebruiker niet gevonden!')</script>";
    }
}
?>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Users
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                    <tr>
                        <th>Klantnr</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Voornaam</th>
                        <th>Familienaam</th>
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
                            <td>
                                <div class="d-flex">
                                    <div class="mx-2">
                                        <a href="users.php?delete=<?php echo $user->id; ?>" onclick="return confirm("weet je zeker dat je deze gebruiker wil verwijderen?")">
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
<script src="assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="assets/static/js/pages/simple-datatables.js"></script>

<?php
require_once("includes/footer.php");
?>
