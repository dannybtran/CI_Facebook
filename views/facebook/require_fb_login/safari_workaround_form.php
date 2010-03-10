<html><body onload="document.getElementById('safari_fix').submit();">
<form id='safari_fix' method='post' action=''>
<? foreach($_REQUEST as $k => $v): ?>
<input type='hidden' name="<?=addslashes($k)?>" value="<?=addslashes($v)?>" />
<? endforeach; ?></form>
</body></html>