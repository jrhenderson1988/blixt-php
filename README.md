# Blixt

[![Build Status](https://www.travis-ci.com/jrhenderson1988/blixt.svg?branch=master)](https://www.travis-ci.com/jrhenderson1988/blixt)

## Structure

- **schemas:** id, name
- **words:** id, word
- **terms:** id, schema_id, word_id, field_count
- **columns:** id, schema_id, name, is_stored, is_indexed
- **documents:** id, schema_id, key
- **fields:** id, document_id, column_id, value
- **occurrences:** id, field_id, term_id, frequency
- **positions:** id, occurrence_id, position

### Schemas

Represent the different types within an index, such as a user, a product etc.

### Words

Represents stems of words, unique to the whole index. (e.g. run is a stem of the words running, runner, runs etc.). An 
inverse document frequency is calculated and stored against the word to provide an index-wide IDF which can be used to 
perform schema-less searches against the index.

### Terms

A term represents the existence of a word within a specific schema. That is, if the stemmed word "run" appears within 
the user schema and it also appears in the product schema, then there will be two term records for that word, within 
that schema. An inverse document frequency is also stored along side the term to provide a schema specific IDF that 
allows searches against a specific schema (these searches won't be affected by the index-wide IDF values for the words).

### Columns

A column is an available field within a schema. The column stores rules about whether the data it represents is stored,
whether it is indexed and it's weight which can be used to prioritise columns in queries (e.g. a product title may have
more weight than a description)

### Documents

A document represents an actual entity in an index, this is the item that is actually indexed and will form the basis of
the results to queries.

### Fields

A field is an attribute of a document that matches a column. Columns, documents and fields act like a typical database.
A column is like a table column, a document is like a row or record and a field is a cell or entry.

### Occurrences

An occurrence represents the existence of a term (A schema specific word) within a field in a document. An occurrence is
unique representation of a term and field. Along side an occurrence, we also store a frequency, which represents the 
number of times the term appears within the field (which is used to calculate scores later when searching).

### Positions

Positions represent that actual positions of terms within fields. If the word "run" (or its stems) appears 3 times in a
field, there would be 3 position records, each marking the position of that occurrence within the corresponding field.


## An example

Imagine we have a "user" schema in our index. That schema defines 2 columns for each user instance, "name" and "about".
Each column is both stored and indexed and they both have a weight of 1. We have 2 users to add to the index, 
[key: 1, name: "Joe Bloggs", "about": "Joe likes Jane"] and [key: 2, name: "Jane Doe", about: "Jane loves to party"]. We
would end up with the following structure:

#### schemas

| id | name |
|----|------|
| 1  | user |

#### columns

| id | name  | stored | indexed | weight |
|----|-------|--------|---------|--------|
| 1  | name  | 1      | 1       | 1      |
| 2  | about | 1      | 1       | 1      |

#### words (Stemmed by de-pluralizing/removing trailing 's')

| id | word  |
|----|-------|
| 1  | joe   |
| 2  | blogg |
| 3  | like  |
| 4  | jane  |
| 5  | doe   |
| 6  | love  |
| 7  | to    |
| 8  | party |

#### terms

| id | schema_id | word_id | document_count |
|----|-----------|---------|----------------|
| 1  | 1         |  1      | 2              |
| 2  | 1         |  2      | 1              |
| 3  | 1         |  3      | 2              |
| 4  | 1         |  4      | 3              |
| 5  | 1         |  5      | 1              |
| 6  | 1         |  6      | 1              |
| 7  | 1         |  7      | 1              |
| 8  | 1         |  8      | 1              |

#### documents

| id | schema_id | key |
|----|-----------|-----|
| 1  | 1         | 1   |
| 2  | 1         | 2   |

#### fields

| id | document_id | column_id | value               |
|----|-------------|-----------|---------------------|
| 1  | 1           | 1         | Joe Bloggs          |
| 2  | 1           | 2         | Joe likes Jane      |
| 3  | 2           | 1         | Jane Doe            |
| 4  | 2           | 2         | Jane likes to party |

#### occurrences

| id | field_id | term_id | frequency |
|----|----------|---------|-----------|
| 1  | 1        | 1       | 1         |
| 2  | 1        | 2       | 1         |
| 3  | 2        | 1       | 1         |
| 4  | 2        | 3       | 1         |
| 5  | 2        | 4       | 1         |
| 6  | 3        | 4       | 1         |
| 7  | 3        | 5       | 1         |
| 8  | 4        | 4       | 1         |
| 9  | 4        | 3       | 1         |
| 10 | 4        | 7       | 1         |
| 11 | 4        | 8       | 1         |

#### positions

| id | occurrence_id | position |
|----|---------------|----------|
| 1  | 1             | 1        |
| 2  | 2             | 2        |
| 3  | 3             | 1        |
| 4  | 4             | 2        |
| 5  | 5             | 3        |
| 6  | 6             | 1        |
| 7  | 7             | 2        |
| 8  | 8             | 1        |
| 9  | 9             | 2        |
| 10 | 10            | 3        |
| 11 | 11            | 4        |

## Initialisation

When the Blixt object is created, it initially loads all of the stored schema and column objects into memory, if there 
are any. This allows Blixt to quickly look up a schema and its associated columns to validate an index request and to
quickly gather the `is_indexed` and `is_stored` constraints on each column.

## Indexing

Before indexing of a document can take place, a schema must be defined with a set of columns, each with their own 
properties that specify whether the fields they represent should be stored and or indexed. If a schema already exists, 
this part can be skipped. If a schema definition is not provided and no such schema exists, an exception is thrown.

A document is provided in the form of an indexable document, specifying a key which can be used by the client system to 
identify it later. An indexable document contains a set of fields. A schema (or type) is provided along with the
document so that Blixt is able to place the document in the correct schema within the index.

Blixt begins processing the document by first checking to ensure it does not already exist in the index under that
schema. If it does exist, an exception is thrown.

Blixt will then add a document record and begin processing the document's fields. Each field is split into tokens (in 
most cases a token is a word), and then each token is stemmed (i.e. finding the root of a word, for example with an 
English/Porter stemmer the word "run" is the stem of the words "running", "runner", "runs" etc.)

Word records are then added for each of the tokens if no corresponding records already exist, and then term records are
created under the schema accordingly. The document count totals for the term records are updated to reflect the addition
of the new document.

Occurrence records are created for each unique term and field combination, indicating that the specified term occurs 
within the specific field. The frequency (number of times that term occurred within the field) is stored along side the 
occurrence record. Positional data is also stored against each occurrence record representing each position in a field 
that a term occurred.

## Searching

The following types of queries are supported:

- **Boolean:** A simplified boolean query where all terms in a phrase are considered optional (or) unless preceded 
directly with a `+` which makes a term required (and) or a `~` which makes a term remove a document from consideration
(not). This type of query forms the basis of all of the other queries.

Searching begins by splitting the search phrase into separate words (tokenization) and then turning each word into its 
root form (stemming). The index is then queried to find word records matching the terms. Using the word records and the 
schema we're interrogating, term records are then extracted. If any of the search terms that had the `+` operator 
attached to it are not present in the term records, then an empty result set is returned. 

A query is then performed to find fields that contain the terms of the query (regardless of operator) and the documents
that are associated with those fields are returned (with their fields, occurrences and positions all loaded 
accordingly). Each document is then analysed and either accepted or rejected, if a document does not contain a required 
term (`+`) or does contain a removed term (`~`) it is rejected, all other documents are accepted. The set of accepted 
documents are then returned as results.

- **Multi-term:** A simple query that is carried out by first performing a boolean query and then scoring all of the 
documents found using our scoring function (Which is similar to Lucene's scoring function). The documents are ranked in
order of their scores and the results are returned.

The following types of queries are due to be supported later:

- **Single Term Query:** Similar to a multi-term query, documents are scored against a single term.

- **Full Phrase Query:** Similar to a multi-term query, but only documents that contain every term in the correct 
order are allowed. 

It is also intended to implement query parsing so that an input search string can be converted to the correct query type
before searching is performed.

## Reading

- https://www.elastic.co/guide/en/elasticsearch/guide/current/controlling-relevance.html
- https://lucene.apache.org/core/3_0_3/fileformats.html
- http://aakashjapi.com/fuckin-search-engines-how-do-they-work/
- https://github.com/zendframework/ZendSearch