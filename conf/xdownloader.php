<?php
include("inc/controller.php");
$username = $session->username;
$pagename = 'xdownloader';
$container = '';
$version = 'Beta 1';
if(!$session->logged_in){
    header("Location: ".$configs->loginPage());
    exit;
}
else{

include ('inc/localize.php');
$version = $configs->getConfig('Version');
$adapter = $configs->getAdapterConfig('adapter');
$interface = $adapter;
error_reporting(E_ERROR);
include ('inc/process_menu.php');
include ('widgets/lang_select.php');
include ('widgets/sys_data.php');
include ('widgets/theme_select.php');

$uptobox_conf = 'inc/uptobox_conf.php';
$alldebrid_conf = 'inc/alldebrid_conf.php';

if (file_exists($uptobox_conf) && file_exists($alldebrid_conf)) {
    include ('inc/uptobox_conf.php');
    include ('inc/alldebrid_conf.php'); 
} elseif (!file_exists($uptobox_conf) && file_exists($alldebrid_conf)) {
    include ('inc/alldebrid_conf.php'); 
} elseif (file_exists($uptobox_conf) && !file_exists($alldebrid_conf)) {
    include ('inc/uptobox_conf.php');
} else {}

switch (intval($_GET['id'])) {
              /* reload services */
    case 88:
    $process = $_GET['xdownloader'];
      if  ($process == "delete_ub") {
        shell_exec("sudo rm inc/uptobox_conf.php");
        header('Location: xdownloader.php');
    }
    else if ($process == "delete_adb") {
        shell_exec("sudo rm inc/alldebrid_conf.php");
        header('Location: xdownloader.php');
    }
         break;
        }
    ### ALLDEBRID ###
    if(isset($_POST['add_adb_conf'])){
    $my_pseudo_adb_conf = $_POST['my_pseudo_adb'];
    $my_pass_adb_conf = $_POST['my_pass_adb'];
    $my_folder_adb_conf = $_POST['my_folder_adb'];

    $command_adb = '<?php $my_pseudo_adb_config = "'.$my_pseudo_adb_conf.'";$my_pass_adb_config = "'.$my_pass_adb_conf.'";$my_folder_adb_config = "'.$my_folder_adb_conf.'"; ?>';
    shell_exec("sudo echo '$command_adb' > inc/alldebrid_conf.php");
    header('Location: xdownloader.php');
    }
    if(isset($_POST['add_adb'])){

	$my_link_adb = $_POST['link_adb'];
	$api_alldebrid = 'https://api.alldebrid.com';
	$my_pseudo_adb = $my_pseudo_adb_config;
	$my_pass_adb = $my_pass_adb_config;
	$my_folder_adb = $_POST['my_folder_adb'];

	$token_user_adb = $api_alldebrid."/user/login?agent=mySoft&username=".$my_pseudo_adb."&password=".$my_pass_adb;
	$recup_token_user_adb = json_decode(file_get_contents($token_user_adb));

	$my_token_adb = $recup_token_user_adb->token;
	$generate_my_link_adb = $api_alldebrid."/link/unlock?agent=mySoft&token=".$my_token_adb."&link=".$my_link_adb;
	$my_link_debrid_adb = json_decode(file_get_contents($generate_my_link_adb));
	$link_debrid_adb = $my_link_debrid_adb->infos->link;
	$link_debrid_adb_ok = $my_link_debrid_adb->success;

    if($link_debrid_adb_ok == "true"){
	shell_exec("sudo wget -bqc -P ".$my_folder_adb." ".$link_debrid_adb);  
	$message_success_adb = '<span class="btn btn-success btn-sm mt5 confirmation" style="width:100%;">'.T('LINK_SUCCESS').'</span>'; 
	echo '<META http-equiv="refresh" content="3; URL=xdownloader.php">';
	}else{
    $message_erreur_adb = '<span class="btn btn-danger btn-sm mt5 confirmation" style="width:100%;">'.T('LINK_INVALID').'</span>';
    echo '<META http-equiv="refresh" content="3; URL=xdownloader.php">';
    }
    }

    ### UPTOBOX ###
    if(isset($_POST['add_ub_conf'])){
    $my_token_ub_conf = $_POST['my_token_ub'];
    $my_folder_ub_conf = $_POST['my_folder_ub'];

    $command_upbx = '<?php $my_token_ub_config = "'.$my_token_ub_conf.'";$my_folder_ub_config = "'.$my_folder_ub_conf.'"; ?>';
    shell_exec("sudo echo '$command_upbx' > inc/uptobox_conf.php");
    header('Location: xdownloader.php');
    }
    if(isset($_POST['add_ub'])){

	$my_link_ub = $_POST['link_ub'];
	$url_explode = explode("/", $my_link_ub);
	$api_uptobox = 'https://uptobox.com/api';
	$my_token_ub = $my_token_ub_config;
	$my_folder_ub = $_POST['my_folder_ub'];

	$token_waiting_ub = $api_uptobox."/link?token=".$my_token_ub."&id=".$url_explode[3];
	$my_token_waiting_ub = json_decode(file_get_contents($token_waiting_ub));
	$my_token_waiting_final_ub = $my_token_waiting_ub->data->waitingToken;
	$generate_my_link_ub = $api_uptobox."/link?token=".$my_token_ub."&id=".$url_explode[3]."&waitingToken=".$my_token_waiting_final_ub;
	$my_link_debrid_ub = json_decode(file_get_contents($generate_my_link_ub));

	$link_debrid_ub = $my_link_debrid_ub->data->dlLink;
	$link_debrid_ub_ok = $my_link_debrid_ub->message;

    if($link_debrid_ub_ok == "Success"){
	shell_exec("sudo wget -bqc -P ".$my_folder_ub." ".$link_debrid_ub);  
	$message_success_ub = '<span class="btn btn-success btn-sm mt5 confirmation" style="width:100%;">'.T('LINK_SUCCESS').'</span>'; 
	echo '<META http-equiv="refresh" content="3; URL=xdownloader.php">';
	}else{
    $message_erreur_ub = '<span class="btn btn-danger btn-sm mt5 confirmation" style="width:100%;">'.T('LINK_INVALID').'</span>';
    echo '<META http-equiv="refresh" content="3; URL=xdownloader.php">';
    }
    }
        ?>

</span>
<?php include ('dash_header.php'); ?>
<script id="source" language="javascript" type="text/javascript">
<?php include "js/plugins/ajaxDataCharts/dash_app_ajax.js"; ?>
</script>
<?php include ('dash_navigation.php'); ?>
<div class="mainpanel">
  <!-- Title Header -->
  <div class="page-header">
    <h2><?php echo T('XDOWNLOADER_DASH'); ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="index.php"><?php echo T('HOME') ?></a>
      </li>
      <li class="active">
        <?php echo T('XDOWNLOADER_DASH'); ?>
      </li>
    </ol>
  </div>
  <div class="contentpanel" style="padding-bottom:50px">
    <div class="row">
        <div class="col-sm-15 col-md-10 col-md-offset-1">
                <div class="panel panel-main panel-inverse">
                <div class="panel-heading"><h4 class="panel-title"><?php echo T('ALLDEBRID'); ?>
                <?php if (file_exists($alldebrid_conf)) { ?>
                	<button style="float:right;font-size: 8px;" onclick="location.href='?id=88&xdownloader=delete_adb'" class="btn btn-danger btn-sm confirmation"><?php echo T('DELETE_CONFIG');?></button>
                <?php }else{} ?>
                </h4>
                	</div>
                  <div class="panel-body">
                  <?php 
                  	if (!file_exists($alldebrid_conf)) {
					?>
					<form action="#" autocomplete="off" style="display: flex;" align="center" method="POST">
					<input class="form-control" name="my_pseudo_adb" placeholder="<?php echo T('MY_PSEUDO_ADB'); ?>" required/>
					<input class="form-control" name="my_pass_adb" placeholder="<?php echo T('MY_PASS_ADB'); ?>" required/>
					<input class="form-control" name="my_folder_adb" placeholder="<?php echo T('MY_FOLDER_ADB'); ?>" required/>
					<br>
                    <button style="margin-left: 10px;" name="add_adb_conf" class="btn btn-success"><?php echo T('SAVE');?></button>

                    </form>
					<?php
					} else {
					?>
					<form action="#" autocomplete="off" style="display: flex;" align="center" method="POST">
                  	
                    <input class="form-control" style="margin-left: 10px;margin-right: 10px;" name="link_adb" placeholder="<?php echo T('LINK_ADB'); ?>" required/>
                    <input class="form-control" name="my_folder_adb" value="<?php echo $my_folder_adb_config; ?>" required/>
                    <br>
                    <button style="margin-left: 10px;" name="add_adb" class="btn btn-success"><?php echo T('DOWNLOAD');?></button>
                  </form>
					<?php
					}

				if(!empty($message_success_adb)){
					echo $message_success_adb;
				}elseif(!empty($message_erreur_adb)){
					echo $message_erreur_adb;
				}
				//$link_hosts_icon = '<img src="https://s2.googleusercontent.com/s2/favicons?domain='.parse_url($my_link, PHP_URL_HOST).'">';

				//echo "Hébérgeur : ".$link_hosts_icon.$my_link_debrid->infos->host."<br>";
				//echo "Nom du fichier : ".$my_link_debrid->infos->filename."<br>";
				//echo "Lien : <a href='".$my_link_debrid->infos->link."'>".$my_link_debrid->infos->link."</a><br>";

				//shell_exec("sudo wget -bqc -P /home/user ".$link_debrid);

				?>
				
                  </div>
                </div>
                <div class="panel panel-main panel-inverse">
                <div class="panel-heading"><h4 class="panel-title"><?php echo T('UPTOBOX'); ?>
                <?php if (file_exists($uptobox_conf)) { ?>
                	<button style="float:right;font-size: 8px;" onclick="location.href='?id=88&xdownloader=delete_ub'" class="btn btn-danger btn-sm confirmation"><?php echo T('DELETE_CONFIG');?></button>
                <?php }else{} ?>
                </h4>
                	</div>
                  <div class="panel-body">
                  <?php 
                  	if (!file_exists($uptobox_conf)) {
					?>
					<form action="#" autocomplete="off" style="display: flex;" align="center" method="POST">
					<input class="form-control" name="my_token_ub" placeholder="<?php echo T('MY_TOKEN_UB'); ?>" required/>
					<input class="form-control" name="my_folder_ub" placeholder="<?php echo T('MY_FOLDER_UB'); ?>" required/>
					<br>
                    <button style="margin-left: 10px;" name="add_ub_conf" class="btn btn-success"><?php echo T('SAVE');?></button>

                    </form>
					<?php
					} else {
					?>
					<form action="#" autocomplete="off" style="display: flex;" align="center" method="POST">
                  	
                    <input class="form-control" style="margin-left: 10px;margin-right: 10px;" name="link_ub" placeholder="<?php echo T('LINK_UB'); ?>" required/>
                    <input class="form-control" name="my_folder_ub" value="<?php echo $my_folder_ub_config; ?>" required/>
                    <br>
                    <button style="margin-left: 10px;" name="add_ub" class="btn btn-success"><?php echo T('DOWNLOAD');?></button>
                  </form>
					<?php
					}

				if(!empty($message_success_ub)){
					echo $message_success_ub;
				}elseif(!empty($message_erreur_ub)){
					echo $message_erreur_ub;
				}

				$lien = 'https://api.1fichier.com/v1/download/get_token.cgi';
				$token = 'VRu=ITW1=_WYjcV6JtJ7Pq6EY5FDXJGm';
				$headers = array(
    'Content-Type: application/json',
    sprintf('Authorization: Bearer %s', $token)
  );
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $lien);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, rawurldecode(http_build_query(array(
    'url' => 'https://1fichier.com/?499b4540qb428bq0xlrc'
  ))));

				$json = json_decode(curl_exec($curl));
				var_dump($json);
				curl_close($curl);

				
				?>
				</div>
                </div>

          </div>
        </div>
        <!-- END Row -->
    </div><!-- contentpanel -->
</div><!-- mainpanel -->
</section>
<footer><span>Copyright &copy; <?php echo date("Y"); ?> <a href="https://quickbox.io/" target="_blank">QuickBox.IO</a> - <?php echo T('ALL_RIGHTS'); ?></span></footer>
<script src="lib/jquery-ui/jquery-ui.min.js"></script>
<script src="lib/jquery.ui.touch-punch.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.js"></script>
<script src="lib/jquery-toggles/toggles.js"></script>
<script src="lib/jquery.gritter/jquery.gritter.js"></script>
<script src="js/quick.min.js"></script>
<!-- Datatables JS - https://cdn.datatables.net/ -->
<script src="lib/select2/select2.js"></script>
<!-- Initialize Form Validation -->
</body>
</html>
<?php } ?>
