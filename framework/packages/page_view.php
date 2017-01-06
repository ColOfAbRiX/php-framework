<?php
/* Funzioni di visualizzazione della pagina
 *
 * Funzioni per la creazione completa di una pagina del sito utilizzando i template
 * del file di configurazione.
 *
 * @package    Kernel
 * @subpackage User
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @see        config.php
 * @see        template_engine.php
 * @see        database.php
 * @depend     Smarty, PEAR, config.php, database.php, general.php
 */

// Signature
define( "KERNEL_USER_PAGE_VIEW", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../kernel/database.php" );
require_once( dirname(__FILE__)."/../kernel/template_engine.php" );
require_once( dirname(__FILE__)."/../kernel/config.php" );

/**
 * Recupera il contenuto di una pagina
 *
 * Recupera il contenuto di una pagina dal database a partire dall'identificativo
 * della pagina. Il secondo parametro viene utilizzato come variabile di output e
 * vi viene inserito il titolo della pagina per risparmiare sul numero di query
 * eseguite. L'utilizzo di questa pagina è quasi esclusivo di {@link build_page}.
 * In caso la pagina non sia trovata la funzione solleva un errore
 *
 * @author     Fabrizio Colonna
 * @date       09/01/2006
 * @see        queries.php
 * @global     Array     Insieme delle query SQL del sito
 * @param      String    $page Una opzionale funzione di elaborazione dei dati
 * @param      String    $title E' un parametro di output in cui viene inserito il titolo della pagina
 * @return     String    Il codice della pagina recuperata dal database
 */
function get_page_content( $page, &$title = null )
{
	global $SQL;

	$page_row = db_execute_array( $SQL["page_content"], Array($page) );

	if( empty( $page_row ) )
	{
		trigger_error( "La pagina chiamata \"$page\" non esiste", E_USER_ERROR );
	}

	$content = $page_row[0]["content"];
	$title = $page_row[0]["name"];

	return $content;
}

/**
 * Recupera la lista completa delle pagine
 *
 * La lista recuperata serve per essere utilizzata nella sezione amministrativa
 *
 * @author     Fabrizio Colonna
 * @date       09/01/2006
 * @see        queries.php
 * @global     Array    Insieme delle query SQL del sito
 * @param      Integer  $start L'offset da cui caricare la lista
 * @param      Integer  $count Quante pagine recuperare
 * @return     Array    Il codice della pagina recuperata dal database
 */
function get_page_list( $start = 0, $count = 0 )
{
	global $SQL;

	$str_sql = $SQL["page_list"];
	$parameters = Array( $start, $count );
	
	// Se è specificato count devo cambiare la stringa SQL
	if( $count == 0 )
	{
		$str_sql = str_replace( ", !", "", $str_sql );
		$parameters = Array( $start );
	}
	if( $start == 0 )
	{
		$str_sql = eregi_replace( " LIMIT (\!|\!,\ \!)", "", $str_sql );
		$parameters = Array();
	}

	// Recupero la lista delle pagine
	$pagine = db_execute_array( $str_sql, $parameters );

	/*
	for( $i = 0; $i < count($pagine); $i++ )
	{
		$pagine[$i] = $pagine[$i]["name"];
		
	}
	*/

	return $pagine;
}

/**
 * Crea la lista delle pagine sul sito
 *
 * La lista recuperata serve per essere utilizzata nella sezione amministrativa
 *
 * @author     Fabrizio Colonna
 * @date       09/01/2006
 * @see        queries.php
 * @global     Array    Insieme delle query SQL del sito
 * @param      Bool    $admin Indica se abilitare l'amministrazione delle pagine.
 * @return     Array   Il codice della pagina recuperata dal database
 */
function build_page_list( $admin = false )
{
	$page_list = get_page_list();

	$container["template"] = "page_list_frame.tmpl";
	$container["list_name"] = "page_list";

	$items["template"] = "page_list_item.tmpl";
	$items["item_function"] = "page_check_item";

	for( $i = 0; $i < count($page_list); $i++ )
	{
		$items["data"][$i] = Array( "page_name"=>$page_list[$i]["name"], "page_id"=>$page_list[$i]["id"] );
	}
	
	debug_watch( "items", $items );

	return build_cmplx_tmpl( $container, $items );
}

function page_check_item( $item, $id )
{
	global $param_p;
	
	if( $param_p == $item["page_id"] )
	{
		$item["page_name"] = "<b>{$item["page_name"]}</b>";
	}
	
	return $item;
}
?>