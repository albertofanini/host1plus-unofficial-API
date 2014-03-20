Host1Plus Unofficial API
========================

This project contains a PHP Class able to start, restart, stop or simply check the status of your VPS server hosted on Host1Plus.

Usage
=====

```PHP
include "host1plus.class.php";

$username = "mylogin@information";
$password = "mypassword";
$ovzp_ctid = "####"; // get this number on your client area

$h1p = new Host1Plus($username, $password); // login to your client area on Host1Plus

// choose one of the following:

$h1p->status($ovzp_ctid); // get vps status
$h1p->stop($ovzp_ctid); // shut down your vps
$h1p->start($ovzp_ctid); // start your vps
$h1p->reboot($ovzp_ctid); // restart your vps

$h1p->logout(); // recommended action
```

