<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MarkdownTwigExtension extends AbstractExtension
{
    const NAMESPACE_LINKS = [
        'ORM\\' => '/orm/reference',
        'Illuminate\\Support\\' => '/orm/reference',
    ];
    public function getFilters()
    {
        return [
            new TwigFilter('linkClasses', [$this, 'linkClasses'], ['is_safe' => ['all']]),
            new TwigFilter('lcFirst', 'lcfirst'),
            new TwigFilter('join', function ($value, $glue = ', ') {
                if (is_array($value)) {
                    return implode($glue, $value);
                }

                if ($value instanceof \phpDocumentor\Descriptor\Collection) {
                    return implode($glue, $value->getAll());
                }

                return $value;
            }),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('generateSignature', [$this, 'generateSignature'], ['is_safe' => ['html']]),
        ];
    }

    public function linkClasses($value)
    {
        return preg_replace_callback(
            '/\\\\([A-Za-z0-9_\\\\]+)/',
            function ($match) {
                $type = $match[1];
                foreach (self::NAMESPACE_LINKS as $namespace => $link) {
                    if (strncmp($type, $namespace, strlen($namespace)) === 0) {
                        return '[' . $type . '](' . $link . '/' . str_replace('\\', '/', $type) . ')';
                    }
                }
                return $type;
            },
            $value
        );
    }

    /**
     * @param \phpDocumentor\Descriptor\MethodDescriptor $method
     */
    public function generateSignature($method, $maxLength = null)
    {
        $signature = 'function ' . $method->getName() . '(';
        $args = [];

        foreach ($method->getArguments() as $argument) {
            $arg = '$' . $argument->getName();
            if ($argument->isByReference()) {
                $arg = '&' . $arg;
            }
            if ($argument->getType() !== 'mixed') {
                $arg = $argument->getType() . ' ' . $arg;
            }
            if ($argument->getDefault()) {
                $arg = $arg . ' = ' . $argument->getDefault();
            }
            $args[] = $arg;
        }

        $signature .= implode(', ', $args) . ')';

        if ($method->isStatic()) {
            $signature = 'static ' . $signature;
        }

        $signature = $method->getVisibility() . ' ' . $signature;

        if ($method->isAbstract()) {
            $signature = 'abstract ' . $signature;
        }

        if ($method->isFinal()) {
            $signature = 'final ' . $signature;
        }

        if ($method->getResponse() && $method->getName() !== '__construct') {
            $type = $method->getResponse()->getType();
            if ($type === 'self') {
                $signature .= ': ' . $method->getParent()->getName();
            } else {
                $signature .= ': ' . $type;
            }
        }

        if ($maxLength && strlen($signature) > $maxLength) {
            if (preg_match('/^(.*)\((.*)\)($|:.*$)/', $signature, $matches)) {
                $lines = [$matches[1] . '('];
                $args = explode(', ', $matches[2]);
                foreach ($args as $arg) {
                    $lines[] = '    ' . $arg . ', ';
                }
                $lines[] = ')' . $matches[3];
                $signature = implode("\n", $lines);
            }
        }

        return $signature;
    }
}
