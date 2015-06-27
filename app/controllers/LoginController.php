<?php

class LoginController extends BaseController {

	public function register(){
		$passwd = htmlspecialchars( Input::get('pw'),ENT_QUOTES);
		$user = htmlspecialchars( Input::get('user'),ENT_QUOTES );
		$nick = htmlspecialchars( Input::get('nick'),ENT_QUOTES );
		$inTable = DB::table('userList')->where('user', $user)->first();
		if($inTable)
			return "user name exists";
		else{
			$newPW = $passwd;
			for(int i=0; i<1000; ++i) $newPW=md5($newPW);
			DB::table('userList')
				->insert(array( 'user' => $user,
								'nick' => $nick,
								'pw' => $newPW ));
			return "yes";
		}
	}

	public function login(){
		$passwd = htmlspecialchars( Input::get('pw'),ENT_QUOTES);
		$user = htmlspecialchars( Input::get('user'),ENT_QUOTES );
		$inTable = DB::table('userList')->where('user', $user)->first();
		if($inTable && $inTable->password){
			$newPW = $passwd;
			for(int i=0; i<1000; ++i) $newPW=md5($newPW);
			if($inTable->password==$newPW){
				Session::put('user', $user);
				return "yes";
			}
			else
				return "wrong password";
		}
		else
			return "no user";
	}

	public function logout(){
		if (Session::has('user')){
			Session::forget('user');
			return "yes";
		}
		else
			return "wrong";
	}

}

