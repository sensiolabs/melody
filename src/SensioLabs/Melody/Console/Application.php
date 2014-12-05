<?php

namespace SensioLabs\Melody\Console;

use SensioLabs\Melody\Console\Command\RunCommand;
use SensioLabs\Melody\Console\Command\SelfUpdateCommand;
use SensioLabs\Melody\Melody;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\ProcessHelper;

/**
 * Application
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class Application extends BaseApplication
{
    const MANIFEST_URI = 'http://melody.sensiolabs.org/manifest.json';

    private static $logo = '
          :::   :::   :::::::::: :::        ::::::::  :::::::::  :::   :::
        :+:+: :+:+:  :+:        :+:       :+:    :+: :+:    :+: :+:   :+:
      +:+ +:+:+ +:+ +:+        +:+       +:+    +:+ +:+    +:+  +:+ +:+
     +#+  +:+  +#+ +#++:++#   +#+       +#+    +:+ +#+    +:+   +#++:
    +#+       +#+ +#+        +#+       +#+    +#+ +#+    +#+    +#+
   #+#       #+# #+#        #+#       #+#    #+# #+#    #+#    #+#
  ###       ### ########## ########## ########  #########     ###
';

    public function __construct()
    {
        parent::__construct('Melody', Melody::VERSION);
    }

    public function getHelp()
    {
        return self::$logo.parent::getHelp();
    }

    public function getLongVersion()
    {
        $version = parent::getLongVersion().' by <comment>SensioLabs</comment>';
        $commit = '@git-commit@';

        if ('@'.'git-commit@' !== $commit) {
            $version .= ' ('.substr($commit, 0, 7).')';
        }

        return $version;
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new RunCommand();
        $commands[] = new SelfUpdateCommand();

        return $commands;
    }

    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        $helperSet->set(new ProcessHelper());

        return $helperSet;
    }
}
