<?php
/**
 * General Purprose functions
 *
 * Funzioni di utilizzo generale. Leggere la descrizione di ogni funzione per sapere il relativo comportamento.
 *
 * @package    Kernel
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 */

// Signature
define( "KERNEL_GENERAL", 1 );

/**
 * Taglia una stringa in modo logico.
 *
 * La funzione permette di trocare una frase senza però troncare le singole parole. Data una lunghezza desiderata la
 * stringa verrà tagliata allo spazio successivo alla lunghezza delle parole.
 *
 * @author     Fabrizio Colonna
 * @date       01/12/2005
 * @param      String     $text Il testo da tagliare.
 * @param      Integer    $length La lunghezza a cui tagliare il testo.
 * @param      String     $terminator Terminatore della stringa troncata.
 * @return     String     Il testo tagliato.
 */
function cutout_text( $text, $length, $terminator = "" )
{
	if( strlen($text) <= $length || $length == -1 )
	{
		return $text;
	}

	//$cutted = substr( $text, 0, $length );
	$next_space = strpos( $text, " ", $length );

	if( strlen($text) <= $next_space )
	{
		return $text;
	}

	$out = substr( $text, 0, $next_space );
	$out .= $terminator;

	return $out;
}

/**
 * Converte un testo in HTML
 *
 * Converte un qualsiasi testo semplice in HTML trasformando le entità, rimpiazzando i caratteri di nuova linea con un
 * &lt;br&gt; e gli spazi e le tabulature con un non-breakeable-space.
 *
 * @author     Fabrizio Colonna
 * @date       20/01/2005
 * @param      String     $text Il testo da convertire
 * @return     String     Il testo convertito in HTML
 */
function htmlencode( $text )
{
	$text = htmlentities( $text );
	$text = str_replace( " ", "&nbsp;", $text );
	str_replace( "\t", str_repeat("&nbsp;", 4), $text );
	$text = nl2br( $text );
	return $text;
}

/**
 * Recupera un parametro da un array di sistema bypassando gli indici non definiti
 *
 * La funzione permette di bypassare l'errore ed il relativo codice per evitarlo che si ha quando si tenta di accedere
 * ad una variabile di sistema non definita.
 *
 * @author     Fabrizio Colonna
 * @date       14/12/2005
 * @param      String   $name L'indice da recuperare
 * @param      Array    $array L'array a cui accedere per recuperare le informazioni
 * @param      Mixed    $default Il valore da restituire in caso l'indice non sia stato trovato
 * @return     String   Il valore recuperato
 */
function get_index( $name, $array, $default = null )
{
	if( isset($array[$name]) )
	{
		return $array[$name];
	}

	return $default;
}

/**
 * Inizializza un array
 *
 * Questa semplice funzione permette di inizializzare in maniera veloce, e senza tanti fronzoli nel
 * codice, un semplice array vuoto. Se l'array esiste gia non viene toccato.
 *
 * @author     Fabrizio Colonna
 * @date       26/01/2007
 * @param      String   $array L'array da inizializzare
 * @return     void
 */
function array_init( &$array )
{
	if( empty($array) )
	{
		$array = Array();
	}
}

/**
 * Registra un array
 *
 * Crea una variabile globale per ogni indice dell'array le assegna il valore che aveva nell'array. E' possibile
 * specificare un testo da anteporre al nome della variabile globale creata per poterle raggruppare. Se l'array
 * specificato non è associativo la funzione non registra il suo contenuto.
 *
 * @author     Fabrizio Colonna
 * @date       21/01/2006
 * @param      Array     $array L'array da registrare
 * @param      String    $prefix Il testo da anteporre al nome delle variabili
 * @return     Int       Il numero di variabili registrate
 */
function array_register( $array, $prefix )
{
	$i = 0;
	foreach( $array as $key => $value )
	{
		if( !is_numeric($key) )
		{
			// Costruisco il nome della variabile
			$var_name = $prefix . $key;
			global $$var_name;
			$$var_name = $value;
			$i++;
		}
	}

	return $i;
}

/**
 * Crea una query URL
 *
 * Trasforma un array associativo in una stringa formattata secondo lo standard delle query URI
 *
 * @author     Fabrizio Colonna
 * @date       04/02/2006
 * @param      Array     $array L'array associativo da trasformare
 * @return     Int       Query string derivata dall'array
 */
function make_query_string( $array )
{
	$output = "";

	if( count($array) != 0 )
	{
		foreach( $array as $key => $value )
		{
			$output = "$key=$value&";
		}

		// Elimino l'ultimo "&" eventualmente inserito
		$output = substr( $output, 0, strlen($output) - 1 );
	}

	return $output;
}


/**
 * Stampa un oggetto
 *
 * Stampa un oggetto in una stringa in modo da darne una visualizzazione
 *
 * @author     Fabrizio Colonna
 * @date       04/02/2006
 * @param      Mixed     $ipnut L'oggetto da stampare
 * @return     String    La stringa contenente l'oggetto stampato
 */
