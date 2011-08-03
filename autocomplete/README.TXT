LTERKeywordAutocomplete V1.0 6/30/2011

This zip file contains two files you will need to implement "autocomplete"
using LTER Keywords in existing or new web forms.

LTERAutoComp.php is a tool that fetches and selects keywords that match what is
typed in. It needs to be put in the same directory as the form with
autocomplete, but no editing is required, unless you wish to change the source
of keywords (see "ADVANCED USE" below). 

LTERKeywordForm.html is a "sample" form that demonstrates the use of
autocomplete. You can modify it to create a new form for your system, or copy
parts of it into existing forms to add autocomplete capabilities.

If you are modifying an existing web form, copy the three "<script>" tags in
the <head> to the <head> section of your web form. Then copy the <input> and
<script> tags in the body of web form into the body of your web form. You can
customize the name and size of the input fields to match your needs.

ADVANCED USE:

Word Lists: LTERAutoComp.php uses a web fetch to get the list of words from the LTER
server. In some circumstances it may run faster if the file containing the list
of words is local to your system. You can obtain a list of the current
preferred terms at: http://vocab.lternet.edu/webservice/preferredterms.php
However, if you use a local list, you are responsible for updating it.

If you want to use a set of site-specific terms use,
http://vocab.lternet.edu/webservice/preferredterms.php?service=http://vocab.lternet.edu/vocab/luq/services.php
where "http://vocab.lternet.edu/vocab/luq/services.php" is the link to the web
services.php on the Tematres server for your site to fetch a list. Only terms
found in the taxonomys or that have related terms will be returned (no synonyms
or "use for" or "free" terms).

If you want you can combine the list of LTER-wide and site-specific keywords
into a single file and use that as the source of keywords.

Directories: Sometimes due to security or other server configurations, you might
want to put LTERAutoComp.php into a different directory within your
webspace. You are free to do so, just be sure to add the relative web address
(that is, the path relative to your webroot) in the script tag of your web form.

If you have any questions, contact John Porter (jporter@lternet.edu) and I'll
try to help you out.


