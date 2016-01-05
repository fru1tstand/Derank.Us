<?php
namespace derankus\database;
require_once PHP_ROOT . '/derankus/Setup.php';
use common\mysql\MySQL;
use common\mysql\QueryBuilder;
use derankus\feature\HiddenLobby;
use derankus\html\template\LobbyCard;

class Queries {

	/**
	 * Fetches the SQL for the "select unique lobbies" query.
	 *
	 * @param int $minutes
	 * @return string
	 */
	public static function getSelectUniqueLobbiesQuery(int $minutes): string {
		$hiddenLobbies = implode(", ", array_keys(HiddenLobby::get()));
		if ($hiddenLobbies == "") {
			$hiddenLobbies = "0";
		}
		return "
				SELECT
					`unique_lobbies`.`id` AS ". LobbyCard::FIELD_DB_ID .",
					`lobby_id` AS " . LobbyCard::FIELD_LOBBY_ID . ",
					`display_name` AS " . LobbyCard::FIELD_DISPLAY_NAME . ",
					`post_date` AS " . LobbyCard::FIELD_POST_DATE . ",
					`profile_id` AS " . LobbyCard::FIELD_PROFILE_ID . ",
					`title` AS " . LobbyCard::FIELD_TITLE . "
				FROM (
					SELECT MAX(id) AS id
					FROM lobby_post
					WHERE
						(`post_date` + 60 * $minutes) > UNIX_TIMESTAMP()
						AND `lobby_id` <> 0
						AND `profile_id` <> 0
						AND `lobby_id` NOT IN ($hiddenLobbies)
					GROUP BY lobby_id
				) AS unique_lobbies
				INNER JOIN lobby_post ON lobby_post.id = unique_lobbies.id
				WHERE fn_is_lobby_active(`unique_lobbies`.`id`) = 1
				ORDER BY post_date DESC";
	}

	/**
	 * Returns the lobby_id associated to the lobby_post.id value.
	 *
	 * @param int $dbId
	 * @return string | null
	 * @throws \Exception
	 */
	public static function selectLobbyIdFromDbId(int $dbId) {
		return MySQL::newQueryBuilder()
			->withQuery("SELECT `lobby_id` FROM `lobby_post` WHERE `id` = ?")
			->withParam($dbId, QueryBuilder::PARAM_TYPE_INT)
			->build()
			->getResultValue();
	}
}
