<?php
/**
 * Main startup file
 *
 * La pagina principale contiene la gestione delle richieste dell'utente. Si occupa di inizializzare l'applicazione
 * tramite le funzioni appropriate, controlla il parametro di azione passato tramite GET e chiamato "r" (per cause
 * storiche) e quindi richiama la pagina che gestisce l'azione richiesta. In caso l'azione sia classificata come azione
 * amministrativa, ovvero quando è presente il parametro "a" (da administrative) in GET, la pagina richiede le credenziali
 * dell'utente. Terminate le esecuzioni delle azioni la pagina termina l'applicazione.
 *
 * @package    Main
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 * @see        config.php
 */
// Dipendenze
require_once( dirname(__FILE__)."/framework/application.php" );

//$_SESSION["is_logged"] = true;
//$_GET["r"] = "show_page";
//$_GET["p"] = "useless";
//$_GET["debug_filter"] = "tags has \"kernel\"";

// Inizializzazione dell'applicazione
app_start();
app_end();

// Gestione del login: per entrare nella sezione amministrativa basta la presenza è necessario avere una determinata variabile
// di sessione che viene impostata solamente col login.
if( @$param_a )
{
    require_once( dirname(__FILE__)."/kernel/packages/login.php" );
}

// Recupero dei contenuti
switch( @$param_r )
{
	// Home page
    default:
 	{
 		// Azione di default (visualizzazione della home page)
 		$param_p = "home";
 	}

 	// Sezioni utente
 	case "admin_page":
	case "show_page":
	{
		// Visualizzazione di una pagina testuale
		include( "text_page.php" );
		break;
	}
	case "show_gallery":
	{
		// Visualizzazione completa di una news
		//include( "news.php" );
		echo( "Sei nella gallery fotografica" );
 		break;
	}
	case "admin_news":
	case "show_news":
	{
		// Visualizzazione completa di una news
		include( "news.php" );
 		break;
	}

	case "admin_menu":
	{
		// Amministrazione dei menu
		include( "news.php" );
 		break;
	}
}

// Termine dell'applicazione
app_end();
?>
