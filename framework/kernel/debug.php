<?php
/**
 * Funzioni per il debugging dell'applicazione
 *
 * Funzioni utilizzate per visualizzare messaggi di errore all'utente e al programmatore in fase di
 * programmazione pemettendo di fare il debugging dell'applicazione.<br>
 * Le principali funzionalità offerte da questo sistema di debug sono:
 *   - Visualizzazione di messaggi suddivisi in 4 categorie: messaggi di sistema, messaggi di errore,
 *     visualizzazione variabili, marker placement.
 *   - Timer per la misurazione delle prestazioni degli script
 *   - Divisione degli items del debug in sezioni
 *   - Filtraggio dell'output
 *
 * <b>NOTA</b>: Le funzioni di debug non utilizzano i metodi standard per la visualizzazione del testo in
 * HTML, ovverosia non fanno uso dei template. Il codice HTML è scritto all'interno del codice PHP,
 * questo per rendere indipendente dal resto del sistema, e quindi non dipendente dai suoi errori
 * il sistema di debug.<br>
 * <b>NOTA</b>: Chi volesse modificare le funzioni di debug deve stare attento a non usare queste funzioni
 * all'interno del debug stesso, perchè potrebbero venire a crearsi dei riferimenti circolari con
 * conseguenti loop infiniti.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @see        config.php, debug_gfx.php
 */

