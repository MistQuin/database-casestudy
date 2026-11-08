<?php
// UPDATE: edit an existing member (MemberID comes from the URL and is never editable)
require __DIR__ . '/includes/functions.php';
$pageTitle = 'Edit Member';
$id = (int)($_GET['id'] ?? 0);
$row = $id > 0 ? find_member($id) : null;
if (!$row) { flash('err', 'Member not found.'); header('Location: index.php'); exit; }

$d = $row; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = member_input($_POST);
    $errors = validate_member($d);
    if (!$errors) {
        try {
            $st = db()->prepare(
                'UPDATE Member SET FirstName = ?, LastName = ?, DateOfBirth = ?, ContactNo = ?, Email = ?, Address = ?,
                        EmergencyContactName = ?, EmergencyContactNo = ?, JoinDate = ?, HealthConsent = ?, ConsentDate = ?
                 WHERE MemberID = ?');
            $st->execute(array_merge(member_params($d), [$id]));
            flash('ok', "Member updated: {$d['FirstName']} {$d['LastName']} (ID $id).");
            header('Location: index.php'); exit;
        } catch (PDOException $ex) {
            $errors['_form'] = $ex->getCode() === '23000'
                ? 'Another member already has the same first name, last name and date of birth.'
                : 'Could not update the member. Please try again.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="pagehead"><h2>Edit Member</h2></div>
<?php if ($errors): ?><div class="msg err" role="alert"><?= isset($errors['_form']) ? h($errors['_form']) : 'Please fix the highlighted fields.' ?></div><?php endif; ?>
<?php $action = 'edit.php?id=' . $id; $submitLabel = 'Update Member'; $isEdit = true; require __DIR__ . '/includes/form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
