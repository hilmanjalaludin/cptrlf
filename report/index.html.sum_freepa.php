<?php
include(dirname(__FILE__).'/../sisipan/sessions.php');
include(dirname(__FILE__).'/../fungsi/global.php');
include(dirname(__FILE__).'/../class/MYSQLConnect.php');
include(dirname(__FILE__).'/../class/class.application.php');
include(dirname(__FILE__).'/../class/lib.form.php');
include(dirname(__FILE__).'/../sisipan/parameters.php');

class index extends mysql
{
	var $start_date;
	var $end_date;

	function index()
	{
		parent::__construct();
		$this -> header();
		$this -> setLabel();
		$this -> content();
		$this -> footer();
	}
	function styleCss(){ ?>
		<style>
			table.grid{}
			td.header { background-color:#2182bf;font-family:Arial;font-weight:bold;color:#f1f5f8;font-size:12px;padding:5px;}
			td.sub { background-color:#eeeeee;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;}
			td.subtot { background-color:#ef9b9b;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;}
			td.content { padding:2px;height:24px;font-family:Arial;font-weight:normal;color:#456376;font-size:12px;background-color:#f9fbfd;}
			td.first {border-left:1px solid #dddddd;border-top:1px solid #dddddd;border-bottom:0px solid #dddddd;}
			td.middle {border-left:1px solid #dddddd;border-bottom:0px solid #dddddd;border-top:1px solid #dddddd;}
			td.lasted {border-left:1px solid #dddddd; border-bottom:0px solid #dddddd; border-right:1px solid #dddddd; border-top:1px solid #dddddd;}
			td.agent{font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd;
					border-bottom:0px solid #dddddd; border-right:0px solid #dddddd; border-top:0px solid #dddddd;
					background-color:#fcfeff;padding-left:2px;color:#06456d;font-weight:bold;}
			h1.agent{font-style:inherit; font-family:Trebuchet MS;color:blue;font-size:14px;color:#2182bf;}

			td.total{
						padding:2px;font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd;
					border-bottom:1px solid #dddddd; border-top:1px solid #dddddd;
					border-right:1px solid #dddddd; border-top:1px solid #dddddd;
					background-color:#2182bf;padding-left:2px;color:#f1f5f8;font-weight:bold;}
			span.top{color:#306407;font-family:Trebuchet MS;font-size:28px;line-height:40px;}
			span.middle{color:#306407;font-family:Trebuchet MS;font-size:14px;line-height:18px;}
			span.bottom{color:#306407;font-family:Trebuchet MS;font-size:12px;line-height:18px;}
			td.subtotal{ font-family:Arial;font-weight:bold;color:#3c8a08;height:30px;background-color:#FFFCCC;}
			td.tanggal{ font-weight:bold;color:#FF4321;height:22px;background-color:#FFFFFF;height:30px;}
			h3{color:#306407;font-family:Trebuchet MS;font-size:14px;}
			h4{color:#FF4321;font-family:Trebuchet MS;font-size:14px;}
		</style>

	<?php }


	function header()
	{
		global $Themes;

		echo "
			<html>
				<head>
					<title>{$Themes -> V_WEB_TITLE} - Reporting </title>
					<meta http-equiv=Content-Type content=\"text/html; charset=windows-1252\">
					<meta name=ProgId content=\"Excel.Sheet\">
					<meta name=Generator content=\"Microsoft Excel 12\">\n\r";

			// cs : css
			$this -> styleCss();
			// ce : css

		echo "
				</head>
				<body>\n\r";

	}

	/** set label html **/

	private function setLabel()
	{
		$report_type = array (
			'sum_freepa' => 'Report Summary FreePA'
			);

		$start_date  = str_replace("-","/",$this -> escPost('start_date'));
		$end_date  	 = str_replace("-","/",$this -> escPost('end_date'));
		$start_selling_date = str_replace("-", "/", $this->escPost('start_selling_date'));
		$end_selling_date = str_replace("-", "/", $this->escPost('end_selling_date'));
		$labelReport  		 = $report_type[$this -> escPost('report_type')];
		$today		 		 = date("d/m/Y");

		echo "<div class=\"label_header\" style=\"margin-bottom:5px;padding-top:5px;padding-bottom:5px;border-bottom:1px solid #eee;width:'100%';\">
				<span class='top'>{$labelReport}</span><br/>
				<span class='middle'>Monitoring Date : {$start_date} - {$end_date}  </span> |
				<span class='middle'>Selling Date : {$start_selling_date} - {$end_selling_date}  </span><br/>
				<span class='bottom'>Report Date :  $today </span>
				</div>\n\r";//<span class='middle'>Mode : {$ModeReport}</span><br/>
	}

	function content()
	{
		if( $this ->havepost('report_type') )
		{
			$new_name_file = $_REQUEST['report_type'];
			if( !empty($new_name_file))
			{
				include(dirname(__FILE__).'/HTML/'.$new_name_file.'.php');
				$object = new $new_name_file();
				switch($_REQUEST['content'])
				{
					default : $object -> show_content_html();  break;
					case 'HTML' : $object -> show_content_html();  break;
				}
			}
		}
	}

	function footer()
	{
		echo "</body>
				</html>";
	}
}

new index();

?>
