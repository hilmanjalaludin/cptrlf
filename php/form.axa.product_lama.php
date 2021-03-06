<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

 
/*
||/\/\/\/\/\/\/\/\/-----------------------------------------------
||/\/\/\/\/\/\/\/\/-----------------------------------------------
*/
 
/* 
 * @ package 	: clas AXA_Product
 * 
 * @ params		: extends mysql
 * @ render		: object
 */
 
 // NOTES : js diganti dulu sama js/Ext.AxaProduct_dep.js (abie)
 
class AXA_Product extends mysql
{

  var $_url; 
  var $_tem;
  var $_data;
  var $_ciputholder;
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt
 * @ return  : void
 */

private function _get_data_customer()
{
	$datas = $this -> Customer -> DataPolicy( $this -> escPost('customerid') ); // data customer 
	if( !is_array($datas) ) return null;
	else
	{
		return $datas['Customer'];
	}
}
private function _get_data_ciputholder(){
	$datar = $this -> Customer -> DataPolicy($this -> escPost('customerid'));
	if(!is_array($datar)) return null;
	else{
		return $datar['Customer'];
	}
}

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 private function _getCampaignId()
 {
	$_conds = 0;
	if($this -> havepost('campaignid')){
		$_conds = (int)$this -> escPost('campaignid');
	}
	
	return $_conds;
 }

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 private function _getCustomerId()
 {
	$_conds = 0;
	if($this -> havepost('customerid')){
		$_conds = (int)$this -> escPost('customerid');
	}
	
	return $_conds;
 }
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 
  
public function AXA_Product()
{
	parent::__construct();
	
	$this -> _url  =& application::get_instance(); /// Application();
	$this -> _tem  =& Themes::get_instance();  // Themes
	$this -> _data =& self::_get_data_customer(); // customer;
	$this -> _ciputholder =& self::_get_data_ciputholder(); // ciputholder;
	
	if(class_exists('Themes')) 
	{
		self::AXA_Header();
		self::AXA_Body();
	}
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
  
function _get_policy_number()
{
	$_CustomerId = $this -> escPost('customerid');
	
	$_conds = array();
	$_conds['new'] = 'New Policy';
	$sql = " select a.PolicyNumber, a.PolicyNumber 
				from t_gn_policy a 
				left join t_gn_insured b on a.PolicyId=b.InsuredId
				left join t_gn_policyautogen c on a.PolicyNumber=c.PolicyNumber
				where c.CustomerId='$_CustomerId'";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows ){
		$_conds[$rows['PolicyNumber']] = $rows['PolicyNumber'];
	}
	
	return $_conds;
}


 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function _get_member_of()
{
	return array
	(
		'1'=>'Self', /// dependent berdiri sendiri tidak terikat 
		'2'=>'Holder', // terikat dgn holder 
		'3'=>'Spouse' // terikat dgn spouse
	);
}	

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 public function AXA_Header()
 { 
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="utf-8" http-equiv="encoding">
<title>Create Policy </title>
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/policy.screen.css?time=<?php echo time();?>" />
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/themes/base/jquery.ui.autocomplete.css?time=<?php echo time();?>" />
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/themes/base/jquery.ui.menu.css?time=<?php echo time();?>" />
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css?time=<?php echo time();?>" />	
<!-- <script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script> --> 
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.8.2.js?time=<?php echo time();?>"></script>

<!-- <script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script> -->
<script src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.9.0/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js?time=<?php echo time();?>"></script>

<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2_dep.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.AxaProduct_dep.js?time=<?php echo time();?>"></script>

</head>
 <?php }
 
