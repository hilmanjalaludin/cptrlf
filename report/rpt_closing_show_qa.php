<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$connect = new mysql();

	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	// $cignasystem = $_REQUEST['cignasystem'];
	$prodid = $_REQUEST['prodid'];

	$productName = getProductName($prodid, $connect);

	function getProductName($prodid, $dbase)
	{
		$sql = "select pr.ProductName from t_gn_product pr
				where pr.ProductCode='$prodid' ";
		$te = $dbase->query($sql);
		$te2 = $te->result_object();

		return $te2[0]->ProductName;

	}
	
		// $ListPages -> pages = $db -> escPost('v_page'); 
		// $ListPages -> setPage(10);

		// query for data policys
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as policy_ref,
				cst.NumberCIF AS prospect_id,
				prd.ProductCode AS product_id,
				cmp.CampaignNumber AS campaign_id,
				cmp.CampaignNumber as campaign_TBSS,
				plc.PolicySalesDate as input,
				plc.PolicyEffectiveDate as effdt,
				'' as payer_cifno,
				s.Salutation as payer_title,
				py.PayerFirstName as payer_fname,
				py.PayerLastName as payer_lname,
				g.GenderShortCode as payer_sex,
				py.PayerDOB as payer_dob,
				'' as addrtype,
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
				pt.PaymentTypeDesc as pay_type,
				ct.CreditCardTypeDesc as card_type,
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
				0 as benefit_level,
				round(sum(plc.Premi),0) as premium,
				round(if(prt.ProductType='PA',plc.Premi, if(pm.PayModeCode='M', if(count(distinct ins.InsuredId)>1, 1, 0)*0.10*12*sum(plc.Premi),if(count(distinct ins.InsuredId)>1, 1, 0)*0.10*sum(plc.Premi))),0) as nbi,
				'N' as export,
				'' as exportdate,
				'' as canceldate,
				cst.CustomerUpdatedTs as callDate2,
				0 as paystatus,
				'' as paynotes,
				'' as payauthcode,
				'' as paytransdate,
				'' as payorderno,
				'' as payccnum,
				'' as paycvv,
				'' as payexpdate,
				'' as paycurency,
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
				'' as operid,
				agt.id as sellerid,
				spv.id as spv_id,
				am.id as atm_id,
				'' as tsm_id,
				'' as pcifnumber,
				'' as pcardtype,
				'' as prefnumber,
				'' as paccnumber,
				'' as paccname,
				'' as pcardnumber,
				'' as record_id,
				cst.CustomerUpdatedTs as callDate,
				cst.CustomerHomePhoneNum2 as phone2,
				cst.CustomerMobilePhoneNum2 as payer_mobilephone2,
				cst.CustomerWorkPhoneNum2 as payer_officephone2
				
				FROM t_gn_customer AS cst
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner JOIN t_gn_policy AS plc ON plc.PolicyNumber = pa.PolicyNumber
				
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
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				group by pa.PolicyNumber
				order by pa.PolicyNumber
				";
							//where c.CallReasonId in (16,17,37,38,39,40,41,42)
							//AND (cs.cignasystemcode)like '%".$cignasystem."%'
				// echo "$sql";
				// die();
		$policyRestObj = $connect->query($sql);

		// query for data insured
		$sql = "SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				pg.PremiumGroupOrder as holder_id,
				pg.PremiumGroupCode as holder_type,
				s.Salutation as holder_title,
				ins.InsuredFirstName as holder_fname,
				ins.InsuredLastName as holder_lname,
				g.GenderShortCode as holder_sex,
				ins.InsuredDOB as holder_dob,
				ins.InsuredIdentificationNum as holder_ssn,
				r.RelationshipTypeCode as relation,
				prp.ProductPlan as benefit_level,
				plc.Premi as premium,
				'' as holder_race,
				idt.IdentificationType as holder_idtype,
				0 as holder_issmoker,
				'' as holder_nationality,
				'' as holder_maritalstatus,
				'' as holder_occupation,
				'' as holder_jobtype,
				'' as holder_position,
				0 as holder_height,
				0 as holder_weight,
				'' as uwstatus,
				'' as uwlastupdate,
				'' as uwapprovedate,
				'' as uwprintdate,
				prd.ProductCode as product_id,
				'' as rating_factor1,
				'' as rating_factor2,
				'' as holder_birthplace

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
				inner JOIN t_gn_product AS prd ON prd.ProductId = pa.ProductId
				left join t_lk_salutation s on s.SalutationId=ins.SalutationId
				left join t_lk_gender g on g.GenderId=ins.GenderId
				left join t_lk_identificationtype idt on idt.IdentificationTypeId=ins.IdentificationTypeId
				left join t_lk_premiumgroup pg on pg.PremiumGroupId=ins.PremiumGroupId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=ins.RelationshipTypeId
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				ORDER BY pa.PolicyNumber, pg.PremiumGroupOrder

				 ";

				$insuredRestObj = $connect->query($sql);

		// query for data beneficiary
		$sql ="SELECT DISTINCT 
				pa.PolicyNumber AS policy_id,
				'' as holder_id,
				'' as bnf_id,
				bnf.BeneficiaryFirstName as bnf_fname,
				bnf.BeneficiaryLastName as bnf_lname,
				g.GenderShortCode as bnf_sex,
				bnf.BeneficiaryIdentificationNum as bnf_ssn,
				'' as bnf_bene_ind,
				'' as bnf_client_type,
				bnf.BeneficieryPercentage as bnf_percent,
				'' as bnf_coverage,
				r.RelationshipTypeCode as bnf_relation,
				bnf.BeneficiaryDOB as bnf_dob

				FROM t_gn_customer AS cst
				inner JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
				inner join t_gn_beneficiary bnf on bnf.CustomerId=cst.CustomerId
				inner join t_gn_policyautogen pa on pa.CustomerId = cst.CustomerId
				inner join t_gn_product prd on prd.ProductId=pa.ProductId
				left join t_lk_gender g on g.GenderId=bnf.GenderId
				left join t_lk_relationshiptype r on r.RelationshipTypeId=ins.RelationshipTypeId
				WHERE date(cst.CustomerUpdatedTs) >= '$start_date'
				and date(cst.CustomerUpdatedTs) <= '$end_date'
				and prd.ProductCode='$prodid'
				-- cst.CallReasonId IN (37,38) 
				ORDER BY pa.PolicyNumber
				";

				$benfRestObj = $connect->query($sql);
	
		SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: #3366FF;"> &nbsp;&nbsp;&nbsp;Closing TXT (ver. Agent) &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: red;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
		echo "</br><th> &nbsp;&nbsp;&nbsp;Product id : $prodid</th>";
		echo "</br><th> &nbsp;&nbsp;&nbsp;Product Name: $productName</th>";
		echo "</br>";
		echo "</br>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<!-- <table width="99%" border="0" align="center"> -->
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
<!-- </fieldset> -->	
<tbody>
	<?php
		$no = 1;
		foreach ($policyRestObj ->result_object() as $key => $row) {
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->operid; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sellerid; ?></td>
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
	?>
</table>

</fieldset>

<fieldset class="corner">
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<!-- <table width="99%" border="0" align="center"> -->
	<tr><th>[Insured]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_title</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_fname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_lname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_dob</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_ssn</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;relation</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;benefit_level</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;premium</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_race</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_idtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_issmoker</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_nationality</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_maritalstatus</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_occupation</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_jobtype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_position</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_height</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_weight</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uwstatus</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uwlastupdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uwapprovedate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uwprintdate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;product_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;rating_factor1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;rating_factor2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_birthplace</th>

	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$no = 1;
		foreach ($insuredRestObj ->result_object() as $key => $row) {
	?>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		<tr>
			<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->policy_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_type; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_title; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_fname; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_lname; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_sex; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_dob; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_ssn; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->relation; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->benefit_level; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->premium; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_race; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_idtype; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_issmoker; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_nationality; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_maritalstatus; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_occupation; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_jobtype; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_position; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_height; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_weight; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwstatus; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwlastupdate; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwapprovedate; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->uwprintdate; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->product_id; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->rating_factor1; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->rating_factor2; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->holder_birthplace; ?></td>
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
	<tr><th>[Beneficiary]</th></tr>
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;holder_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_fname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_lname</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_ssn</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_bene_ind</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_client_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_percent</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_coverage</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_relation</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;bnf_dob</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$no = 1;
		foreach ($benfRestObj ->result_object() as $key => $row) {
	?>
		<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		<tr>
			<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->policy_id ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->holder_id ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_id ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_fname ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_lname ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_sex ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_ssn ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_bene_ind ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_client_type ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_percent ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_coverage ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_relation ; ?></td>
			<td nowrap style="text-align: center" class="content-middle"><?php echo $row->bnf_dob ; ?></td>
		</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>
</fieldset>