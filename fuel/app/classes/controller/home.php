<?php

class Controller_Home extends Controller
{

	public function action_index()
	{
		$view = View::forge('home/index');
		$data = array(
			'header' => View::forge('header'),
			'navbar' => View::forge('navbar'),
			'page_title' => 'aniTrace',
			'loggedin' => false,
		);
		$view->set_global( $data );

		return $view;
	}

	public function action_404()
	{
		return '404';
	}
}