function object_to_text( $input )
{
	// Controllo particolare nel caso sia un oggetto
	if( is_array($input) )
	{
		$not_allowed = Array( "GLOBALS" => "GLOBALS" );
		$keys = array_keys( $input );
		$found_keys = array_intersect( $keys, $not_allowed );

		if( !empty($found_keys) )
		{
			// Non può essere visualizzato l'array $_GLOBAL
			$input = array_diff_key( $input, $not_allowed );
		}
		
		return "";
	}
	
	return var_export( $input, true );
}

/**
 * Esegue l'escape di una stringa per regexp
 * 
 * La funzione permette di inserire una stringa contenente una Regular Expression all'interno di
 * un pattern di un'altra Regular Expression eseguendo un opportuno escpaing.
 *
 * @author     filippo dot toso at humanprofile dot biz (02-Aug-2006 11:11)
 * @date       05/10/2007
 * @param      String    $content La stringa da controllare
 * @return     String    La stringa correttamente formata
 */
function safe_preg( $content )
{
	$search  = array( '\\\\', '^', '$', '.', '[', ']', '|', '(', ')', '?', '*', '+', '{', '}' );
	$replace = array( '\\\\\\\\', '\\^', '\\$', '\\.', '\\[', '\\]', '\\|', '\\(', '\\)', '\\?', '\\*', '\\+', '\\{', '\\}' );
	return str_replace( $search, $replace, $content );
}

/**
 * Recupero dati
 *
 * Recupera i dati inviati tramite GET e POST e li inserisce in variabili globali accessibili. Questo sistema è un po'
 * come il register_globals (che è stato disabilitato per motivi di sicurezza), ma più avanzato e più sicuro.
 * Le nuove variabili avranno nome param_xxx, dove al posto di xxx c'è il nome della variabile. E' possibile cambiare il
 * prefisso del nome nel file di configurazione.
 *
 * @author     Fabrizio Colonna
 * @date       12/01/2006
 * @return     void
 */
function init_parameters()
{
	global $CONFIG;

	$types = Array( "get", "post", "session", "server", "cookie", "env" );

	foreach( $types as $type )
	{
		// Recuper l'array globale
		$global_type = "_" . strtoupper( $type );
		$global_type = @$GLOBALS[$global_type];

		if( isset( $CONFIG["register"][$type] ) )
		{
			// Registro le variabili dell'array globale recuperato
			if( $global_type != null && isset($CONFIG["register"][$type]) )
			{
				array_register( $global_type, $CONFIG["register"]["{$type}_prefix"] );
			}
		}
	}
}

/**
 * Richiama una funzione esterna al codice
 *
 * Richiama una funzione che non necessariamente esiste gia. Se non esiste la funzione prova a
 * caricarla dal file che viene specificato. Per motivi di efficienza la funzione tiene traccia di
 * quali file sono presenti in una variabile globale.
 * 
 * <bNOTA:</b> La funzione esegue un controllo solo sul nome relativo, e non assoluto, del file che
 * tenta di caricare, quindi prestate attenzione ai casi di omonimia.
 *
 * @author     Fabrizio Colonna
 * @date       05/06/2006
 * @param      String    $file Il file in cui cercare la funzione
 * @param      String    $function Il nome della funzione da eseguire
 * @param      Array     $params L'array dei parametri da passare alla funzione
 * @param      Mixed     $return Il valore di ritorno della funzione chiamata. Valido solo se la funzione ritorna true
 * @return     Boolean   True se la funzione è stata chiamata con successo, False altrimenti
 */
function call_ext_function( $file, $function, $params, &$return )
{
   	global $callext_missed;
   	array_init( $callext_missed );
   	
   	debug_system_advise( "Called external function. Checking if exists..." );
   	
    // Per evitare di fare un'inclusione inutile controllo se la funzione c'è gia
    if( function_exists($function) )
    {
        $return = call_user_func_array( $function, $params );
    }
    else
    {
    	// Se il file non è stato specificato si esce
    	if( $file == "" )
    	{
    		return false;
    	}
    	
		// Controllo di non aver gia provato a caricare questo file
		if( in_array($file, $callext_missed) )
	    {
	        return false;
	    }
	
        // In modo che sia comparabile
        $file_path = realpath( $file );
        
        // $file vale false se il file a cui faceva riferimento non esiste
        if( $file_path == false || !is_file($file_path) )
        {
        	array_push( $callext_missed, $file );
	        return false;
        }

        // Recupero la lista dei file inclusi
        $included_files = get_included_files();

        // Se il file è gia stato incluso e non ho trovato la funzione non posso fare tanto...
        if( !in_array($file_path, $included_files) )
        {
            // Includo il file dove c'è il gestore, e se lo trovo lo eseguo.
            include_once( $file_path );

            // Controllo che la funzione esista nel file incluso
            if( function_exists($function) )
            {
		    	//debug_timer_start(2);
                $return = call_user_func_array( $function, $params );
            }
            else
            {
        		array_push( $callext_missed, $file );
    	        return false;
            }
        }
        else
        {
        	array_push( $callext_missed, $file );
            return false;
        }
    }

    return true;
}
?>