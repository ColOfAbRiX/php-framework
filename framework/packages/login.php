<?php
/**
 * Funzioni per effettuare il login degli utenti/amministratori
 *
 * Queste funzioni si occupano di gestire tutti i processi e le fasi di login che un
 * utente può fare
 *
 * @package    Kernel
 * @subpackage User
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 */

// Signature
define( "KERNEL_USER_LOGIN", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../kernel/components.php" );
require_once( dirname(__FILE__)."/../kernel/database.php" );

/**
 * Crea la form per il login e ne gestisce gli eventi
 *
 * La funzione crea la form per il login degli amministratori e ne effettua il login se la form è gia stata inviata. Se
 * il login ha avuto successo vengono valorizzate le variabili di sessione "is_logged" e "user_id" contenenti la prima
 * una variabile booleana che indica il successo del login e la seconda l'identificativo dell'utente sul database.
 *
 * @author     Fabrizio Colonna
 * @date       01/12/2005
 * @param      String   $text Il testo da tagliare.
 * @param      Integer  $length La lunghezza a cui tagliare il testo.
 * @return     String   Il testo tagliato.
 */
function build_login()
{
    global $param_s;
    
    // Creo la form dal template di login (login.tmpl), cancellando i dati ogni volta che viene inviata la form (null).
    // Se la form non è stata inviata termino l'esecuzione (app_end), altrimenti chiamo la funzione che effettua il
    // login (check_user) passandole i dati del login. La form è stata inviata quando è stato inviato il campo "s" della
    // form stessa e quando vale "do_login"
    $is_logged = build_html_frame(
        Array(
            "template" => "login.tmpl",
            "values" => null
        ),
        Array(
            "true_func" => "check_user",
            "true_params" => Array( get_index("user", $_POST, ""), get_index("pwd", $_POST, "") ),
            "false_func" => "app_end"
        ),
        $condition = (@$param_s == "do_login"),
        Array( "a" )
    );

    // NOTA: Potevo inserire questa condizione anche all'interno della funzione check_user che tanto veniva chiamata in
    // automatico, ma ho preferito farla qui per coerenza.
    if( $condition )			// Significa che la form è stata inviata
    {
    	// Controllo il risultato della riceca dell'utente nel DB
	    if( !$is_logged )
	    {
	        trigger_error( "Username o password errati! (sostituire questo messaggio)", E_USER_ERROR );
	    }
	    else
	    {
	    	// Salvo nella sessione i dati del login
	    	$_SESSION["is_logged"] = true;
	    	$_SESSION["user_id"] = $is_logged;

	        trigger_error( "Login effettuato con successo! (sostituire questo messaggio)", E_USER_ERROR );
	    }
    }
}

/**
 * Effettua il login di un amministratore
 *
 * Tramite lo username e la password come argomenti la funzione è ingrado di determinare se quell'utente è presente sul
 * database. Se l'utente è presente viene restituito il suo id. Se l'utente non fosse presente la funzione attende due
 * secondi prima di terminare per prevenire attacchi brute-force e restituisce il valore zero.
 *
 * @author     Fabrizio Colonna
 * @date       01/12/2005
 * @param      String    $user Il nome utente
 * @param      String    $pwd La password
 * @return     Int       Zero se l'utente non è presente, il suo ID del database altrimenti
 */
function check_user( $user, $pwd )
{
    global $SQL;

    // Recupero gli utenti con un dato nome
    $users = db_execute_array( $SQL["user_login"], Array($user) );

    for( $i = 0; $i < count($users); $i++ )
    {
        list( $user_id, $name_db, $seed_db, $pwd_db ) = array_values( $users[$i] );

        // Ricostruisco l'hash
        $check = "$name_db:$pwd:$seed_db";
    	$check = sha1( $check );

        debug_watch( "\$pwd_db", $pwd_db );
        debug_watch( "\$check", $check );

        // Controllo l'hash del db e quello nuovo
    	if( $check == $pwd_db )
    	{
    	    // L'utente è presente nel db
    	    return $user_id;
    	}
    	else
    	{
    	    // L'utente non è presente nel db
    	    sleep( 2 );            // Perdo alcuni secondi, così evito gli attacchi brute force
    	    return 0;
    	}
    }

    return 0;
}

build_login();
?>