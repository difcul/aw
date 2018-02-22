# Using RDF and Sparql
Tiago Guerreiro and Francisco Couto


## SPARQL 

DBPedia stores Wikipedia data as a dataset using the RDF model, and it can be accessed using SPARQL.

DBPedia has a SPARQL endpoint (http://dbpedia.org/sparql) where you can execute queries.

Using the browser try the following query to get all names of diseases:

```
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?disease where {
 ?disease a dbo:Disease .
}
```

Now try the URL with xml format suing _curl_ and XPath to get all links:   

```
curl "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=PREFIX+dbo%3A+<http%3A%2F%2Fdbpedia.org%2Fontology%2F>%0D%0A%0D%0ASELECT+%3Fdire+%7B%0D%0A+%3Fdisease+a+dbo%3ADisease+.%0D%0A%7D&format=text%2Fxml" > Diseases.xml

xmllint --xpath '//a/@href' Diseases.xml 
```

Back to the browser try the following query to get for each disease people that died from it:

```
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?disease ?person where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
}
```


Try the following query to get the English names instead of links:
```
PREFIX dbo: <http://dbpedia.org/ontology/>

SELECT ?diseasename ?personname where {
 ?disease a dbo:Disease .
 ?person dbo:deathCause ?disease .
 ?person rdfs:label ?personname FILTER (lang(?personname) = "en").
 ?disease rdfs:label ?diseasename FILTER (lang(?diseasename) = "en").
}
```

Try the following query to get also the death date:

```
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

```
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

```
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

## RFDa

RDFa (Resource Description Framework in attributes) enables us to embed descriptions of things (types) and their properties within HTML documents using common vocabularies (check http://schema.org/).


### Extract Data 
 
Some RDFa parsers available: 
- Google structured data testing tool: http://www.google.com/webmasters/tools/richsnippets
- Yandex Structured Data validator: http://webmaster.yandex.com/microtest.xml
- RDFa Play: http://rdfa.info/play
- Structured data linter: http://linter.structured-data.org/

Check using the Google structured data testing tool what kind of data you have in the PubMed initial web page: https://www.ncbi.nlm.nih.gov/pubmed/

For example, you should be able to identify that the country-name is USA (https://search.google.com/structured-data/testing-tool/u/0/#url=https%3A%2F%2Fwww.ncbi.nlm.nih.gov%2Fpubmed%2F). 

### Adding structured data 

Get the file _mywebapp.php_ created in a previous module and replace the PHP code :

```
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

This means that now other applications understand the semantics of the data your web application is providing.

## Additional References

- https://www.w3.org/2009/Talks/0615-qbe/

- https://coffeecode.net/rdfa/codelab/

 


