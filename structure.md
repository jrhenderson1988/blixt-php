

+ src/
    + Exceptions/
    + Index/
        c Index.php
    + Stemming/
        i Stemmer.php
        a AbstractStemmer.php
        c EnglishStemmer.php
    + Tokenization/
        c Token.php
        i Tokenizer.php
        a AbstractTokenizer.php
        c DefaultTokenizer.php
    + Entities/
        i Schema.php            // a global document type (id, name)
        i Word.php              // a global stemmed word (id, word)
        i Term.php              // a word in a schema (id, word_id, schema_id)
        i Column.php            // an available attribute in a schema (id, schema_id, name, is_stored, is_indexed, weight)
        i Document.php          // a document stored against a schema that contains fields mapped against columns (id, key)
        i Field.php             // a field stored in a document against a schema's column (id, document_id, column_id, value)
        i Presence.php          // refers to the presence of a term in a field (id, term_id, field_id)
        i Occurrence.php        // an occurrence refers to the positions of the different presences in a field (id, presence_id, position)
    + Storage/
        i Storage.php               // the storage engine that encapsulates the repositories
        + Repositories
            i SchemaRepository.php
            i WordRepository.php
            i TermRepository.php
            i ColumnRepository.php
            i DocumentRepository.php
            i FieldRepository.php
            i PresenceRepository.php
            i OccurrenceRepository.php
    - c Blixt.php
    
    
    
    
    
    
Storage/
    Storage.php (Defines exists(), create(), schemas(), words(), columns(), documents(), fields(), occurrences(), presences())
    Entities/
        Schema.php (getId(), getName())
        Word.php (getId(), getSchemaId(), getWord())
    Repositories/
        SchemaRepository.php (findByName(), findById(), create()...)

