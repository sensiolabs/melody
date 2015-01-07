<?php

namespace SensioLabs\Melody\WorkingDirectory;

use Symfony\Component\Filesystem\Filesystem;

/**
 * WorkingDirectoryFactory
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class WorkingDirectoryFactory
{
    private $storagePath;
    private $filesystem;

    public function __construct($storagePath)
    {
        $this->storagePath = $storagePath;
        $this->filesystem = new Filesystem();

        $this->filesystem->mkdir($this->storagePath);
    }

    public function createTmpDir(array $packages, array $repositories)
    {
        $hash = $this->generateHash($packages, $repositories);

        $path = sprintf('%s/%s', $this->storagePath, $hash);

        return new WorkingDirectory($path, $this->filesystem);
    }

    private function generateHash(array $packages, array $repositories)
    {
        ksort($packages);

        if (empty($repositories)) {
            $config = $packages;
        } else {
            //var_dump($repositories);
            //var_dump("==============\n");
            $this->sortRepositories($repositories);
            //var_dump($repositories);
            $config = array($repositories, $packages);
        }

        // Some application use `basename(__DIR__)` and may generate class
        // name with this dirname. And a sha256 hash may start with a number.
        // This will lead to a fatal error because PHP forbid that.
        return 'a'.hash('sha256', serialize($config));
    }

    private function extractRepositoriesUrls($repositories)
    {
        $urls = array();
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($repositories));

        foreach ($it as $key => $leaf) {
            if ('url' === $key) {
                $repositoryKey = $it->getSubIterator(0)->key();
                $urls[$repositoryKey][] = $leaf;
            }
        }

        return $urls;
    }

    private function sortRepositories(array &$repositories)
    {

        var_dump($this->extractRepositoriesUrls($repositories));
die('ieee');
        // trier les clés du tableau de façon recursive
        // cherche la clé url
//        $urls = array_map(function ($item) {
//            // chercher la clé url
//            if (isset($item['url'])) {
//                return $item['url'];
//            }
//            if (isset($item['package']['dist']['url'])) {
//                return $item['package']['dist']['url'];
//            }
//            if (isset($item['package']['source']['url'])) {
//                return $item['package']['source']['url'];
//            }
//
//        }, $repositories);
//
//        // trier le repositories
//        array_multisort($urls, \SORT_ASC, $repositories);
//        var_dump('uuuuuuuuuu');
//        var_dump($repositories);
//        var_dump('pppppppppuuuuuuuuuu');
//        $repositories = array_map('serialize', $repositories);
//        sort($repositories);
    }
}
