<?php
setcookie('EPICPASS','',time() - 666, '/');
setcookie('EPICNAME','',time() - 666, '/');
header("Location: ../"); exit;
?>