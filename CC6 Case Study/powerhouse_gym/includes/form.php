<?php /* Shared Add/Edit form. Expects $d (values), $errors, $action, $submitLabel, $isEdit, $id */
function fld(string $name, string $label, array $d, array $errors, string $type = 'text', string $hint = '', bool $req = true): void { ?>
  <div class="field <?= isset($errors[$name]) ? 'bad' : '' ?>">
    <label for="<?= $name ?>"><?= h($label) ?><?= $req ? ' <b>*</b>' : ' <i>(optional)</i>' ?></label>
    <input type="<?= $type ?>" id="<?= $name ?>" name="<?= $name ?>" value="<?= h($d[$name] ?? '') ?>" <?= $type==='date' ? 'max="'.date('Y-m-d').'"' : '' ?>>
    <?php if ($hint): ?><small><?= h($hint) ?></small><?php endif; ?>
    <?php if (isset($errors[$name])): ?><span class="err"><?= h($errors[$name]) ?></span><?php endif; ?>
  </div>
<?php } ?>
<form method="post" action="<?= h($action) ?>" novalidate class="card form">
  <?php if (!empty($isEdit)): ?>
  <div class="field idbox"><label>Member ID</label>
    <input type="text" value="<?= (int)$id ?>" disabled><small>The ID is assigned by the database and cannot be changed.</small></div>
  <?php endif; ?>
  <h3>Personal details</h3>
  <div class="grid">
    <?php fld('FirstName','First name',$d,$errors); fld('LastName','Last name',$d,$errors);
          fld('DateOfBirth','Date of birth',$d,$errors,'date');
          fld('ContactNo','Contact no.',$d,$errors,'text','e.g. 09171234567');
          fld('Email','Email',$d,$errors,'email','',false); ?>
  </div>
  <?php fld('Address','Address',$d,$errors); ?>
  <h3>Emergency contact</h3>
  <div class="grid">
    <?php fld('EmergencyContactName','Name',$d,$errors); fld('EmergencyContactNo','Contact no.',$d,$errors,'text','e.g. 09181234567'); ?>
  </div>
  <h3>Membership &amp; consent</h3>
  <div class="grid">
    <?php fld('JoinDate','Join date',$d,$errors,'date'); ?>
    <div class="field">
      <label>Health-notes consent (RA 10173)</label>
      <label class="check"><input type="checkbox" name="HealthConsent" value="1" <?= !empty($d['HealthConsent']) ? 'checked' : '' ?>> Member gave written consent</label>
    </div>
    <?php fld('ConsentDate','Consent date',$d,$errors,'date','Required if consent is given',false); ?>
  </div>
  <div class="actions"><button class="btn" type="submit"><?= h($submitLabel) ?></button><a class="btn ghost" href="index.php">Cancel</a></div>
</form>
