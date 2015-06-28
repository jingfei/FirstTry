<?php

class LoginController extends BaseController {

	public function register(){
		$input = Input::json()->all();
		$passwd = htmlspecialchars( $input['pw'],ENT_QUOTES);
		$user = htmlspecialchars( $input['user'],ENT_QUOTES );
		$nick = htmlspecialchars( $input['nick'],ENT_QUOTES );
		$inTable = DB::table('userList')->where('user', $user)->first();
		if($inTable)
			return array("result"=>"user name exists");
		else{
			$newPW = $passwd;
			for($i=0; $i<1000; ++$i) $newPW=md5($newPW);
			DB::table('userList')
				->insert(array( 'user' => $user,
								'nick' => $nick,
								'pw' => $newPW ));
			return array("result"=>"yes");
		}
	}

	public function login(){
		$input = Input::json()->all();
		$passwd = htmlspecialchars( $input['pw'],ENT_QUOTES);
		$user = htmlspecialchars( $input['user'],ENT_QUOTES );
		$inTable = DB::table('userList')->where('user', $user)->first();
		if($inTable && $inTable->pw){
			$newPW = $passwd;
			for($i=0; $i<1000; ++$i) $newPW=md5($newPW);
			if($inTable->pw==$newPW){
				Session::put('user', $user);
				return array("result"=>"yes");
			}
			else
				return array("result"=>"wrong password");
		}
		else
			return array("result"=>"no user");
	}

	public function logout(){
		if (Session::has('user')){
			Session::forget('user');
			return array("result"=>"yes");
		}
		else
			return array("result"=>"wrong");
	}

}

