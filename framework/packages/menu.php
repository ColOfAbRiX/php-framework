<?php
/**
 * Funzioni di visualizzazione del menu di navigazione
 *
 * Contiele le funzioni per la visualizzazione del menu di menu di navigazione
 * in genere presenti sulla destra dello schermo, ma configurabile tramite
 * i template.
 *
 * @package    Kernel
 * @subpackage User
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @see        config.php
 * @see        general.php
 * @see        database.php
 * @depend     Smarty, PEAR, config.php, database.php, general.php
 */

// Signature
define( "KERNEL_USER_MENU", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../kernel/database.php" );
require_once( dirname(__FILE__)."/../kernel/template_engine.php" );
require_once( dirname(__FILE__)."/../kernel/config.php" );
require_once( dirname(__FILE__)."/config/menu.php" );

/**
 * Restituisce il riquadro di un menu correttamente formattato.
 *
 * @author     Fabrizio Colonna
 * @date       23/12/2005
 * @global     Array    Insieme delle query SQL del sito
 * @global     Array    Parametri di configurazione
 * @param      Integer  $menu L'indice del menu da caricare
 * @param      String   $frame_tmpl Il template di tutto il menu
 * @param      String   $tmpl_var La variabile del template $tmpl da valorizzare
 * @param      String   $item_tmpl Il templat di un item della lista
 * @param      Function $fnc Una opzionale funzione di elaborazione dei dati
 * @return     String   Il codice HTML contenente il menu
 */
function get_menu_frame( $menu, $frame_tmpl, $tmpl_var, $item_tmpl, $fnc = null )
{
	global $CONFIG, $SQL;

	$data = db_execute_array( $SQL["menu_items"], Array($menu) );

	$container =
		Array( "template"  => $frame_tmpl,
	           "list_name" => $tmpl_var );
	$items =
	    Array( "data"      => $data,
	           "template"  => $item_tmpl,
	           "in_func"   => "std_mnu_format" );

	// Richiamo il caricamento dei template
	return build_cmplx_tmpl( $container, $items );
}

/**
 * Elabora i dati del menu ricevuti dal database.
 *
 * Fornisce l'elaborazione standard per i dati dei menu provenienti dal database.
 *
 * @author     Fabrizio Colonna
 * @date       08/01/2006
 * @global     Array    Insieme delle query SQL del sito
 * @global     Array    Parametri di configurazione
 * @param      Integer  $menu L'indice del menu da caricare
 * @param      String   $frame_tmpl Il template di tutto il menu
 * @param      String   $tmpl_var La variabile del template $tmpl da valorizzare
 * @param      String   $item_tmpl Il templat di un item della lista
 * @param      Function $fnc Una opzionale funzione di elaborazione dei dati
 * @return     String   Il codice HTML contenente tutte le news formattate secondo i template
 */
function std_mnu_format( $data )
{
	$out = Array();

	for( $i = 0; $i < count($data); $i++ )
	{
		$item = $data[$i];

		if( strlen($item["link"]) > 0 )
		{
			$item["item"] = "<a href=\"{$item["link"]}\">{$item["item"]}</a>";
		}
		$item["item"] = str_repeat( "&nbsp;", $item["indent"] ) . $item["item"];

		array_push( $out, Array( "item" => $item["item"] ));
	}

	return $out;
}
?>