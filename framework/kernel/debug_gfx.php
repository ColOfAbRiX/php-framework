<?php
/**
 * Funzioni per la visualizzazione grafica del debug
 *
 * Per mantenere indipendente il debug dal resto del sistema è necessario utilizare delle
 * funzioni personalizzate per la grafica. Questo file contiene appunto i componenti per
 * la creazione dell'interfaccia del debug.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 * @see        config.php
 * @depend     Smarty, PEAR, config.php
 */
 // Signature
define( "KERNEL_ADMIN_DEBUG_GFX", 1 );

/**
 * Intestazione HTML della grafica del debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @return     void
 */
function debugfx_begin()
{
    $previous_get = make_query_string( $_GET );

    ob_start();
	?>
<script language="JavaScript">
function toggle_panel_status( obj )
{
	if( obj.style.visibility == "visible" || obj.style.position == "" )
	{
		obj.style.position = "absolute";
		obj.style.visibility = "hidden";
		obj.style.top = "-999";
		obj.style.left = "-999";
	}
	else
	{
		obj.style.position = "";
		obj.style.visibility = "visible";
		obj.style.top = "";
		obj.style.left = "";
	}
	
	// Trovo l'ID dell'oggetto
	nome = obj.id
	do
	{
		id = parseInt(nome);
		nome = nome.substr(1);
	}
	while( nome != "" && !(id > 0) );

	toggle_all_stack( id )
	
	return false;
}

function toggle_all_stack(id)
{
	obj_container = document.getElementById( "stack_trace_" + id );

	for( i = 1; i < items_count[id]; i++ )
	{
		obj = document.getElementById( "call_" + id + "_" + i );
		
		if( obj_container.style.visibility == "hidden" )
		{
			obj.style.position = "absolute";
			obj.style.visibility = "hidden";
		}
	}
}
items_count = Array(10);
</script>

<p>&nbsp;</p>
<center>
<div id="dbg_frame" style="width: 700px; text-align: center; position: relative;">
  <table cellspacing="0" cellpadding="0" border="0" width="700px" style="border-top: 2px solid black; border-bottom: 2px solid black;">

    <tr bgcolor="#FFEEEE">
      <td colspan="6" align="center" style="border-bottom: 1px solid #AAAAAA; padding: 4px; color: red; font-size: 10pt">
      
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td width="50%" align="left">
	          <b style="color: red">DEBUG</b>
            </td>
            <td width="50%" align="right">
              <form action="?<?php echo($previous_get); ?>" method="GET">
	           <input type="text" name="debug_filter" value="<?php echo( htmlencode( get_index( "debug_filter", $_GET)) ) ?>" size="25" style="border: 1px #CCCCCC solid; background-color: #FFFFFF; font-size: 8pt">
	           <input type="submit" value="OK" style="border: 1px #CCCCCC solid; background-color: #EEEEEE; font-size: 8pt; padding-left: 3px; padding-right: 3px">
	         </form>
            </td>
          </tr>
        </table>
      
      </td>
    </tr>
    <?php
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * Corpo della grafica del debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @return     void
 */
function debugfx_write_item( $data, $time, $stack_trace, $tags, $bgcolor="white" )
{
    global $debugfx_item_id;
    $debugfx_item_id = $debugfx_item_id + 1;
 
    // Formattazione del tempo di esecuzione
    $utime      =  number_format( (double)$time - (int)$time, 6 );
    $time       =  date( "H:i:s", (int)$time ) . "." . substr( $utime, 2 );

    // Recupero la stack trace per visualizzare i parametri di chiamata della funzione
    $last_call  =  $stack_trace[0];
    $file       =  basename( get_index("file", $last_call) );
    $line       =  get_index( "line", $last_call) ;
    $section    =  $tags;//get_index( "section", $last_call, "N/A" );

    $fnc_args   =  $last_call["args"];
    $function   =  $last_call["function"];
       
    ob_start();
    ?>
    <tr style="padding: 3px; background-color: #FFffff;">
      <td style="border-bottom: 1px solid #AAAAAA;" bgcolor="<?php echo($bgcolor) ?>">
        <table width="700px" cellpadding="2">
          <tr>
            <td width="100%">

              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                  <td align="left" width="27%"><b>Section:</b> <?php echo($section) ?></td>
                  <td align="center" width="27%"><b>File:</b> <?php echo($file) ?></td>
                  <td align="center" width="20%"><b>Line:</b> <?php echo($line) ?></td>
                  <td align="right" width="25%"><b>On time</b> <?php  echo($time) ?></td>
                </tr>
              </table>

            </td>
          </tr>
          <tr>
            <td width="100%">

              <table width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td width="70px" align="left"><b>Function:</b></td>
                  <td width="630px" align="left">
                    <?php echo($function) ?>(<?php
                    if( count($fnc_args) > 0 )
                    {
                      ?><a onClick="return toggle_panel_status(parameters_<?php echo($debugfx_item_id) ?>)" href="">...</a><?php
                    }
                    ?>)
                  </td>
                </tr>
                <tr width="100%" id="parameters_<?php echo($debugfx_item_id) ?>" style="position: absolute; visibility: hidden">
                  <td width="70px">&nbsp;</td>
                  <td width="630px" align="left" style="border-left: 2px solid black; padding-left: 8px">
                    <?php
                    foreach( $fnc_args as $i => $arg )
                    {
                        $arg = object_to_text( $arg );
                        $arg = str_replace( "\n", "", $arg );
                        $arg = htmlencode( $arg );
                        $arg = str_replace( "&nbsp;", " ", $arg );
                        
                        echo( "                    <i>\$$i</i> = $arg<br>\n" );
                    }
                    ?>
                  </td>
                </tr>
              </table>

            </td>
          </tr>
          <tr>
            <td width="100%" align="left">
              <?php echo( $data ) ?>
            </td>
          </tr>
          <tr>
            <td width="100%" align="left">
              <b>Stack Trace:</b> <a onClick="return toggle_panel_status(stack_trace_<?php echo($debugfx_item_id) ?>)" href="">...</a>
              <table width="98%" align="center" cellspacing="0" cellpadding="0" border="0" id="stack_trace_<?php echo($debugfx_item_id) ?>" style="position: absolute; visibility: hidden">
                <?php
                for( $i = 1; $i < count($stack_trace); $i++ )
                {
                    $call_fnc = $stack_trace[$i]["function"];
                    $call_fnc_args = $stack_trace[$i]["args"];
                ?>
                <tr style="padding-top: 5px">
                  <td width="100%" colspan="2">
                    <?php echo($call_fnc) ?>(<?php
                    if( count($call_fnc_args) > 0 )
                    {
                        ?><a onClick="return toggle_panel_status(call_<?php echo("{$debugfx_item_id}_{$i}")?>)" href="">...</a><?php
                    }
                    ?>)
                  </td>
                </tr>
                <tr width="100%" id="call_<?php echo("{$debugfx_item_id}_{$i}")?>" style="position: absolute; visibility: hidden">
                  <td width="10%">&nbsp;</td>
                  <td width="100%" style="border-left: 2px solid black; padding-left: 8px">
                    <?php
                    for( $j = 0; $j < count($call_fnc_args); $j++ )
                    {
                        $arg = "";
                        //$arg = var_export( $call_fnc_args[$j], true );
                        $arg = object_to_text( $call_fnc_args[$j] );
                        $arg = str_replace( "\n", "", $arg );
                        $arg = htmlencode( $arg );
                        $arg = str_replace( "&nbsp;", " ", $arg );
                        
                        echo( "<i>\$$j</i> = $arg<br>" );
                    }
                    ?>
                  </td>
                </tr>
                <?php
                }
                ?>

              </table>
                <script language="JavaScript">
                items_count[<?php echo($debugfx_item_id)?>] = <?php echo($i)?>;
                </script>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <?php
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * Chiusura della grafica del debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @return     void
 */
function debugfx_end()
{
    ob_start();
    ?>
  </table>

</div>
</center>
    <?php
    
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * Formatta un dato di tipo Sysinfo
 *
 * @author		Fabrizio Colonna
 * @see			debugfx_write_item
 * @date		25/01/2007
 * @return		String
 */
function debugfx_show_type_sysinfo( $data )
{
    $text = "<font color=\"#00AA00\">{$data["type_data"]["text"]}</font>";
    return debugfx_write_item( $text, $data["time"], $data["stack_trace"], $data["tags"], "#EEFFEE" );
}

/**
 * Cancella la lista delle informazioni di debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @return     String
 */
function debugfx_show_type_marker( $data )
{
    global $debugfx_mark_count;
    $debugfx_mark_count = $debugfx_mark_count + 1;
    
    $text = $data["type_data"]["description"];    
    $text = "<font color=\"#ff0000\"><b>Marker n.$debugfx_mark_count</b>&nbsp;&nbsp;$text</font>";

    return debugfx_write_item( $text, $data["time"], $data["stack_trace"], $data["tags"], "#EEEEFF" );
}

/**
 * Cancella la lista delle informazioni di debug
 *
 * @author     Fabrizio Colonna
 * @date       25/01/2007
 * @return     String
 */
function debugfx_show_type_variable( $data )
{
    $text = $data["type_data"]["text"];
    $value = $data["type_data"]["value"];
    $warning = (int)$data["type_data"]["warning"];
    
    // Codificazione dei colori
    $colors["red1"] = 255 * ($warning / 10);
    $colors["blue1"] = 255 * (1 - ($warning / 10));
    $colors["blue2"] = 205 + 50 * (1 - $warning / 10);
    $colors["green1"] = $colors["blue2"];
    
    // Formattazione dei colori
    foreach( $colors as $name => $color )
    {
        $color = dechex( $color );
        $color = str_repeat( "0", 2 - strlen($color)) . $color;
        $colors[$name] = $color;
    }
    
    $colore_testo = "#{$colors["red1"]}00{$colors["blue1"]}";
    $colore_sfondo = "#FF{$colors["blue2"]}{$colors["green1"]}";
 
    $value = htmlencode( var_export($value, true) );
   	$value = str_replace( "&nbsp;", " ", $value );
    $text = "<font color=\"$colore_testo\">$text</font>&nbsp;=&nbsp;$value";
    
    return debugfx_write_item( $text, $data["time"], $data["stack_trace"], $data["tags"], $colore_sfondo );
}
?>