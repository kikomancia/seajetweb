<?php
if (!empty($_POST['password'])) {
    echo password_hash($_POST['password'], PASSWORD_DEFAULT);
}
?>