<?phpclass DateFactory extends mysql{	function DateFactory()	{		//	}	/** format date indonesia ***/		function date_time_english($date, $limiter="/")	{		if(empty($date)) return null;				$time_date = explode(' ', $date);		$date_explode = explode('-',$time_date[0]);		if( is_array($date_explode))		{			return $date_explode[2].$limiter.$date_explode[1].$limiter.$date_explode[0].' '.$time_date[1];		}	}		/** format date indonesia ***/		function date_time_indonesia($date, $limiter="/")	{			$time_date = explode(' ', $date);			$date_explode = explode('-',$time_date[0]);			if( is_array($date_explode))			{				if(empty($date_explode[0]) ){					return '';				}					else{						return $date_explode[2].$limiter.$date_explode[1].$limiter.$date_explode[0].' '.$time_date[1];				}			}			}	/** next date ****************/		function nextDate($date)	{		$dates = explode("-", $date);		$yyyy = $dates[0];		$mm   = $dates[1];		$dd   = $dates[2];				$currdate = mktime(0, 0, 0, $mm, $dd, $yyyy);		$dd++;				/* ambil jumlah hari utk bulan ini */		$nd = date("t", $currdate);		if($dd>$nd)		{			$mm++;			$dd = 1;			if($mm>12)			{				$mm = 1;				$yyyy++;			}		}						if (strlen($dd)==1)$dd="0".$dd;			if (strlen($mm)==1)$mm="0".$mm;					return $yyyy."-".$mm."-".$dd;	}			/** format date indonesia ***/		function indonesia($date, $limiter="/")	{		$time_date = explode(' ', $date);		$date_explode = explode('-',$time_date[0]);		if( is_array($date_explode))		{						return $date_explode[2].$limiter.$date_explode[1].$limiter.$date_explode[0];		}	}	/** format date engglish ***/	function exp_date_english($date, $src='/', $rplc='-')	{		$date_explode = explode($src, $date);		if( is_array($date_explode))		{			return $date_explode[0].$rplc.$date_explode[1].$rplc.$date_explode[2];		}	}		function exp_date_indo($date, $src='/', $rplc='-')	{		$date_explode = explode($src, $date);		if( is_array($date_explode))		{			return $date_explode[2].$rplc.$date_explode[1].$rplc.$date_explode[0];		}	}	/** format date engglish ***/	function english($date, $limiter="/")	{		$time_date = explode(' ', $date);		$date_explode = explode('-',$time_date[0]);		if( is_array($date_explode))		{			return $date_explode[2].$limiter.$date_explode[1].$limiter.$date_explode[0];		}	}		/** diffrennt date *************** of yeaRS ***/function set_date_diff($d1,$d2)	{  		$d1 = (is_string($d1) ? strtotime($d1) : $d1);  		$d2 = (is_string($d2) ? strtotime($d2) : $d2);  		$diff_secs = abs($d1 - $d2);  		$base_year = min(date("Y", $d1), date("Y", $d2));  		$diff_date = mktime(0, 0, $diff_secs, 1, 1, $base_year);  		return array( 						"years"=> date("Y", $diff_date) - $base_year, 						"months_total"=>(date("Y", $diff_date) - $base_year) * 12 + date("n", $diff_date) - 1, 						"months"=>date("n", $diff_date) - 1, "days_total" =>floor($diff_secs / (3600 * 24)),  						"days"=>date("j", $diff_date) - 1, "hours_total" =>floor($diff_secs / 3600),  						"hours"=>date("G", $diff_date), "minutes_total" =>floor($diff_secs / 60), 						"minutes"=> (int) date("i", $diff_date), "seconds_total"=>$diff_secs, "seconds"=> (int) date("s", $diff_date)					);  	}	/*  * diffrennt date ***************  * return object class  */	function get_date_diff($d1,$d2)	{  		$d1 = (is_string($d1) ? strtotime($d1) : $d1);  		$d2 = (is_string($d2) ? strtotime($d2) : $d2);  		$diff_secs = abs($d1 - $d2);  		$base_year = min(date("Y", $d1), date("Y", $d2));  		$diff_date = mktime(0, 0, $diff_secs, 1, 1, $base_year);  		return new class_diffrent(array( 						"years"=> date("Y", $diff_date) - $base_year, 						"months_total"=>(date("Y", $diff_date) - $base_year) * 12 + date("n", $diff_date) - 1, 						"months"=>date("n", $diff_date) - 1, "days_total" =>floor($diff_secs / (3600 * 24)),  						"days"=>date("j", $diff_date) - 1, "hours_total" =>floor($diff_secs / 3600),  						"hours"=>date("G", $diff_date), "minutes_total" =>floor($diff_secs / 60), 						"minutes"=> (int) date("i", $diff_date), "seconds_total"=>$diff_secs, "seconds"=> (int) date("s", $diff_date)					));  	}	}/* **************************************************** * return function object ***************************** * author Omens ***************************************  * **************************************************** */class class_diffrent extends DateFactory{	private $class_diffrent;	/** aksesor of class ***/		function class_diffrent($RETURN = array())	{		$this -> class_diffrent = $RETURN;	}	/** get yeras ****/	function months_total()	{		return $this -> class_diffrent['months_total'];		}	/** get yeras ****/	function months()	{		return $this -> class_diffrent['months'];			}	/** get yeras ****/	function days_total()	{		return $this -> class_diffrent['days_total'];				}	/** get yeras ****/	function days()	{		return $this -> class_diffrent['days'];				}/** get yeras ****/	function hours_total()	{		return $this -> class_diffrent['hours_total'];				}	/** get yeras ****/	function hours()	{		return $this -> class_diffrent['hours'];				}		/** get yeras ****/	function minutes_total()	{		return $this -> class_diffrent['minutes_total'];				}		/** get yeras ****/	function minutes()	{		return $this -> class_diffrent['minutes'];				}	/** get yeras ****/	function seconds_total()	{		return $this -> class_diffrent['seconds_total'];				}		/** get yeras ****/	function seconds()	{		return $this -> class_diffrent['seconds'];				}	} ?>