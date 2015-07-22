<?php

namespace SensioLabs\Melody\Runner;

use SensioLabs\Melody\Resource\LocalResource;
use SensioLabs\Melody\Resource\Resource;
use SensioLabs\Melody\Script\Script;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessBuilder;

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
        if ($script->getResource() instanceof LocalResource) {
            $bootstrap = $this->getLocalBootstrap($script->getResource());
        } else {
            $bootstrap = $this->getRemoteBootstrap($script->getResource());
        }

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

        if (!defined('PHP_WINDOWS_VERSION_BUILD') && php_sapi_name() === 'cli') {
            $process->setTty(true);
        }

        return $process;
    }

    private function getLocalBootstrap(LocalResource $resource)
    {
        $template = <<<'TEMPLATE'
{{ head }}

require '{{ script_filename }}';

TEMPLATE;

        return strtr($template, array(
            '{{ head }}' => $this->getHead(),
            '{{ script_filename }}' => $resource->getFilename(),
        ));
    }

    private function getRemoteBootstrap(Resource $resource)
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
