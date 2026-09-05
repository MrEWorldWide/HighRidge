<?php
// age.php — minimal 21+ gate you can call like: age.php?next=/Menu.html

$cookieName   = '21plus';
$rememberDays = 1;
$next         = isset($_GET['next']) ? $_GET['next'] : '/';

// If already verified, skip straight through
if (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === '1') {
    header('Location: ' . $next);
    exit;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer']) && $_POST['answer'] === 'yes') {
        // Remember for X days
        setcookie($cookieName, '1', [
            'expires'  => time() + 60*60*24*$rememberDays,
            'path'     => '/',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: ' . $next);
        exit;
    } else {
        // Under 21 ? anywhere you prefer
        header('Location: https://www.responsibility.org/');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Age Verification</title>
<style>
  body{margin:0;min-height:100vh;display:grid;place-items:center;background:#111;color:#f5f5f5;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
  .card{width:min(520px,92vw);background:#1b1c20;border:1px solid #2a2c33;border-radius:16px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.35)}
  h1{margin:.2rem 0 0.5rem;font-size:1.35rem}
  p{color:#c9cbd3;line-height:1.5}
  .row{display:flex;gap:12px;margin-top:12px}
  button{flex:1;padding:12px 14px;border-radius:10px;border:1px solid #2f323a;background:#23252b;color:#fff;font-weight:600;cursor:pointer}
  button:hover{filter:brightness(1.12)}
  small{display:block;margin-top:10px;color:#a9acb6}
</style>
</head>
<body>
  <div class="card" role="dialog" aria-labelledby="t" aria-describedby="d">
    <h1 id="t">Are you 21 years of age or older?</h1>
    <p id="d">You must be 21+ to proceed.</p>

    <form method="post" class="row">
      <button type="submit" name="answer" value="yes">Yes, I am 21+</button>
      <button type="submit" name="answer" value="no">No</button>
    </form>

    <small>Lasts for 24 hours</small>
  </div>
</body>
</html>
