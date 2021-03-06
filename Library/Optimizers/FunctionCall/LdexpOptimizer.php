<?php

/*
 * This file is part of the Zephir.
 *
 * (c) Zephir Team <team@zephir-lang.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Zephir\Optimizers\FunctionCall;

use Zephir\Call;
use Zephir\CompilationContext;
use Zephir\CompiledExpression;
use Zephir\Exception\CompilerException;
use Zephir\Optimizers\MathOptimizer;

/**
 * LdexpOptimizer.
 *
 * Optimizes calls to 'ldexp' using internal function
 */
class LdexpOptimizer extends MathOptimizer
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getFunctionName()
    {
        return 'ldexp';
    }

    /**
     * @param array              $expression
     * @param Call               $call
     * @param CompilationContext $context
     *
     * @throws CompilerException
     *
     * @return bool|CompiledExpression|mixed
     */
    public function optimize(array $expression, Call $call, CompilationContext $context)
    {
        if (!isset($expression['parameters']) || 2 != \count($expression['parameters'])) {
            throw new CompilerException(sprintf("'%s' requires two parameters", $this->getFunctionName()), $expression);
        }

        $resolvedParams = $call->getReadOnlyResolvedParams($expression['parameters'], $context, $expression);

        $context->headersManager->add('kernel/math');

        return new CompiledExpression(
            'double',
            'zephir_'.$this->getFunctionName().'('.$resolvedParams[0].', '.$resolvedParams[1].' TSRMLS_CC)',
            $expression
        );
    }
}
