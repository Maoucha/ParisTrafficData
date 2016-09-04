# ParisTrafficData
Visualizing Paris road traffic based on Paris Open data
 
## Preparing data source

### Creating the database

Run the `schema.sql` SQL script to create the database:
 
```
mysql [options] < schema.sql
```

### Loading data

As the data from the CSV files downloaded from [Paris open data's website](http://opendata.paris.fr/explore/dataset/referentiel-comptages-routiers/table/)
requires to be slightly transformated, run the PHP script to properly populate the database:

```
php tools.php sensor <path_to_referentiel_CSV_file>
```



