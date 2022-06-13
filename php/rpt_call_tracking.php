<?php 
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

$report_type = array (
			'call_tracking_overview' => 'Call Track by Agent',
			'call_tracking_overview2' => 'Call Track by Campaign',
			);
?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript">
var ShowCampaign = function(){
	var CampaignStatus = Ext.Cmp('cmp_status').getValue();
	// alert(CampaignStatus);
	Ext.Ajax({
			url: '../class/class.campaign.tracking.php',
			method: 'GET',
			param: {
				action: 'get_campaign_list',
				cmp_status: CampaignStatus
			}
		}).load("List_campaign");
};

var ShowHTML  = function ()
{
	var dialog = Ext.Window({
            url: '../report/index.html.cmptracking.php',
            name: 'WinRptCampTrack',
            param: {
                report_type: Ext.Cmp('report_type').getValue(),
                start_date: Ext.Cmp('start_date').getValue(),
                end_date: Ext.Cmp('end_date').getValue(),
                Campaign : Ext.Cmp('select_camp').getValue()
            }
        });
        dialog.newtab();
}

var ShowExcel=function ()
{
	alert('Sorry under constraction :(');
	// var dialog = Ext.Window({
            // url: '../report/index.excel.cmptracking.php',
            // name: 'WinRptCampTrack',
            // param: {
                // report_type: Ext.Cmp('report_type').getValue(),
                // start_date: Ext.Cmp('start_date').getValue(),
                // end_date: Ext.Cmp('end_date').getValue(),
                // Campaign : Ext.Cmp('select_camp').getValue()
            // }
        // });
        // dialog.newtab();
}
$(function(){
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Show HTML'],['Show EXCEL']],
		extMenu  :[['ShowHTML'],['ShowExcel']],
		extIcon  :[['zoom.png'],['zoom.png']],
		extText  :true,
		extInput :false,
		extOption: [{}]
	});
	$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
});
</script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Campaign Tracking</legend>	
	<div id="span_top_nav">
		<table cellpadding="4px">	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Interval</td>
					<td>
						<?php $jpForm->jpInput('start_date','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date','xx002');?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> * Report Type</td>
					<td><?php $jpForm->jpCombo('report_type','xx004', $report_type,NULL,'',0,0);?></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> *Campaign Status</td>
					<td><?php $jpForm->jpCombo('cmp_status','xx004', array(0=>"Not Active",1=>"Active"),NULL,'onchange="ShowCampaign()"',0,0);?></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> *Campaign Name</td>
					<td><span id="List_campaign"><?php $jpForm->jpCombo('cmp_name','xx004', array(),NULL);?></span></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">&nbsp;</td>
				</tr>
			</table>
	</div>
	<div id="toolbars" class="toolbars"></div>
	
</fieldset>