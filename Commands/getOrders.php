<?php

namespace FatchipAfterbuy\Commands;

use FatchipAfterbuy\Services\ReadData\ReadDataInterface;
use FatchipAfterbuy\Services\WriteData\WriteDataInterface;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

class getOrders extends ShopwareCommand
{
    /**
     * @var ReadDataInterface
     */
    protected $readDataService;

    /**
     * @var WriteDataInterface
     */
    protected $writeDataService;

    /**
     * @param ReadDataInterface $readDataService
     * @param WriteDataInterface $writeDataService
     */
    public function __construct(ReadDataInterface $readDataService, WriteDataInterface $writeDataService) {
        parent::__construct(null);

        $this->readDataService = $readDataService;
        $this->writeDataService = $writeDataService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('Afterbuy:Get:Orders')
            ->setDescription('Receive orders from Afterbuy')
            /*->addArgument(
                'my-argument',
                InputArgument::REQUIRED,
                'An required argument (positional)'
            )
            ->addOption(
                'my-option',
                null,
                InputOption::VALUE_OPTIONAL,
                'An optional *option*',
                'My-Default-Value'
            )*/
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
        /**
         * Structure for receiving and writing data
         * Should look everywhere the same.
         * Dependenciies are handeld via services.xml
         */

        /**
         * filter array is unused yet but can be implemented
         */
        $filter = array();

        $data = $this->readDataService->get($filter);
        $this->writeDataService->put($data);
    }
}
