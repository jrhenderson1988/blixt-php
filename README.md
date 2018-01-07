# Blixt

## Structure

- **schemas:** id, name
- **words:** id, word
- **terms:** id, schema_id, word_id, document_count
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

Searching begins by providing a query object to Blixt along with an optional schema (or set of schemas). A string may be
passed instead of a query object, but that will be internally converted to a relevant query object. Different query 
objects refer to different types of searches and therefore have different approaches to the search.

All of the below queries allow for some extra filtering by the specification of additional where clauses that can be 
used to further filter down the result set. There where clauses can be against any of the other fields that are stored.

When searching, it is also possible to pass in a single schema to only interrogate that schema, a collection of schemas 
to interrogate more than one schema, or no schema at all to interrogate all schemas.  

### Boolean Query (Coming later)

TODO: The simplest of all of the queries. Given a set of terms separated by boolean operators (| = or, & = and, ~ = not)

### Single Term Query

A simple query that is based on a single term/word/token. An object representing a single term query is passed to the 
index' `search` method, along with a single schema (to search only a single schema), a set of schemas (to search many 
specific schemas) or no schema (to search all schemas).

The target schemas are determined and the search process is carried out once for each. Firstly, a global word record is
queried from the index and a corresponding term record is then looked up in the target schema. Next, the fields that 
contain the term are looked up and their associated documents are retrieved (with all of their fields loaded).

TODO - Continue

### Multi Term Query (With partial matching)


### Full Phrase Query (Where all terms must be matched in the correct order)


## Reading

- https://www.elastic.co/guide/en/elasticsearch/guide/current/controlling-relevance.html
- https://lucene.apache.org/core/3_0_3/fileformats.html
- http://aakashjapi.com/fuckin-search-engines-how-do-they-work/
- https://github.com/zendframework/ZendSearch