<?php

$urlCalled = $_SERVER['REQUEST_URI'];
$urlCalled = str_replace('login.index.php', '', $urlCalled);

?>
<script>
	window.location.href = "<?php echo $urlCalled; ?>";
</script>