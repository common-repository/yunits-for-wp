<?php
/**
 * @license MIT
 *
 * Modified by yardinternet on 28-October-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace YunitsForWP\Vendor_Prefixed\DI\Definition;

/**
 * A definition that extends a previous definition with the same name.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface ExtendsPreviousDefinition extends Definition
{
    public function setExtendedDefinition(Definition $definition);
}
