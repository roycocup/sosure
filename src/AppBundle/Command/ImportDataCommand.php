<?php

namespace AppBundle\Command;

use AppBundle\Entity\UserIncome;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PHPExcel_IOFactory;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ImportDataCommand extends ContainerAwareCommand
{
    public $dataPath = __DIR__ .'/../../../web/data/';
    public $output;
    public $input;
    public $numIncomeDataRows = 0;

    private $_incomeFilename;
    private $_mappingFilename;
    private $_em; // entity manager

    protected function configure()
    {

        $this
            ->setName('import-data')
            ->setDescription('Command line tool to import, extract and persist data from the online excel/csv.')
            ->addArgument('filename1', InputArgument::REQUIRED, 'Please provide the name of the file that contains the income data')
            ->addArgument('filename2', InputArgument::REQUIRED, 'Please provide the name of the file that contains MSOA to Post Code mapping')
        ;
    }

    // Main execution controller
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_em = $this->getContainer()->get('doctrine')->getManager();
        // making input and output available to to the rest of the class
        $this->output = $output;
        $this->input = $input;

        // clear tables
        $this->clearTables();

        // grab the parameters which need to come in separatly as have different type of data
        $this->_incomeFilename = $input->getArgument('filename1');
        $this->_mappingFilename = $input->getArgument('filename2');

        // append the full path to the filename for convenience
        $this->_incomeFilename = $this->dataPath.$this->_incomeFilename;
        $this->_mappingFilename = $this->dataPath.$this->_mappingFilename;

        // will stop execution if not found
        $this->checkFilesExist();

        $output->writeln('Importing income data');
        $this->importIncomeData();

        $start = microtime(true);
        $output->writeln('Importing post codes');
        $this->importMappingData();
        $output->writeln((microtime(true) - $start) / 60 . " minutes taken thus far");

        $output->writeln('Mapping post codes');
        $this->updatePostCodes();

        // generate fake users
        // associate users

        $output->writeln('All data imported.');
    }

    public function clearTables()
    {
        $qh = $this->getHelper('question');
        $q1 = new ConfirmationQuestion('This operation will drop tables "user" and "userincome". Continue? [Y/n]');

        if (!$qh->ask($this->input, $this->output, $q1)) {
            $this->output->writeln('Operation aborted!');
            die;
        }

        $this->output->writeln('Deleting... ');
        $userTable = $this->_em->getClassMetadata('AppBundle:User')->getTableName();
        $userIncomeTable = $this->_em->getClassMetadata('AppBundle:UserIncome')->getTableName();
        $this->_deleteTable($userTable);
        $this->_deleteTable($userIncomeTable);
        $this->output->writeln('Tables deleted');
    }


    private function _deleteTable($tableName)
    {
        $this->_executeSQL("SET FOREIGN_KEY_CHECKS=0;");
        $this->_executeSQL("Truncate table $tableName");
        $this->_executeSQL("SET FOREIGN_KEY_CHECKS=1;");
    }

    private function _executeSQL($sql)
    {
        $sql = "$sql;";
        $connection = $this->_em->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
    }

    // this will create a temporary table visible only during this session
    private function _createTemporaryTable($tableName)
    {
        $sql = "DROP TABLE IF EXISTS {$tableName}";
        $this->_executeSQL($sql);

        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (
                  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                  postcode VARCHAR(7), 
                  msoa VARCHAR(10)
                  )";
        $this->_executeSQL($sql);
    }

    public function checkFilesExist()
    {
        if (!file_exists($this->_incomeFilename)){
            throw new \Exception('File not found in ' . $this->_incomeFilename);
        }else if(!file_exists($this->_mappingFilename)){
            throw new \Exception('File not found in ' . $this->_mappingFilename);
        }
        return true;
    }

    // import all data into the database so that we can update the income table
    // with a simple update in sql rather than a realtime update as we import the CSV
    public function importMappingData()
    {
        $tablename = "temp_msoa_postcode";
        $this->_createTemporaryTable($tablename);

        $fh = fopen($this->_mappingFilename, "r");

        // first line is headers
        $headers = fgetcsv($fh);

        $row = 0;
        while (($data = fgetcsv($fh)) !== false) {
            $this->_executeSQL('INSERT INTO '. $tablename .' (postcode, msoa) VALUES ("'.$data[0].'", "'.$data[5].'")');
            if ($row % 1000 == 0)
                $this->output->writeLn($row . ' rows inserted');
            $row++;
        }

        $this->output->writeLn('Temporary table imported');
        fclose($fh);

    }

    // this will update the postcode on the income table with the data from the temporary table "msoa_postcode"
    public function updatePostCodes()
    {
        $sql = "UPDATE user_income ui,temp_msoa_postcode t set ui.postcode = t.postcode where ui.msoa_code = t.msoa";
        $this->_executeSQL($sql);
    }



    public function importIncomeData()
    {
        $fh = fopen($this->_incomeFilename, "r");

        // iterate the file with generator and/or stream and populate the entity
        $row = 0;
        while (($data = fgetcsv($fh)) !== false) {

            // bypass every row until we find the headers
            if ($data[0] != 'MSOA code' && $row == 0)
                continue;

            // we are at the headers
            if ($data[0] == 'MSOA code')
            {
                //$this->incomeHeaders[] = $data;
                $row++;
                continue;
            }

            $msoa_code = $data[0];
            $total_week_income = $data[6];

            $userIncome = new UserIncome();
            $userIncome->setMsoaCode($msoa_code);
            $userIncome->setTotalIncome($total_week_income);
            $this->_em->persist($userIncome);

            $row++;
        }

        fclose($fh);
        $this->_em->flush();
        $this->numIncomeDataRows = $row;
        $this->output->writeln($row.' rows imported');
    }

}
