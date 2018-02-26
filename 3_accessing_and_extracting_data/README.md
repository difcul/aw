# Accessing and extracting data
Tiago Guerreiro and Francisco Couto

XPath (XML Path Language) is as an efficient tool to get information from XML and HTML documents, 
instead of using regular expressions in _grep_ and _sed_ tools.
XPath is a query language for selecting nodes from a XML document. 
However, it can be used to query any markup document. 


## XPath

XPAth enables us to query that document by using the structure of the document.
For example, to get the XML data for two articles from PubMed and save it to a file:

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29462659,29461895&retmode=text&rettype=xml" > Articles.xml 
```

Type ```cat Articles.xml``` to check the output, and try to figure out what the following XPath query will return:

```
/PubmedArticle/PubmedData/ArticleIdList/ArticleId 
```

You can execute the query using the _xmllint_ tool (type ```man xmllint``` to know more about _xmllint_) to get their Ids:

```
xmllint --xpath '/PubmedArticleSet/PubmedArticle/PubmedData/ArticleIdList' Articles.xml
```

The XPath syntax enables more complex queries, try these ones:

- ```/PubmedArticleSet/PubmedArticle/PubmedData/ArticleIdList/ArticleId[1]``` - first element of type _ArticleId_, that is a child of _ArticleIdList_.
- ```PubmedArticleSet``` - root elements of type _PubmedArticleSet_;
- ```//ArticleId``` - elements of type _ArticleId_ that are descendants of something;
- ```PubmedArticleSet//ArticleId``` - elements of type _ArticleId_ that are descendants of _PubmedArticleSet_; 
- ```//ArticleIdList/*``` - any elements that is a child of _ArticleIdList_;
- ```//ArticleId/@IdType``` - all _IdType_ attributes of tags _ArticleId_;
- ```//ArticleId[@IdType="doi"]'``` - element of type _ArticleId_, that has an attribute _IdType_ with the value 'doi';

Check W3C for more about XPath syntax https://www.w3schools.com/xml/xpath_syntax.asp

Note you can use PHP (or other programming language) to execute XPath queries (https://www.w3schools.com/php/func_simplexml_xpath.asp),
and also execute cURL (http://php.net/manual/en/book.curl.php)


## HTML documents 

You can apply the same concepts to HTML documents rather than XML. 
For example download the wikipedia page about Asthma:

```
curl https://en.wikipedia.org/wiki/Asthma > Asthma.html
```

Now try to get all links using the following XPath:

```
xmllint --xpath '//a/@href' Asthma.html 
```

And get only the links to images and save it to a file: 

```
xmllint --xpath '//a[@class="image"]/@href' Asthma.html > AsthmaImages.txt
```

You can now replace the _sed_ and _grep_ commands of previous modules by using XPath queries.

## Abstracts and Annotation

To get the abstracts of the articles you can use the following query and save it to a file:
```
xmllint --xpath 'string(//Abstract)' Articles.xml > Abstracts.txt
```

To find more diseases in the abstracts you can use the MER API (http://labs.fc.ul.pt/mer/):
```
curl --data-urlencode "text=$(cat ArticlesAbstracts.xml)" 'http://labs.rd.ciencias.ulisboa.pt/mer/api.php?lexicon=disease'
```

and you should get the following output with the diseases found and where they were found: 
```
337	343	asthma
345	349	COPD
356	362	cancer
245	260	viral infection
351	362	lung cancer
378	396	pulmonary fibrosis
367	396	idiopathic pulmonary fibrosis
```

Search these terms in the Disease Ontology portal (http://disease-ontology.org/), and you will get the the following identifiers: 

** asthma: DOID:2841
- COPD: DOID:3083
- cancer: DOID:162
- viral infection: DOID:934
- lung cancer: DOID:2841
- pulmonary fibrosis: DOID:3770
- idiopathic pulmonary fibrosis: DOID:0050156 

Considering that Asthma is on what the user is interested in, you can measure the similarity (relevance) between Asthma and the other terms using the tool DiShIn (http://labs.fc.ul.pt/dishin/) and their identifiers.
You will see that cancer and viral infection have low similarity, so less relevant than all the others. 

To perform these last steps programmatically you should download the Disease Ontology:
- https://github.com/DiseaseOntology/HumanDiseaseOntology/blob/master/src/ontology/
- SQLite: http://labs.rd.ciencias.ulisboa.pt/dishin/hdo.db
and install MER (https://github.com/lasigeBioTM/MER) and DiShIn (https://github.com/lasigeBioTM/DiShIn) locally. 
MER is available in _appserver_ at /opt/MER-0.1/.


## Additional References

- http://webdam.inria.fr/Jorge/files/wdm-xpath.pdf

- https://www.w3schools.com/xml/xpath_intro.asp



