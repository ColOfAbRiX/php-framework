<?php
/**
 * News file
 *
 * Gestisce la visualizzazione di tutte le news, di una news singola e la loro modifica
 * ed inserimento.
 *
 * @package    Main
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 * @see        news.php
 */

include_once( dirname(__FILE__)."/framework/packages/news.php" );
$title = "News";

// Recupero l'ID della news da visualizzare
$param_id = (int)@$param_id;

if( isset($_SESSION["is_logged"]) && !(bool)$_SESSION["is_logged"] )
{
	// Amministrazione
	$text = "Amministrazione pagine";
}
else
{
	// Visualizzazione utente
	if( isset($param_id) && $param_id == 0 )
	{
		// La lista delle news
		$text = build_news();
	}
	else
	{
		// Una news visualizzata completamente
		$text = build_complete_news( get_news_result_by_id((int)$param_id) );
	}
}

echo( $text );
?>