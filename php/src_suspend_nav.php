<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
/** get Call status list **/
 
	function getCallStatus()
	{
		global $db;
		$sql = "select a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a 
				where a.CallReasonStatusFlag=1
				order by a.CallReasonId asc";
				
		$qry = $db->execute($sql,__FILE__,__LINE__);
		while( $res = $db->fetchrow($qry))
		{
			$datas[$res -> CallReasonId] = $res -> CallReasonDesc; 
		}
	  return "[".json_encode($datas)."]";
	}
	
		
/** get status closing ****/
	
	function getClsoingStatus()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonEvent =1 ";
		$qry = $db -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
			}
		}
		return implode(',',array_keys($datas));
	}	

/** set general query SQL ****/

	$sql = "SELECT 
			a.CustomerId, a.CustomerFirstName, e.Gender, a.CustomerDOB, c.CardTypeDesc
			FROM t_gn_customer a
			INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
			LEFT JOIN t_lk_gender e ON a.GenderId=e.GenderId
			LEFT JOIN t_lk_cardtype c ON a.CardTypeId=c.CardTypeId
			LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
			LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId ";
	
/** not valid page if not search **/

	$NavPages -> setPage(10);
	$NavPages -> IFpage('campaign_id');
	$NavPages -> query($sql);
	
 /** set filter **/
 
	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null
				 AND ( f.CallReasonCategoryId NOT IN(".getClsoingStatus().") OR f.CallReasonCategoryId is null)
				 AND b.AssignBlock=0 
				 and d.CampaignStatusFlag=1";
				 
/** custom filtering data **/
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 
	if( $db->havepost('cust_name')) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
		
	if( $db->havepost('gender')) 
		$filter.=" AND e.Gender = '".$db->escPost('gender')."'"; 
	
	if( $db->havepost('card_type')) 
		$filter.=" AND c.CardTypeDesc = '".$db->escPost('card_type')."'"; 
	
	if( $db -> havepost('call_status'))
		$filter.=" AND a.CallReasonId LIKE '%".$db->escPost('call_status')."%'"; 
	
	if( $db -> havepost('cust_fine_code'))
		$filter.=" AND a.NumberCIF LIKE '%".$db->escPost('cust_fine_code')."%'"; 
	
	if( isset($_SESSION['V_CMP']))
		$filter.=" AND d.CampaignId =".$_SESSION['V_CMP'];		
		
    $NavPages -> setWhere($filter);
	
?>

	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
  	<script type="text/javascript">
	
	
	/* create object **/
	 var Reason = <?php echo getCallStatus(); ?>;
	 var datas  = {}
	 
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	
	/* catch of requeet accep browser **/
	
		datas = 
		{
			cust_name 		: '<?php echo $db -> escPost('cust_name');?>',
			gender	 		: '<?php echo $db -> escPost('gender');?>',
			campaign_id 	: '<?php echo $db -> getSession('V_CMP');?>', 
			user_id 		: '<?php echo $db -> escPost('user_id');?>',
			cust_fine_code	: '<?php echo $db -> escPost('cust_fine_code');?>', //datas.cust_fine_code,
			call_status 	: '<?php echo $db -> escPost('call_status');?>', //datas.call_status
			order_by 		: '<?php echo $db -> escPost('order_by');?>',
			type	 		: '<?php echo $db -> escPost('type');?>'
		}
			
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'src_customer_nav.php',
			custlist:'src_customer_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
	/* creete object javaclass **/
	
		
		
		var defaultPanel = function()
		{
			doJava.File = '../class/class.src.customers.php' 
			
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action :'tpl_onready', 
					cust_name : datas.cust_name, 
					gender : datas.gender,
					campaign_id : datas.campaign_id,
					cust_fine_code: datas.cust_fine_code,
					call_status : datas.call_status
				}
				doJava.Load('span_top_nav');	
			}
		} 
		
		doJava.onReady(
			evt=function(){ 
			  defaultPanel();
			},
		  evt()
		)
		
		
	
	/* function searching customers **/
	
		var searchCustomer = function()
		{
			var cust_name 	 	= doJava.dom('cust_name').value;
			var gender	 	 	= doJava.dom('gender').value;
			var campaign_id  	= doJava.dom('campaign_id').value; 
			var cust_fine_code  = doJava.dom('cust_fine_code').value;
			var call_status   	= doJava.dom('call_status').value;	
				datas = {
					cust_name 	: cust_name,
					gender	 	: gender,
					campaign_id : campaign_id,
					cust_fine_code : cust_fine_code,
					call_status : call_status
				}
				
		    extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent()
		}
		
	/* function clear searching form **/	
		
		var resetSeacrh = function()
		{
			if( doJava.destroy() ){
				doJava.init = [['cust_name'],['gender'],['card_type']]
				doJava.setValue('');	
			}
		}
		
  
	
	
 /* go to call contact detail customers **/
 
		var gotoCallCustomer = function()
		{
			var arrCallRows  = doJava.checkedValue('chk_cust_call');
			var arrCountRows = arrCallRows.split(','); 
				if( arrCallRows!='')
				{	
					if( arrCountRows.length == 1 )
					{
						arrCallRows = arrCountRows[0].split('_'); 
						
						if( (arrCallRows[2]!='16') &&  (arrCallRows[2]!='17'))
						{
							class_active.NotActive(); 
							extendsJQuery.contactDetail(arrCallRows[0],arrCallRows[1])
						}
						else{
							alert('Please Select other status!');
							return false
						}
					}
					else{
						alert("Select One Customers !")
						return false;
					}
					
				}else{
					alert("No Customers Selected !");
					return false;
				}	
		}
		
	
	/* memanggil Jquery plug in */
	
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Go to Call '],['Clear']],
				extMenu  :[['searchCustomer'],['gotoCallCustomer'],['resetSeacrh']],
				extIcon  :[['zoom.png'],['telephone_go.png'],['cancel.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render : 4,
						type   : 'combo',
						header : 'Call Reason ',
						id     : 'v_result_customers', 	
						name   : 'v_result_customers',
						triger : '',
						store  : Reason
					}]
			});
			
			$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
		
	</script>
	
	
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-customers">&nbsp;&nbsp;Customer Search </legend>	
				<div id="span_top_nav"></div>
				<div id="toolbars"></div>
				<div id="customer_panel" class="box-shadow" style="background-color:#FFFFFF;">
					<div class="content_table" ></div>
					<div id="pager"></div>
				</div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	
	
	