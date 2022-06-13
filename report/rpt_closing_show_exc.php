<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$connect = new mysql();

	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$prodid = $_REQUEST['prodid'];
	$Campaign = $_REQUEST['Campaign'];
	$exported = $_REQUEST['exported'];
	$query = $_REQUEST['query'];
	$query_check = $_REQUEST['query_check'];
	$qcstatus = explode (',',$_REQUEST['QCStat']);
	$qcstatusname = $db->Entity->ReasonLabelQuality();
	$SelectQCStatus = array();
	
	if(count($qcstatus)>0){
		foreach($qcstatus as $ind=>$val)
		{
			$SelectQCStatus[$val] = $qcstatusname[$val];
			// echo $val;
		}
	}
	// print_r($SelectQCStatus);
	if ($prodid) {
		$productName = getProductName($prodid, $connect);
	}
	else{
		$productName = 'All active product';
	}
	function getProductName($prodid, $dbase)
	{
		$sql = "select pr.ProductName from t_gn_product pr
				where pr.ProductCode='$prodid' ";
		$te = $dbase->query($sql);
		$te2 = $te->result_object();

		return $te2[0]->ProductName;
	}
	
		// query for data policys
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as policy_ref,
				cst.NumberCIF AS prospect_id,
				prd.ProductCode AS product_id,
				cmp.CampaignNumber AS campaign_id,
				cmp.CampaignNumber as campaign_TBSS,
				DATE_FORMAT(cst.CustomerUpdatedTs, '%Y-%m-%d 00:00:00') as input,
				cst.CustomerUpdatedTs as effdt,
				'' as payer_cifno,
				s.Salutation as payer_title,
				py.PayerFirstName as payer_fname,
				py.PayerLastName as payer_lname,
				g.GenderShortCode as payer_sex,
				date_format(py.PayerDOB, '%Y-%m-%d %H:%i:%s') as payer_dob,
				if(py.payeraddrtype=1,'HA', 'OA') as addrtype,
				py.PayerAddressLine1 AS addr1,
				py.PayerAddressLine2 AS addr2,
				py.PayerAddressLine3 AS addr3,
				py.PayerAddressLine4 AS addr4,
				py.PayerCity as city,
				py.PayerZipCode as post,
				pv.ProvinceCode as province,
				py.PayerHomePhoneNum as phone,
				py.PayerFaxNum as faxphone,
				py.PayerEmail as email,
				ct.CPayment as pay_type,
				ct.CreditCardTypeCode as card_type,
				bk.BankName as bank,
				'' as branch,
				py.PayerCreditCardNum as acctnum,
				py.PayerCreditCardExpDate as ccexpdate,
				pm.PayModeCode as bill_freq,
				'' as question1,
				'' as question2,
				'' as question3,
				'' as question4,
				'' as question5,
				case prp.ProductPlan 
					when 1 then 'A'
					when 2 then 'B'
					when 3 then 'C'
					when 4 then 'D'
					when 5 then 'E'
					when 6 then 'F'
					when 7 then 'G'
					when 8 then 'H'
					when 9 then 'i'
					when 10 then 'J'
				end as benefit_level,
				round((if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi)),0) as premium,
				round(if(prt.ProductType='PA',plc.Premi, if(pm.PayModeCode='M', if(count(distinct ins.InsuredId)>1, 0.9, 1)*12*sum(plc.Premi),if(count(distinct ins.InsuredId)>1, 0.9, 1)*sum(plc.Premi))),0) as nbi,
				'N' as export,
				'' AS exportdate,
				'' as canceldate,
				date_format(cst.CustomerRejectedDate,'%Y-%m-%d') as callDate2,
				0 as paystatus,
				'' as paynotes,
				'' as payauthcode,
				'' as paytransdate,
				'' as payorderno,
				'' as payccnum,
				'' as paycvv,
				'' as payexpdate,
				'IDR' as paycurency,
				'' as paycardtype,
				id.IdentificationType as payer_idtype,
				'' as payer_personalid,
				py.PayerMobilePhoneNum as payer_mobilephone,
				py.PayerOfficePhoneNum as payer_officephone,
				'' as deliverydate,
				'' as seperate_policy,
				1 as 'status',
				'' as payer_occupationid,
				'' as payer_birthplace,
				'' as payer_religionid,
				0 as payer_income,
				'' as payer_position,
				'' as payer_company,
				agt.init_name as operid,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as atm_id,
				'' as tsm_id,
				'' as pcifnumber,
				'' as pcardtype,
				'' as prefnumber,
				'' as paccnumber,
				py.PayerFirstName as paccname,
				'' as pcardnumber,
				'' as record_id,
				cst.CustomerUpdatedTs as callDate,
				cst.CustomerHomePhoneNum2 as phone2,
				cst.CustomerMobilePhoneNum2 as payer_mobilephone2,
				cst.CustomerWorkPhoneNum2 as payer_officephone2
				
				FROM t_gn_customer AS cst
				inner join t_gn_insured ins on ins.CustomerId = cst.CustomerId
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				inner JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
				inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
				
				left join t_lk_salutation s on s.SalutationId=py.SalutationId
				left join t_lk_gender g on g.GenderId=py.GenderId
				left join t_lk_province pv on pv.ProvinceId=py.ProvinceId
				left join t_lk_paymenttype pt on pt.PaymentTypeId=py.PaymentTypeId
				left join t_lk_creditcardtype ct on ct.CreditCardTypeId=py.CreditCardTypeId
				left join t_lk_bank bk on bk.BankId=py.PayersBankId
				left join t_lk_paymode pm on pm.PayModeId=prp.PayModeId
				left join t_lk_identificationtype id on id.IdentificationTypeId=py.IdentificationTypeId
				inner join t_lk_producttype prt on prt.ProductTypeId=prd.ProductTypeId
				WHERE 1=1 "; 
		/****
		--- inner JOIN tms_agent AS tsm ON tsm.handling_type=1
		--- tsm.init_name
		***/
		if ($prodid) {
			$sql = $sql . " and prd.ProductCode = '$prodid' ";
		}
		if ($start_date) {
			$sql = $sql . " and date(cst.CustomerUpdatedTs) >= '$start_date' ";
		}
		if ($end_date) {
			$sql = $sql . " and date(cst.CustomerUpdatedTs) <= '$end_date' ";
		}
		if ($Campaign) {
			$sql = $sql . " and cmp.CampaignNumber in ($Campaign) ";
		}
		if ($exported) {
			$sql = $sql . " and pa.isExported = 1 ";
		}
		else
			$sql = $sql . " and pa.isExported = 0 ";

		if ($query_check) {
			$query = str_replace("\\", '', $query);
			$sql = $sql . " ". $query." ";
		}
		
		if(count($qcstatus)>0){
			//kondisi yang memperhatikan status insured
			/*if(count($qcstatus)==1 and $qcstatus[0]==1)
			{
				$filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."
					and ins.QCStatus=1";
			}
			elseif(count($qcstatus)==1 and $qcstatus[0]==2)
			{
				$filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."
					or (cst.CallReasonQue =1 and ins.QCStatus=2)";
			}
			else
			{
				if()
				{
				}
			}*/
			
			//kondisi hnya memperhatikan status insured yang approve
			if(count($qcstatus)==1)
			{
				if($qcstatus[0]==1)
				{
					$filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."
					and ins.QCStatus=1";
				}
				else
				{
					$filterQC = "AND cst.CallReasonQue =".$qcstatus[0];
				}
			}
			else
			{
				$filterQC = " AND (";
				foreach($qcstatus as $index=>$value){
					
					if ($value==1)
					{
						$array_filter[$value] = " (cst.CallReasonQue =".$value."
									 and ins.QCStatus=1) ";
					}
					else
					{
						$array_filter[$value] .= " cst.CallReasonQue =".$value." ";
					}
				}
				$filterQC .= implode(" OR ",$array_filter);
				$filterQC .=" ) ";
			}
		}

		$tail= $filterQC." group by pa.PolicyNumber
				order by pa.PolicyNumber";
		$sql = $sql . $tail;
		// echo $sql;
		$policyRestObj = $connect->query($sql);
                /*    
		$sqlsurvey = "SELECT  CONCAT(DATE_FORMAT(i.`PolicySalesDate`,'%d/%m/%y') , '-', pa.PolicyNumber) AS FormNumber,e.`Pros_Refnumber` AS refNumber,e.`CampaignId`,a.`customer_id`,d.`survey_quest_id`, d.`survey_question`,a.`answer_value` FROM t_gn_multians_survey a
						INNER JOIN t_gn_customer e ON e.`CustomerId` = a.`customer_id`
						INNER JOIN `t_gn_campaign` f ON f.`CampaignId` = e.`CampaignId`				
						INNER JOIN t_gn_insured h ON h.`CustomerId` = e.`CustomerId`
						INNER JOIN t_gn_policyautogen pa ON pa.CustomerId = e.CustomerId
						INNER JOIN t_gn_policy i ON i.`PolicyId` = h.`PolicyId`
						INNER JOIN t_gn_prod_survey b ON a.`prod_survey_id` = b.`prod_survey_id`
						INNER JOIN t_lk_survey c ON c.`survey_id` = b.`survey_id`
						INNER JOIN t_lk_question_survey d ON d.`survey_quest_id` = c.`survey_quest_id`
						WHERE 1 = 1 and a.`quest_have_ans` <> 0 ";
		*/
                $sqlsurvey = "SELECT  CONCAT(DATE_FORMAT(i.`PolicySalesDate`,'%d/%m/%y') , '-', pa.PolicyNumber) AS FormNumber,concat(h.InsuredFirstName, h.InsuredLastName) as InsuredName,i.PolicyId,e.`Pros_Refnumber` AS refNumber,e.`CampaignId`,a.`customer_id`,d.`survey_quest_id`, d.`survey_question`,a.`answer_value` FROM t_gn_multians_survey a
						INNER JOIN t_gn_customer e ON e.`CustomerId` = a.`customer_id`
						INNER JOIN `t_gn_campaign` f ON f.`CampaignId` = e.`CampaignId`
						INNER JOIN t_gn_insured h ON h.`CustomerId` = e.`CustomerId`
						INNER JOIN t_gn_policyautogen pa ON pa.CustomerId = e.CustomerId
						INNER JOIN t_gn_policy i ON i.`PolicyId` = h.`PolicyId`
						INNER JOIN t_gn_prod_survey b ON a.`prod_survey_id` = b.`prod_survey_id`
						inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
						INNER JOIN t_lk_survey c ON c.`survey_id` = b.`survey_id`
						INNER JOIN t_lk_question_survey d ON d.`survey_quest_id` = c.`survey_quest_id`
						WHERE 1 = 1 and a.`quest_have_ans` <> 0 ";
                
		if ($Campaign){
			$sqlsurvey .= " and f.`CampaignNumber` in (".$Campaign.")";
		}
		
		if(count($qcstatus)>0){
			//kondisi yang memperhatikan status insured
			/*if(count($qcstatus)==1 and $qcstatus[0]==1)
			 {
			 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."
			 and ins.QCStatus=1";
			 }
			 elseif(count($qcstatus)==1 and $qcstatus[0]==2)
			 {
			 $filterQC = "AND cst.CallReasonQue =".$qcstatus[0]."
			 or (cst.CallReasonQue =1 and ins.QCStatus=2)";
			 }
			 else
			 {
			 if()
			 {
			 }
			 }*/
				
			//kondisi hnya memperhatikan status insured yang approve
			if(count($qcstatus)==1)
			{
				if($qcstatus[0]==1)
				{
					$filterQC = "AND e.CallReasonQue =".$qcstatus[0]."
					and h.QCStatus=1";
				}
				else
				{
					$filterQC = "AND e.CallReasonQue =".$qcstatus[0];
				}
			}
			else
			{
				$filterQC = " AND (";
				foreach($qcstatus as $index=>$value){
						
					if ($value==1)
					{
						$array_filter1[$value] = " (e.CallReasonQue =".$value."
									 and h.QCStatus=1) ";
					}
					else
					{
						$array_filter1[$value] .= " e.CallReasonQue =".$value." ";
					}
				}
				$filterQC .= implode(" OR ",$array_filter1);
				$filterQC .=" ) ";
			}
		}
		
		$ssurvey = $filterQC." order by pa.PolicyNumber";  
		
		// query for data insured
		$insured ="
				SELECT DISTINCT 
				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber) 
			 	AS FormNumber,
				concat(ins.InsuredFirstName, ins.InsuredLastName) as InsuredName,
				date_format(ins.InsuredDOB,'%m/%d/%Y') as InsuredDOB,
				g.GenderShortCode as sex,
				py.PayerAddressLine1 as Address1,
				py.PayerAddressLine2 as Address2,
				py.PayerCity as City,
				cst.pros_Refnumber AS Refnumber,
				py.PayerZipCode as ZipCode,
				py.PayerHomePhoneNum as Phone1,
				py.PayerOfficePhoneNum as Phone2,
				'' as EXTPhone2,
				py.PayerMobilePhoneNum as MobilePhone,
				'' as MaritalStatus,
				'' as Children,
				'' as FACode,
				date_format(plc.PolicySalesDate, '%m/%d/%Y') as Activation_Date,
				'' as BranchCode,
				'' as CSName,
				ins.InsuredFirstName as ACCHolderName,
				'' as ACCNumber,				
				'' as ACCBranch,
				'' as Program,
				py.PayerEmail as Email,
				'' as Asken,
				'' as Region,
				'' as NamaLG,
				'' as IDStaffBank,
				agt.init_name as operid,
				agt.id as sellerid,
				agt.spv_id as spv_id,
				agt.mgr_id as am_id,
				'' as tsm_id,
				date_format(date_add(plc.PolicySalesDate,interval + 3 month), '%m/%d/%Y %h:%i:%s ')  as EXPDate,
				'' as EXPCard,
				prd.ProductCode AS product_id,
				'3' as Periode,
				round(plc.Premi,0) as PremiumAmount,
				cst.Remark_1,
				cst.Remark_2,
				cst.Remark_3,
				cst.Remark_4,
				cst.Remark_5								
				FROM t_gn_customer AS cst
				inner join t_gn_insured ins on ins.customerid=cst.customerid
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				
				
				left join t_lk_salutation s on s.SalutationId=ins.SalutationId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId
				WHERE 1 = 1
		";
		/* inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
		-- inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
		-- inner JOIN tms_agent AS tsm ON tsm.UserId=430
		*/
		$insured_tail =	"ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";

		// query for data beneficiary
		$benf ="
		SELECT DISTINCT
				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber) 
			 	AS FormNumber,
				plc.PolicyId AS PolicyId,
				concat(bnf.BeneficiaryFirstName, bnf.BeneficiaryLastName) as Name,
				date_format(bnf.BeneficiaryDOB,'%m/%d/%Y') as DOB,
				g.GenderShortCode as Gender,
				r.BeneficiaryAliasCode as Relation,
				bnf.BeneficieryPercentage as Percentage

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner join t_gn_beneficiary bnf on bnf.CustomerId=cst.CustomerId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=bnf.RelationshipTypeId
				WHERE 1 = 1
				";

				
		$tail_benf= " group by bnf.BeneficiaryId
				ORDER BY pa.PolicyNumber, bnf.BeneficiaryFirstName ";
	
		
		SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: #3366FF;"> &nbsp;&nbsp;&nbsp;Extract Data &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: red;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Input Date : $start_date To $end_date</th>";
		echo "</br><th> &nbsp;&nbsp;&nbsp;Product id : $prodid</th>";
		echo "</br><th> &nbsp;&nbsp;&nbsp;Product Name: $productName</th>";
		echo "</br><th> &nbsp;&nbsp;&nbsp;QC Status: ".( (count($SelectQCStatus)>0)?implode(", ",$SelectQCStatus):"All")."</th>";
		echo "</br>";
		echo "</br>";
