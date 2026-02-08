<?php
session_start();
echo "<pre>";
echo "__FILE__ = " . __FILE__ . "\n\n";
echo "session_name = " . session_name() . "\n";
echo "session_id   = " . session_id() . "\n\n";

echo "COOKIE[" . session_name() . "] = ";
var_export($_COOKIE[session_name()] ?? null);
echo "\n\n";

echo "SESSION:\n";
print_r($_SESSION);
echo "</pre>";


