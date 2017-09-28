# So Sure

### Background:
One of our current projects is to determine the weekly income of our users to for use with both advertising and claims.

The initial though was to use the dataset from:
https://www.ons.gov.uk/employmentandlabourmarket/peopleinwork/earningsandworkinghours/datasets/smallareaincomeestimatesformiddlelayersuperoutputareasenglandandwales

which has a MSOA code and total weekly income

There is an additional dataset here:
https://ons.maps.arcgis.com/home/item.html?id=ef72efd6adf64b11a2228f7b3e95deea

which contains a postcode & MSOA (MSOA11CD) mapping

Task:
Write 2 symfony cli commands to import both datasets into a database of your choice with doctrine objects. 

For the excel file, you can either use a library (we’re currently using "phpoffice/phpexcel”) 
or export the sheet to a csv and just import the csv (either solution is completely valid for the purposes of this task). 

Create a user object and populate sample users (around 100) with postcodes. (we currently 
use "fzaninotto/faker” & "doctrine/doctrine-fixtures-bundle” which may be applicable). 

Display a list of users + their weekly incomes. Note that you can ignore security / pagination / beautiful display (although make it at least presentable).

Financial calculations are quite important to us. Make sure to account for float/currency in the calculations.

Show daily, monthly, and annual amounts based on the weekly figures.

Assume that the user can afford to pay 5% of their weekly income for insurance. Display that figure.

Insurance uses IPT instead of VAT (current rate is 12%), which is included in our policy price. 
Based on 3.2, display how much possible money we can receive from the user exclusive of IPT.

Display sum & average calculations across all the users for each of the columns.

Make sure to provide a readme file on how to import the data and avoid checking in the actual csv files as quite big. 
You can send the GitHub repo link to me when completed.

