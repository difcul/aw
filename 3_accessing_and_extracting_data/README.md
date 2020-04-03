# Accessing and extracting data
Francisco Couto and Tiago Guerreiro

XPath (XML Path Language) is as an efficient tool to get information from XML and HTML documents, 
instead of using regular expressions in _grep_ and _sed_ tools.
XPath is a query language for selecting nodes from a XML document. 
However, it can be used to query any markup document. 

# Part 1 - Submit by April 1st

## XPath

XPAth enables us to query that document by using the structure of the document. 

For example, to get the XML data for two articles from PubMed ([29490421](https://www.ncbi.nlm.nih.gov/pubmed/29490421) and [29490060](https://www.ncbi.nlm.nih.gov/pubmed/29490060)) 
and save it to a file:
```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29490421,29490060&retmode=text&rettype=xml" > Articles.xml 
```

Type ```cat Articles.xml``` to check the output, and try to figure out what the following XPath query will return:

```txt
/PubmedArticle/PubmedData/ArticleIdList/ArticleId 
```

You can execute the query using the _xmllint_ tool (type ```man xmllint``` to know more about _xmllint_ or check https://youtu.be/myanCTM-3Tw ) to get their Ids:

```shell
xmllint --xpath '/PubmedArticleSet/PubmedArticle/PubmedData/ArticleIdList' Articles.xml
```

The XPath syntax enables more complex queries, try these ones:

- ```/PubmedArticleSet/PubmedArticle/PubmedData/ArticleIdList/ArticleId[1]``` - first element of type _ArticleId_, that is a child of _ArticleIdList_.
- ```PubmedArticleSet``` - root elements of type _PubmedArticleSet_;
- ```//ArticleId``` - elements of type _ArticleId_ that are descendants of something;
- ```PubmedArticleSet//ArticleId``` - elements of type _ArticleId_ that are descendants of _PubmedArticleSet_; 
- ```//ArticleIdList/*``` - any elements that is a child of _ArticleIdList_;
- ```//ArticleId/@IdType``` - all _IdType_ attributes of tags _ArticleId_;
- ```//ArticleId[@IdType="doi"]``` - element of type _ArticleId_, that has an attribute _IdType_ with the value 'doi';
- ```//AbstractText/text()``` - the text value of elements of type _AbstractText_ 

Check W3C for more about XPath syntax https://www.w3schools.com/xml/xpath_syntax.asp

Note you can use PHP (or other programming language) to execute XPath queries (https://www.w3schools.com/php/func_simplexml_xpath.asp),
and also execute cURL (http://php.net/manual/en/book.curl.php)


## HTML documents 

You can apply the same concepts to HTML documents rather than XML. 
For example download the wikipedia page about Asthma:

```shell
curl https://en.wikipedia.org/wiki/Asthma > Asthma.html
```

Now try to get all links using the following XPath:

```shell
xmllint --xpath '//a/@href' Asthma.html 
```

And get only the links to images and save it to a file: 

```shell
xmllint --xpath '//a[@class="image"]/@href' Asthma.html > AsthmaImages.txt
```

You can now replace the _sed_ and _grep_ commands of previous modules by using XPath queries.

## Additional Exercise for Evaluation [UPDATED]

- [**Not mandatory**] Replace the _sed_ and _grep_ commands of previous modules by using XPath queries (if you haven't done it already).

- Retrieve the information (title of article and link to page) of at least 10 articles for each disease on your diseases.txt file (which should include the diseases from DBpedia). You should use the disease name as the search query. The arcticles can be **either** from (only one of these):
  - **Google Scholar** (https://scholar.google.com/), which provides scientifical articles related to the disease
  **OR**
  - **Wikipedia search results** (https://en.wikipedia.org/), which provides wikipedia articles related to the disease. Note there is a search page (https://en.wikipedia.org/w/index.php?search=&title=Special%3ASearch&go=Go).

When searching for each disease on your Web Application (mywebapp.php), it should present the title of the articles, with an hyperlink to the article page (similarly to pubmed on TP2).

The information sources (pubmed, flickr, dbpedia) from prior weeks should continue to show up on the search results. Create a Heading for each information source in order to separate the results.

- **Hint**: Find mechanisms to make requests to Google Scholar (or Wikipedia) with some time interval, otherwise you will be blocked for some time. There are ways to do that with xargs and with other alternatives you may use. 

- **Note**: You may find difficulties accessing the results from Google Scholar. In that case, focus on Wikipedia search results.

- All students should submit their files by Wednesday, April 1st. Submit your relevant files on Moodle - a ZIP file AW-3_1-XXXXX.ZIP, where 3_1 means the third script PART 1, and XXXXX is to be replaced by your student number (for example, AW-3_1-12345.ZIP).

In your zip file, include a Text File with:
The link for your web application (example: http://appserver.alunos.di.fc.ul.pt/~awXXXXX/tp3_1/mywebapp.php). 
Relevant commands that you may have executed in your terminal in order to complete the ADDITIONAL EXERCISE (for instance, xargs commands that you may have used). 

# Part 2 - Submit by April 16th

## Abstracts and Annotation

To get the abstracts of the articles you can use the following query and save it to a file:
```shell
xmllint --xpath '//AbstractText/text()' Articles.xml > Abstracts.txt
```

To find more diseases in the abstracts you can use grep (https://youtu.be/0OCjQLxKH_g) or a text mining tool such as MER (http://labs.fc.ul.pt/mer/).
To use MER API type:
```shell
curl --data-urlencode "text=$(cat Abstracts.txt)" 'http://labs.rd.ciencias.ulisboa.pt/mer/api.php?lexicon=doid'
```

and you should get the following output with the diseases found and where they were found: 
```txt
348     354     asthma          http://purl.obolibrary.org/obo/DOID_2841
359     363     COPD            http://purl.obolibrary.org/obo/DOID_3083
496     500     COPD            http://purl.obolibrary.org/obo/DOID_3083
504     510     asthma          http://purl.obolibrary.org/obo/DOID_2841
1066    1076    bronchitis      http://purl.obolibrary.org/obo/DOID_6132
1095    1101    asthma          http://purl.obolibrary.org/obo/DOID_2841
1135    1142    disease         http://purl.obolibrary.org/obo/DOID_4
1306    1314    impetigo        http://purl.obolibrary.org/obo/DOID_8504
1316    1320    acne            http://purl.obolibrary.org/obo/DOID_6543
1322    1337    gastroenteritis http://purl.obolibrary.org/obo/DOID_2326
2015    2025    bronchitis      http://purl.obolibrary.org/obo/DOID_6132
1173    1185    otitis media    http://purl.obolibrary.org/obo/DOID_10754
2156    2168    otitis media    http://purl.obolibrary.org/obo/DOID_10754
1105    1142    chronic obstructive pulmonary disease   http://purl.obolibrary.org/obo/DOID_3083
1281    1304    urinary tract infection                 http://purl.obolibrary.org/obo/DOID_13148
```
The links (URIs) represent the respective entries in the Disease Ontology (http://disease-ontology.org/).

Considering that _asthma_ is on what the user is interested in, you can measure the similarity (relevance) between _asthma_ ```DOID:2841``` and 
the other terms using the tool DiShIn (http://labs.fc.ul.pt/dishin/) and their identifiers. 
You will see that _disease_ has low similarity values, because it is a generic term. So, _disease_ should have a lower contribution for ranking search results about _asthma_.

Tip: calculate the similarity between the term and itself, and the resnik measure will give you the information content (specificity) of that term.  

To perform these last steps programmatically you can use MER (https://github.com/lasigeBioTM/MER) and DiShIn (https://github.com/lasigeBioTM/DiShIn) locally, 
and follow the example https://github.com/lasigeBioTM/MER#ontology-and-pubmed

## MER and DiShIn at appserver

MER and DiShIn are also available in _appserver_ at _/home/aw000/MER_ and /home/aw000/DiShIn_, respectively.
So in _appserver_ you can execute the following commands (type ```man sort``` to know more about these tools) :

```shell
text=$(cat Abstracts.txt) 
(cd /home/aw000/MER; ./get_entities.sh "$text" doid | sort -u -g) > Terms.txt
cat Terms.txt
```
and you will get as output:

```txt
348     354     asthma                                  http://purl.obolibrary.org/obo/DOID_2841
359     363     COPD                                    http://purl.obolibrary.org/obo/DOID_3083
496     500     COPD                                    http://purl.obolibrary.org/obo/DOID_3083
504     510     asthma                                  http://purl.obolibrary.org/obo/DOID_2841
1066    1076    bronchitis                              http://purl.obolibrary.org/obo/DOID_6132
1095    1101    asthma                                  http://purl.obolibrary.org/obo/DOID_2841
1105    1142    chronic obstructive pulmonary disease   http://purl.obolibrary.org/obo/DOID_3083
1135    1142    disease                                 http://purl.obolibrary.org/obo/DOID_4
1173    1185    otitis media                            http://purl.obolibrary.org/obo/DOID_10754
1281    1304    urinary tract infection                 http://purl.obolibrary.org/obo/DOID_13148
1306    1314    impetigo                                http://purl.obolibrary.org/obo/DOID_8504
1316    1320    acne                                    http://purl.obolibrary.org/obo/DOID_6543
1322    1337    gastroenteritis                         http://purl.obolibrary.org/obo/DOID_2326
2015    2025    bronchitis                              http://purl.obolibrary.org/obo/DOID_6132
2156    2168    otitis media                            http://purl.obolibrary.org/obo/DOID_10754
```

To calculate the similarity between _asthma_ and _COPD_ execute:

```shell
python /home/aw000/DiShIn/dishin.py /home/aw000/DiShIn/doid.db DOID_2841 DOID_3083
```

And between _asthma_ and _disease_ execute:
```shell
python /home/aw000/DiShIn/dishin.py /home/aw000/DiShIn/doid.db DOID_2841 DOID_4
```

And to know the information content of _disease_ and of _asthma_:
```shell
python /home/aw000/DiShIn/dishin.py /home/aw000/DiShIn/doid.db DOID_4 DOID_4 | grep  "Resnik.*DiShIn"
python /home/aw000/DiShIn/dishin.py /home/aw000/DiShIn/doid.db DOID_2841 DOID_2841 | grep  "Resnik.*DiShIn"
```

To obtain the similarities between _asthma_ and all the terms identified by MER execute (type ```man xargs``` to know more about this tool):

```shell
cat Terms.txt | sed 's/^.*DOID_\([0-9]*\).*$/DOID_\1/' | xargs -l python /home/aw000/DiShIn/dishin.py /home/aw000/DiShIn/doid.db DOID_2841
```

## MER and DiShIn locally

To run in a local machine install the tools first:

```shell
git clone git://github.com/lasigeBioTM/MER
git clone git://github.com/lasigeBioTM/DiShIn
```
and then download the Human Disease Ontology (doid.owl) from https://github.com/DiseaseOntology/HumanDiseaseOntology/tree/master/src/ontology and pre-process it by executing the commands:  

```shell
wget https://raw.githubusercontent.com/DiseaseOntology/HumanDiseaseOntology/master/src/ontology/doid.owl
cd MER/data
cp ../../doid.owl .
../produce_data_files.sh doid.owl
cd ..
./get_entities.sh "Asthma" doid
cd ..
cd DiShIn
cp ../doid.owl .
python dishin.py doid.owl doid.db http://purl.obolibrary.org/obo/ http://www.w3.org/2000/01/rdf-schema#subClassOf ''
python dishin.py doid.db DOID_4 DOID_4 
cd ..
```

The pre-process may take some time, some you may download the files:
```shell
wget http://appserver.alunos.di.fc.ul.pt/~aw000/aw/doid-lexicon-database-20190217.zip
unzip doid-lexicon-database-20190217.zip
cd MER
./get_entities.sh "Asthma" doid
cd ..
cd DiShIn
python dishin.py doid.db DOID_4 DOID_4 
cd ..
```

## Additional Exercise for Evaluation

-  Extract the abstracts of the PubMed Articles related to each disease (see beginning of PART 1). Remember that their IDs have already been retrieved with ./getPubMedIds.sh, and should be in a .txt file already (you do **NOT** need to extract IDs from new diseases).

- For each disease, find the terms from the Disease Ontology that are mentioned in the abstracts (using MER). Calculate the similarity (Resnik) between the disease and each term identified by MER (using DiShIn).

- After searching for each disease, present the similarity between the disease and each of the other terms in mywebapp.php.

- All students should submit their files by Thursday, **April 16th**. However, **I encourage you to submit by April 7th**. Submit your relevant files on Moodle - a ZIP file AW-3_2-XXXXX.ZIP, where 3_2 means the third script PART 2, and XXXXX is to be replaced by your student number (for example, AW-3_2-12345.ZIP).

In your zip file, include a Text File with:
The link for your web application (example: http://appserver.alunos.di.fc.ul.pt/~awXXXXX/tp3_2/mywebapp.php). 
Relevant commands that you may have executed in your terminal in order to complete the ADDITIONAL EXERCISE (for instance, xargs commands that you may have used). 


## Additional References

- http://labs.rd.ciencias.ulisboa.pt/book/ (3.7 XML Processing; 5.9 Entity Linking; 5.10 Large lexicons)

- http://webdam.inria.fr/Jorge/files/wdm-xpath.pdf

- https://www.w3schools.com/xml/xpath_intro.asp

- https://www.researchgate.net/publication/329435244_MER_A_shell_script_and_annotation_server_for_minimal_named_entity_recognition_and_linking

- https://www.researchgate.net/publication/323219905_Semantic_Similarity_Definition

