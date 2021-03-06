<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.list.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
require(dirname(__FILE__)."/../class/lib.form.php");

$ListPages -> pages = $db -> escPost('v_page');
$ListPages -> setPage(15);

$sql = "SELECT a.`CampaignId`,a.CampaignName,IF(COUNT(*) = 1,0,COUNT(*)) AS datasize FROM t_gn_campaign a LEFT JOIN t_gn_customer ON a.`CampaignId` = t_gn_customer.`CampaignId`";

$filter = '';
if( $db->havepost('CampaingId')){

	$filter =" AND a.CampaignId = ".$db->escPost('CampaingId');
}

$ListPages -> query($sql);
$ListPages -> setWhere($filter);
//$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
$ListPages -> GroupBy('a.CampaignId');
$ListPages -> setLimit();
$ListPages -> result();
//$ListPages -> echo_query();

?>
<style>
	.wraptext{color:#000;text-align:justify;font-size:11px;width:200px;line-height:18px;border:0px solid #000;padding:2px;overflow:auto;}
	.wraptext:hover{color:blue;}
	.bold{font-weight:bold;color:#434152;}
	.number{text-align:right;padding-right:3px;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first " width="5%">&nbsp;#</th>	
		<th class="custom-grid th-middle" width="8%"  align="center">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignId');" title="Order ASC/DESC">Campaign Name</span></th>		
		<th class="custom-grid th-lasted" width="20%" align="left" nowrap>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CampaignTypeStatus');" title="Order ASC/DESC">DataSize</span></th>
		
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color; ?>">
				<td class="content-first" ><?php $jpForm -> jpCheck('CampaignId',NULL,$row ->CampaignId, NULL, NULL,0);?></td>
				<td class="content-middle" ><?php echo $row->CampaignName;?></td>				
				<td class="content-lasted" align="justify" nowrap><?php echo $row -> datasize ;?></td>
			</tr>	
</tbody>
	<?php
		};
	?>
</table>