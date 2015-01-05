<?php

namespace SensioLabs\Melody\Console\Command;

use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Melody;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * RunCommand
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('execute a script')
            ->addArgument('script', InputArgument::REQUIRED, 'Which script do you want to run?')
            ->addArgument('arguments', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Which arguments do you want to pass to the script?')
            ->addOption('no-cache', null, InputOption::VALUE_NONE, 'If set, do not rely on previous cache.')
            ->addOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.')
            ->setHelp(
<<<EOT
The <info>run</info> command executes single-file scripts using Composer

<info>php melody.phar run test.php</info>

You may also run a gist file:

<info>php melody.phar run https://gist.github.com/lyrixx/23bb3980daf65154c3d4</info>

Or a stream resource

<info>php melody.phar run http://my.private/snippets/test.php</info>
<info>curl http://my.private/snippets/demo.php | php melody.phar run php://stdin -- --arg1</info>

If you want to debug things a little bit, it might be useful to use:

<info>php melody.phar run -vvv --no-cache test.php</info>

If you want to pass arguments to your script, use:

<info>php melody.phar run test.php -- -a --arg1 arg2</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $melody = new Melody();

        $processHelper = $this->getHelperSet()->get('process');

        $cliExecutor = function (Process $process, $verbose) use ($output, $processHelper) {
            if ($verbose) {
                // print debugging output for the build process
                $processHelper->mustRun($output, $process);
            } else {
                $callback = function ($type, $text) use ($output) {
                    if ($type == 'out' && $output instanceof ConsoleOutputInterface) {
                        $output = $output->getErrorOutput();
                    }
                    $output->write($text);
                };
                $process->run($callback);
            }
        };

        $configuration = new RunConfiguration($input->getOption('no-cache'), $input->getOption('prefer-source'));

        $script = $input->getArgument('script');
        $arguments = $input->getArgument('arguments');

        $melody->run($script, $arguments, $configuration, $cliExecutor);
    }
}
