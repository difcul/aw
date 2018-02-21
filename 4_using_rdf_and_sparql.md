# Using RDF and Sparql
Tiago Guerreiro and Francisco Couto

"RDF is a standard model for data interchange on the Web. RDF has features that facilitate data merging even if the underlying schemas differ, and it specifically supports the evolution of schemas over time without requiring all the data consumers to be changed.

RDF extends the linking structure of the Web to use URIs to name the relationship between things as well as the two ends of the link (this is usually referred to as a “triple”). Using this simple model, it allows structured and semi-structured data to be mixed, exposed, and shared across different applications.

This linking structure forms a directed, labeled graph, where the edges represent the named link between two resources, represented by the graph nodes. This graph view is the easiest possible mental model for RDF and is often used in easy-to-understand visual explanations." - retrieved from https://www.w3.org/RDF/

In this tutorial, we make a short introduction to creating RDF files to describe oneself (using the FOAF - Friend of a Friend - ontology), loading and using RDF files, and querying a RDF-based knowledge base (DBPedia). 

## Requirements

To experiment with the samples provided in this tutorial, you need to install EasyRDF(http://www.easyrdf.org/) in your server account. To do so, follow the instructions in their [Getting started](http://www.easyrdf.org/docs/getting-started) webpage.

## Brief introdution to RDF

RDF (Resource Description Framework) is a graph-based data model composed of triples (subject, predicate, object). Subjects, predicates and objects are described as URIs; objects can also be described as literals. 

Let's consider the following graph (retrieved from XML.com):

![Knowledge as a graph](whatisrdf_1.gif)

We can describe the relationshipos in the graph as triples (subject, predicate, object):

```
(vincent\_donofrio, starred\_in, law\_&\_order\_ci)
(law\_&\_order\_ci ,is\_a, tv\_show)
(the\_thirteenth\_floor, similar\_plot\_as, the\_matrix)
...
```

The same information in a RDF/XML format is represented as follows:

```
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:ex="http://www.example.org/">
    <rdf:Description rdf:about="http://www.example.org/vincent_donofrio">
        <ex:starred_in>
            <ex:tv_show rdf:about="http://www.example.org/law_and_order_ci" />
        </ex:starred_in>
    </rdf:Description>
    <rdf:Description rdf:about="http://www.example.org/the_thirteenth_floor">
        <ex:similar_plot_as rdf:resource="http://www.example.org/the_matrix" />
    </rdf:Description>
</rdf:RDF>
```

which is the standard but tends to obscure the graph information. Notation 3 improves readability and is much closer to the tabular format we presented:

```
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix ex: <http://www.example.org/> .

ex:vincent_donofrio ex:starred_in ex:law_and_order_ci .
ex:law_and_order_ci rdf:type ex:tv_show .
ex:the_thirteenth_floor ex:similar_plot_as ex:the_matrix .
```

## FOAF (Friend-of-a-friend)

FOAF (Friend-of-a-friend) is a standard RDF vocabulary (ontology) to describe people and relationships. Ontologies as FOAF enable us to expect structure from data and understand its semantics.

Look at the FOAF file of Tim-Berners Lee (retrieved from http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf):
```
<rdf:RDF xmlns="http://xmlns.com/foaf/0.1/"
    xmlns:cc="http://creativecommons.org/ns#"
    xmlns:cert="http://www.w3.org/ns/auth/cert#"
    xmlns:con="http://www.w3.org/2000/10/swap/pim/contact#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:dct="http://purl.org/dc/terms/"
    xmlns:doap="http://usefulinc.com/ns/doap#"
    xmlns:foaf="http://xmlns.com/foaf/0.1/"
    xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
    xmlns:owl="http://www.w3.org/2002/07/owl#"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:s="http://www.w3.org/2000/01/rdf-schema#"
    xmlns:sioc="http://rdfs.org/sioc/ns#"
    xmlns:solid="https://www.w3.org/ns/solid/terms#"
    xmlns:space="http://www.w3.org/ns/pim/space#">

    <rdf:Description rdf:about="../../DesignIssues/Overview.html">
        <dc:title>Design Issues for the World Wide Web</dc:title>
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <PersonalProfileDocument rdf:about="">
        <cc:license rdf:resource="http://creativecommons.org/licenses/by-nc/3.0/"/>
        <dc:title>Tim Berners-Lee's FOAF file</dc:title>
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
        <primaryTopic rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </PersonalProfileDocument>

    <rdf:Description rdf:about="#i">
        <cert:key rdf:parseType="Resource">
            <rdf:type rdf:resource="http://www.w3.org/ns/auth/cert#RSAPublicKey"/>
            <cert:exponent rdf:datatype="http://www.w3.org/2001/XMLSchema#integer">65537</cert:exponent>
            <cert:modulus rdf:datatype="http://www.w3.org/2001/XMLSchema#hexBinary">ebe99c737bd3670239600547e5e2eb1d1497da39947b6576c3c44ffeca32cf0f2f7cbee3c47001278a90fc7fc5bcf292f741eb1fcd6bbe7f90650afb519cf13e81b2bffc6e02063ee5a55781d420b1dfaf61c15758480e66d47fb0dcb5fa7b9f7f1052e5ccbd01beee9553c3b6b51f4daf1fce991294cd09a3d1d636bc6c7656e4455d0aff06daec740ed0084aa6866fcae1359de61cc12dbe37c8fa42e977c6e727a8258bb9a3f265b27e3766fe0697f6aa0bcc81c3f026e387bd7bbc81580dc1853af2daa099186a9f59da526474ef6ec0a3d84cf400be3261b6b649dea1f78184862d34d685d2d587f09acc14cd8e578fdd2283387821296f0af39b8d8845</cert:modulus>
        </cert:key>
    </rdf:Description>

    <rdf:Description rdf:about="http://dig.csail.mit.edu/2005/ajar/ajaw/data#Tabulator">
        <doap:developer rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://dig.csail.mit.edu/2007/01/camp/data#course">
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://dig.csail.mit.edu/breadcrumbs/blog/4">
        <dc:title>timbl's blog on DIG</dc:title>
        <s:seeAlso rdf:resource="http://dig.csail.mit.edu/breadcrumbs/blog/feed/4"/>
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://dig.csail.mit.edu/data#DIG">
        <member rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://wiki.ontoworld.org/index.php/_IRW2006">
        <dc:title>Identity, Reference and the Web workshop 2006</dc:title>
        <con:participant rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://www.ecs.soton.ac.uk/~dt2/dlstuff/www2006_data#panel-panelk01">
        <s:label>The Next Wave of the Web (Plenary Panel)</s:label>
        <con:participant rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://www.w3.org/2000/10/swap/data#Cwm">
        <doap:developer rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://www.w3.org/2011/Talks/0331-hyderabad-tbl/data#talk">
        <dct:title>Designing the Web for an Open Society</dct:title>
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://www.w3.org/People/Berners-Lee/card#i">
        <owl:sameAs rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <rdf:Description rdf:about="http://www.w3.org/data#W3C">
        <member rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </rdf:Description>

    <PersonalProfileDocument rdf:about="https://timbl.com/timbl/Public/friends.ttl">
        <cc:license rdf:resource="http://creativecommons.org/licenses/by-nc/3.0/"/>
        <dc:title>Tim Berners-Lee's editable FOAF file</dc:title>
        <maker rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
        <primaryTopic rdf:resource="https://www.w3.org/People/Berners-Lee/card#i"/>
    </PersonalProfileDocument>

    <con:Male rdf:about="https://www.w3.org/People/Berners-Lee/card#i">
        <rdf:type rdf:resource="http://xmlns.com/foaf/0.1/Person"/>
        <sioc:avatar rdf:resource="images/timbl-image-by-Coz-cropped.jpg"/>
        <s:label>Tim Berners-Lee</s:label>
        <s:seeAlso rdf:resource="https://timbl.com/timbl/Public/friends.ttl"/>
        <s:seeAlso rdf:resource="https://www.w3.org/2007/11/Talks/search/query?date=All+past+and+future+talks&#38;event=None&#38;activity=None&#38;name=Tim+Berners-Lee&#38;country=None&#38;language=None&#38;office=None&#38;rdfOnly=yes&#38;submit=Submit"/>
        <con:assistant rdf:resource="https://www.w3.org/People/Berners-Lee/card#amy"/>
        <con:homePage rdf:resource="./"/>
        <con:office rdf:parseType="Resource">
            <con:address rdf:parseType="Resource">
                <con:city>Cambridge</con:city>
                <con:country>USA</con:country>
                <con:postalCode>02139</con:postalCode>
                <con:street>32 Vassar Street</con:street>
                <con:street2>MIT CSAIL Room 32-G524</con:street2>
            </con:address>
            <con:phone rdf:resource="tel:+1-617-253-5702"/>
            <geo:location rdf:parseType="Resource">
                <geo:lat>42.361860</geo:lat>
                <geo:long>-71.091840</geo:long>
            </geo:location>
        </con:office>
        <con:preferredURI>https://www.w3.org/People/Berners-Lee/card#i</con:preferredURI>
        <con:publicHomePage rdf:resource="./"/>
        <owl:sameAs rdf:resource="http://www.advogato.org/person/timbl/foaf.rdf#me"/>
        <space:preferencesFile rdf:resource="https://timbl.com/timbl/Data/preferences.n3"/>
        <space:storage rdf:resource="https://timbl.databox.me/"/>
        <account rdf:resource="http://en.wikipedia.org/wiki/User:Timbl"/>
        <account rdf:resource="http://twitter.com/timberners_lee"/>
        <account rdf:resource="http://www.reddit.com/user/timbl/"/>
        <based_near rdf:parseType="Resource">
            <geo:lat>42.361860</geo:lat>
            <geo:long>-71.091840</geo:long>
        </based_near>
        <family_name>Berners-Lee</family_name>
        <givenname>Timothy</givenname>
        <homepage rdf:resource="https://www.w3.org/People/Berners-Lee/"/>
        <img rdf:resource="https://www.w3.org/Press/Stock/Berners-Lee/2001-europaeum-eighth.jpg"/>
        <mbox rdf:resource="mailto:timbl@w3.org"/>
        <mbox_sha1sum>965c47c5a70db7407210cef6e4e6f5374a525c5c</mbox_sha1sum>
        <name>Timothy Berners-Lee</name>
        <nick>TimBL</nick>
        <nick>timbl</nick>
        <openid rdf:resource="https://www.w3.org/People/Berners-Lee/"/>
        <phone rdf:resource="tel:+1-(617)-253-5702"/>
        <title>Sir</title>
        <weblog rdf:resource="http://dig.csail.mit.edu/breadcrumbs/blog/4"/>
        <workplaceHomepage rdf:resource="https://www.w3.org/"/>
        <solid:publicTypeIndex rdf:resource="https://timbl.com/timbl/Public/PublicTypeIndex.ttl"/>
    </con:Male>
</rdf:RDF>
```

As you can see, an extensive set of semantically-rich information can be retrieved from the RDF FOAF file. Let's see how we can use it. 

## Loading a FOAF RDF file 

One simple example of using RDF files is to access a FOAF profile and printing one field. The following example prints the name of the person:

```
<?php
    /**
     * Basic "Hello World" type example
     *
     * A new EasyRdf_Graph object is created and then the contents
     * of my FOAF profile is loaded from the web. An EasyRdf_Resource for
     * the primary topic of the document (me, Nicholas Humfrey) is returned
     * and then used to display my name.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2013 Nicholas J Humfrey
     * @license    http://unlicense.org/
     */
    require 'vendor/autoload.php';
?>
<html>
<head>
  <title>Basic FOAF example</title>
</head>
<body>

<?php
  $foaf = EasyRdf_Graph::newAndLoad('http://njh.me/foaf.rdf');
  $me = $foaf->primaryTopic();
?>

<p>
  My name is: <?= $me->get('foaf:name') ?>
</p>

</body>
</html>
```

This script is using an online RDF file. You can try to select other attributes to try it.

## Introduction to SPARQL

SPARQL is a query language that enables us to deal with the semantic web. It deals with structured and unstructured data and enables us to explore data by exploring unknown relationships. SPARQL allows us to query multiple databases in a single query. 

A SPARQL query comprises, in order:

- Prefix declarations, for abbreviating URIs
- Dataset definition, stating what RDF graph(s) are being queried
- A result clause, identifying what information to return from the query
- The query pattern, specifying what to query for in the underlying dataset
- Query modifiers, slicing, ordering, and otherwise rearranging query results

```
# prefix declarations
PREFIX foo: <http://example.com/resources/>
...
# dataset definition
FROM ...
# result clause
SELECT ...
# query pattern
WHERE {
    ...
}
# query modifiers
ORDER BY ...
```

SPARQL queries are executed against RDF datasets, consisting of RDF graphs.

## Performing queries with SPARQL

### Introducing SELECT, variables and triple patterns:

"In the graph http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf, find me all subjects (?person) and objects (?name) linked with the foaf:name predicate. Then return all the values of ?name. In other words, find all names mentioned in Tim Berners-Lee's FOAF file."

```
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>
SELECT ?name
FROM <http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf>
WHERE {
    ?person foaf:name ?name .
}
```

In addition, we would have to refer to the data graph used.

SPARQL variables start with a ? and can match any node (resource or literal) in the RDF dataset. Triple patterns are just like triples, except that any of the parts of a triple can be replaced with a variable. The SELECT result clause returns a table of variables and values that satisfy the query. 

### Selecting all properties:

"Give me all properties about Apollo 7"
```
SELECT ?p ?o
{ 
  <http://nasa.dataincubator.org/spacecraft/1968-089A> ?p ?o
}
```

### Using multiple triple patterns

"Find me all the people in Tim Berners-Lee's FOAF file that have names and email addresses. Return each person's URI, name, and email address."

```
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>
SELECT *
FROM <http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf>
WHERE {
    ?person foaf:name ?name .
    ?person foaf:mbox ?email .
}
```

### Traversing a graph example

"Find me the homepage of anyone known by Tim Berners-Lee."

```
PREFIX foaf:  <http://xmlns.com/foaf/0.1/>
PREFIX card: <http://www.w3.org/People/Berners-Lee/card#>
SELECT ?homepage
FROM <http://dig.csail.mit.edu/2008/webdav/timbl/foaf.rdf>
WHERE {
    card:i foaf:knows ?known .
    ?known foaf:homepage ?homepage .
}
```
        

By using ?known as an object of one triple and the subject of another, we traverse multiple links in the graph.

Now that we have understood how it works, let's see some results. To do so, we can use, for example, [ARC](http://sparql.org/sparql.html). Paste the queries there and see the results.
 
(Examples and descriptions retrieved from [Sparql By Example](http://www.cambridgesemantics.com/semantic-university/sparql-by-example))
   
## Using DBPedia

"DBpedia is a crowd-sourced community effort to extract structured information from Wikipedia and make this information available on the Web. DBpedia allows you to ask sophisticated queries against Wikipedia, and to link the different data sets on the Web to Wikipedia data" - Retrieved from DBPedia.org

Now let's check the next example where we query DBPedia endpoint for all the countries that are members of the United Nations. Explore the code. Check DBPedia webpage and try different queries, try using different prefixes and traversing graphs.

```
<?php
    /**
     * Making a SPARQL SELECT query
     *
     * This example creates a new SPARQL client, 
pointing at the
     * dbpedia.org endpoint. It then makes a SELECT 
query that
     * returns all of the countries in DBpedia along 
with an
     * english label.
     *
     * Note how the namespace prefix declarations are 
automatically
     * added to the query.
     *
     * @package    EasyRdf
     * @copyright  Copyright (c) 2009-2013 Nicholas J 
Humfrey
     * @license    http://unlicense.org/
     */
    require 'vendor/autoload.php';
    // Setup some additional prefixes for DBpedia
    EasyRdf_Namespace::set('category', 
'http://dbpedia.org/resource/Category:');
    EasyRdf_Namespace::set('dbpedia', 
'http://dbpedia.org/resource/');
    EasyRdf_Namespace::set('dbo', 
'http://dbpedia.org/ontology/');
    EasyRdf_Namespace::set('dbp', 
'http://dbpedia.org/property/');
    $sparql = new 
EasyRdf_Sparql_Client('http://dbpedia.org/sparql');
?>
<html>
<head>
  <title>EasyRdf Basic Sparql Example</title>
  <meta http-equiv="content-type" content="text/html; 
charset=utf-8" />
</head>
<body>
<h1>EasyRdf Basic Sparql Example</h1>

<h2>List of countries</h2>
<ul>
<?php
    $result = $sparql->query(
        'SELECT * WHERE {'.
        '  ?country rdf:type dbo:Country .'.
        '  ?country rdfs:label ?label .'.
        '  ?country dc:subject 
category:Member_states_of_the_United_Nations .'.
        '  FILTER ( lang(?label) = "en" )'.
        '} ORDER BY ?label'
    );
    foreach ($result as $row) {
        echo "<li>".$row->label."  ". 
$row->country."</li>\n";
    }
?>
</ul>
<p>Total number of countries: <?= $result->numRows() 
?></p>

</body>
</html>
```

### Credits

The examples presented in this tutorial were retrieved and adapted from the EasyRDF examples (http://www.easyrdf.org/examples) page and from  Sparql-By-Example (http://www.cambridgesemantics.com/semantic-university/sparql-by-example).