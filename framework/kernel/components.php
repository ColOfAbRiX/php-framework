<?php
/**
 * Creazione di componenti HTML
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
define( "KERNEL_ADMIN_COMPONENTS", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/template_engine.php" );
require_once( dirname(__FILE__)."/../general.php" );
require_once( dirname(__FILE__)."/config.php" );
require_once( dirname(__FILE__)."/debug.php" );

/**
 * Crea e gestisce una form HTML
 *
 * La funzione permette di gestire le operazioni più comuni effettuate con una form HTML. Ovverosia la funzione si occupa
 * di creare la form se una data condizione non è verificata, in caso contrario invece viene effettuata una chiamata ad
 * una funzione personalizzata per la gestione dei dati. In genere la condizione è tale che è vera solo quando la form è
 * stata inviata, utilizzando un campo nascosco o un parametro GET inserito all'interno della form. Per comodità del
 * programmatore è possibile, utilizzando un parametro di output, conoscere al di fuori di questa funzione, il valore
 * della condizione in modo che sia possibile prendere delle decisioni. Il parametro $get_preseve permette di preservare
 * dalla cancellazione i parametri GET desiderati con cui la pagina è stata chiamata rendendoli disponibili all'interno
 * del template di login in una variabile chiamata $old_query_string.
 * Un uso molto pratico di questa funzione è la creazione di una form HTML per il login dell'utente, predisponendo gia le
 * chiamate alle funzioni che gestiscono effettivamente il login.
 *
 * Descrizione parametro $template:
 *   $template["template"]: Template della form
 *   $template["values"]:   Opzionale. Altri assegnamenti da passare al template.
 * Descrizione parametro $clbk:
 *   $clbk["true_func"]:    Funzione da richiamare in caso $match == true
 *   $clbk["true_params"]:  Array contenente i parametri da passare alla funzione "vero".
 *   $clbk["false_func"]:   Opzionale. Funzione da richiamare in caso $match == false
 *   $clbk["false_params"]: Array contenente i parametri da passare alla funzione "false".
 *
 * @author     Fabrizio Colonna
 * @date       03/02/2006
 * @param      Array     $template Inoformazioni sul template che contiene la form. Chiavi: template, values
 * @param      Array     $clbk Opzionale. Funzioni per l'elaborazione dei dati.
 * @param      String    $match La condizione che deve essere verificata per chiamare le funzioni di callback
 * @param      Array     $get_preserve Array che contiene le chiavi GET da mantenere durante l'operazione
 * @param      Boolean   $condition Output. Viene valorizzata con il valore logico della condizizone
 * @return     Mixed     Ritorna il valore della funzione di callback se questa viene chiamata. Null altrimenti.
 */
function build_html_frame( $template, $clbk = Array(), $match = true, $get_preserve = Array(), &$condition = null )
{
    $output = null;

    // Metto in forma più usabile alcuni parametri
    $values = get_index( "values", $template, Array() );
    $template = $template["template"];

    // Valuto la condizione di controllo
    $condition = (bool)@eval( "return $match;" );
    $str_cond = $condition ? "true" : "false";			// La condizione sotto forma di stringa

    if( !$condition )
    {
        // I vecchi parametri GET vengono resi disponibili come variabile di template
        $old_params = get_preserve_string( $get_preserve );
        $values = array_merge( $values, Array( "old_query_string" => $old_params ) );

        // Visualizzo la form
		echo( build_template( $template, $values ) );
    }

    // Richiamo la funzione di callback
    $clbk_func = get_index( "{$str_cond}_func", $clbk );
    if( function_exists( $clbk_func ) )
    {
    	$clbk_params = get_index( "{$str_cond}_params", $clbk, Array() );
    	$output = call_user_func_array( $clbk_func, $clbk_params );
    }

    return $output;
}

/**
 * Crea una query recuperando i valori correnti di GET
 *
 * Passando un array contenente la lista dei parametri GET da salvare la funzione crea una query URL con quei parametri
 * e i relativi valori presi dall'array $_GET
 *
 * @author     Fabrizio Colonna
 * @date       05/02/2006
 * @param      Array     $get_preserve Array che contiene le chiavi da inserire nella string
 * @return     Mixed     La query GET contenente i parametri specificati
 */
function get_preserve_string( $get_preserve )
{
    $params = Array();
    for( $i = 0; $i < count($get_preserve); $i++ )
    {
        if( isset($_GET[$get_preserve[$i]]) )
        {
            $params = array_merge( $params,
                Array( $get_preserve[$i] => @$_GET[$get_preserve[$i]] ) );
        }
    }
    $old_params = make_query_string( $params );

    return $old_params;
}
?>