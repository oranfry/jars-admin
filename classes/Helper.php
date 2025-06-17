<?php

namespace jars\admin;

use obex\Obex;

class Helper
{
    public static function parseChildPath(object $jars, string $childpath_string, string $linetype, string $id, array &$lines, array &$linetypes, &$me = null): array
    {
        if (!$childpath_string) {
            return [];
        }

        $me = Obex::from($lines)
            ->filter('id', 'is', $id)
            ->find('type', 'is', $linetype);

        if (!$me) {
            throw new Exception('No such line to drill into (1)');
        }

        $myLinetype = Obex::from($linetypes)
            ->find('name', 'is', $linetype);

        for ($_childpath = $childpath_string; preg_match('/^(\/([a-z]+))/', $_childpath, $matches); ) {
            $_childpath = substr($_childpath, strlen($matches[1]));

            $property = $matches[2];
            $id = null;

            if (preg_match('/^(\/([0-9a-f]{64}))/', $_childpath, $matches)) {
                $_childpath = substr($_childpath, strlen($matches[1]));
                $id = $matches[2];
            } elseif ($_childpath) {
                throw new Exception('Invalid child path ' . $_childpath);
            }

            $child = Obex::from($myLinetype->children)
                ->find('property', 'is', $property);

            $lines = $me->$property;
            $linetype = $child->linetype;

            $linetypes = Obex::from($jars->linetypes())
                ->filter('name', 'is', $linetype)
                ->resolve();

            if ($id) {
                $me = Obex::from($lines)
                    ->filter('id', 'is', $id)
                    ->find('type', 'is', $linetype);

                if (!$me) {
                    throw new Exception('No such line to drill into (2)');
                }
            }

            $myLinetype = Obex::from($linetypes)
                ->find('name', 'is', $linetype);

            $childpath[] = (object) compact('property', 'linetype', 'id');
        }

        return $childpath;
    }
}