// Signature
define( "KERNEL_ADMIN_DEBUG", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../general.php" );
require_once( dirname(__FILE__)."/debug_gfx.php" );
require_once( dirname(__FILE__)."/config.php" );

/**
 * Visualizza le informazioni di debug
 *
 * Visualizza le variabili con il relativo al debug inserendole in una opportuna form grafica, utilizzando
 * per ogni tipologia di dato le opportune funzioni.<br>
 * Le visualizzazioni personalizzate vengono gestite chiamando le funzioni del tipo relativo costruendo il
 * nome della variabile con debug_show_type_<tipo> e passandole i dati da visualizzare. Vedere anche le 
 * funzioni {@link debugfx_show_type_variable}, {@link debugfx_show_type_sysinfo}, {@link debugfx_show_type_marker}
 *
 * @author     Fabrizio Colonna
 * @date       06/12/2005
 * @param      String    $buffer Il buffer su cui scrivere le informazioni di debug
 * @global     Array     $debug_item_list
 * @global     Boolean   $debug_is_showing
 * @return     String    L'insieme formattato in HTML delle informazioni di debug
 */
function debug_show( $buffer = null )
{
	global $debug_item_list, $debug_is_showing;
	$debug_is_showing = true;
	$output = "";

	// Se non ci sono variabili da scrivere salto la procedura
	if( !empty($debug_item_list) )
	{
	    // Inizializzazione grafica del debug
	    $output .= debugfx_begin();
	    
	    foreach( $debug_item_list as $data )
	    {
	    	// Filtraggio delle informazioni di debug
	    	if( debug_filter_check( get_index("debug_filter", $_GET), $data ) )
	    	{
		        // Richiamo la funzione specifica per visualizzare il tipo di dato corrente
		        $format_data = "debugfx_show_type_{$data["type"]}";
		        
		        // Eseguo la funzione
		        $data_type = $format_data( $data );
	
		        $output .= $data_type;
	    	}
	    }

	    // Finalizzazione grafica del debug
	    $output .= debugfx_end();
	}

	$debug_is_showing = false;
	return $buffer . $output;
}

/**
 * Filtra un dato
 * 
 * Il filtro si intende eseguito su ognuna delle singore righe del debug. E' possibile esprimere
 * espressioni booleane, anche complesse, sui dati che compaiono nella visualizzazione del debug per
 * ottenere un sottoinsieme ristretto di informazioni, generalmente più utili, delle informazioni
 * complete.<br>
 * Operatori di confronto: A<B, A<=B, A==B, A!=B, A>=B, A>B, A has "text", A regexp /regexp/imsxeuADSUX<br>
 * Operatori logici: (expr1)and(expr2), (expr1)or(expr2), (expr1)xor(expr2), not(expr)<br>
 * Variabili di controllo: variabile $data<br>
 * Esempio: <samp>(type_data.value.items regexp /<[^<>]>/m) or (type_data.value.item regexp /<[^<>]>/m)</samp>
 *
 * @author     Fabrizio Colonna
 * @date       15/09/2007
 * @see        debug_show
 * @param      String   $filter La stringa filtro
 * @param      Array    $data Dati dell'item
 * @return     Bool     True se il dato è valido, false altrimenti
 */
function debug_filter_check( $filter, $data )
{
	// Mettere un controllo sul formato degli operatori logici
	//$filter = "(type_data.value.items regexp /<[^<>]>/m) or (type_data.value.item regexp /<[^<>]>/m)";
	
	// Il filtro vuoto fa passare tutto
	if( $filter == "" )
	{
		return true;
	}

	// RegEx di controllo
	$l_value     =  "[\w\d\.]*";
	$number      =  "\d+(?:\.\d*|)";
	$string      =  "\"[^\"]*\"";
	$regexp      =  "\/[^\/]*\/(?:[msxeuADSUX]{0,1}){0,10}";
	$r_value     =  "(?:$number|$string|$regexp)";
	$compare_ops =  "(?:<=|==|!=|>=|<|>|\shas\s|\sregexp\s)";
	$pattern     =  "/\s*($l_value\s*$compare_ops\s*$r_value)\s*/i";
	// /\s*([\w\d\.]*\s*(?:<=|==|!=|>=|<|>|\shas\s|\sregexp\s)\s*(?:\d+(?:\.\d*|)|"[^"]*"|\/[^\/]*\/(?:[msxeuADSUX]{0,1}){0,10}))\s*/i
	
	preg_match_all( $pattern, $filter, $output );

	// Sostituisco ogni singola espressione con il suo risultato booleano
	foreach( $output[0] as $expression )
	{
		$original_expr = $expression;
		
		// Recupero la parte a dx e a sx dell'operatore per eseguire delle operazioni
		list( $right, $left ) = preg_split( "/\s*$compare_ops\s*/", $expression, 2 );
		
		// Elabotazione di R-Value
		$right_new = "\$data";		// -> Accesso all'array $data
		$array_indexes = split( "\.", $right );
		
		foreach( $array_indexes as $index )
		{
			if( !is_numeric($index) )
			{
				$right_new .= "[\"$index\"]";
			}	
			else
			{
				$right_new .= "[$index]";
			}
		}
		
		// Gli operatori determinano il formato della stringa da eseguire
		$operator = str_replace( $right, "", $expression );
		$operator = str_replace( $left, "", $operator );
		$operator = preg_replace( "/\s*/", "", $operator );
		
		switch( strtolower($operator) )
		{
			case "has":		// Operatore "contiene"
				$expression = "strlen(stristr( $right_new, $left )) > 0";
				break;
	
			case "regexp":	// Regular Expression
				$expression = "preg_match( \"$left\", $right_new ) > 0";
				break;
	
			default:		// Classici operatori di confronto
				$expression = str_replace( $right, $right_new, $expression );
				$expression = str_replace( $left, $left, $expression );
				$expression = str_replace( $operator, $operator, $expression );
				break;
		}
	
		// Valutazione dell'output
		$expr_result = eval( "return ($expression);" ) ? "true" : "false";

		
		// Inserimento nella query filtro
		$filter = str_replace( $original_expr, $expr_result, $filter );
	}

	$filter = preg_replace( "/\s+/", " ", $filter );		// Causa errore con gli spazi
	$output = eval( "return ($filter);" );
	
	return $output;
}

/**
 * Crea ed inserisce un item nel debug
 * 
 * La funzione aiuta nella creazione di un nuovo item per il debug e lo inserisce automaticamente nella lista di
 * debug tramite {@link debug_add_item}.
 *
 * @author     Fabrizio Colonna
 * @date       24/01/2007
 * @see        debug_add_item
 * @param      String   $type Tipo di item da creare
 * @param      Array    $type_data Dati dell'item
 * @param      Integer  $stack_skip Numero di passi da non contare nella visualizzazione dello ST
 * @param      Integer  $extra_skip Numero stacktrace da non contare dovuti alle chiamate di debug fino a questa funzione compresa
 * @global     Integer  $debug_sections
 * @return     void
 */
function debug_create_item( $type, $type_data, $stack_skip, $extra_skip )
{
	global $debug_sections;
	
	// Recupero la sezione di codice specificata per ultima
	$current_section = "";
	if( !empty($debug_sections) )
	{
		$current_section = $debug_sections[count($debug_sections) - 1];
	}
	
	// Elimino lo stack trace 
	$stack_trace = debug_backtrace();
	for( ; $extra_skip > 0; $extra_skip-- )
	{
		if( isset($stack_trace) )
		{
			array_shift( $stack_trace );
		}
	}

	$item_info = Array(
		"type" => $type,
		"type_data" => $type_data,
		"stack_trace" => $stack_trace,
		"skip_stack" => $stack_skip,
		"time" => microtime( true ),
		"tags" => $current_section
	);
	
	// Aggiungo il marker alla lista di debug
	debug_add_item( $item_info );
}

/**
 * Aggiunge un elemento alla lista di debug
 *
 * Aggiunge un elemento alla lista globale di debug, come una variabile o un marker, occupandosi di inizializzare la
 * lista se necessario. L'oggetto da aggiungere alla lista deve rispettare un determinato formato. Vedere {@link debug_create_item}
 * per ulteriori informazioni.
 *
 * @author     Fabrizio Colonna
 * @date       24/01/2007
 * @see        debug_create_item
 * @param      Array    $item Oggetto da aggiungere
 * @global     Array    $debug_item_list
 * @global     Boolean  $debug_is_showing
 * @return     void
 */
function debug_add_item( $item )
{
	global $debug_item_list, $debug_is_showing;
	array_init( $debug_item_list );
	
	$debug_is_showing = false;
	
	// Questo controllo serve per evitare loop infiniti impedendo di aggiungere watch durante la fase
	// di visualizzazione
	if( !$debug_is_showing )
	{
		// Aggiunta dell'emento
		array_push( $debug_item_list, $item );
	}
}

/**
 * Cancella la lista delle informazioni di debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @global     Integer   $debug_item_list
 * @return     void
 */
function debug_reset()
{
    global $debug_item_list;
    $debug_item_list = Array();
}



/**
 * Visualizza una variabile
 *
 * La funzione salva il valore della variabile passata per permetterne la visualizzazione al momento della visualizzazione
 * del debug. E' possibile aggiungere un warning level che permette di colorare di diverse tonalità di rosso il messaggio
 * in modo da metterlo in evidenza.
 *
 * @author     Fabrizio Colonna
 * @date       18/07/2006
 * @see        debug_show, debugfx_show_type_variable
 * @param      String    $text Il nome della variabile da salvare (può essere descrittivo)
 * @param      Mixed     $value Il valore da salvare
 * @param      Integer   $w_level Livello di Warning di debug, da 0 a 10
 * @param      Integer   $skip_stack Passi dello stacktrace da saltare
 * @return     void
 */
function debug_watch( $text, $value, $w_level = 0, $skip_stack = 0 )
{
	debug_create_item( 
		"variable", 
		Array( "text" => $text, "value" => $value, "warning" => $w_level ),
		$skip_stack, 2 );
}

/**
 * Imposta un marker nel codice.
 *
 * Imposta un marker nel codice per varie ragioni, per esempio può essere utile determinare se una porzione di codice è
 * stata eseguita oppure no.<br>
 * I marker hanno sfondo azzurro.
 *
 * @author     Fabrizio Colonna
 * @date       24/01/2007
 * @see        debug_show, debugfx_show_type_marker
 * @param      String    $description Descrizione facoltativa del marker
 * @return     void
 */
						
function debug_place_mark( $description = "" )
{
	debug_create_item( "marker", Array( "description" => $description ), 0, 2 );
}

/**
 * Inserisce un avviso di sistema
 *
 * Inserisce un messaggio di sistema nel debug in modo che sia visualizzato all'utente in maniera differente. In genere un
 * messaggio di sistema è un messaggio riguardante una particolare funzione o stato in cui si trova il kernel.<br>
 * I messaggi di sistema hanno un colore verde
 *
 * @author     Fabrizio Colonna
 * @date       24/01/2007
 * @see        debug_show, debugfx_show_type_sysinfo
 * @param      String    $text Testo da visualizzare
 * @return     void
 */
function debug_system_advise( $text )
{
	if( $text == "" )
		return;

	debug_create_item( "sysinfo", Array( "text" => $text ), 0, 2 );
}

/**
 * Inserisce un messaggio di errore
 * 
 * I messaggi di errore sono coloratidì di rosso intenso.
 *
 * @author     Fabrizio Colonna
 * @date       24/01/2007
 * @see        debug_show, debugfx_show_type_variable
 * @param      String    $text Testo da visualizzare
 * @return     void
 */
function debug_error( $text )
{
	debug_create_item( 
		"variable", 
		Array( "text" => "Error", "value" => $text, "warning" => 10 ),
		1, 2
	);
}



/**
 * Avvia un timer di debug
 * 
 * Avvia un timer specifico che può essere utilizzato in molteplici modi, ad esempio per il controllo
 * delle prestazioni del codice. Prima di avviare un timer, lo stesso viene resettato chiamando
 * la funzione {@link debug_timer_reset}.<br>
 * La funzione permette di avviare più timer simultaneamente semplicemente chiamando varie volte
 * la funzione e specificando l'id del timer da avviare. Non passando il parametro $id la funzione cerca
 * il primo identificativo libero e lo assegna al timer. E' sempre consigliato usare l'opzione di default,
 * a meno di casi particolari.<br>
 * Per fermare il timer consultare {@link debug_timer_stop}, mentre per prenderne il conteggio attuale
 * vedere {@link debug_timer_get}.
 *
 * @author     Fabrizio Colonna
 * @date       28/01/2007
 * @see        debug_timer_stop, debug_timer_get, debug_timer_reset
 * @param      Integer    $id Identificativo del timer da avviare
 * @global     Integer    $debug_timers
 * @return     Integer    L'identificativo del timer avviato
 */
function debug_timer_start( $id = -1 )
{
	global $debug_timers;

	if( $id == -1 )
	{
		$id = debug_timer_newid();
	}
	
	// Reset del timer
	debug_timer_reset( $id );
	
	// Avvio il timer
	$debug_timers[$id]["start_time"] = microtime( true );
	unset( $debug_timers[$id]["stop_time"] );
	
	return $id;
}

/**
 * Arresta un timer di debug
 * 
 * Arresta un timer specifico che può essere utilizzato in molteplici modi, ad esempio per il controllo
 * delle prestazioni del codice. La funzione permette di fermare più timer con più chiamate. La
 * funzione restituisce il tempo in secondi dall'avvio del timer con una precisione di 1us. Più
 * chiamate per lo stesso timer (ovvero per lo stesso ID) non avranno nessun effetto, ovverosia un timer
 * può essere bloccato al più una volta e il risultato restituito sarà 0
 *
 * @author     Fabrizio Colonna
 * @date       28/01/2007
 * @see        debug_timer_start, debug_timer_get, debug_timer_reset
 * @param      Integer  $id Identificativo del timer da avviare
 * @global     Integer  $debug_timers
 * @return     float	Il tempo in secondi trascorso dall'avvio del timer
 */
function debug_timer_stop( $id )
{
	global $debug_timers;
	$timer = $debug_timers[$id];

	// Controllo che il bravo programmatore abbia gia inizializzato il timer indicato
	if( empty($timer) || empty($timer["start_time"])  )
	{
		return 0.0;
	}

	// Se il timer è gia stato fermato non aggiorno il contatore
	if( empty($timer["stop_time"]) )
	{
		// Memorizzo il tempo di stop
		$debug_timers[$id]["stop_time"] = microtime( true );
	}

	return debug_timer_get( $id );
}

/**
 * Trova il conteggio del timer
 * 
 * Restituisce un valore float che indica la differenza di tempo trascorso dall'inizio del conteggio
 * del timer al tempo in cui viene chiamata la funzione, oppure, se il timer è stato fermato, il
 * tempo totale per cui il timer ha funzionato. Se il timer specificato non esiste viene restuito
 * il valore 0.
 *
 * @author     Fabrizio Colonna
 * @date       28/01/2007
 * @see        debug_timer_start, debug_timer_stop, debug_timer_reset
 * @param      Integer  $id Identificativo del timer
 * @global     Integer  $debug_timers
 * @return     float	Il tempo in secondi trascorso dall'avvio del timer
 */
function debug_timer_get( $id )
{
	global $debug_timers;
	$timer = $debug_timers[$id];

	// Recupero il tempo corrente
	$stop_time = microtime( true );
	
	// Controllo che il bravo programmatore abbia gia inizializzato il timer indicato
	if( empty($timer) || empty($timer["start_time"]) )
	{
		return 0.0;
	}

	// Se il timer è stato fermato uso lo stop come conteggio
	if( !empty($timer["stop_time"]) )
	{
		$stop_time = $timer["stop_time"];
	}

	// Trovo la differenza di tempo
	$time_diff = $stop_time - (float)$timer["start_time"];
	
	return number_format( $time_diff, 6 );
}

/**
 * Resetta un timer
 * 
 * Imposta il timer specificato come non utilizzato, e quindi permette di poterlo riutilizzare
 *
 * @author     Fabrizio Colonna
 * @see        debug_timer_start, debug_timer_stop, debug_timer_get
 * @date       28/01/2007
 * @param      Integer  $id Identificativo del timer
 * @global     Integer   Contatore dei marks
 * @return     void
 */
function debug_timer_reset( $id )
{
	global $debug_timers;
	
	unset( $debug_timers[$id] );
}

/**
 * Restituisce l'ID di un nuovo timer
 * 
 * Serve per creare nuovi timer a tempo di esecuzione senza sapere a priori il suo id
 *
 * @author     Fabrizio Colonna
 * @date       28/01/2007
 * @global     Integer    $debug_timers
 * @return     Integer    Identificativo del nuovo timer
 */
function debug_timer_newid()
{
	global $debug_timers;
	
	for( $i = 1; isset($debug_timers[$i]); $i++ );
	
	return $i;
}



/**
 * Restituisce un UID
 * 
 * Questa funzione viene utilizzata per generare un numero univoco rispetto alla sessione utilizzabile
 * in vari ambiti.
 *
 * @author     Fabrizio Colonna
 * @date       01/10/2007
 * @return     integer
 */
function debug_get_uid()
{
	global $debug_uid;
	
	$debug_uid = $debug_uid + 1;
	
	return $debug_uid;
}



/**
 * Entra in una sezione logica di codice
 * 
 * Queste funzioni sono utili per marcare porzioni di informazioni di debug come relative
 * ad una certa sezione di codice così da permettere una comoda ricerca in fase di analisi
 * e di filtraggio dei risultati.<br>
 * Le sezioni vengono trattate come uno stack, quindi per tornare indietro ad una data
 * sezione bisogna prima uscire da quelle successive. Inoltre ad ogni chiamata "enter" deve
 * corrispondere una chiamata "exit".
 *
 * @author     Fabrizio Colonna
 * @date       15/09/2007
 * @see        debug_exit_section
 * @param      String  $section Il nome della sezione
 * @global     Array   $debug_sections
 * @return     void
 */
function debug_enter_section( $section )
{
	global $debug_sections;
	array_init( $debug_sections );
	
	array_push( $debug_sections, $section );
}

/**
 * Elimina l'ultima sezione inserita
 * 
 * Queste funzioni sono utili per marcare porzioni di informazioni di debug come relative
 * ad una certa sezione di codice così da permettere una comoda ricerca in fase di analisi
 * e di filtraggio dei risultati.<br>
 * Le sezioni vengono trattate come uno stack, quindi per tornare indietro ad una data
 * sezione bisogna prima uscire da quelle successive. Inoltre ad ogni chiamata "enter" deve
 * corrispondere una chiamata "exit"
 *
 * @author     Fabrizio Colonna
 * @date       15/09/2007
 * @see        debug_enter_section
 * @global     Array   $debug_sections
 * @return     void
 */
function debug_exit_section()
{
	global $debug_sections;
	
	if( empty($debug_sections) )
	{
		return;
	}
	
	array_pop( $debug_sections );
}
?>