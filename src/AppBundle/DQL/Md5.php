<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 21.07.16
 * Time: 10:21
 */

namespace AppBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class Md5
 * custom md5 function for dql
 * @package AppBundle\DQL
 */
class Md5 extends FunctionNode
{
	public $value;

	/**
	 * @param Parser $parser
	 */
	public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		$this->value = $parser->StringPrimary();
		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

	/**
	 * @param SqlWalker $sqlWalker
	 * @return string
	 */
	public function getSql(SqlWalker $sqlWalker)
	{
		return 'MD5(' . $this->value->dispatch($sqlWalker) . ')';
	}
}
