# MySQL-Front Dump 2.5
#
# Host: localhost   Database: enrico
# --------------------------------------------------------
# Server version 4.0.18-nt


#
# Table structure for table 'menu'
#

CREATE TABLE `menu` (
  `id_menu` tinyint(3) unsigned NOT NULL default '0',
  `item` varchar(255) NOT NULL default '',
  `link` varchar(230) NOT NULL default '',
  `position` tinyint(3) unsigned default '0',
  `indent` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`id_menu`,`item`)
) TYPE=MyISAM;



#
# Dumping data for table 'menu'
#

INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("1", "News", "index.php?r=show_news", "1", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("1", "Home page", "index.php", "0", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("2", "Prova", "", "0", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("2", "Qui <b>dentro</b> posso scrivere quello che voglio", "", "0", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("2", "<b>Personalizzato</b > <u>con</u> <i>link</i>", "index.php", "0", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("1", "Test", "index.php?r=show_page&p=useless", "5", "0");
INSERT INTO `menu` (`id_menu`, `item`, `link`, `position`, `indent`) VALUES("1", "Altra Pagina", "index.php?r=show_page&p=altra", "3", "0");


#
# Table structure for table 'news'
#

CREATE TABLE `news` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `category` tinyint(3) unsigned default '0',
  `date` int(11) NOT NULL default '0',
  `content` text,
  `image` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'news'
#

INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("1", "Prima news", "1", "1133360700", "Questa è la prima news inserita e serve solamente per fare un test. Devo scrivere qualcosa di lungo, così è più bello e si vede meglio l\'effetto del testo tagliato", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("2", "Questa è solamente una prova", "2", "1133360700", "Ancora non c\'è nessun contenuto da visualizzare, quindi a che serve?", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("3", "Tutto OK", "1", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("4", "Ambarabaciccicocco", "3", "1133404870", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("5", "Tutto OK 2", "1", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("6", "Un\'altro titolo lungo", "2", "1133404869", "Sto sviluppando ancora le news, sono un po\' indietro, speravo di avere gia finito, ma sto cercando di dare una buona scalabilità per potere mantenere meglio il sito in seguito e per poterlo riutilizzare da altre parti", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("7", "Questa è solamente una prova", "3", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("8", "Ancora ??", "2", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("9", "Un\'altro titolo lungo", "2", "1133404869", "Questa è la prima news inserita e serve solamente per fare un test. Devo scrivere qualcosa di lungo, così è più bello e si vede meglio l\'effetto del testo tagliato", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("10", "Tre civette sul comò", "3", "1133404999", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("11", "Ma gia questo è qualcosa", "3", "1133404869", "Questa è la prima news inserita e serve solamente per fare un test. Devo scrivere qualcosa di lungo, così è più bello e si vede meglio l\'effetto del testo tagliato", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("12", "Tutto OK", "1", "1133416651", "Sto sviluppando ancora le news, sono un po\' indietro, speravo di avere gia finito, ma sto cercando di dare una buona scalabilità per potere mantenere meglio il sito in seguito e per poterlo riutilizzare da altre parti", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("13", "Non so più cosa inventarmi", "3", "1133405869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("14", "Un\'altro titolo lungo", "3", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("15", "Ma gia questo è qualcosa", "2", "1133237976", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("16", "Tutto OK", "3", "1133485247", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("17", "E\' meglio di niente, quindi va bene", "1", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("18", "Ma gia questo è qualcosa", "1", "1133404869", "Questa è la prima news inserita e serve solamente per fare un test. Devo scrivere qualcosa di lungo, così è più bello e si vede meglio l\'effetto del testo tagliato", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("19", "Non so più cosa inventarmi", "2", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("20", "Un\'altro titolo lungo", "3", "1133416437", "Sto sviluppando ancora le news, sono un po\' indietro, speravo di avere gia finito, ma sto cercando di dare una buona scalabilità per potere mantenere meglio il sito in seguito e per poterlo riutilizzare da altre parti", "futur53.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("21", "Un titolo un po\' più lungo non fa male", "1", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", "range_av.jpg");
INSERT INTO `news` (`id`, `title`, `category`, `date`, `content`, `image`) VALUES("22", "Tutto OK", "1", "1133404869", "Sto andando avanti con il sito, persino durante le ore di lezione. Sono importanti le lezioni, ma molto spesso sono noiose e programmare un po\' non fa male, specialmente se il professore è uno di quei tipi noiosi. Ovviamente quello che sto scrivendo serve solamente a riempire molte righe per vedere l\'effetto che fa.", NULL);


#
# Table structure for table 'news_category'
#

CREATE TABLE `news_category` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `category` char(50) NOT NULL default '',
  UNIQUE KEY `id_2` (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'news_category'
#

INSERT INTO `news_category` (`id`, `category`) VALUES("1", "Progettazione");
INSERT INTO `news_category` (`id`, `category`) VALUES("2", "Test");
INSERT INTO `news_category` (`id`, `category`) VALUES("3", "Categoria di Prova");


#
# Table structure for table 'pages'
#

CREATE TABLE `pages` (
  `id` varchar(50) NOT NULL default '',
  `name` varchar(50) NOT NULL default '',
  `content` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



#
# Dumping data for table 'pages'
#

INSERT INTO `pages` (`id`, `name`, `content`) VALUES("home", "Home Page", "<h2>Home page</h2>\r\n<p>Questa è la <b>home page</b> del sito, ora è semplicemente una pagina priva di qualsiasi contenuto informativo.</p>\r\n<p>Ovviamente il testo che sto scrivendo è come sempre un semplice testo che serve ad allungare il contenuto della pagina</p>");
INSERT INTO `pages` (`id`, `name`, `content`) VALUES("useless", "Pagina fittizia", "<h2>Da Google News</h2>\r\n<div class=mainbody><table border=0 width=75% valign=top cellpadding=2 cellspacing=7><tr><td width=80 align=center valign=top><a href=\"http://www.rai.it/news/articolornews24/0,9219,4226430,00.html\" id=r-0i_1102035455><img src=http://news.google.it/news?imgefp=KiRSE2TUxbwJ&imgurl=www.rainews24.rai.it/ran24/immagini/aviaria_uovo_ricerca.jpg width=79 height=55 alt=\"\" border=1><br><font size=-2>RAI Net News</font></a></td><td valign=top><a href=\"http://www.tgcom.mediaset.it/mondo/articoli/articolo290959.shtml\" id=r-0_1102035455><b>Aviaria,Turchia: 5 nuovi casi umani</b></a><br><font size=-1><font color=#6f6f6f><b>TGCOM&nbsp;-</font> <nobr>11 ore fa</nobr></b></font><br><font size=-1>L&#39;influenza aviaria fa sempre piÃ¹ paura alla Turchia. Nelle ultime ore sono stati accertati cinque nuovi casi di contagio umano: tre nella zona di Ankara e due in quella di Van. Lo ha reso noto il funzionario <b>...</b> \r\n</font><br><font size=-1><a href=\"http://qn.quotidiano.net/art/2006/01/08/5397728\">Altri cinque casi, la Turchia trema</a> <font size=-1 color=#6f6f6f><nobr>Quotidiano Nazionale</nobr></font></font><br><font size=-1><a href=\"http://www.ilgiornale.it/a.pic1?ID=55574\">Altri due casi di aviaria, panico in Turchia</a> <font size=-1 color=#6f6f6f><nobr>il Giornale</nobr></font></font><br><font size=-1 class=p><a href=\"http://www.ansa.it/main/notizie/fdg/200601081630226413/200601081630226413.html\"><nobr>ANSA</nobr></a>&nbsp;- <a href=\"http://www.rainews24.it/Notizia.asp?NewsID=59076\"><nobr>RaiNews24</nobr></a>&nbsp;- <a href=\"http://www.ilpassaporto.kataweb.it/dettaglio.jsp?id=1246568&c=2\"><nobr>Il Passaporto</nobr></a>&nbsp;- <a href=\"http://redazione.romaone.it/4Daction/Web_RubricaNuova?ID=71202&doc=si\"><nobr>RomaOne</nobr></a>&nbsp;- </font><font class=p size=-1><a class=p href=\"http://news.google.it/?ned=it&ncl=http://www.tgcom.mediaset.it/mondo/articoli/articolo290959.shtml&hl=it\"><nobr><b>e altri 85 articoli simili&nbsp;&raquo;</b></nobr></a></font></table><table border=0 width=75% valign=top cellpadding=2 cellspacing=7><tr><td width=80 align=center valign=top><a href=\"http://www.unita.it/index.asp?SEZIONE_COD=HP&TOPIC_TIPO=&TOPIC_ID=46634\" id=r-1i_1102051566><img src=http://news.google.it/news?imgefp=R6HGhNEUIj4J&imgurl=www.unita.it/images/2006gennaio/0108FassinoDalema.jpg width=79 height=54 alt=\"\" border=1><br><font size=-2>L&#39;UnitÃ </font></a></td><td valign=top><a href=\"http://www.tgcom.mediaset.it/politica/articoli/articolo291003.shtml\" id=r-1_1102051566><b>Fassino: da Cdl campagna vergognosa</b></a><br><font size=-1><font color=#6f6f6f><b>TGCOM&nbsp;-</font> <nobr>7 ore fa</nobr></b></font><br><font size=-1>&quot;Respingo la campagna vergognosa della destra che cerca di criminalizzare l&#39;opposizione. Noi siamo gente per bene&quot;. CosÃ¬ il segretario dei ds, Piero Fassino, riferendosi alle polemiche sul caso Unipol. &quot;Il <b>...</b> \r\n</font><br><font size=-1><a href=\"http://www.unita.it/index.asp?SEZIONE_COD=HP&TOPIC_TIPO=&TOPIC_ID=46634\">Prodi: sto con Fassino ei Ds. Ingrao: il nemico Ã¨ Berlusconi</a> <font size=-1 color=#6f6f6f><nobr>L\'UnitÃ </nobr></font></font><br><font size=-1><a href=\"http://finanza.repubblica.it/scripts/cligipsw.dll?app=KWF&tpl=kwfinanza%5Cdettaglio_news.tpl&del=20060108&fonte=RPB&codnews=455335\">Fassino contrattacca: &quot;Berlusconi cerca di rimuovere i problemi&quot;</a> <font size=-1 color=#6f6f6f><nobr>La Repubblica</nobr></font></font><br><font size=-1 class=p><a href=\"http://www.rainews24.it/Notizia.asp?NewsID=59082\"><nobr>RaiNews24</nobr></a>&nbsp;- <a href=\"http://www.ilpassaporto.kataweb.it/dettaglio.jsp?id=1246916&c=2\"><nobr>Il Passaporto</nobr></a>&nbsp;- <a href=\"http://www.articolo21.info/news.php?id=11892\"><nobr>Articolo 21</nobr></a>&nbsp;- </font><font class=p size=-1><a class=p href=\"http://news.google.it/?ned=it&ncl=http://www.tgcom.mediaset.it/politica/articoli/articolo291003.shtml&hl=it\"><nobr><b>e altri 8 articoli simili&nbsp;&raquo;</b></nobr></a></font></table><table border=0 width=75% valign=top cellpadding=2 cellspacing=7><tr><td width=80 align=center valign=top><a href=\"http://redazione.romaone.it/4Daction/Web_RubricaNuova?ID=71205&doc=si\" id=r-2i_1102046003><img src=http://news.google.it/news?imgefp=YLyByk7-Em8J&imgurl=www.romaone.it/immagini/Notizie/01/ateneview160.jpg width=80 height=60 alt=\"\" border=1><br><font size=-2>RomaOne</font></a></td><td valign=top><a href=\"http://www.lastampa.it/redazione/cmsSezioni/cronache/200601articoli/1593girata.asp\" id=r-2_1102046003><b>Nessun rischio Tsunami in Italia</b></a><br><font size=-1><font color=#6f6f6f><b>La Stampa&nbsp;-</font> <nobr>12 ore fa</nobr></b></font><br><font size=-1>ROMA. Nessun danno in Italia e nessun rischio tsunami dopo la scossa di terremoto di almeno 6,4 gradi (ma fino a 6,7 gradi secondo altre rilevazioni) secondo sulla scala Richter con epicentro in Grecia, avvertita <b>...</b> \r\n</font><br><font size=-1><a href=\"http://ilrestodelcarlino.quotidiano.net/art/2006/01/08/5397711\">Forte scossa ad Atene, avvertita anche in Italia</a> <font size=-1 color=#6f6f6f><nobr>Il Resto del Carlino</nobr></font></font><br><font size=-1><a href=\"http://today.reuters.it/news/newsArticle.aspx?type=topNews&storyID=2006-01-08T150811Z_01_PAR843855_RTRIDST_0_OITTP-GREECE-QUAKE-PUNTO.XML\">Forte terremoto Grecia, avvertito anche Sud Italia</a> <font size=-1 color=#6f6f6f><nobr>Reuters Italia</nobr></font></font><br><font size=-1 class=p><a href=\"http://www.ansa.it/main/notizie/fdg/200601081605226490/200601081605226490.html\"><nobr>ANSA</nobr></a>&nbsp;- <a href=\"http://www.adnkronos.com/3Level.php?cat=Esteri&loid=1.0.266171409\"><nobr>IGN - Italy Global Nation</nobr></a>&nbsp;- <a href=\"http://www.ilpassaporto.kataweb.it/dettaglio.jsp?id=1246683&c=2\"><nobr>Il Passaporto</nobr></a>&nbsp;- <a href=\"http://www.montagna.org/node/3977\"><nobr>Montagna.org</nobr></a>&nbsp;- </font><font class=p size=-1><a class=p href=\"http://news.google.it/?ned=it&ncl=http://www.lastampa.it/redazione/cmsSezioni/cronache/200601articoli/1593girata.asp&hl=it\"><nobr><b>e altri 31 articoli simili&nbsp;&raquo;</b></nobr></a></font></table><table border=0 width=75% valign=top cellpadding=2 cellspacing=7><tr><td width=80 align=center valign=top><a href=\"http://www.repubblica.it/2005/e/sezioni/scienza_e_tecnologia/microsoft2/fal/fal.html\" id=r-3i_1101972184><img src=http://news.google.it/news?imgefp=-kIz-8xNIBgJ&imgurl=www.repubblica.it/2005/e/sezioni/scienza_e_tecnologia/microsoft2/fal/reut_7342604_57510.jpg width=56 height=80 alt=\"\" border=1><br><font size=-2>La Repubblica</font></a></td><td valign=top><a href=\"http://www.repubblica.it/2005/e/sezioni/scienza_e_tecnologia/microsoft2/fal/fal.html\" id=r-3_1101972184><b>Falla in Windows, Microsoft ammette</b></a><br><font size=-1><font color=#6f6f6f><b>La Repubblica&nbsp;-</font> <nobr>6 gen 2006</nobr></b></font><br><font size=-1>ROMA - Microsoft ha ammesso una falla nel suo sistema operativo. Una vulnerabilitÃ  individuata nei giorni scorsi da alcuni esperti di sicurezza nei file Wmf (Windows Meta File); ma oggi il colosso di Redmond <b>...</b> \r\n</font><br><font size=-1><a href=\"http://punto-informatico.it/p.asp?i=57105&r=PI\">Falla WMF, ecco la patch ufficiale</a> <font size=-1 color=#6f6f6f><nobr>Punto Informatico</nobr></font></font><br><font size=-1><a href=\"http://www.tgcom.mediaset.it/tgtech/articoli/articolo290601.shtml\">Windows, nuova falla pericolosa</a> <font size=-1 color=#6f6f6f><nobr>TGCOM</nobr></font></font><br><font size=-1 class=p><a href=\"http://www.adnkronos.com/3Level.php?cat=CyberNews&loid=1.0.265431321\"><nobr>IGN - Italy Global Nation</nobr></a>&nbsp;- <a href=\"http://liberoblog.libero.it/hi-tech/bl2179.phtml\"><nobr>Libero.it</nobr></a>&nbsp;- <a href=\"http://www.ansa.it/main/notizie/fdg/200601061838226369/200601061838226369.html\"><nobr>ANSA</nobr></a>&nbsp;- <a href=\"http://www.corriere.it/Primo_Piano/Scienze_e_Tecnologie/2006/01_Gennaio/06/windows.shtml\"><nobr>Corriere della Sera</nobr></a>&nbsp;- </font><font class=p size=-1><a class=p href=\"http://news.google.it/?ned=it&ncl=http://www.repubblica.it/2005/e/sezioni/scienza_e_tecnologia/microsoft2/fal/fal.html&hl=it\"><nobr><b>e altri 39 articoli simili&nbsp;&raquo;</b></nobr></a></font></table><table border=0 width=75% valign=top cellpadding=2 cellspacing=7><tr><td width=80 align=center valign=top><a href=\"http://www.lastampa.it/redazione/cmsSezioni/cronache/200601articoli/1590girata.asp\" id=r-4i_1102034244><img src=http://news.google.it/news?imgefp=xL4Dw-98mQYJ&imgurl=www.lastampa.it/redazione/cmssezioni/cronache/200601images/lampedusa01.jpg width=80 height=63 alt=\"\" border=1><br><font size=-2>La Stampa</font></a></td><td valign=top><a href=\"http://www.lastampa.it/redazione/cmsSezioni/cronache/200601articoli/1590girata.asp\" id=r-4_1102034244><b>Nuovi sbarchi clandestini a Lampedusa</b></a><br><font size=-1><font color=#6f6f6f><b>La Stampa&nbsp;-</font> <nobr>15 ore fa</nobr></b></font><br><font size=-1>PALERMO. Sono sbarcati all&#39;alba sull&#39;isola di Lampedusa 180 clandestini. Gli extracomunitati, tra cui 9 donne e un bambino, erano a bordo di un barcone che si Ã¨ arenato sulla spiaggia vicina al porto dell&#39;isola pelagia. <b>...</b> \r\n</font><br><font size=-1><a href=\"http://today.reuters.it/news/newsArticle.aspx?type=topNews&storyID=2006-01-08T163640Z_01_CIN851750_RTRIDST_0_OITTP-IMMIGRAZIONE-LAMPEDUSA-8GENN-PUNTO.XML&archived=False\">Immigrazione, 329 clandestini sbarcati a Lampedusa</a> <font size=-1 color=#6f6f6f><nobr>Reuters Italia</nobr></font></font><br><font size=-1><a href=\"http://www.ilpassaporto.kataweb.it/dettaglio.jsp?id=1246763&c=2\">Un aiuto per voi</a> <font size=-1 color=#6f6f6f><nobr>Il Passaporto</nobr></font></font><br><font size=-1 class=p><a href=\"http://www.scriptanews.it/article3105-agrigento-sbarchi.html\"><nobr>Scriptanews.it</nobr></a>&nbsp;- <a href=\"http://today.reuters.it/news/newsArticle.aspx?type=topNews&storyID=2006-01-08T143032Z_01_CIN851750_RTRIDST_0_OITTP-IMMIGRAZIONE-LAMPEDUSA-8GENN-PUNTO.XML\"><nobr>Reuters Italia</nobr></a>&nbsp;- <a href=\"http://www.ilpassaporto.kataweb.it/dettaglio.jsp?id=1245368&c=2\"><nobr>Il Passaporto</nobr></a>&nbsp;- <a href=\"http://today.reuters.it/news/newsArticle.aspx?type=topNews&storyID=2006-01-08T103622Z_01_CIN838173_RTRIDST_0_OITTP-IMMIGRAZIONE-LAMPEDUSA-8GENN.XML\"><nobr>Reuters Italia</nobr></a>&nbsp;- </font><font class=p size=-1><a class=p href=\"http://news.google.it/?ned=it&ncl=http://www.lastampa.it/redazione/cmsSezioni/cronache/200601articoli/1590girata.asp&hl=it\"><nobr><b>e altri 9 articoli simili&nbsp;&raquo;</b></nobr></a></font></table>");
INSERT INTO `pages` (`id`, `name`, `content`) VALUES("altra", "Altra Pagina", "Questa è un\'altra pagina");


#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `id` int(3) NOT NULL UNIQUE default 0,
  `name` varchar(50) NOT NULL default '',
  `pwd_hash` varchar(40) NOT NULL default '',
  `seed` int(10) unsigned NOT NULL default '0',
  `config` mediumtext,
  PRIMARY KEY  (`name`,`pwd_hash`)
) TYPE=MyISAM;



#
# Dumping data for table 'users'
#

INSERT INTO `users` (`name`, `pwd_hash`, `seed`, `config`) VALUES("Administrator", "e5b2ef724c18053058d8953a2e20a1efce3ca356", "398141412", NULL);
