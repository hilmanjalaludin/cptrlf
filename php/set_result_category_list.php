<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		$sql = " select * from t_lk_callreasoncategory a ";
	
	/** list pages ***/
	
		$ListPages -> query($sql);
		$ListPages -> setWhere();
		$ListPages -> OrderBy("a.CallReasonCategoryOrder","ASC");
		$ListPages -> setLimit();
		$ListPages -> result();
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_category');">#</a></th>	
		<th nowrap class="custom-grid th-middle" align="center">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle" align="left">&nbsp;Result ID </th>        
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;Result Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Result Category Order</th>
		<th nowrap width="15%" align="center" class="custom-grid th-lasted">&nbsp;Status</th>
	</tr>
</thead>	
<tbody>
	<?php
	//(CallReasonId, CallReasonCategoryId, CallReasonLevel, CallReasonCode, CallReasonDesc, CallReasonStatusFlag, CallReasonContactedFlag)
	
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
		
	?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CallReasonCategoryId; ?>" name="chk_category" id="chk_category"></td>
				<td class="content-middle" align="center"><?php echo $no ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonCategoryCode; ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonCategoryName; ?></td>
				<td class="content-middle" align="center"><?php echo $row -> CallReasonCategoryOrder; ?></td>
				<td align="center" class="content-lasted"><?php echo ($row -> CallReasonCategoryFlags?'Active':'Not Active');?></td>
				
				
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



