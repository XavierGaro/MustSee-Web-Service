<?php
$encoding = $this->data['encoding'];
header("Content-type: application/json;charset=$encoding");
echo $this->data['Data'];