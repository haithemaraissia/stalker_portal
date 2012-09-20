<?php

set_time_limit(120);

session_start();

ob_start();

include "./common.php";

$error = '';
$action_name = 'add';
$action_value = _('Add');

$db = new Database();

moderator_access();

if (@$_SESSION['login'] != 'alex' && @$_SESSION['login'] != 'duda' && @$_SESSION['login'] != 'azmus' && @$_SESSION['login'] != 'vitaxa' && !check_access()){
    exit;
}

foreach (@$_POST as $key => $value){
    $_POST[$key] = trim($value);
}
    
if (@$_POST['add']){
    $sql = 'insert into epg_setting (
                uri,
                id_prefix
                ) 
            values (
                "'.@$_POST['uri'].'",
                "'.@$_POST['id_prefix'].'"
            )';
    $db->executeQuery($sql);
    header("Location: epg_setting.php");
}

$id = @intval($_GET['id']);

if (!empty($id)){
    
    if (@$_POST['edit']){
        $sql = 'update epg_setting set uri="'.@$_POST['uri'].'", id_prefix="'.@$_POST['id_prefix'].'" where id='.intval($_GET['id']);
        $db->executeQuery($sql);
        header("Location: epg_setting.php");
    }elseif (@$_GET['del']){
        $sql = 'delete from epg_setting where id='.intval($_GET['id']);
        $db->executeQuery($sql);
        header("Location: epg_setting.php");
    }
}

if (@$_GET['edit'] && !empty($id)){
    $action_name = 'edit';
    $action_value = _('Save');
    $edit = $db->executeQuery('select * from epg_setting where id='.$id)->getAllValues();
    $edit = @$edit[0];
}

if (isset($_GET['update_epg'])){
    $epg = new Epg();
    
    if (isset($_GET['force'])){
        $force = true;
    }else{
        $force = false;
    }
    
    $error = $epg->updateEpg($force);
}

$settings = $db->executeQuery('select * from epg_setting')->getAllValues();

$debug = '<!--'.ob_get_contents().'-->';
ob_clean();
echo $debug;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">

body {
    font-family: Arial, Helvetica, sans-serif;
    font-weight: bold;
}
td {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
    text-decoration: none;
    color: #000000;
}
.list, .list td, .form{
    border-width: 1px;
    border-style: solid;
    border-color: #E5E5E5;
}
a{
	color:#0000FF;
	font-weight: bold;
	text-decoration:none;
}
a:link,a:visited {
	color:#5588FF;
	font-weight: bold;
}
a:hover{
	color:#0000FF;
	font-weight: bold;
	text-decoration:underline;
}
</style>
<title><?= _('EPG settings')?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
    <font size="5px" color="White"><b>&nbsp;<?= _('EPG settings')?>&nbsp;</b></font>
    </td>
</tr>
<tr>
    <td width="100%" align="left" valign="bottom">
        <a href="index.php"><< <?= _('Back')?></a> | <a href="?update_epg"><?= _('Update EPG')?></a> | <a href="?update_epg&force"><?= _('Force update EPG')?></a>
    </td>
</tr>
<tr>
    <td align="center">
    <font color="Red">
    <strong>
    <pre>
    <? echo $error?>
    </pre>
    </strong>
    </font>
    <br>
    <br>
    </td>
</tr>
<tr>
<td align="center">
    <table class='list' cellpadding='3' cellspacing='0'>
        <tr>
            <td>ID</td>
            <td>URI</td>
            <td><?= _('ID prefix')?></td>
            <td>ETag/MD5</td>
            <td><?= _('Updated')?></td>
            <td>&nbsp;</td>
        </tr>
        <? foreach ($settings as $setting){
                echo '<tr>';
                echo '<td>'.$setting['id'].'</td>';
                echo '<td>'.$setting['uri'].'</td>';
                echo '<td>'.$setting['id_prefix'].'</td>';
                echo '<td>'.$setting['etag'].'</td>';
                echo '<td>'.$setting['updated'].'</td>';
                echo '<td>';

                echo '<a href="?edit=1&id='.$setting['id'].'">edit</a>&nbsp;';
                echo '<a href="?del=1&id='.$setting['id'].'" onclick="if(confirm(\''._('Do you really want to delete this record?').'\')){return true}else{return false}">del</a>';
                echo '</td>';
                echo '</tr>';
           }?>
    </table>
</td>
</tr>
<tr>
    <td align="center">
<br>
<br>
        <form method="POST">
            <table class="form">
                <tr>
                    <td>URI</td>
                    <td><input type="text" name="uri" value="<?echo @$edit['uri']?>" size="50"/></td>
                </tr>
                <tr>
                    <td><?= _('ID prefix')?>:</td>
                    <td><input type="text" name="id_prefix" value="<?echo @$edit['id_prefix']?>" size=""/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="<? echo $action_name ?>" value="<? echo $action_value?>"/></td>
                </tr>
            </table>
        </form>
    </td>
</tr>
</table>