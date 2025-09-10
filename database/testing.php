<?php
echo "Alice: " . password_hash("Password123!", PASSWORD_DEFAULT) . "\n";
echo "Bob: " . password_hash("Secret456!", PASSWORD_DEFAULT) . "\n";
?>