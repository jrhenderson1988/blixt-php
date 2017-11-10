# Blixt

## Structure

- **words:** id, word
- **columns:** id, name, stored, indexed, weight
- **documents:** id, key
- **fields:** id, document_id, column_id, value
- **presences:** id, field_id, word_id, frequency
- **occurrences** id, presence_id, position

### Word

A word is a stemmed word, appearing somewhere in the index.

### Column

Columns are used to define the available fields within the index. For example, we could have first name, last name and
address columns in a user index. A column can be given a weight to affect the outcome of a search against the index, to
strengthen or weaken the influence of values in a column. A column can also be given a type, which we can further use to
cast corresponding values, especially when making comparisons.

### Document

A document represents an item in an index. For example, given a user index, we could have documents for Joe Bloggs and
Jane Doe each with their own fields. A document must always have a unique key that we can use to look up a document in
order to avoid duplicates and to refer to records outside of the search index.

### Field

A document can contain many fields, which represent values of the columns in the index. For example, given a user index,
that defines a set of columns for first name and last name, and a document within that index representing a person, Joe 
Bloggs. We would have 2 fields, one for each column giving the values of each for the document, in this case the first 
name column for the document as "Joe" and the last name column as "Bloggs".

Depending on the definition of the corresponding column, a field may be stored or not. If a field is stored, its value 
is present whereas if a field is not stored, its value is missing. Indexing can occur regardless of whether the field is
stored or not as the value of the field is analysed at index time and references to words and positions etc. are stored
in other tables.

The total number of words that appear in a field, including duplicates, is stored.

### Presence

A presence, represents the existence of a word in a field. A reference to both the field and the word is stored. There
can be no duplicate presence records. Multiple instances of a word appearing in a field is handled by the occurrence
table.

A presence record also stores the number of times (frequency) the referenced word appears in the referenced field.

### Occurrence

An occurrence record represents each instance of a word appearing in a field. It includes the position that the word 
appears in the field (where each full word is counted as 1, rather than physical character position). The number of 
times (frequency) a word appears in a field can be derived from counting the number of occurrence records we have for a 
presence.

## Notes

TODO
- When constructed, Blixt should be provided a default Config object which holds a stemmer, tokenizer and storage factory
- Blixt stores the Config in a property and ensures each part of it is provided.
- The Index constructor accepts a storage engine (created from factory), a stemmer and a tokenizer along with a name and optional schema
    - The storage engine provides methods to check existence, create and open the index storage
    - When constructed, the Index class uses the storage engine to create or open the storage ready for use. If the index needs to be created, the provided schema is used or an error is thrown
- Blixt has an open method which accepts a name of an index and an optional config object.
    - If the optional config object is provided, it's values are merged with the default config object to create a new config
    - From the config, the stemmer and tokenizer are extracted and a storage engine is created using the storage factory
    - These items are passed into the index along with the name and the index is either created or updated.
    
    $config = new Config(new SQLiteFactory(), new EnglishStemmer(), new DefaultTokenizer());
    $blixt = new Blixt($config);
    $index = $blixt->open('users');

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