# Accessing webpages and extracting contents
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

You can execute the query using the _xmllint_ tool (type ```man xmllint``` to know more about _xmllint_):

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

And get only the links to images: 

```
xmllint --xpath '//a[@class="image"]/@href' Asthma.html 
```

You can now replace the _sed_ and _grep_ commands of previous modules by using XPath queries.

## Additional References

- http://webpages.fc.ul.pt/~fjcouto/files/manual_soa_ajax_20120221.pdf 

- https://www.w3schools.com/xml/xpath_intro.asp

- https://www.w3schools.com/xml/xquery_intro.asp

