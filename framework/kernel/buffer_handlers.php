<?php
/**
 * Gestori dei buffer levels
 *
 * Contiene le funzioni per la gestione dei livelli di buffer predefiniti, i livelli user e system.
 * Il livello user comprende la creazione del layout della pagina e dell'inserimento del contenuto. Il livello system
 * prevede l'aggiunta delle intestazioni di pagina al codice HTML.
 * 
 * Ogni gestore del buffer è una funzione che richiede un solo paramentro in cui verrà messo il contenuto del buffer
 * appena chiuso, e in output restituisce il nuovo contenuto del buffer:
 * <samp>    String buffer_handler( String )</samp>
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2007, Fabrizio Colonna
 */
// Signature
define( "KERNEL_BUFFER_HANDLERS", 1 );

/**
 * Supervisione dell'output
 *
 * La funzione si occupa di supervisionare l'output: utilizza la codifica specificata, comprime il contenuto se la
 * relativa opzione è attiva e invia le intestazioni HTTP standard (Content-Encoding, Content-Type, Content-Length,
 * Content-Language, Content-MD5, Date, Expires, Last-Modified, Pragma ).<br>
 * Attenzione, all'interno di questa fuzione non è possisibile utilizzare le funzioni di debug.
 *
 * @author     Fabrizio Colonna
 * @date       17/01/2006
 * @see        buffer.php, application.php
 * @param      String    $buffer Il contenuto del buffer appena chiuso
 * @global     Array     $CONFIG
 * @return     String    L'output da inviare al browser
 */
function system_buffer_handler( $buffer )
{
	global $CONFIG;

	//list( $in, $out, $int ) = iconv_get_encoding();
	//$buffer = iconv( $int, $CONFIG["encoding"], $buffer );

	// Abilitazione della compressione
	if( $CONFIG["compression"] && can_compress() )
	{
		header( "Content-Encoding: gzip" );

		// Code from phpBB
        $gzip_contents = $buffer;
        $gzip_size = strlen($gzip_contents);
        $gzip_crc = crc32($gzip_contents);
        $gzip_contents = gzcompress($gzip_contents, $CONFIG["compression_level"]);
        $gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

        $buffer = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $buffer .= $gzip_contents;
        $buffer .= pack('V', $gzip_crc);
        $buffer .= pack('V', $gzip_size);
	}
	else
	{
		header( "Content-Encoding: {$CONFIG["encoding"]}" );
	}

	// Invio intestazioni standard
	$len = strlen( $buffer );
	$md5 = md5( $buffer );
	$date = gmdate("D, d M Y H:i:s");

	header( "Content-Type: text/html" );
	header( "Content-Length: $len" );
	header( "Content-Language: {$CONFIG["language"]}" );
	header( "Content-MD5: $md5" );
	header( "Date: $date" );

	// Disabilitazione cache del browser
	if( !$CONFIG["browser_cache"] )
	{
	    header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
	    header( "Last-Modified: $date GMT" );
	    header( "Cache-Control: no-cache, must-revalidate" );
	    header( "Pragma: no-cache" );
	}

	// Messaggio di warning se sono in debug
	if( $CONFIG["debug"] )
	{
		header( "Warning: 999 server 'Debug mode is active' $date GMT" );
	}

	return $buffer;
}

/**
 * Gestione del contenuto
 *
 * La funzione gestisce l'output inviato dal sito richiamando le funzioni standard per creare correttamente la struttura
 * grafica del sito.<br>
 * <b>NOTA:</b> all'interno di questa fuzione non è possisibile utilizzare le funzioni di debug.
 *
 * @author     Fabrizio Colonna
 * @date       17/01/2006
 * @global     Array     Parametri di configurazione
 * @param      String    $buffer Il contenuto del buffer
 * @return     String    L'output da inviare al browser
 */
function user_buffer_handler( $buffer )
{
	global $CONFIG;

	// Costruisco la pagina
	$buffer = build_template(
		$CONFIG["tmpl"]["layout"],
		Array("content" => $buffer)
	);

	return $buffer;
}

/**
 * Indica se è possibile usare la compressione dei dati in output.
 *
 * Controlla se è possibile comprimere i dati prima di inviarli al client. Se manca una libreria la funzione tenta di
 * caricarla, se non vi riesce termina. La funzione non è considerata di utilizzo generale perchè il suo unico scopo è
 * relativo a questo file.
 *
 * @author     Fabrizio Colonna
 * @date       12/01/2006
 * @return     Bool      TRUE se è possibile comprimere, FALSE altrimenti
 */
function can_compress()
{
	if( !(bool)$_COOKIE["can_compress"] )				// Uso un cookie per non fare ogni volta una serie di controlli
	{
		// Il browser non accetta la compressione
		if( !strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") )
		{
			//return false;
		}

		// La libreria di compressione non è caricata
		if( !extension_loaded("zlib") )
		{
			// Le impostazioni di protezione di php.ini non permettono il caricamento
			if( (bool)ini_get("enable_dl") && !(bool)ini_get("safe_mode") )
			{
				return false;
			}

			// Provo a caricare l'estensione per la compressione
			$lib = "zlib.so";
			if( strtoupper(substr(PHP_OS, 0, 3) == "WIN" ))
			{
				$lib = "zlib.dll";
			}

			// Ci sono altri problemi durante il caricamento
			if( !dl($lib) )
			{
				return false;
			}
		}

		// Imposto il cookie per ricordarmi che su questo client è possibile comprimere
		setcookie( "can_compress", "1", time() + 60 * 60 * 24 * 1, "/" );		// Il cookie scade in un giorno
	}

	return true;
}
?>