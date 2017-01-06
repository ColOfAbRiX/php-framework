<?php
/**
 * Funzioni di visualizzazione delle news
 *
 * Contiene le funzioni per la visualizzazione da parte dell'utente delle news.
 * Permette un alto grado di adattamento per consentire di creare molti pattern
 * diversi di news utilizzando un template per la singola news ed un altro
 * template per la visualizzazione complessiva.
 * Fare riferimento alla sezione "News" all'interno del file di configurazione
 * per sapere come configurare il comportamento delle funzioni.
 *
 * <b>Note sul formato del pattern</b> Il pattern di ripetizione delle news è
 * composto da più elementi separati tra loro da un punto e virgola. Ogni elemento
 * ha il seguente formato: <start>,<type>[/num],<end>
 * - <start>: La news di partenza
 * - <type> : Il tipo di visualizzazione (full, reduced, list)
 * - [/num] : Il numero di news in un singolo template. Opzionale, se assente si considera 1
 * - <end>  : La news finale
 * Se <start> non è presente viene preso pari a zero e template "full"; se non è
 * presente <end> l'algoritmo va avanti fino alla fine delle news. Sono possibili delle
 * sovrapposizioni tra i vari <start> ed <end> dei vari elementi, l'ultimo elemento
 * della stringa ha la priorità. Ecco un esempio di pattern:
 *   "0,full,5;-,list,10;6,reduced/2,10;-,list,-".
 * Il significato di questo pattern è: elementi da 1 a 5 full, elementi da 6 a 10 reduced,
 * i restanti list. Gli elementi list dal 6 al 10 sono rimpiazzati da quelli reduced. Un
 * template reduced richiede 2 news.
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
define( "KERNEL_USER_NEWS", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../kernel/database.php" );
require_once( dirname(__FILE__)."/../kernel/template_engine.php" );
require_once( dirname(__FILE__)."/../kernel/config.php" );

/**
 * Compila un pattern di news.
 *
 * Ovvero passa dalla visualizzazione user-friendly a quella php-friendly; per una descrizione
 * del formato di output vedere {@link get_all_news}.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @see        get_all_news
 * @param      String   $text_pattern Il pattern da compilare.
 * @return     Array    L'array contenente il pattern.
 */
function compile_pattern( $text_pattern )
{
	$sections = explode( ";", $text_pattern );
	$pattern = Array();
	$last_start = 0;

	for( $i = 0; $i < count($sections); $i++ )
	{
		list( $start, $tmpl, $end ) = explode( ",", $sections[$i] );

		// Controllo gli indici particolari
		if( !is_numeric($start) ) {
			$start = $last_start;
		}
		if( !is_numeric($end) ) {
			$end = $start + 1;
		}

		for( $j = (int)$start; $j < (int)$end; $j++ ) {
			$pattern[$j] = $tmpl;
		}

		// Memorizzo l'ultimo indice di partenza
		$last_start = $end;
	}

	return $pattern;
}

/**
 * Crea un box di una news con lo stile specificato.
 *
 * La funzione è in grado di creare box che richiedono più di una news. Le variabili
 * all'interno del template hanno nomi che terminano con _<n>, dove <n> rappresenta
 * il numero della news a cui fa riferimento. Viene sempre e comunque creato un set
 * di variabili per la prima news che non termina con _<n> per aumentare la
 * chiarezza all'interno del template.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     Array    Parametri di configurazione
 * @param      Array    $news Un array di dataset che contiengono le news.
 * @param      String   $tpye Stile di visualizzazione della news (full, reduced, list).
 * @return     String   Restituisce la news creata
 */
function get_news_box( $news, $type )
{
	// Passando un array in cui in ogni indice c'è un PEAR::DB_Result si possono
	// visualizzare più news in un solo template
	global $CONFIG;
	$tmpl_vars = Array();

	// Rendo uniforme il formato delle news, in modo da scrivere meno codice
	if( empty($news[0]) )
	{
		$news = Array( $news );
	}

	for( $i = -1; $i < count($news); $i++ )
	{
		// Devo ripetere il primo indice con un nome senza "_x"
		$current_news = ($i != -1) ? $news[$i] : $news[0];
		$index = ($i != -1) ? "_".($i + 1) : "";

		// Array dei nomi delle variabili nel template
		$names_array = Array( "news_id$index"   , "news_title$index"  ,
		                      "news_date$index" , "news_content$index",
		                      "news_image$index", "news_section$index" );

		// Array dei valori delle variabili nel template
		$values_array = Array($current_news["id_news"],
		                      $current_news["title"],
		                      date( $CONFIG["date_format"], (int)$current_news["date"] ),
		                      cutout_text($current_news["content"], $CONFIG["news"]["text_cutout"]),
		                      $current_news["image"],
		                      $current_news["category"] );

		// Creo un array combinando i nomi con i valori e li aggiungo all'array dei valori
		$tmp = array_combine( $names_array, $values_array );
		$tmpl_vars = array_merge( $tmpl_vars, $tmp );
	}

	// Rendo disponibile il numero di news all'interno del template
	$tmpl_vars = array_merge( $tmpl_vars, Array("news_count" => count($news)) );

	// Creo il template riempito
	$output = build_template( $CONFIG["news"]["tmpl_$type"], $tmpl_vars );
	return $output ;
}

/**
 * Visualizza una news completamente.
 *
 * Crea una pagina basandosi sul template $CONFIG["news_complete"] che permette
 * di visualizzare una news completa in ogni sua parte a pagina intera.
 *
 * @author     Fabrizio Colonna
 * @date       04/12/2005
 * @global     Array    Parametri di configurazione
 * @param      PEAR::DB_Result $news La news da visualizzare
 * @return     String   La pagina contenente la news
 */
