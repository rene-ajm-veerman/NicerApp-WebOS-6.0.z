<?php
if (!array_key_exists('s',$_GET)) die('403 - Forbidden - "s" URL parameter missing');
if ($_GET['s']!=='music') die('403 - Forbidden - "s" URL parameter incorrect');
?>
<html>
<body>
<script>
    document.location.href = 'https://nicer.app/music?pw='<?=$_GET['pw']?><?php if (array_key_exists('idxStart',$_GET)) echo '&idxStart='.$_GET['idxStart'];?>
</script>
</body>
</html>
