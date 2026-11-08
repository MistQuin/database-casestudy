<?php
session_start();
require_once __DIR__ . '/../config.php';

/** Character count that works with or without the mbstring extension. */
function str_len(string $s): int { return function_exists('mb_strlen') ? mb_strlen($s) : strlen(utf8_decode($s)); }

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function flash(string $type, string $msg): void { $_SESSION['flash'] = [$type, $msg]; }
function take_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

const MEMBER_FIELDS = ['FirstName','LastName','DateOfBirth','ContactNo','Email','Address',
                       'EmergencyContactName','EmergencyContactNo','JoinDate','HealthConsent','ConsentDate'];

/** Read the form values into a clean array. */
function member_input(array $post): array {
    $d = [];
    foreach (['FirstName','LastName','DateOfBirth','ContactNo','Email','Address',
              'EmergencyContactName','EmergencyContactNo','JoinDate','ConsentDate'] as $f) {
        $d[$f] = trim($post[$f] ?? '');
    }
    $d['HealthConsent'] = isset($post['HealthConsent']) ? 1 : 0;
    return $d;
}

function valid_date(string $s): bool {
    $dt = DateTime::createFromFormat('Y-m-d', $s);
    return $dt && $dt->format('Y-m-d') === $s;
}

/** Server-side checks. Returns [field => message]. */
function validate_member(array $d): array {
    $e = [];
    $name = '/^[\p{L} .\'-]+$/u';
    $phone = '/^(09\d{9}|\+639\d{9})$/';
    $today = date('Y-m-d');

    foreach (['FirstName' => 'First name', 'LastName' => 'Last name'] as $f => $label) {
        if ($d[$f] === '')                         $e[$f] = "$label is required.";
        elseif (str_len($d[$f]) > 50)            $e[$f] = "$label must be 50 characters or fewer.";
        elseif (!preg_match($name, $d[$f]))        $e[$f] = "$label may contain letters, spaces, . ' - only.";
    }
    if ($d['DateOfBirth'] === '')                  $e['DateOfBirth'] = 'Date of birth is required.';
    elseif (!valid_date($d['DateOfBirth']))        $e['DateOfBirth'] = 'Enter a valid date.';
    elseif ($d['DateOfBirth'] > $today)            $e['DateOfBirth'] = 'Date of birth cannot be in the future.';
    elseif ($d['DateOfBirth'] < '1900-01-01')      $e['DateOfBirth'] = 'Date of birth is not realistic.';

    if ($d['ContactNo'] === '')                    $e['ContactNo'] = 'Contact number is required.';
    elseif (!preg_match($phone, $d['ContactNo']))  $e['ContactNo'] = 'Use a PH mobile number: 09XXXXXXXXX or +639XXXXXXXXX.';

    if ($d['Email'] !== '' && (!filter_var($d['Email'], FILTER_VALIDATE_EMAIL) || str_len($d['Email']) > 100))
        $e['Email'] = 'Enter a valid email address (or leave it blank).';

    if ($d['Address'] === '')                      $e['Address'] = 'Address is required.';
    elseif (str_len($d['Address']) > 150)        $e['Address'] = 'Address must be 150 characters or fewer.';

    if ($d['EmergencyContactName'] === '')         $e['EmergencyContactName'] = 'Emergency contact name is required.';
    elseif (str_len($d['EmergencyContactName']) > 100) $e['EmergencyContactName'] = 'Name must be 100 characters or fewer.';

    if ($d['EmergencyContactNo'] === '')           $e['EmergencyContactNo'] = 'Emergency contact number is required.';
    elseif (!preg_match($phone, $d['EmergencyContactNo'])) $e['EmergencyContactNo'] = 'Use a PH mobile number: 09XXXXXXXXX or +639XXXXXXXXX.';
    elseif ($d['EmergencyContactNo'] === $d['ContactNo'])  $e['EmergencyContactNo'] = 'Must be different from the member\'s own number.';

    if ($d['JoinDate'] === '')                     $e['JoinDate'] = 'Join date is required.';
    elseif (!valid_date($d['JoinDate']))           $e['JoinDate'] = 'Enter a valid date.';
    elseif ($d['JoinDate'] > $today)               $e['JoinDate'] = 'Join date cannot be in the future.';

    if ($d['HealthConsent'] === 1) {
        if ($d['ConsentDate'] === '')              $e['ConsentDate'] = 'Consent date is required when consent is given.';
        elseif (!valid_date($d['ConsentDate']))    $e['ConsentDate'] = 'Enter a valid date.';
        elseif ($d['ConsentDate'] > $today)        $e['ConsentDate'] = 'Consent date cannot be in the future.';
    }
    return $e;
}

/** Values bound to the INSERT / UPDATE statements. */
function member_params(array $d): array {
    return [
        $d['FirstName'], $d['LastName'], $d['DateOfBirth'], $d['ContactNo'],
        $d['Email'] === '' ? null : $d['Email'],
        $d['Address'], $d['EmergencyContactName'], $d['EmergencyContactNo'],
        $d['JoinDate'], $d['HealthConsent'],
        ($d['HealthConsent'] === 1) ? $d['ConsentDate'] : null,
    ];
}

function find_member(int $id): ?array {
    $st = db()->prepare('SELECT * FROM Member WHERE MemberID = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}
