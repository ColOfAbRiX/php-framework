<?php
/**
 * Estensione della classe Smarty
 *
 * In tantissime parti del codice mi servirebbe sapere quali variabili un template richiede per
 * sapere a runtime quali caricare, ma la classe standard di Smarty non mi permette di farlo. In
 * questo file c'è una classe che estende la classe Smarty e le aggiunge questa funzionalità.
 * Wow, è proprio forte l'ereditarietà.
 *
 * @package    Kernel
 * @subpackage Admin
 * @author     Fabrizio Colonna <colofabrix@tin.it>
 * @copyright  Copyright 2005, Fabrizio Colonna
 */
// Signature
define( "KERNEL_ADMIN_ADV_SMARTY", 1 );
// Dipendenze
require_once( dirname(__FILE__)."/../libs/smarty/Smarty.class.php" );

/**
 * Advanced Smarty Class.
 *
 * Classe Smarty estesa per aggiungerle alcune funzionaltià di grande utilità per la flessibilità
 * del sito.
 *
 * @author     Fabrizio Colonna
 * @date       18/01/2006
 */
class AdvSmarty extends Smarty
{
	/**
	 * La funzione recupera le variabili presenti dentro il template.
	 *
	 * Il codice è un riadattamento di quello della funzione Smarty_Compile::_compile_file().
	 * NOTA: Quelle merde dei programmatori di smarty hanno chiamato una funzione get_templates_vars()
	 * mentre non fa assolutamente quello che le da il nome, ma restituisce le variabili assegnate,
	 * così ho dovuto inventarmi un altro nome. Ci tengo a dirlo perchè mi ha dato molto fastidio.
	 *
	 * @author     Fabrizio Colonna
	 * @date       18/01/2006
	 * @param      String    $resource Il nome della risorsa da analizzare
	 * @return     Array     Un array contenente tutte le variabili presenti nel template
	 */
	function get_vars_in_template( $resource )
	{
		return $this->_get_template( $resource, "variables" );
	}

	/**
	 * La funzione recupera le funzioni presenti dentro il template.
	 *
	 * Il codice è un riadattamento di quello della funzione Smarty_Compile::_compile_file().
	 *
	 * @author     Fabrizio Colonna
	 * @date       18/01/2006
	 * @param      String    $resource Il nome della risorsa da analizzare
	 * @return     Array     Un array contenente tutte le variabili presenti nel template
	 */
	function get_functions_in_template( $resource )
	{
		return $this->_get_template( $resource, "functions" );
	}

	function _get_template( $resource, $type )
	{
		$out = Array();
		$_match = Array();

		// Carico il contenuto del file
        $_params = array( "resource_name" => $resource );
        if( !$this->_fetch_resource_info($_params) )
        {
            return false;
        }

        $source_content = $_params["source_content"];  // Contenuto del file

        // Trovo tutti i tag Smarty
        $ldq = preg_quote($this->left_delimiter, "~");
        $rdq = preg_quote($this->right_delimiter, "~");

        preg_match_all( "~{$ldq}\s*(.*?)\s*{$rdq}~s", $source_content, $_match );
        $template_tags = $_match[1];

        $this->regexp();

    	switch( $type )
    	{
    		case "variables":
    			$reg_exp = $this->_var_regexp;
    			break;

    		case "functions":
    			$reg_exp = $this->_func_regexp;
    			break;
    	}

        // Trovo le variabili
        for( $i = 0; $i < count($template_tags); $i++ )
        {
        	$token = $template_tags[$i];
        	//$reserved = Array( "literal" );

        	// Comandi riservati di Smarty
        	if( $token == "literal" )
        	{
        		continue;
        	}

			if( preg_match( "~^$reg_exp$~", $token ) )
			{
				if( !array_search( $token, $out ) )
				{
					array_push( $out, $token );
				}
			}
        }

        return $out;
	}

