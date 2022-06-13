<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.list.table.php");
	require(dirname(__FILE__)."/../class/class.query.parameter.php");
	require(dirname(__FILE__).'/../sisipan/parameters.php');
	
	
	SetNoCache();
	
	
/** get all status ***/

	function get_value_status()
	{
		$query = new ParameterQuery();
		if( is_object($query))
		{
			return $query -> ImplodeStatus();
		}
	}
	
/** get info data **/
	
	
	function getLastBySpv($CustomerId='',$spv_id=''){
		global $db;
		
		$V_DATAS = '-';
		
		if( $CustomerId !=''):
			$sql = "SELECT a.CallHistoryNotes, b.id, b.full_name  FROM t_gn_callhistory a 
					left join tms_agent b on a.CreatedById=b.UserId
						where a.CustomerId='".$CustomerId."'
						and b.handling_type NOT IN(4,5,2)
						order by a.CallHistoryId DESC LIMIT 1 ";
						
			$qry = $db ->execute($sql,__FILE__,__LINE__);	
			if( $qry && ($row=$db ->fetchrow($qry))){
				$V_DATAS = 'Re-Confirm ( '.$row->id.' )'; 
			}
			else{
				$sql2 = " select concat(a.id,' - ',a.full_name) as name from tms_agent a where a.UserId='".$spv_id."'";
				$res  = $db->valueSQL($sql2);
			}	if($res!='') $V_DATAS = $res;
		endif;	
		
		return $V_DATAS;
	}

	function getSpvName($spv_id=''){
		global $db;
		if( $spv_id!=''){
			$sql = " select concat(a.id,' - ',a.full_name) as name from tms_agent a where a.UserId='".$spv_id."'";
			$name = $db->valueSQL($sql);
			if( $name !=''){
				return '<span style="color:red;">'.$name.'</span>';
			}
			else return '-'; 	
		}
	}
	
	function getLastHistory($customerId=''){
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
			where a.CustomerId =".$customerId."
			order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
	$status = array(16,17);
	
 /** set properties pages records **/
	$ListPages -> setPage(10);
	$ListPages -> pages = $db -> escPost('v_page'); 
	//$ListPages -> IFpage('campaign_id');
	
	
 /** set  genral query SQL  **/
 
	$sql = "SELECT 
				a.CustomerId,  a.CallReasonId, c.CampaignName, a.CustomerFirstName, 
				a.CustomerLastName, a.QaProsess, a.QaProsessId, i.id, i.full_name,
				e.id as agentName, spv.id as `spv`, am.id as `am`,
				DATE(g.PolicySalesDate) as PolicySalesDate,
				IF(h.AproveName is null,'Request Confirm', h.AproveName) as  AproveName
			FROM t_gn_customer a 
				LEFT JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
				LEFT JOIN t_lk_callreason d on a.CallReasonId =d.CallReasonId
				LEFT JOIN tms_agent e on a.SellerId=e.UserId 
				LEFT JOIN t_gn_policyautogen f ON a.CustomerId=f.CustomerId
				LEFT JOIN t_gn_policy g ON f.PolicyNumber=g.PolicyNumber
				LEFT JOIN t_lk_aprove_status h on a.CallReasonQue=h.ApproveId 
				LEFT JOIN tms_agent i ON a.QaProsessId = i.UserId
				LEFT JOIN tms_agent spv ON b.AssignSpv = spv.UserId
				LEFT JOIN tms_agent am ON b.AssignMgr = am.UserId
				LEFT JOIN t_gn_productplan j ON g.ProductPlanId=j.ProductPlanId
				LEFT JOIN t_gn_product k ON j.ProductId=k.ProductId";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null  
				 AND a.CallReasonQue IN(7, 2, 13,".$db -> Entity -> VerifiedConfirm().")";
				 // AND ( a.CallReasonQue IS NULL OR a.CallReasonQue='".$db -> Entity -> VerifiedConfirm()."')";
				 //AND b.AssignBlock=0
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number') ) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
	

	if( $db->havepost('start_date') && $db->havepost('end_date') )
		$filter .= " AND date(a.CustomerUpdatedTs) >= '".$db->formatDateEng($_REQUEST['start_date'])."' 
					 AND date(a.CustomerUpdatedTs) <= '".$db->formatDateEng($_REQUEST['end_date'])."' "; 
	
	
    if( $db->havepost('call_result')){ 
		$filter .=" AND a.CallReasonId ='".$db->escPost('call_result')."'"; 
		//$filter .=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	if( $db->havepost('user_id') )
	$filter.=" AND a.SellerId =".$db->escPost('user_id');
	
	if( $db->havepost('user_spv') )
		$filter.=" AND b.AssignSpv =".$db->escPost('user_spv');

	if( $db->havepost('user_am') )
		$filter.=" AND b.AssignMgr =".$db->escPost('user_am');
		
	if( $db->havepost('cmp_status') )
	{
		$filter.=" AND c.CampaignStatusFlag =".$db->escPost('cmp_status');
	}
	else
	{
		$filter.=" AND c.CampaignStatusFlag =1";
	}
	
	if(!$db->havepost('call_result')){
		$filter.=" AND a.CallReasonId IN(".get_value_status().") ";
	}
	
	if( $db->havepost('ProductGroup') )
	{
		$filter.=" AND k.product_category_id =".$db->escPost('ProductGroup');
	}
	
	$ListPages -> setWhere($filter);
	
 /* order by ****/
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> GroupBy('a.CustomerId');
	$ListPages -> setLimit();
	// if( $db->getSession('handling_type')==9 ) 
		// echo $ListPages ->query;
	$ListPages -> result();
	
	SetNoCache();
	
	

?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:120px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" >&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cust_call');">#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.CampaignName');">Campaign Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');">Customer Name</span></th>     
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('h.AproveName');">Approve Status</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">Agent Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">SPV Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('e.full_name');">AM Name</span></th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;Status</th>
		<th nowrap class="custom-grid th-middle" style="text-align:left;">&nbsp;QA Processing</th>
		<th nowrap class="custom-grid th-lasted" style="text-align:left;">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.PolicySalesDate');">Days</span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$diff_days = $db -> Date -> get_date_diff(date('Y-m-d'),$row -> PolicySalesDate);
			$color= ($no%2!=0?'#FAFFF9':'#FFFFFF');
	?>
			<tr class="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first">
				<input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_call" name="chk_cust_call"></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><div style="width:100px;"><?php  echo $row -> CampaignName; ?></div></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" nowrap><span style="color:#b14a06;font-weight:bold;"><?php echo strtoupper($row -> AproveName); ?></span></td>
				<td class="content-middle" nowrap><?php echo $row -> agentName; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> spv; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> am; ?></td>
				<td class="content-middle" <?php echo($row -> QaProsess?'style="color:red;"':'style="color:green;"');?> nowrap><?php echo ($row -> QaProsess?'Processing ...':'IDLE'); ?></td>
				<td class="content-middle" <?php echo($row -> QaProsessId?'style="color:red;"':'style="color:green;"');?> nowrap><?php echo ($row -> QaProsessId?$row->id." - ".$row->full_name:" - "); ?></td>
				<td class="content-lasted" style="color:red;">+<?php echo $diff_days -> days();?></td>
			</tr>
</tbody>
	<?php
		//echo $row -> CustomerId;
		$no++;
		};
		// ::1
	?>
</table>


