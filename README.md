# Blixt

## Structure

- **words:** id, word
- **schemas:** id, name
- **columns:** id, schema_id, name, weight, stored, indexed
- **terms** id, schema_id, word_id
- **documents:** id, schema_id, key
- **fields:** id, document_id, column_id, value
- **presences:** id, field_id, term_id, frequency
- **occurrences** id, presence_id, position

### Word

A word is a stemmed word, appearing somewhere in the index.

### Schema

A schema represents a document type. Examples of schemas would be product or user.

### Term

A term represents a word in a schema. For example, if we have a word record for "run" that appears in a field for a user
document, we would have a record in the terms table that refers to the word_id for "run" and the schema_id for user. A
term also stores the total number of fields for which the word appears in the schema.

### Column

Columns are used to define the available fields within a schema. For example, we could have first name, last name and
address columns in a user schema. A column can be given a weight to affect the outcome of a search against the index, to
strengthen or weaken the influence of values in a column. A column can also be given a type, which we can further use to
cast corresponding values, especially when making comparisons.

### Document

A document represents an instance of a schema. For example, given a user schema, we could have documents for Joe Bloggs
and Jane Doe each with their own fields. A document must always have a unique key (within that particular schema) that 
we can use to look up a document in the index in order to avoid duplicates and to refer to records outside of the search
index.

### Field

A document can contain many fields, which represent values of the columns in the schema. For example, given a user
schema, that defines a set of columns for first name and last name, and a document within that schema representing a
person, Joe Bloggs. We would have 2 fields, one for each column giving the values of each for the document, in this case
the first name column for the document as "Joe" and the last name column as "Bloggs".

Depending on the requirements a field can be either stored or not. If a field is stored, its value is present whereas if
a field is not stored, its value is missing. Indexing can occur regardless of whether the field is stored or not as the
value of the field is analysed at index time and references to terms and positions etc. are stored in other tables.

The total number of terms that appear in a field, including duplicates, is stored.

### Presence

A presence, represents the existence of a term in a field. A reference to both the field and the term is stored. There
can be no duplicate presence records. Multiple instances of a term appearing in a field is handled by the occurrence
table.

An presence record also stores the number of times (frequency) the referenced term appears in the referenced field.

### Occurrence

An occurrence record represents each instance of a term appearing in a field. It includes the position that the term 
appears in the field (where each full term is counted as 1, rather than physical character position). The number of 
times (frequency) a term appears in a field can be derived from counting the number of occurrence records we have for a 
field-term.

## Notes

TODO: Make adjustments to make an index represent a schema/type such as "users". The schema_id can be removed from all 
of the other tables and everything inside the index would be considered in relation to the schema/index. For example, 
the columns table would define the set of columns and their weights, types and whether or not they're indexed/stored for
the data to be stored in that specific index.

It would also be beneficial to add a method into the primary Blixt class so that we can open a schema if it exists and 
provide a way for the user to define the schema for the index as a second parameter, should the index not already exist:

    $blixt = new Blixt(...);
    $blixt->open('users', function () {
        // Define the schema here
    });

Different types of queries to implement:
- One word query (Match one word)
- Phrase query (Match some of the words)
- Full Phrase query (Matches all of the words)
- Boolean query

## Reading

- https://www.elastic.co/guide/en/elasticsearch/guide/current/scoring-theory.html
- http://aakashjapi.com/fuckin-search-engines-how-do-they-work/