function build_complete_news( $news )
{
	global $CONFIG;

	if( count($news) == 0 )
	{
		trigger_error( "Non esiste la news richiesta" );
	}

	// Array dei nomi delle variabili nel template
	$tmpl_vars = Array( "news_id"      =>  $news["id_news"],
	                    "news_title"   =>  $news["title"],
	                    "news_date"    =>  date( $CONFIG["date_format"], (int)$news["date"] ),
	                    "news_content" =>  $news["content"],
	                    "news_image"   =>  $news["image"],
	                    "news_section" =>  $news["category"]
	);

	// Creo il template riempito
	$output = build_template( $CONFIG["news"]["tmpl_complete"], $tmpl_vars );

	return $output;
}

/**
 * Recupera una news dal database a partire dal suo id.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     PEAR::DB Connession di default al database
 * @global     Array    Insieme delle query SQL del sito
 * @param      Integer  $newsId L'id della news da recuperare
 * @return     PEAR::DB_Result Il dataset che contiene la news recuperata.
 */
function get_news_result_by_id( $newsId )
{
	global $SQL;

	// Recupero le informazioni dal database
	$news = db_execute( $SQL["news_by_id"], Array($newsId) );
	pear_check( $news );

	// In caso la news non venga trovata non visualizzo niente, salto la news
	if( $news->numRows() == 0 )
	{
		trigger_error( "La news $newsId non esiste!", E_USER_ERROR );
	}

	return $news->fetchRow();
}

/**
 * Crea una struttura dati contente tutte le news suddivise per stili.
 *
 * Formato dell'<b>array di input</b>: ogni indice corrisponde ad una news e  in ogni indice
 * è presente lo stile della news e il numero di news da caricare in ogni template nella forma
 * template/numero.
 * L'<b>array di uscita</b> ha il seguente formato: è un array a due dimensioni, la prima dimensione
 * corrisponde ad un elemento del pattern stringa, ovvero ad uno stile, la seconda dimensione contiene
 * tutte le news formattate con lo stile dell'indice della prima dimensione a cui appartiene.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     Array    Parametri di configurazione
 * @global     Array    Insieme delle query SQL del sito
 * @param      Array    $pattern Il pattern per la visualizzazione in stili misti. Vedi note per il formato.
 * @return     Array    Struttura di array contenente le news suddivise per stili
 */
function get_all_news( $pattern )
{
	global $CONFIG, $SQL;
	$last_pattern = "full/1";
	$output = $create = Array();
	$i = $iType = 0;
	$i_type = $i_news = $last_news = 0;

	// Recupero le news dal database
	$news = db_execute_array( $SQL["news_all"], Array($CONFIG["news"]["max"]) );

	// Nota: il formato di $news e di $pattern non possono essere uguali perchè il pattern
	// può avere dei buchi e delle sovrapposizioni, mentre le news sono per forza continue

	for( $i = 0; $i < count($news); $i++, $i_news++ )
	{
		if( empty($news[$i]) ) { continue; }		// Per via di errori nella lettura del database

		// Il pattern può non cominciare da zero oppure può avere dei buchi
		if( empty($pattern[$i]) )
		{
			$pattern[$i] = $last_pattern;
		}

		// Cambiamento di pattern, quindi un nuovo indice dei tipi e reset di quello delle news
		if( $pattern[$i] != $last_pattern && $i != 0 )
		{
			if( $last_news >= 1 )
			{
				// Il numero delle news non è divisibile per il numero di news per template
				$output[$i_type][$i_news] = get_news_box( $create, $tmpl );
				$create = Array();
				$last_news = 0;
			}
			$i_type++;
			$i_news = 0;
		}

		// Creazione di un nuovo tipo nell'array di output
		if( count($output) - 1 < $i_type ) {
			$output[$i_type] = Array();
		}

		// Recupero template e news per template
		if( $last_news == 0 )
		{
			list( $tmpl, $last_news ) = explode( "/", $pattern[$i] . "/" );
			if( empty($last_news) ) $last_news = "1";
			$last_news = (int)$last_news;
		}

		// Tengo memoria di tutte le news
		array_push( $create, $news[$i] );

		if( $last_news == 1 )
		{
			// Posso visualizzare le news
			$output[$i_type][$i_news] = get_news_box( $create, $tmpl );
			$create = Array();
		}
		else
		{
			$i_news--;
		}

		$last_news--;
		$last_pattern = $pattern[$i];
	}

	return $output;
}

/**
 * Restituisce il riquadro di tutte le news correttamente formattato.
 *
 * La funzione esegue la query news_all (vedi queries.php) per recuperare tutte le news
 * e utilizza quindi la funzione {@link get_all_news} per creare una struttura dati
 * contenente tutte le news suddivise per stile di template. La funzione inserisce i
 * gruppi di news nel template $CONFIG["news_frame"].
 *
 * Variabili di debug: $news_pattern.
 *
 * @author     Fabrizio Colonna
 * @date       01/12/2005
 * @see        queries.php
 * @global     Array    Parametri di configurazione
 * @return     String   Il codice HTML contenente tutte le news formattate secondo i template
 */
function build_news()
{
	global $CONFIG;

	debug_watch( "\$debug_pattern", $CONFIG["news"]["pattern"] );

	// Recupero tutte le news secondo il pattern specificato
	$pattern = compile_pattern( $CONFIG["news"]["pattern"] );
	$all_news = get_all_news( $pattern );

	for( $i = 0; $i < count($all_news); $i++ )
	{
		$all_news[$i] = implode( "", $all_news[$i] );
	}

	return build_template( $CONFIG["news"]["tmpl_frame"], Array("news_box" => $all_news) );
}
?>