    function regexp()
    {
        // matches double quoted strings:
        // "foobar"
        // "foo\"bar"
        $this->_db_qstr_regexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';

        // matches single quoted strings:
        // 'foobar'
        // 'foo\'bar'
        $this->_si_qstr_regexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

        // matches single or double quoted strings
        $this->_qstr_regexp = '(?:' . $this->_db_qstr_regexp . '|' . $this->_si_qstr_regexp . ')';

        // matches bracket portion of vars
        // [0]
        // [foo]
        // [$bar]
        $this->_var_bracket_regexp = '\[\$?[\w\.]+\]';

        // matches numerical constants
        // 30
        // -12
        // 13.22
        $this->_num_const_regexp = '(?:\-?\d+(?:\.\d+)?)';

        // matches $ vars (not objects):
        // $foo
        // $foo.bar
        // $foo.bar.foobar
        // $foo[0]
        // $foo[$bar]
        // $foo[5][blah]
        // $foo[5].bar[$foobar][4]
        $this->_dvar_math_regexp = '(?:[\+\*\/\%]|(?:-(?!>)))';
        $this->_dvar_math_var_regexp = '[\$\w\.\+\-\*\/\%\d\>\[\]]';
        $this->_dvar_guts_regexp = '\w+(?:' . $this->_var_bracket_regexp
                . ')*(?:\.\$?\w+(?:' . $this->_var_bracket_regexp . ')*)*(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?';
        $this->_dvar_regexp = '\$' . $this->_dvar_guts_regexp;

        // matches config vars:
        // #foo#
        // #foobar123_foo#
        $this->_cvar_regexp = '\#\w+\#';

        // matches section vars:
        // %foo.bar%
        $this->_svar_regexp = '\%\w+\.\w+\%';

        // matches all valid variables (no quotes, no modifiers)
        $this->_avar_regexp = '(?:' . $this->_dvar_regexp . '|'
           . $this->_cvar_regexp . '|' . $this->_svar_regexp . ')';

        // matches valid variable syntax:
        // $foo
        // $foo
        // #foo#
        // #foo#
        // "text"
        // "text"
        $this->_var_regexp = '(?:' . $this->_avar_regexp . '|' . $this->_qstr_regexp . ')';

        // matches valid object call (one level of object nesting allowed in parameters):
        // $foo->bar
        // $foo->bar()
        // $foo->bar("text")
        // $foo->bar($foo, $bar, "text")
        // $foo->bar($foo, "foo")
        // $foo->bar->foo()
        // $foo->bar->foo->bar()
        // $foo->bar($foo->bar)
        // $foo->bar($foo->bar())
        // $foo->bar($foo->bar($blah,$foo,44,"foo",$foo[0].bar))
        $this->_obj_ext_regexp = '\->(?:\$?' . $this->_dvar_guts_regexp . ')';
        $this->_obj_restricted_param_regexp = '(?:'
                . '(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')(?:' . $this->_obj_ext_regexp . '(?:\((?:(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . ')'
                . '(?:\s*,\s*(?:' . $this->_var_regexp . '|' . $this->_num_const_regexp . '))*)?\))?)*)';
        $this->_obj_single_param_regexp = '(?:\w+|' . $this->_obj_restricted_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_var_regexp . $this->_obj_restricted_param_regexp . ')))*)';
        $this->_obj_params_regexp = '\((?:' . $this->_obj_single_param_regexp
                . '(?:\s*,\s*' . $this->_obj_single_param_regexp . ')*)?\)';
        $this->_obj_start_regexp = '(?:' . $this->_dvar_regexp . '(?:' . $this->_obj_ext_regexp . ')+)';
        $this->_obj_call_regexp = '(?:' . $this->_obj_start_regexp . '(?:' . $this->_obj_params_regexp . ')?(?:' . $this->_dvar_math_regexp . '(?:' . $this->_num_const_regexp . '|' . $this->_dvar_math_var_regexp . ')*)?)';

        // matches valid modifier syntax:
        // |foo
        // |@foo
        // |foo:"bar"
        // |foo:$bar
        // |foo:"bar":$foobar
        // |foo|bar
        // |foo:$foo->bar
        $this->_mod_regexp = '(?:\|@?\w+(?::(?:\w+|' . $this->_num_const_regexp . '|'
           . $this->_obj_call_regexp . '|' . $this->_avar_regexp . '|' . $this->_qstr_regexp .'))*)';

        // matches valid function name:
        // foo123
        // _foo_bar
        $this->_func_regexp = '[a-zA-Z_]\w*';

        // matches valid registered object:
        // foo->bar
        $this->_reg_obj_regexp = '[a-zA-Z_]\w*->[a-zA-Z_]\w*';

        // matches valid parameter values:
        // true
        // $foo
        // $foo|bar
        // #foo#
        // #foo#|bar
        // "text"
        // "text"|bar
        // $foo->bar
        $this->_param_regexp = '(?:\s*(?:' . $this->_obj_call_regexp . '|'
           . $this->_var_regexp . '|' . $this->_num_const_regexp  . '|\w+)(?>' . $this->_mod_regexp . '*)\s*)';

        // matches valid parenthesised function parameters:
        //
        // "text"
        //    $foo, $bar, "text"
        // $foo|bar, "foo"|bar, $foo->bar($foo)|bar
        $this->_parenth_param_regexp = '(?:\((?:\w+|'
                . $this->_param_regexp . '(?:\s*,\s*(?:(?:\w+|'
                . $this->_param_regexp . ')))*)?\))';

        // matches valid function call:
        // foo()
        // foo_bar($foo)
        // _foo_bar($foo,"bar")
        // foo123($foo,$foo->bar(),"foo")
        $this->_func_call_regexp = '(?:' . $this->_func_regexp . '\s*(?:'
           . $this->_parenth_param_regexp . '))';
    }
}
?>