<?php

declare(strict_types=1);

namespace Command;

use Crawler\CrawlerContainer;
use Database\DatabaseInterface;
use DateTime;
use Event\CrawlingUrlEvent;
use Formatter\CLIFormatter;
use Notification\NotificationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RunCrawlerCommand extends Command
{
    protected static $defaultName = 'run';

    private $crawlerContainer;
    private $notificationManager;
    private $database;
    private $eventDispatcher;

    /** @var SymfonyStyle */
    private $io;

    public function __construct(
        CrawlerContainer $crawlerContainer,
        NotificationManager $notificationManager,
        DatabaseInterface $database,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct();

        $this->crawlerContainer = $crawlerContainer;
        $this->notificationManager = $notificationManager;
        $this->database = $database;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Run the crawler.')
            ->setHelp('Run the crawler.')
            ->addOption('show-only', null, InputOption::VALUE_NONE, 'When set, only show ads already crawled.')
            ->addOption('notify', null, InputOption::VALUE_NONE, 'When set, notifications will be sent.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->section(sprintf('%s - Starting crawl', (new DateTime())->format('Y-m-d H:i:s')));
        $progressBar = $this->createProgressBar();

        $this->eventDispatcher->addListener(CrawlingUrlEvent::NAME, function (CrawlingUrlEvent $event) use ($progressBar) {
            $progressBar->setMessage($event->getFormattedUrl(), 'title');
            $progressBar->advance();

            if ($this->crawlerContainer->getTotalNumberOfUrls() === $progressBar->getProgress()) {
                $progressBar->finish();
            }
        });

        if ($input->getOption('show-only')) {
            return $this->displayAds($this->database->getAds());
        }

        $this->crawlerContainer->crawl();
        $newAds = $this->database->getNewAds();

        $this->displayAds($newAds);

        if ($input->getOption('notify')) {
            $this->notificationManager->notify($newAds);
        }

        return 0;
    }

    private function displayAds(array $ads): int
    {
        $this->io->section(sprintf('%s - New ads', (new DateTime())->format('Y-m-d H:i:s')));

        if (0 === count($ads)) {
            $this->io->text('No new ad');

            return 0;
        }

        $this->io->table(CLIFormatter::getHeaders($ads), CLIFormatter::format($ads));

        return 0;
    }

    private function createProgressBar(): ProgressBar
    {
        $progressBar = $this->io->createProgressBar($this->crawlerContainer->getTotalNumberOfUrls());

        $progressBar->setFormat("%current%/%max% %bar% %percent:3s%% \e[2m%title%\n\n\e[0m");
        $progressBar->setBarCharacter("\033[32m▓\033[0m");
        $progressBar->setEmptyBarCharacter("\033[31m░\033[0m");
        $progressBar->setProgressCharacter('');

        $progressBar->start();

        return $progressBar;
    }
}
