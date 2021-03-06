# SPARQLStore

The `SPARQLStore` is the name for the component that can establish a connection between a [RDF triple store][tdb] and Semantic MediaWiki (a more general introduction can be found [here](https://www.semantic-mediawiki.org/wiki/Help:Using SPARQL and RDF stores)).

The `SPARQLStore` is composed of a base store (by default using the existing `SQLStore`), a `QueryEngine`, and a connector for the RDF back-end. Currently, the base store takes the position of accumulating information about properties, value annotations, and statistics.

## Repository connector

A repository connector is responsible to establish a communication (via REST) between Semantic MediaWiki and an external [TDB][tdb] with the main objective to transfer/update triples from SMW to the back-end and to return match results for a query request made by the `QueryEngine`.

The following client repositories are currently supported:

- [Jena Fuseki][fuseki]
- [Virtuoso][virtuoso]
- [4Store][4store]
- [Sesame][sesame]

### Create a connection
<pre>
$connectionManager = new ConnectionManager();

$connectionManager->registerConnectionProvider(
	'sparql',
	new RepositoryConnectionProvider( 'fuseki' )
);

$connection = $connectionManager->getConnection( 'sparql' )
</pre>

## QueryEngine

The `QueryEngine` is responsible for transforming a `ask` description object into a qualified
[`SPARQL` query language][sparql-query] string.

- The `CompoundConditionBuilder` builds a SPARQL condition from a `#ask` query artefact (aka `Description` object)
- The condition is transformed into a qualified `SPARQL` statement for which the repository connector is making a request to the back-end and is expected to return a list of raw results (as `XML` or `Json`)
- The raw results are being parsed by a `HttpResponseParser` to provide a unified `RepositoryResult` object
- During the final step, the `QueryResultFactory` converts the `RepositoryResult` into a SMW specific `QueryResult` object which will fetch the remaining data (those selected as printrequests) from the base store and make them available to a `QueryResultPrinter`

### Create a query request
<pre>
/**
 * Equivalent to [[Foo::+]]
 *
 * SELECT DISTINCT ?result WHERE {
 * ?result swivt:wikiPageSortKey ?resultsk .
 * ?result property:Foo ?v1 .
 * }
 * ORDER BY ASC(?resultsk)
 */
$description = new SomeProperty(
    new DIProperty( 'Foo' ),
    new ThingDescription()
);

$query = new Query( $description );

$sparqlStorefactory = new SPARQLStoreFactory(
  new SPARQLStore()
);

$queryEngine = $sparqlStorefactory->newMasterQueryEngine();
$queryResult = $queryEngine->getQueryResult( $query );
</pre>

[fuseki]: https://jena.apache.org/
[fuseki-dataset]: https://jena.apache.org/documentation/tdb/dynamic_datasets.html
[sparql-query]:http://www.w3.org/TR/sparql11-query/
[sparql-dataset]: https://www.w3.org/TR/sparql11-query/#specifyingDataset
[virtuoso]: https://github.com/openlink/virtuoso-opensource
[4store]: https://github.com/garlik/4store
[tdb]: http://en.wikipedia.org/wiki/Triplestore
[sesame]: http://rdf4j.org/
