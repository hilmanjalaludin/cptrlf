<?
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

	if ($prodid) {
		$productName = getProductName($prodid, $connect);
	}
	else
		$productName = 'All active product';

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
				tsm.init_name as tsm_id,
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
				inner JOIN tms_agent AS tsm ON tsm.handling_type=1
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

		$tail=" AND cst.CallReasonQue =1
				and ins.QCStatus=1 
				group by pa.PolicyNumber
				order by pa.PolicyNumber";
		$sql = $sql . $tail;
		// echo $sql;
		$policyRestObj = $connect->query($sql);

		// query for data insured
		$insured ="
				SELECT DISTINCT 
				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber) 
			 	AS FormNumber,
				concat(ins.InsuredFirstName, ins.InsuredLastName) as InsuredName,
				date_format(ins.InsuredDOB,'%Y-%m-%d') as InsuredDOB,
				g.GenderId as sex,
				py.PayerAddressLine1 as Address1,
				py.PayerAddressLine2 as Address2,
				py.PayerCity as City,
				'' as RTdanRW,
				py.PayerZipCode as ZipCode,
				py.PayerHomePhoneNum as Phone1,
				py.PayerWorkPhoneNum as Phone2,
				'' as EXTPhone2,
				py.PayerMobilePhoneNum as MobilePhone,
				'' as MaritalStatus,
				'' as Children,
				'' as FACode,
				date_format(plc.PolicySalesDate, '%Y-%m-%d') as Activation_Date,
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
				spv.id as spv_id,
				am.id as am_id,
				tsm.init_name as tsm_id,
				date_format(plc.PolicySalesDate, '%Y-%m-%d') + interval '3' month as EXPDate,
				'' as EXPCard,
				prd.ProductCode AS product_id,
				'3' as Periode,
				round(plc.Premi,0) as PremiumAmount
												
				FROM t_gn_customer AS cst
				inner join t_gn_insured ins on ins.customerid=cst.customerid
				inner join t_gn_policy plc on plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.PolicyNumber=plc.PolicyNumber
				inner join t_gn_payer py on py.CustomerId=cst.CustomerId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				inner JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
				inner JOIN tms_agent AS spv ON agt.spv_id = spv.UserId
				inner JOIN tms_agent AS am ON spv.mgr_id = am.UserId
				inner JOIN tms_agent AS tsm ON tsm.UserId=430
				left join t_lk_salutation s on s.SalutationId=ins.SalutationId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId
				WHERE 1 = 1
		";

		$insured_tail =	"ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder ";

		// query for data beneficiary
		$benf ="
		SELECT DISTINCT
				concat(date_format(plc.PolicySalesDate,'%d/%m/%y'), '-', pa.PolicyNumber) 
			 	AS FormNumber,
				plc.PolicyId AS PolicyId,
				concat(bnf.BeneficiaryFirstName, bnf.BeneficiaryLastName) as Name,
				bnf.BeneficiaryDOB as DOB,
				ins.GenderId as Gender,
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
	<tr><th>[Policy]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;FormNumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;InsuredName</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;InsuredDOB</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Address1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Address2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;City</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;RTdanRW</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ZipCode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Phone1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Phone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;EXTPhone2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;MobilePhone</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;MaritalStatus</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Children</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;FACode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Activation_Date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;BranchCode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;CSName</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ACCHolderName</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ACCNumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ACCBranch</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Program</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Email</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Asken</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Region</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;NamaLG</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;IDStaffBank</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;operid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sellerid</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;spv_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;am_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;tsm_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;product_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;EXPDate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;EXPCard</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Periode</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;PremiumAmount</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$policy_cond = $policy_array ? " and pa.PolicyNumber in (". $policy_array .") " : ' and 1 = -1 ';
		$insured_query = $insured . $policy_cond . $insured_tail;
		$insuredRestObj = $connect->query($insured_query);
		$no = 1;
		$pol_id='';
		$holder_id=0;
		foreach ($insuredRestObj ->result_object() as $key => $row) {
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
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InsuredName; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->InsuredDOB; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sex; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Address1; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Address2; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->City; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->RTdanRW; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ZipCode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Phone1; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Phone2; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EXTPhone2; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MobilePhone; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->MaritalStatus; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Children; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->FACode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Activation_Date; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->BranchCode; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CSName; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ACCHolderName; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ACCNumber; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ACCBranch; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Program; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Email; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Asken; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Region; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->NamaLG; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->IDStaffBank; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->operid; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sellerid; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->spv_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->am_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->tsm_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EXPDate; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EXPCard; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Periode; ?></td>
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
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;FormNumber</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;PolicyId</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;DOB</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Gender</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Relation</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Percentage</th>

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
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->PolicyId ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->Name ; ?></td>
<!--			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_lname ; ?></td>-->
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->DOB ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->Gender ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->Relation ; ?></td>
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