 /*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 public function AXA_Body() 
 {  ?>
	<body >
	<table border=0 width="90%" align="center" cellpadding="5px">	
			<!-- start : layout top -->
			<tr><td><?php self::AXA_Toper(); ?></td></tr>
			<!-- start : layout top -->
			<tr><td><?php self::AXA_Tabs(); ?></td></tr>	
			<!-- start : layout footer -->
			<tr><td><?php self::AXA_Footer();?></td></tr>	
		</table>
	</body>
	</html>
	<?php 
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Insured()
{ ?>

<form name="form_data_insured">
<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Insured</b>
	<?php $this -> DBForm -> jpCheck("CopyDataInsured",'Insured = Payer ',1,"onchange=Ext.DOM.CopyDataInsured(this);",0,1);?>
	</legend>	
<table border="0px" cellpadding="2px">
	<tr>
		<td class="header_table required">* Policy Number</td>
		<td><span id="policy_number"><?php $this -> DBForm -> jpCombo('InsuredPolicyNumber','select long',self::_get_policy_number(),'new',"onchange=Ext.DOM.LoadSamePlan(this);Ext.DOM.benefInsured();"); ?> </span></td>
		<td class="header_table">Payment Mode</td>
		<td><span id="pay_plan"><?php $this -> DBForm -> jpCombo('InsuredPayMode','select long', $this ->Customer -> Paymode( $this -> escPost('campaignid') ), null,"OnChange=getPremi(this);"); ?></span> </td>
	</tr>
	
	<tr>
		<td class="header_table required">* Group Premi</td>
		<td><span id="group_premi"><?php $this -> DBForm -> jpCombo('InsuredGroupPremi','select long', $this->Customer->PremiumGroup(),null, "onchange=Ext.DOM.ClearInsured();" ); ?> </span></td>
		<td class="header_table">Plan Type</td>
		<td><span id="plan_type"><?php $this -> DBForm -> jpCombo('InsuredPlanType','select long', $this -> Customer -> ProductPlan($this -> escPost('campaignid')), null, null ); ?></span> </td>
	</tr>
	
	<tr>
		<td class="header_table required">* ID Type</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredIdentificationTypeId','select long', $this->Customer->IndentificationId() ); ?> </td>
		<td class="header_table">Premi</td>
		<td><?php $this -> DBForm -> jpInput('InsuredPremi','input long',null, null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
	</tr>
	<tr>
		<td class="header_table required">* ID No</td>
		<td><?php $this -> DBForm -> jpInput('InsuredIdentificationNum','input long',null,'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
		<td colspan = 2 rowspan = 8><div id="benefit"></div></td>
	</tr>
	<tr>
		<td class="header_table sunah">Relation</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredRelationshipTypeId','select long', $this->Customer->RelationshipType(),79); ?></td>
		
	</tr>
	<tr>
		<td class="header_table sunah">Title</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredSalutationId','select long',$this->Customer->Salutation()); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">First Name</td>
		<td><?php $this -> DBForm -> jpInput('InsuredFirstName','input long',null,null); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Last Name</td>
		<td><?php $this -> DBForm -> jpInput('InsuredLastName','input long',null,null); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Gender</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredGenderId','select long',$this -> Customer -> Gender()); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">POB</td>
		<td><?php $this -> DBForm -> jpInput('InsuredPOB','input long suggestcity',null, null); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">DOB</td>
		<td><?php $this -> DBForm -> jpInput('InsuredDOB','input long date',null, null, 1); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Age</td>
		<td><?php $this -> DBForm -> jpInput('InsuredAge','input long',null, null, 1); ?></td>
	</tr>
</table>	
</fieldset>
</form>
<?php
}

private function BillAddress()
{
	$data = array(1=>'Home Address', 2=>'Office Address');
	return $data;
}
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Benefiecery() { ?>

<form name="form_data_benefiecery">
<?php
 for( $_benefiecery=1; $_benefiecery<=4; $_benefiecery++)
 {  ?>

 <fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Benefiecery <?php echo $_benefiecery; ?></b>
		<?php $this -> DBForm -> jpCheck("Benefeciery",null,$_benefiecery,"onclick=FormBenefiecery(this,". $_benefiecery .");");?>
	</legend>	
	
	<table cellpadding="5px"> 
		<tr>
			<td class="header_table">Relation</td>
			<td><?php $this -> DBForm -> jpCombo("BenefRelationshipTypeId_{$_benefiecery}",'select long',  $this -> Customer -> RelationshipType()); ?></td>
		</tr>
		<tr>
			<td class="header_table">Title</td>
			<td><?php $this -> DBForm -> jpCombo("BenefSalutationId_{$_benefiecery}",'select long', $this -> Customer -> Salutation()); ?></td>
		</tr>
		<tr>
			<td class="header_table required">* First Name</td>
			<td> <?php $this -> DBForm -> jpInput("BenefFirstName_{$_benefiecery}","input long",null,null); ?></td>
		</tr>
		<tr>
			<td class="header_table ">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("BenefLastName_{$_benefiecery}","input long",null,null); ?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("BenefGenderId_{$_benefiecery}",'select long', $this -> Customer -> Gender());?></td>
		</tr>
		<tr>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("BenefDOB_{$_benefiecery}","input long date");?></td>
		</tr>
		<tr>
			<td class="header_table required">* Percentage</td>
			<td><?php $this -> DBForm -> jpInput("BenefPercentage_{$_benefiecery}","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>&nbsp;<span class="wrap">( % )</span></td>
		</tr>
	</table>
  </fieldset><br>		
<?php }	 ?>		
</form>
<?php 
}				
 
private function getAddress()
{
	$sql = "select a.CustomerAddressLine1,a.CustomerAddressLine2,a.CustomerAddressLine3,a.CustomerAddressLine4 
			from t_gn_customer a where a.CustomerId='".$this -> escPost('customerid')."'";
	$qry = $this->query($sql);
	
	foreach($qry->result_assoc() as $row)
	{
		foreach($row as $anu)
		{
			$datas[] = $anu;
		}
	}
	
	return $datas[0]." ".$datas[1]." ".$datas[2]." ".$datas[3];
}
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Payers() 
{
	?>

	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Payer & information</b>
	</legend>
	<input type="hidden" id="PayerXsellbank" name="PayerXsellbank" value="" />
	<input type="hidden" id="PayerValidXsell" name="PayerValidXsell" value="" />
	<form name="form_data_payer" >
	<input type="hidden" id="isXsell" name="isXsell" value="" />
	<table width="100%" align="center" cellpadding="5px" border="0px">	
		<tr>
			<td class="header_table required">* Title</td>
			<td><?php $this -> DBForm -> jpCombo("PayerSalutationId",'select long', $this -> Customer -> Salutation());?></td>
			<td class="header_table required" nowrap>* First Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerFirstName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table" nowrap>Last Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerLastName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("PayerGenderId",'select long',  $this -> Customer -> Gender());?></td>
			<td class="header_table">POB</td>
			<td><?php $this -> DBForm -> jpInput("PayerPOB","input long suggestcity",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("PayerDOB","input long date");?><input type="hidden" name="PayerAge" id="PayerAge" value=""/></td>
		</tr>
		<tr>
			<td class="header_table required">Marital status</td>
			<td><?php $this -> DBForm -> jpCombo("PayerMaritalStatus","input long date", $this->Customer->getMaritallist());?></td>
			<td class="header_table required">Certificate status</td>
			<td><?php $this -> DBForm -> jpCombo("PayerCertificateStatus","input long date", $this->Customer->getCertificateList());?></td>
		</tr>
		<tr>
			<td class="header_table required">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("PayerIdentificationTypeId","select long", $this -> Customer -> IndentificationId() );?></td>
			<td class="header_table required" >* ID No</td>
			<td><?php $this -> DBForm -> jpInput("PayerIdentificationNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header_table">Billing Address</td>
			<td><?php $this -> DBForm -> jpCombo("PayerAddrType",'select long',  $this -> BillAddress());?></td>
		</tr>
		<tr>
			<td class="header_table">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("PayerMobilePhoneNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"',true);?> </td>
			<td class="header_table">Fax Phone</td>
			<td><?php $this -> DBForm -> jpInput("PayerFaxNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header_table">Address</td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine1","input",null,null,0,100);?></td>
			
		</tr>	
		<tr>
			<td class="header_table">Home Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerHomePhoneNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Email</td>
			<td><?php $this -> DBForm -> jpInput("PayerEmail", "input long");?></td>
			<td class="header_table"></td>
			<td> <?php $this -> DBForm -> jpInput("PayerAddressLine2","input",null,null,0,100);?></td>
		</tr>	
		<tr>
			<td class="header_table">Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerOfficePhoneNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Province</td>
			<td><?php $this -> DBForm -> jpCombo("PayerProvinceId", 'select long',$this -> Customer -> Province() );?></td>
			
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine3","input",null,null,0,100);?></td>
			
		</tr>	
		<tr>
			<td class="header_table">Mobile Phone 2</td>
			<td><?php $this -> DBForm -> jpInput("PayerMobilePhoneNum2","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"',true);?> </td>
			<td class="header_table">Payment Type</td>
			<td><?php $this -> DBForm -> jpCombo("PayerPaymentType", 'select long',$this -> Customer -> payment_method() );?></td>
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine4", "input",null,null,0,100);?>  </td>
			
		</tr>	
		<tr>
			<td class="header_table">Home Phone 2</td>
			<td><?php $this -> DBForm -> jpInput("PayerHomePhoneNum2","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td colspan="2">
				<?php // style=\"display:none;\"
					echo "<table width=\"95%\" align=\"center\" cellpadding=\"5px\" id=\"payment_cc_form\" style=\"display:none;\">
						<tr>
							<td class=\"header_table\" valign=\"top\">Card Number</td>
							<td valign=\"top\">".$this -> DBForm -> RTInput("PayerCreditCardNum", "input long",null,null,'',0,16)."</td>
							<td><span id=\"error_message_html\"><img src=\"../gambar/icon/delete.png\"></span></td>
						</tr>
						<tr>
							<td class=\"header_table\" nowrap>Expiration Date</td>
							<td >".$this -> DBForm -> RTInput("PayerCreditCardExpDate", "input small")."
							<span class=\"wrap\">&nbsp;(mm/yy)&nbsp;</span><span id=\"error_message_exp\"><img src=\"../gambar/icon/delete.png\"></span>
							</td>
						</tr>
						</table>
						<table width=\"90%\" align=\"center\" cellpadding=\"5px\" id=\"payment_saving_form\" style=\"display:none;\">
						<tr>
							<td class=\"header_table\" valign=\"top\">Saving Account</td>
							<td valign=\"top\">".$this -> DBForm -> RTInput("SavingAccount", "input long",null,null,'',0,16)."</td>
							<td><span id=\"error_message_html\"></span></td>
						</tr>
						</table>";
				?>
			</td>
			<td class="header_table">City</td>
			<td><?php $this -> DBForm -> jpInput("PayerCity","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?>  </td>
			
		</tr>	
		<tr>
			<td class="header_table">Office Phone 2</td>
			<td><?php $this -> DBForm -> jpInput("PayerOfficePhoneNum2", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Card Type</td>
			<td><div id="dyn_card_type"><?php $this -> DBForm -> jpCombo("CreditCardTypeId", 'select long',$this -> Customer -> CardType() );?></div></td>
			<td class="header_table">Zip</td>
			<td><?php $this -> DBForm -> jpInput("PayerZipCode","input long",null,null,0,5);?></td>
			
		</tr>
		<tr>
			<td class="header_table">Last Sell</td>
			<td><?php $this -> DBForm -> jpCheck("cbxDataPayer",'Payer = Xsell',null,"onchange=Ext.DOM.CopyDataPayer(this);",0,1);?> </td>
			<td colspan="2"><div id="xsellinfo">&nbsp;</div></td>
			<td class="header_table">Bank</td>
			<td><div id="dyn_bank"><?php $this -> DBForm -> jpCombo('PayersBankId', 'select long',$this -> Customer -> Bank());?></div></td>
		</tr>
	 </table>
	 </form>
	</fieldset> 
	
	<?php 
}

private function Ciputra_Holder() 
{
	?>

	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Polis Holder</b>
	</legend>
	<input type="hidden" id="PayerXsellbank" name="PayerXsellbank" value="" />
	<input type="hidden" id="PayerValidXsell" name="PayerValidXsell" value="" />
	<form name="form_data_holder" >
	<input type="hidden" id="isXsell" name="isXsell" value="" />
	<table width="100%" align="center" cellpadding="5px" border="0px">	
		<tr>
			<!-- t d class="header_table required">* Nomor SPAJ</td>
			<td><?#php $this -> DBForm -> jpInput("nomor_spaj","input long",null,'onkeyup="Ext.Set(this.id).IsString();"',1);?></t d -->
			<td class="header_table required" nowrap>* First Name</td>
			<td><?php $this -> DBForm -> jpInput("HolderFirstName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table" nowrap>Last Name</td>
			<td><?php $this -> DBForm -> jpInput("HolderLastName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table required">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("HolderGenderId",'select long',  $this -> Customer -> Gender());?></td>
		</tr>
		<tr>
			<td class="header_table">&nbsp;</td>
			<td>&nbsp;</td>
			<td class="header_table required">POB</td>
			<td><?php $this -> DBForm -> jpInput("HolderPOB","input long suggestcity",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table required">DOB</td>
			<td><?php $this -> DBForm -> jpInput("HolderDOB","input long date");?><input type="hidden" name="PayerAge" id="PayerAge" value=""/></td>
		</tr>
		<tr>
			<td class="header_table required">Pekerjaan Client</td>
			<?php //print_r($this->Customer->getIncomeList());?>
			<td><?php $this -> DBForm -> jpCombo("HolderPosition",'select long', $this->Customer->getOcupationList(), null, 0, 1);?></td>
			<td class="header_table required">Jabatan Client</td>
			<td><?php $this -> DBForm -> jpCombo("HolderOccupation","input long",$this->Customer->getJobPosList(),'onkeyup="Ext.Set(this.id).IsString();"',0, 1);?></td>
			<td class="header_table required">Income Setahun</td>
			<td><?php $this -> DBForm -> jpCombo("HolderIncome","input long", $this->Customer->getIncomeList(),'onkeyup="Ext.Set(this.id).IsNumber();"',0, 1);?></td>
		</tr>
		<tr>
			<td class="header_table required">Tempat Kerja</td>
			<td><?php $this -> DBForm -> jpInput("HolderCompany",'select long',  null,null, 1);?></td>
			<td class="header_table required">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("HolderMobilePhoneNum","input long suggestcity",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header_table required">Marital status</td>
			<td><?php $this -> DBForm -> jpCombo("HolderMaritalStatus","input long date", $this->Customer->getMaritallist());?></td>
		</tr>
		<tr>
			<td class="header_table required">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("HolderIdentificationTypeId","select long", $this -> Customer -> IndentificationId(),$value="",NULL,1);?></td>
			<td class="header_table required" >* ID No</td>
			<td><?php $this -> DBForm -> jpInput("HolderIdentificationNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"',1);?></td>
			<td class="header_table required">Hubungan dengan PH</td>
			<td><?php $this -> DBForm -> jpCombo("HolderRelationshipTypeId",'select long', $this->Customer->RelationshipType(),79);?></td>
		</tr>
		<tr>
			<td class="header_table required">Type Alamat</td>
			<td><?php $this -> DBForm -> jpCombo("HolderAddrType","input long",$this->Customer->TypeAlamat() );?> </td>
			<td class="header_table required">Alamat 1</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine1", "input long",null );?></td>
			<td class="header_table">Alamat 2</td>
			<td><?php $this -> DBForm -> jpInput("HolderAddressLine2", "input long",null );?></td>

	
		</tr>	
		<tr>
			<td class="header_table required">Province</td>
			<td><?php $this -> DBForm -> jpCombo("HolderProvinceId","input long",$this -> Customer -> Province(),null, true);?> </td>
			<td class="header_table required">Kota</td>
			<td><?php $this -> DBForm -> jpInput("HolderCity","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?>  </td>
			<td class="header_table">Email</td>
			<td><?php $this -> DBForm -> jpInput("HolderEmail", "input long");?></td>
		</tr>
		
		<tr>
			<td class="header_table required">Bank</td>
			<td><div id="dyn_bank"><?php $this -> DBForm -> jpCombo('HoldersBankId', 'select long',$this -> Customer -> Bank());?></div></td>
			<td class="header_table required">Cabang Bank</td>
			<td><?php $this -> DBForm -> jpInput("HolderBankBranch", "input long",null,'onkeyup="Ext.Set(this.id).IsString();"' );?></td>
			<td class="header_table required">Nomor Rekening</td>
			<td><?php $this -> DBForm -> jpInput("HolderCreditCardNum","input",null,null,0,100);?></td>
		</tr>
		<tr>
			<td class="header_table">Office Phone 2</td>
			<td><?php $this -> DBForm -> jpInput("HolderOfficePhoneNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header_table">Card Type</td>
			<td><div id="dyn_card_type"><?php $this -> DBForm -> jpCombo("HolderCreditCardTypeId", 'select long',$this -> Customer -> CardType() );?></div></td>
			<td class="header_table required">Zip</td>
			<td><?php $this -> DBForm -> jpInput("HolderZipCode","input long",null,null,0,5);?></td>
		</tr>
	 </table>
	 </form>
	</fieldset> 
	
	<?php 
}

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 
public function AXA_Tabs() 
{ ?>
<!-- start : layout content -->	
 <fieldset class="corner">
 <legend class="icon-customers">&nbsp;&nbsp;&nbsp;Policy </legend>
	<div id="tabs" class="corener">
		<ul>
			<li><a href="#tabs-5" id="PAYER">PAYER AND ADDRESS INFO</a></li>
			<li><a href="#tabs-2" id="INSURED">INSURED</a></li>
			<li><a href="#tabs-3" id="INSURED">HOLDER</a></li>
			<li><a href="#tabs-6" id="BENEFICIARY">BENEFICIARY</a></li>
			<li><a href="#tabs-7" id="TRANSACTION">TRANSACTION</a></li>
			<li><a href="#tabs-8" id="BENEFIT">BENEFIT</a></li>
			<li><a href="#tabs-9" id="SURVEY">SURVEY</a></li>
			<li><a href="#tabs-10" id="UNDERWRITING">UNDERWRITING</a></li>
			<li><a href="#tabs-11" id="PAYMENTINFO" >PAYMENT INFO</a></li>
		</ul>
		
		<div id="tabs-5" style="height:360px;overflow:auto;"><?php self::AXA_Payers();?></div>
		<div id="tabs-2" style="height:360px;overflow:auto;"><?php self::AXA_Insured();?></div>
		<div id="tabs-3" style="height:360px;overflow:auto;"><?php self::Ciputra_Holder();?></div>
		<div id="tabs-6" style="height:360px;overflow:auto;"><?php self::AXA_Benefiecery();?></div>
		<div id="tabs-7" style="height:360px;overflow:auto;"><?php self::AXA_Transaction();?></div>
		<div id="tabs-8" style="height:360px;overflow:auto;"><?php self::AXA_Benefit();?></div>
		<div id="tabs-9" style="height:360px;overflow:auto;"><?php self::AXA_Survey();?></div>
		<div id="tabs-10" style="height:360px;overflow:auto;"><?php self::AXA_Underwriting();?></div>
		<div id="tabs-11" style="height:360px;overflow:auto;"><?php self::AXA_Payment();?></div>
	</div>
 </fieldset>	
 <?php
 }
 
 function AXA_Underwriting()
 {
	?>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Underwriting </b></legend>
		<form name="form_uw">
		<span id="uw"></span>
		</form>
	</fieldset>	
	<?php
 }
 
 function AXA_Survey()
 {
	?>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Survey </b></legend>
		<form name="form_survey">
		<span id="survey"></span>
		</form>
	</fieldset>	
	<?php
 }
 
 function AXA_Benefit()
 {
	?>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Benefit </b></legend>
		<span id="Benefit"></span>
	</fieldset>	
	<?php
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
function AXA_Transaction()
{ ?>
	<fieldset class="corner" style="margin-left:-5px;">
		<legend class="icon-application ">&nbsp;&nbsp;<b> Transaction </b></legend>
		<span id="Transaction"></span>
	</fieldset>	
<?php  
}

function AXA_Payment(){
 ?>
	<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Payment Information</b>
	</legend>
	<form name="form_payment_ivr" >
	<table width="100%" align="left" cellpadding="5px" border="0px">
		<tr>
			<td class="header_table" width="20%" nowrap>IVR Payment Methode</td>
			<!--<td><?php $this -> DBForm -> jpHidden('hiddennocc','');?>&nbsp;&nbsp;<span id="error_messagecc_html"></span></td>-->
                        <!-- by japri -->
			<td width="20%">
				<?php $this -> DBForm -> jpCombo("IvrPayMethod", 'select long',$this -> Customer -> PaymentMethod(),"","onChange=Ext.DOM.loadIvrBank();");?> 	
				
			</td>
			<td width="20%">
				<div style="float:left;">
					<a href="javascript:void(0);" class="sbutton" onclick="Ext.DOM.splitintoivr();" style="margin:4px;"><span>&nbsp;Send To IVR</span></a>				
					<a href="javascript:void(0);" class="sbutton" onclick="Ext.DOM.check_digit_valid();" style="margin:4px;"><span>&nbsp;Check Digit</span></a>
				</div>
			</td>
                            <?php
                             
                            /*$this -> DBForm -> jpButton("splitintoivr",'IVR','Guide Customer','onclick="Ext.DOM.splitintoivr();"',0); */
                            ?>
                        
