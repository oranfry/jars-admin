<?php

namespace jars\admin;

use obex\Obex;

class Helper
{
    public static function parseChildPath(object $jars, string $childpath_string, string $linetype, string $id, array &$lines, array &$linetypes, &$me = null, &$context = null): array
    {
        if (!$childpath_string) {
            return [];
        }

        $me = Obex::from($lines)
            ->filter('id', 'is', $id)
            ->find('type', 'is', $linetype);

        if (!$me) {
            return [];
        }

        $myLinetype = Obex::from($linetypes)
            ->find('name', 'is', $linetype);

        for ($_childpath = $childpath_string; preg_match('/^(\/([a-z]+))/', $_childpath, $matches); ) {
            $context = $me;
            $_childpath = substr($_childpath, strlen($matches[1]));

            $property = $matches[2];
            $id = null;

            preg_match('/^(\/([0-9a-f]{64}))/', $_childpath, $matches);

            $_childpath = substr($_childpath, strlen($matches[1]));
            $id = $matches[2];

            $child = Obex::from($myLinetype->children)
                ->find('property', 'is', $property);

            $lines = $me->$property;
            $linetype = $child->linetype;
            $only_parent = $child->only_parent;

            $linetypes = Obex::from($jars->linetypes())
                ->filter('name', 'is', $linetype)
                ->resolve();

            if ($id) {
                $me = Obex::from($lines)
                    ->filter('id', 'is', $id)
                    ->find('type', 'is', $linetype);
            }

            $myLinetype = Obex::from($linetypes)
                ->find('name', 'is', $linetype);

            $childpath[] = (object) compact(
                'id',
                'linetype',
                'only_parent',
                'property',
            );

            if (!$me) {
                break;
            }
        }

        return $childpath;
    }
}
