<?php

namespace SensioLabs\Melody\Console\Command;

use SensioLabs\Melody\Configuration\RunConfiguration;
use SensioLabs\Melody\Configuration\UserConfigurationRepository;
use SensioLabs\Melody\Exception\TrustException;
use SensioLabs\Melody\Melody;
use SensioLabs\Melody\Resource\Resource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

/**
 * RunCommand.
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
            ->addOption('trust', 't', InputOption::VALUE_NONE, 'Trust the resource.')
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

If you trust a remote resource, use:

<info>php melody.phar run https://gist.github.com/lyrixx/23bb3980daf65154c3d4 --trust</info>

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $melody = new Melody();
        $configRepository = new UserConfigurationRepository();
        $userConfig = $configRepository->load();

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

                return $process->run($callback);
            }
        };

        $runConfiguration = new RunConfiguration(
            $input->getOption('no-cache'),
            $input->getOption('prefer-source'),
            $input->getOption('trust')
        );

        $script = $input->getArgument('script');
        $arguments = $input->getArgument('arguments');

        while (true) {
            try {
                $melody->run($script, $arguments, $runConfiguration, $userConfig, $cliExecutor);

                return 0;
            } catch (TrustException $e) {
                if (false === $this->confirmTrust($e->getResource(), $input, $output)) {
                    $output->writeln('<error>Operation aborted by the user.</error>');

                    return 1;
                }

                $userConfig->addTrustedSignature($e->getResource()->getSignature());
                $configRepository->save($userConfig);
            }
        }
    }

    private function confirmTrust(Resource $resource, InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $message = <<<EOT
<comment>You are running an untrusted resource</comment>
  <info>URL:            </info> %s
  <info>Revision:       </info> #%d
  <info>Owner:          </info> %s
  <info>Created at:     </info> %s
  <info>Last update:    </info> %s

EOT;
        $output->writeln(sprintf(
            $message,
            $resource->getMetadata()->getUri(),
            $resource->getMetadata()->getRevision(),
            $resource->getMetadata()->getOwner(),
            $resource->getMetadata()->getCreatedAt()->format(\DateTime::RSS),
            $resource->getMetadata()->getUpdatedAt()->format(\DateTime::RSS)
        ));

        $actions = array('abort', 'continue', 'show-code');
        $defaultAction = 'abort';
        $actionLabels = array_map(function ($action) use ($defaultAction) {
            if ($action === $defaultAction) {
                return sprintf('<%1$s>%2$s</%1$s>', 'default', $action);
            }

            return $action;
        }, $actions);

        $defaultStyle = clone($output->getFormatter()->getStyle('question'));
        $defaultStyle->setOptions(array('reverse'));
        $output->getFormatter()->setStyle('default', $defaultStyle);

        $question = new Question(
            sprintf('<question>What do you want to do (%s)?</question> ', implode(', ', $actionLabels)),
            $defaultAction
        );
        $question->setAutocompleterValues($actions);
        $action = $this->getHelper('question')->ask($input, $output, $question);

        if ($action === 'show-code') {
            $output->writeln(PHP_EOL.$resource->getContent().PHP_EOL);

            return $dialog->askConfirmation($output, '<question>Do you want to continue [y/N]?</question> ', false);
        }

        return $action === 'continue';
    }
}
