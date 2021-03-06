<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	$AgentStatus = array(0=> "Logout", 1=> "Ready", "Not Ready", "ACW", "Busy");	
	$extStatus   = array(4=>"Offhook", "Ringing", "Dialing", "Talking", "Held", 17=>"Reserved", 25 => "Idle");

	//print_r($_SESSION);
	//Get Sessions
	
	$username     	= $db -> getSession('username');
	$user_group   	= $db -> getSession('user_group');
	$handling_type	= $db -> getSession('handling_type');
	$action 		= $db -> escPost('action');
	$agentExt     	= $db -> getSession('agentExt');
	
	function getMgrId($uid){
		global $db;
		$sql = " select a.mgr_id from tms_agent a where a.UserId='".$db -> getSession('UserId')."'";
		return $db->valueSQL($sql);
	}
	
	function getCustName($customerid){
		global $db;
		$sql = " select a.CustomerFirstName from t_gn_customer a where a.CustomerId='".$customerid."'";
		return $db->valueSQL($sql);
	}
	
	function showList(){
		global $db;
		global $user_group;
		global $user_profile;
		global $AgentStatus;
		global $extStatus;
		global $agentExt;
		
		if($db -> getSession('handling_type')==1)
		{
			$sql = "SELECT a.userid, a.name, b.ext_number, b.status, b.ext_status, d.reason_desc as ReasonStatus,
							      b.ext_status_time, b.status_time, b.login_time,
							      unix_timestamp(now()) - unix_timestamp(b.ext_status_time) as ext_duration,
							      unix_timestamp(now()) - unix_timestamp(b.status_time) as stat_duration,
							      remote_number, data
							FROM cc_agent a 
								LEFT OUTER JOIN cc_agent_activity b ON a.id = b.agent
								LEFT OUTER JOIN tms_agent c ON a.userid = c.id
								LEFT JOIN cc_reasons d on b.status_reason=d.reasonid
							WHERE c.user_state='1'
							AND c.handling_type not in (1,9)";
			
		}
		else if($db -> getSession('handling_type')==9)
		{
			$sql = "SELECT a.userid, a.name, b.ext_number, b.status, b.ext_status, d.reason_desc as ReasonStatus,
							      b.ext_status_time, b.status_time, b.login_time,
							      unix_timestamp(now()) - unix_timestamp(b.ext_status_time) as ext_duration,
							      unix_timestamp(now()) - unix_timestamp(b.status_time) as stat_duration,
							      remote_number, data
							FROM cc_agent a 
								LEFT OUTER JOIN cc_agent_activity b ON a.id = b.agent
								LEFT OUTER JOIN tms_agent c ON a.userid = c.id
								LEFT JOIN cc_reasons d on b.status_reason=d.reasonid
							WHERE c.user_state='1'";
			
		}
		else if( $db -> getSession('handling_type')==2 )
		{		
			$sql = "SELECT a.userid, a.name, b.ext_number, b.status, b.ext_status, d.reason_desc as ReasonStatus,
			               b.ext_status_time, b.status_time, b.login_time,
			               unix_timestamp(now()) - unix_timestamp(b.ext_status_time) as ext_duration,
			               unix_timestamp(now()) - unix_timestamp(b.status_time) as stat_duration,
			               remote_number, data
		          FROM cc_agent a 
				  	   LEFT OUTER JOIN cc_agent_activity b ON a.id = b.agent
					   LEFT OUTER JOIN tms_agent c ON a.userid = c.id
					   LEFT JOIN cc_reasons d on b.status_reason=d.reasonid
		          WHERE c.user_state='1'
							AND c.mgr_id = ".getMgrId($db -> getSession('UserId'))."
							and c.UserId <> ".$db -> getSession('UserId');
		}
		else if( $db -> getSession('handling_type')==3)
		{		
			$sql = "SELECT a.userid, a.name, b.ext_number, b.status, b.ext_status, d.reason_desc as ReasonStatus,
			               b.ext_status_time, b.status_time, b.login_time,
			               unix_timestamp(now()) - unix_timestamp(b.ext_status_time) as ext_duration,
			               unix_timestamp(now()) - unix_timestamp(b.status_time) as stat_duration,
			               remote_number, data
		          FROM cc_agent a 
				  	   LEFT OUTER JOIN cc_agent_activity b ON a.id = b.agent
					   LEFT OUTER JOIN tms_agent c ON a.userid = c.id
					   LEFT JOIN cc_reasons d on b.status_reason=d.reasonid
		          WHERE  c.user_state='1' 
						 and c.spv_id = ".$db -> getSession('UserId')."
						and c.UserId <> ".$db -> getSession('UserId');	
		}
		// penambahan untuk QA by (@ray)
		else if( $db -> getSession('handling_type')==5)
		{		
			
			$sql = "SELECT a.userid, a.name, b.ext_number, b.status, b.ext_status, d.reason_desc as ReasonStatus,
							      b.ext_status_time, b.status_time, b.login_time,
							      unix_timestamp(now()) - unix_timestamp(b.ext_status_time) as ext_duration,
							      unix_timestamp(now()) - unix_timestamp(b.status_time) as stat_duration,
							      remote_number, data
							FROM cc_agent a 
								LEFT OUTER JOIN cc_agent_activity b ON a.id = b.agent
								LEFT OUTER JOIN tms_agent c ON a.userid = c.id
								LEFT JOIN cc_reasons d on b.status_reason=d.reasonid
							WHERE c.user_state='1'
							and c.UserId <> ".$db -> getSession('UserId');
			
		
						 
							
		}
		
		//echo $sql;
		
		$res = $db->execute($sql,__FILE__,__LINE__);
		?>
		
<table width="100%" class="custom-grid" cellspacing=0>
	<tr> 
		<th nowrap width="25"  class="custom-grid th-first">&nbsp;No.</th>
		<th nowrap width="180" class="custom-grid th-middle">&nbsp;Agent</th>
		<th nowrap width="30"  class="custom-grid th-middle">&nbsp;Ext</th>		
		<th nowrap width="100" class="custom-grid th-middle">&nbsp;Status</th>
		<th nowrap width="85"  class="custom-grid th-middle">&nbsp;Status Time</th>		
		<th nowrap width="180" class="custom-grid th-middle">&nbsp;Ext Status</th>
		<th nowrap width="120" class="custom-grid th-middle">&nbsp;Data</th>
		<th nowrap width="30"  class="custom-grid th-lasted">&nbsp;Spy</th>
</tr>

	<?php
	
		$seq_no = 1;
		while ($row = $db->fetchassoc($res)):
			$status		   = $row['status'];
			$stat_duration = $row['stat_duration'];
			$ext_duration  = $row['ext_duration'];
			$ext_stat  	   = $row['ext_status'];
			$remote	   	   = $row['remote_number'];
			$status_time   = $row['status_time'];
			$ext_number	   = $row['ext_number'];
			$ReasonStatus  = $row['ReasonStatus'];
			
			if($stat_duration<0): $stat_duration=0; endif;
			if($ext_duration<0) : $ext_duration =0; endif;
			
			if($ext_stat==7):
				$ext_stat_str = $extStatus[$row["ext_status"]]." with $remote (".toDuration($ext_duration).')';
				$data = getCustName($row['data']);
				$actionCmd= '<span onclick="spyAgent(\''.$agentExt.'\', \''.$ext_number.'\')" style="cursor:pointer;color:red;font-weight:bold;"> [ Spy ]</span><span onclick="coachAgent(\''.$agentExt.'\', \''.$ext_number.'\')" style="cursor:pointer;color:red;font-weight:bold;"> [ Coach ]</span>';
			else:
				$ext_stat_str = $extStatus[$row["ext_status"]];
				$data = '';
				$actionCmd  = "";
			endif;
			
			
			$status_str = $AgentStatus[$status];
			
			if($status==1): $status_str; endif;
			
			if($ReasonStatus!='' ):  $status_str .= " [ ".$ReasonStatus." ] "; endif;
		
			if($status == 0):
				$style = ' style="color: red;"';
				$ext_stat_str = "";
				$data = "";
				$status_time = "";
				
			else:
				$style = ' style="color: blue;"';
			endif;
			
			
		($seq_no%2==0?$bgcolor = "#f7f9fe":$bgcolor = "#FFFFFF");
	?>
	<tr class="onselect" bgcolor="<?php echo $bgcolor; ?>">
		<td nowrap class="content-first" >&nbsp;<?php echo $seq_no; ?></td>
		<td nowrap class="content-middle" <?php echo $style;?> >&nbsp;<?php echo $row["userid"]." - ".$row["name"]; ?></td>
		<td nowrap class="content-middle">&nbsp;<?php echo $row["ext_number"]; ?></td>
		<td nowrap class="content-middle" <?php echo $style;?> >&nbsp;<?php echo $status_str; ?></td>
		<td nowrap class="content-middle" <?php echo $style;?> >&nbsp;<?php echo ($status==1?toDuration($stat_duration):'00:00:00'); ?></td>
		<td nowrap class="content-middle" <?php echo $style;?> >&nbsp;<?php echo $ext_stat_str; ?></td>		
		<td nowrap class="content-middle">&nbsp;<?php echo $data; ?></td>
		<td nowrap class="content-lasted">&nbsp;<?php echo $actionCmd; ?></td>
	</tr>
	<?php
		 $seq_no++;
		endwhile;
	?>
	
</table>
<?php
	}
	
	if($action == 'getcontent'){
		showList();
		exit;
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link type="text/css" rel="stylesheet" href="../gaya/gaya_utama.css"/>
	<link type="text/css" href="../pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $Themes->V_UI_THEMES; ?>/ui.all.css" rel="stylesheet" />
	<link type="text/css" rel="stylesheet" href="../gaya/other.css" />	
	<link rel="stylesheet" type="text/css" href="<?php echo $app->basePath();?>gaya/custom.css"/>
	
</head>
<body>

<script type="text/javascript">
<!--
isMSIE = (navigator.appName=="Microsoft Internet Explorer");

function docGID(s) {
	return document.getElementById(s);
}

function GetXmlHttpObject() {
	var xmlHttp=null;
  try {
  	// Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
	}catch(e){
		//Internet Explorer
		try {
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch(e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}

/* get content from web and then put on targetID */
function getContent(param, targetID){
	if (isMSIE)
  	var httpreq = new ActiveXObject("Microsoft.XMLHTTP");
  else
    var httpreq = new XMLHttpRequest();

  httpreq.open('get', param);
  httpreq.onreadystatechange = function () {
		if ((httpreq.readyState == 4) && (httpreq.status == 200)) {
    	var response = httpreq.responseText;
      if ((response!="") && (response!="-")) {
      	docGID(targetID).innerHTML = response;
      }
    }
  };
  httpreq.send(null);
}

function hiddenAction(v_file, v_url_params) {	
	var xmlHttp = GetXmlHttpObject();
	
	if (xmlHttp==null) {
	  alert ("Browser does not support HTTP Request")
	  return;
	}
	
	var url = v_file+'?'+v_url_params
	
	url=url+"&sid="+Math.random()
	//alert(url)
	xmlHttp.onreadystatechange=function() {
		
	}
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

function onRefreshContent(){
	param = "mon_agent_activity_list.php?action=getcontent"
	getContent(param, "agentlist");
}


function coachAgent(srcExt, targetExt){
	if(confirm('Do you want to Listen ?')){
		hiddenAction('mon_agent_activity_action.php','action=spyw&src='+srcExt+'&target='+targetExt);
	}
}

function spyAgent(srcExt, targetExt){
	hiddenAction('mon_agent_activity_action.php','action=spy&src='+srcExt+'&target='+targetExt);
}


setInterval("onRefreshContent()", 1000);

-->
</script>	
	
<div class="content_table" id='agentlist' style="height:600px;overflow:auto;">&nbsp;
	<?php
	showList();
	?>
</div>
</body>
</html>
