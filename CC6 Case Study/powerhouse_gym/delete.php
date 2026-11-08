<?php
// DELETE: confirmation screen (GET) -> delete only after the user confirms (POST)
require __DIR__ . '/includes/functions.php';
$pageTitle = 'Delete Member';
$id = (int)($_GET['id'] ?? 0);
$m = $id > 0 ? find_member($id) : null;
if (!$m) { flash('err', 'Member not found. It may have been deleted already.'); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $st = db()->prepare('DELETE FROM Member WHERE MemberID = ?');
        $st->execute([$id]);
        flash('ok', "Member deleted: {$m['FirstName']} {$m['LastName']} (ID $id).");
    } catch (PDOException $ex) {
        flash('err', 'This member cannot be deleted because other records (memberships, payments, health notes) still refer to it.');
    }
    header('Location: index.php'); exit;
}
require __DIR__ . '/includes/header.php';
?>
<div class="pagehead"><h2>Delete Member</h2></div>
<div class="card confirm">
  <p class="warn">Are you sure you want to permanently delete this member? This cannot be undone.</p>
  <dl>
    <dt>Member ID</dt><dd><?= (int)$m['MemberID'] ?></dd>
    <dt>Name</dt><dd><?= h($m['FirstName'] . ' ' . $m['LastName']) ?></dd>
    <dt>Date of birth</dt><dd><?= h(date('M d, Y', strtotime($m['DateOfBirth']))) ?></dd>
    <dt>Contact no.</dt><dd><?= h($m['ContactNo']) ?></dd>
    <dt>Join date</dt><dd><?= h(date('M d, Y', strtotime($m['JoinDate']))) ?></dd>
  </dl>
  <form method="post" action="delete.php?id=<?= $id ?>" class="actions">
    <button class="btn danger" type="submit">Yes, delete member</button>
    <a class="btn ghost" href="index.php">No, go back</a>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
