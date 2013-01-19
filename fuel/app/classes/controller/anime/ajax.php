<?php
use \Model\Anime;
/**
 * Methods for Ajax request in animtion page.
 **/
class Controller_Anime_Ajax extends Controller
{
	/**
	 * Construtor for Anime
	 **/
	public function before()
	{
		if(!Sentry::check())
		{
			Response::redirect(Uri::create('auth/'));
		}
	}

	/**
	 * Default method. Return all availible animes which is not finished user.
	 **/
	public function action_index()
	{
		return json_encode(Anime::getList('download'));
	}

	/**
	 * Return all watchable animes.
	 **/
	public function action_watchableList()
	{
		return json_encode(Anime::getList('watchable'));
	}

	/**
	 * Return anime information.
	 * @param  int   anime ID
	 * @return json  anime information
	 **/
	public function action_anime($id=0)
	{
		$id = intval($id);
		if( $id > 0 )
		{
			echo json_encode(Anime::getAnime($id));
		}
	}

	/**
	 * Add or subtract anime volumn.
	 * @param  string  'up' or 'down'
	 * @param  int     anime ID
	 * @return json    anime information after update
	 **/
	public function action_vol($action, $id=0)
	{
		$id = intval($id);
		if( $id > 0 )
		{
			if( $action == 'up' )
			{
				Anime::volumnUp($id);
				echo json_encode(Anime::getAnime($id));
			}
			else
			{
				Anime::volumnDown($id);
				echo json_encode(Anime::getAnime($id));
			}
		}
	}

	/**
	 * Add or subtract anime download progress.
	 * @param  string  'up' or 'down'
	 * @param  int     anime ID
	 * @return json    anime information after update
	 **/
	public function action_download($action, $id=0)
	{
		$id = intval($id);
		if( $id > 0 )
		{
			if( $action == 'up' )
			{
				Anime::downloadUp($id);
				echo json_encode(Anime::getAnime($id));
			}
			else
			{
				Anime::downloadDown($id);
				echo json_encode(Anime::getAnime($id));
			}
		}
	}

	/**
	 * Set if anime has finished.
	 * @param  int   anime ID
	 * @return jsosn anime information
	 **/
	public function action_finished($id=0)
	{
		$id = intval($id);
		if( $id > 0 )
		{
			Anime::setFinished($id);
			return json_encode(Anime::getAnime($id));
		}
	}

	/**
	 * Add a new anime.
	 * @return  json  anime information which has been inserted
	 **/
	public function action_add()
	{
		$validate = Validation::forge();
		$validate->add_field('name', 'anime name', 'required');
		$validate->add_field('link', 'external link', 'max_length[10240]');
		if( $validate->run() )
		{
			$data = array(
				'name' => Input::post('name'),
				'sub' => Input::post('sub', ''),
				'link' => Input::post('link', '')
			);
			$newAnime = Anime::addAnime($data);
			echo json_encode($newAnime);
		}
		else
		{
			echo json_encode(array('stat'=>'input error'));
		}
	}

	/**
	 * Modify anime information
	 * @return  json  anime information after update
	 **/
	public function action_mod()
	{
		$id = intval(Input::post('id', 0));
		$data = array(
				'id' => $id,
				'name' => Input::post('name', ''),
				'sub' => Input::post('sub', ''),
				'volumn' => intval(Input::post('vol', 0)),
				'download' => intval(Input::post('buy', 0)),
				'link' => Input::post('link', ''),
				);
		if( $id > 0 && $data['name'] != '' )
		{
			echo json_encode(Anime::setAnime($data));
		}
		else
		{
			echo json_encode(array('stat'=>'failed'));
		}
	}

   public function action_delete($id=0) {
      $id = intval($id);
      if($id > 0 ){
         $affected = Anime::deleteAnime($id);
         if( $affected == 1 )
         {
            echo json_encode(array('stat'=>'ok'));
         }
         else
         {
            echo json_encode(array('stat'=>'error'));
         }
      }
      else
      {
         echo json_encode(array('stat'=>'error'));
      }
   }

	/**
	 * Return user information needed in views.
	 **/
	private function getUserInfo()
	{
		$user = Sentry::user();
		$result = array(
			'username' => $user->get('username'),
			'isAdmin' => true,
		);
		return $result;
	}
}