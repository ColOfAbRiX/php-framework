<?php
function a()
{
	echo( "A" );	
}

function b()
{
	echo( "B" );
}

function c()
{
	echo( "C" );
}

class prova
{
	var $links;
	
	function __construct()
	{
		$this->links[0] = Array( "uno", "a" );
		$this->links[1] = Array( "due", "b" );
	}
	
	function __call( $nome, $bho )
	{
		foreach( $this->links as $ln )
		{
			if( $ln[0] == $nome )
			{
				$funzione = $ln[1];
				$funzione();
			}
		}
	}
};

$a = new prova();
a();		// Chiamata da dentro il kernel
$a->uno();	// Chiamata da un package che usa il wrapper
$a->due();	// Chiamata da un package che usa il wrapper

$a->links[1] = Array( "due", "c" );
a();		// Chiamata da dentro il kernel
$a->uno();	// Chiamata da un package che usa il wrapper
$a->due();	// Chiamata da un package che usa il wrapper
?>