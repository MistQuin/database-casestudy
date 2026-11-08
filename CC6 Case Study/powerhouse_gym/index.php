<?php
// READ: list, search, filter, sort
require __DIR__ . '/includes/functions.php';
$pageTitle = 'Members';

$sortable = ['MemberID' => 'ID', 'LastName' => 'Name', 'DateOfBirth' => 'Date of birth',
             'ContactNo' => 'Contact no.', 'JoinDate' => 'Join date'];
$q       = trim($_GET['q'] ?? '');
$consent = $_GET['consent'] ?? '';
$sort    = array_key_exists($_GET['sort'] ?? '', $sortable) ? $_GET['sort'] : 'MemberID';   // whitelist
$dir     = (($_GET['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';                           // whitelist

$sql = 'SELECT MemberID, FirstName, LastName, DateOfBirth, ContactNo, Email, JoinDate, HealthConsent FROM Member';
$where = []; $params = [];
if ($q !== '') {
    $where[] = '(FirstName LIKE ? OR LastName LIKE ? OR CONCAT(FirstName, " ", LastName) LIKE ? OR ContactNo LIKE ? OR Email LIKE ? OR MemberID = ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like, ctype_digit($q) ? (int)$q : 0);
}
if ($consent === '1' || $consent === '0') { $where[] = 'HealthConsent = ?'; $params[] = (int)$consent; }
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= " ORDER BY $sort $dir, MemberID ASC";          // $sort/$dir come only from the whitelist above

$st = db()->prepare($sql); $st->execute($params);
$members = $st->fetchAll();
$total = (int)db()->query('SELECT COUNT(*) FROM Member')->fetchColumn();

function sort_link(string $col, string $label, string $sort, string $dir): string {
    $next = ($sort === $col && $dir === 'ASC') ? 'desc' : 'asc';
    $qs = http_build_query(array_merge($_GET, ['sort' => $col, 'dir' => $next]));
    $arrow = $sort === $col ? ($dir === 'ASC' ? ' ▲' : ' ▼') : '';
    return '<a href="?' . h($qs) . '">' . h($label) . $arrow . '</a>';
}
require __DIR__ . '/includes/header.php';
?>
<div class="pagehead"><h2>Members</h2><span class="count">Showing <?= count($members) ?> of <?= $total ?></span></div>
<form class="card search" method="get">
  <input type="search" name="q" value="<?= h($q) ?>" placeholder="Search by name, contact no., email or ID">
  <select name="consent">
    <option value="">All consent status</option>
    <option value="1" <?= $consent==='1'?'selected':'' ?>>Consent given</option>
    <option value="0" <?= $consent==='0'?'selected':'' ?>>No consent</option>
  </select>
  <input type="hidden" name="sort" value="<?= h($sort) ?>"><input type="hidden" name="dir" value="<?= strtolower($dir) ?>">
  <button class="btn" type="submit">Search</button>
  <?php if ($q !== '' || $consent !== ''): ?><a class="btn ghost" href="index.php">Clear</a><?php endif; ?>
</form>

<div class="card tablewrap">
<table class="table">
  <thead><tr>
    <?php foreach ($sortable as $col => $label): ?><th><?= sort_link($col, $label, $sort, $dir) ?></th><?php endforeach; ?>
    <th>Email</th><th>Health consent</th><th>Actions</th>
  </tr></thead>
  <tbody>
  <?php if (!$members): ?>
    <tr><td colspan="8" class="empty">No members found<?= $q !== '' ? ' matching “' . h($q) . '”' : '' ?>.</td></tr>
  <?php endif; ?>
  <?php foreach ($members as $m): ?>
    <tr>
      <td><?= (int)$m['MemberID'] ?></td>
      <td><b><?= h($m['LastName']) ?></b>, <?= h($m['FirstName']) ?></td>
      <td><?= h(date('M d, Y', strtotime($m['DateOfBirth']))) ?></td>
      <td><?= h($m['ContactNo']) ?></td>
      <td><?= h(date('M d, Y', strtotime($m['JoinDate']))) ?></td>
      <td><?= $m['Email'] ? h($m['Email']) : '<span class="muted">—</span>' ?></td>
      <td><?= $m['HealthConsent'] ? '<span class="pill yes">Yes</span>' : '<span class="pill no">No</span>' ?></td>
      <td class="acts"><a href="edit.php?id=<?= (int)$m['MemberID'] ?>">Edit</a>
          <a class="del" href="delete.php?id=<?= (int)$m['MemberID'] ?>">Delete</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
