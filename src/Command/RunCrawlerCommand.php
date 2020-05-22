<?php

declare(strict_types=1);

namespace Command;

use Crawler\CrawlerContainer;
use Database\DatabaseInterface;
use DateTime;
use Formatter\CLIFormatter;
use Notification\NotificationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCrawlerCommand extends Command
{
    protected static $defaultName = 'crawler:run';

    private $crawlerContainer;
    private $notificationManager;
    private $database;

    /** @var SymfonyStyle */
    private $io;

    public function __construct(CrawlerContainer $crawlerContainer, NotificationManager $notificationManager, DatabaseInterface $database)
    {
        parent::__construct();

        $this->crawlerContainer = $crawlerContainer;
        $this->notificationManager = $notificationManager;
        $this->database = $database;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Run the crawler.')
            ->setHelp('Run the crawler.')
            ->addOption('display', null, InputOption::VALUE_NONE, 'When set, new ads will be displayed.')
            ->addOption('notify', null, InputOption::VALUE_NONE, 'When set, notifications will be sent.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->crawlerContainer->crawl();
        $newAds = $this->database->getNewAds();

        if ($input->getOption('display')) {
            $this->io->section(sprintf('%s - New ads', (new DateTime())->format('Y-m-d H:i:s')));

            if (0 === count($newAds)) {
                $this->io->text('No new ad');

                return 0;
            }

            $this->io->table(CLIFormatter::getHeaders($newAds), CLIFormatter::format($newAds));
        }

        if ($input->getOption('notify')) {
            $this->notificationManager->notify($newAds);
        }

        return 0;
    }
}
