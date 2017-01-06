<?php
/**
 * Queries per l'accesso ai dati nel database
 *
 * Contiene tutti le query per il recupero dei dati da database in modo da centralizzare
 * il loro utilizzo ed eliminare una possibile ridondanza di codice. Permette inoltre un
 * migliore mantenimento e scalabilità del sito.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @date       01/12/2005
 */
 
// Signature
define( "KERNEL_ADMIN_QUERIES", 1 );

// Recupera una news completa di categoria a partire dal suo ID
$SQL["news_by_id"]   = "SELECT news.id AS id_news, title, news.date, content, image, news_category.category " .
                       "FROM news LEFT JOIN news_category ON news.category = news_category.id " .
                       "WHERE news.id = ! ".
                       "ORDER BY news.date DESC;";

// Recupera tutte le news complete di categoria
$SQL["news_all"]     = "SELECT news.id AS id_news, title, news.date, content, image, news_category.category " .
                       "FROM news LEFT JOIN news_category ON news.category = news_category.id " .
                       "ORDER BY news.date DESC ".
                       "LIMIT 0, !;";

// Query per il recupero degli elementi del menu di navigazione destro
$SQL["menu_items"]   = "SELECT item, link, indent " .
                       "FROM menu " .
                       "WHERE id_menu = ! " .
                       "ORDER BY position;";

// Query per il recupero del contenuto di una pagina
$SQL["page_content"] = "SELECT name, content " .
                       "FROM pages " .
                       "WHERE id = ?;";

// Query per il recupero della lista delle pagine
$SQL["page_list"]    = "SELECT id, name " .
                       "FROM pages " .
                       "ORDER BY name " .
                       "LIMIT !, !;";

// Query per il login degli utenti
$SQL["user_login"]   = "SELECT id, name, seed, pwd_hash " .
                       "FROM users " .
                       "WHERE name LIKE ?;";
?>