?>
</legend>
<!--
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<tr><th>[Policy]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_ref</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;prospect_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;product_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_TBSS</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;input</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;effdt</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_cifno</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_title</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_fname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_lname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_dob</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;addrtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;addr1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;addr2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;addr3</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;addr4</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;city</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;post</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;province</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;phone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;faxphone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;email</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;pay_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;card_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bank</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;branch</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;acctnum</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ccexpdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bill_freq</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question3</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question4</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question5</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;benefit_level</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;premium</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;nbi</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;export</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;exportdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;canceldate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;callDate2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paystatus</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paynotes</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payauthcode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paytransdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payorderno</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payccnum</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paycvv</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payexpdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paycurency</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paycardtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_idtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_personalid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_mobilephone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_officephone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;deliverydate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;seperate_policy</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;status</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_occupationid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_birthplace</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_religionid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_income</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_position</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_company</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;operid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sellerid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;spv_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;atm_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;tsm_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;pcifnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;pcardtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;prefnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paccnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;paccname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;pcardnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;record_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;calldate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;phone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_mobilephone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payer_officephone2</th>
	</tr>
	</div>
</thead>
-->
<!-- </fieldset> -->
	
<!--
<tbody>
	<?php
		$no = 1;
		$policy_array = '';
		foreach ($policyRestObj ->result_object() as $key => $row) {
			$policy_array = $policy_array .','. "'".$row ->policy_id."'";
	?>		
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_ref; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->prospect_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->campaign_TBSS; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->input; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->effdt; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_cifno; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_title; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_fname; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_lname; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_sex; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_dob; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addrtype; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr1; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr2; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr3; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->addr4; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->city; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->post; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->province; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->phone; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->faxphone; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->email; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->pay_type; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->card_type; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bank; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->branch; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->acctnum; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ccexpdate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->bill_freq; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question1; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question2; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question3; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question4; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->question5; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->benefit_level; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->premium; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->nbi; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->export; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->exportdate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->canceldate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->callDate2; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paystatus; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paynotes; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payauthcode; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paytransdate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payorderno; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payccnum; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycvv; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payexpdate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycurency; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paycardtype; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_idtype; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_personalid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_mobilephone; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_officephone; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->deliverydate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->seperate_policy; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->status; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_occupationid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_birthplace; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_religionid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_income; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_position; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_company; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sellerid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->operid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->spv_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->atm_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->tsm_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->pcifnumber; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->pcardtype; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->prefnumber; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paccnumber; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->paccname; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->pcardnumber; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->record_id; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->calldate; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->phone2; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_mobilephone2; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->payer_officephone2; ?></td>
			</tr>
		</div>
</tbody>
	<?php
		$no++;
		};
		$policy_array = substr($policy_array,1);
	?>
