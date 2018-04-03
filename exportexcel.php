<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ ?>
<?php
require_once("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}

$filename = BRAND." log ".date('m d Y').".xls"; // File Name
// Download file
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=$filename");  
header("Pragma: no-cache"); 
header("Expires: 0");

$chk = $dclass->select('*', 'tbl_access_log', ' AND parent_id='.$_SESSION['custid'].' AND status = 1 ');

?>
<table>
    <tr>
        <th>Name</th>
        <th>Date</th>
        <th>Time</th>
        <th>Ip Address</th>
        <th>Description</th>
    </tr>
    <?php 
    foreach($chk as $value){
    $agentname = $dclass->select('fname,lname', 'tblmember', ' AND intid='.$value['agent_id']);   
    

    ?>
    <tr>
        <td><?php echo $agentname[0]['fname'].' '.$agentname[0]['lname'] ?></td>
        <td><?php echo date('M d, Y',strtotime($value['date'])) ?></td>
        <td><?php echo date('H:i:s',strtotime($value['date'])) ?></td>
        <td><?php echo $value['ip_address'] ?></td>
        <td><?php echo $value['message'] ?></td>
    </tr>
    <?php } ?>
</table>
<?php 