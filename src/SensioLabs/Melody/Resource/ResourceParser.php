<?php

namespace SensioLabs\Melody\Resource;

use SensioLabs\Melody\Exception\ParseException;
use Symfony\Component\Yaml\Exception\ParseException as YamlException;
use Symfony\Component\Yaml\Yaml;

/**
 * ResourceParser.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ResourceParser
{
    const MELODY_PATTERN = '{^(?:#![^\n]+\n)?<\?php\s*<<<CONFIG(?P<config>.+)CONFIG;(?P<code>.+)$}sU';
    
    public function parseResource(Resource $resource)
    {
        preg_match(self::MELODY_PATTERN, $resource->getContent(), $matches);

        if (!$matches) {
            throw new ParseException('Impossible to parse the content of the document.');
        }

        try {
            return Yaml::parse($matches['config']);
        } catch (YamlException $e) {
            throw new ParseException('The front matter is not a valid Yaml.', 0, $e);
        }
    }
}
