<?php
/**
 * Text Page File
 *
 * Visualizzazione di un semplice contenuto HTML
 *
 * @package    Main
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 * @see        page_view.php
 */
require_once( dirname(__FILE__)."/framework/packages/page_view.php" );

if( !isset($_SESSION["is_logged"]) )
{
	// Visualizzazione utente

	// Controllo che sia stato specificato il nome per una pagina
	if( empty( $param_p ) )
	{
		trigger_error( "E' necessario specificare un nome per la pagina", E_USER_ERROR );
	}

	// Recupero il contenuto della pagina
	$text = get_page_content( $param_p, $title = "" );
}
else if( (bool)$_SESSION["is_logged"] == true )
{
	// Amministrazione

	$title = "Amministrazione pagine";
	$text = build_page_list();
	
}

echo( $text );
?>