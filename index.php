<?php



$redirecturi = urlencode("http://localhost/oauth/b.php");
echo "无权访问，请使用中供账号<a href='http://localhost/oauth/Authorize.php?response_type=code&client_id=testclient&state=xyz&redirect_uri={$redirecturi}'>授权</a>";