</table>
-->

</fieldset>

<fieldset class="corner">
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<!-- <table width="99%" border="0" align="center"> -->
	<tr><th>[Insured]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;formnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;insuredname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;insureddob</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;address1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;address2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;city</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;rtdanrw</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;zipcode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;phone1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;phone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;extphone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;mobilephone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;maritalstatus</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;children</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;facode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;activationdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;branchcode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;csname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;accholdername</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;accnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;accbranch</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;program</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;email</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;asken</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;region</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;namalg</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;idstaffbank</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;operid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sellerid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SPVid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;AMid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;TSMid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;productid</th>
                <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;periode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;expdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;expcard</th>		
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;premiumamount</th>

	</tr>
	</div>
</thead>
</fieldset>	

<tbody>
	<?php
		$policy_cond = $policy_array ? " and pa.PolicyNumber in (". $policy_array .") " : ' and 1 = -1 ';
		$insured_query = $insured . $policy_cond . $insured_tail;
		// echo "<br />";
		// echo $insured_query;
		$insuredRestObj = $connect->query($insured_query);
		$sqlsurvey .= $policy_cond. $ssurvey;
		
		$policysurvey = $connect->query($sqlsurvey);
		$no = 1;
		$pol_id='';
		$holder_id=0;
		foreach ($insuredRestObj ->result_object() as $key => $row) {
                    
                    if($row->sex == 'M'){
                        $sex = "BAPAK";
                    }  else {
                        $sex = "IBU";
                    }
                    if($row->EXTPhone2 == ""){
                        $extphone = "?";
                    }  else {
                        $extphone = strtoupper($row->EXTPhone2);
                    }
                    
                    if($row->Children == ""){
                        $children = "?";
                    }  else {
                        $children = strtoupper($row->Children);
                    }
                    if($row ->BranchCode == ""){
                        $branchcode = "?";
                    }  else {
                        $branchcode = strtoupper($row ->BranchCode);
                    }
                    if($row ->CSName == ""){
                        $csname = "?";
                    }  else {
                        $csname = strtoupper($row ->CSName);
                    }
                    if($row ->Program == ""){
                        $program = "?";
                    }else{
                        $program = strtoupper($row ->Program);
                    }
                    if($row ->Asken == ""){
                        $asken = "?";
                    }else{
                        $asken = strtoupper($row ->Asken);
                    }
                    if($row->Region==''){

                            $region='?';

                    }else{

                            $region=strtoupper($row->Region);

                    }

                    if($row ->NamaLG==''){

                            $namalg ='?';

                    }else{

                            $namalg = strtoupper($row->NamaLG);

                    }

                    if($row ->IDStaffBank ==''){

                            $idstaffbank = '?';

                    }else{

                            $idstaffbank = strtoupper($row ->IDStaffBank);

                    }
                        if ($row ->policy_id != $pol_id) {
				$pol_id=$row ->policy_id;
				$holder_id=0;
			}
			$holder_id++;
	?>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		<tr>
			<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->FormNumber; ?></td>
<!--			<td nowrap style="text-align: center" class="content-middle"><?php echo $holder_id; ?></td>-->
                        <td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->InsuredName); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InsuredDOB; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $sex; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->Address1); ?></td>
                        <td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->Address2); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->City); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->Refnumber); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->ZipCode); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Phone1; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Phone2; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $extphone; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MobilePhone; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->MaritalStatus); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $children; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->FACode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Activation_Date; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $branchcode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $csname; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->ACCHolderName); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->ACCNumber); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->ACCBranch); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $program; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->Email); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $asken; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $region; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $namalg; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $idstaffbank; ?></td>
                        <td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->operid); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->sellerid); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->spv_id); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->am_id); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row ->tsm_id); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id; ?></td>
                        <td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Periode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EXPDate; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EXPCard; ?></td>			
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->PremiumAmount; ?></td>
						
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>
</fieldset>

