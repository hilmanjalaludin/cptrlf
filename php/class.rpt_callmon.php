<?phprequire("../sisipan/sessions.php");require("../fungsi/global.php");require("../class/MYSQLConnect.php");require("../class/lib.form.php");class CallReport extends mysql{	var $JPForm;	function CallReport()	{		session_start();		parent::__construct();		self::index();	}			function index()	{		$this -> JPForm = new jpForm();				switch($_REQUEST['action'])		{			case 'get_user_am' 			: $this -> getManager(); 		  	break;			case 'get_user_spv' 		: $this -> getSupervisor(); 	 	break;			case 'get_user_tm' 			: $this -> getTelemarketer();   	break;			case 'get_user_campaign' 	: $this -> getCampaign();   		break;			case 'get_empty_filter'		: $this -> getEmptyFilter();   		break;			case 'get_user_spv_cmb'		: $this -> getSpvByAgent();   		break;		}	}		/* getEmptyFilter **/		function getEmptyFilter()	{		$this -> JPForm -> jpCombo('group_filter_select','xx002',array(),NULL,NULL);	}			/** function getCampaign **/		function getCampaign()	{				$sql = "select a.CampaignNumber, a.CampaignName from t_gn_campaign a  ";		$qry = $this -> query($sql);		foreach( $qry -> result_assoc() as $rows )		{			$datas[$rows['CampaignNumber']] = $rows['CampaignName'];				}				//$this -> JPForm -> jpMultiple('group_filter_select','xx001',$datas,NULL,NULL);		$this -> JPForm -> jpListcombo('group_filter_select', $label = 'Campaign Name',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);	}			/** get manager **/		function getManager()	{		if( $this -> getSession('handling_type')==1) {			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=2 and a.user_state=1 ";		}				if( $this -> getSession('handling_type')==2) {			$sql = "select a.UserId, a.full_name from tms_agent a 					where a.handling_type=2 and a.user_state=1 					and a.UserId='".$_SESSION['UserId']."'";		}				if( $this -> getSession('handling_type')==3) {			$sql = "select a.UserId, a.full_name from tms_agent a 					where a.handling_type=2 and a.user_state=1 					and a.mgr_id='".$_SESSION['mgr_id']."'";		}			 		if( $this -> getSession('handling_type')==5) {			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=2 and a.user_state=1 ";		}				$qry = $this -> query($sql);		foreach( $qry -> result_assoc() as $rows )		{			$datas[$rows['UserId']] = $rows['full_name'];				}			//	$this -> JPForm -> jpMultiple('group_filter_select','xx001',$datas,NULL,NULL);		$this -> JPForm -> jpListcombo('group_filter_select', $label = 'User Manager',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);	}				/** get manager **/		function getSpvByAgent()	{		if( $this -> getSession('handling_type')!='') {			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=3 and a.user_state=1 ";		}				$qry = $this -> query($sql);		foreach( $qry -> result_assoc() as $rows )		{			$datas[$rows['UserId']] = $rows['full_name'];				}		$this -> JPForm -> jpCombo('group_filter_select','xx004',$datas,NULL,'onchange="getFilterSupervisor(this);"');			}	/** get manager **/		function getSupervisor()	{		if( $this -> getSession('handling_type')==1) {			$sql = " select a.UserId, a.full_name from tms_agent a  where a.handling_type=3 and a.user_state=1";		}				if( $this -> getSession('handling_type')==2) {			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=3 and a.user_state=1 					 and a.mgr_id='".$_SESSION['UserId']."'";		}				if( $this -> getSession('handling_type')==3) {			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=3 and a.user_state=1 					 and a.UserId='".$_SESSION['UserId']."'";		}				$qry = $this -> query($sql);		foreach( $qry -> result_assoc() as $rows )		{			$datas[$rows['UserId']] = $rows['full_name'];				}				$this -> JPForm -> jpListcombo('group_filter_select', $label = 'User SPV',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);		//$this -> JPForm -> jpCombo('group_filter_select','xx004',$datas,NULL,'onchange="getFilterSupervisor(this);"');	}			/** function get getTelemarketer **/		function getTelemarketer()	{			if($this -> getSession('handling_type')==1){			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=4 and a.user_state=1";		}		else if($this -> getSession('handling_type')==2){			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=4 and a.user_state=1					 and a.mgr_id='".$_SESSION['UserId']."'";		}		else if($this -> getSession('handling_type')==1){			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=4 and a.user_state=1					 and a.spv_id='".$_SESSION['UserId']."'";		}		else{			$sql = " select a.UserId, a.full_name from tms_agent a 					 where a.handling_type=4 and a.user_state=1";		} 				if($this -> havepost('spv_id')) $sql.=" AND a.spv_id='".$_REQUEST['spv_id']."'";				$qry = $this -> query($sql);				foreach( $qry -> result_assoc() as $rows )		{			$datas[$rows['UserId']] = $rows['full_name'];				}		$this -> JPForm -> jpListcombo('list_user_tm', $label = 'User TM',$datas,$values = NULL, $js = NULL,$attr = false, $dis=0);	}}new CallReport();?>