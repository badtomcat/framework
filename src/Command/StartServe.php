<?php

namespace Badtomcat\Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;

//use ZipArchive;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 8:16
 */
class StartServe extends Command
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('host','s',InputOption::VALUE_OPTIONAL,'Host','127.0.0.1');
        $this->addOption('port','p',InputOption::VALUE_OPTIONAL,'Port',17739);
        $this->setName('serve');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        chdir($this->path.'/web');

        $output->writeln("<info>Laravel development server started:</info> <http://{$this->host($input)}:{$this->port($input)}>");
        //"c:\xampp\php\php.exe" -S 127.0.0.1:8000 "D:\git\tpv3"/server.php

//        print $this->serverCommand($input);
        passthru($this->serverCommand($input));
    }

    /**
     * Get the full server command.
     *
     * @param InputInterface $input
     * @return string
     */
    protected function serverCommand(InputInterface $input)
    {
        return sprintf('%s -S %s:%s %s/server.php',
            ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false)),
            $this->host($input),
            $this->port($input),
            ProcessUtils::escapeArgument($this->path.'')
        );
    }

    /**
     * Get the host for the command.
     *
     * @param InputInterface $input
     * @return string
     */
    protected function host(InputInterface $input)
    {
        return $input->getOption('host');
    }

    /**
     * Get the port for the command.
     *
     * @param InputInterface $input
     * @return string
     */
    protected function port(InputInterface $input)
    {
        return $input->getOption('port');
    }

}