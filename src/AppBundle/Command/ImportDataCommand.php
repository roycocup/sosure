<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPExcel_IOFactory;

class ImportDataCommand extends ContainerAwareCommand
{
    public $dataPath = __DIR__ .'/../../../web/data/';

    private $_incomeFilename;
    private $_mappingFilename;

    protected function configure()
    {

        $this
            ->setName('import-data')
            ->setDescription('Command line tool to import, extract and persist data from the online excel/csv.')
            ->addArgument('filename1', InputArgument::REQUIRED, 'Please provide the name of the file that contains the income data')
            ->addArgument('filename2', InputArgument::REQUIRED, 'Please provide the name of the file that contains MSOA to Post Code mapping')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //$objPHPExcel = PHPExcel_IOFactory::load("05featuredemo.xlsx");
//        $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter(',')
//            ->setEnclosure('"')
//            ->setLineEnding("\r\n")
//            ->setSheetIndex(0);


        // test entities, headers
        // iterate the files and insert records


        $this->_incomeFilename = $input->getArgument('filename1');
        $this->_mappingFilename = $input->getArgument('filename2');


        if (!file_exists($this->dataPath.$this->_incomeFilename)){
            $output->writeln('File not found in ' . $this->dataPath.$this->_incomeFilename);
        }else if(!file_exists($this->dataPath.$this->_mappingFilename)){
            $output->writeln('File not found in ' . $this->dataPath.$this->_mappingFilename);
        }

        $incomeReader = PHPExcel_IOFactory::createReaderForFile($this->dataPath.$this->_incomeFilename);
        $mappingsReader = PHPExcel_IOFactory::createReaderForFile($this->dataPath.$this->_mappingFilename);


        $output->writeln('All data imported.');
    }

}
