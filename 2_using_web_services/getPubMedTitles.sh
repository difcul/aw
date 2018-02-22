curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=$1&retmode=text&rettype=xml" | egrep "<ArticleTitle>" | sed -e "s/<[^>]*>//g" -e "s/^ *//" -e "s/ *$//"
