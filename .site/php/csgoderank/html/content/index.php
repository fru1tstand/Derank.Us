<?php
namespace csgoderank\html\content;
require_once $_SERVER['DOCUMENT_ROOT'] . '/.site/php/csgoderank/Setup.php';
use csgoderank\database\Queries;
use csgoderank\html\template\LobbyCard;
use csgoderank\html\template\StaticPage;


$cardContent = LobbyCard::createContentsFromQuery(Queries::getSelectUniqueLobbiesQuery(10));
$cardHtml = (count($cardContent) > 0) ? "" : "<tr><td>No active lobbies found. Consider making one!</td></tr>";
foreach ($cardContent as $content) {
	$cardHtml .= $content->getRenderContents();
}

$body = <<<HTML
<div id="landing-banner">
	<div>
		<h1>Derank.Us</h1>
		<p class="hint">Find Lobbies. Get Silver.</p>
	</div>
</div>

<div class="card-list">
	$cardHtml
</div>

<iframe class="hidden" id="lobby-linker"></iframe>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="/.site/php/csgoderank/js/index.php"></script>
HTML;

StaticPage::createContent()
	->with(StaticPage::FIELD_TITLE, "Home")
	->with(StaticPage::FIELD_BODY, $body)
	->render();
