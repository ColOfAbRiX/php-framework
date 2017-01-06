<?php
/* Funzioni per la personalizzazione dell'interfaccia grafica
 *
 * Questa pagina contiene le funzioni che permettono di creare l'interfaccia grafica appropriata
 * per il sito. Se bisogna modificare la struttura base di una pagina per aggiungere, cambiare o
 * eliminare qualcosa agire su questa pagina.
 *
 * @package    Kernel
 * @subpackage User
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @depend     Smarty, PEAR, config.php, database.php, general.php
 */
require_once( dirname(__FILE__)."/../framework/packages/menu.php" );
require_once( dirname(__FILE__)."/../framework/packages/page_view.php" );

/**
 * Crea il menu di navigazione del sito
 *
 * @author     Fabrizio Colonna
 * @date       26/01/2006
 * @global     Array    Parametri di configurazione
 * @return     String   Il codice HTML contenente il menu
 */
function fnc_nav_menu()
{
	global $CONFIG;
	$out = get_menu_frame( 1,
	                       $CONFIG["menu"][1]["frame_tmpl"],
	                       "items",
	                       $CONFIG["menu"][1]["item_tmpl"] );
	return $out;
}

/**
 * Crea il menu sulla destra del sito
 *
 * @author     Fabrizio Colonna
 * @date       26/01/2006
 * @global     Array    Parametri di configurazione
 * @return     String   Il codice HTML contenente il menu
 */
function fnc_dx_menu()
{
	global $CONFIG;
	return get_menu_frame( 2,
	                       $CONFIG["menu"][2]["frame_tmpl"],
	                       "items",
	                       $CONFIG["menu"][2]["item_tmpl"] );
}

/**
 * Recupera il titolo della pagina
 *
 * @author     Fabrizio Colonna
 * @date       26/01/2006
 * @global     String   Il titolo della pagina
 * @return     String   Il titolo della pagina
 */
function get_title()
{
	global $title;
	return $title;
}
?>