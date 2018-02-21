curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=$1&retmax=10&retmode=xml" | grep "<Id>" | sed -e "s/<Id>//" -e "s/<\/Id>//" 