<fieldset class="corner">
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<tr><th>[Beneficiary]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;formnumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policyid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;dob</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;gender</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;relation</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;percentage</th>

	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
	$benf_query = $benf . $policy_cond . $tail_benf;
	// echo $benf_query;
	$benfRestObj = $connect->query($benf_query);

		$no = 1;
		$pol_id='';
		$holder_id=0;
		foreach ($benfRestObj ->result_object() as $key => $row) {
			if ($row ->policy_id != $pol_id) {
				$pol_id=$row ->policy_id;
				$holder_id=0;
			}
			$holder_id++;
	?>
		<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		<tr>
			<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->FormNumber ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->PolicyId) ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->Name) ; ?></td>
<!--			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->bnf_lname) ; ?></td>-->
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->DOB ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->Gender) ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->Relation) ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->Percentage ; ?></td>
			
			
		</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>
</fieldset>
<fieldset class="corner">
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<!-- <table width="99%" border="0" align="center"> -->
	<tr><th>[Survey]</th></tr>
	<?php 
	$tampkey = '';
	$nomor = 1;
	foreach ($policysurvey->result_object() as $key => $row){
		
		if($tampkey <> $row->customer_id){
			if ($nomor > 1){
				break;
			}	
   	?>
   				<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;No</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;formnumber</th>
                <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policyid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;refnumber</th>
                <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;insuredname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;answer1</th>	
   	<?php 		
			 
			
		}else{
	?>
			<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;question<?php echo $nomor;?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;answer<?php echo $nomor;?></th>	
	<?php 		
		}
		
		$tampkey =  $row->customer_id;
		$nomor++;
	}
	?>
		
	</tr>
	</div>
</thead>
<tbody>
	<?php 
	$no = 1;
	$oldkey ='';
	$jumlah = 0;
	echo $policysurvey->num_rows;
	foreach ($policysurvey->result_object() as $key => $row){
		
		
	?>
		<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		
		
			<?php 
			
				if($oldkey <> $row->customer_id){
					if($no > 1){
						echo "</tr>";	
					}
			?>
			<tr>
			<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->FormNumber; ?></td>
                        <td nowrap style="text-align: center" class="content-middle"><?php echo $row->PolicyId; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->refNumber; ?></td>
                        <td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->InsuredName); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->survey_question); ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->answer_value); ?></td>
			
			<?php 
			$no++;
			
				}else{
									
			?>	
					<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->survey_question); ?></td>
					<td nowrap style="text-align: center" class="content-middle"><?php echo strtoupper($row->answer_value); ?></td>					
			<?php 		
				}		
			
			$oldkey =$row->customer_id;	
	      
	}
		
	?>
	</tr>	
	</div>
</tbody>
</fieldset>	

