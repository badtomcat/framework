<?php

namespace Badtomcat\Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
//use ZipArchive;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/19
 * Time: 8:16
 */
class ShowVersion extends Command
{
//    protected $path;

    public function __construct()
    {
//        $this->path = $path;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ver');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<comment>dev</comment>");
//        $zip = new ZipArchive;
//        $res = $zip->open(__DIR__ . '/app.zip');
//        if ($res === TRUE) {
//            $output->writeln("<comment>ok</comment>");
//            $zip->extractTo($this->path);
//            $zip->close();
//        } else {
//            $output->writeln("<comment>failed.</comment>");
//        }
    }
}