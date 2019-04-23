<?php

namespace SensioLabs\Melody\Runner;

use SensioLabs\Melody\Resource\LocalResource;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Script\Script;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Runner.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Runner
{
    const BOOTSTRAP_FILENAME = 'bootstrap.php';

    private $vendorDir;

    public function __construct($vendorDir = 'vendor')
    {
        $this->vendorDir = trim($vendorDir, '/');
    }

    public function getProcess(Script $script, $dir)
    {
        $bootstrap = $this->getBootstrap($script->getResource());

        $file = sprintf('%s/%s', $dir, self::BOOTSTRAP_FILENAME);

        file_put_contents($file, $bootstrap);

        $phpFinder = new PhpExecutableFinder();

        $process = ProcessBuilder::create(array_merge(
            array($phpFinder->find(false)),
            $script->getConfiguration()->getPhpOptions(),
            $phpFinder->findArguments(),
            array($file),
            $script->getArguments()
        ))
            // forward the PATH variable from the user running the webserver, to the subprocess
            // so it can find binaries like e.g. composer
            ->setEnv('PATH', $_SERVER['PATH'])
            ->getProcess()
        ;

        if (!defined('PHP_WINDOWS_VERSION_BUILD') && PHP_SAPI === 'cli') {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
            }
        }

        return $process;
    }

    private function getBootstrap(Resource $resource)
    {
        $template = <<<TEMPLATE
{{ head }}
?>{{ code }}

TEMPLATE;

        return strtr($template, array(
            '{{ head }}' => $this->getHead(),
            '{{ code }}' => $resource->getContent(),
        ));
    }

    private function getHead()
    {
        $head = <<<'HEAD'
<?php

require __DIR__.'/{{ vendorDir }}/autoload.php';

// Error handling part
error_reporting(-1);
ini_set('display_errors', 1);
set_error_handler(function ($level, $message, $file = 'unknown', $line = 0, $context = array()) {
    $message = sprintf('%s: %s in %s line %d', $level, $message, $file, $line);

    throw new \ErrorException($message, 0, $level, $file, $line);
});

HEAD;

        return strtr($head, array(
            '{{ vendorDir }}' => $this->vendorDir,
        ));
    }
}
