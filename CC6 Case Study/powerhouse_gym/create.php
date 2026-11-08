<?php
// CREATE: add a new member
require __DIR__ . '/includes/functions.php';
$pageTitle = 'Add Member';
$d = ['JoinDate' => date('Y-m-d')]; $errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = member_input($_POST);
    $errors = validate_member($d);
    if (!$errors) {
        try {
            $st = db()->prepare(
                'INSERT INTO Member (FirstName, LastName, DateOfBirth, ContactNo, Email, Address,
                                     EmergencyContactName, EmergencyContactNo, JoinDate, HealthConsent, ConsentDate)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $st->execute(member_params($d));
            $newId = (int)db()->lastInsertId();
            flash('ok', "Member added: {$d['FirstName']} {$d['LastName']} (ID $newId).");
            header('Location: index.php'); exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {      // UNIQUE (FirstName, LastName, DateOfBirth)
                $errors['_form'] = 'This member already exists (same first name, last name and date of birth). Use Edit or renew the existing record instead of adding a new one.';
            } else { $errors['_form'] = 'Could not save the member. Please try again.'; }
        }
    } 
}
require __DIR__ . '/includes/header.php';
?>
<div class="pagehead"><h2>Add Member</h2></div>
<?php if ($errors): ?><div class="msg err" role="alert"><?= isset($errors['_form']) ? h($errors['_form']) : 'Please fix the highlighted fields.' ?></div><?php endif; ?>
<?php $action = 'create.php'; $submitLabel = 'Save Member'; $isEdit = false; require __DIR__ . '/includes/form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
