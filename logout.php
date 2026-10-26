<?php
// ========================================================
// Digital Memory Jar - Logout Handler
// ========================================================

session_start();
session_unset();
session_destroy();

header("Location: index.php?msg=logged_out");
exit;
