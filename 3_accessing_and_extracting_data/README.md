# Accessing and extracting data
Tiago Guerreiro and Francisco Couto

XPath (XML Path Language) is as an efficient tool to get information from XML and HTML documents, 
instead of using regular expressions in _grep_ and _sed_ tools.
XPath is a query language for selecting nodes from a XML document. 
However, it can be used to query any markup document. 


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

You can execute the query using the _xmllint_ tool (type ```man xmllint``` to know more about _xmllint_) to get their Ids:

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
- ```//ArticleId[@IdType="doi"]'``` - element of type _ArticleId_, that has an attribute _IdType_ with the value 'doi';
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

## Abstracts and Annotation

To get the abstracts of the articles you can use the following query and save it to a file:
```shell
xmllint --xpath '//AbstractText/text()' Articles.xml > Abstracts.txt
```

To find more diseases in the abstracts you can use the MER API (http://labs.fc.ul.pt/mer/):
```shell
curl --data-urlencode "text=$(cat Abstracts.txt)" 'http://labs.rd.ciencias.ulisboa.pt/mer/api.php?lexicon=disease'
```

and you should get the following output with the diseases found and where they were found: 
```txt
348	354	asthma
359	363	COPD
496	500	COPD
504	510	asthma
1066	1076	bronchitis
1095	1101	asthma
1135	1142	disease
1306	1314	impetigo
1316	1320	acne
1322	1337	gastroenteritis
2015	2025	bronchitis
1173	1185	otitis media
2156	2168	otitis media
1105	1142	chronic obstructive pulmonary disease
1281	1304	urinary tract infection
```

Search some of these terms in the Disease Ontology portal (http://disease-ontology.org/), and you will get the the following identifiers:
```txt
http://purl.obolibrary.org/obo/DOID_10754	otitis media
http://purl.obolibrary.org/obo/DOID_13148	urinary tract infection
http://purl.obolibrary.org/obo/DOID_2326	gastroenteritis
http://purl.obolibrary.org/obo/DOID_2841	asthma
http://purl.obolibrary.org/obo/DOID_3083	chronic obstructive pulmonary disease
http://purl.obolibrary.org/obo/DOID_3083	COPD
http://purl.obolibrary.org/obo/DOID_4	disease
http://purl.obolibrary.org/obo/DOID_6132	bronchitis
http://purl.obolibrary.org/obo/DOID_6543	acne
http://purl.obolibrary.org/obo/DOID_8504	impetigo
```
Considering that _asthma_ is on what the user is interested in, you can measure the similarity (relevance) between _asthma_ ```DOID:2841``` and 
the other terms using the tool DiShIn (http://labs.fc.ul.pt/dishin/) and their identifiers. 
You will see that _disease_ has low similarity values, because it is a generic term. So, _disease_ should have a lower contribution for ranking search results about _asthma_. 

To perform these last steps programmatically you can use MER (https://github.com/lasigeBioTM/MER) and DiShIn (https://github.com/lasigeBioTM/DiShIn) locally, 
and follow the example https://github.com/lasigeBioTM/MER#ontology-and-pubmed

MER and DiShIn are also available in _appserver_ at _/home/aw000/MER_ and /home/aw000/DiShIn_, respectively.
So in _appserver_ you can execute the following commands:

```shell
text=$(cat Abstracts.txt) 
(cd /home/aw000/MER; ./get_entities.sh "$text" doid-simple | ./link_entities.sh data/doid-simple.owl | sort | uniq)
```
and you will get as output:

```txt
http://purl.obolibrary.org/obo/DOID_10754	otitis media
http://purl.obolibrary.org/obo/DOID_13148	urinary tract infection
http://purl.obolibrary.org/obo/DOID_2326	gastroenteritis
http://purl.obolibrary.org/obo/DOID_2841	asthma
http://purl.obolibrary.org/obo/DOID_3083	chronic obstructive pulmonary disease
http://purl.obolibrary.org/obo/DOID_3083	COPD
http://purl.obolibrary.org/obo/DOID_4	disease
http://purl.obolibrary.org/obo/DOID_6132	bronchitis
http://purl.obolibrary.org/obo/DOID_6543	acne
http://purl.obolibrary.org/obo/DOID_8504	impetigo
```

then create a python script (in a file named _disease.py_) to call DiShIn:

```python
import sys
sys.path.insert(0, '/home/aw000/DiShIn/')

import ssm
import semanticbase

ssm.semantic_base('/home/aw000/DiShIn/disease.db')

e1 = ssm.get_id('DOID_2841') # Asthma
e2 = ssm.get_id('DOID_3083') # COPD
e3 = ssm.get_id('DOID_4') # Disease

ssm.intrinsic = True
ssm.mica = True

print ('similarity(asthma,COPD) = ' + str(ssm.ssm_lin (e1,e2)))
print ('similarity(asthma,disease) = ' + str(ssm.ssm_lin (e1,e3)))
```

execute it:
```shell
python3 disease.py 
```

and the result will be something like this:

```txt
similarity(asthma,COPD) = 0.5502114916789094
similarity(asthma,disease) = -0.0
```
To run in a local machine you have to install the tools first:

```
git clone https://github.com/lasigeBioTM/MER.git
git clone https://github.com/lasigeBioTM/DiShIn.git
```

## Additional References

- http://webdam.inria.fr/Jorge/files/wdm-xpath.pdf

- https://www.w3schools.com/xml/xpath_intro.asp

- https://www.researchgate.net/publication/323220245_Text_Mining_for_Bioinformatics_Using_Biomedical_Literature

- https://www.researchgate.net/publication/323219905_Semantic_Similarity_Definition

