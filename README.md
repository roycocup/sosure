# So Sure

### Usage
Download and place both datasets inside web/data folder.

Open the income dataset and export the sheet named 'Total weekly income' to csv.

Setup and configure a mysql database

Make sure you have composer installed and run the following commands

`composer install` - to install all the dependencies for this project

`./bin/console doctrine:schema:update --force` - to import the schema into the database

For the first leg of the program, you need to import all data into the database. 
You can do so by using a command somewhat like the following: 
`./bin console import-data income-data.csv mappings-data.csv` - Imports, extracts and persists the data for the task

`./bin/console calculate` - Calculates and displays data for a number of users




