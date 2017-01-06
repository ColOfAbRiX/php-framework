<?php
/**
 * Funzioni di elaborazioni del database
 *
 * Questo file espone le funzioni per l'utilizzo del database come la connessione
 * e l'esecuzioni di query sul database. Le funzioni di questo file memorizzano
 * una connessione nella variabile globale $DATABASE che usano come connessione di
 * default. E' comunque possibile non utilizzare questa variabile globale ed usare
 * una connessione alternativa. Se ne la connessione di default ne la connessione
 * alternativa sono specificate viene creta sul momento una nuova connessione non
 * persistente, ovvero la connessione viene utilizzata e poi subito richiusa.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @see        config.php
 * @see        debug.php
 * @see        queries.php
 */

// Signature
define( "KERNEL_ADMIN_DATABASE", 1 );
// Dipendenze
//require_once( dirname(__FILE__)."/../lib/pear/DB.php" );
require_once( dirname(__FILE__)."/config.php" );
require_once( dirname(__FILE__)."/queries.php" );
require_once( dirname(__FILE__)."/debug.php" );
require_once( dirname(__FILE__)."/../libs/pear/DB.php" );

/**
 * Apre la connessione al database utilizzando i parametri di config.php.
 *
 * La funzione crea una connessione al database utilizzando i parametri della sezione database nel file di configurazione
 * {@link config.php}. Viene scelto come modo di restituzione dei dati un array associativo e vengono abilitate tutte le
 * opzioni di portabilità di PEAR {@link http://pear.php.net/manual/en/package.database.db.php PEAR::DB}.
 * Se l'impostazione di connessione selettiva è attivata non è possibile collegarsi al database  tramite questa funzione,
 * a meno di passare $hide_selective = true. Nel caso di connessione selettiva è sufficiente richiamare una funzione per
 * il recupero dei dati come {@link db_execute} o {@link db_execute_array}. Per disconnetters invece bisogna comunque
 * chiamare {@link db_disconnect}.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @global     Array    Parametri di configurazione
 * @param      bool     $set_global Indica se ipostare la connessione come default.
 * @param      bool     $hide_selective Serve per nascondere l'opzione della connessione selettiva.
 * @return     PEAR::DB La connessione al database
 */
function db_connect( $set_global = true, $hide_selective = false )
{
	global $CONFIG;

	debug_system_advise( "Database connecting..." );

	$dsn = "{$CONFIG["db"]["type"]}://{$CONFIG["db"]["usr"]}:{$CONFIG["db"]["pwd"]}@{$CONFIG["db"]["host"]}/{$CONFIG["db"]["name"]}";

	if( $CONFIG["debug"] )
	{
		debug_watch( "DB Connection String", $dsn );
	}

	// Se la connessione è selettiva ci si collega solamente chiamando db_execute()
	if( $CONFIG["db"]["selective"] && !$hide_selective )
	{
		return null;
	}

	// Connessione al database
	$db = DB::connect( $dsn );
	pear_check( $db );

	// Opzioni per comatibilità massima
	//$db->setOptions( "portability", DB_PORTABILITY_ALL ); // Sembra non funzionare
	// Connessione persistente
	$db->setOption( "persistent", $CONFIG["db"]["persistent"] );
	// Modo di recupero con array associativi
	$db->setFetchMode( DB_FETCHMODE_ASSOC );

	// Imposta l'oggetto appena creato come database di default
	if( $set_global )
	{
		set_global_db( $db );
	}

	return $db;
}

/**
 * Disconnessione dal database specificato.
 *
 * La funzione chiude la connessione al database. Se non ne viene specificato uno viene utilizzato il database globale di
 * defalt, in caso contrario il database globale viene disconnesso solo se nelle impostazioni non è attiva l'opzione di
 * connessione persistente.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @see        config.php
 * @param      PEAR::DB $db L'oggetto che fa riferimento alla connessione aperta.
 * @global     PEAR::DB Connession di default al database
 * @global     Array    Parametri di configurazione
 * @return     void
 */
function db_disconnect( &$db = null )
{
	global $DATABASE, $CONFIG;

	// Se è presente disconnetto la connessione specificata
	if( is_a($db, "PEAR") )
	{
		$db->disconnect();
		$db = null;
		return;
	}

	// Mi disonnetto solo se la connessione non è persistente
	if( !$CONFIG["db"]["persistent"] )
	{
		// Disconnetto la connessione globale
		if( is_a($DATABASE, "PEAR") )
		{
			$DATABASE->disconnect();
			$DATABASE = null;
		}
	}

    debug_watch( "database", "disconnected" );
}

