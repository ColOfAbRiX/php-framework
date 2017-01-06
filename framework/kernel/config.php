<?php
/**
 * Variabili di configurazione del sito
 *
 * Contiene tutti i parametri variabili utilizzati per variare il comportamento del sito.
 * La regola dovrebbe essere che ogni sezione viene utilizzata solamente nel file appropriato
 * e non in altre parti per eumentare la frazionabilità del codice.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Fabrizio Colonna 2005
 * @date       30/11/2005
 */
// Dipendenze
require_once( dirname(__FILE__)."/definitions.php" );

// Signature
define( "KERNEL_CONFIG", 1 );

# #              A L T R O               # #
$CONFIG["debug"]                      = ON;                              // Debug mode
$CONFIG["compression"]                = ON;                             // Compressione dei dati
$CONFIG["compression_level"]          = 6;                               // Livello di compressione [0-9]
$CONFIG["browser_cache"]              = OFF;                             // Cache del browser
$CONFIG["date_format"]                = "d/m/Y";                         // Formato generale della data
$CONFIG["encoding"]                   = "ISO-8859-1";                    // Codifica della pagina
$CONFIG["language"]                   = "IT";                            // Lingua del sito
//$CONFIG["user_interface"]             = "/kernel/std_interface.php";     // File che contiene le funzioni utente per creare la pagina

# #           R E G I S T E R            # #
$CONFIG["register"]["get"]            = ON;                              // Registra le variabili GET
$CONFIG["register"]["get_prefix"]     = "param_";                        // Prefisso per le variabili GET
$CONFIG["register"]["post"]           = OFF;                             // Registra le variabili POST
$CONFIG["register"]["post_prefix"]    = "param_";                        // Prefisso per le variabili POST
$CONFIG["register"]["cookies"]        = OFF;                             // Registra i COOKIE
$CONFIG["register"]["cookies_prefix"] = "param_";                        // Prefisso per le varabili cookie
$CONFIG["register"]["session"]        = OFF;                             // Registra le variabili di sessione
$CONFIG["register"]["session_prefix"] = "param_";                        // Prefisso per le variabili di sessione

# #           D A T A B A S E            # #
$CONFIG["db"]["name"]                 = "framework_php";                 // Nome del database
$CONFIG["db"]["host"]                 = "localhost";                     // Indirizzo del database
$CONFIG["db"]["usr"]                  = "guest";                         // Utente del database
$CONFIG["db"]["pwd"]                  = "guest";                         // Password per l'utente
$CONFIG["db"]["type"]                 = "mysql";                         // Tipo del datbase
$CONFIG["db"]["persistent"]           = false;                           // Connessione persistente al database
$CONFIG["db"]["selective"]            = false;                           // Connessione al db solo quando utilizzato
$CONFIG["db"]["start_state"]          = DISCONNECTED;                       // Stato di partenza del db

# #              E R R O R I              # #
$CONFIG["error"]["use_php"]           = NO;                              // Indica se usare o no la gestione degli errori di PHP
$CONFIG["error"]["report"]            = E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR;
$CONFIG["error"]["tmpl"]              = "error.tmpl";                    // Template del messaggio di errore
$CONFIG["error"]["logging"]           = ENABLED;                         // Abilita il log degli errori
$CONFIG["error"]["log_tmpl"]          = "error.log.tmpl";                // Template del log degli errori
$CONFIG["error"]["terminate"]         = true;                			 // Terminare in caso di errore


# #           T E M P L A T E S           # #
$CONFIG["tmpl"]["cache"]              = OFF;                             // Abilita la cache per i template
$CONFIG["tmpl"]["debug"]              = OFF;                             // Abilita le opzioni di debug per Smarty
$CONFIG["tmpl"]["tmpl_dir"]           = "templates";                     // Directory dei template (riferito alla root del sito)
$CONFIG["tmpl"]["cache_dir"]          = "framework/cache";               // Directory della cache dei template (riferito alla root del sito)
$CONFIG["tmpl"]["layout"]             = "general.tmpl";                  // Template di una generica pagina
$CONFIG["tmpl"]["code_dir"]           = "templates";                     // Cartella contenente il codice dei templates

# #                M E N U                # #
$CONFIG["menu"][1]["item_tmpl"]       = "menu.item.tmpl";                // Template di una riga del menu
$CONFIG["menu"][1]["frame_tmpl"]      = "menu.frame.tmpl";               // Riquadro del menu
$CONFIG["menu"][2]["item_tmpl"]       = "menu.item.tmpl";                // Template di una riga del menu
$CONFIG["menu"][2]["frame_tmpl"]      = "menu.frame.tmpl";               // Riquadro del menu

# #                N E W S                # #
// Il sistema di news è da aggiornare e questa sezione cambierà di parecchio
$CONFIG["news"]["n_full"]             = 5;                               // Numero di news piene
$CONFIG["news"]["n_reduced"]          = 6;                               // Numero di news ridotte
$CONFIG["news"]["npt_full"]           = 1;                               // News per template nel template full
$CONFIG["news"]["npt_reduced"]        = 2;                               // News per template nel template reduced
$CONFIG["news"]["npt_list"]           = 1;                               // News per template nel template list
$CONFIG["news"]["text_cutout"]        = 150;                             // Taglio delle frasi molto lunghe
$CONFIG["news"]["max"]                = 20;                              // Numero massimo di news da visualizzare
$CONFIG["news"]["tmpl_full"]          = "news.box.full.tmpl";            // Template delle news full
$CONFIG["news"]["tmpl_reduced"]       = "news.box.red.tmpl";             // Template delle news reduced
$CONFIG["news"]["tmpl_list"]          = "news.box.list.tmpl";            // Template delle news list
$CONFIG["news"]["tmpl_frame"]         = "news.frame.tmpl";               // Riquadro di tutte le news
$CONFIG["news"]["tmpl_complete"]      = "news.complete.tmpl";            // News a pieno schermo
$CONFIG["news"]["pattern"] = "-,full/{$CONFIG["news"]["npt_full"]},{$CONFIG["news"]["n_full"]}" .
                             ";-,reduced/{$CONFIG["news"]["npt_reduced"]}," .($CONFIG["news"]["n_full"] + $CONFIG["news"]["n_reduced"]) .
                             ";-,list/{$CONFIG["news"]["npt_list"]},-";
?>