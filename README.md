# blixt

- **terms:** id, name
- **schemas:** id, name
- **columns:** id, schema_id, name, weight
- **documents:** id, schema_id, primary_key
- **attributes:** id, document_id, column_id, value
- **attribute_term:** id, attribute_id, term_id, position


Example:

    Schema: user
    Primary key: 1
    Attributes:
        id: 1 (Primary key, not indexed)
        name: Jonathon Henderson (indexed)
        about: This is about Jonathon (indexed)
        age: 29 (not indexed)

- terms

| id | name      |
|----|-----------|
| 1  | jonathon  |
| 2  | henderson |
| 3  | this      |
| 4  | is        |
| 5  | about     |

- schemas

| id | name |
|----|------|
| 1  | user |

- columns

| id | schema_id | name  | weight |
|----|-----------|-------|--------|
| 1  | 1         | id    | 1      |
| 2  | 1         | name  | 1      |
| 3  | 1         | about | 1      |
| 4  | 1         | age   | 1      |

- documents

| id | schema_id | primary_key |
|----|-----------|-------------|
| 1  | 1         | 1           |

- attributes

| id | document_id | column_id | value                  |
|----|-------------|-----------|------------------------|
| 1  | 1           | 1         | 1                      |
| 2  | 1           | 2         | Jonathon Henderson     |
| 3  | 1           | 3         | This is about Jonathon |
| 4  | 1           | 4         | 29                     |

- attribute_term

| id | attribute_id | term_id | position |
|----|--------------|---------|----------|
| 1  | 2            | 1       | 1        |
| 2  | 2            | 2       | 2        |
| 3  | 3            | 3       | 1        |
| 4  | 3            | 4       | 2        |
| 5  | 3            | 5       | 3        |
| 5  | 3            | 1       | 4        |

### Notes
Suggested methods for storage engine interface:

- findTerm(name)
- findTerms(names)
- createTerm(name)
- createTerms(names)

- findSchema(name)
- createSchema(name)

- findColumn(schema_id, name)
- findColumns(schema_id, names)
- createColumn(schema_id, name)
- createColumns(schema_id, names)

- findDocument(schema_id, primary_key)
- findDocuments(schema_id, primary_keys)
- createDocument(schema_id, primary_key)
- createDocuments(schema_id, primary_keys)

- findAttribute(document_id, column_id)
- findAttributes(document_id, column_ids)
- createAttribute(document_id, column_id, value)
- createAttributes(document_id, column_id, value)