/**
 * Imposta come database di default il database specificato.
 *
 * @author     Fabrizio Colonna
 * @date       30/11/2005
 * @global     PEAR::DB Connession di default al database
 * @param      PEAR::DB $db L'oggetto che fa riferimento alla connessione aperta.
 * @return     void
 */
function set_global_db( $db )
{
	global $DATABASE;
	$DATABASE = $db;
}

/**
 * Recupera il database globale di default.
 *
 * Se non esiste ancora un database di defaul la funzione si preoccupa di crearne uno e poi restituirlo. Predispone ad
 * upgradare il sito utilizzando i paradigmi della OOP.
 *
 * @author     Fabrizio Colonna
 * @date       19/
 * @global     PEAR::DB Connession di default al database
 * @return     PEAR::DB Il database di default
 */
function get_global_db()
{
	global $DATABASE;

	$db = $DATABASE;

	// Se il database non c'è lo creo
	if( !is_object($db) )
	{
		$db = db_connect( true, true );		// Si toglie l'impostazione di connessione selettiva
	}

	return $db;
}

/**
 * Esegue una query parametrizzata.
 *
 * Esegue la query specificata sostituendoci i parametri indicati nel secondo parametro. Per il formato dei parametri
 * nella query vedere la documentazione di PEAR {@link http://pear.php.net/manual/en/package.database.db.db-common.execute.php
 * DB->execute() }. Ogni parametro passato subisce un escaping al fine di evitare problemi con la sicurezza derivanti da
 * SQL-Injiection, e per questo i parametri passati. non devo avere gia subito un escapging.
 *
 * Variabili di debug: $parameters, $executed_query.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @global     PEAR::DB Connession di default al database
 * @global     Array    Parametri di configurazione
 * @param      String   $query La query SQL da eseguire
 * @param      Array    $parameters Array di parametri da sostituire nella query
 * @param      PEAR::DB $db Connessione al database da usare
 * @return     PEAR::DB_Result
 */
function db_execute( $query, $parameters = Array(), $db = null )
{
	global $DATABASE, $CONFIG;

	$volatile = (bool)$CONFIG["db"]["selective"];

	// Gestione parametri in ingresso
	if( !is_object($db) )
	{
		$db = $DATABASE;
	}
	if( !is_object($db) )
	{
		$db = db_connect( !$volatile, true );		// Si toglie l'impostazione di connessione selettiva
	}

	// NOTA: L'escaping dei parametri di ingresso per aumentare la sicurezza non è necessario perchè se ne occupa la
	// funzione prepare

	// Preparo ed eseguo la query sostituendo i parametri
	$sth = $db->prepare( $query );
	$out = $db->execute( $sth, $parameters );

	if( $CONFIG["debug"] )
	{
		debug_watch( "Executed Query", $db->last_query );
	}

	pear_check( $out );

	// Connessione non persistente
	if( $volatile )
	{
		db_disconnect( $db );
	}

	return $out;
}

/**
 * Esegue una query parametrizzata e restituisce il risultato come array.
 *
 * La funzione esegue la stessa operazione di {@link db_execute} ma differisce per il formato restituito. Vedere le
 * considerazioni fatte in proposito a {@link db_execute}. Questa funzione restituisce un array di record.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @see        db_execute
 * @param      String   $query La query SQL da eseguire
 * @param      Array    $parameters Array di parametri da sostituire nella query
 * @param      PEAR::DB $db Connessione al database da usare
 * @return     Array    L'array del set recuperato
 */
function db_execute_array( $query, $parameters = Array(), $db = null )
{
	$output = Array();
	$i = 0;

	$data_set = db_execute( $query, $parameters, $db );

	// Trasformo il risultato in un array
	while( $data_set->fetchInto($output[$i++]) );
	if( $output[$i - 1] == null )
	{
		unset( $output[$i - 1] );
	}

	return $output;
}

/**
 * Esegue una query parametrizzata che restituisce uno scalare
 *
 * La funzione esegue la stessa operazione di {@link db_execute} ma differisce per il formato restituito. Vedere le
 * considerazioni fatte in proposito a {@link db_execute}. Questa funzione restituisce un array di record.
 *
 * @author     Fabrizio Colonna
 * @date       29/11/2005
 * @see        db_execute
 * @param      String   $query La query SQL da eseguire
 * @param      Array    $parameters Array di parametri da sostituire nella query
 * @param      PEAR::DB $db Connessione al database da usare
 * @return     Mixed    Risultato scalare del valore recuperato
 */
function db_execute_scalar( $query, $parameters = Array(), $db = null )
{
	$data_set = db_execute( $query, $parameters, $db );
	$data_set->fetchInto( $output );
	
	return $output;
}
?>