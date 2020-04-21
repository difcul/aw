# Using RDF and Sparql
Francisco Couto and Tiago Guerreiro

# Part 1 - Submit by April 23rd

## SPARQL 

DBPedia stores Wikipedia data as a dataset using the RDF model, and it can be accessed using SPARQL.

DBPedia has a SPARQL endpoint (http://dbpedia.org/sparql) where you can execute queries.

Open the URL http://dbpedia.org/sparql on your browser and try the following query to get all diseases:

```
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?disease where {
 ?disease a dbo:Disease .
}
```

Now try the URL with xml format suing _curl_ and XPath to get all links:   

```shell
curl "http://dbpedia.org/sparql/?query=PREFIX+dbo%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2F%3E%0D%0A%0D%0ASELECT+%3Fdisease+where+%7B%0D%0A+%3Fdisease+a+dbo%3ADisease+.%0D%0A%7D&format=application/xml" | sed 's/xmlns="[^"]*"/xmlns=""/' > Diseases.xml

xmllint --xpath '//uri/text()' Diseases.xml
```

Note that we had to remove the xmlns value (namespace) using _sed_, so xmllint could work with local names.

You can also use _EasyRdf_ (http://www.easyrdf.org/), a PHP library for RDF developers
that helps you execute SPARQL queries,
for example: https://github.com/njh/easyrdf/blob/0.9.0/examples/basic_sparql.php

For python developers, you can the package RDFLib, for example: https://github.com/RDFLib/sparqlwrapper

## Complex queries

Back to the browser try the following query to get for each disease people that died from it:

```sparql
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?disease ?person where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
}
```

Try the following query to get the English names instead of links:
```sparql
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?diseasename ?personname where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
 ?person rdfs:label ?personname FILTER (lang(?personname) = "en").
 ?disease rdfs:label ?diseasename FILTER (lang(?diseasename) = "en").
}
```

Try the following query to get also the death date:

```sparql
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?diseasename ?personname ?deathdate where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
 ?person rdfs:label ?personname FILTER (lang(?personname) = "en").
 ?disease rdfs:label ?diseasename FILTER (lang(?diseasename) = "en").
 ?person dbo:deathDate ?deathdate .
}
```

Try the following query to get only people that died between 1800-01-01 and 1900-01-01:

```sparql
PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT ?diseasename ?personname ?deathdate where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
 ?person rdfs:label ?personname FILTER (lang(?personname) = "en").
 ?disease rdfs:label ?diseasename FILTER (lang(?diseasename) = "en").
 ?person dbo:deathDate ?deathdate . 
 FILTER ((?deathdate > "1800-01-01"^^xsd:date) && (?deathdate < "1900-01-01"^^xsd:date)) . 
}
```

Try the following query to also get their occupation if available:

```sparql
PREFIX dbo: <http://dbpedia.org/ontology/>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT ?diseasename ?personname ?deathdate ?occupationname where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
 ?person rdfs:label ?personname FILTER (lang(?personname) = "en").
 ?disease rdfs:label ?diseasename FILTER (lang(?diseasename) = "en").
 ?person dbo:deathDate ?deathdate .
 FILTER ((?deathdate > "1800-01-01"^^xsd:date) && (?deathdate < "1900-01-01"^^xsd:date)) . 
 OPTIONAL {?person dbo:occupation ?occupation . 
           ?occupation rdfs:label ?occupationname FILTER (lang(?occupationname) = "en").}
}
```
You can now try the same queries using _curl_ and _xmllint_, or _EasyRdf_.

## Additional Exercise for Evaluation

- Using SPARQL, retrieve (programatically) the DBPedia **abstract** - both in **English and in Portuguese** - of the diseases your Web Application is currently supporting. After searching for a disease on your Web Application, it should first present an Heading called Abstract, followed by the text in English. Then, it should present an Heading called Resumo, followed by the text in Portuguese.

- After the two abstracts, the Web Application should also present the list of people who have died from that disease between **1900-01-01 and 2000-01-01**, followed by their **nationality**, and **occupation** (in English), if available.

- [**Updated**] The diseases supported are the ones included in the first script **OR** the set of diseases gathered from DBPedia on the Weekly Exercise of the second script.

**HINT**: Note that the second option may be easier, since you have already learned how to filter a set of diseases (e.g., from the field pulmonology) in the second script with: **?uri dbp:field dbr:Pulmonology .**

- All students should submit their files by Thursday, **April 23th**. Submit your relevant files on Moodle - a ZIP file AW-4_1-XXXXX.ZIP, where 4_1 means the fourth script PART 1, and XXXXX is to be replaced by your student number (for example, AW-4_1-12345.ZIP).

In your zip file, include a Text File with:
The link for your web application (example: http://appserver.alunos.di.fc.ul.pt/~awXXXXX/tp4_1/mywebapp.php). 
Relevant commands that you may have executed in your terminal in order to complete the ADDITIONAL EXERCISE (for instance, xargs commands that you may have used). 

# Part 2 - Submit by April 30th

## RFDa

RDFa (Resource Description Framework in attributes) enables us to embed descriptions of things (types) and their properties within HTML documents using common vocabularies (check http://schema.org/).


### Extract Data 
 
Some RDFa parsers that are available: 
- Google structured data testing tool: http://www.google.com/webmasters/tools/richsnippets
- Yandex Structured Data validator: http://webmaster.yandex.com/microtest.xml
- RDFa Play: http://rdfa.info/play
- Structured data linter: http://linter.structured-data.org/

Check using the Google structured data testing tool what kind of data you have in the PubMed initial web page: https://www.ncbi.nlm.nih.gov/pubmed/

For example, you should be able to identify that the country-name is USA (https://search.google.com/structured-data/testing-tool/u/0/#url=https%3A%2F%2Fwww.ncbi.nlm.nih.gov%2Fpubmed%2F). 

### Adding structured data 

Now you will add structured data to your web application using the ScholarlyArticle vocabulary (http://schema.org/ScholarlyArticle)

Get the file _mywebapp.php_ created in a previous module and replace the PHP code :

```php
...
$c=array_combine($links,$titles);

echo '<div vocab="http://schema.org/" typeof="ScholarlyArticle" resource="#article">';

foreach ($c as $key => $value) {
  echo '<span property="name">';
  echo '<a href="' . $key . '">' . $value . '</a></br>'; 
  echo '</span>';
}

echo '</div>';
...
```

Note that visually nothing changed when opening the URL: _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_

Now check using the Google structured data testing tool what kind of data you get from  _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_.

You can now add other structured date, such as the _datePublished_

This means that now other applications understand the semantics of the data your web application is providing.

## Additional Exercise for Evaluation

To be announced.

## Additional References

- https://www.w3.org/2009/Talks/0615-qbe/

- http://sparqles.ai.wu.ac.at/availability

- https://www.w3.org/wiki/SparqlEndpoints 

- https://coffeecode.net/rdfa/codelab/

 


