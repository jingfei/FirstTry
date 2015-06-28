<?php

class MapController extends BaseController {

	public function map(){
		return View::make('pages.map');
	}
	public function map2(){
		return View::make('pages.map2');
	}
	public function health(){
		$weight = $pulse = $tem = $blood = "[";
		$date = new DateTime('2015-06-01');
		$start = (int)$date->format('U');
		$date = new DateTime('2015-06-30');
		$end = (int)$date->format('U');
		for($i=$start; $i<=$end; $i+=86400){
			if($i==$end){
				$weight .= "[".(string)$i.", ".(string)rand(47,55)."]]";
				$pulse .= "[".(string)$i.", ".(string)rand(60,80)."]]";
				$blood .= "[".(string)$i.", ".(string)rand(120,150)."]]";
				$tem .= "[".(string)$i.", ".(string)rand(33,38)."]]";
			}
			else{
				$weight .= "[".(string)$i.", ".(string)rand(47,55)."],";
				$pulse .= "[".(string)$i.", ".(string)rand(60,80)."],";
				$blood .= "[".(string)$i.", ".(string)rand(120,150)."],";
				$tem .= "[".(string)$i.", ".(string)rand(33,38)."],";
			}
		}
		return View::make('pages.health')
					->with('weight',$weight)
					->with('pulse',$pulse)
					->with('blood',$blood)
					->with('tem',$tem);
	}

	public function xml(){

		$xml = simplexml_load_file( 'http://opendata.cwb.gov.tw/member/opendataapi?dataid=F-C0032-001&authorizationkey=CWB-35122AA7-5BAF-4A6F-ADEC-B95A93558999' );
		$time = $xml->sent;
		$xml = $xml->dataset->location;

		$rain = array();
		foreach($xml as $data){
			$county=(string)$data->locationName;
			$county = str_replace("臺","台",$county);
			$county = str_replace("桃園市","桃園縣",$county);
			$value=(string)$data->weatherElement[4]->time->parameter->parameterName;
			array_push($rain,array("COUNTYNAME"=>$county,
							 "value"=>$value,
						));
		}

		$weather = array();
		foreach($xml as $data){
			$county=(string)$data->locationName;
			$county = str_replace("臺","台",$county);
			$county = str_replace("桃園市","桃園縣",$county);
			$name=(string)$data->weatherElement[0]->time->parameter->parameterName;
			$value=(string)$data->weatherElement[0]->time->parameter->parameterValue;
			array_push($weather,array("COUNTYNAME"=>$county,
							 "name"=>$name,
							 "value"=>$value
						));
		}

		$tem = array();
		foreach($xml as $data){
			$county=(string)$data->locationName;
			$county = str_replace("臺","台",$county);
			$county = str_replace("桃園市","桃園縣",$county);
			$value=(string)(((int)$data->weatherElement[2]->time->parameter->parameterName+(int)$data->weatherElement[1]->time->parameter->parameterName)*5);
			array_push($tem,array("COUNTYNAME"=>$county,
							 "value"=>$value,
						));
		}

		return View::make('pages.map')
					->with('time',$time)
					->with('weather',json_encode($weather))
					->with('tem',json_encode($tem))
					->with('rain',json_encode($rain));
	}

}

