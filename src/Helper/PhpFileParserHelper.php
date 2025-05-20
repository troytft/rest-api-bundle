<?php

declare(strict_types=1);

namespace RestApiBundle\Helper;

use Symfony\Component\Finder\SplFileInfo;

class PhpFileParserHelper
{
    public static function getClassByFileInfo(SplFileInfo $fileInfo): ?string
    {
        $tokens = \token_get_all($fileInfo->getContents());

        $namespaceTokenOpened = false;
        $namespace = '';

        foreach ($tokens as $token) {
            if (\is_array($token) && \T_NAMESPACE === $token[0]) {
                $namespaceTokenOpened = true;
            } elseif ($namespaceTokenOpened && \is_array($token) && \T_WHITESPACE !== $token[0]) {
                $namespace .= $token[1];
            } elseif ($namespaceTokenOpened && \is_string($token) && ';' === $token) {
                break;
            }
        }

        if (!$namespace) {
            return null;
        }

        $fileNameWithoutExtension = $fileInfo->getBasename('.'.$fileInfo->getExtension());

        return \sprintf('%s\%s', $namespace, $fileNameWithoutExtension);
    }
}
