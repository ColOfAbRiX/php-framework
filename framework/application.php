<?php
/**
 * Funzioni dell'applicazione web
 *
 * Questo file contiene funzioni relative all'esecuzione dell'applicazione web (sito), come la sua
 * inizializzazione e terminazione.
 * In genere questo file deve essere modificato da sito a sito per far fronte alle specifiche
 * esigenze del caso, quindi non ? da considerarsi come una vera e propria libreria.
 *
 * @package    Kernel
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 */

// Signature
define( "KERNEL_APPLICATION", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/kernel/template_engine.php" );
require_once( dirname(__FILE__)."/kernel/buffer_handlers.php" );
require_once( dirname(__FILE__)."/kernel/buffering.php" );
require_once( dirname(__FILE__)."/kernel/database.php" );
require_once( dirname(__FILE__)."/kernel/config.php" );
require_once( dirname(__FILE__)."/kernel/errors.php" );
require_once( dirname(__FILE__)."/kernel/debug.php" );

/**
 * Inizializzazione dell'applicazione.
 *
 * La funzione esegue una serie di operazioni standard per inizializzare l'applicazione web.
 * Come prima cosa viene disabilitata l'opzione di compressione automatica di php.ini per dare la possibilit? allo script
 * di fare come desidera. Quindi viene creato un sistema di buffering a due livelli per gestire separatamente contenuto
 * e struttura grafica. Il secondo livello utilizza la funzione {@link handle_content} per inserire il contenuto all'
 * interno della struttura grafica, il primo livello chiama la funzione {@link output_supervisor} per eseguire alcune
 * operazioni secondarie. Come ultima cosa viene creato il template engine e viene gestita la connessione al database. In
 * caso fosse necessario la funzione randomizza anche il seme per la generazione di numeri casuali. In caso sia abilitata
 * la modalit? debug vengono visualizzati tutti gli errori sollevati da PHP, mentre in caso di esecuzione normale vengono
 * visualizzati solo gli errori critici.
 *
 * @author     Fabrizio Colonna
 * @date       12/01/2006
 * @global     Array    Parametri di configurazione
 * @return     void
 */
function app_start()
{
	global $CONFIG;

	// Configurazione di debug
	if( $CONFIG["debug"] )
	{
		debug_enter_section( "kernel_core" );
		debug_timer_start( "application" );
		error_reporting( E_ALL );
	}
	else
	{
		error_reporting( $CONFIG["error"]["report"] );
	}

    // In caso venisse usato...
	mt_srand( time() );
	array_init( $_SESSION );

	// Disabilito la compressione automatica e il flush implicito
	ini_set( "zlib.output_compression", "Off" );
	ob_implicit_flush(0);

	// Registro le variabili
	init_parameters();

	// Inizializzazione del sistema di buffering
	buffer_start();
	buffer_add_level( "", "system_buffer_handler", "buffer_handlers.php" );
	if( $CONFIG["debug"] )
	{
		buffer_add_handler( "debug_show", "kernel/debug.php" );
	}
	buffer_add_level( "", "user_buffer_handler", "buffer_handlers.php" );

    // Gestione degli errori
	if( !(bool)$CONFIG["error"]["use_php"] )
	{
		set_error_handler( "error_handler" );
	}

	// Creazione del template engine
	smarty_create();
	// Connessione al database
	if( $CONFIG["db"]["start_state"] )
	{
		db_connect();
	}
}

/**
 * Finalizzazione dell'applicazione.
 *
 * La funzione termina l'applicazione web, distrugge il template engine e, se necessario,
 * disconnette il database e alla fine svuota i buffer di memoria.
 *
 * Variabili di debug: $execution_time.
 *
 * @author     Fabrizio Colonna
 * @date       12/01/2006
 * @global     Array    Parametri di configurazione
 * @global     Bool     In fase di terminazione
 * @return     void
 */
function app_end()
{
	global $CONFIG, $ending;

	// Siccome anche questa funzione pu? sollevare degli errori, a causa di cosa ? previsto all'interno del gestore degli
	// errori pu? capitare che app_end() sia chiamata pi? di una volta con conseguente ciclo infinito.
	if( !empty($_SESSION["ending"]) )
	{
		return;
	}
	$_SESSION["ending"] = 1;

    // Ripristino il vecchio gestore degli errori
    restore_error_handler();

	// Visualizzazione variabili di debug
	if( $CONFIG["debug"] )
	{
	    debug_watch( "SESSION", $_SESSION );
	    debug_watch( "GET", $_GET );
	    debug_watch( "POST", $_POST );
	    debug_watch( "COOKIE", $_COOKIE );
		debug_watch( "execution_time", debug_timer_stop( "application" ) );
		debug_place_mark( "End point" );
		debug_exit_section();
	}

	// Il database ed il template engine vanno chiusi dopo l'uscita dal livello utente di buffering perch? vengono
	// utlizzati per creare il frame della pagina

	// Terminazione del buffer
	buffer_commit();                       // Da qui non ? pi? possibile vedere l'outputs
    buffer_end();

    // Eliminazione del template engine
	smarty_destroy();
	// Disconnessione dal database
	db_disconnect();

    die();
}

/**
 * Ristabilisce il controllo del programma (DA PERFEZIONARE)
 *
 * Termina l'esecuzione della pagina restituendo completamente il controllo dell'output al programmatore, ma mantiene le
 * informazioni di debug.
 *
 * @author     Fabrizio Colonna
 * @date       02/02/2006
 * @todo       Tutto
 * @return     void
 */
function app_cut()
{
	global $ending;

	// Impedisce di chiamare app_end()
	$ending = 1;

	// Cancella i buffer
	//while( @ob_end_clean() );

	debug_watch( "<font color=red>disable_</font>", "Application cutted", debug_backtrace() );
}
?>