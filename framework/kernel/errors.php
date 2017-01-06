<?php
/**
 * Funzioni di gestione personalizzata degli errori
 *
 * Questo file contiene le funzioni utilizzate per gestire gli errori che potrebbero
 * venire sollevati durante la normale esecuzione del codice.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @depend     PEAR, PEAR::DB, config.php, debug.php
 */
// Signature
define( "KERNEL_ERRORS", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/template_engine.php" );
require_once( dirname(__FILE__)."/../general.php" );
require_once( dirname(__FILE__)."/config.php" );
require_once( dirname(__FILE__)."/debug.php" );

/**
 * Gestore degli errori.
 *
 * Il messaggio di errore personalizzato viene visualizzato trame il template della variabile di configurazione
 * $CONFIG["error.tmpl"]. Se si è in modalità di debug non verrà caricato il template ma verrà solo visualizzato un
 * messagio di errore. La visualizzazione degli errori è nel contesto del contenuto, e non ha un livello proprio come il
 * debug.
 *
 * @author     Fabrizio Colonna
 * @date       19/12/2006
 * @param      Integer   $errno The first parameter, errno, contains the level of the error raised, as an integer.
 * @param      String    $errstr The second parameter, errstr, contains the error message, as a string.
 * @param      String    $errfile Contains the filename that the error was raised in, as a string.
 * @param      Integer   $errline Contains the line number the error was raised at, as an integer.
 * @param      Array     $errcontext Contain an array of every variable that existed in the scope the error was triggered in
 * @global     Array     Parametri di configurazione
 * @return     void
 */
function error_handler( $errno, $errstr, $errfile = null, $errline = 0, $errcontx = null )
{
	global $CONFIG;

	// Maschero gli errori che non devo gestire
	$error_mask = (int)ini_get( "error_reporting" );
	$errno &= $error_mask;

	switch( $errno )
	{
		// Il caso zero capita quando l'errore è stato mascherato
		case 0:
			return;

		// Errore sollevato da una chiamata del programmatore
		case E_USER_ERROR:
			$error_msg = $errstr;
			break;

		// Tutti gli altri errori
		default:
			$error_msg = "$errstr\n$errfile - $errline";
			break;
	}

	if( !$CONFIG["debug"] )
	{
		// Cancello il testo gia scritto
		buffer_clear_level();
		error_show( nl2br($error_msg) );

		// Sistema per il logging degli errori. Da completare
		error_log_message( Array($errno, $errstr, $errfile, $errline, $errcontx) );
        die();
    }
	else
	{
		// Nel debug non faccio vedere tutti quei messaggioni
        debug_error( "($errno) $errstr" );
	}

	// Termino l'esecuzione della pagina se richiesto
    if( $CONFIG["error"]["terminate"] )
	{
		app_end();
	}
}

/**
 * Visualizza un messaggio di errore personalizzato per il debug.
 *
 * Il messaggio di errore personalizzato viene visualizzato trame il template della variabile di configurazione
 * $CONFIG["error.tmpl"]. Se si è in modalità di debug non verrà caricato il template ma verrà solo visualizzato un
 * messagio di errore.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     Array     Parametri di configurazione
 * @param      String   $text Il testo da visualizzare
 * @return     void
 */
function error_show( $text )
{
	global $CONFIG;
	echo( build_template( $CONFIG["error"]["tmpl"], Array("content" => $text) ) );
}

/**
 * Crea il log dell'errore
 *
 * Carica un template e lo riempie con informazioni utili per il debug. Ovviamente funziona
 * se l'errore non è nel template engine..
 *
 * @author     Fabrizio Colonna
 * @date       20/01/2005
 * @param      Array    $params I parametri da visualizzare, sono gli stessi di {@link error_handler}
 * @return     String   Il messaggio per il log
 */
function error_log_message( $params )
{
	global $CONFIG;

	$context = print_r($params[4], true );
	$stack = print_r( debug_backtrace(), true );
	$environment = print_r( $GLOBALS, true );

	$data = Array
	(
		"error_num" => $params[0],
		"error_msg" => $params[1],
		"error_file" => $params[2],
		"error_line" => $params[3],
		"error_context" => htmlencode($context),
		"error_callstack" => htmlencode($stack),
		"error_globals" => htmlencode($environment)
	);

	return build_template( $CONFIG["error"]["log_tmpl"], $data );
}

/**
 * Controlla gli errori in un oggetto PEAR.
 *
 * Se l'oggetto PEAR ha sollevato un eccezione la funzione si occupa di rilevare l'errore e di visualizzarlo tramite la
 * funzione error_show()
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     Array     Parametri di configurazione
 * @param      Object   $pear L'oggetto PEAR da controllare.
 * @return     bool      True se tutto è andato a buon fine
 */
function pear_check( $pear )
{
	if( PEAR::isError($pear) )
	{
		trigger_error( "PEAR: " . $pear->getMessage(), E_USER_ERROR );
        return false;
	}
    return true;
}
?>