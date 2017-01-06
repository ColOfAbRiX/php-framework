<?php
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