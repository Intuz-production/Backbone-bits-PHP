<?php 
/*********************************************************************
The MIT License (MIT)

Copyright (c) 2018 Intuz

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ 

require("config/configuration.php");
if(!$gnrl->checkMemLogin()){
    $gnrl->redirectTo("login?msg=logfirst");
}

$member_id = $_SESSION['custid']; 
$info = new SplFileInfo($_REQUEST['image_path']);

if($info->getExtension() == 'mp4'){
    ?><video width="320" height="240" controls>
        <source src="<?php echo $_REQUEST['image_path'] ?>" type="video/mp4">
    </video><?php 
}
else {
    ?><img src="<?php echo $_REQUEST['image_path'] ?>" ><?php 
} 
?>