<?php
/**
 * Sistema di buffering
 *
 * In questo file sono racchiuse tutte le funzioni per creare e gestire componenti HTML, come possono
 * essere delle form o delle tabelle
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 */

// Signature
define( "KERNEL_BUFFERING", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../general.php" );
require_once( dirname(__FILE__)."/debug.php" );

/**
 * Avvia il sistema di buffering dell'output
 *
 * @author     Fabrizio Colonna
 * @date       04/05/2006
 * @return     void
 */
function buffer_start()
{
    global $buffer_handlers;

    debug_system_advise( "Buffer starting..." );

    $buffer_handlers = Array( null );
    $buffer_handlers[1] = Array( "priorities" => Array() ); // Per il livello di buffering di default, il pi? basso
}

/**
 * Commissiona l'esecuzione di tutti i livelli di buffering.
 *
 * Una volta mandato eseguiti i suoi compiti riporta il sistema nelle condizioni iniziali.
 *
 * @author     Marco Lazzaretti
 * @date       22/05/2006
 * @return     void
 */
function buffer_commit()
{
    global $buffer_handlers;
    $levels = count($buffer_handlers);

    debug_system_advise( "Buffer committing all levels..." );

    for( $i = 1; $i < $levels; $i++)
    {
        $tmp = buffer_commit_level();
        echo( $tmp );
    }
    
    // Non so che succede, ma questo evita dei problemi che non fanno visualizzare niente
	//ob_end_flush();
	echo( $tmp );
	
    // Reinizializzo il sistema
    buffer_start();
}

/**
 * Termina il sistema di buffering
 *
 * La funzione elimina tutti i cambiamenti avvenuti dall'ultima commit e non reinizializza il sistema. Si rende necessaria
 * una nuova chiamata a buffer_start
 *
 * @author     Fabrizio Colonna
 * @date       23/05/2006
 * @return     void
 */
function buffer_end()
{
    global $buffer_handlers;
    //$levels = count($buffer_handlers);
    $levels = ob_get_level();

    for( $i = 2; $i <= $levels; $i++)
    {
        ob_end_clean();
    }

    unset( $buffer_handlers );
}

/**
 * Avvia il sistema di buffering dell'output
 *
 * @author     	Marco Lazzaretti
 * @date       	04/05/2006
 * @global      Array      L'insieme di tutti i gestori degli errori per i livelli di buffering
 * @param       Integer    $level Il livello al quale vogliamo inserire il nuovo buffer
 * @param       String     $initial_value La stringa che contiene il valore iniziale del buffer
 * @param       String     $buffer_handler Il gestore associato al nuovo buffer
 * @param       String     $handler_file Il nome del file che contiene la funzione handler specificata
 * @return     	void
 */
function buffer_add_level( $initial_value = "", $buffer_handler = "", $handler_file = "", $level = -1 )
{
    global $buffer_handlers;

	//se l'utente introduce un livello troppo basso, lo metto come primo livello utente
	if( ($level < 1) && ($level != -1) ) $level = 1;

	$last_level = ob_get_level();
    $level_difference = $last_level - $level;

    // se il livello ? pari a -1 (valore di default) oppure oltre l'ultimo creato,
	// il livello del nuovo buffer deve essere impostato al livello inferiore
	// rispetto all'ultimo
	if( ($level >= $last_level) || ($level == -1) )
	{
		ob_start();
		$buffer_handlers[$last_level + 1] = Array( "priorities" => Array() );
		buffer_add_handler( $buffer_handler, $handler_file, $last_level + 1 );
		echo( $initial_value );
	}
	// altrimenti dobbiamo andare a inserire il nostro nuovo buffer prima di quelli che lo
	// devono seguire
	else
	{
		$tmp_buf = Array();
		for( $i = 0; $i < $level_difference; $i++)
		{
			// inserisco nell'ultimo elemento dell'array il contenuto del livello a priorit?
			// maggiore tra i buffer e lo elimino
			$tmp_buf[$level_difference - $i] = ob_get_clean();
		}

		// prima di ricostruire la struttura dei buffer devo inserire il nuovo gestore
		// nell'array degli handlers, perci? spezzo l'array in due parti (la prima con $level elementi,
		// la seconda con la differenza tra il numero di elementi di $buffer_handlers e $level elementi),
		// $first_half e $second_half e ci inserisco in mezzo il nuovo gestore, quindi faccio il
		// merge delle due parti per ricomporre l'array $buffer_handlers

		$first_half = array_slice( $buffer_handlers, 0, $level );
		$second_half = array_slice( $buffer_handlers, $level, count($buffer_handlers) );

		//array_push( $first_half, $buffer_handler );
		array_push( $first_half, Array() );

		// Ora l'array di array buffer_handlers pu? essere ricomposto
		$buffer_handlers = array_merge( $first_half, $second_half );

		// a questo punto abbiamo in mano un array che contiene tutti i dati dei buffer ai livelli
		// che hanno indice inferiore rispetto a quello che dobbiamo inserire
		buffer_add_handler( $buffer_handler, $handler_file, $level );
		ob_start();
		echo( $initial_value );

		// a questo punto possiamo reinserire i buffer che devono adesso seguire il nuovo
		for( $i = 0; $i < $level_difference; $i++ )
		{
			// ricreo il buffer che viene gestito dal nostro sistema e non da php
            ob_start();
            // valorizzazione del buffer
            echo( $tmp_buf[$i] );
		}
	}
}

/**
 * Commissiona l'esecuzione di un livello di buffering
 *
 * @author     Marco Lazzaretti
 * @date       04/05/2006
 * @return     String    Il contenuto del livello di buffer.
 */
function buffer_commit_level()
{
    global $buffer_handlers;

    debug_system_advise( "Buffer handling level " . ob_get_level() );

    // Per prima cosa riportiamo l'output al livello di buffering superiore
	$tmp = buffer_call_handlers();
	ob_end_clean();

	// Ora aggiorniamo la struttura degli handlers
	array_pop($buffer_handlers);
	return $tmp;
}

/**
 * Elimina il contenuto dell'ultimo livello di buffering
 *
 * @author     Fabrizio Colonna
 * @date       10/05/2006
 * @return     void
 */
function buffer_clear_level()
{
    ob_clean();
}

/**
 * Aggiunge un gestore di buffer
 *
 * Aggiunge un gestore della terminazione di buffering del livello specificato. Tra tutti i gestori assegnati ad un
 * livello di buffer, l'ultimo inserito ha la priorit? maggiore.
 * Se $level ? inferiore a 1 viene impostato a 1, se superiore all'ultimo livello amissibile viene considerato pari a
 * questo.
 *
 * @author     Fabrizio Colonna
 * @global     Array     L'insieme di tutti i gestori degli errori per i livelli di buffering
 * @param      String    $handler Il nome della funzione che gestisce il livello di buffering
 * @param      String    $handler Il nome della funzione che gestisce il livello di buffering
 * @param      Integer   $level Il livello a cui assegnare l'handler
 * @date       04/05/2006
 * @return     void
 */
function buffer_add_handler( $handler, $handler_file, $level = -1 )
{
    global $buffer_handlers;

    // Controllo della validit? dei dati in ingresso
    if( $level < 1 && $level != -1 )
    {
        $level = 1;
    }
    elseif( $level > count($buffer_handlers) || $level == -1  )
    {
        $level = count($buffer_handlers) - 1;
    }
    if( $handler == "" || $handler_file == "" )
    {
        return;
    }

    // Aggiungo il nuovo gestore. Il controllo della sua esistenza viene fatto durante la chiamata
    // Per memorizzare il file in cui si trova la funzione il nome della funzione viene utilizzato come chiave dell'array
    // e il nome del file come valore. La priorit? dei gestori viene memorizzata in un indice a parte.
    $level_handlers = $buffer_handlers[$level];

    // Array dei gestori
    $level_handlers = array_merge( $level_handlers, Array($handler => $handler_file) );

    // Array delle priorit?
    $priorities = $level_handlers["priorities"];
    array_init( $priorities );
    if( !in_array($handler, $priorities) )
    {
        array_unshift( $priorities, $handler );
    }
    $level_handlers["priorities"] = $priorities;

    $buffer_handlers[$level] = $level_handlers;
}

/**
 * Rimuove un gestore di buffer.
 *
 * Rimuove un gestore del livello di buffer dall'insieme dei gestori per quel livello, oppure li elimina tutti.
 * Se $level ? inferiore a 1 viene impostato a 1, se superiore all'ultimo livello amissibile viene considerato pari a
 * questo.
 *
 * @author     Fabrizio Colonna
 * @global     Array     L'insieme di tutti i gestori degli errori per i livelli di buffering
 * @param      Integer    $level Il nome della funzione che gestisce il livello di buffering
 * @param      String     $handler Il livello a cui eliminare l'hander
 * @param      Bool       $remove_all Se TRUE elimina tutti i gestori del livello e non considera $handler
 * @date       04/05/2006
 * @return     void
 */
function buffer_remove_handler( $level, $handler, $remove_all = false )
{
    global $buffer_handlers;

    // Controllo della validit? dei dati in ingresso
    if( $level < 1 )
    {
        $level = 1;
    }
    elseif( $level > count($buffer_handlers) )
    {
        $level = count($buffer_handlers);
    }
    if( $handler == "" )
    {
        return;
    }

    // Recupero i gestori per il livello
    $level_handlers = $buffer_handlers[$level];

    if( $remove_all )
    {
        // Elimino tutti gli handler
        $level_handlers = Array();
        $level_handlers["priorities"] = Array();
    }
    else
    {
        // Elimino l'handler
        $level_handlers = array_diff_key( $level_handlers, Array($handler => null) );
        $level_handlers["priorities"] = array_diff( $level_handlers["priorities"], Array($handler) );
    }

    $buffer_handlers[$level] = $level_handlers;
}

/**
 * Chiama i gestori del livello di buffer pi? elevato
 *
 * La fuzione si occupa di richiamatre tutti i gestori dell'ultimo livello di buffer specificato, chiamando per primo il
 * gestore inserito per ultimo. Se siamo al primo livello, nel quale non ? pi? possibile gestire il codice, viene creato un
 * buffer temporaneo.
 *
 * @author     Fabrizio Colonna
 * @date       04/05/2006
 * @global     Array     L'insieme di tutti i gestori degli errori per i livelli di buffering
 * @return     String    L'output di tutti i gestori chiamati per l'ultimo livello
 */
function buffer_call_handlers()
{
    global $buffer_handlers;

    $level = ob_get_level();
    $level = count($buffer_handlers) - 1;

    $level_handlers = $buffer_handlers[$level];
    $priorities = $level_handlers["priorities"];

    // Recupero il contenuto del buffer
    $buffer = ob_get_contents();

    // Ciclo per la chiamata di tutti i gestori
    foreach( $priorities  as $handler )
    {
        $file = $level_handlers[$handler];
        $file = dirname(__FILE__)."/../$file";

        call_ext_function( $file, $handler, Array($buffer), $buffer );
    }

    return $buffer;
}
?>