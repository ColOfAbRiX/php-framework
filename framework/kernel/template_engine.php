<?php
/**
 * Funzioni per l'elaborazione dei template
 *
 * Questo file espone le funzioni necessare per l'utilizzo del template engine Smarty.
 * Le funzioni di questo file memorizzano una oggetto Smarty nella variabile globale $SMARTY
 * che usano come oggetto di default. E' comunque possibile non utilizzare questa variabile
 * globale ed usare una oggetto alternativo. Se ne l'oggetto di default ne l'oggetto
 * alternativo sono specificati viene creta sul momento una nuovo template engine Smarty ed
 * impstato come default.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @depend     config.php, debug.php
 */
// Signature
define( "KERNEL_ADMIN_TEMPLATE_ENGINE", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../general.php" );
require_once( dirname(__FILE__)."/adv_smarty.php" );
require_once( dirname(__FILE__)."/config.php" );
require_once( dirname(__FILE__)."/debug.php" );

/**
 * Crea un nuovo Template Engine Smarty utilizzando i parametri di config.php.
 *
 * La funzione crea un nuovo oggetto Smarty utilizzando i parametri della sezione template nel file di configurazione
 * {@link config.php}. L'utilizzo della cache è sempre abilitato. Nella modalità di debug la cache viene sempre cancelata
 * e viene forzata la ricompilazione dei template.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @global     Array    Parametri di configurazione
 * @param      boolean  $set_global Indica se ipostare la connessione come default.
 * @return     PEAR::DB La connessione al database
 */
function smarty_create( $set_global = true )
{
	global $CONFIG;

	$smarty = new AdvSmarty();

	$root_dir = dirname(__FILE__) . "/../../";
	$smarty->template_dir = $root_dir . $CONFIG["tmpl"]["tmpl_dir"];
	$smarty->cache_dir = $root_dir . $CONFIG["tmpl"]["cache_dir"];
	$smarty->compile_dir = $root_dir . $CONFIG["tmpl"]["cache_dir"];

	$smarty->caching = $CONFIG["tmpl"]["cache"];			// Impostazione caching
	$smarty->force_compile = false;
	$smarty->compile_check = false;

	// Impostazione per il debug
	if( $CONFIG["debug"] )
	{
		$smarty->force_compile = (bool)$CONFIG["tmpl"]["debug"];
		$smarty->compile_check = (bool)$CONFIG["tmpl"]["debug"];
		
		$smarty->debugging = true;
		$smarty->clear_cache();
	}

	// Imposto l'oggetto globale
	if( $set_global )
	{
		set_global_smarty( $smarty );
	}

	return $smarty;
}

/**
 * Elimina un oggetto smarty
 *
 * La funzione elimina un template engine Smarty. Se non ne viene specificato uno cancellato l'oggetto globale.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @param      Smarty $obj L'oggetto che fa riferimento al template engine
 * @global     Smarty L'oggetto Smarty globale
 * @return     void
 */
function smarty_destroy( $obj = null )
{
	global $SMARTY;

	if( !is_a($obj, "AdvSmarty") )
	{
		// Elimino l'oggetto specificato
		$obj = null;
	}
	elseif( !is_a($SMARTY, "AdvSmarty") )
	{
		// Elimino l'oggetto globale
		$SMARTY = null;
	}
}

/**
 * Recupera il template engine globale di default.
 *
 * Se non esiste ancora un template engine di defaul la funzione si preoccupa di crearne uno e poi restituirlo.
 * Predispone ad upgradare il sito utilizzando i paradigmi della OOP.
 *
 * @author     Fabrizio Colonna
 * @date       19/
 * @global     Smarty Connession di default al database
 * @return     Smarty Il template engine di default
 */
function get_global_smarty()
{
	global $SMARTY;

	$smarty = $SMARTY;

	// Se il database non c'è lo creo
	if( !is_object($smarty) )
	{
		$smarty = smarty_create( true );
	}

	return $smarty;
}

/**
 * Imposta come template engine di default l'oggetto specificato.
 *
 * @author     Fabrizio Colonna
 * @date       05/12/2005
 * @global     Smarty Smarty Template Engine
 * @param      Smarty $obj L'oggetto Smarty da utilizzare
 * @return     void
 */
function set_global_smarty( $obj )
{
	global $SMARTY;
	$SMARTY = $obj;
}

/**
 * Crea un template
 *
 * Carica il template specificato e lo riempie con l'array di parametri passato. Prima di utilizzare il valore statico
 * passato assieme alla variabile viene utilizzato il sistema di assegnamento dinamico dei valori {@link dynamic_assign}
 * I valori assegnati al template engine una volta inseriti vengono subito eliminati per non sovraccaricare l'ambiente e
 * quindi non sono disponibili oltre questa funzione.
 *
 * Variabili di debug: $assignments
 *
 * @author     Fabrizio Colonna
 * @date       05/12/2005
 * @global     Smarty   Smarty Template Engine
 * @param      String   $template Il template da recuperare
 * @param      Array    $values L'array associativo con i valori da sostituire nel template
 * @param      Smarty   $obj Il template engine da utilizzare
 * @return     String   Il template riempito con i valori
 */
function build_template( $template, $values = Array(), $obj = null )
{
	// Attenzione! Questa funzione presenta dei problemi con il caching. In alcune situazioni
	// vengono riutilizzati gli stessi parametri impostati per altri template, non ne conosco la
	// causa e non so come risolverli. Ho quindi disabilitato il caching.

	global $SMARTY;

	// Gestione parametri in ingresso
	if( !is_a($obj, "Smarty") )
	{
		$obj = $SMARTY;
	}
	if( !is_a($obj, "Smarty") )
	{
		$obj = smarty_create( false );
	}


	// Cancellazione preventiva degli assegnamenti precedenti
	$obj->clear_all_assign();

	// Provo ad assegnare le varibili tramite la chiamata di funzioni
	$id = debug_timer_start( debug_timer_newid() );
	$values = dynamic_assign( $template, $values );

	// Assegno tutti i valori
	foreach ($values as $key => $value)
	{
		$obj->assign( $key, $value );
	}

	// Recupero l'output ed elimino i valori inseriti
	debug_watch( "Building '$template' with", $values );
	
	$output = $obj->fetch( $template );
	debug_watch( "Building template required", (debug_timer_stop($id) * 1000) . "ms" );
	
	$obj->clear_all_assign();


	return $output;
}

/**
 * Crea un template complesso
 *
 * Un template complesso è un template che contiene una serie di altri template creati o no in sequenza. Un esempio
 * concreto di template complesso può essere una lista di news. Ogni news è descritta da un template, queste news vengono
 * poi caricate e quindi sono tutte inserite all'interno di un'altro template.
 *
 * Descrizione parametro $container:<br>
 *   $container["template"]:  Nome del template per il contenitore.<br>
 *   $container["list_name"]: Variabile del template contenitore che accoglia la lista di elementi.<br>
 *   $container["values"]:    Opzionale. Altri assegnamenti da passare al template.<br>
 * Descrizione parametro $items:<br>
 *   $items["data"]:          I dati in ingresso sotto forma di un array. La prima dimensione dell'array contiene<br>
 *                            la lista dei dati sequenziali e dentro ogni indice c'è un array associativo in cui<br>
 *                            ogni chiave è una variabile del template e ogni valore sono i valori delle variabili<br>
 *                            nel template.<br>
 *   $items["template"]:      Nome del template per un singolo elemento<br>
 *   $items["input_filter"]:  Opzionale. Funzione di elaborazione dei dati in ingresso<br>
 *   $items["item_function"]: Opzionale. Funzione di elaborazione che viene chiamata per ogni elemento<br>
 *   $items["merge_function"]:Opzionale. Funzione personalizzata per l'unione degli elementi<br>
 *
 * @author     Fabrizio Colonna
 * @date       14/12/2005
 * @param      Array    $container Dati per il template contenitore dei singoli elementi
 * @param      Array    $items Dati per i singoli elementi da elaborare
 * @return     String   Il template corretamente formattato
 */
function build_cmplx_tmpl( $container, $items )
{
	$id = debug_timer_start( debug_timer_newid() );

	$filled = "";
	$out_data = Array();

	// Chiamo più facilmente i dati
	$in_data = $items["data"];
	$input_func = get_index( "input_filter", $items );			// Potrebbe non essere passato
	$item_func = get_index( "item_function", $items );			// Potrebbe non essere passato
	$custom_merge_func = get_index( "merge_function", $items );	// Potrebbe non essere passato

	// Elaborazione dei dati in ingresso da una funzione personalizzata
	if( $input_func )
	{
		$in_data = $input_func( $in_data );
	}

	// Riempio ogni template con un valore della lista
	for( $i = 0; $i < count($in_data); $i++ )
	{
		// Chiamata di funzione per la gestione di un singolo item
		if( $item_func )
		{
			$in_data[$i] = $item_func( $in_data[$i], $i );
		}
		
		if( $custom_merge_func )
		{
			debug_error( "Sezione da testare" );

			// Chiamata alla funzione di unione personalizzata
			$tmp = $custom_merge_func( $in_data, $i );
			$out_data = array_merge( $out_data, $tmp );
		}
		else
		{
			$out_data[$i] = build_template( $items["template"], $in_data[$i] );
		}
	}

	// Unisco tutti i valori
	$filled = Array( $container["list_name"] => implode("", $out_data) );
	if( isset($container["values"]) )
	{
		$filled = array_merge( $container["values"], $filled );
	}

	// Sostituisco la stringa risultante nel template unitario
	return build_template( $container["template"], $filled );
}

/**
 * Assegna i valori delle variabili all'interno di un template
 *
 * Carica le funzioni del file di codie associato con il template specificato (la posizione di questo file viene definita
 * nel file {@link config.php}), quindi analizza le variabili presenti all'interno del template. Se trova una
 * corrispondenza tra una funzione del codice ed una variabile del template, chiama questa funzione per assegnare il
 * valore alla variabile. In caso questa corrispondenza non venga trovata la funzione controlla che la variabile sia
 * stata almeno specificata tra i valori statici (la variabile $values passata come argomento). In caso non la trovi
 * nemmeno li solleva un errore.
 *
 * @author     Fabrizio Colonna
 * @date       29/01/2006
 * @param      String    $template Il template da elaborare
 * @param      Array     $values Array associativo contenente le assegnazioni gia valorizzate
 * @return     Array     L'array contenente tutti i valori del template
 * @obsolete
 */
function dynamic_assign( $template, $values = Array() )
{
    global $CONFIG;

	$code_dir = dirname(__FILE__) . "/../../" . $CONFIG["tmpl"]["code_dir"] . "/";
	$file_name = $code_dir . $template . ".php";

	$obj = get_global_smarty();

	// Recupero le variabili all'interno del template generale
	$variables = $obj->get_vars_in_template( $template );

	// Per ogni variabile del template richiamo una funzione omonima e salvo il risultato
	// nelle variabili assengate. Se la funzione non esiste la variabile viene saltata.
	foreach ( $variables as $variable )
	{
		$var_name = $func_name = substr( $variable, 1 );

	    $called = call_ext_function( $file_name, $func_name, Array(), $var_value );
		if( $called )
		{
    		// Inserisco il risultato assieme alle altre variabili gia assegnate
    		$values = array_merge( $values, Array($var_name => $var_value) );
		}
	}

	return $values;
}
?>