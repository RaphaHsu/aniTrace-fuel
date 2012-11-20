<?php
namespace Model;
use DB;
use Sentry;
use Arr;

/**
 * Anime list access.
 **/
class Anime extends \Model
{
	/**
	 * Return anime list by type.
	 *
	 * @param  string  'all' for all animes, 'download' for download list, 'watchable' for watchable animes.
	 * @return array   list of anime
	 **/
	public static function getList($type='')
	{
		if( $type == '' )
		{
			return DB::select()
				->from('anime_lists')
				->where('user_id', Sentry::user()->get('id'))
				->where_open()
					->where('download', 0)
					->where('volumn', 0)
				->where_close()
				->or_where_open()
					->where('download', '>', 'volumn')
					->or_where('finished', '0')
				->or_where_close()
				->execute()->as_array();
		}
		elseif( $type == 'download' )
		{
			return DB::select()
				->from('anime_lists')
				->where('user_id', Sentry::user()->get('id'))
				->and_where('finished', 0)
				->execute()->as_array();
		}
		elseif( $type == 'watchable' )
		{
			$sql  = 'select * from anime_lists
							where user_id = ' . Sentry::user()->get('id') .'
							and (
								(`download` = 0 and `volumn` = 0)
								or (`download` > `volumn` or `finished` = 0)
							) ';
			return DB::query($sql)->execute()->as_array();
		}
	}

	/**
		* Get anime information.
		* @param  int    anime ID
		* @return array  properties of anime
	 **/
	public static function getAnime($id = 0)
	{
		return DB::select()
			->from('anime_lists')
			->where('user_id', Sentry::user()->get('id'))
			->execute()->as_array()[0];
	}

	/**
		* Get anime volumn.
		* @param  int anime ID
		* @return int anime volumn
	 **/
	public static function getVolumn($id = 0)
	{
		return Anime::getAnime($id)['volumn'];
	}

	/**
		* Get anime download value.
		* @param  int anime ID
		* @return int anime download
	 **/
	public static function getDownload($id = 0)
	{
		return Anime::getAnime($id)['download'];
	}

	/**
	 * 
	 **/
	public static function setAnime($data='')
	{
		$id = Arr::get($data, 'id', '');
		if( Arr::is_assoc($data) && $id != '')
		{
			// Check value of volumn and download, Minumal value is 0.
			if( $data['volumn'] < 0 )
			{
				$data['volumn'] = 0;
			}
			if( $data['download'] < 0 )
			{
				$data['download'] = 0;
			}
			$result = DB::update('anime_lists')
				->where('id', $id)
				->where('user_id', Sentry::user()->get('id'))
				->set( $data )
				->execute();
		}
	}

	/**
		* Add anime volumn by one.
		* @param  int anime ID
	 **/
	public static function volumnUp($id)
	{
		$vol = Anime::getVolumn($id);
		$vol++;
		DB::update('anime_lists')
			->where('id', $id)
			->where('user_id', Sentry::user()->get('id'))
			->value('volumn', $vol)
			->execute();
	}

	/**
		* Subtract anime volumn by one until volumn equal 0.
		* @param  int anime ID
	 **/
	public static function volumnDown($id)
	{
		$vol = Anime::getVolumn($id);
		if( $vol > 0 )
		{
			$vol--;
			DB::update('anime_lists')
				->where('id', $id)
				->where('user_id', Sentry::user()->get('id'))
				->value('volumn', $vol)
				->execute();
		}
	}

	/**
		* Add download value by one.
		* @param  int anime ID
	 **/
	public static function downloadUp($id)
	{
		$download = Anime::getDownload($id);
		$download++;
			DB::update('anime_lists')
				->where('id', $id)
				->where('user_id', Sentry::user()->get('id'))
				->value('download', $download)
				->execute();
	}

	/**
		* Subtract anime download by one until download equal 0.
		* @param  int anime ID
	 **/
	public static function downloadDown($id)
	{
		$download = Anime::getDownload($id);
		if( $download > 0 )
		{
			$download--;
			DB::update('anime_lists')
				->where('id', $id)
				->where('user_id', Sentry::user()->get('id'))
				->value('download', $download)
				->execute();
		}
	}

	/**
		* Modify finish status between 0 and 1.
		* 0 if not finished, 1 if finished.
		* @param  int anime ID
	 **/
	public static function setFinished($id)
	{
		$finished = Anime::getAnime($id)['finished'];
		$finished = ( $finished + 1 ) % 2;  // Swap between 0 and 1
		DB::update('anime_lists')
			->where('id', $id)
			->where('user_id', Sentry::user()->get('id'))
			->value('finished', $finished)
			->execute();
	}
}
