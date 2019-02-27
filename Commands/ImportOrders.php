<?php

namespace viaebShopwareAfterbuy\Commands;

use viaebShopwareAfterbuy\Services\ReadData\ReadDataInterface;
use viaebShopwareAfterbuy\Services\WriteData\WriteDataInterface;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

class ImportOrders extends ShopwareCommand
{
    /**
     * @var ReadDataInterface
     */
    protected $readDataService;

    /**
     * @var \abaccAfterbuy\Services\WriteData\Internal\WriteOrdersService
     */
    protected $writeDataService;

    /**
     * @param ReadDataInterface $readDataService
     * @param WriteDataInterface $writeDataService
     */
    public function __construct(ReadDataInterface $readDataService, WriteDataInterface $writeDataService) {
        parent::__construct();

        $this->readDataService = $readDataService;
        $this->writeDataService = $writeDataService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('Afterbuy:Import:Orders')
            ->setDescription('Receive orders from Afterbuy')
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Use to import all'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> implements a command.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $this->writeDataService->getOrderImportDateFilter($input->getOption('force'));

        $data = $this->readDataService->get($filter);
        $this->writeDataService->put($data);
    }
}