			<!-- <td><?php //$this -> DBForm -> jpButton("getcustomercc",'IVR','Change to IVR','onclick="Ext.DOM.getcustomercc();"',0);?></td> -->
			<!-- <td><?php //$this -> DBForm -> jpButton("check_digit",'IVR','Check Digit','onclick="Ext.DOM.check_digit_valid();"');?></td> -->
		</tr>
		<tr>
			<td class="header_table" width="20%" nowrap>IVR Bank</td>
			<td width="20%"><div id="ivr_bank"><?php $this -> DBForm -> jpCombo("IvrBankId", 'select long',$this -> Customer -> Bank()); ?></div></td>
		</tr>
		<tr>
			<td colspan = "3">
				<div id="ivr_list" style="height:200px;overflow:auto;"></div>
			</td>
		</tr>
	</table>
	</form>
	</fieldset>
 <?php
 }

 
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
  
public function AXA_Toper() 
 { ?>
	<fieldset class="corner" style="background:url('../gambar/pager_bg.png') left top;">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Product</legend>
		<form name="form_data_product">	
			<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo self::_getCustomerId(); ?>"/>
			<input type="hidden" name="CampaignId" id="CampaignId" value="<?php echo self::_getCampaignId(); ?>"/>
			<input type="hidden" name="categorycode" id="categorycode" value="" />
			
			<table cellpadding="5px" width="100%" align="center">
				<tr>
					<td class="header_table">Product</td>
					<td><?php $this -> DBForm -> jpCombo("ProductId","select long", $this -> Customer -> ProductId($_REQUEST['campaignid']),null,"onChange=getSplitProduct(this);");?></td>
					<td class="header_table">Sales Date</td>
					<td><?php $this -> DBForm -> jpInput("SalesDate","input long",$this -> formatDateId(date('Y-m-d')),null,1);?></td>
				</tr>
				<tr>
					<td class="header_table">Pecah Policy</td>
					<td><?php $this -> DBForm -> jpCombo("PecahPolicy","select long", array('0'=>'No','1'=>'Yes'),0,"onChange=Ext.DOM.PecahPolicy(this.value);",1);?></td>
					<td class="header_table">Efective Date</td>
					<td><?php $this -> DBForm -> jpInput("EfectiveDate","input long",$this -> formatDateId(date('Y-m-d')),null,1);?></td>
				</tr>
			</table>
	</form>
	</fieldset>
 <?php }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
public function AXA_Footer()
{ ?>
	<div style="float:right;">	
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:window.close('windowPolicy');" style="margin:4px;"><span>&nbsp;Exit</span></a> &nbsp;
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:SavePolis();" style="margin:4px;"><span>&nbsp;Save</span></a> &nbsp;
	</div>	
<?php }  

 }
 
 new AXA_Product();
 
 
?>

